<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");

if(verificaFuncaoUsuario(FUNCAO_USUARIO_ADD)!==false){


//conecta no banco e instacia uma conexão com o Registry
require_once("../conexao.php");
require_once("../model/Registry.php");
//carrega DAO's das chaves estrangeiras
require_once("../dao/PerfilDAO.php");
require_once("../dao/UsuarioDAO.php");
require_once("../dao/ProcessoDAO.php");
require_once("../dao/SubsecaoDAO.php");
require_once("../dao/MunicipioDAO.php");

//carrega Models
require_once('../model/Perfil.php');	
require_once('../model/Usuario.php');
require_once('../model/Subsecao.php');

// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);

require_once("../menu_topo.php");

//SUBMENU DE AÇÕES
require_once('submenu_user.php');
//FIM SUBMENU DE AÇÕES

//se tiver enviado o formulário
if( !empty($_POST) ){

	//valida entradas do usuário
	$nome=validaLiteral(trim($_POST["nome"]),USUARIO_NOME_SIZE);
	$idperfil=validaInteiro($_POST["idperfil"],USUARIO_IDPERFIL_SIZE);	
	$nome_instituicao=validaLiteral($_POST["nome_instituicao"],USUARIO_NOMEINSTITUICAO_SIZE);
	$login=validaLiteral($_POST["login"],USUARIO_LOGIN_SIZE);
	$senha=validaLiteral($_POST["senha"],USUARIO_SENHA_SIZE+30);//+30 devido aos caract especiais
	$rsenha=validaLiteral($_POST["rsenha"],USUARIO_SENHA_SIZE+30);//+30 devido aos caract especiais
	$idmunicipio=validaInteiro($_POST["idmunicipio"],MUNICIPIO_ID_SIZE);	
	$email1=validaLiteral($_POST["email1"],USUARIO_EMAIL_SIZE);
	$email2=validaLiteral($_POST["email2"],USUARIO_EMAIL_SIZE);
	$celular=validaLiteral($_POST["celular"],USUARIO_FONE_SIZE+1);
	$telefone=validaLiteral($_POST["telefone"],USUARIO_FONE_SIZE+1);
	$expira=validaLiteral($_POST["expira"],3);
		$novoato=validaLiteral($_POST["novoato"],USUARIO_DTEXPIRACAO_SIZE+2);
		$dtlimite=transformaDataBanco($novoato);
	$limita=validaLiteral($_POST["limita"],3);
		if(isset($_POST["limitaprocessos"])){
			$limitaprocessos=$_POST["limitaprocessos"];
		}else{
			$limitaprocessos=0;
		}

	// se os campos obrigatórios passarem na validação
	if( $nome!==false && $idperfil!==false
		 && $login!==false  && $senha!==false && $rsenha!==false 
		 && $idmunicipio!==false  && $email1!==false 
		 && $celular!==false
		&& $expira!==false && $limita!==false && ($senha==$rsenha)){

		//valida para entrar na área de segurança da SQL
		$nome=sqlTrataString($nome);
		$idperfil=sqlTrataInteiro($idperfil);
		$nome_instituicao=sqlTrataString($nome_instituicao);
		$login=sqlTrataString($login);
		$senha=sqlTrataString($senha);
		$rsenha=sqlTrataString($rsenha);
		$idmunicipio=sqlTrataInteiro($idmunicipio);
		$email1=sqlTrataString($email1);
		$email2=sqlTrataString($email2);
		//remove caracteres além do número do celular
		$celular=sqlTrataString($celular);
		$celular=str_replace("(", "", $celular);
		$celular=str_replace(")", "", $celular);
		$celular=str_replace("-", "", $celular);
		$celular=str_replace(" ", "", $celular);
		//remove caracteres além do número do telefone
		$telefone=sqlTrataString($telefone);
		$telefone=str_replace("(", "", $telefone);
		$telefone=str_replace(")", "", $telefone);
		$telefone=str_replace("-", "", $telefone);
		$telefone=str_replace(" ", "", $telefone);
		//$expira	=> 	sim OU nao
		if($dtlimite!==false && $expira=="sim"){	$dtlimite=sqlTrataInteiro($dtlimite);	}else{	$dtlimite='0';	}

		//se entrar nesse IF os dados já podem ser inseridos no banco
		if($nome!==false && $idperfil!==false
		 && $login!==false  && $senha!==false && $rsenha!==false 
		 && $idmunicipio!==false  && $email1!==false 
		 && $celular!==false && $dtlimite!==false ){

			// Instanciar as infos do usuario
			$Usuario = new Usuario();
			$Usuario->setNome($nome);
			$Usuario->setPerfil($idperfil);
			$Usuario->setNomeInstituicao($nome_instituicao);			
			$Usuario->setLogin(strtolower($login));
			$Usuario->setSenha(codifica($senha));
			$Usuario->setMunicipio($idmunicipio);
			//pega Subseção correspondente do municipio:
			$SubsecaoDAO = new SubsecaoDAO();
			$idsubsecao = $SubsecaoDAO->getSubsecaoFromMunicipio($idmunicipio);
			

			//se tiver encontrado subseção atribui o valor, se não põe como nulo
			if($idsubsecao>0){
				$idsubsecao=$idsubsecao["idsubsecao"];
				$Usuario->setSubsecao($idsubsecao);
			}else{
				$idsubsecao=0;
				$Usuario->setSubsecao(0);
			}

			$Usuario->setEmail1($email1);
			if($email2){
				$Usuario->setEmail2($email2);
			}else{
				$Usuario->setEmail2(NULL);
			}
			$Usuario->setCelular($celular);
			if($telefone){
				$Usuario->setTelefone($telefone);
			}else{
				$Usuario->setTelefone(NULL);
			}
			
			$Usuario->setDtExpiracao($dtlimite);			
			// Instanciar o DAO para inserir o usuario na base
			$UsuarioDAO = new UsuarioDAO();
			// Chama a função que verifica duplicidade do registro
			$possuiDuplicidade = $UsuarioDAO->isDuplicated($Usuario);
			if($possuiDuplicidade!==false){
				enviaMsg("erro","Usuário não cadastrado","O login <strong><em>".strtoupper($login)."</em></strong> já está em uso");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_user.php?n=".$nome."\">";
				exit();
			}
			// Chama a função de inserção que só resulta FALSE caso dê algum problema.
			//insere usuario
			$idusuario = $UsuarioDAO->insert($Usuario);
			//atribui ID via método do Usuário
			$Usuario->setId($idusuario);
			if($idusuario){

				//$limita 	=>	sim OU nao
				if(isset($limitaprocessos) && $limita=="sim" && sizeof($limitaprocessos)>0 && $limitaprocessos!=0){
					foreach($limitaprocessos as $pro){
						$inseriu = $UsuarioDAO->insertLimitacaoProcesso($pro,$idusuario);
						if(!$inseriu){
							enviaMsg("erro","Usuário cadastrado com erros","A restrição de visibilidade de processos não pôde ser efetuada");
							echo "<meta http-equiv=\"refresh\" content=\"0; url=index_user.php\">";
							exit();
						}
					}
				}


				//Recupera e altera dados do PAD para salvar os valores e não os IDS no histórico
				$PerfilDAO = new PerfilDAO();
				$dado=$PerfilDAO->getOne($idperfil);
				$Usuario->setPerfil($dado["nome"]);
				$Subsecao = new Subsecao();
				$Subsecao->setId($idsubsecao);
				$SubsecaoDAO = new SubsecaoDAO();
				$dado=$SubsecaoDAO->getOne($Subsecao);
				$Usuario->setSubsecao($dado["nome"]);
				$MunicipioDAO = new MunicipioDAO();
				$dado=$MunicipioDAO->getOne($idmunicipio);
				$Usuario->setMunicipio($dado["nome"]);
				
				$log_obs = $Usuario->toLog();
				$log_obs .= 'Restringe visualização a determinados Processos? '.$limita.APP_LINE_BREAK;
				if($limita=="sim"){
					if(sizeof($limitaprocessos)>0){

						//carrega DAO de processos para descobrir o nome e tipo dos processos
						$ProcessoDAO = new ProcessoDAO();
						$nomeprocessos=$ProcessoDAO->getAll();

						$log_obs.="Processos Visíveis:";
						for($i=0;$i<sizeof($limitaprocessos);$i++){
							for($j=0;$j<sizeof($nomeprocessos);$j++){
								if($limitaprocessos[$i]==$nomeprocessos[$j]["idprocesso"]){
									$log_obs.=" Processo ".$nomeprocessos[$j]["numero"];
								}
							}
							
							if($i<sizeof($limitaprocessos)-1){
								$log_obs.=',';
							}
						}
						$log_obs.=APP_LINE_BREAK;
					}
				} 
				require_once("../dao/HistoricoDAO.php");
				require_once("../model/Historico.php");
				$Historico = new Historico();
				$Historico->setAcao(LOG_ADD_USER);
				$Historico->setProcesso(0);
				$Historico->setDocumento(0);
				$Historico->setObs(sqlTrataString($log_obs));
				$HistoricoDAO = new HistoricoDAO();
				$idhistorico = $HistoricoDAO->insert($Historico);

				if($idhistorico){
					enviaMsg("sucesso","Usuário cadastrado com sucesso");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index_user.php\">";
					exit();
				}else{
					enviaMsg("erro","Usuário cadastrado com erros","O histórico não pôde ser salvo");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index_user.php\">";
					exit();
				}

			//fim if($idprocesso)
			}else{

				enviaMsg("erro","Usuário não cadastrado","Tente novamente mais tarde");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=add_user.php\">";
				exit();

			}		
			
			
		}else{
			enviaMsg("erro","Usuário não cadastrado","Os dados inseridos estão inválidos");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=add_user.php\">";
			exit();
		}
		
	}else{

		enviaMsg("erro","Usuário não cadastrado","Os dados inseridos são inválidos");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=add_user.php\">";
		exit();
		
	}
}

?>

<div id="conteudo_borda">
    <div  id="conteudo">
<!-- <form enctype="multipart/form-data" name="add_user" action="add_user.php" method="post" class="form-horizontal" onSubmit="return validaForm('add_user','nome','idperfil','nome_instituicao','login','senha','rsenha','idmunicipio','email1','celular','expira','novoato');" > -->            
        <form id="add_user" enctype="multipart/form-data" name="add_user" action="add_user.php" method="post" class="form-horizontal">

            <div class="form-group">
                <label class="col-sm-10 control-label">Nome Completo</label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome Completo" maxlength="<?php echo USUARIO_NOME_SIZE-20; ?>" value="<?php if(isset($_GET["n"])){ echo htmlentities($_GET["n"]); } ?>">
                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-10 control-label">Perfil</label>
                <div class="col-lg-5">
                  <select id="idperfil"  name="idperfil" class="form-control" onchange="showCampoViaSelect(this.value);">
					<option value="-1">Selecione o perfil</option>
					<?php
                    // Instanciar o DAO e retornar dados da tabela
                    $perfilDAO = new PerfilDAO();
                    $perfil = $perfilDAO->getAll();
                    if(sizeof($perfil)>0){
						for($i=0;$i<sizeof($perfil);$i++){							
								echo "<option value=\"".
								$perfil[$i]["idperfil"]."\">".
								$perfil[$i]["nome"]."</option>";
						}
					}
					?>
                  </select>
                  <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
                </div>
            </div>

            <div class="form-group campoviaselect" id="campoviaselect<?php echo PERFIL_IDINSTITUICAO; ?>">
                <label class="col-sm-10 control-label">Nome da Instituição</label>
                <div class="col-lg-5">
                    <input type="text" class="form-control" id="nome_instituicao" name="nome_instituicao" placeholder="Nome da Instituição" maxlength="<?php echo USUARIO_NOMEINSTITUICAO_SIZE; ?>">
                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED.' - somente letras e números'; ?></span>
                </div>
            </div>

			<div class="form-group">
                <label class="col-sm-10 control-label">Nome de Usuário (<em>login</em>)</label>
                <div class="col-lg-5">
                    <input type="text" class="form-control camposomente09AZponto<?php echo USUARIO_LOGIN_SIZE; ?>" id="login" name="login" placeholder="Login do Usuário" maxlength="<?php echo USUARIO_LOGIN_SIZE; ?>" value="<?php if(isset($_GET["l"])){ echo $_GET["l"]; } ?>">
                    <span id="helpBlock" class="help-block">Este campo é obrigatório e aceita até <strong><?php echo USUARIO_LOGIN_SIZE; ?></strong> caracteres, padrão: nome.sobrenome</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-10 control-label">Senha</label>
                <div class="col-lg-5">
                    <input type="password" class="form-control camposenha" id="senha" name="senha" placeholder="Senha do Usuário" maxlength="<?php echo USUARIO_SENHA_SIZE; ?>" autocomplete="off">
                    <span id="helpBlock" class="help-block"><?php echo '* Campo obrigatório - de 6 a '.USUARIO_SENHA_SIZE.' caracteres, pelo menos: 1 letra maiúscula, 1 letra minúscula, 1 número e 1 caracter especial exceto aspas simples (\') ou aspas duplas (") '; ?></span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-10 control-label">Redigite a Senha</label>
                <div class="col-lg-5">
                    <input type="password" class="form-control  camposenha" id="rsenha" name="rsenha" placeholder="Redigite a Senha do Usuário" maxlength="<?php echo USUARIO_SENHA_SIZE; ?>" autocomplete="off">
                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
                </div>
            </div>

			<div class="form-group">
                <label class="col-sm-10 control-label">Município</label>
                <div class="col-lg-5">
                  <select id="idmunicipio"  name="idmunicipio" class="form-control">
					<option value="-1">Selecione o Município</option>
					<?php
                    // Instanciar o DAO e retornar dados da tabela
                    $MunicipioDAO = new MunicipioDAO();
                    $dados = $MunicipioDAO->getAll();
                    if(sizeof($dados)>0){
						for($i=0;$i<sizeof($dados);$i++){							
								echo "<option value=\"".$dados[$i]["idmunicipio"]."\">".$dados[$i]["nome"]."</option>";
						}
					}
					?>
                  </select>
                  <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-10 control-label">E-mail Principal</label>
                <div class="col-lg-8">
                    <input type="text" class="form-control campodeemail<?php echo USUARIO_EMAIL_SIZE; ?>" id="email1" name="email1" placeholder="E-mail Principal do Usuário" maxlength="<?php echo USUARIO_EMAIL_SIZE; ?>">
                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-10 control-label">E-mail Secundário</label>
                <div class="col-lg-8">
                    <input type="text" class="form-control campodeemail<?php echo USUARIO_EMAIL_SIZE; ?>" id="email2" name="email2" placeholder="E-mail Secundário do Usuário" maxlength="<?php echo USUARIO_EMAIL_SIZE; ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-10 control-label">Celular (com DDD abreviado para 2 dígitos. Ex.: 48)</label>
                <div class="col-lg-3">
                    <input type="text" class="form-control campocelular" id="celular" name="celular" placeholder="Celular do Usuário" maxlength="<?php echo USUARIO_FONE_SIZE+1; ?>">
                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-10 control-label">Telefone</label>
                <div class="col-lg-3">
                    <input type="text" class="form-control campocelular" id="telefone" name="telefone" placeholder="Telefone do Usuário" maxlength="<?php echo USUARIO_FONE_SIZE+1; ?>">
                </div>
            </div>

	        <div class="form-group">
	            <label class="col-sm-11 control-label">Credencial expira?</label>
	            <div class="col-lg-2">
	              <select id="expira"  name="expira" class="form-control" onchange="$('#div_novoato').toggle('slow');">
	                <option value="nao">Não</option>
	                <option value="sim">Sim</option>
	              </select>
	            </div>
	        </div> 
			
	        <div class="form-group" id="div_novoato" style="display:none;">
	        	<label class="col-sm-12 control-label">Data Limite</label>
	        	<div class="col-lg-3">
	        	<div class="input-group date">
		            <input type="text" class="form-control" id="novoato" name="novoato" placeholder="Data Limite de Acesso" maxlength="<?php echo USUARIO_DTEXPIRACAO_SIZE+2; ?>" autocomplete="off">
		            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
	        	</div>
	        	</div>
	        </div>

	        <div class="form-group">
	            <label class="col-sm-11 control-label">Limitar acesso a processos específicos?</label>
	            <div class="col-lg-2">
	              <select id="limita"  name="limita" class="form-control" onchange="$('#div_limitaprocessos').toggle('slow');">
	                <option value="nao">Não</option>
	                <option value="sim">Sim</option>
	              </select>
	            </div>
	        </div> 

            <div class="form-group" id="div_limitaprocessos" style="display:none;">
                <label class="col-sm-11 control-label">Clique nos processos que o usuário irá visualizar</label>
                <div class="col-lg-5">
	              <select multiple id="limitaprocessos[]"  name="limitaprocessos[]" class="form-control">
					<?php
                    // Instanciar o DAO e retornar dados da tabela
                    $ProcessoDAO = new ProcessoDAO();
                    $dados = $ProcessoDAO->getAll();
                    if(sizeof($dados)>0){
						for($i=0;$i<sizeof($dados);$i++){							
								echo "<option value=\"".
								$dados[$i]["idprocesso"]."\">Processo ".
								$dados[$i]["numero"]."</option>";
						}
					}
					?>
	              </select>
                <span id="helpBlock" class="help-block">Segure a tecla <em>CTRL</em> para selecionar mais de um processo.</span>
                </div>
            </div>
            
            <div class="form-group" style="margin-top:40px;">
              <div class="col-lg-10">
                <button type="reset" id="cancelar" class="btn btn-default index_user.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                <button type="submit" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salvar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
              </div>
            </div>

        </form>
	</div>
</div>
<?php
include_once("../menu_rodape.php");

//else do verificaFuncaoUsuario(FUNCAO_USUARIO_ADD)
}else{

	enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou dados inválidos");
	echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
	exit();

}
?>
