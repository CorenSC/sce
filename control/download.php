<?php
//inclui variaveis e informações gerais do sistema
require_once('../config.php');
//require_once("../login_verifica.php"); DESABILITADO pois haverá download sem estar logado
if(isset($_GET["id"]) && !empty($_GET["id"])){
	//carrega funções
	require_once('../bin/functions.php');
	//valida ID do modelo
	$id = sqlTrataInteiro(validaInteiro($_GET["id"],MODELO_ID_SIZE));
	if($id!==false){
		//carrega as bibliotecas para recuperar informações do BD
		require_once("../conexao.php");
		require_once('../model/Registry.php');
		require_once('../model/Modelo.php');
		require_once('../dao/ModeloDAO.php');
		// Armazenar essa instância (conexão) no Registry - conecta uma só vez
		$registry = Registry::getInstance();
		$registry->set('Connection', $myBD);
		// Recupera link através de consulta ao banco de dados
		$Modelo = new Modelo();
		$Modelo->setId($id);
		$ModeloDAO = new ModeloDAO();
		$result = $ModeloDAO->getOne($Modelo);
		if($result && sizeof($result)>0){
			$urlDoc = APP_URL_UPLOAD.'modelos/'.$result["link"];
			if(!file_exists($urlDoc)){
				echo "<script>alert('Opss, arquivo inexistente! Por favor comunique o administrador do sistema. Obrigado!');</script>";
				echo "<meta http-equiv=\"refresh\" content=\"0; url=".APP_URL."\">";
				exit();
			}else{
				$nomeDoc = removeCaracteresEspeciais($result["nome"]).".".retornaExtensaoArquivo($result["link"]);
				header('Content-Description: File Transfer');
				header("Content-Disposition: attachment; filename=\"".$nomeDoc."\";");
				header('Content-Type: application/octet-stream');
			    header('Content-Transfer-Encoding: binary');
			    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			    header('Pragma: public');
			    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past	    
			    readfile($urlDoc);
			}			
		}else{
			echo "<script>alert('O modelo selecionado não está mais disponível, por favor confira se o link está correto e caso o problema persista notifique o Coren/SC. Obrigado!');</script>";
			echo "<meta http-equiv=\"refresh\" content=\"0; url=".APP_URL."\">";
			exit();
		}
	}else{
		echo "<script>alert('As instruções para o download estão incorretas, por favor tente novamente e caso o problema persista notifique o Coren/SC. Obrigado!');</script>";
		echo "<meta http-equiv=\"refresh\" content=\"0; url=".APP_URL."\">";
		exit();
	}
}else{
	echo "<script>alert('Dados incorretos ou link inválido, por favor tente novamente e caso o problema persista notifique o Coren/SC. Obrigado!');</script>";
	echo "<meta http-equiv=\"refresh\" content=\"0; url=".APP_URL."\">";
	exit();
} ?>