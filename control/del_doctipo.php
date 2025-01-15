<?php	
//carrega as bibliotecas para recuperar informações do BD
require_once('../config.php');
require_once('../bin/functions.php');
require_once("../login_verifica.php");
require_once("../conexao.php");

if(isset($_POST["id"])){
	$deletou = false;
	
	$id = validaInteiro($_POST["id"],DOCUMENTOTIPO_ID_SIZE);
	
	//verifica se o id é valido e se o usuário pode deletar esse registro
	if($id!==false && verificaFuncaoUsuario(FUNCAO_DOCUMENTOTIPO_DEL)!==false){
		
		$id = sqlTrataInteiro($id);
		if($id!==false){
			
			// Carrega DAOS e Models pertinentes
			require_once('../model/Registry.php');
			require_once('../model/DocumentoTipo.php');
			require_once('../dao/DocumentoTipoDAO.php');			
			// Armazenar essa instância (conexão) no Registry - conecta uma só vez
			$registry = Registry::getInstance();
			$registry->set('Connection', $myBD);
			// Instanciar o DAO
			$DocumentoTipoDAO = new DocumentoTipoDAO();
			// Instancia o objeto
			$DocumentoTipo = new DocumentoTipo();
			$DocumentoTipo->setId($id);
			
			$dados=$DocumentoTipoDAO->getOne($DocumentoTipo);
			$deletou = $DocumentoTipoDAO->delete($DocumentoTipo);

			//após deletar, altera os IDS para VALORES
			$DocumentoTipo->setNome($dados["nome"]);

			if($deletou){
				$log = $DocumentoTipo->toLog();
				//registra ação no log
				require_once("../dao/HistoricoDAO.php");
				require_once("../model/Historico.php");
				$Historico = new Historico();
				$Historico->setAcao(LOG_DEL_TIPODOC);
				$Historico->setProcesso(0);
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