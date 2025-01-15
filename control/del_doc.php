<?php	
//carrega as bibliotecas para recuperar informações do BD
require_once('../config.php');
require_once('../bin/functions.php');
require_once("../conexao.php");
require_once("../login_verifica.php");



if(isset($_POST["idprocesso"]) && !empty($_POST["idprocesso"]) && isset($_POST["iddocumento"]) && !empty($_POST["iddocumento"])){

	$deletou = false;
	$idprocesso 	=	validaInteiro($_POST["idprocesso"],PROCESSO_ID_SIZE);
	$iddocumento 	= 	validaInteiro($_POST["iddocumento"],DOCUMENTO_ID_SIZE);
	$idprocesso 	= sqlTrataInteiro($idprocesso);
	$iddocumento 	= sqlTrataInteiro($iddocumento);

	//importa DAOS e MODELS
	require_once('../model/Registry.php');
	require_once('../dao/DocumentoDAO.php');
	require_once('../dao/HistoricoDAO.php');
	require_once('../model/Documento.php');
	require_once('../model/Historico.php');

	// Armazenar essa instância (conexão) no Registry - conecta uma só vez
	$registry = Registry::getInstance();
	$registry->set('Connection', $myBD);			
	// Instancia o objeto nota
	$documento = new Documento();
	$documento->setProcesso($idprocesso);
	$documento->setId($iddocumento);
	// Instanciar o DAO e pegar infos do documento
	$DocumentoDAO = new DocumentoDAO();
	$infosDoc = $DocumentoDAO->getOne($documento);
	
	//verifica se o idprocesso é valido e se o usuário pode deletar esse documento OU se foi ele que enviou, podendo deletar tbm
	if($idprocesso!==false && $iddocumento!==false 
		&& (verificaProcessoUsuario($idprocesso)!==false && verificaFuncaoUsuario(FUNCAO_DOCUMENTO_DEL)!==false || $infosDoc["idusuario"]==$_SESSION["USUARIO"]["idusuario"] ) ){
		
			//remove o arquivo da tabela de documentos
			$deletou = $DocumentoDAO->delete($documento);

			if($deletou){

				//encripta dados para dificultar localização do arquivo pdf "fisicamente"
				$dadosencriptados=key_encrypt(time().'_'.$iddocumento);

				//registra ação no log
				$Historico = new Historico();
				$Historico->setAcao(LOG_DEL_DOC);
				$Historico->setProcesso($idprocesso);
				$Historico->setDocumento($iddocumento);
				$Historico->setObs('<a target="_blank" href="'.APP_URL.'/control/window.php?a=view&p=doc&id='.$iddocumento.'">Visualizar arquivo removido</a>');
				$HistoricoDAO = new HistoricoDAO();
				$HistoricoDAO->insert($Historico);
			}
	}	
	if($deletou){
		enviaMsg("sucesso","Sucesso","O documento \"".$infosDoc["nomedocumentotipo"]."\" foi removido do processo!");
	}else{
		enviaMsg("erro","Documento não removido","Você não tem permissão para isso ou o documento já foi excluído");
	}
	echo "del";//obrigatório imprimir algo (ver arquivo app.js)
}else{
	echo "<meta http-equiv=\"refresh\" content=\"0; url=../404.html\">";
}
?>