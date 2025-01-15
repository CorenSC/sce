<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");

//armazena o idprocesso na variavel $idprocesso
if(isset($_REQUEST["p"]) && !empty($_REQUEST["p"])){
	$idprocesso=validaInteiro($_REQUEST["p"], PROCESSO_ID_SIZE);	
}else{
	$idprocesso=false;
}


//conecta no banco e instacia uma conexão com o Registry
require_once("../conexao.php");
require_once("../model/Registry.php");
// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);

//carrega DAO
require_once('../dao/ProcessoDAO.php');
require_once('../dao/EtapaDAO.php');
require_once('../dao/PerfilDAO.php');
require_once('../dao/DocumentoDAO.php');
require_once('../dao/DocumentoTipoDAO.php');
require_once('../dao/HistoricoDAO.php');
//carrega Model
require_once('../model/Processo.php');
require_once('../model/Etapa.php');
require_once('../model/Usuario.php');
require_once('../model/Documento.php');
require_once('../model/Historico.php');
require_once('../model/DocumentoTipo.php');


//IF Nº0
if( verificaFuncaoUsuario(FUNCAO_DOCUMENTO_ADD)!==false && $idprocesso!==false ){

	//MENSAGENS
	if(isset($_GET["s"]) && !empty($_GET["s"])){
		echo "<script>";
		if($_GET["s"]==101){
			echo "showMsgErro('Documento não cadastrado', 'Os dados fornecidos foram invalidados.');";	
		}
		if($_GET["s"]==102){
			echo "showMsgErro('Documento não cadastrado', 'Falha ao tentar enviar o arquivo para o servidor.');";	
		}
		if($_GET["s"]==103){
			echo "showMsgErro('Documento não cadastrado', 'Você precisa encerrar o volume e iniciar um novo para enviar este arquivo.');";	
		}
		if($_GET["s"]==104){
			echo "showMsgErro('Documento não cadastrado', 'Você precisa abrir o volume antes de enviar um arquivo.');";	
		}
		if($_GET["s"]==105){
			echo "showMsgErro('Documento cadastrado com erros', 'O prazo não pôde ser inserido.');";	
		}
		if($_GET["s"]==106){
			echo "showMsgErro('Documento cadastrado com erros', 'Os dados fornecidos para o campo prazo foram invalidados.');";
		}
		if($_GET["s"]==107){
			echo "showMsgErro('Documento cadastrado com erros', 'A data de realização não pôde ser inserida.');";
		}
		if($_GET["s"]==108){
			echo "showMsgErro('Documento cadastrado com erros', 'O AR não pôde ser atribuido ao documento desejado.');";
		}
		if($_GET["s"]==109){
			echo "showMsgErro('Documento cadastrado com erros', 'O documento a ser desentranhado é inválido.');";
		}
		if($_GET["s"]==111){
			echo "showMsgErro('Documento cadastrado com erros', 'O documento não pôde ser desentranhado.');";
		}
		if($_GET["s"]==112){
			echo "showMsgErro('Documento não cadastrado', 'O documento \"Alvo\" do AR é inválido.');";	
		}
		echo "</script>";		
	}


	// Instanciar infos
	$Processo = new Processo();
	$Processo->setId($idprocesso);
	// Instanciar o DAO e retornar infos da base
	$ProcessoDAO = new ProcessoDAO();
	$infosprocesso = $ProcessoDAO->getInfosCapa($Processo);
	//se não encontrar informações da capa, é pq o processo foi removido ou o link está incorreto
	if(!$infosprocesso){
		enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou dados inválidos");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
		exit();
	}
	//se encontrar a coluna bloquear com o valor de BLOQUEIO, impede modificações
	if($infosprocesso["bloquear"]==ETAPA_BLOQUEIA_PROCESSO){
		enviaMsg("erro","Erro","Este processo não pode ser modificado");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doc.php?p=$idprocesso\">";
		exit();
	}
	//atribui dados ao objeto Processo
	$Processo->setModo($infosprocesso["modo"]);
	$Processo->setMilitar($infosprocesso["militar"]);
	//CARREGA (SE HOUVER) OS TIPOS DE DOCUMENTO DA ETAPA
    	$DocumentoTipoDAO = new DocumentoTipoDAO();
		$documentostipo = $DocumentoTipoDAO->getAll();	        	
    	//inserção dos tipos de documento definidos para a etapa
    	$Etapa = new Etapa();
    	$Etapa->setId($infosprocesso["idetapa"]);
    	//pega os documentos que são atrelados a essa etapa
    	$EtapaDAO = new EtapaDAO();
    	$documentos=$EtapaDAO->getTiposDocumentos($Etapa);
    	//variaveis de controle
    	$numdocs=sizeof($documentos);
    	//se não houver documentos que possam ser enviados, retira o usuário da página
    	if($numdocs<1){
    		enviaMsg("erro","Esta etapa não permite o envio de documentos");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doc.php?p=".$idprocesso."\">";
			exit();
    	}
    	//pega infos da Etapa atual
		$infosetapa = $EtapaDAO->getOne($infosprocesso["idetapa"]);
		//verifica se o usuário atual pode fazer ações nesta etapa
		$etapaperfis = $EtapaDAO->getEtapaPerfis($Etapa);
		$etapaperfisarray = array();
		for($i=0;$i<sizeof($etapaperfis);$i++){     
			$etapaperfisarray[]=$etapaperfis[$i]["idperfil"];
		}
		//se o usuário pode efetuar ações nesta etapa ( se ele for do perfil responsavel e etapa da Comissão de Ética ele também efetua ações) OU se é admin 
		if( in_array($_SESSION["USUARIO"]["idperfil"],$etapaperfisarray) || isAdmin() || ($_SESSION["USUARIO"]["idperfil"]==PERFIL_IDRESPONSAVEL && in_array(PERFIL_IDCOMISSAOETICA,$etapaperfisarray) ) ){
			$usuarioEfetuaAcoes=true;
		}else{
			$usuarioEfetuaAcoes=false;
			enviaMsg("erro","Você não tem permissão para adicionar documentos ao processo");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doc.php?p=".$idprocesso."\">";
			exit();
		}


	require_once("../menu_topo.php");

	//SUBMENU DE AÇÕES DA CAPA DO PROCESSO / DOCUMENTO
	require_once('submenu_doc.php');
	//FIM SUBMENU DE AÇÕES  

	//se tiver enviado o formulário (IF Nº1)
	if( isset($_POST) && !empty($_POST) ){


		//validações obrigatórios
		$idusuario 				=	sqlTrataInteiro(validaInteiro($_SESSION["USUARIO"]["idusuario"],	USUARIO_ID_SIZE));
		$iddocumentotipo		=	$_POST["iddocumentotipo"];
		$userfile_obrigatorio	=	$_POST["userfile_obrigatorio"];
		$obs 					=	sqlTrataString($_POST["obs"]);

		//validações não obrigatórios
		if(isset($_POST["aprova"])){
			$aprova=sqlTrataInteiro(validaInteiro($_POST["aprova"],	3));
		}else{
			$aprova=ETAPA_NAO_APROVAVEL;
		}
		if(isset($_POST["justificativa"])){
			$justificativa=sqlTrataString($_POST["justificativa"]);
		}else{
			$justificativa=NULL;
		}
		if(!isset($_POST["dtposse1"]) || empty($_POST["dtposse1"])){
			$dtposse1=NULL;
		}else{
			$dtposse1=validaLiteral($_POST["dtposse1"], PROCESSO_DTPOSSE1_SIZE);
			if(strlen($dtposse1)==16){
				$dtposse1.=':00';
			}
			$dtposse1=transformaDataTimestampBanco($dtposse1);
		}
		if(!isset($_POST["dtposse2"]) || empty($_POST["dtposse2"])){
			$dtposse2=NULL;
		}else{
			$dtposse2=validaLiteral($_POST["dtposse2"], PROCESSO_DTPOSSE2_SIZE);
			if(strlen($dtposse2)==16){
				$dtposse2.=':00';
			}
			$dtposse2=transformaDataTimestampBanco($dtposse2);
		}
		if(!isset($_POST["dtposse3"]) || empty($_POST["dtposse3"])){
			$dtposse3=NULL;
		}else{
			$dtposse3=validaLiteral($_POST["dtposse3"], PROCESSO_DTPOSSE3_SIZE);
			if(strlen($dtposse3)==16){
				$dtposse3.=':00';
			}
			$dtposse3=transformaDataTimestampBanco($dtposse3);
		}
		if(!isset($_POST["obsposse"]) || empty($_POST["obsposse"])){
			$obsposse=NULL;
		}else{
			$obsposse=sqlTrataString($_POST["obsposse"]);
		}

		if($idusuario!==false ){

				//se o usuário tiver escolhido datas de posse, salva essas datas no processo
				if($aprova==ETAPA_NAO_APROVADA && ($justificativa==NULL || empty($justificativa)) ){
					enviaMsg("erro","Documento não enviado","A não aprovação exige uma justificativa");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=add_doc.php?p=".$idprocesso."\">";
					exit();
				}

				//inicia como FALSE para que só vire TRUE ao salvar no histórico o documento
				$inseriuLog = false;
				//se tiver documentos
				if($numdocs==sizeof($_FILES['userfile']['name']) && $numdocs>=1){

					//verifica antes se, sendo 2 documentos, o usuário enviou ao menos UM deles, independente de ser obrigatório ou não
					if($numdocs>1){
						$haEnvioDeDocumento=false;
						for($i=0;$i<sizeof($_FILES['userfile']['name']);$i++){
							//se encontrar algum arquivo a ser enviado, permite a continuidade da rotina
							if(!empty($_FILES['userfile']['name'][$i])){
								$haEnvioDeDocumento=true;
							}
						}
						//se não escolheu enviar NENHUM, invalida envio
						if(!$haEnvioDeDocumento){
							enviaMsg("erro","Ops! Você precisa enviar ao menos um dos arquivos, tente novamente");
							echo "<meta http-equiv=\"refresh\" content=\"0; url=add_doc.php?p=".$idprocesso."\">";
							exit();
						}
					}
					
					for($i=0;$i<sizeof($_FILES['userfile']['name']);$i++){

						//primeiro verifica se é obrigatório OU se enviou nome mas não é obrigatório
						if($userfile_obrigatorio[$i]==1 || (!empty($_FILES['userfile']['name'][$i]) && $userfile_obrigatorio[$i]==2)){
							//verifica se todos os documentos enviados estão no formato exigido
							if( 
								!verificaExtensaoArquivo($_FILES['userfile']['name'][$i],'doc')
							&&	!verificaExtensaoArquivo($_FILES['userfile']['name'][$i],'docx')
							&&	!verificaExtensaoArquivo($_FILES['userfile']['name'][$i],'odt')
							&&	!verificaExtensaoArquivo($_FILES['userfile']['name'][$i],'pdf')
							&&	!verificaExtensaoArquivo($_FILES['userfile']['name'][$i],'xls')
							&&	!verificaExtensaoArquivo($_FILES['userfile']['name'][$i],'xlsx')
							){

								enviaMsg("erro","Nenhum documento inserido","Os documentos enviados precisam ser no formato PDF, DOC, DOCX, XLS, XLSX ou ODT");
								echo "<meta http-equiv=\"refresh\" content=\"0; url=add_doc.php?p=$idprocesso\">";
								exit();

							}
						}
					}
					//envia e salva cada documento no sistema
					for($i=0;$i<sizeof($_FILES['userfile']['name']);$i++){

						//primeiro verifica se é obrigatório OU se enviou nome mas não é obrigatório
						if($userfile_obrigatorio[$i]==1 || (!empty($_FILES['userfile']['name'][$i]) && $userfile_obrigatorio[$i]==2)){

							//envia o documento
							$caminho = APP_URL_UPLOAD;
							$pasta = $idprocesso.'/';
							//se não existir, cria a pasta
							if(!file_exists($caminho.$pasta)){
								mkdir($caminho.$pasta,0774);
							}
							$link = codifica($i.time().$idprocesso).'.'.retornaExtensaoArquivo($_FILES['userfile']['name'][$i]);
							$destino = $caminho.$pasta.$link;
							//envia arquivo para o servidor
							if(move_uploaded_file($_FILES['userfile']['tmp_name'][$i],$destino)){
								//define o arquivo como 644
								chmod($destino,0664);
								//infos comuns a todos tipos de documento
								$Documento = new Documento();
								$Documento->setProcesso($idprocesso);
								$Documento->setUsuario($idusuario);
								$Documento->setDocumentoTipo($iddocumentotipo[$i]);
								$Documento->setLink($link);
								$Documento->setObs($obs);
								$DocumentoDAO = new DocumentoDAO();	
								$iddocumento = $DocumentoDAO->insert($Documento);
								if(!$iddocumento || $iddocumento===false || $iddocumento==false){
									enviaMsg("erro","Documento não inserido","O documento não pôde ser registrado no banco de dados");
									echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doc.php?p=$idprocesso\">";
									exit();
								}

							}else{

								//não enviou o arquivo
								enviaMsg("erro","Documento não inserido","O arquivo não pôde ser enviado para o servidor");
								echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doc.php?p=$idprocesso\">";
								exit();

							}

							//GRAVAR DADOS NO LOG
							//define observações:
							$obs_log = $Documento->toLog();
							//se o usuário enviar uma aprovação / não aprovação para o documento
							if(isset($_POST["aprova"]) && !empty($_POST["aprova"]) && isset($aprova) && !empty($aprova)){
								if($aprova==ETAPA_NAO_APROVADA){
									$obs_log.="Etapa anterior não aprovada".APP_LINE_BREAK;
									if(isset($justificativa) && !empty($justificativa)){
										$obs_log.="Justificativa: ".$justificativa.APP_LINE_BREAK;
									}else{
										$obs_log.="Nenhuma justificativa informada";
									}
								}
								if($aprova==ETAPA_APROVADA){
									$obs_log.="Etapa anterior aprovada".APP_LINE_BREAK;
									if(isset($justificativa) && !empty($justificativa)){
										$obs_log.="Justificativa: ".$justificativa.APP_LINE_BREAK;
									}
								}							
							}

							//se inseriu o documento com sucesso => SALVAR NO HISTÓRICO
							$Historico = new Historico();
							$Historico->setAcao(LOG_ADD_DOC);
							$Historico->setProcesso($idprocesso);
							$Historico->setDocumento($iddocumento);
							$Historico->setObs(sqlTrataString($obs_log));
							$HistoricoDAO = new HistoricoDAO();
							$inseriuLog=$HistoricoDAO->insert($Historico);
							if(!$inseriuLog){
								enviaMsg("erro","Documento inserido com erros","O histórico não pôde ser salvo");
								echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doc.php?p=$idprocesso\">";
								exit();
							}
						}
					}
					//fim inserção de documentos no BD e no FTP
								
					//enviar e-mails
						$emailsetapa = $EtapaDAO->getEmails($Etapa);

						//se houver e-mails a enviar
						if(sizeof($emailsetapa)>0){
							//var de controle
							$emails = array();
							//para cada email 
							for($i=0;$i<sizeof($emailsetapa);$i++){
								if($emailsetapa[$i]["numero"] == 1){
									$emails[$i]["mensagem"]=$infosetapa["msgemail1"];
								}else{
									$emails[$i]["mensagem"]=$infosetapa["msgemail2"];
								}
								if(isset($emailsetapa[$i]["idperfil"]) && !empty($emailsetapa[$i]["idperfil"])){
									$emails[$i]["idperfil"]=$emailsetapa[$i]["idperfil"];
								}else{
									$emails[$i]["idperfil"]=NULL;
								}
								if(isset($emailsetapa[$i]["idusuario"]) && !empty($emailsetapa[$i]["idusuario"])){
									$emails[$i]["idusuario"]=$emailsetapa[$i]["idusuario"];
								}else{
									$emails[$i]["idusuario"]=NULL;
								}
								$emails[$i]["tipoemail"] = $emailsetapa[$i]["tipoemail"];
							}
							//define variaveis para o ajax_mail
							$tipoajaxmail="add_doc";
							$nomeprocesso='Processo de '.$infosprocesso["nometipo"].' nº '.$infosprocesso["numero"];
							$linkprocesso=APP_URL.'/control/index_doc.php?p='.$idprocesso;
							$processousuarioid=$infosprocesso["idusuario"];
							$PerfilDAO = new PerfilDAO();
							$dadosperfil=$PerfilDAO->getOne($_SESSION["USUARIO"]["idperfil"]);
							$nomeperfilusuario=$dadosperfil["nome"];
							//envia e-mails			
							require_once("ajax_mail.php");
						}

					//carrega dados da última etapa do processo encontrado em ETAPA_PROCESSO
		            $dadosEtapaAnterior=$EtapaDAO->getLastEtapaProcesso($Processo);
		            //verifica se a última etapa de ETAPA_PROCESSO é a etapa atual do PROCESSO (consistência dos dados)
		            if($dadosEtapaAnterior["idetapa"] != $infosprocesso["idetapa"]){
		              //se não for, pega dados de todas etapas e compara com a etapa que está salva no processo
		              $arrayEtapas = $EtapaDAO->getAll();
		              foreach($arrayEtapas as $e){
		                //quando encontrar o ID da etapa do processo, insere esta etapa como a última do etapa_processo
		                if($e["idetapa"] == $infosprocesso["idetapa"]){
		                  $EtapaCorreta = new Etapa();
		                  $EtapaCorreta->setId($e["idetapa"]);
		                  $EtapaCorreta->setProcesso($idprocesso);
		                  $EtapaCorreta->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
		                  $EtapaCorreta->setAprova($e["aprova"]);
		                  $EtapaCorreta->setAprovaMsg(NULL);
		                  //insere o ETAPA_PROCESSO
		                  $inseriuEtapaProcesso = $EtapaDAO->insertEtapaProcesso($EtapaCorreta);
		                  //sai do foreach
		                  break; 
		                }
		              }
		            }

					//SE NECESSÁRIO atualizar historico da etapa atual (que passa agora a ser a anterior - a etapa do momento do envio do documento) em etapa_processo
					$dadosEtapaAnterior=$EtapaDAO->getLastEtapaProcesso($Processo);
						//se for uma etapa que está aguardando aprovação
						if(isset($dadosEtapaAnterior["aprovacao"]) && $dadosEtapaAnterior["aprovacao"]==ETAPA_AGUARDANDO_APROVACAO){
							$EtapaAnterior = new Etapa();
							$EtapaAnterior->setId($dadosEtapaAnterior["idetapa_processo"]);
							$EtapaAnterior->setAprova($aprova);
							$EtapaAnterior->setAprovaMsg($justificativa);
							$EtapaAnterior->setUsuario2($_SESSION["USUARIO"]["idusuario"]);
							//atualiza etapa no BD
							$atualizouAnterior = $EtapaDAO->updateEtapaProcesso($EtapaAnterior);
							//depois de atualizar no banco, atualiza o array com dados da etapa anterior
							$dadosEtapaAnterior=$EtapaDAO->getLastEtapaProcesso($Processo);
						}

					//iniciar historico da nova etapa em etapa_processo
						$arrayEtapas = $EtapaDAO->getAll();
						//Objeto ETAPA para instanciar infos da proxima etapa
						$NovaEtapa = new Etapa();
          				$NovaEtapa = proximaEtapaProcesso2($arrayEtapas,$dadosEtapaAnterior,$Processo);
						//$NovaEtapa = proximaEtapaProcesso($arrayEtapas,$dadosEtapaAnterior,$idprocesso);
						
					//se o ID da NovaEtapa >= 0 é possível trocar de etapa, então insere a nova etapa
						if($NovaEtapa->getId() >= 0){
							//insere nova etapa no histórico do processo
							$inseriuEtapaProcesso = $EtapaDAO->insertEtapaProcesso($NovaEtapa);
							//atualiza processo
							$atualizouEtapa = $EtapaDAO->updateEtapa($NovaEtapa);
							//atualiza prazo da etapa do processo
							//calcula o prazo de acordo com o valor em dias que vier do getPrazo
							if($NovaEtapa->getPrazo()>0){
								$dtprazo=date('Ymd', strtotime('+'.$NovaEtapa->getPrazo().' days'));
							}else{
								$dtprazo=0;
							}							
							$Processo->setPrazo($dtprazo);
							$atualizouDtPrazo = $ProcessoDAO->updateDtPrazo($Processo);
						}

					//sucesso, documento(s) enviado!
					enviaMsg("sucesso","Documento(s) inserido(s) com sucesso");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doc.php?p=$idprocesso\">";
					exit();
					

				}else{

					//dados inválidos
					enviaMsg("erro","Documento não inserido","O número de documentos a serem enviados precisa ser o requisitado nesta etapa");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doc.php?p=$idprocesso\">";
					exit();

				}
			
		}else{

			//dados inválidos
			enviaMsg("erro","Documento não inserido","Dados inválidos");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doc.php?p=$idprocesso\">";
			exit();

		}


	//fim do IF Nº1
	//se não tiver sido enviado nenhum POST para página, exiba as informações abaixo:
	}else{




	?>

	<div id="conteudo_borda">
		<div id="conteudo">
			<form enctype="multipart/form-data" id="add_doc" name="add_doc" action="add_doc.php" method="post" class="form-horizontal">
		    	<input type="hidden" name="p" id="p" value="<?php echo $idprocesso; ?>" />
		        <div class="form-group">
		        	<label for="title" class="col-lg-8 control-label"><?php echo 'Processo de '.$infosprocesso["nometipo"].' nº '.$infosprocesso["numero"]; ?></label>
		        	<label for="title" class="col-lg-8 control-label"><?php echo 'Etapa nº '.$infosprocesso["ordemetapa"].' - '.$infosprocesso["nomeetapa"]; ?></label>
		        </div>

		        <?php
		        if(isset($infosprocesso["msgadd"]) && !empty($infosprocesso["msgadd"])){
		        	echo "	<div class=\"well bg-msg\">
		        				<p>".$infosprocesso["msgadd"]."</p>
		        			</div>";
		        }
		        
		        	//se for uma etapa que exija aprovação e envio de documentos
		        	if($infosetapa["aprova"]==ETAPA_AGUARDANDO_APROVACAO && $numdocs>0){

		        		echo "	<div class=\"well bg-msg\">

			        				<div class=\"form-group\">
										<label class=\"col-sm-12 control-label\">Você aprova o documento que estava pendente de aprovação?</label>
										<div class=\"col-sm-2\">
										<select id=\"aprova\" name=\"aprova\" class=\"form-control\">
							                <option value=\"".ETAPA_APROVADA."\">Sim, o documento foi aprovado</option>
							                <option value=\"".ETAPA_NAO_APROVADA."\">Não, o documento não aprovado</option>
										</select>
										<span id=\"helpBlock\" class=\"help-block\">".APP_MSG_REQUIRED."</span>
							            </div>
							        </div> 
							        <div class=\"form-group\">
						            	<label class=\"col-sm-12 control-label\">Justificativa</label>
							        	<div class=\"col-sm-5\">
							        		<textarea class=\"form-control\" name=\"justificativa\" id=\"justificativa\" rows=\"3\"></textarea>
											<span id=\"helpBlock\" class=\"help-block\">".APP_MSG_REQUIRED." caso o documento não seja aprovado</span>
							        	</div>
							        </div>

						        </div>
									";

		        	}

		        	//se for uma etapa que exija definição de data de posse
		        	if($infosetapa["escolhedata"]==ETAPA_ESCOLHE_DATA && $numdocs>0){

		        		echo "	<div class=\"well\">
		        					<p>Informe as datas e horários em que a Comissão de Ética estará disponível para efetuar esta posse:</p>
			        				<div class=\"form-group\">
					                  <label for=\"dtposse1\" class=\"col-sm-12 control-label\">Informe a possível data e hora de posse n.01</label>
						                <div class=\"col-sm-3\">
						                  <div class=\"input-group datetimepicker\">
						                      <span class=\"input-group-addon\"><i class=\"glyphicon glyphicon-calendar\" style=\"cursor:default !important;\"></i></span>
						                      <input type=\"text\" class=\"form-control\" id=\"dtposse1\" name=\"dtposse1\" placeholder=\"Ex.: 20/08/2025 07:00\" autocomplete=\"off\">
						                  </div>
						                </div>
						                <label for=\"dtposse2\" class=\"col-sm-12 control-label\">Se houver, informe a possível data e hora de posse n.02</label>
						                <div class=\"col-sm-3\">
						                  <div class=\"input-group datetimepicker\">
						                      <span class=\"input-group-addon\"><i class=\"glyphicon glyphicon-calendar\" style=\"cursor:default !important;\"></i></span>
						                      <input type=\"text\" class=\"form-control\" id=\"dtposse2\" name=\"dtposse2\" placeholder=\"Ex.: 20/08/2025 07:00\"autocomplete=\"off\">
						                  </div>
						                </div>
						                <label for=\"dtposse3\" class=\"col-sm-12 control-label\">Se houver, informe a possível data e hora de posse n.03</label>
						                <div class=\"col-sm-3\">
						                  <div class=\"input-group datetimepicker\">
						                      <span class=\"input-group-addon\"><i class=\"glyphicon glyphicon-calendar\" style=\"cursor:default !important;\"></i></span>
						                      <input type=\"text\" class=\"form-control\" id=\"dtposse3\" name=\"dtposse3\" placeholder=\"Ex.: 20/08/2025 07:00\"autocomplete=\"off\">
						                  </div>
						                </div>
						                <label for=\"obsposse\" class=\"col-sm-10 control-label\"><br>Se necessário (ou não puder definir nenhuma data e hora para a posse), utilize o campo abaixo para escrever uma observação</label>
						                <div class=\"col-sm-5\">
						                  <textarea class=\"form-control\" name=\"obsposse\" id=\"obsposse\" rows=\"3\"></textarea>
						                </div>
					            	</div>
								</div>	";
		        	}	

		        	//se houver tipos de documento definidos para esta etapa:
		        	if($numdocs>0){

		        		$cont=0;
		        		foreach ($documentos as $doc) {
			        		$cont++;

			        		echo "<div class=\"well\">";

				        		//TIPO DO DOCUMENTO
				        		echo "	<div class=\"form-group\">
								            <label class=\"col-sm-10 control-label\">Tipo do Documento";
								            if($numdocs>1){
								            	echo " nº ".$cont;
								            }
								echo "		</label>
								            <div class=\"col-lg-10\">
								              <select id=\"iddocumentotipo[]\"  name=\"iddocumentotipo[]\" class=\"form-control\">";					                
												// Recuperar infos do checklist
												$DocumentoTipoDAO = new DocumentoTipoDAO();
												$dados = $DocumentoTipoDAO->getAll();//getAllToProcess($p)
								                if(sizeof($dados)>0){
													for($i=0;$i<sizeof($dados);$i++){	
														if($doc["iddocumentotipo"]==$dados[$i]["iddocumentotipo"]){
															echo "<option value=\"".$dados[$i]["iddocumentotipo"]."\">".$dados[$i]["nome"]."</option>";	
														}											
								                    }
								                }
								        echo "</select>
								        	</div>
								        </div>";
								//CRIA INPUT HIDDEN PRA DIZER SE É OBRIGATORIO OU NAO
								echo "<input type=\"hidden\" name=\"userfile_obrigatorio[]\" id=\"userfile_obrigatorio[]\" value=\"".$doc["obrigatorio"]."\">";

						        //ENVIO DO ARQUIVO
						        echo "	<div class=\"form-group\">
								        	<label class=\"col-sm-10 control-label\">Arquivo";
											if($numdocs>1){
								            	echo " nº ".$cont;
								            }
								echo "		</label>
								            <div class=\"col-lg-10\">
								                <input rel=\"".$doc["obrigatorio"]."\" name=\"userfile[]\" id=\"userfile[]\" type=\"file\" value=\"\" />
								                <span class=\"help-block\">";
								//se for envio obrigatório, avisa o usuário
								if($doc["obrigatorio"]==1){
									echo APP_MSG_REQUIRED.". ";
								}
								
								echo "O arquivo precisa ser do tipo <strong>DOC</strong>, <strong>DOCX</strong>, <strong>PDF</strong>, <strong>XLS</strong>, <strong>XLSX</strong> ou <strong>ODT</strong></span>
								            </div>
								        </div>";

						    echo "</div>";

			        	}

		        	}		    

		        ?>

		        <div class="form-group">
	            	<label for="obs" class="col-sm-10 control-label">Observações</label>
		        	<div class="col-sm-5">
		        		<textarea class="form-control" name="obs" id="obs" rows="3"></textarea>
						<span id="helpBlock" class="help-block">Se desejar informar algo relativo a este documento, digite aqui</span>
		        	</div>
		        </div>

		        <div class="form-group">
		          <div class="col-lg-10" id="processo_<?php echo $idprocesso; ?>">
		            <button type="reset" id="cancelar" class="btn btn-default index_doc.php?p=<?php echo $idprocesso; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
		            <button type="submit" class="btn btn-primary enviando_formulario">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Enviar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
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