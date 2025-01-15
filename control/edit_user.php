<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");

//quando editando um usuário
if(isset($_REQUEST["id"]) && !empty($_REQUEST["id"]) && verificaFuncaoUsuario(FUNCAO_USUARIO_EDIT)){

$idusuario=validaInteiro($_REQUEST["id"],USUARIO_ID_SIZE);

if($idusuario){

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
		//alterasenha recebe o valor sim ou nao para atualizar ou nao a senha
		$alterasenha = $_POST["alterasenha"];
		$tentativas_num=validaInteiro($_POST["tentativas_num"],MUNICIPIO_ID_SIZE);
		
		// se os campos obrigatórios passarem na validação
		if( $nome!==false && $idperfil!==false && $login!==false  && $senha!==false 
		 && $rsenha!==false && $idmunicipio!==false  && $email1!==false && $celular!==false
		&& $expira!==false && $limita!==false && (($alterasenha=="sim" && $senha==$rsenha) || ($alterasenha=="nao"))){

			//valida para entrar na área de segurança da SQL
			$idusuario=sqlTrataInteiro($idusuario);
			$nome=sqlTrataString($nome);
			$idperfil=sqlTrataInteiro($idperfil);
			$nome_instituicao=sqlTrataString($nome_instituicao);
			$login=strtolower(sqlTrataString($login));
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
			$tentativas_num=sqlTrataInteiro($tentativas_num);

			//se entrar nesse IF os dados já podem ser inseridos no banco
			if($idusuario!==false && $nome!==false && $idperfil!==false
				 && $login!==false  && $senha!==false && $rsenha!==false 
				 && $idmunicipio!==false  && $email1!==false 
				 && $celular!==false && $dtlimite!==false){
			
				
				// Instanciar as infos do usuario
				$Usuario = new Usuario();
				$Usuario->setId($idusuario);
				$Usuario->setNome($nome);

				$Usuario->setPerfil($idperfil);
				$Usuario->setNomeInstituicao($nome_instituicao);			
				$Usuario->setLogin(strtoupper($login));
				if($alterasenha=="sim"){
					$Usuario->setSenha(codifica($senha));
				}	
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
				$Usuario->setTentativasNum($tentativas_num);

				// Instanciar o DAO para inserir o usuario na base
				$UsuarioDAO = new UsuarioDAO();
				// Chama a função de verificação de duplicidade que só resulta FALSE caso dê algum problema.
				$possuiDuplicidade = $UsuarioDAO->isDuplicatedEdit($Usuario);

				if($possuiDuplicidade!==false){
					enviaMsg("erro","Usuário não atualizado","O login <strong><em>".$login."</em></strong> já está em uso");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=edit_user.php?id=".$idusuario."\">";
					exit();
				}

				$atualizou = $UsuarioDAO->update($Usuario);
				if($atualizou){

					//deleta registros anteriores em usuario_processo
					$deletouAnteriores = $UsuarioDAO->deleteLimitacaoProcesso($Usuario);
					//$limita 	=>	sim OU nao
					if($limita=="sim" && isset($limitaprocessos) && sizeof($limitaprocessos)>0 && $limitaprocessos!=0){
						foreach($limitaprocessos as $pro){
							$inseriu = $UsuarioDAO->insertLimitacaoProcesso($pro,$idusuario);
							if(!$inseriu){
								enviaMsg("erro","Usuário atualizado com erros","A restrição de visibilidade de processos não pôde ser efetuada");
								echo "<meta http-equiv=\"refresh\" content=\"0; url=index_user.php\">";
								exit();
							}
						}
					}
					
					//Recupera e altera dados do Processo para salvar os valores e não os IDS no histórico
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
					
					//registra ação no log
					$obs = $Usuario->toLog();
					$obs.= "Alterou Senha? ".$alterasenha.APP_LINE_BREAK;
					$obs.= "Restringe visualização a determinados Processos? ".$limita.APP_LINE_BREAK;
					if($limita=="sim"){
						if(isset($limitaprocessos) && sizeof($limitaprocessos)>0){
							//carrega DAO de processos para descobrir o nome e tipo dos processos
							$ProcessoDAO = new ProcessoDAO();
							$nomeprocessos=$ProcessoDAO->getAll();

							$obs.="Processos Visíveis:";
							for($i=0;$i<sizeof($limitaprocessos);$i++){
								for($j=0;$j<sizeof($nomeprocessos);$j++){
									if($limitaprocessos[$i]==$nomeprocessos[$j]["idprocesso"]){
										$obs.=$nomeprocessos[$j]["numero"];
									}
								}
								
								if($i<sizeof($limitaprocessos)-1){
									$obs.=',';
								}
							}							
						}
						$obs.=APP_LINE_BREAK;
					} 
					require_once("../dao/HistoricoDAO.php");
					require_once("../model/Historico.php");
					$Historico = new Historico();
					$Historico->setAcao(LOG_EDIT_USER);
					$Historico->setProcesso(0);
					$Historico->setDocumento(0);
					$Historico->setObs(sqlTrataString($obs));
					$HistoricoDAO = new HistoricoDAO();
					$idhistorico = $HistoricoDAO->insert($Historico);

					if($idhistorico){
						enviaMsg("sucesso","Usuário atualizado com sucesso");
						echo "<meta http-equiv=\"refresh\" content=\"0; url=index_user.php\">";
						exit();
					}else{
						enviaMsg("erro","Usuário atualizado com erros","O histórico não pôde ser salvo");
						echo "<meta http-equiv=\"refresh\" content=\"0; url=index_user.php\">";
						exit();
					}

				//fim if($idprocesso)
				}else{

					enviaMsg("erro","Usuário não atualizado","Tente novamente mais tarde");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=add_user.php\">";
					exit();

				}
				
			}else{
				enviaMsg("erro","Usuário não atualizado","Os dados informados estão inválidos");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=add_user.php\">";
				exit();								
			}
			
		}else{

			enviaMsg("erro","Usuário não atualizado","Os dados inseridos são inválidos");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=add_user.php\">";
			exit();		

		}


	//se não enviou o formulário, então precisa carregar as infos do usuário:
	}else{

				// Instanciar o DAO para recuperar infos da base
				$UsuarioDAO = new UsuarioDAO();
				$result=$UsuarioDAO->getOne($idusuario);

				if(!$result){
					enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou dados inválidos");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
					exit();
				}else{
					// Instanciar as infos do usuario
					$Usuario = new Usuario();
					$Usuario->setId($idusuario);
					$Usuario->setNome($result["nome"]);
					$Usuario->setPerfil($result["idperfil"]);
					$Usuario->setSubsecao($result["idsubsecao"]);
					$Usuario->setCelular($result["celular"]);
					$Usuario->setTelefone($result["telefone"]);
					$Usuario->setEmail1($result["email1"]);
					$Usuario->setEmail2($result["email2"]);
					$Usuario->setLogin($result["login"]);
					$Usuario->setDtExpiracao($result["dtexpiracao"]);	
					$Usuario->setNomeInstituicao($result["nome_instituicao"]);
					$Usuario->setMunicipio($result["idmunicipio"]);
					$Usuario->setTentativasNum($result["tentativas_num"]);

					//verifica se o usuário possui processos cadastrados
					$resultprocessos = $UsuarioDAO->getProcessos($idusuario);
					$usuarioprocessos=array();
					foreach ($resultprocessos as $p) {
						$usuarioprocessos[]=$p["idprocesso"];
					}
				}

	?>

	<div id="conteudo_borda">
	    <div  id="conteudo">
			<!-- <form  .... onSubmit="return validaForm('edit_user','nome','idperfil','nome_instituicao','login','alterasenha','senha','rsenha','idmunicipio','email1','celular','expira','novoato');"> -->	            
	        <form id="edit_user" name="edit_user" action="edit_user.php?id=<?php echo $Usuario->getId(); ?>" method="post" class="form-horizontal">

	        	<input type="hidden" name="id" id="id" value="<?php echo $Usuario->getId(); ?>">

	            <div class="form-group">
	                <label class="col-sm-10 control-label">Nome Completo</label>
	                <div class="col-lg-8">
	                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome Completo" maxlength="<?php echo USUARIO_NOME_SIZE-30; ?>"  value="<?php echo htmlentities($Usuario->getNome()); ?>">
	                    <span id="helpBlock" class="help-block">Este campo é obrigatório e aceita até <strong><?php echo USUARIO_NOME_SIZE; ?></strong> caracteres</span>
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
									if($perfil[$i]["idperfil"] == $Usuario->getPerfil()){
										$selecionado = "selected=\"selected\"";
									}else{
										$selecionado = "";
									}
									echo "<option value=\"".
									$perfil[$i]["idperfil"]."\" $selecionado >".
									$perfil[$i]["nome"]."</option>";
							}
						}
						?>
	                  </select>
	                  <span id="helpBlock" class="help-block">Este campo é obrigatório</span>
	                </div>
	            </div>

	            <?php
	            //verifica se o perfil atual selecionado é o de instituição, se for não oculta o campo abaixo
	            if($Usuario->getPerfil()==PERFIL_IDINSTITUICAO){
	            	$campoinstituicao="display:block !important";
	            }else{
	            	$campoinstituicao="";
	            }
	            ?>
	            <div class="form-group campoviaselect" id="campoviaselect<?php echo PERFIL_IDINSTITUICAO; ?>" style="<?php echo $campoinstituicao; ?>">
	                <label class="col-sm-10 control-label">Nome da Instituição</label>
	                <div class="col-lg-5">
	                    <input type="text" class="form-control " id="nome_instituicao" name="nome_instituicao" placeholder="Nome da Instituição" maxlength="<?php echo USUARIO_NOMEINSTITUICAO_SIZE; ?>" value="<?php echo htmlentities($Usuario->getNomeInstituicao()); ?>">
	                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
	                </div>
	            </div>

	            <div class="form-group">
	                <label class="col-sm-10 control-label">Nome de Usuário (<em>login</em>)</label>
	                <div class="col-lg-5">
	                    <input type="text" class="form-control camposomente09AZponto<?php echo USUARIO_LOGIN_SIZE; ?>" id="login" name="login" placeholder="Login do Usuário" maxlength="<?php echo USUARIO_LOGIN_SIZE; ?>" value="<?php echo $Usuario->getLogin(); ?>">
	                    <span id="helpBlock" class="help-block">Este campo é obrigatório e aceita até <strong><?php echo USUARIO_LOGIN_SIZE; ?></strong> caracteres, padrão: nome.sobrenome</span>
	                </div>
	            </div>

	            <div class="form-group">
		            <label class="col-sm-11 control-label">Alterar senha do usuário?</label>
		            <div class="col-lg-2">
		              <select id="alterasenha"  name="alterasenha" class="form-control" onchange="$('.div_senha, .div_rsenha').toggle('slow');">
		                <option value="nao" selected="selected">Não</option>
		                <option value="sim">Sim</option>
		              </select>
		            </div>
		        </div> 

	            <div class="form-group div_senha" style="display:none;">
	                <label class="col-sm-10 control-label">Senha</label>
	                <div class="col-lg-4">
	                    <input type="password" class="form-control camposenha" id="senha" name="senha" placeholder="Senha do Usuário" maxlength="<?php echo USUARIO_SENHA_SIZE; ?>" autocomplete="off">
	                    <span id="helpBlock" class="help-block"><?php echo '* Campo obrigatório - de 6 a '.USUARIO_SENHA_SIZE.' caracteres, pelo menos: 1 letra maiúscula, 1 letra minúscula, 1 número e 1 caracter especial exceto aspas simples (\') ou aspas duplas (") '; ?></span>
	                </div>
	            </div>

	            <div class="form-group div_rsenha" style="display:none;">
	                <label class="col-sm-10 control-label">Redigite a Senha</label>
	                <div class="col-lg-4">
	                    <input type="password" class="form-control camposenha" id="rsenha" name="rsenha" placeholder="Redigite a Senha do Usuário" maxlength="<?php echo USUARIO_SENHA_SIZE; ?>" autocomplete="off">
	                    <span id="helpBlock" class="help-block">* Campo obrigatório</span>
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
								if($dados[$i]["idmunicipio"] == $Usuario->getMunicipio()){
									$selecionado = "selected=\"selected\"";
								}else{
									$selecionado = "";
								}
								echo "<option value=\"".$dados[$i]["idmunicipio"]."\" $selecionado >".$dados[$i]["nome"]."</option>";
							}
						}
						?>
	                  </select>
	                  <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
	                </div>
	            </div>

		        <div class="form-group">
	                <label class="col-sm-10 control-label">Email Principal</label>
	                <div class="col-lg-8">
	                    <input type="text" class="form-control campodeemail<?php echo USUARIO_EMAIL_SIZE; ?>" id="email1" name="email1" placeholder="E-mail Principal do Usuário" maxlength="<?php echo USUARIO_EMAIL_SIZE; ?>" value="<?php echo htmlentities($Usuario->getEmail1()); ?>">
	                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
	                </div>
	            </div>

	            <div class="form-group">
	                <label class="col-sm-10 control-label">Email Secundário</label>
	                <div class="col-lg-8">
	                    <input type="text" class="form-control campodeemail<?php echo USUARIO_EMAIL_SIZE; ?>" id="email2" name="email2" placeholder="E-mail Secundário do Usuário" maxlength="<?php echo USUARIO_EMAIL_SIZE; ?>" value="<?php echo htmlentities($Usuario->getEmail2()); ?>">
	                </div>
	            </div>

	            <div class="form-group">
	                <label class="col-sm-10 control-label">Celular (com DDD abreviado para 2 dígitos. Ex.: 48)</label>
	                <div class="col-lg-3">
	                    <input type="text" class="form-control campocelular" id="celular" name="celular" placeholder="Celular do Usuário" maxlength="<?php echo USUARIO_FONE_SIZE+1; ?>" value="<?php echo exibeTelefone($Usuario->getCelular()); ?>">
	                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
	                </div>
	            </div>

	            <div class="form-group">
	                <label class="col-sm-10 control-label">Telefone</label>
	                <div class="col-lg-3">
	                    <input type="text" class="form-control campocelular" id="telefone" name="telefone" placeholder="Telefone do Usuário" maxlength="<?php echo USUARIO_FONE_SIZE+1; ?>" value="<?php echo exibeTelefone($Usuario->getTelefone()); ?>">
	                </div>
	            </div>

		        <div class="form-group">
		            <label class="col-sm-11 control-label">Credencial expira?</label>
		            <div class="col-lg-2">
		              <select id="expira"  name="expira" class="form-control" onchange="$('#div_novoato').toggle('slow');">
		                <option value="nao">Não</option>
		                <?php
		                if($Usuario->getDtExpiracao() > 0){
		                	echo '<option selected="selected" value="sim">Sim</option>';
		                }else{
		                	echo '<option value="sim">Sim</option>';
		                } ?>
		                
		              </select>
		            </div>
		        </div> 
				
		        <div class="form-group" id="div_novoato" <?php if($Usuario->getDtExpiracao() == 0){ echo 'style="display:none;"'; }?> >
		        	<label class="col-sm-12 control-label">Data Limite</label>
		        	<div class="col-lg-3">
		        	<div class="input-group date">
			            <input type="text" class="form-control" id="novoato" name="novoato" placeholder="Data Limite de Acesso" maxlength="<?php echo USUARIO_DTEXPIRACAO_SIZE+2; ?>" value="<?php echo exibeData($Usuario->getDtExpiracao()); ?>" autocomplete="off">
			            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
		        	</div>
		        	</div>
		        </div>

		        <div class="form-group">
		            <label class="col-sm-11 control-label">Limitar acesso a processos específicos?</label>
		            <div class="col-lg-2">
		              <select id="limita"  name="limita" class="form-control" onchange="$('#div_limitaprocessos').toggle('slow');">
		                <option value="nao">Não</option>
		                <?php 
		                if(sizeof($usuarioprocessos)>0){
		                	echo '<option selected="selected" value="sim">Sim</option>';
		                }else{
		                	echo '<option value="sim">Sim</option>';
		                }
		                ?>
		              </select>
		            </div>
		        </div> 

	            <div class="form-group" id="div_limitaprocessos" <?php if(sizeof($usuarioprocessos)<=0){ echo 'style="display:none;"'; } ?> >
	                <label class="col-sm-11 control-label">Clique nos processos que o usuário irá visualizar</label>
	                <div class="col-lg-5">
		              <select multiple id="limitaprocessos[]"  name="limitaprocessos[]" class="form-control">
						<?php
	                    // Instanciar o DAO e retornar dados da tabela
	                    $ProcessoDAO = new ProcessoDAO();
	                    $processo = $ProcessoDAO->getAll();
	                    if(sizeof($processo)>0){
							for($i=0;$i<sizeof($processo);$i++){

									if( in_array($processo[$i]["idprocesso"],$usuarioprocessos) ){
										$selecionado="selected=\"selected\"";
									}else{
										$selecionado="";
									}

									echo "<option value=\"".
									$processo[$i]["idprocesso"]."\" $selecionado >Processo ".
									$processo[$i]["numero"]."</option>";
							}
						}
						?>
		              </select>
	                <span id="helpBlock" class="help-block">Segure a tecla <em>CTRL</em> para selecionar mais de um processo.</span>
	                </div>
	            </div>

	            <div class="form-group">
		            <label class="col-sm-12 control-label">Número de tentativas inválidas de login (limite: <?php echo APP_MAX_LOGIN_ATTEMPTS; ?>)</label>
		            <div class="col-lg-2">
	                    <input type="number" class="form-control" id="tentativas_num" name="tentativas_num" placeholder="Nenhuma" value="<?php echo $Usuario->getTentativasNum(); ?>">
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

	}//fim do else (se não tiver enviado o formulário)

}//fim do if $iduser!==false

}//fim do if GET["id"]
else{

//QUANDO o usuário estiver atualizando dados de si mesmo
	



	//conecta no banco e instacia uma conexão com o Registry
	require_once("../conexao.php");
	require_once("../model/Registry.php");
	//carrega DAO's das chaves estrangeiras
	require_once("../dao/UsuarioDAO.php");

	//carrega Models
	require_once('../model/Usuario.php');

	// Armazenar essa instância (conexão) no Registry - conecta uma só vez
	$registry = Registry::getInstance();
	$registry->set('Connection', $myBD);

	require_once("../menu_topo.php");
	

	$idusuario = $_SESSION["USUARIO"]["idusuario"];
	if(isset($_POST["idusuario"]) && !empty($_POST)){

		//valida entradas do usuário
		$nome=validaLiteral(trim($_POST["nome"]),USUARIO_NOME_SIZE);
		$senha=validaLiteral($_POST["senha"],USUARIO_SENHA_SIZE+30);//+30 devido aos caract especiais
		$rsenha=validaLiteral($_POST["rsenha"],USUARIO_SENHA_SIZE+30);//+30 devido aos caract especiais
		//se for instituição, valida o nome_instituicao
		if($_SESSION["USUARIO"]["idperfil"] == PERFIL_IDINSTITUICAO){
			$nome_instituicao=validaLiteral($_POST["nome_instituicao"],USUARIO_NOMEINSTITUICAO_SIZE);
		}
		$email1=validaLiteral($_POST["email1"],USUARIO_EMAIL_SIZE);
		$email2=validaLiteral($_POST["email2"],USUARIO_EMAIL_SIZE);
		$celular=validaLiteral($_POST["celular"],USUARIO_FONE_SIZE+1);
		$telefone=validaLiteral($_POST["telefone"],USUARIO_FONE_SIZE+1);

		//alterasenha recebe o valor sim ou nao para atualizar ou nao a senha
		$alterasenha = $_POST["alterasenha"];
		// se os campos obrigatórios passarem na validação
		if( $email1!==false && $celular!==false && ($alterasenha=="sim" && $senha!==false && $rsenha!==false && $senha==$rsenha || $alterasenha=="nao")){

			$nome=sqlTrataString($nome);
			$senha=sqlTrataString($senha);
			$rsenha=sqlTrataString($rsenha);
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
			if($_SESSION["USUARIO"]["idperfil"] == PERFIL_IDINSTITUICAO){
				$nome_instituicao=sqlTrataString($nome_instituicao);
			}

			//se entrar nesse IF os dados já podem ser atualizados no banco
			if($email1!==false && $nome!==false && $celular!==false && $senha!==false && $rsenha!==false){

				// Instanciar as infos do usuario
				$Usuario = new Usuario();
				$Usuario->setId($idusuario);
				if($alterasenha=="sim"){
					$Usuario->setSenha(codifica($senha));
				}
				$Usuario->setNome($nome);
				if($_SESSION["USUARIO"]["idperfil"] == PERFIL_IDINSTITUICAO){
					$Usuario->setNomeInstituicao($nome_instituicao);
				}else{
					$Usuario->setNomeInstituicao(NULL);
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
				// Instanciar o DAO para inserir o usuario na base
				$UsuarioDAO = new UsuarioDAO();
				$atualizou=$UsuarioDAO->updateMyUser($Usuario);
				if($atualizou){
					//registra ação no log
					$obs = $Usuario->toLog();
					$obs .="Alterou senha? ".$alterasenha.APP_LINE_BREAK;
					require_once("../dao/HistoricoDAO.php");
					require_once("../model/Historico.php");
					$Historico = new Historico();
					$Historico->setAcao(LOG_UPDATE_USER);
					$Historico->setProcesso(0);
					$Historico->setDocumento(0);
					$Historico->setObs(sqlTrataString($obs));
					$HistoricoDAO = new HistoricoDAO();
					$salvouHistorico = $HistoricoDAO->insert($Historico);

					if($salvouHistorico){
						enviaMsg("sucesso","Suas informações foram atualizadas com sucesso");
						echo "<meta http-equiv=\"refresh\" content=\"0; url=edit_user.php\">";
						exit();
					}else{
						enviaMsg("erro","Suas informações foram atualizadas com erro","O histórico não pôde ser salvo");
						echo "<meta http-equiv=\"refresh\" content=\"0; url=edit_user.php\">";
						exit();
					}


				}else{

					enviaMsg("erro","Suas informações não foram atualizadas","Tente novamente mais tarde");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=edit_user.php\">";
					exit();

				}
			}else{
				//dados invalidos
				enviaMsg("erro","Usuário não atualizado","Os dados inseridos estão inválidos");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=edit_user.php\">";
				exit();

			}
			
		}else{
			//dados invalidos			
			enviaMsg("erro","Usuário não atualizado","Os dados inseridos são inválidos");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=edit_user.php\">";
			exit();
		}


	}

	echo "	<script>
				$('.navbar-left > li').removeClass(\"active\");
				$('#edit_user_php').addClass(\"active\");
			</script>";

	// Instanciar o DAO para recuperar infos da base
	$UsuarioDAO = new UsuarioDAO();
	$result=$UsuarioDAO->getOne($idusuario);

	if(!$result){
		enviaMsg("erro","O usuário selecionado não foi encontrado ou está inativo");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index.php\">";
		exit();
	}else{
		// Instanciar as infos do usuario
		$Usuario = new Usuario();
		$Usuario->setId($idusuario);
		$Usuario->setNome($result["nome"]);
		$Usuario->setLogin($result["login"]);
		$Usuario->setPerfil($result["idperfil"]);
		$Usuario->setEmail1($result["email1"]);
		$Usuario->setEmail2($result["email2"]);
		$Usuario->setCelular($result["celular"]);
		$Usuario->setTelefone($result["telefone"]);
		$Usuario->setDtExpiracao($result["dtexpiracao"]);
		if($Usuario->getPerfil() == PERFIL_IDINSTITUICAO){
			$Usuario->setNomeInstituicao($result["nome_instituicao"]);	
		}		
		
	}

	?>

	<div id="conteudo_borda">
	    <div  id="conteudo">
	        <!-- <form name="edit_user" action="edit_user.php" method="post" class="form-horizontal" onSubmit="return validaForm('edit_user','alterasenha','senha','rsenha','nome','nome_instituicao','email1','celular');"   autocomplete="off"> -->
	        <form id="edit_user" name="edit_user" action="edit_user.php" method="post" class="form-horizontal" autocomplete="off">

	        	<input type="hidden" name="idusuario" id="idusuario" value="<?php echo $idusuario; ?>">

	            <div class="form-group">
	                <label class="col-sm-10 control-label">Seu Login</label>
	                <div class="col-lg-5">
	                    <input type="text" class="form-control" id="login" name="login" placeholder="Seu nome de usuário" value="<?php echo $Usuario->getLogin(); ?>" readonly="readonly">
	                </div>
	            </div>

	            <div class="form-group">
		            <label class="col-sm-11 control-label">Deseja alterar sua senha?</label>
		            <div class="col-lg-2">
		              <select id="alterasenha"  name="alterasenha" class="form-control" onchange="$('.div_senha, .div_rsenha').toggle('slow');">
		                <option value="nao" selected="selected">Não</option>
		                <option value="sim">Sim</option>		                
		              </select>
		            </div>
		        </div>

	            <div class="form-group div_senha" style="display:none;">
	                <label class="col-sm-10 control-label">Senha</label>
	                <div class="col-lg-4">
	                    <input type="password" class="form-control camposenha" id="senha" name="senha" placeholder="Digite sua nova senha" maxlength="<?php echo USUARIO_SENHA_SIZE; ?>">
	                    <span id="helpBlock" class="help-block"><?php echo '* Campo obrigatório - de 6 a '.USUARIO_SENHA_SIZE.' caracteres, pelo menos: 1 letra maiúscula, 1 letra minúscula, 1 número e 1 caracter especial exceto aspas simples (\') ou aspas duplas (") '; ?></span>
	                </div>
	            </div>

	            <div class="form-group div_rsenha" style="display:none;">
	                <label class="col-sm-10 control-label">Redigite a Senha</label>
	                <div class="col-lg-4">
	                    <input type="password" class="form-control camposenha" id="rsenha" name="rsenha" placeholder="Redigite sua nova senha" maxlength="<?php echo USUARIO_SENHA_SIZE; ?>">
	                    <span id="helpBlock" class="help-block">* Campo obrigatório</span>
	                </div>
	            </div>

	            <div class="form-group">
	                <label class="col-sm-10 control-label"><?php if($Usuario->getPerfil() == PERFIL_IDINSTITUICAO){ echo "Nome do Responsável pela Comissão na sua Instituição"; }else{ echo "Seu Nome"; } ?></label>
	                <div class="col-lg-8">
	                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome Completo" maxlength="<?php echo USUARIO_NOME_SIZE; ?>"  value="<?php echo $Usuario->getNome(); ?>">
	                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
	                </div>
	            </div>

	            <?php
	            //se for um usuário do tipo INSTITUIÇÃO, exibe o campo para atualizar o nome da instituição
	            if($Usuario->getPerfil() == PERFIL_IDINSTITUICAO){
	            ?>

	            <div class="form-group">
	                <label class="col-sm-10 control-label">Nome da sua Instituição</label>
	                <div class="col-lg-5">
	                    <input type="text" class="form-control " id="nome_instituicao" name="nome_instituicao" placeholder="Nome da Instituição" maxlength="<?php echo USUARIO_NOMEINSTITUICAO_SIZE; ?>" value="<?php echo htmlentities($Usuario->getNomeInstituicao()); ?>">
	                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
	                </div>
	            </div>

	            <?php
	            }//fim IF se for um IDPERFIL = instituição
	            else{
	            	echo '<input type="hidden" name="nome_instituicao" id="nome_instituicao" value="0000">';
	            }
	            ?>
	            
	            <div class="form-group">
	                <label class="col-sm-10 control-label">Seu E-mail Principal</label>
	                <div class="col-lg-8">
	                    <input type="text" class="form-control campodeemail<?php echo USUARIO_EMAIL_SIZE; ?>" id="email1" name="email1" placeholder="E-mail Principal do Usuário" maxlength="<?php echo USUARIO_EMAIL_SIZE; ?>" value="<?php echo htmlentities($Usuario->getEmail1()); ?>">
	                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?><br>** Este campo aceita até <strong><?php echo USUARIO_EMAIL_SIZE; ?></strong> caracteres</span>
	                </div>
	            </div>

	            <div class="form-group">
	                <label class="col-sm-10 control-label">Seu E-mail Secundário</label>
	                <div class="col-lg-8">
	                    <input type="text" class="form-control campodeemail<?php echo USUARIO_EMAIL_SIZE; ?>" id="email2" name="email2" placeholder="E-mail Secundário do Usuário" maxlength="<?php echo USUARIO_EMAIL_SIZE; ?>" value="<?php echo htmlentities($Usuario->getEmail2()); ?>">
	                    <span id="helpBlock" class="help-block">* Este campo aceita até <strong><?php echo USUARIO_EMAIL_SIZE; ?></strong> caracteres</span>
	                </div>
	            </div>

	            <div class="form-group">
	                <label class="col-sm-10 control-label">Celular</label>
	                <div class="col-lg-3">
	                    <input type="text" class="form-control campocelular" id="celular" name="celular" placeholder="Celular do Usuário" maxlength="<?php echo USUARIO_FONE_SIZE+1; ?>" value="<?php echo exibeTelefone($Usuario->getCelular()); ?>">
	                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
	                </div>
	            </div>

	            <div class="form-group">
	                <label class="col-sm-10 control-label">Telefone</label>
	                <div class="col-lg-3">
	                    <input type="text" class="form-control campocelular" id="telefone" name="telefone" placeholder="Telefone do Usuário" maxlength="<?php echo USUARIO_FONE_SIZE+1; ?>" value="<?php echo exibeTelefone($Usuario->getTelefone()); ?>">
	                </div>
	            </div>
	            
	            <div class="form-group" style="margin-top:40px;">
	              <div class="col-lg-10">
	                <button type="reset" id="cancelar" class="btn btn-default index_pro.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
	                <button type="submit" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salvar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
	              </div>
	            </div>

	        </form>
		</div>
	</div>
	<?php

	include_once("../menu_rodape.php");













}
?>
