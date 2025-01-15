<?php	
//carrega as bibliotecas para recuperar informações do BD
require_once('../config.php');
require_once('../bin/functions.php');
require_once("../conexao.php");
require_once('../model/Registry.php');
require_once('../dao/UsuarioDAO.php');
require_once('../model/Usuario.php');
require_once("../login_verifica.php");

if(isset($_POST["idusuario"])){
	$deletou = false;
	$idusuario =validaInteiro($_POST["idusuario"],USUARIO_ID_SIZE);

	//verifica se o idprocesso é valido e se o usuário pode deletar esse documento
	if($idusuario!==false && verificaFuncaoUsuario(FUNCAO_USUARIO_DEL)!==false){
		
		$idusuario = sqlTrataInteiro($idusuario);
		
		if($idusuario!==false){
			
			// Armazenar essa instância (conexão) no Registry - conecta uma só vez
			$registry = Registry::getInstance();
			$registry->set('Connection', $myBD);			
			// Instancia o objeto
			$usuario = new Usuario();
			$usuario->setId($idusuario);			
			// Instanciar o DAO e "excluir" usuario
			$usuarioDAO = new UsuarioDAO();
			$dados = $usuarioDAO->getOne($idusuario);
			$deletou = $usuarioDAO->delete($usuario);
			//se deletou usuário			
			if($deletou){
				//deleta também as configurações de visibilidade de processo, se houver
				$deletou_processos = $usuarioDAO->deleteLimitacaoProcesso($usuario);
				//complementa infos do log
				$usuario->setLogin($dados["login"]);
				$usuario->setNome($dados["nome"]);
				//registra ação no log
				require_once("../dao/HistoricoDAO.php");
				require_once("../model/Historico.php");
				$Historico = new Historico();
				$Historico->setAcao(LOG_DEL_USER);
				$Historico->setProcesso(0);
				$Historico->setDocumento(0);
				$Historico->setObs(sqlTrataString($usuario->toLog()));
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