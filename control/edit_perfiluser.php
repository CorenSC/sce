<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");

if($_REQUEST["id"]){

$idperfil=validaInteiro($_REQUEST["id"],PERFIL_ID_SIZE);

if($idperfil!==false){

	//conecta no banco e instacia uma conexão com o Registry
	require_once("../conexao.php");
	require_once("../model/Registry.php");

	//carrega Models e DAO
	require_once('../model/Perfil.php');
	require_once('../dao/PerfilDAO.php');

	// Armazenar essa instância (conexão) no Registry - conecta uma só vez
	$registry = Registry::getInstance();
	$registry->set('Connection', $myBD);

	// Instanciar o DAO
	$perfilDAO = new PerfilDAO();

	require_once("../menu_topo.php");


	//SUBMENU DE AÇÕES
	require_once('submenu_user.php');
	//FIM SUBMENU DE AÇÕES
	

	//se tiver enviado o formulário
	if( !empty($_POST) ){

		//valida entradas do usuário
		//$idusuario
		$nome=validaLiteral($_POST["nome"],USUARIO_NOME_SIZE);
		if(isset($_POST["funcoes"])){
			$funcoes=$_POST["funcoes"];
		}else{
			$funcoes=0;
		}

		// se os campos obrigatórios passarem na validação
		if( $nome!==false && verificaFuncaoUsuario(FUNCAO_PERFIL_EDIT)!==false){

			//valida para entrar na área de segurança da SQL
			$nome=sqlTrataString($nome);

			//se entrar nesse IF os dados já podem ser inseridos no banco
			if($nome!==false){		
				
				// Instanciar as infos do usuario
				$perfil = new Perfil();
				$perfil->setId($idperfil);
				$perfil->setNome($nome);
				// Chama a função que verifica duplicidade do registro
				$possuiDuplicidade = $perfilDAO->isDuplicatedEdit($perfil);
				if($possuiDuplicidade!==false){
					enviaMsg("erro","Erro","O nome <strong>$nome</strong> já está em uso, por favor escolha outro");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=edit_perfiluser.php?id=".$idperfil."\">";
					exit();
				}
				//insere no banco
				$atualizou = $perfilDAO->update($perfil);
				
				//remove funções anteriores
				$perfilDAO->deleteFuncoes($idperfil);
				//adiciona novas funcoes
				if($funcoes!=0 && sizeof($funcoes)>0){
					for($i=0;$i<sizeof($funcoes);$i++){				
						$inseriufuncoes = $perfilDAO->insertFuncao($idperfil,$funcoes[$i]);
						if(!$inseriufuncoes){
							enviaMsg("erro","Erro","As funções do perfil não foram cadastradas corretamente, tente novamente mais tarde");
							echo "<meta http-equiv=\"refresh\" content=\"0; url=edit_perfiluser.php\">";
							exit();
						}
					}
				}
				
				//registra ação no log
				$log_obs = $perfil->toLog();
				if($funcoes!=0 && sizeof($funcoes)>0){
					$log_obs.="- Funções atreladas: ".APP_LINE_BREAK;
					for($i=0;$i<sizeof($funcoes);$i++){
						$dados=$perfilDAO->getOneFuncao($funcoes[$i]);
						$log_obs.=($i+1).' - '.$dados["nome"].APP_LINE_BREAK;
					}
				}else{
					$log_obs.="Nenhuma função atrelada".APP_LINE_BREAK;
				}

				//instancia DAO e insere
				require_once("../dao/HistoricoDAO.php");
				require_once("../model/Historico.php");
				$Historico = new Historico();
				$Historico->setAcao(LOG_EDIT_PERFIL);
				$Historico->setProcesso(0);
				$Historico->setDocumento(0);
				$Historico->setObs($log_obs);
				$HistoricoDAO = new HistoricoDAO();
				$inseriuHistorico = $HistoricoDAO->insert($Historico);

				if($inseriuHistorico){
					enviaMsg("sucesso","Perfil atualizado com sucesso");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index_perfiluser.php\">";
					exit();
				}else{
					enviaMsg("erro","Perfil atualizado com erros","O histórico não pôde ser salvo");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index_perfiluser.php\">";
					exit();
				}
								
			}else{
				enviaMsg("erro","Perfil não atualizado","Os dados informados estão inválidos");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_perfiluser.php\">";
				exit();
			}
			
		}else{
			enviaMsg("erro","Perfil não atualizado","Os dados inseridos são inválidos");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=index_perfiluser.php\">";
			exit();
		}

	}///fim if !empty POST

	//se não enviou o formulário, exibe as infos do perfil
	else{

		//retorna infos do banco (do perfil e das funções do perfil)
		$perfilatual = $perfilDAO->getOne($idperfil);
		$perfilfuncoes = $perfilDAO->getFunctions($idperfil);
		if($perfilatual!==false){
			// Instanciar as infos do usuario
			$perfil = new Perfil();
			$perfil->setNome($perfilatual["nome"]);
				
			$perfilfuncoesarray = array();
			for($i=0;$i<sizeof($perfilfuncoes);$i++){			
				$perfilfuncoesarray[]=$perfilfuncoes[$i]["idfuncao"];
			}

		}else{
		
			enviaMsg("erro","Erro ao carregar perfil, tente novamente mais tarde");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=index_perfiluser.php\">";
			exit();

		}

	}

	?>

	<div id="conteudo_borda">
	    <div  id="conteudo">
	        <!-- <form name="edit_perfiluser" action="edit_perfiluser.php" method="post" class="form-horizontal" onSubmit="return validaForm('edit_perfiluser','nome');" > -->
	        <form id="edit_perfiluser" name="edit_perfiluser" action="edit_perfiluser.php" method="post" class="form-horizontal">

	        	<input type="hidden" name="id" id="id" value="<?php echo $idperfil; ?>">

	            <div class="form-group">
	                <label class="col-sm-10 control-label">Nome do Perfil</label>
	                <div class="col-lg-8">
	                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome do Perfil" maxlength="<?php echo PERFIL_NOME_SIZE-10; ?>" value="<?php echo htmlentities($perfil->getNome()); ?>">
	                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?><br>** Este campo aceita até <strong><?php echo PERFIL_NOME_SIZE; ?></strong> caracteres</span>
	                </div>
	            </div>

	        	<div class="form-group">
	                <label class="col-sm-11 control-label">Clique nas funções deste perfil</label>
	                <div class="col-lg-4">
		            <select multiple id="funcoes[]"  name="funcoes[]" class="form-control">
						<?php
	                    $funcao = $perfilDAO->getAllFuncoes();
	                    if(sizeof($funcao)>0){
	                    	$categoria_anterior="";
							for($i=0;$i<sizeof($funcao);$i++){

								if($categoria_anterior!=$funcao[$i]["categoria"]){
									echo "<optgroup label=\"".$funcao[$i]["categoria"]."\"></optgroup>";
								}

								if( in_array($funcao[$i]["idfuncao"],$perfilfuncoesarray) ){
									$selecionado="selected=\"selected\"";
								}else{
									$selecionado="";
								}
								
								echo "<option value=\"".
								$funcao[$i]["idfuncao"]."\" $selecionado>&nbsp;&nbsp;&nbsp;".
								$funcao[$i]["nome"]."</option>";
								$categoria_anterior = $funcao[$i]["categoria"];
							}
						}
						?>
		            	</select>
	                	<span id="helpBlock" class="help-block">Segure a tecla <em>CTRL</em> para selecionar mais de uma função.</span>
	                </div>
	            </div>
	            
	            <div class="form-group" style="margin-top:20px;">
	              <div class="col-lg-10">
	                <button type="reset" id="cancelar" class="btn btn-default index_perfiluser.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
	                <button type="submit" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salvar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
	              </div>
	            </div>

	        </form>
		</div>
	</div>
	<?php

	include_once("../menu_rodape.php");

}//fim do if $iduser!==false

}//fim do if GET["id"]
else{
	enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou dados inválidos");
	echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
	exit();
}
?>
