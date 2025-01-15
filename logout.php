<?php
	require_once("config.php");	
	require_once("bin/functions.php");	
	ini_set("session.save_path", APP_URL_UPLOAD.'sessions');
	ini_set("session.name", APP_SESSION_NAME);
	session_start();
	unset($_SESSION[APP_SESSION_NAME]);
	// destroy the Session, not just the data stored!
	session_destroy();
	// delete the session contents, but keep the session_id and name:
	session_unset(APP_SESSION_NAME);
	echo "<meta http-equiv=\"refresh\" content=\"0; url=".APP_URL."/login.php?s=9\">";
	exit();
?>