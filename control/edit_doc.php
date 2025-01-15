<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");

//armazena o idprocesso na variavel $idprocesso
if(isset($_REQUEST["p"]) && isset($_REQUEST["c"])){
	$idprocesso=validaInteiro($_REQUEST["p"], PROCESSO_ID_SIZE);
	$iddocumento=validaInteiro($_REQUEST["c"], DOCUMENTO_ID_SIZE);
}else{
	enviaMsg("erro","Acesso Negado","Dados do formulário ou link incorretos, tente novamente mais tarde");
	echo "<meta http-equiv=\"refresh\" content=\"0; url=index.php\">";
	exit();
}

//IF Nº0
if( verificaFuncaoUsuario(FUNCAO_DOCUMENTO_EDIT)!==false && $idprocesso!==false && $iddocumento!==false && verificaProcessoUsuario($idprocesso)!==false){

	require_once("../menu_topo.php");

	//SUBMENU DE AÇÕES DA CAPA DO PROCESSO / DOCUMENTO
	require_once('submenu_doc.php');
	//FIM SUBMENU DE AÇÕES  

	//conecta no banco e instacia uma conexão com o Registry
	require_once("../conexao.php");
	require_once("../model/Registry.php");
	// Armazenar essa instância (conexão) no Registry - conecta uma só vez
	$registry = Registry::getInstance();
	$registry->set('Connection', $myBD);
	//carrega DAO
	require_once('../dao/ProcessoDAO.php');
	require_once('../dao/PerfilDAO.php');
	require_once('../dao/DocumentoDAO.php');
	require_once('../dao/DocumentoTipoDAO.php');
	require_once('../dao/HistoricoDAO.php');
	//carrega Model
	require_once('../model/Processo.php');
	require_once('../model/Usuario.php');
	require_once('../model/Documento.php');
	require_once('../model/Historico.php');
	require_once('../model/DocumentoTipo.php');

	//recuperar e instanciar infos do documento
	$DocumentoDAO = new DocumentoDAO();
	$Documento = new Documento();
	$Documento->setId($iddocumento);
	$Documento->setProcesso($idprocesso);
	$infosdocumento=$DocumentoDAO->getOne($Documento);

	// recuperar e instanciar infos do processo
	$Processo = new Processo();
	$Processo->setId($idprocesso);
	$ProcessoDAO = new ProcessoDAO();
	$infosprocesso = $ProcessoDAO->getInfosCapa($Processo);

	//se tiver enviado o formulário (IF Nº1)
	if( isset($_POST) && !empty($_POST) ){

		//validações obrigatórios
		$iddocumentotipo=		sqlTrataInteiro(validaInteiro($_POST["iddocumentotipo"],			DOCUMENTOTIPO_ID_SIZE));
		$idprocesso=			sqlTrataInteiro(validaInteiro($_POST["p"],							PROCESSO_ID_SIZE));
		$iddocumento=			sqlTrataInteiro(validaInteiro($_POST["c"],							DOCUMENTO_ID_SIZE));
		$idusuario =			sqlTrataInteiro(validaInteiro($_SESSION["USUARIO"]["idusuario"],	USUARIO_ID_SIZE));
		$atualizar = 			sqlTrataString(validaLiteral($_POST["atualizar"],						3));
		//validações não obrigatórios
		if(isset($_POST["obs"]) && !empty($_POST["obs"])){
			$obs=sqlTrataString(validaLiteral($_POST["obs"],5000));
		}else{
			$obs=NULL;
		}
		//link  - recebe o anterior, caso seja atualizado o documento este valor é alterado para o novo documento
		$link = $infosdocumento["link"];
		
		if($iddocumentotipo!==false && $idprocesso!==false && $iddocumento!==false && $idusuario!==false && $atualizar!==false && $obs!==false){
			
			//valida e envia o arquivo selecionado (se for o caso)
			if($atualizar=="sim"){

				if( 			verificaExtensaoArquivo($_FILES['userfile']['name'],'doc')
							||	verificaExtensaoArquivo($_FILES['userfile']['name'],'docx')
							||	verificaExtensaoArquivo($_FILES['userfile']['name'],'odt')
							||	verificaExtensaoArquivo($_FILES['userfile']['name'],'pdf')
							||	verificaExtensaoArquivo($_FILES['userfile']['name'],'xls')
							||	verificaExtensaoArquivo($_FILES['userfile']['name'],'xlsx')	){
					//envia o documento
					$caminho = APP_URL_UPLOAD;
					$pasta = $idprocesso.'/';
					//se não existir, cria a pasta
					if(!file_exists($caminho.$pasta)){
						mkdir($caminho.$pasta,0774);
					}
					$link = codifica(mt_rand(1,99).time().$idprocesso).'.'.retornaExtensaoArquivo($_FILES['userfile']['name']);
					$destino = $caminho.$pasta.$link;
					//envia arquivo para o servidor
					if(move_uploaded_file($_FILES['userfile']['tmp_name'],$destino)){
						//define o arquivo como 664
						chmod($destino,0664);
					}else{
						//não enviou o arquivo
						enviaMsg("erro","Documento não atualizado","O arquivo não pôde ser enviado para o servidor");
						echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doc.php?p=$idprocesso\">";
						exit();
					}
				}else{
					//arquivo no formato incorreto
					enviaMsg("erro","Nenhum documento atualizado","Os documentos enviados precisam ser no formato PDF, DOC, DOCX, XLS, XLSX ou ODT");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doc.php?p=$idprocesso\">";
					exit();
				}
			}			

			//instancia documento
			$Documento = new Documento();
			$Documento->setId($iddocumento);
			$Documento->setUsuarioAtualizacao($idusuario);
			$Documento->setProcesso($idprocesso);
			$Documento->setDocumentoTipo($iddocumentotipo);
			$Documento->setLink($link);
			$Documento->setObs($obs);			
			//atualiza documento
			$DocumentoDAO = new DocumentoDAO();	
			$atualizou = $DocumentoDAO->update($Documento);

			//se atualizou o documento, prossegue para os próximos passos
			if($atualizou!==false){

				//GRAVAR DADOS NO LOG
				//define observações:
				$obs_log = $Documento->toLog();
				$obs_log .= "Substituiu o arquivo anterior? ";
				if($atualizar=="sim"){
					$obs_log .= "Sim (Ref.:".base64_encode($infosdocumento["link"]).")";
				}else{
					$obs_log .= "Não";
				}
					
				//se atualizou o documento com sucesso => SALVAR NO HISTÓRICO
				$Historico = new Historico();
				$Historico->setAcao(LOG_EDIT_DOC);
				$Historico->setProcesso($idprocesso);
				$Historico->setDocumento($iddocumento);
				$Historico->setObs(sqlTrataString($obs_log));
				$HistoricoDAO = new HistoricoDAO();
				$inseriuLog=$HistoricoDAO->insert($Historico);
				if(!$inseriuLog){
					enviaMsg("erro","Documento atualizado com erros","O histórico não pôde ser salvo");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doc.php?p=$idprocesso\">";
					exit();
				}

				//Se chegar aqui deu tudo certo...
				enviaMsg("sucesso","Documento atualizado com sucesso");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doc.php?p=$idprocesso\">";
				exit();

			}else{

				//Se cair aqui DEU ERRO NA ATUALIZAÇÃO
				enviaMsg("erro","Documento não atualizado","Tente novamente mais tarde");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doc.php?p=$idprocesso\">";
				exit();

			}
			
		}else{
				//erro de validação dos campos
				enviaMsg("erro","Documento não atualizado","Os dados fornecidos foram invalidados");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doc.php?p=$idprocesso\">";
				exit();
		}


	//fim do IF Nº1
	//se não tiver sido enviado nenhum POST para página, exiba as informações abaixo:
	}else{


	//se não encontrar informações da capa, é pq o processo foi removido ou o link está incorreto
	if(!$infosprocesso){
		enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou dados inválidos");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
		exit();
	}

	?>

	<div id="conteudo_borda">
		<div id="conteudo">
			<form id="edit_doc" enctype="multipart/form-data" name="edit_doc" action="edit_doc.php" method="post" class="form-horizontal">
		    	<input type="hidden" name="p" id="p" value="<?php echo $idprocesso; ?>" />
		    	<input type="hidden" name="c" id="c" value="<?php echo $iddocumento; ?>" />
		    	<div class="form-group">
		        <label for="title" class="col-lg-8 control-label"><?php echo 'Processo de '.$infosprocesso["nometipo"].' nº '.$infosprocesso["numero"]; ?></label>
		        </div>
		        <?php
    			echo "<div class=\"well\">";

	        	//TIPO DO DOCUMENTO
        		echo "	<div class=\"form-group\">
				            <label class=\"col-sm-10 control-label\">Tipo do Documento</label>
				            <div class=\"col-lg-10\">
				              <select id=\"iddocumentotipo\" name=\"iddocumentotipo\" class=\"form-control\">";					                
								// Recuperar infos do checklist
								$DocumentoTipoDAO = new DocumentoTipoDAO();
								$dados = $DocumentoTipoDAO->getAll();//getAllToProcess($p)
				                if(sizeof($dados)>0){
									for($i=0;$i<sizeof($dados);$i++){
										//SE doc = parecerTéc: só troca pro parecerTec / parecerTecHomologado!
										if($infosdocumento["iddocumentotipo"]==DOC_IDPARECER_TEC || $infosdocumento["iddocumentotipo"]==DOC_IDPARECER_TEC_H){
											if($dados[$i]["iddocumentotipo"]==DOC_IDPARECER_TEC || $dados[$i]["iddocumentotipo"]==DOC_IDPARECER_TEC_H){
												if($dados[$i]["iddocumentotipo"]==$infosdocumento["iddocumentotipo"]){
													echo "<option value=\"".$dados[$i]["iddocumentotipo"]."\" selected=\"selected\">".$dados[$i]["nome"]."</option>";	
												}else{
													echo "<option value=\"".$dados[$i]["iddocumentotipo"]."\">".$dados[$i]["nome"]."</option>";
												}
											}
										//Outros tipos de doc => fixo, NÃO é possível alterar para outro tipo
										}else{
											if($dados[$i]["iddocumentotipo"]==$infosdocumento["iddocumentotipo"]){
												echo "<option value=\"".$dados[$i]["iddocumentotipo"]."\" selected=\"selected\">".$dados[$i]["nome"]."</option>";	
											}
										}
				                    }
				                }
				        echo "</select>
				        	</div>
				        </div>";

				echo "	<div class=\"form-group\">
				            <label class=\"col-sm-10 control-label\">Deseja alterar o arquivo enviado?</label>
				            <div class=\"col-lg-10\">
				              <select id=\"atualizar\" name=\"atualizar\" class=\"form-control\" onchange=\"$('.div_arquivo').toggle('slow');\">
				              	<option value=\"nao\" selected=\"selected\">Não</option>
				              	<option value=\"sim\">Sim</option>				              	
				              </select>
				        	</div>
				        </div>";

		        //ENVIO DO ARQUIVO
		        echo "	<div class=\"form-group div_arquivo\" style=\"display:none;\">
				        	<label class=\"col-sm-10 control-label\">Arquivo</label>
				            <div class=\"col-lg-10\">
				                <input name=\"userfile\" id=\"userfile\" type=\"file\" value=\"\" />
				                <span class=\"help-block\">O arquivo precisa ser do tipo <strong>DOC</strong>, <strong>DOCX</strong>, <strong>PDF</strong>, <strong>XLS</strong>, <strong>XLSX</strong> ou <strong>ODT</strong></span>
				            </div>
				        </div>
					</div>";
		        ?>
		        <div class="form-group">
	            	<label for="obs" class="col-sm-10 control-label">Observações</label>
		        	<div class="col-sm-5">
		        		<textarea class="form-control" name="obs" id="obs" rows="3"><?php echo $infosdocumento["obs"]; ?></textarea>
						<span id="helpBlock" class="help-block">Se desejar informar algo relativo a este documento, digite aqui</span>
		        	</div>
		        </div>

		        <div class="form-group">
		          <div class="col-lg-10" id="processo_<?php echo $idprocesso; ?>">
		            <button type="reset" id="cancelar" class="btn btn-default index_doc.php?p=<?php echo $idprocesso; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
		            <button type="submit" class="btn btn-primary enviando_formulario">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salvar Alterações&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
		          </div>
		        </div>
		        
		    </form>
		    
		</div>
	</div>
	<?php

	}//fim do else do "IF FOI ENVIADO ALGO - post" (IF Nº1)

	include_once("../menu_rodape.php");

//ELSE DO IF Nº0
}else{

		//acesso negado
		enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou foram dados inválidos");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
		exit();

}
?>