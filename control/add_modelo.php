<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");

if(verificaFuncaoUsuario(FUNCAO_MODELO_ADD)!==false){

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

	// se os campos obrigatórios passarem na validação
	if( $nome!==false){

		//valida para entrar na área de segurança da SQL
		$nome=sqlTrataString($nome);

		//se entrar nesse IF os dados já podem ser inseridos no banco
		if($nome!==false){

			//se escolheu algum arquivo para enviar e é do tipo correto
			if(isset($_FILES['userfile']['name']) && ( verificaExtensaoArquivo($_FILES['userfile']['name'],'pdf') || verificaExtensaoArquivo($_FILES['userfile']['name'],'doc') ||  verificaExtensaoArquivo($_FILES['userfile']['name'],'docx') || verificaExtensaoArquivo($_FILES['userfile']['name'],'xls') || verificaExtensaoArquivo($_FILES['userfile']['name'],'xlsx')  || verificaExtensaoArquivo($_FILES['userfile']['name'],'odt') ) ) {
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
					enviaMsg("erro","Modelo não cadastrado","O arquivo modelo precisa ser do tipo .DOCX, .PDF, .XLS, .XLSX ou .ODT");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index_modelo.php?showAllRecords=true\">";
					exit();
				}
				$destino = $caminho.$pasta.$link;
				if(move_uploaded_file($arquivo['tmp_name'],$destino)){
					chmod($destino,0664);
				}
				  
				// Instanciar as infos do usuario
				$Modelo = new Modelo();
				$Modelo->setNome($nome);		
				$Modelo->setLink($link);		
				// Instanciar o DAO para inserir o usuario na base
				$ModeloDAO = new ModeloDAO();
				// Chama a função que verifica duplicidade do registro
				$possuiDuplicidade = $ModeloDAO->isDuplicated($Modelo);
				if($possuiDuplicidade!==false){
					enviaMsg("erro","Modelo não cadastrado","Já existe um modelo chamado <strong>".$nome."</strong>, por favor utilize outro nome");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index_modelo.php?showAllRecords=true\">";
					exit();
				}
				
				//insere registro
				$idmodelo = $ModeloDAO->insert($Modelo);
				//atribui ID via método do Usuário
				$Modelo->setId($idmodelo);
				if($idmodelo){		

					//registra ação no log				
					$log_obs = $Modelo->toLog();
					
					require_once("../dao/HistoricoDAO.php");
					require_once("../model/Historico.php");
					$Historico = new Historico();
					$Historico->setAcao(LOG_ADD_MODELO);
					$Historico->setProcesso(0);
					$Historico->setDocumento(0);
					$Historico->setObs($log_obs);
					$HistoricoDAO = new HistoricoDAO();
					$inseriu = $HistoricoDAO->insert($Historico);
					//cadastrado
					if($inseriu){						
						enviaMsg("sucesso","Modelo cadastrado com sucesso");
						echo "<meta http-equiv=\"refresh\" content=\"0; url=index_modelo.php?showAllRecords=true\">";
						exit();
					//historico não cadastrado
					}else{						
						enviaMsg("erro","Modelo cadastrado com erros","O histórico não pôde ser salvo.");
						echo "<meta http-equiv=\"refresh\" content=\"0; url=index_modelo.php?showAllRecords=true\">";
						exit();
					}

				//fim if($idprocesso)
				}else{
					enviaMsg("erro","Modelo não cadastrado","Algum dado foi informado incorretamente, tente novamente.");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index_modelo.php?showAllRecords=true\">";
					exit();
				}

			//não cadastrado - não é no formato correto
			}else{
				enviaMsg("erro","Modelo não cadastrado","O arquivo modelo precisa ter a extensão .DOCX, .ODT, .XLS ou .XLSX ou .PDF");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_modelo.php?showAllRecords=true\">";
				exit();
			}

		}else{

			enviaMsg("erro","Modelo não cadastrado","Os dados fornecidos estavam inválidos, tente novamente.");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=index_modelo.php?showAllRecords=true\">";
			exit();
		}
		
	}else{
		
		//não cadastrado - dados invalidos
		enviaMsg("erro","Modelo não cadastrado","Os dados fornecidos são inválidos, tente novamente.");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_modelo.php?showAllRecords=true\">";
		exit();
		
	}

}//fim do IF do POST
else{

?>

<div id="conteudo_borda">
    <div  id="conteudo">
        <!-- <form enctype="multipart/form-data" name="add_modelo" action="add_modelo.php" method="post" class="form-horizontal" onSubmit="return validaForm('add_modelo','nome','arquivo');" > -->    
        <form id="add_modelo" enctype="multipart/form-data" name="add_modelo" action="add_modelo.php" method="post" class="form-horizontal">

            <div class="form-group">
                <label class="col-sm-10 control-label">Nome do Modelo</label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome do Modelo" maxlength="<?php echo MODELO_NOME_SIZE-20; ?>">
                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
                </div>
            </div>

        	<div class="form-group">
	        	<label class="col-sm-10 control-label">Arquivo</label>
	            <div class="col-lg-10">
	                <!-- O Nome do elemento input determina o nome da array $_FILES -->
	                <input name="userfile" id="userfile" type="file" value="" />
	                <span class="help-block"><?php echo APP_MSG_REQUIRED; ?><br>** O arquivo precisa ser do tipo <strong>DOC</strong>, <strong>DOCX</strong>, <strong>ODT</strong>, <strong>PDF</strong>, <strong>XLS</strong> ou <strong>XLSX</strong></span>
	            </div>
	        </div>
            
            <div class="form-group" style="margin-top:40px;">
              <div class="col-lg-10">
                <button type="reset" id="cancelar" class="btn btn-default index_modelo.php?showAllRecords=true">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                <button type="submit" class="btn btn-primary enviando_formulario">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Enviar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
              </div>
            </div>

        </form>
	</div>
</div>
<?php
include_once("../menu_rodape.php");

}//fim do else do POST

//else do verificaFuncaoUsuario
}else{

		enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou dados inválidos");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
		exit();

}
?>
