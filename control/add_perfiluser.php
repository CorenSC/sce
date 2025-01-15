<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");

if(verificaFuncaoUsuario(FUNCAO_PERFIL_ADD)!==false){

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
	$nome=validaLiteral($_POST["nome"],PERFIL_NOME_SIZE);
	if(isset($_POST["funcoes"])){
		$funcoes=$_POST["funcoes"];
	}else{
		$funcoes=0;
	}
	

	// se os campos obrigatórios passarem na validação
	if( $nome!==false && verificaFuncaoUsuario(FUNCAO_PERFIL_ADD)!==false){

		//valida para entrar na área de segurança da SQL
		$nome=sqlTrataString($nome);

		//se entrar nesse IF os dados já podem ser inseridos no banco
		if($nome!==false){		
			
			// Instanciar as infos do usuario
			$perfil = new Perfil();
			$perfil->setNome($nome);		
			// Chama a função que verifica duplicidade do registro
			$possuiDuplicidade = $perfilDAO->isDuplicated($perfil);
			if($possuiDuplicidade!==false){
				enviaMsg("erro","Erro","O nome <strong>$nome</strong> já está em uso, por favor escolha outro");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=add_perfiluser.php\">";
				exit();
			}
			//insere no banco
			$idperfil = $perfilDAO->insert($perfil);

			if($idperfil){

				$perfil->setId($idperfil);

					if($funcoes!=0 && sizeof($funcoes)>0){
						for($i=0;$i<sizeof($funcoes);$i++){
							$idfuncao = $perfilDAO->insertFuncao($idperfil,$funcoes[$i]);
							if(!$idfuncao){
								enviaMsg("erro","Erro","As funções do perfil não foram cadastradas corretamente, tente novamente mais tarde");
								echo "<meta http-equiv=\"refresh\" content=\"0; url=add_perfiluser.php\">";
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
					$Historico->setAcao(LOG_ADD_PERFIL);
					$Historico->setProcesso(0);
					$Historico->setDocumento(0);
					$Historico->setObs($log_obs);
					$HistoricoDAO = new HistoricoDAO();
					$inseriuHistorico = $HistoricoDAO->insert($Historico);

					if($inseriuHistorico){			
						enviaMsg("sucesso","Perfil cadastrado com sucesso");
						echo "<meta http-equiv=\"refresh\" content=\"0; url=index_perfiluser.php\">";
						exit();
					}else{
						enviaMsg("erro","Perfil cadastrado com erros","O histórico não pôde ser salvo");
						echo "<meta http-equiv=\"refresh\" content=\"0; url=index_perfiluser.php\">";
						exit();
					}

			//fim if($idprocesso)
			}else{

				//perfil n cadastrado
				enviaMsg("erro","Perfil não cadastrado","Tente novamente mais tarde");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_perfiluser.php\">";
				exit();

			}
			
			
		}else{
			//dados invalidos
			enviaMsg("erro","Perfil não cadastrado","Os dados informados estão inválidos");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=index_perfiluser.php\">";
			exit();
		}
		
	}else{

		//dados invalidos
		enviaMsg("erro","Perfil não cadastrado","Os dados inseridos são inválidos");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_perfiluser.php\">";
		exit();

	}

}///fim if !empty POST
else{

?>

<div id="conteudo_borda">
    <div  id="conteudo">
		<!-- <form name="add_perfiluser" action="add_perfiluser.php" method="post" class="form-horizontal" onSubmit="return validaForm('add_perfiluser','nome');" > -->
        <form id="add_perfiluser" name="add_perfiluser" action="add_perfiluser.php" method="post" class="form-horizontal">

            <div class="form-group">
                <label class="col-sm-10 control-label">Nome do Perfil</label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome do Perfil" maxlength="<?php echo PERFIL_NOME_SIZE-10; ?>">
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
								
							echo "<option value=\"".
							$funcao[$i]["idfuncao"]."\">&nbsp;&nbsp;&nbsp;".
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
                <button type="reset" id="cancelar" class="btn btn-default index_user.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                <button type="submit" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salvar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
              </div>
            </div>

        </form>
	</div>
</div>
<?php


include_once("../menu_rodape.php");

}//fim do else

//else do verificaFuncaoUsuario(FUNCAO_PERFIL_ADD)
}else{

	enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou dados inválidos");
	echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
	exit();

}
?>
