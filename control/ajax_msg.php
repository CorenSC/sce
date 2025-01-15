<?php

require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../login_verifica.php");

//carrega as bibliotecas para recuperar informações do BD
//conecta no banco e instacia uma conexão com o Registry
require_once("../conexao.php");
require_once("../model/Registry.php");
// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);

if(isset($_POST["tipoMsg"]) && !empty($_POST["tipoMsg"])){

	$tipoMsg=$_POST["tipoMsg"];
	$msg_1="";
	$msg_2="";
	if(isset($_POST["msg_1"]) && !empty($_POST["msg_1"])){
		$msg_1=$_POST["msg_1"];
	}
	if(isset($_POST["msg_2"]) && !empty($_POST["msg_2"])){
		$msg_2=$_POST["msg_2"];
	}

	//apenas chama função que faz esse envio de mensagens
	enviaMsg($tipoMsg,$msg_1,$msg_2);

}else{
	echo "erro_ajax";
	exit();
}

?>