<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");

if(verificaFuncaoUsuario(FUNCAO_DOCUMENTOTIPO_EDIT)!==false){

//conecta no banco e instacia uma conexão com o Registry
require_once("../conexao.php");
require_once("../model/Registry.php");
//carrega DAO's das chaves estrangeiras
require_once("../dao/DocumentoTipoDAO.php");
//carrega Models
require_once('../model/DocumentoTipo.php');

// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);

require_once("../menu_topo.php");

//SUBMENU DE AÇÕES
require_once('submenu_doctipo.php');
//FIM SUBMENU DE AÇÕES

//se tiver enviado o formulário
if(isset($_POST) && !empty($_POST) ){

	//valida entradas do usuário
	$nome=validaLiteral($_POST["nome"],DOCUMENTOTIPO_NOME_SIZE);
	$iddocumentotipo=validaInteiro($_POST["p"],DOCUMENTOTIPO_ID_SIZE);

	// se os campos obrigatórios passarem na validação
	if( $nome!==false && $iddocumentotipo!==false){

		//valida para entrar na área de segurança da SQL
		$nome=sqlTrataString($nome);
		$iddocumentotipo=sqlTrataInteiro($iddocumentotipo);

		//se entrar nesse IF os dados já podem ser inseridos no banco
		if($nome!==false && $iddocumentotipo!==false){


			// Instanciar as infos
			$DocumentoTipo = new DocumentoTipo();
			$DocumentoTipo->setId($iddocumentotipo);
			$DocumentoTipo->setNome($nome);
			// Instanciar o DAO para inserir o registro na base
			$DocumentoTipoDAO = new DocumentoTipoDAO();
			// Chama a função que verifica duplicidade do registro
			$possuiDuplicidade = $DocumentoTipoDAO->isDuplicatedEdit($DocumentoTipo);
			if($possuiDuplicidade!==false){
				enviaMsg("erro","Tipo de documento não atualizado","O nome <strong>".$nome."</strong> já existe, por favor escolha outro nome");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doctipo.php\">";
				exit();
			}

			//atualiza registro
			$atualizou = $DocumentoTipoDAO->update($DocumentoTipo);
			//atribui ID via método do Usuário
			if($atualizou){						

				//registra ação no log				
				$log_obs = $DocumentoTipo->toLog();
				
				require_once("../dao/HistoricoDAO.php");
				require_once("../model/Historico.php");
				$Historico = new Historico();
				$Historico->setAcao(LOG_EDIT_TIPODOC);
				$Historico->setProcesso(0);
				$Historico->setDocumento(0);
				$Historico->setObs(sqlTrataString($log_obs));
				$HistoricoDAO = new HistoricoDAO();
				$HistoricoDAO->insert($Historico);

			}//fim if atualizou		
			
				
			if($atualizou && $HistoricoDAO){

				//atualizado
				enviaMsg("sucesso","Tipo de documento atualizado com sucesso");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doctipo.php\">";
				exit();

			}else{

				//não atualizado
				enviaMsg("erro","Tipo de documento atualizado com erros","O histórico não pôde ser salvo");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doctipo.php\">";
				exit();
			}

		}else{

			//não atualizado - dados invalidos
			enviaMsg("erro","Tipo de documento não atualizados","Os dados fornecidos estavam inválidos, tente novamente.");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doctipo.php\">";
			exit();
		}
		
	}else{
		
		//não atualizado - dados invalidos
		enviaMsg("erro","Tipo de documento não atualizado","Os dados fornecidos são inválidos, tente novamente.");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doctipo.php\">";
		exit();
		
	}



}//fim do IF do POST
else{

	if(isset($_GET["p"]) && !empty($_GET["p"])){

		//valida informações
		$iddocumentotipo=validaInteiro($_GET["p"],DOCUMENTOTIPO_ID_SIZE);
		if($iddocumentotipo!==false){

			// Instanciar as infos
			$DocumentoTipo = new DocumentoTipo();	
			$DocumentoTipo->setId($iddocumentotipo);

			// Instanciar o DAO para recuperar as informações do banco de dados
			$DocumentoTipoDAO = new DocumentoTipoDAO();

			// Chama a função que retorna os dados do registro requisitado
			$result = $DocumentoTipoDAO->getOne($DocumentoTipo);

			//se encontrou o registro, exibe na tela
			if($result!==false){
		?>
				<div id="conteudo_borda">
				    <div  id="conteudo">
				    	<!-- <form enctype="multipart/form-data" name="edit_doctipo" action="edit_doctipo.php" method="post" class="form-horizontal" onSubmit="return validaForm('edit_doctipo','nome');" > -->
				        <form id="edit_doctipo" name="edit_doctipo" action="edit_doctipo.php" method="post" class="form-horizontal">
				        <input type="hidden" name="p" id="p" value="<?php echo $iddocumentotipo; ?>">

				            <div class="form-group">
				                <label class="col-sm-10 control-label">Nome do Tipo de Documento</label>
				                <div class="col-lg-8">
				                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome do Tipo de Documento" maxlength="<?php echo DOCUMENTOTIPO_NOME_SIZE-20; ?>" value="<?php echo htmlentities($result["nome"]); ?>">
				                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
				                </div>
				            </div>

				            <div class="form-group">
				              <div class="col-lg-10">
				                <button type="reset" id="cancelar" class="btn btn-default index_doctipo.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
				                <button type="submit" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salvar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
				              </div>
				            </div>

				        </form>
					</div>
				</div>
				<?php

				include_once("../menu_rodape.php");

			}//fim if($result!==false){
			else{
				enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou dados inválidos");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
				exit();
			}

		}//fim if($identidadetipo!==false){	
		else{
			enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou dados inválidos");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
			exit();
		}

	}//fim do IF do GET "p"
	else{
		
		enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou dados inválidos");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
		exit();

	}

}//fim do else do POST

//else do verificaFuncaoUsuario(FUNCAO_USUARIO_ADD)
}else{

		enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou dados inválidos");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
		exit();

}
?>
