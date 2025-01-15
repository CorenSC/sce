<?php	
//carrega as bibliotecas para recuperar informações do BD
require_once('../config.php');
require_once('../bin/functions.php');
require_once("../conexao.php");
require_once('../model/Registry.php');
require_once('../dao/ProcessoDAO.php');
require_once('../model/Processo.php');
require_once("../login_verifica.php");

if(isset($_POST["ordem"])){
	
	$reordenou = false;	
	$ordem = $_POST["ordem"];

	// Armazenar essa instância (conexão) no Registry - conecta uma só vez
	$registry = Registry::getInstance();
	$registry->set('Connection', $myBD);			

	
	for($i=0;$i<sizeof($ordem);$i++){
		$note = new Note();
		$note->setId($ordem[$i]);
		$note->setOrder($i);
		$note->setUser($_SESSION["USUARIO"]["iduser"]);

		// Instanciar o DAO e excluir a nota da base
		$noteDAO = new NoteDAO();
		$reordenou = $noteDAO->updateOrder($note);
	}
	
	echo ($reordenou)?'sucesso':'problema';		
}else{
	echo "<meta http-equiv=\"refresh\" content=\"0; url=../404.html\">";
}	
	
?>