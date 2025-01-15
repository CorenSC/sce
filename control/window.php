<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");
?>
<?php
if(isset($_REQUEST["id"]) && isset($_REQUEST["a"]) && isset($_REQUEST["p"])){

$idprocesso=$_REQUEST["id"];
$acao=$_REQUEST["a"];
$pagina=$_REQUEST["p"];

if(isset($_REQUEST["d"])){
	$documento=$_REQUEST["d"];
}else{
	$documento=false;
}
if(isset($_REQUEST["c"])){
	$checklist=$_REQUEST["c"];
}else{
	$checklist=false;
}
if(isset($_REQUEST["t"])){
	$tipo=$_REQUEST["t"];
}else{
	$tipo=false;
}

$title1="";
if($acao=="view")
	$title1="Visualização de ";
if($acao=="edit")
	$title1="Edição de ";

$title2="";
if($pagina=="doc")
	$title2="documento";
if($pagina=="pro")
	$title2="processo";
if($pagina=="check")
	$title2="checklist";
	
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo APP_CHARSET; ?>" />
<title><?php echo APP_TITLE.' - '.$title1.' '.$title2; ?></title>
</head>
<body>
    <div id="conteudo_borda">    
	<?php
	if($acao=="view" || $acao=="edit" || $acao=="del"){
		if($pagina=="doc" || $pagina=="pro" || $pagina=="check"){
			require_once($acao."_".$pagina.".php");
		}else{
			require_once("../404.html");
			exit();
		}
	}else{
		require_once("../404.html");
		exit();
	}
	?>    
    </div>    
<?php
}else{
	require_once("../404.html");
	exit();
}
?>

</body>
</html>