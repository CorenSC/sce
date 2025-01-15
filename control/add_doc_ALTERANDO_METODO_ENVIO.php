<?php

ini_set( 'display_errors', TRUE );
error_reporting( E_ALL | E_STRICT );
ini_set('memory_limit', '800M');//memória máxima de 800 MB

require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");

//armazena o IDPAD na variavel $idpad
if(isset($_REQUEST["p"])){
	$idpad=validaInteiro($_REQUEST["p"], $GLOBALS["pad_idpad_size"]);	
}else{
	$idpad=false;
}


//conecta no banco e instacia uma conexão com o Registry
require_once("../conexao.php");
require_once("../model/Registry.php");
// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);

//carrega DAO
require_once('../dao/PadDAO.php');
require_once('../dao/DocumentoDAO.php');
require_once('../dao/EntidadeDAO.php');
require_once('../dao/DocumentoTipoDAO.php');
require_once('../dao/HistoricoDAO.php');
//carrega Model
require_once('../model/Pad.php');
require_once('../model/Usuario.php');
require_once('../model/Documento.php');
require_once('../model/Historico.php');
require_once('../model/Entidade.php');
require_once('../model/DocumentoTipo.php');

//verifica se o usuário é fiscal, se for, só permite editar PAD's em que foi designado.
$fiscal=false;
if($_SESSION["USUARIO"]["idperfil"] == $GLOBALS["perfil_idfiscal"]){
  $fiscal=true;
  $PadDAO = new PadDAO();
  $Usuario = new Usuario();
  $Usuario->setId($_SESSION["USUARIO"]["idusuario"]);
  $aux=$PadDAO->getPadsFiscal($Usuario);
  $padsfiscal=array();
  foreach($aux as $a){
    $padsfiscal[]=$a["idpad"];
  }
}

//IF Nº0
if( verificaFuncaoUsuario(FUNCAO_DOCUMENTO_ADD)!==false && $idpad!==false && ( !$fiscal || ($fiscal && isset($padsfiscal) && in_array($idpad,$padsfiscal)) )){

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
	$Pad = new Pad();
	$Pad->setId($idpad);
	// Instanciar o DAO e retornar infos da base
	$PadDAO = new PadDAO();
	$infosprocesso = $PadDAO->getInfosCapa($Pad);
	//salva se o tipo da entidade é secretaria ou não
	if($infosprocesso["numsecretariaunidades"]>0){
		$entidadeSecretaria=1;//sim
		$EntidadeDAO = new EntidadeDAO();
		$unidadesSecretaria=$EntidadeDAO->getUnidadesSecretaria($Pad);
	}else{
		$entidadeSecretaria=2;//não
	}
	//retorna o último volume usado no PAD.
	$volume = $PadDAO->getNumVolume($Pad);
	if($volume==false || $volume["ultimovolume"]==null ){
		$volume=1;
	}else{
		$volume=$volume["ultimovolume"];
	}
	//verifica se número de volume encontrado está fechado
	$DocumentoDAO = new DocumentoDAO();
	$Documento = new Documento();
	$Documento->setPad($idpad);
	$Documento->setVolume($volume);
	$volumeFechado=$DocumentoDAO->volumeFechado($Documento); 
	if($volumeFechado!==false){
		$volumeFechado=true;
		$volume++;
	}




	require_once("../menu_topo.php");

	//SUBMENU DE AÇÕES
	echo "<div id=\"conteudo_borda\" class=\"noprint\"><div class=\"well submenu\">";
	//botão EDITAR PAD
	if(verificaFuncaoUsuario(FUNCAO_PROCESSO_EDIT)){
	  echo "<button class=\"btn btn-success\"  value=\"edit_pro.php?p=".$idpad."&r=index_doc.php\" title=\"Alterar informações do PAD\" aria-label=\"Left Align\" type=\"button\"> 
	          <span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span> <strong>EDITAR</strong>
	        </button>";
	}
	//botão EXCLUIR PAD
	if(verificaFuncaoUsuario(FUNCAO_PROCESSO_DEL)){
	  echo " &nbsp;
	        <button class=\"btn btn-warning del_processo\"  id=\"processo_".$idpad."\" title=\"Remover PAD\" aria-label=\"Left Align\" type=\"button\"> 
	          <span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span> <strong>REMOVER</strong>
	        </button>";
	}
	//botão ADD DOCUMENTO
	if(verificaFuncaoUsuario(FUNCAO_DOCUMENTO_ADD)!==false){
	  echo "&nbsp;&nbsp;
	        <button class=\"btn btn-info disabled\" value=\"add_doc.php?p=".$idpad."\" title=\"Adicionar Documento\" type=\"button\" aria-label=\"Left Align\">   
	          <span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> <strong>ADICIONAR DOCUMENTO</strong>
	        </button>";
	}
	//botão HISTORICO DO PROCESSO
	if(verificaFuncaoUsuario(FUNCAO_HISTORICO_PRO)!==false){
	  echo "&nbsp;&nbsp;
	        <button class=\"btn btn-primary\" value=\"view_historico_pro.php?p=".$idpad."\" title=\"Visualizar Histórico do PAD\" type=\"button\" aria-label=\"Left Align\">    
	          <span class=\"glyphicon glyphicon-book\" aria-hidden=\"true\"></span> <strong>HISTÓRICO</strong>
	        </button>";
	}
	//botão MODELOS DE DOCUMENTO
	echo "&nbsp;&nbsp;
	    <button class=\"btn btn-primary\" value=\"index_modelo.php?p=".$idpad."&r=index_doc.php\" title=\"Visualizar Modelos de Documento\" type=\"button\" aria-label=\"Left Align\">   
	        <span class=\"glyphicon glyphicon-inbox\" aria-hidden=\"true\"></span> <strong>MODELOS DE DOCUMENTO</strong>
	      </button>";
	echo "</div></div>";
	//FIM SUBMENU DE AÇÕES

	//se tiver enviado o formulário (IF Nº1)
	if( isset($_POST) && !empty($_POST) ){


		//validações obrigatórios
		$iddocumentotipo=		validaInteiro($_POST["iddocumentotipo"],			DOCUMENTOTIPO_ID_SIZE);
		$volume 		=		validaInteiro($_POST["volume"],						$GLOBALS["documento_volume_size"]);
		$idusuario 		=		validaInteiro($_SESSION["USUARIO"]["idusuario"],	USUARIO_ID_SIZE);
		//validações não obrigatórios
		if(isset($_POST["idsecretariaunidade"])){
			$idsecretariaunidade=validaInteiro($_POST["idsecretariaunidade"],$GLOBALS["secretariaunidade_idsecretariaunidade_size"]);
		}
		//documento desentranhado
		if(isset($_POST["idoriginal"])){
			$idoriginal=validaInteiro($_POST["idoriginal"],DOCUMENTO_ID_SIZE);
		}
		//se o tipo for AR, data de recebimento é NULL
		if($iddocumentotipo==$GLOBALS["documento_idtipo_ar"]){
			$dtrecebimento=NULL;
		}else{
			$dtrecebimento=validaLiteral($_POST["dtrecebimento"], $GLOBALS["documento_dtrecebimento_size"]);
			if(strlen($dtrecebimento)==16){
				$dtrecebimento.=':00';
			}
			$dtrecebimento=transformaDataTimestampBanco($dtrecebimento);
		}
		//se selecionou é 1, se não selecionou é 2
		//1 -> flag ATIVO
		//2 -> flag INATIVO
			if(isset($_POST["paginabranca"]) && !empty($_POST["paginabranca"]) ){
				$paginabranca = 1;
			}else{
				$paginabranca = 2;
			}
			if(isset($_POST["rubrica"]) && !empty($_POST["rubrica"])){
				$rubrica = 1;
			}else{
				$rubrica = 2;
			}
			
			if(isset($_POST["paginaassinatura"]) && !empty($_POST["paginaassinatura"])){
				$paginaassinatura = 1;
			}else{
				$paginaassinatura = 2;
			}
			if(isset($_POST["conformeoriginal"]) && !empty($_POST["conformeoriginal"])){
				$conformeoriginal = 1;
			}else{
				$conformeoriginal = 2;
			}


		if($dtrecebimento!==false && $volume!==false && $idusuario!==false && $iddocumentotipo!==false && isset($_FILES['userfile']['name']) && verificaExtensaoArquivo($_FILES['userfile']['name'],'pdf')!==false){
			

				//envia o documento			
				$arquivo = $_FILES['userfile'];
				$caminho = APP_URL_UPLOAD;
				$pasta = $idpad.'/';
				//se não existir, cria a pasta
					if(!file_exists($caminho.$pasta))
					//define a pasta como 775
					mkdir($caminho.$pasta,0775);
					//mkdir($caminho.$pasta,0777);
					//para remover: rmdir();

				$link = codifica(time().$idpad).'.pdf';
				$destino = $caminho.$pasta.$link;
				
				
				if(move_uploaded_file($arquivo['tmp_name'],$destino)){

					//define o arquivo como 644
					chmod($destino,0644);
					//altera a permissao da pasta e arquivos, permitindo sua exclusão via ftp
					//chmod($caminho.$pasta,0777);

					
					//infos comuns a todos tipos de documento
					$Documento = new Documento();
					$Documento->setPad($idpad);
					$Documento->setUsuario($idusuario);
					$Documento->setDocumentoTipo($iddocumentotipo);
					$Documento->setLink($link);		
					$Documento->setPaginaBranca($paginabranca);		
					$Documento->setCarimboNum($rubrica);		
					//paginaassinatura
					$Documento->setCarimboAss($paginaassinatura);		
					$Documento->setCarimboOriginal($conformeoriginal);
					//se for secretaria, insere o ID da Secretaria Unidade
					if($entidadeSecretaria==1 && $iddocumentotipo!=$GLOBALS["documento_idtipo_ar"]){
						$Documento->setSecretariaUnidade($idsecretariaunidade);
					}

					//se documento diferente de AR, seta o volume e a data de recebimento normalmente
					if($iddocumentotipo!=$GLOBALS["documento_idtipo_ar"]){
						$Documento->setDtRecebimento($dtrecebimento);					
						$Documento->setVolume($volume);
					//se o documento for um AR, o volume e data de recebimento devem ser o mesmo do alvo
					}else{
						//valida se o documento passado é válido
						$iddocumentoar=validaInteiro($_POST["iddocumentoar"],DOCUMENTO_ID_SIZE);
						if($iddocumentoar!==false){
							$DocumentoDAO = new DocumentoDAO();
							$DocumentoAlvo = new Documento();
							$DocumentoAlvo->setId($iddocumentoar);
							$documentoAlvoDoAr=$DocumentoDAO->getOne($DocumentoAlvo);
							$Documento->setDtRecebimento($documentoAlvoDoAr["dtrecebimento"]);					
							$Documento->setVolume($documentoAlvoDoAr["volume"]);
						}else{
							//remove arquivo
							unlink($destino);
							//documento alvo é inválido
							echo "<meta http-equiv=\"refresh\" content=\"0; url=add_doc.php?p=$idpad&s=112\">";
							exit();
						}
					}

					//conta o numero de folhas do documento
						//inclui as bibliotecas para gerar o PDF
					    require_once('../bin/tcpdf/config/tcpdf_config.php');
					    require_once('../bin/tcpdf/tcpdf.php');
					    require_once('../bin/tcpdf/tcpdi.php');
					    //inclui a classe de geração de PDF's do Coren
					    require_once('../model/PDF.php');
					    // Cria um novo documento PDF.
					    $pdf = new PDFCoren(PDF_PAGE_ORIENTATION, 'mm', PDF_PAGE_FORMAT, true, 'UTF-8', false);
					    $arquivo = APP_URL_UPLOAD.$Documento->getPad().'/'.$Documento->getLink();
					    $pagecount = $pdf->setSourceData(file_get_contents($arquivo)); 
					    $numfolhas=($pagecount * 1);
					    $volumefolhas=($numfolhas * 1);

					    //conta folhas S/ CARIMBO PÁGINA EM BRANCO se o número de folhas for maior que 1
					    if($Documento->getPaginaBranca()==2 && $numfolhas>1){

					    	//verifica se o numero de folhas é impar
						    $calc1 = (int) $numfolhas;
			                $calc2 = $calc1 % 2;
			                //se for ÍMPAR
			                if($calc2!=0){

			                	//divide valor por 2, e arredonda o número de folhas para cima
			                	//3 / 2 =>	1,5	=>	2
			                	//5 / 2 =>	2,5	=>	3
			                	//7 / 2 =>	3,5	=>	4
			                	$volumefolhas = ceil($numfolhas / 2);

			                //se for PAR, o volume folhas é o número de folhas / 2
			                }else{
			                	$volumefolhas = ($numfolhas / 2);
			                }

					    }
					    //folhas C/ CARIMBO são contadas apenas como + 1, então não é preciso fazer + nada
					    /*
		        		C/ CARIMBO PÁGINA EM BRANCO
							1 folha é 1 folha
							2 folhas é 2 folhas
							3 folhas é 3 folhas ...
						S/ CARIMBO PÁGINA EM BRANCO
							1 folha é 1 folha ->  coloca 1 página branca automaticamente
							2 folhas é 1 folha
							3 folhas é 2 folhas -> coloca 1 página branca automaticamente
							4 folhas é 2 folhas
							5 folhas é 3 folhas -> coloca 1 página branca automaticamente
							6 folhas é 3 folhas
							7 folhas é 4 folhas -> coloca 1 página branca automaticamente
						*/
					    	


		        		

					//revisar trecho abaixo posteriormente
					//se for AR, o volume de folhas é sempre duplicado.
					//posteriormente é comparado se o documento relacionado ao AR possui a config de páginas em branco ativa
					if($iddocumentotipo==$GLOBALS["documento_idtipo_ar"]){
						$volumefolhas=($numfolhas * 2);
					}

					$Documento->setNumFolhas($numfolhas);
					$Documento->setVolumeFolhas($volumefolhas);

					//verificacoes do volume do documento					
					//calcula o total do volume
					//verifica se o documento pode ser inserido (dependendo do total de folhas do volume)
					$DocumentoDAO = new DocumentoDAO();	
					$totalFolhasVolume= $DocumentoDAO->totalFolhasVolume($Documento);
					$totalFolhasVolume=$totalFolhasVolume["totalfolhasanteriores"];
					$somaFolhasVolume=$totalFolhasVolume + $volumefolhas;
					echo 'TOTAL ANTES: '.$totalFolhasVolume.'<br><br>';
					//se a soma de folhas anterior for > 2 (mais do que só o termo de abertura) e o total de folhas do documento for mais que 199, exige que o doc seja do tipo "termo de encerramento"
					if($totalFolhasVolume>2 && $somaFolhasVolume>199){
						if($volumeFechado){
							$Documento->setVolume($volume+1);
						}else{
							//se o total de folhas for maior que 199, precisa fechar o volume antes de enviar um novo arquivo
							if($iddocumentotipo!=$GLOBALS["documento_idtipo_fechavolume"]){																
								//remove arquivo enviado pelo usuario
								unlink($destino);
								//documento precisa ser obrigatoriamente do tipo "Encerramento do Volume"';
								echo "<meta http-equiv=\"refresh\" content=\"0; url=add_doc.php?p=$idpad&s=103\">";
								exit();
							}
						}
					}else{
						//se o volume for > 1, e o total de folhas for 0, exige "termo de abertura"
						if($volume>1 && $totalFolhasVolume==0){
							if($iddocumentotipo!=$GLOBALS["documento_idtipo_abrevolume"]){
								//remove arquivo enviado pelo usuario
								unlink($destino);
								//documento precisa ser obrigatoriamente do tipo "Abertura do Volume"';
								echo "<meta http-equiv=\"refresh\" content=\"0; url=add_doc.php?p=$idpad&s=104\">";
								exit();
							}
						}
					}
					

					//insere documento na tabela
					$DocumentoDAO = new DocumentoDAO();	
					$iddocumento = $DocumentoDAO->insert($Documento);

					
					//Se o documento é do tipo Designação (INSERE/ATUALIZA PRAZO)
					if($iddocumentotipo==$GLOBALS["documento_idtipo_designacao"]){
						//precisa limpar prazos anteriores
						//insere o novo prazo previsto
						$prazo=(int)validaInteiro(transformaDataBanco($_POST["prazo"]),$GLOBALS["pad_prazo_size"]);
						if($prazo!==false){
							//inicia DAO dos PADs
							$PadDAO = new PadDAO();
							//se for Secretaria e o id da secretaria diferente de 0 (pois na SMS não se põe prazo para o PAD todo)
							if($entidadeSecretaria==1 && $idsecretariaunidade!=0){
								$PadDAO->deletePrazos($Documento);
								$inseriuPrazo=$PadDAO->insertPrazoDesignacao($Documento,$prazo);
								if($inseriuPrazo==false){
									//problema na inserção do prazo
									echo "<meta http-equiv=\"refresh\" content=\"0; url=add_doc.php?p=$idpad&s=105\">";
									exit();
								}
							//se não for secretaria
							}else{
								//dá update no prazo do PAD
								$atualizouPrazo=$PadDAO->updatePrazoDesignacao($Documento,$prazo);
								if($atualizouPrazo==false){
									//problema na inserção do prazo
									echo "<meta http-equiv=\"refresh\" content=\"0; url=add_doc.php?p=$idpad&s=105\">";
									exit();
								}
							}
						}else{
							//problema na validação do campo de prazo
							echo "<meta http-equiv=\"refresh\" content=\"0; url=add_doc.php?p=$idpad&s=106\">";
							exit();
						}
					}

					//se for Secretaria
					//o documento do tipo Relatório (DATA REALIZADO)
					//e o id da secretaria diferente de 0 (pois na SMS não se põe prazo para o PAD todo)
					if($entidadeSecretaria==1 && $iddocumentotipo==$GLOBALS["documento_idtipo_relatorio"] && $idsecretariaunidade!=0){
						//precisa salvar a data atual
						$PadDAO = new PadDAO();
						$atualizou=$PadDAO->updatePrazoRealizado($Documento);
						if($atualizou==false){
							//problema na inserção da data de realização
							echo "<meta http-equiv=\"refresh\" content=\"0; url=add_doc.php?p=$idpad&s=107\">";
							exit();
						}
					}

				  	//se for AR
					if($iddocumentotipo==$GLOBALS["documento_idtipo_ar"]){
						//verifica infos						
						$iddocumentoar=validaInteiro($_POST["iddocumentoar"],DOCUMENTO_ID_SIZE);
						if($iddocumentoar!==false){
							//define que:
							//ID do documento => "idSelecionadoNaLista", iddocumento que receberá o AR na última folha
							//ID do pad => "idDocumentoAtual", iddocumento do AR que será inserido no final do arquivo						
							$Documento3 = new Documento();
							$Documento3->setId($iddocumentoar);
							$Documento3->setPad($iddocumento);
							$DocumentoDAO = new DocumentoDAO();						
							//retorna documentos do tipo AR do mesmo "documento selecionado na lista"
							$documentosAnteriores = $DocumentoDAO->getAnterioresAR($Documento3);
							//desabilita (flag=2) nos docs diferentes do documento Atual
							foreach($documentosAnteriores as $documento){
								$Documento2 = new Documento();
								$Documento2->setId($iddocumento);
								$Documento2->setPad($documento["idar"]);
								//desabilita documento de AR que seja do mesmo documento
								$DocumentoDAO->desabilitaDocumentoAR($Documento2);
							}

							//atualiza infos do documento AR de acordo com os do documento em que ele será "anexado"
							$documentoSelecionado = $DocumentoDAO->show($Documento3);

							$Documento->setPaginaBranca($documentoSelecionado["paginabranca"]);
							$Documento->setCarimboNum($documentoSelecionado["carimbonum"]);
							$Documento->setCarimboAss($documentoSelecionado["carimboass"]);
							$Documento->setCarimboOriginal($documentoSelecionado["carimbooriginal"]);
							//se NÃO tiver página branca, então divide o número de páginas do documento AR
							//aproveita para setar no documento todas as flags do documento relacionado ao AR
							if($Documento->getPaginaBranca()==2){
								$DocumentoDAO->atualizaVolumeFolhas($Documento3);
							}
							//Setar no documento todas as flags do documento relacionado ao AR
							$DocumentoDAO->atualizaFlagsAR($Documento3);
							//deleta AR da tabela de AR's
							$DocumentoDAO->deleteAR($Documento3);
							//adiciona folha com o AR ao final do documento selecionado na lista
							$insertAR = $DocumentoDAO->insertAR($Documento3);
							if(!$insertAR){
								//problema na inserção do AR
								echo "<meta http-equiv=\"refresh\" content=\"0; url=add_doc.php?p=$idpad&s=108\">";								
								exit();
							}
						}
					}

					//se for DESENTRANHAMENTO
					//adiciona linha à tabela desentranhamento
					if($iddocumentotipo==$GLOBALS["documento_idtipo_desentranhamento"]){
						if($idoriginal!==false){	
							$Documento4 = new Documento();
							$Documento4->setUsuario($idusuario);
							$Documento4->setId($iddocumento);//documento desentranhador
							$Documento4->setPad($idoriginal);//id a ser desentranhado
							//remove desentranhamento anteriores para o mesmo documento (idoriginal)
							$deleteDesentranhamento=$DocumentoDAO->deleteDesentranhamento($Documento4);
							//insere um novo desentranhamento
							$desentranhamento=$DocumentoDAO->insertDesentranhamento($Documento4);
							if(!$desentranhamento){
								//falha na inserção do desentranhamento
								echo "<meta http-equiv=\"refresh\" content=\"0; url=add_doc.php?p=$idpad&s=111\">";
								exit();
							}
						}else{
							//falha na validação do iddocumento selecionado como documento a ser desentranhado
							echo "<meta http-equiv=\"refresh\" content=\"0; url=add_doc.php?p=$idpad&s=109\">";								
							exit();
						}						
					}

					//se tiver carimbo "Conforme Original", inserimos na tabela de desentranhamentos
					if($Documento->getCarimboOriginal()==1){
						$Documento4 = new Documento();
						$Documento4->setUsuario($idusuario);
						$Documento4->setId($iddocumento);//documento desentranhador
						$Documento4->setPad($iddocumento);//id a ser desentranhado
						//remove desentranhamento anteriores para o mesmo documento (idoriginal)
						$deleteDesentranhamento=$DocumentoDAO->deleteDesentranhamento($Documento4);
						//insere um novo desentranhamento
						$desentranhamento=$DocumentoDAO->insertDesentranhamento($Documento4);
						if(!$desentranhamento){
							//falha na inserção do desentranhamento
							echo "<meta http-equiv=\"refresh\" content=\"0; url=add_doc.php?p=$idpad&s=111\">";
							exit();
						}
					}











					//após salvar o documento no método normal (sem carimbos e tal) salva-se o documento já modificado



					/*

					2 opções:

					A)	MÉTODO ATUAL - Enviar 1 documento sem nada e ao visualizar o documento o sistema coloca os carimbos gerando 1 documento temporario
						PROBLEMAS:
							1 - Servidor da aplicação ficará mais carregado pois precisa inserir os carimbos a cada visualização de documento;

					B)	Enviar 3 (ou 2) documentos, 
						1 sem nada (sem nenhum carimbo/páginas em branco), 
						1 com as definições do usuário ao enviar o arquivo (c/ carimbos e páginas em branco),
						1 sem as páginas em branco (se o usuário tiver selecionado colocar páginas em branco no documento anterior).
						PROBLEMAS: 
							1 - Ao inserir novos documentos, é possível que alguns ou todos documentos daquele PAD precisem ser refeitos, podendo demorar muito mais para adicionar documentos;
							2 - Espaço em disco: será gasto o triplo do tamanho pois ao invés de 1, teremos 3 pdfs por documento.


					*/





					// Instanciar o DAO para retornar infos da base
					$documentoDAO = new DocumentoDAO();
					$documento = new Documento();
					$documento->setId($iddocumento);
					$results = $documentoDAO->show($documento);

					if($results["arlink"]!==NULL){
					    $LinkAR=$results["arlink"];
					}else{
					    $LinkAR=false;
					}

					//infos do Documento
					$documento->setPad($results['idpad']);
					$documento->setDtRecebimento($results['dtrecebimento']);
					$documento->setVolume($results['volume']);
					$documento->setLink($results['link']);
					$documento->setDocumentoTipo($results['nome']);
					$documento->setUsuario($results['nomeusuario']);
					//1 é TRUE, 2 é FALSE
					$documento->setPaginaBranca($results['paginabranca']);
					$documento->setCarimboNum($results['carimbonum']);
					$documento->setCarimboAss($results['carimboass']);
					$documento->setCarimboOriginal($results['carimbooriginal']);

					//MUITA ATENÇÃO PARA ESTE TRECHO, PRECISAMOS CRIAR DOIS DOCUMENTOS:
					//1 DO JEITO QUE O USUÁRIO DEFINIU 
					//1 DO JEITO QUE O USUÁRIO DEFINIU EXCETO PÁGINAS EM BRANCO

					//padrão de "retirada para impressão" das páginas em branco:
					if(isset($_GET["pb"]) && !empty($_GET["pb"])){
					    $documento->setPaginaBranca(2);
					}

					//infos do PAD
					$pad = new Pad();
					$pad->setId($results['idpad']);
					$pad->setAno($results['ano']);
					$pad->setNumero($results['numero']);
					//infos do Usuario
					$usuario = new Usuario();
					$usuario->setNome($results['nomeusuario']);
					$usuario->setRubrica(APP_URL_UPLOAD.'carimbos/'.$results['rubrica']);
					$usuario->setAssinatura(APP_URL_UPLOAD.'carimbos/'.$results['assinatura']);
					//$usuario->setPaginaAssinatura(APP_URL_UPLOAD.'carimbos/'.$results['paginaassinatura']);
					$usuario->setFuncao($results['funcao']);
					$usuario->setMatricula($results['matriculausuario']);
					//infos do Usuario do Carimbo "Conforme Original"
					$usuarioCO = new Usuario();
					$usuarioCO->setNome($results['usuarioconformeoriginal']);
					$usuarioCO->setAssinatura(APP_URL_UPLOAD.'carimbos/'.$results['assinaturaconformeoriginal']);
					$usuarioCO->setSetor($results['nomesetorconformeoriginal']);
					$usuarioCO->setMatricula($results['matricula']);
					//outras infos
					$documentoUsuarioDataConferencia=exibeDataSemTimestamp($results['dtconformeoriginal']);

					//número de folhas antes de apresentar este (para saber qual é a numeração inicial)
					//"O número da folha inicial de um documento, é sempre a soma das folhas anteriores +1"
					$numfolha = $documentoDAO->showFolhasAnteriores($documento);
					if($numfolha['totalfolhasanteriores']!=NULL){
					    $documentoTotalFolhasAnteriores = ($numfolha['totalfolhasanteriores']+1);    
					}else{
					    $documentoTotalFolhasAnteriores = 1;
					}


					//inclui as bibliotecas para gerar o PDF
					    require_once('../bin/tcpdf/config/tcpdf_config.php');
					    require_once('../bin/tcpdf/tcpdf.php');
					    require_once('../bin/tcpdf/tcpdi.php');
					    //inclui a classe de geração de PDF's do Coren
					    require_once('../model/PDF.php');

					    // Cria um novo documento PDF.
					    $pdf = new PDFCoren(PDF_PAGE_ORIENTATION, 'mm', PDF_PAGE_FORMAT, true, 'UTF-8', false);

					    //armazena em pagecount as paginas do arquivo atual
					    $pagecount = $pdf->setSourceData(file_get_contents($arquivo));

					    //DEFINE AS INFORMAÇÕES DO PDF
					        //flags => 1 é TRUE e 2 é FALSE
					            //se insere o carimbo conforme o original ou NÃO
					            $pdf->setFlagConformeOriginal($documento->getCarimboOriginal());
					            //se insere o carimbo número da folha com rubrica ou NÃO
					            $pdf->setFlagNumeroFolha($documento->getCarimboNum());
					            //se insere a assinatura do usuário ou NÃO
					            $pdf->setFlagPaginaAssinatura($documento->getCarimboAss());
					            //se insere o carimbo página em branco ou NÃO
					            $pdf->setFlagPaginaEmBranco($documento->getPaginaBranca());

					        //se tiver inserido
					        if($pdf->getFlagNumeroFolha()==1){
					            //link para rubrica do usuário
					            $pdf->setRubrica($usuario->getRubrica());
					        }
					        
					        //nome do usuário que gerou o documento
					        $pdf->setUsuario($usuario->getNome());
					        //função do usuário que gerou o documento
					        $pdf->setUsuarioFuncao($usuario->getFuncao());
					        //Matricula do usuário que gerou o documento
					        $pdf->setUsuarioMatricula($usuario->getMatricula());
					        //Assinatura do usuário que gerou o documento
					        $pdf->setUsuarioAssinatura($usuario->getAssinatura());
					        //nome do documento
					        $pdf->setTitulo($documento->getDocumentoTipo());
					        //ano e número do PAD (para gerar o nome do arquivo .pdf)
					        $pdf->setPADAno($pad->getAno());
					        $pdf->setPADNumero($pad->getNumero());

					        //carimbo conforme original
					            if($pdf->getFlagConformeOriginal()==1 || $usuarioCO->getNome()!=null){
					                //link para assinatura do usuário (conforme o original)
					                $pdf->setAssinaturaCO($usuarioCO->getAssinatura());
					                //setor do usuário
					                $pdf->setSetorCO($usuarioCO->getSetor());
					                //matrícula do usuário
					                $pdf->setMatriculaCO($usuarioCO->getMatricula());
					                //data de conferência (conforme o original)
					                $pdf->setDataCO($documentoUsuarioDataConferencia);
					            }

					        if($pdf->getFlagPaginaAssinatura()==1){
					            //link para página com assinatura do usuário
					            //$pdf->setPaginaAssinatura($usuario->getPaginaAssinatura());
					            $pdf->setPaginaAssinatura($usuario->getAssinatura());
					        }

					        //numero da folha - sempre relativo ao total de folhas no PAD
					        //o número da folha inicial de um documento é sempre a soma das anteriores
					        //aí, na primeira iteração, o número da página é $pdf->setNumeroFolha($pdf->getNumeroFolha() + 1);
					        //setNumeroFolha recebe ZERO pois este controle é interno, para saber se a página é impar, posição no DOCUMENTO
					        //setTotalFolhasAnteriores recebe o total de folhas dos documentos anteriores para exibir corretamente o número da folha nos carimbos
					        $pdf->setNumeroFolha(0);
					        $pdf->setTotalFolhasAnteriores($documentoTotalFolhasAnteriores);


					    //para cada página faça
					    for ($i = 1; $i <= $pagecount; $i++) {

					        //importe a página para o template padrão
					        $tplidx = $pdf->importPage($i);
					        //adicione a página ao template
					        $pdf->AddPage();
					        //adiciona nova página
					        $pdf->setNumeroFolha($pdf->getNumeroFolha() + 1);
					        //insere o doc enviado com o tamanho reduzido
					        $pdf->useTemplate($tplidx,10,21,'190','269',true);
					        //OLD:  $pdf->useTemplate($tplidx,5,15,'185','277',true);
					        //TAMANHO MAXIMO (TODA TELA):
					        //$pdf->useTemplate($tplidx,0,0,'210','297',true);

					        
					        //inserção do carimbo NÚMERO DA FOLHA COM RUBRICA
					        //se igual a 1 é TRUE, se igual a 2 é FALSE
					        if($pdf->getFlagNumeroFolha() == 1){
					            $pdf->carimboNumFolha();
					        }
					        
					        //inserção do carimbo CONFORME O ORIGINAL
					        //se igual a 1 é TRUE, se igual a 2 é FALSE
					        if($pdf->getFlagConformeOriginal() == 1 || $usuarioCO->getNome()!=null){
					            $pdf->carimboConfereComOriginal();
					        }        

					        //inserção da PÁGINA COM ASSINATURA DO USUÁRIO
					        //se igual a 1 é TRUE, se igual a 2 é FALSE
					        if($pdf->getFlagPaginaAssinatura() == 1){
					            //Se $i == $pagecount então é a última folha após a última folha insira a página de assinatura
					            if($i == $pagecount){
					                //$pdf->inserePaginaAssinaturaAutor();
					                $pdf->insereAssinaturaAutor();
					            }
					        }

					        //insere uma página em branco com o carimbo "PAGINA EM BRANCO"
					        //se flag estiver ativa (igual a 1 é TRUE, se igual a 2 é FALSE)
					        //OU se o doc tiver 1 só folha e a opção "remover página em branco" estiver inativa = insere página em branco
					        if($pdf->getFlagPaginaEmBranco() == 1 || ( $pagecount==1 && !isset($_GET["pb"]) ) ){
					            $pdf->inserePaginaEmBranco();
					        }

					        //Limpa o buffer de saída para permitir e agilizar a geração do PDF
					        ob_clean();
					    }


					    //TRECHO AR
					    if($LinkAR!==false){

					        $arquivo = APP_URL_UPLOAD.$documento->getPad().'/'.$LinkAR;
					        //armazena em pagecount as paginas do arquivo atual
					        $pagecount = $pdf->setSourceData(file_get_contents($arquivo));
					        
					        //para cada página faça
					        for ($i = 1; $i <= $pagecount; $i++) {

					            //importe a página para o template padrão
					            $tplidx = $pdf->importPage($i);
					            //adicione a página ao template
					            $pdf->AddPage();
					            //adiciona nova página
					            $pdf->setNumeroFolha($pdf->getNumeroFolha() + 1);
					            //insere o doc enviado com o tamanho reduzido
					            $pdf->useTemplate($tplidx,10,21,'190','269',true);
					            //OLD:  $pdf->useTemplate($tplidx,5,15,'185','277',true);
					            //TAMANHO MAXIMO (TODA TELA):
					            //$pdf->useTemplate($tplidx,0,0,'210','297',true);

					            
					            //inserção do carimbo NÚMERO DA FOLHA COM RUBRICA
					            //se igual a 1 é TRUE, se igual a 2 é FALSE
					            if($pdf->getFlagNumeroFolha() == 1){
					                $pdf->carimboNumFolha();
					            }
					            
					            //inserção do carimbo CONFORME O ORIGINAL
					            //se igual a 1 é TRUE, se igual a 2 é FALSE
					            if($pdf->getFlagConformeOriginal() == 1 || $usuarioCO->getNome()!=null){
					                $pdf->carimboConfereComOriginal();
					            }        

					            //insere uma página em branco com o carimbo "PAGINA EM BRANCO"
					            //se igual a 1 é TRUE, se igual a 2 é FALSE
					            if($pdf->getFlagPaginaEmBranco() == 1){
					                $pdf->inserePaginaEmBranco();
					            }
					            
					            /*
					            AR NÃO TEM INSERÇÃO DE ASSINATURA
					            //inserção da PÁGINA COM ASSINATURA DO USUÁRIO
					            //se igual a 1 é TRUE, se igual a 2 é FALSE
					            if($pdf->getFlagPaginaAssinatura() == 1){
					                //Se $i == $pagecount então é a última folha após a última folha insira a página de assinatura
					                if($i == $pagecount){
					                    //$pdf->inserePaginaAssinaturaAutor();
					                    $pdf->insereAssinaturaAutor();
					                }
					            }
					            */

					            //Limpa o buffer de saída para permitir e agilizar a geração do PDF
					            ob_clean();
					        }
					    }
					    

					    //Adiciona ao arquivo PDF informações de autoria do documento
					    $pdf->addInformacoesAutoria();
					    //Insere opções ou configurações para que o arquivo PDF fique da melhor maneira possível
					    $pdf->formataArquivo();
					    //Limpa o buffer de saída para permitir e agilizar a geração do PDF
					    ob_clean();
					    //Manda exibir na tela o PDF (e define um texto dentro do Output para que o arquivo tenha o nome correto do documento)
					    //$pdf->Output('PAD'.$pad->getAno().$pad->getNumero().'__'.$documento->getDocumentoTipo().'.pdf');
					    $nomePDF='PAD'.$pad->getAno().$pad->getNumero().'__'.$documento->getDocumentoTipo();
					    $nomeTemp=APP_URL_UPLOAD.'temp/'.md5(rand(1,9999).$nomePDF).'.pdf';
					    $pdf->Output($nomeTemp, "F");
					    unset($pdf);




					    echo 'NOME: '.$nomeTemp;
					    exit();






					    //fim da criação do arquivo modificado














					//Recupera e altera dados do PAD para salvar os valores e não os IDS no histórico
					$Documento->setUsuario($_SESSION["USUARIO"]["nome"]);
					$DocumentoTipo = new DocumentoTipo();
					$DocumentoTipo->setId($iddocumentotipo);
					$DocumentoTipoDAO = new DocumentoTipoDAO();
					$dado=$DocumentoTipoDAO->getOne($DocumentoTipo);
					$Documento->setDocumentoTipo($dado["nome"]);
					$Entidade = new Entidade();
					$Entidade->setId($idsecretariaunidade);
					$EntidadeDAO = new EntidadeDAO();
					$dado=$EntidadeDAO->getOneUnidade($Entidade);
					$Documento->setSecretariaUnidade($dado["nome"]);
					$Documento->setDtRecebimento(exibeDataTimestamp($Documento->getDtRecebimento()));

					//GRAVAR DADOS NO LOG
					//define observações:
					$obs_log = $Documento->toLog();
					//se inseriu o documento com sucesso => SALVAR NO HISTÓRICO
					$Historico = new Historico();
					$Historico->setAcao(LOG_ADD_DOC);
					$Historico->setProcesso($idpad);
					$Historico->setDocumento($iddocumento);
					$Historico->setObs(sqlTrataString($obs_log));
					$HistoricoDAO = new HistoricoDAO();
					$inseriuLog=$HistoricoDAO->insert($Historico);




					//Se chegar aqui deu tudo certo...
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doc.php?p=$idpad&s=100\">";								
					exit();






					
					
				}else{


					//não enviou o arquivo
					echo "<meta http-equiv=\"refresh\" content=\"0; url=add_doc.php?p=$idpad&s=102\">";
					exit();

				}
			
			
			
		}else{
				//erro de validação dos campos
				echo "<meta http-equiv=\"refresh\" content=\"0; url=add_doc.php?p=$idpad&s=101\">";
				exit();
		}


	//fim do IF Nº1
	//se não tiver sido enviado nenhum POST para página, exiba as informações abaixo:
	}else{

	?>

	<div id="conteudo_borda">
		<div id="conteudo">

			<form enctype="multipart/form-data" id="add_doc" name="add_doc" action="add_doc.php" method="post" class="form-horizontal" onSubmit="return validaForm('add_doc','iddocumentotipo','iddocumentoar','prazo','idoriginal','idsecretariaunidade','dtrecebimento','userfile');" >
		    	<input type="hidden" name="p" id="p" value="<?php echo $idpad; ?>" />
		        <div class="form-group">
		        <label for="title" class="col-lg-8 control-label"><?php echo 'PAD '.$infosprocesso["numero"].' / '.$infosprocesso["ano"]; ?></label>
		        </div>
		        
		        <div class="form-group">
		            <label class="col-sm-10 control-label">Tipo do Documento</label>
		            <div class="col-lg-10">
		              <select id="iddocumentotipo"  name="iddocumentotipo" class="form-control" onchange="verificaTipoDocumento(this.value,<?php echo $GLOBALS["documento_idtipo_ar"].','.$GLOBALS["documento_idtipo_designacao"].','.$GLOBALS["documento_idtipo_desentranhamento"]; ?>);">
		                <option value="-1">Selecione o tipo do documento</option>
		                <?php
						// Recuperar infos do checklist
						$DocumentoTipoDAO = new DocumentoTipoDAO();
						$dados = $DocumentoTipoDAO->getAll();//getAllToProcess($p)
		                if(sizeof($dados)>0){
							for($i=0;$i<sizeof($dados);$i++){	
								echo "<option value=\"".$dados[$i]["iddocumentotipo"]."\">".$dados[$i]["nome"]."</option>";
		                    }
		                }
		                ?>
		              </select>
		              <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
		            </div>
		        </div>

		        <div class="so_ar" style="display:none">

			        <div class="form-group">
			            <label for="iddocumentoar" class="col-sm-10 control-label">Selecione a qual documento este AR pertence</label>
			            <div class="col-lg-10">
			              <select id="iddocumentoar"  name="iddocumentoar" class="form-control">
			                <option value="-1">Selecione o documento referente a este AR</option>
			                <?php
							// Recuperar infos
							$DocumentoDAO = new DocumentoDAO();
							$dados = $DocumentoDAO->getAllFromPad($Pad);//getAllToProcess($p)
			                if(sizeof($dados)>0){
								for($i=0;$i<sizeof($dados);$i++){	
									echo "<option value=\"".$dados[$i]["iddocumento"]."\">V".$dados[$i]["volume"]." (recebido em ".exibeDataTimestamp($dados[$i]["dtrecebimento"]).") - ".$dados[$i]["nomedocumento"]."</option>";
			                    }
			                }
			                ?>
			              </select>
			              <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
			            </div>
			        </div>

		        </div>

		        <div class="nao_ar" style="display:none">

		            <div class="form-group" id="div_prazo" style="display:none;">

		            	<label for="prazo" class="col-sm-12 control-label">Prazo previsto para concluir esta fiscalização</label>
			        	<div class="col-lg-2">
			        	<div class="input-group date">
				            <input type="text" class="form-control" id="prazo" name="prazo" placeholder="Ex.: 20/08/2015" maxlength="<?php echo USUARIO_DTEXPIRACAO_SIZE+2; ?>">
				            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>

			        	</div>
			        	<span id="helpBlock" class="help-block">&nbsp;</span>
			        	</div>
			        </div>

			        <div class="form-group" id="div_desentranhamento" style="display:none;">

		            	<label for="prazo" class="col-sm-12 control-label">Documento que será desentranhado</label>
			        	<div class="col-lg-10">
				        	<select id="idoriginal"  name="idoriginal" class="form-control">
				                <option value="-1">Selecione o documento</option>
				                <?php
								$DocumentoDAO = new DocumentoDAO();
								$dados = $DocumentoDAO->getAllFromPad($Pad);//getAllToProcess($p)
				                if(sizeof($dados)>0){
									for($i=0;$i<sizeof($dados);$i++){	
										echo "<option value=\"".$dados[$i]["iddocumento"]."\">V".$dados[$i]["volume"]." (recebido em ".exibeDataTimestamp($dados[$i]["dtrecebimento"]).") - ".$dados[$i]["nomedocumento"]."</option>";
				                    }
				                }
				                ?>
			            	</select>
			        	<span id="helpBlock" class="help-block">&nbsp;</span>
			        	</div>
			        </div>

			        <?php 
			        //SE FOR SECRETARIA
			        if($entidadeSecretaria==1){ 
			        ?>
			        <div class="form-group">
			            <label for="idsecretariaunidade" class="col-sm-10 control-label">Selecione a qual unidade da Secretaria este documento pertence</label>
			            <div class="col-lg-10">
			              <select id="idsecretariaunidade"  name="idsecretariaunidade" class="form-control">
			                <option value="-1">Selecione a unidade</option>
			                <option value="0">Este documento não é relacionado a uma unidade, mas sim a Secretaria</option>
			                <?php
							// Recuperar infos já selecionadas
							$dados=$unidadesSecretaria;
			                if(sizeof($dados)>0){
								for($i=0;$i<sizeof($dados);$i++){	
									echo "<option value=\"".$dados[$i]["idsecretariaunidade"]."\">".$dados[$i]["nome"]."</option>";
			                    }
			                }
			                ?>
			              </select>
			              <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
			            </div>
			        </div>
			        <?php 
			        //FIM IF SE FOR SECRETARIA
			        //se não for secretaria, não exibe o campo mas atribui um valor para não travar a validação do formulário
			        }else{
			        	echo '<input type="hidden" id="idsecretariaunidade"  name="idsecretariaunidade" value="99">';
			        }
			        ?>

				    <div class="form-group">
		            	<label for="dtrecebimento" class="col-sm-12 control-label">Data e Hora de Envio</label>
			        	<div class="col-sm-3">
				        	<div class="input-group datetimepicker">
					            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar" style="cursor:default !important;"></i></span>
					            <input type="text" class="form-control" id="dtrecebimento" name="dtrecebimento" placeholder="Ex.: 20/08/2015 07:00" maxlength="<?php echo $GLOBALS["documento_dtrecebimento_size"]-3; ?>">					            
				        	</div>				        	
			        	</div>
			        	<div class="col-sm-12">
			        	<span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED.'<br>** Clique no campo para selecionar a data e hora no calendário'; ?></span>
			        	</div>
			        </div>
		        

					<div class="form-group">
		            	<label for="volume" class="col-sm-2 control-label">Volume</label>
					    <div class="col-sm-2">
					    <input type="text" class="form-control" id="volume" name="volume" value="<?php echo $volume; ?>" maxlength="<?php echo $GLOBALS["documento_volume_size"]; ?>" readonly="readonly">				      
					    </div>
				    </div>

			        <div class="form-group">
			        	<div class="col-sm-2">
		            	<label class="col-sm-2 control-label">Selecione as opções desejadas para o documento</label>
					    	
					    	<table class="table table-condensed" id="tabela_irregularidades">
					    		<tr>
					    			<td width="10px;"><input type="checkbox" id="paginabranca" name="paginabranca" checked="checked"></td>
					    			<td><span class="glyphicon glyphicon-file"></span> Inserir "Página em Branco" a cada página do PDF.</td>
					    		</tr>
					    		<tr>
					    			<td width="10px;"><input type="checkbox" id="rubrica" name="rubrica" checked="checked"></td>
					    			<td><span class="glyphicon glyphicon-eye-open"></span> Inserir rubrica com numeração das folhas.</td>
					    		</tr>
					    		<tr>
					    			<td width="10px;"><input type="checkbox" id="paginaassinatura" name="paginaassinatura"></td>
					    			<td><span class="glyphicon glyphicon-registration-mark"></span> Inserir assinatura na última página do documento.</td>
					    		</tr>
					    		<tr>
					    			<td width="10px;"><input type="checkbox" id="conformeoriginal" name="conformeoriginal"></td>
					    			<td><span class="glyphicon glyphicon-check"></span> Inserir carimbo "Conforme o Original".</td>
					    		</tr>
					    	</table>
					    </div>
				    </div>
				</div>


		        <div class="form-group">
		        	<label class="col-sm-10 control-label">Arquivo</label>
		            <div class="col-lg-10">
		                <!-- O Nome do elemento input determina o nome da array $_FILES -->
		                <input name="userfile" id="userfile" type="file" value="" />
		                <span class="help-block">Este campo é obrigatório e o arquivo precisa ser do tipo <strong>PDF</strong></span>
		                <br />
		            </div>
		        </div>

		        <div class="form-group">
		          <div class="col-lg-10" id="processo_<?php echo $idpad; ?>">
		            <button type="reset" id="cancelar" class="btn btn-default index_doc.php?p=<?php echo $idpad; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
		            <button type="submit" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salvar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
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

		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php?s=1\">";
		exit();

}
?>