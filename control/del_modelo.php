<?php	
//carrega as bibliotecas para recuperar informações do BD
require_once('../config.php');
require_once('../bin/functions.php');
require_once("../login_verifica.php");
require_once("../conexao.php");

if(isset($_POST["id"])){
	$deletou = false;
	
	$id = validaInteiro($_POST["id"],MODELO_ID_SIZE);
	
	//verifica se o id é valido e se o usuário pode deletar esse registro
	if($id!==false && verificaFuncaoUsuario(FUNCAO_MODELO_DEL)!==false){
		
		$id = sqlTrataInteiro($id);
		if($id!==false){
			
			// Carrega DAOS e Models pertinentes
			require_once('../model/Registry.php');
			require_once('../model/Modelo.php');
			require_once('../dao/ModeloDAO.php');			
			// Armazenar essa instância (conexão) no Registry - conecta uma só vez
			$registry = Registry::getInstance();
			$registry->set('Connection', $myBD);
			// Instanciar o DAO
			$ModeloDAO = new ModeloDAO();
			// Instancia o objeto nota
			$Modelo = new Modelo();
			$Modelo->setId($id);
			
			$dados=$ModeloDAO->getOne($Modelo);		
			$deletou = $ModeloDAO->delete($Modelo);

			//após deletar, add VALORES para o log
			$Modelo->setNome($dados["nome"]);

			if($deletou){
				$log = $Modelo->toLog();
				//registra ação no log
				require_once("../dao/HistoricoDAO.php");
				require_once("../model/Historico.php");
				$Historico = new Historico();
				$Historico->setAcao(LOG_DEL_MODELO);
				$Historico->setProcesso(0);
				$Historico->setDocumento(0);
				$Historico->setObs($log);
				$HistoricoDAO = new HistoricoDAO();
				$HistoricoDAO->insert($Historico);
				//remove arquivo de fato
				@unlink(APP_URL_UPLOAD."modelos/".$dados["link"]);
			}
			
		}
	}	
	echo ($deletou)?'sucesso':'problema'; 
}else{
	echo "<meta http-equiv=\"refresh\" content=\"0; url=../404.html\">";
}
?>