<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");

if(verificaFuncaoUsuario(FUNCAO_MODELO_EDIT)!==false){

//conecta no banco e instacia uma conexão com o Registry
require_once("../conexao.php");
require_once("../model/Registry.php");
//carrega DAO's das chaves estrangeiras
require_once("../dao/ModeloDAO.php");
//carrega Models
require_once('../model/Modelo.php');

// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);

require_once("../menu_topo.php");

//SUBMENU DE AÇÕES
require_once('submenu_modelo.php');
//FIM SUBMENU DE AÇÕES

//se tiver enviado o formulário
if(isset($_POST) && !empty($_POST) ){

	//valida entradas do usuário
	$nome=validaLiteral($_POST["nome"],MODELO_NOME_SIZE);
	$idmodelo=validaInteiro($_POST["p"],MODELO_ID_SIZE);
	$altera=validaLiteral($_POST["altera"],3);

	// se os campos obrigatórios passarem na validação
	if( $nome!==false && $idmodelo!==false && $altera!==false){

		//valida para entrar na área de segurança da SQL
		$nome=sqlTrataString($nome);
		$idmodelo=sqlTrataInteiro($idmodelo);

		//se entrar nesse IF os dados já podem ser inseridos no banco
		if($nome!==false && $idmodelo!==false){


			// Instanciar as infos
			$Modelo = new Modelo();
			$Modelo->setId($idmodelo);		
			$Modelo->setNome($nome);		
			// Instanciar o DAO para inserir o usuario na base
			$ModeloDAO = new ModeloDAO();
			// Chama a função que verifica duplicidade do registro
			$possuiDuplicidade = $ModeloDAO->isDuplicatedEdit($Modelo);
			if($possuiDuplicidade!==false){
				enviaMsg("erro","Modelo não atualizado","Já existe um modelo chamado <strong>".$nome."</strong>, por favor utilize outro nome");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_modelo.php?showAllRecords=true\">";
				exit();
			}

			//se escolheu algum arquivo para enviar, se é para alterar e é um DOC/DOCX/PDF
			if($altera=="sim" && isset($_FILES['userfile']['name']) && ( verificaExtensaoArquivo($_FILES['userfile']['name'],'pdf') || verificaExtensaoArquivo($_FILES['userfile']['name'],'doc') || verificaExtensaoArquivo($_FILES['userfile']['name'],'docx') || verificaExtensaoArquivo($_FILES['userfile']['name'],'xls') || verificaExtensaoArquivo($_FILES['userfile']['name'],'xlsx')  || verificaExtensaoArquivo($_FILES['userfile']['name'],'odt') ) ) {
				$arquivo = $_FILES['userfile'];
				$caminho = APP_URL_UPLOAD;
				$pasta = 'modelos/';
				//trocando o nome do arquivo por um número.extensão
				$link="";
				$partida=strripos($_FILES['userfile']['name'],".")+1;
				$final=strlen($_FILES['userfile']['name'])-1;
				$extensaoencontrada=substr(strtolower($_FILES['userfile']['name']),$partida,$final);
				if($extensaoencontrada=='pdf' || $extensaoencontrada=='doc' || $extensaoencontrada=='docx' || $extensaoencontrada=='odt' || $extensaoencontrada=='xls' || $extensaoencontrada=='xlsx'){
					$link = md5(time()).'.'.$extensaoencontrada;
				}else{
					enviaMsg("erro","Modelo não cadastrado","O arquivo modelo precisa ser do tipo DOC, DOCX, PDF, XLS, XLSX ou ODT");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index_modelo.php?showAllRecords=true\">";
					exit();
				}
				$destino = $caminho.$pasta.$link;
				if(move_uploaded_file($arquivo['tmp_name'],$destino)){
					chmod($destino,0664);
				}

				$Modelo->setLink($link);
			}

			//atualiza registro
			$atualizou = $ModeloDAO->update($Modelo);
			//atribui ID via método do Usuário
			if($atualizou){						

				
				//registra ação no log				
				$log_obs = $Modelo->toLog();
				
				require_once("../dao/HistoricoDAO.php");
				require_once("../model/Historico.php");
				$Historico = new Historico();
				$Historico->setAcao(LOG_EDIT_MODELO);
				$Historico->setProcesso(0);
				$Historico->setDocumento(0);
				$Historico->setObs($log_obs);
				$HistoricoDAO = new HistoricoDAO();
				$inseriu = $HistoricoDAO->insert($Historico);

				//atualizado
				if($inseriu){						
					enviaMsg("sucesso","Modelo atualizado com sucesso");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index_modelo.php?showAllRecords=true\">";
					exit();
				//historico não cadastrado
				}else{						
					enviaMsg("erro","Modelo atualizado com erros","O histórico não pôde ser salvo.");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index_modelo.php?showAllRecords=true\">";
					exit();
				}

			//fim if atualizou	
			}else{

				enviaMsg("erro","Modelo não atualizado","Algum dado foi informado incorretamente, tente novamente.");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_modelo.php?showAllRecords=true\">";
				exit();

			}

		//não atualizou - dados invalidos
		}else{
			enviaMsg("erro","Modelo não atualizado","Algum dado foi informado incorretamente, por favor tente novamente.");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=index_modelo.php?showAllRecords=true\">";
			exit();
		}
		
	}else{
		
		//não atualizou - dados invalidos
		enviaMsg("erro","Modelo não atualizado","Os dados fornecidos estavam inválidos, tente novamente.");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_modelo.php?showAllRecords=true\">";
		exit();
		
	}



}//fim do IF do POST
else{

	if(isset($_GET["p"]) && !empty($_GET["p"])){

		//valida informações
		$idmodelo=validaInteiro($_GET["p"],MODELO_ID_SIZE);
		if($idmodelo!==false){

			// Instanciar as infos
			$Modelo = new Modelo();	
			$Modelo->setId($idmodelo);

			// Instanciar o DAO para recuperar as informações do banco de dados
			$ModeloDAO = new ModeloDAO();

			// Chama a função que retorna os dados do registro requisitado
			$result = $ModeloDAO->getOne($Modelo);

			//se encontrou o registro, exibe na tela
			if($result!==false){
		?>
				<div id="conteudo_borda">
				    <div  id="conteudo">
				    	<!-- <form enctype="multipart/form-data" name="edit_modelo" action="edit_modelo.php" method="post" class="form-horizontal" onSubmit="return validaForm('edit_modelo','nome');" > -->
				        <form id="edit_modelo" enctype="multipart/form-data" name="edit_modelo" action="edit_modelo.php" method="post" class="form-horizontal">
				        <input type="hidden" name="p" id="p" value="<?php echo $idmodelo; ?>">

				            <div class="form-group">
				                <label class="col-sm-10 control-label">Nome do Modelo</label>
				                <div class="col-lg-8">
				                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome do Modelo" maxlength="<?php echo MODELO_NOME_SIZE-20; ?>" value="<?php echo htmlentities($result["nome"]); ?>">
				                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
				                </div>
				            </div>

				            <div class="form-group">
					            <label class="col-sm-11 control-label">Deseja alterar o arquivo deste modelo de documento?</label>
					            <div class="col-lg-2">
					              <select id="altera"  name="altera" class="form-control" onchange="$('#div_escondida').toggle('slow');">
					                <option selected="selected" value="nao">Não</option>
					                <option value="sim">Sim</option>
					              </select>
					            </div>
					        </div>

					        <div class="form-group" id="div_escondida" style="display:none;">
					        	<label class="col-sm-10 control-label">Arquivo</label>
					            <div class="col-lg-10">
					                <!-- O Nome do elemento input determina o nome da array $_FILES -->
					                <input name="userfile" id="userfile" type="file" value="" />
					                <span class="help-block"><?php echo APP_MSG_REQUIRED; ?><br>** O arquivo precisa ser do tipo <strong>DOC</strong>, <strong>DOCX</strong>, <strong>ODT</strong>, <strong>PDF</strong>, <strong>XLS</strong> ou <strong>XLSX</strong></span>
					                <br />
					            </div>
					        </div>

				            
				            <div class="form-group" style="margin-top:40px;">
				              <div class="col-lg-10">
				                <button type="reset" id="cancelar" class="btn btn-default index_modelo.php?showAllRecords=true">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
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
