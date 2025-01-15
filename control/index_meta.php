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

			//ADD
			if($_GET["s"]==100){
				echo "showMsgSucesso('Meta cadastrada com sucesso','');";
			}
			if($_GET["s"]==119 && isset($_GET["n"])){
				echo "showMsgErro('Erro', 'Uma meta chamada <strong>\"".$_GET["n"]."\"</strong> para o mesmo ano já existe, por favor, utilize outro nome');";	
			}
			if($_GET["s"]==110){
				echo "showMsgErro('Meta não cadastrado','Tente novamente mais tarde');";
			}

			//EDIT
			if($_GET["s"]==200){
				echo "showMsgSucesso('Meta atualizada com sucesso','');";
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
require_once("../dao/MetaDAO.php");
require_once('../model/Meta.php');


//SUBMENU DE AÇÕES
echo "<div id=\"conteudo_borda\" class=\"noprint\"><div class=\"well submenu\">";
if(verificaFuncaoUsuario($GLOBALS["f_meta_add"])!==false){	
 	echo "	<button class=\"btn btn-info\" value=\"add_meta.php\" title=\"Adicionar Meta\" type=\"button\" aria-label=\"Left Align\">		
		  		<span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> <strong> META</strong>
		  	</button>";
}
if(verificaFuncaoUsuario($GLOBALS["f_metaconfig_add"])!==false){
 	echo "&nbsp;&nbsp; <button class=\"btn btn-primary\" value=\"index_metaconfig.php\" title=\"Parâmetros anuais das Metas\" type=\"button\" aria-label=\"Left Align\">		
		  		<span class=\"glyphicon glyphicon-cog\" aria-hidden=\"true\"></span> <strong>PARÂMETROS DAS METAS</strong>
		  	</button>";
}
if(verificaFuncaoUsuario($GLOBALS["f_metaconfig_add"])!==false){
 	echo "&nbsp;&nbsp; <button class=\"btn btn-info\" value=\"add_metaconfig.php\" title=\"Adicionar parâmetros anual de meta\" type=\"button\" aria-label=\"Left Align\">		
		  		<span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> <strong> PARÂMETRO ANUAL</strong>
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
$MetaDAO = new MetaDAO();
//$results = $metaDAO->index(); 
if(isset($_GET["order"]) && ($_GET["order"]=="ano" || $_GET["order"]=="nome")&&($_GET["ascdesc"]=="ASC" || $_GET["ascdesc"]=="DESC") ){
	$results = $MetaDAO->index($paginacao_inicio,$_GET["order"],$_GET["ascdesc"]);
}else{
	$results = $MetaDAO->index($paginacao_inicio,NULL,NULL);	
}

//Paginação:
$tr = $results[0]["paginacao_numlinhas"]; // verifica o número total de registros 
$tp = $tr / APP_MAX_PAGE_ROWS; // verifica o número total de páginas
$tp2 = ceil($tp); //arredonda para cima o número de páginas, ao inves de ser 1.6 é 2.

echo "<div id=\"conteudo_borda\">
<div  id=\"conteudo\"><h5 class='onlyprint'>Listagem de Metas</h5>";


//se o total de linhas for maior que zero:
if($tr>0){

echo '
<table class="table table-condensed table-responsive table-hover">
    <thead>
      <tr>
        <th><a class="reordenar_meta" href="#" rel="ano">Ano</a></th>
        <th><a class="reordenar_meta" href="#" rel="nome">Meta</a></th>
		<th class="noprint">Ações </th>
      </tr>
    </thead>
    <tbody>
	';
	$nummetas=0;
	for($i=0;$i<sizeof($results);$i++){		
		
		if( isset($results[$i]["idmeta"]) && isset($results[$i]["ano"]) && isset($results[$i]["nome"]) ){
			
			$meta_id=$results[$i]["idmeta"];
			$meta_ano=$results[$i]["ano"];
			$meta_nome=$results[$i]["nome"];
			$nummetas++;
			
			echo "
			<tr id=\"id_".$meta_id."\">
			<th scope=\"row\">".$meta_ano."</th>
			<td>".$meta_nome."</td>

			<td class=\"noprint\"><nobr>";

			
			//verifica as permissóes do usuário e exibe botão de VISUALIZAR, EDITAR E REMOVER
			if(verificaFuncaoUsuario($GLOBALS["f_meta_view"])){
				echo "<button value=\"view_meta.php?id=".$meta_id."\" class=\"btn btn-primary\" aria-label=\"Left Align\" type=\"button\"  title=\"Visualizar meta\"> <span class=\"glyphicon glyphicon-zoom-in\" aria-hidden=\"true\"></span> </button> ";
			}
			if(verificaFuncaoUsuario($GLOBALS["f_meta_edit"])){
				echo "&nbsp;<button class=\"btn btn-success\" value=\"edit_meta.php?p=".$meta_id."\" aria-label=\"Left Align\" type=\"button\" title=\"Editar meta\"> <span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span> </button> ";
			}
			if(verificaFuncaoUsuario($GLOBALS["f_meta_del"])){
				echo "&nbsp;<button class=\"btn btn-warning del_meta\" aria-label=\"Left Align\" type=\"button\" title=\"Excluir meta\"> <span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span> </button> ";
			}
			
			echo "</nobr></td></tr>
			";

		}
	}

	if($nummetas>0){
		echo '</tbody></table>';
	}else{
		echo 'Nenhuma meta encontrada!<br><br>';
	}
}else{
	echo 'Nenhuma meta encontrada!<br><br>';
}

	//Paginação:
	$outrosParametrosPaginacao="";	
	//Agora vamos criar os botões "Anterior e Próximo" ao final da página
	include_once("../paginacao.php");

?>

<?php

include_once("../menu_rodape.php"); 

?>