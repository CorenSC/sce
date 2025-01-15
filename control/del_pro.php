<?php	
//carrega as bibliotecas para recuperar informações do BD
require_once('../config.php');
require_once('../bin/functions.php');
require_once("../login_verifica.php");
require_once("../conexao.php");

if(isset($_POST["id"])){
	$deletou = false;
	
	$id = validaInteiro($_POST["id"],PROCESSO_ID_SIZE);
	
	//verifica se o id é valido e se o usuário pode deletar esse registro
	if($id!==false && verificaProcessoUsuario($id) && verificaFuncaoUsuario(FUNCAO_PROCESSO_DEL)){
		
		$id = sqlTrataInteiro($id);
		if($id!==false){
			
			// Carrega DAOS e Models pertinentes
			require_once('../model/Registry.php');
			require_once('../model/Processo.php');
			require_once('../dao/ProcessoDAO.php');			
			// Armazenar essa instância (conexão) no Registry - conecta uma só vez
			$registry = Registry::getInstance();
			$registry->set('Connection', $myBD);
			// Instanciar o DAO
			$ProcessoDAO = new ProcessoDAO();
			// Instancia o objeto
			$Processo = new Processo();
			$Processo->setId($id);
			
			$dados=$ProcessoDAO->getInfosCapa($Processo);	
			//se não tiver dados, erro = processo já excluído
			if(!$dados){
				echo 'problema';
				exit();
			}
			$deletou = $ProcessoDAO->delete($Processo);

			//após deletar, add VALORES para o log
			$Processo->setUsuario($dados["nomeresponsavel"]);
			$Processo->setProcessoTipo($dados["nometipo"]);
			$Processo->setEtapa($dados["ordemetapa"]." - ".$dados["nomeetapa"]);
			$Processo->setNumero($dados["numero"]);
			$Processo->setDtPosse1($dados["dtposse1"]);
			$Processo->setDtPosse2($dados["dtposse2"]);
			$Processo->setDtPosse3($dados["dtposse3"]);
			$Processo->setDtEscolhida($dados["dtescolhida"]);
			$Processo->setObsPosse($dados["obsposse"]);

			if($deletou){
				$log = $Processo->toLog();
				//registra ação no log
				require_once("../dao/HistoricoDAO.php");
				require_once("../model/Historico.php");
				$Historico = new Historico();
				$Historico->setAcao(LOG_DEL_PRO);
				$Historico->setProcesso($id);
				$Historico->setDocumento(0);
				$Historico->setObs($log);
				$HistoricoDAO = new HistoricoDAO();
				$HistoricoDAO->insert($Historico);
			}
			
		}
	}	
	echo ($deletou)?'sucesso':'problema'; 
}else{
	echo "<meta http-equiv=\"refresh\" content=\"0; url=../404.html\">";
}
?>