<?php
//SE NÃO ESTIVER LOGADO, REDIRECIONA PARA login.php
ini_set("session.save_path", APP_URL_UPLOAD.'sessions');
ini_set('session.gc_maxlifetime', APP_SESSION_LIFETIME);
session_cache_expire(APP_SESSION_LIFETIME);
session_set_cookie_params(APP_SESSION_LIFETIME);
ini_set("session.name", APP_SESSION_NAME);
@session_start();
if(!isset($_SESSION['USUARIO'])){
	echo "<meta http-equiv=\"refresh\" content=\"0; url=".APP_URL."/login.php?page=".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."\">";
	exit();	
} ?>
