<?php	
//carrega as bibliotecas para recuperar informações do BD
require_once('../config.php');
require_once('../bin/functions.php');
require_once("../conexao.php");
require_once('../model/Registry.php');
require_once('../dao/PadDAO.php');
require_once('../model/Pad.php');
require_once("../login_verifica.php");

if(isset($_POST["idliberaprazo"])){
	$deletou = false;
	
	$idliberaprazo = validaInteiro(key_decrypt($_POST["idliberaprazo"]),$GLOBALS["secretariaunidadeprazo_idsecretariaunidadeprazo_size"]);
	
	//verifica se o idprocesso é valido e se o usuário pode deletar esse processo
	if($idliberaprazo!==false && verificaFuncaoUsuario($GLOBALS["f_processo_liberaprazo"])!==false){
		
		$idliberaprazo = sqlTrataInteiro($idliberaprazo);
		if($idliberaprazo!==false){
			
			// Armazenar essa instância (conexão) no Registry - conecta uma só vez
			$registry = Registry::getInstance();
			$registry->set('Connection', $myBD);
			// Instancia o objeto nota
			$Pad = new Pad();
			$Pad->setId($idliberaprazo);
			$Pad->setPrazo(date("Ymd"));
			
			// Instanciar o DAO e "excluir" processo
			$PadDAO = new PadDAO();
			$liberou = $PadDAO->updatePrazoLiberacao($Pad);
			/*
			if($liberou){
				//registra ação no log				
				require_once("../dao/HistoricoDAO.php");
				require_once("../model/Historico.php");
				$Historico = new Historico();
				$Historico->setAcao(LOG_DEL_PRO);
				$Historico->setProcesso($idprocesso);
				$Historico->setDocumento(0);
				$Historico->setObs('');
				$HistoricoDAO = new HistoricoDAO();
				$HistoricoDAO->insert($Historico);
			}
			*/
			
		}
	}	
	echo ($liberou)?'sucesso':'problema'; 
}else{
	echo "<meta http-equiv=\"refresh\" content=\"0; url=../404.html\">";
}
?>