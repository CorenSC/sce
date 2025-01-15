<?php
@ini_set("default_socket_timeout","03");
$f=@fopen("https://www.google.com/recaptcha/api.js","r");
if(!$f){
	$usuario_com_internet=false;
}else{
	$r=@fread($f,4);
	@fclose($f);
	if(strlen($r)>1) {
		$usuario_com_internet=true;
	}else{
		$usuario_com_internet=false;
	}
}
?>