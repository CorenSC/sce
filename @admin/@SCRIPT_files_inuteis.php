<?php

echo "<u>## Arquivos em desuso ##</u><br>";
require_once("../config.php");
require_once("../conexao.php");
require_once("../model/Registry.php");
// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);
//conta registros inválidos
$cont = 0;

/* 	MODELOS 			
echo "<h3>MODELOS</h3><hr>";

					//carrega DAO
					require_once('../dao/ModeloDAO.php');
					$ModeloDAO = new ModeloDAO();
					//retorna os registros ativos
					$validos = $ModeloDAO->getAll();
					//varre a pasta de modelos verificando qual arquivo que está lá que não está na lista dos ativos:
					$dir = APP_URL_UPLOAD."modelos/";
		            // varre arquivos da pasta
		            foreach (glob($dir."*") as $file) {
		            	$encontrou=false;
		            	//retira o caminho do nome do arquivo
		            	$file=str_ireplace($dir, "", $file);
		            	foreach ($validos as $v) {
							if($v["link"]==$file){
								$encontrou=true;
							}
						}
		                if(!$encontrou){
		                	$cont++;
		                	echo $cont." - ".$file."<br>";
		                }
		            }
	FIM MODELOS */		            




/* DOCUMENTOS DE PROCESSOS */	
echo "<h3>DOCUMENTOS DE PROCESSOS</h3><hr>";
					//carrega MODEL
					require_once('../model/Processo.php');
					//carrega DAO
					require_once('../dao/ProcessoDAO.php');
					require_once('../dao/DocumentoDAO.php');
					$ProcessoDAO = new ProcessoDAO();
					$DocumentoDAO = new DocumentoDAO();
					//retorna os processos ativos
					$validos = $ProcessoDAO->getAll();
					//instancia var de Processo
					$Processo = new Processo();
					//varre a pasta de modelos verificando qual arquivo que está lá que não está na lista dos ativos:
					$dir = APP_URL_UPLOAD;
					//varre os processos
					foreach ($validos as $v) {

						$dir=APP_URL_UPLOAD.$v["idprocesso"]."/";

						
						//retorna docs validos do processo
						$Processo->setId($v["idprocesso"]);
						$docs = $DocumentoDAO->getAllFromProcesso($Processo);
						// varre arquivos da pasta
						$arquivos_dup = array();
			            foreach (glob($dir."*") as $file) {
			            	$encontrou=false;
			            	//retira o caminho do nome do arquivo
			            	$file=str_ireplace(APP_URL_UPLOAD.$Processo->getId()."/", "", $file);			            	
			            	foreach ($docs as $v) {
								if($v["link"]==$file){
									$encontrou=true;
								}
							}
			                if(!$encontrou){
			                	$cont++;
			                	$arquivos_dup[]=$file;
			                }
			            }
			            if(sizeof($arquivos_dup)>0){
			            	echo "<strong>PROCESSO ".$Processo->getId()."</strong><br>";
			            	for($i=0;$i<sizeof($arquivos_dup);$i++){
			            		echo ($i+1)." - ".$arquivos_dup[$i]."<br>";
			            	}
			            	echo "<hr>";
			            }
					}

if($cont<1){
	echo "<hr><h3>Maravilha - Nenhum arquivo em desuso!</h3>";
}else{
	echo "<br><h3>".$cont." arquivos em desuso!</h3>";
}
exit();
?>