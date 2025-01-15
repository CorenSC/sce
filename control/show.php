<?php
require_once('../config.php');
require_once('../bin/functions.php');
require_once("../login_verifica.php");

if(isset($_GET["f"]) && !empty($_GET["f"])){
	//quebra em partes o endereço enviado via url
	$aux = explode(APP_LINE_BREAK, key_decrypt(urldecode($_GET["f"])));
	//se não encontrar posicao 1 e 2 do array aux, redireciona para tela de erro
	if(!isset($aux[1]) || !isset($aux[2]) || empty($aux[1]) || empty($aux[2])){
		enviaMsg("erro","Tentativa inválida de acesso a um documento","");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=../404.html\">";
		exit();
	}
	$idprocesso = $aux[1];
	$link = $aux[2];
	if(isset($_GET["n"]) && !empty($_GET["n"])){
		$nomeDoc = 	base64_decode($_GET["n"]);
	}else{
		$nomeDoc = 	"Documento_CorenSC";
	}
	$extensao = retornaExtensaoArquivo($link);
	//se for PDF imprime na tela, todos outros formatos obriga o download
	if(verificaExtensaoArquivo($link,'pdf')){
		//impedindo o download:     heaader("HTTP/1.0 404 Not Found");
		header('Content-Type: application/pdf');
		header('Content-Disposition: inline; filename="'.$nomeDoc.'.'.$extensao.'";');
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	}else{
		header('Content-Description: File Transfer');
		header("Content-Disposition: attachment; filename=\"$nomeDoc.$extensao\";");
		header('Content-Type: application/octet-stream');
	    header('Content-Transfer-Encoding: binary');
	    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	    header('Pragma: public');
	    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	}
	$arquivo = '..'.APP_UPLOAD_FOLDER.$idprocesso.'/'.$link;
	$f=@fopen($arquivo,"r");
	//se o usuário tem permissão de acesso a este processo:
	if(verificaProcessoUsuario($idprocesso) && $f){		
		readfile('..'.APP_UPLOAD_FOLDER.$idprocesso.'/'.$link);
	}else{
		enviaMsg("erro","Tentativa inválida de acesso a documento","Arquivo: $arquivo");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=../404.html\">";
		exit();
	}
}else{
	echo "<meta http-equiv=\"refresh\" content=\"0; url=../404.html\">";
	exit();
} ?>