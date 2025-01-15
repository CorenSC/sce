<?php
//Visualização do Documento Completo do Processo

if($acao == "view" && $pagina=="doc" && $idprocesso !== false ){
	
	//nesse caso o IDPROCESSO é na verdade o ID do documento, portanto:
	$iddocumento = $idprocesso;

	//carrega as bibliotecas para recuperar informações do BD
	require_once("../conexao.php");
	require_once('../model/Registry.php');
	require_once('../dao/DocumentoDAO.php');
	require_once('../model/Documento.php');
	
	// Armazenar essa instância (conexão) no Registry - conecta uma só vez
	$registry = Registry::getInstance();
	$registry->set('Connection', $myBD);
	// Instanciar o documento
	$doc = new Documento();
	$doc->setId($iddocumento);
	// Instanciar o DAO e retornar infos da base

	$documentoDAO = new DocumentoDAO();
	$results = $documentoDAO->getOne($doc);
	//se for um arquivo já removido armazena nosso histórico como documento removido
	$trecho_adicional = "";
	if($results["flag"]==APP_FLAG_INACTIVE){
		$trecho_adicional=" removido (código ".$iddocumento.")";
	}

	if(sizeof($results)>0){

		$idprocesso 	= $results['idprocesso'];
		$nomedoc 		= base64_encode(removeCaracteresEspeciais($results['nomedocumentotipo']));

		//encripta dados para dificultar localização do arquivo pdf "fisicamente"
		//$dadosencriptados=key_encrypt(time().'_'.$iddocumento);
		$dadosencriptados=key_encrypt(time().APP_LINE_BREAK.$results["idprocesso"].APP_LINE_BREAK.$results["link"]);
		
		//registra ação no log
		require_once("../dao/HistoricoDAO.php");
		require_once("../model/Historico.php");
		$log = new Historico();
		$log->setAcao(LOG_VIEW_DOC);
		$log->setProcesso($idprocesso);
		$log->setDocumento($iddocumento);
		$log->setObs('<a target="_blank" href="'.'sistemas.corensc.gov.br'.'/control/window.php?a=view&p=doc&id='.$iddocumento.'">Visualizar arquivo'.$trecho_adicional.'</a>');
		//OLD: $log->setObs('<a target="_blank" href="'.APP_URL.'/control/show.php?f='.urlencode($dadosencriptados).'&n='.$nomedoc.'">Visualizar arquivo</a>');
		
		$logDAO = new HistoricoDAO();
		$logDAO->insert($log);

		require_once("../bin/js-css.php");

		echo "	<div id=\"msg_atencao\" class=\"alert alert-warning\" role=\"alert\"></div>
				<script>
					showMsgAtencao('<center><h3>ATENÇÃO</h3></center>','<br><center>Esta e todas as suas ações no sistema estão sendo gravadas.</center>',2500);
				</script>";
		if(retornaExtensaoArquivo($results["link"])!="pdf"){
			echo "	<script>					
						exibeAposTempo('carregando_oculto',1000);
						exibeAposTempo('carregando_oculto1',6000);
						exibeAposTempo('carregando_oculto2',10000);
						someAposTempo('carregando_oculto',14000);
					</script>
					<center class=\"carregando_oculto\" style=\"display:none;\"><br><img src='../common/images/carregando.gif' height='32px' width='32px'><br><br><strong>Efetuando o download do arquivo...</strong><br><br><br></center>
					<center class=\"carregando_oculto1 bg-primary\" style=\"display:none;\"><br><br><strong>Verifique se o arquivo já foi baixado para sua máquina olhando para o rodapé desta tela ou segure a tecla \"<em>CTRL</em>\" e, sem soltá-la, pressione a tecla \"<em>J</em>\"</strong><br><br><br></center>
					<center class=\"carregando_oculto2 bg-success\" style=\"display:none;\"><br><br><strong>Caso o download já tenha sido concluído, abra o arquivo e depois <a href='#' onClick='window.close();'>clique aqui</a> para fechar esta janela.</strong><br><br><br></center>";	
		}else{
			echo "	<center><br><img src='../common/images/carregando.gif' height='32px' width='32px'><br><br><strong>Carregando visualização do arquivo...</strong></center>";	
		}
		

		//ao visualizar arquivos, o sistema verifica se o backup diario foi feito
		//caso sim, não faz nada, caso não ele salva um arquivo do tipo SQL em
		//RAIZDOSITE/@admin e aproveita para limpar sessions antigas (24 horas atras)
		require_once('@backup_automatico.php');

		echo "<meta http-equiv=\"refresh\" content=\"4; url=show.php?f=".urlencode($dadosencriptados)."&n=".$nomedoc."\">";
		
		exit();


	}else{
		echo "<script>
				alert('Documento não encontrado');
				window.close();
			  </script>";
			  exit();
	}
}

?>