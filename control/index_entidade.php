<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");
require_once("../menu_topo.php");

//exibe mensagens conforme o passado na URL:
if(isset($_GET["s"]) && !empty($_GET["s"])){
	echo "<script>";
	
	//NOVO		
	if($_GET["s"]==100){
		echo "showMsgSucesso('Instituição cadastrada com sucesso', '');";
	}
	if($_GET["s"]==101){
		echo "showMsgSucesso('Instituição atualizada com sucesso', '');";
	}
	if($_GET["s"]==91){
		echo "showMsgErro('Erro de execução','A Instituição foi cadastrada porém os SAE não puderam ser cadastrados.');";
	}
	if($_GET["s"]==1){
		echo "showMsgErro('Instituição inválida, removida ou usuário sem acesso a página requisitada','');";
	}

	if($_GET["s"]==71){
		echo "showMsgErro('Instituição atualizada com erros', 'O histórico não pôde ser salvo.');";	
	}
	if($_GET["s"]==72){
		echo "showMsgErro('Instituição não atualizada', 'Os dados não puderam ser inseridos, tente novamente mais tarde.');";	
	}
	if($_GET["s"]==73){
		echo "showMsgErro('Instituição não atualizada', 'Os dados fornecidos estavam inválidos, tente novamente.');";	
	}
	if($_GET["s"]==74){
		echo "showMsgErro('Instituição atualizada com erros', 'O histórico da Instituição não pôde ser salvo.');";
	}
	if($_GET["s"]==75){
		echo "showMsgErro('Erro de execução','A Instituição foi atualizada porém os SAE não puderam ser alterados.');";
	}
	
	echo "</script>";
}


//carrega as bibliotecas para recuperar informações do BD
//conecta no banco e instacia uma conexão com o Registry
require_once("../conexao.php");
require_once("../model/Registry.php");
// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);
//carrega DAO's das chaves estrangeiras
require_once('../dao/EntidadeDAO.php');
require_once('../model/Entidade.php');


//SUBMENU DE AÇÕES
echo "<div id=\"conteudo_borda\" class=\"noprint\"><div class=\"well submenu\">";
if(verificaFuncaoUsuario($GLOBALS["f_entidade_add"])!==false){
 	echo "	<button class=\"btn btn-info\" value=\"add_entidade.php\" title=\"Adicionar Instituição\" type=\"button\" aria-label=\"Left Align\">		
		  		<span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> <strong> INSTITUIÇÃO</strong>
		  	</button>";
}

 	echo "&nbsp;&nbsp;
 			<button class=\"btn btn-primary\" value=\"index_entidadeunidade.php\" title=\"Visualizar Unidades de uma Instituição\" type=\"button\" aria-label=\"Left Align\">		
		  		<span class=\"glyphicon glyphicon-th-list\" aria-hidden=\"true\"></span> <strong>LISTAR UNIDADES DE INSTITUIÇÃO</strong>
		  	</button>";

if(verificaFuncaoUsuario($GLOBALS["f_entidade_add"])!==false){
 	echo "&nbsp;&nbsp;
 			<button class=\"btn btn-info\" value=\"add_entidadeunidade.php\" title=\"Adicionar Unidade de Instituição\" type=\"button\" aria-label=\"Left Align\">		
		  		<span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> <strong> UNIDADE DE INSTITUIÇÃO</strong>
		  	</button>";
}

echo "</div></div>";
//FIM SUBMENU DE AÇÕES


//Paginação:
if(isset($_GET['pagina']) && $_GET['pagina']>0){
	$pc = $_GET['pagina'];
}else{
	$pc = "1";
}
$paginacao_inicio = $pc - 1; 
$paginacao_inicio = $paginacao_inicio * APP_MAX_PAGE_ROWS;


// Instanciar o DAO e retornar dados do banco
$EntidadeDAO = new EntidadeDAO();
if(isset($_GET["order"]) && ($_GET["order"]=="dtcriacao" || $_GET["order"]=="nomeentidade" || $_GET["order"]=="nomemunicipio" || $_GET["order"]=="nomesubsecao" || $_GET["order"]=="nomeentidadetipo")&&($_GET["ascdesc"]=="ASC" || $_GET["ascdesc"]=="DESC") ){
	$results = $EntidadeDAO->index($paginacao_inicio,$_GET["order"],$_GET["ascdesc"]);
}else{
	$results = $EntidadeDAO->index($paginacao_inicio,NULL,NULL);
}





//Paginação:
$tr = $results[0]["paginacao_numlinhas"]; // verifica o número total de registros 
$tp = $tr / APP_MAX_PAGE_ROWS; // verifica o número total de páginas
$tp2 = ceil($tp); //arredonda para cima o número de páginas, ao inves de ser 1.6 é 2.

echo "<div id=\"conteudo_borda\">
<div  id=\"conteudo\"><h5 class='onlyprint'>Listagem de Instituições</h5>";


//se o total de linhas for maior que zero:
if($tr>0){ 


echo '
<table class="table table-condensed table-responsive table-hover">
    <thead>
      <tr>
        <th><a class="reordenar_entidade" href="#" rel="nomeentidade">Razão Social</a></th>
        <th><a class="reordenar_entidade" href="#" rel="nomemunicipio">Cidade</a></th>
        <th><a class="reordenar_entidade" href="#" rel="nomeentidadetipo">Tipo</a></th>
		<th><a class="reordenar_entidade" href="#" rel="nomesubsecao">Subseção</a></th>
		<th><a class="reordenar_entidade" href="#" rel="dtcriacao">Criação</a></th>';
		if(verificaFuncaoUsuario($GLOBALS["f_entidade_edit"]) || verificaFuncaoUsuario($GLOBALS["f_entidade_del"])){
			echo '	<th class="noprint">Ações </th>';
		}
echo '	</tr>
    </thead>
    <tbody>';
	$numprocessos=0;
	for($i=0;$i<sizeof($results);$i++){
		if(isset($results[$i]["nomeentidade"]) && isset($results[$i]["nomemunicipio"]) && isset($results[$i]["nomeentidadetipo"]) && isset($results[$i]["nomesubsecao"]) && isset($results[$i]["dtcriacao"]) ){
			$idusuario=$results[$i]["idusuario"];
			$id=$results[$i]["identidade"];
			$nome=$results[$i]["nomeentidade"];
			$cidade=$results[$i]["nomemunicipio"];
			$tipo=$results[$i]["nomeentidadetipo"];
			$subsecao=$results[$i]["nomesubsecao"].' '.$results[$i]["cidadesubsecao"];;
			$dtcriacao=$results[$i]["dtcriacao"];	
			$numprocessos++;
			echo "
			<tr id=\"id_".$id."\">
			<th scope=\"row\">".$nome."</th>
			<td>".$cidade."</td>
			<td>".$tipo."</td>
			<td>".textoMaiusculo($subsecao)."</td>				
			<td>".exibeDataTimestamp($dtcriacao)."</td>";

			if(verificaFuncaoUsuario($GLOBALS["f_entidade_edit"]) || verificaFuncaoUsuario($GLOBALS["f_entidade_del"])){
				echo "<td class=\"noprint\"><nobr>";
				//verifica as permissóes do usuário
				if(verificaFuncaoUsuario($GLOBALS["f_entidade_edit"])){
					echo "&nbsp;<button title=\"Editar informações da instituição\" class=\"btn btn-success edit_entidade\" aria-label=\"Left Align\" type=\"button\"> <span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span> </button> ";
				}
				//só remove o registro se ele for o criador, admin, presidente ou coordenador
				if(verificaFuncaoUsuario($GLOBALS["f_entidade_del"]) && ($idusuario==$_SESSION["USUARIO"]["idusuario"] || isAdmin() || isCoordenador() || isPresidente() ) ){
					echo "&nbsp;<button title=\"Remover instituição\" class=\"btn btn-warning del_entidade\" aria-label=\"Left Align\" type=\"button\"> <span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span> </button> ";
				}
				if(verificaFuncaoUsuario($GLOBALS["f_historico_entidade"])){
					echo "&nbsp;<button value=\"view_entidade_historico.php?t=1&p=".$id."\" title=\"Histórico da instituição\" class=\"btn btn-info\" aria-label=\"Left Align\" type=\"button\"> <span class=\"glyphicon glyphicon-time\" aria-hidden=\"true\"></span> </button> ";
				}			
				echo "</nobr></td>";
			}

			echo "</tr>";

		}
	}

	if($numprocessos>0){
		echo '</tbody></table>';
	}else{
		echo 'Nenhuma instituição encontrada!<br><br>';
	}


}else{
	echo 'Nenhuma instituição encontrada!<br><br>';
}

	//Paginação:
	$outrosParametrosPaginacao="";
	//Agora vamos criar os botões "Anterior e Próximo" ao final da página
	include_once("../paginacao.php");

	

?>

<?php

include_once("../menu_rodape.php"); 

?>