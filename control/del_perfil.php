<?php	
//carrega as bibliotecas para recuperar informações do BD
require_once('../config.php');
require_once('../bin/functions.php');
require_once("../conexao.php");
require_once('../model/Registry.php');
require_once('../dao/PerfilDAO.php');
require_once('../model/Perfil.php');
require_once("../login_verifica.php");

if(isset($_POST["idperfil"])){
	$deletou = false;
	
	$idperfil = validaInteiro($_POST["idperfil"],PERFIL_ID_SIZE);
	
	//verifica se o idprocesso é valido e se o usuário pode deletar esse processo
	if($idperfil!==false && verificaFuncaoUsuario(FUNCAO_PERFIL_DEL)!==false){
		
		$idperfil = sqlTrataInteiro($idperfil);		
		if($idperfil!==false){
			
			// Armazenar essa instância (conexão) no Registry - conecta uma só vez
			$registry = Registry::getInstance();
			$registry->set('Connection', $myBD);
			// Instanciar o DAO
			$perfilDAO = new PerfilDAO();
			// Instancia o objeto
			$perfil = new Perfil();
			$perfil->setId($idperfil);
			
			$dados=$perfilDAO->getOne($idperfil);
			$deletou = $perfilDAO->delete($perfil);

			//após deletar, altera os IDS para VALORES
			$perfil->setNome($dados["nome"]);

			if($deletou){
				//registra ação no log
				$log_obs = $perfil->toLog();
				require_once("../dao/HistoricoDAO.php");
				require_once("../model/Historico.php");
				$Historico = new Historico();
				$Historico->setAcao(LOG_DEL_PERFIL);
				$Historico->setProcesso(0);
				$Historico->setDocumento(0);
				$Historico->setObs($log_obs);
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