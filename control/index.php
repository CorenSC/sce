<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../login_verifica.php");
require_once("../bin/js-css.php");
require_once("../menu_topo.php");

//pega infos do Usuário
//carrega as bibliotecas para recuperar informações do BD
//conecta no banco e instacia uma conexão com o Registry
require_once("../conexao.php");
require_once("../model/Registry.php");
// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);
//carrega DAO's p/ retornar infos
require_once('../dao/UsuarioDAO.php');
require_once('../model/Usuario.php');

// Instanciar o DAO e retornar dados do banco
$UsuarioDAO = new UsuarioDAO();
$infos=$UsuarioDAO->getOne($_SESSION["USUARIO"]["idusuario"]);
$ultimosacessos=$UsuarioDAO->getUltimosAcessos($_SESSION["USUARIO"]["idusuario"]);
$ultimosvisualizados=$UsuarioDAO->getUltimosVisualizados($_SESSION["USUARIO"]["idusuario"]);
$ultimosinseridos=$UsuarioDAO->getUltimosInseridos($_SESSION["USUARIO"]["idusuario"]);
$ultimosremovidos=$UsuarioDAO->getUltimosRemovidos($_SESSION["USUARIO"]["idusuario"]);

$nomecompleto=$_SESSION["USUARIO"]["nome"];
$perfilusuario=$infos["nomeperfil"];
$email1=$infos["email1"];
if(strlen($infos["email2"])>1){
	$email2=$infos["email2"];
}else{
	$email2="Não informado";	
}
if(strlen($infos["telefone"])>1){
	$telefone=exibeTelefone($infos["telefone"]);
}else{
	$telefone="Não informado";	
}
if(strlen($infos["celular"])>1){
	$celular=exibeTelefone($infos["celular"]);
}else{
	$celular="Não informado";	
}

echo "	<div id=\"conteudo_borda\">
			<div  id=\"conteudo\"><h5 class='onlyprint'>Tela de Início</h5>";

echo "		<p><strong>".saudacao($nomecompleto)."</strong>!</p>";
if(sizeof($ultimosacessos)<5){
	echo "		<p>Seja bem vindo(a) ao ".APP_TITLE."!</p>
				<p>Leia as informações desta página até o fim para adquirir noções básicas do sistema.</p><br>";
}

if(!isInstituicao()){
echo "		<p>Utilize os botões do menu acima para navegar no sistema e efetuar ações.</p>";
}else{
echo "		<p>Clique no botão <a href=\"index_pro.php\"><img style=\"border:1px solid;\" src=\"..\common\images\btprocesso.png\"></a> do menu acima para acessar seu processo e efetuar ações.</p>";	
}

echo "		<p>Você possui alguma dúvida? Se tiver <a target=\"_blank\" href=\"../Manual_SCE_20180207.pdf\">clique aqui</a> ou no ícone de ajuda <span class=\"glyphicon glyphicon-question-sign\" aria-hidden=\"true\"></span> localizado no topo da página.</p>";

echo "		<p>Caso queira alterar seus dados cadastrais, <a target=\"_self\" href=\"edit_user.php\">clique aqui</a> ou no seu nome de usuário localizado no topo da página.</p>";

//exibe infos de usabilidade caso seja usuário novato
if(sizeof($ultimosacessos)<5 && sizeof($ultimosvisualizados)<5){
echo "		<p style=\"text-decoration:underline;\"><br><strong>Iconografia básica do sistema:</strong></p>";
echo "		<p><button class=\"btn btn-primary\"><span class=\"glyphicon glyphicon-zoom-in\" aria-hidden=\"true\"></span></button> - Visualizar ou acessar determinada parte do sistema</p>";
echo "		<p><button class=\"btn btn-success\"><span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span></button> - Editar ou atualizar alguma informação</p>";
echo "		<p><button class=\"btn btn-warning\"><span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span></button> - Remover algum registro</p>";
}
?>
	<script>
		//para funcionar os accordion:
		$(function() {
			$( ".accordion" ).accordion({heightStyle: "content"});
		});
	</script>
	<?php
	echo '<div class="accordion">
			<h3>Informações gerais do seu usuário</h3>
			<div>';
echo "		<p><strong>Perfil:</strong> ".$perfilusuario.".</p>";
echo "		<p><strong>E-mail Principal:</strong> ".$email1.".</p>";
echo "		<p><strong>E-mail Secundário:</strong> ".$email2.".</p>";
echo "		<p><strong>Telefone:</strong> ".$telefone.".</p>";
echo "		<p><strong>Celular:</strong> ".$celular.".</p>";

			echo '</div>';
if(sizeof($ultimosacessos)>1){			
			echo '<h3>Últimos ('.sizeof($ultimosacessos).') acessos ao sistema</h3>
			<div><ul>';
			for($i=0;$i<sizeof($ultimosacessos);$i++){
				echo "<li>".exibeDataTimeStamp($ultimosacessos[$i]["dthistorico"])."</li>";
			}

			echo '</ul></div>';
}	

if(sizeof($ultimosvisualizados)>0){
			echo '<h3>Últimos ('.sizeof($ultimosvisualizados).') documentos visualizados</h3>
			<div>
			<table class="table table-condensed table-responsive table-hover">
			<thead>
			<tr>
				<th>Data</th>
				<th>Processo</th>
				<th>Documento</th>
			</tr>
			</thead>';
			for($i=0;$i<sizeof($ultimosvisualizados);$i++){
				$dt= exibeDataTimeStamp($ultimosvisualizados[$i]["dthistorico"]);
				$idprocesso=$ultimosvisualizados[$i]["idprocesso"];
				$processo=$ultimosvisualizados[$i]["numero"];
				$documento=$ultimosvisualizados[$i]["nome"];
				if($ultimosvisualizados[$i]["flag"]!=1){
					$documento.=" (removido)";
				}else{
					$documento.=" - ".$ultimosvisualizados[$i]["obs"];
				}				
				echo "<tr>
					<td>".$dt."</td>
					<td><a target='_blank' href='index_doc.php?p=".$idprocesso."'>Processo nº ".$processo."</a></td>
					<td>".$documento."</td>
				</tr>";
			}

			echo '</table></div>';
}

if(sizeof($ultimosinseridos)>0){			
			echo '<h3>Últimos ('.sizeof($ultimosinseridos).') documentos inseridos</h3>
			<div>
			<table class="table table-condensed table-responsive table-hover">
			<thead>
			<tr>
				<th>Data</th>
				<th>Processo</th>
				<th>Documento</th>
			</tr>
			</thead>';
			for($i=0;$i<sizeof($ultimosinseridos);$i++){
				$dt= exibeDataTimeStamp($ultimosinseridos[$i]["dthistorico"]);
				$idprocesso=$ultimosinseridos[$i]["idprocesso"];
				$processo=$ultimosinseridos[$i]["numero"];
				$documento=$ultimosinseridos[$i]["nome"];

				if($ultimosinseridos[$i]["flag"]!=1){
					$documento.=" (removido)";
				}else{
					$documento.=" - <a target='_blank' href='window.php?a=view&p=doc&id=".$ultimosinseridos[$i]["iddocumento"]."'>Visualizar arquivo</a></h3>";
				}
				
				echo "<tr>
					<td>".$dt."</td>
					<td><a target='_blank' href='index_doc.php?p=".$idprocesso."'>Processo nº ".$processo."</a></td>
					<td>".$documento."</td>
				</tr>";
			}

			echo '</table></div>';
}

if(sizeof($ultimosremovidos)>0){			
		echo '<h3>Últimos ('.sizeof($ultimosremovidos).') documentos removidos</h3>
		<div>
		<table class="table table-condensed table-responsive table-hover">
		<thead>
		<tr>
			<th>Data</th>
			<th>Processo</th>
			<th>Documento</th>
		</tr>
		</thead>';
		for($i=0;$i<sizeof($ultimosremovidos);$i++){
			$dt= exibeDataTimeStamp($ultimosremovidos[$i]["dthistorico"]);
			$idprocesso=$ultimosremovidos[$i]["idprocesso"];
			$processo=$ultimosremovidos[$i]["numero"];
			$documento=$ultimosremovidos[$i]["nome"]." - <a target='_blank' href='window.php?a=view&p=doc&id=".$ultimosremovidos[$i]["iddocumento"]."'>Visualizar arquivo</a></h3>";
			
			echo "<tr>
				<td>".$dt."</td>
				<td><a target='_blank' href='index_doc.php?p=".$idprocesso."'>Processo nº ".$processo."</a></td>
				<td>".$documento."</td>
			</tr>";
		}

		echo '</table></div>';
}


//INSERIDO NO RODAPÉ DE PÁGINAS COM MAIOR ACESSO (INDEX/INDEX_PRO/INDEX_DOC) - FUNÇÕES AUTOMÁTICAS DE CONFIG DO SISTEMA
require_once("@config.php");

include_once("../menu_rodape.php"); 
?>