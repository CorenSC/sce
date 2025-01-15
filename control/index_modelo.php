<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");
require_once("../menu_topo.php");

//carrega as bibliotecas para recuperar informações do BD
//conecta no banco e instacia uma conexão com o Registry
require_once("../conexao.php");
require_once("../model/Registry.php");
// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);
//carrega DAO's das chaves estrangeiras
require_once("../dao/ModeloDAO.php");
require_once('../model/Modelo.php');

//SUBMENU DE AÇÕES
require_once('submenu_modelo.php');
//FIM SUBMENU DE AÇÕES

echo "<div id=\"conteudo_borda\">
<div  id=\"conteudo\"><h5 class='onlyprint'>Listagem de Modelos de Documento</h5>";
$ModeloDAO=new ModeloDAO();
$results=$ModeloDAO->getAll();

//Paginação:
if(isset($_GET['pagina']) && $_GET['pagina']>0){
	$pc = $_GET['pagina'];
}else{
	$pc = "1";
}
$paginacao_inicio = $pc - 1; 
$paginacao_inicio = $paginacao_inicio * APP_MAX_PAGE_ROWS;


// Instanciar o DAO e retornar dados do banco
$ModeloDAO = new ModeloDAO();
if(isset($_GET["order"]) && ($_GET["order"]=="dtcriacao" || $_GET["order"]=="nome" || $_GET["order"]=="dtatualizacao")&&($_GET["ascdesc"]=="ASC" || $_GET["ascdesc"]=="DESC") ){
	$results = $ModeloDAO->index($paginacao_inicio,$_GET["order"],$_GET["ascdesc"]);
}else{
	$results = $ModeloDAO->index($paginacao_inicio,NULL,NULL);
}


//Paginação:
$tr = $results[0]["paginacao_numlinhas"]; // verifica o número total de registros 
$tp = $tr / APP_MAX_PAGE_ROWS; // verifica o número total de páginas
$tp2 = ceil($tp); //arredonda para cima o número de páginas, ao inves de ser 1.6 é 2.

//se o total de linhas for maior que zero:
if($tr>0){ 


echo '
<table class="table table-condensed table-responsive table-hover">
    <thead>
      <tr>
        <th><a class="reordenar_padrao" href="#" rel="index_modelo|nome">'.exibeFlagReordenacao('nome').'Nome</a></th>
        <th><a class="reordenar_padrao" href="#" rel="index_modelo|dtcriacao">'.exibeFlagReordenacao('dtcriacao').'Data de Criação</a></th>
        <th><a class="reordenar_padrao" href="#" rel="index_modelo|dtatualizacao">'.exibeFlagReordenacao('dtatualizacao').'Data de Atualização</a></th>
        <th>Opções</th>
      </tr>
    </thead>
    <tbody>
	';
	for($i=0;$i<sizeof($results);$i++){		
		
		if(isset($results[$i]["idmodelo"]) && isset($results[$i]["nome"]) && isset($results[$i]["dtcriacao"]) && isset($results[$i]["link"])){

			$id=$results[$i]["idmodelo"];
			$nome=$results[$i]["nome"];
			$dtcriacao=$results[$i]["dtcriacao"];
			$dtatualizacao=$results[$i]["dtatualizacao"];
			$link=$results[$i]["link"];
			echo "
			<tr id=\"id_".$id."\">
			<th scope=\"row\">".$nome."</th>
			<td>".exibeData($dtcriacao)."</td>
			<td>".exibeData($dtatualizacao)."</td>
			<td class=\"noprint\"><nobr>
			<a href=\"download.php?id=$id\"><button class=\"btn btn-primary \" aria-label=\"Left Align\" type=\"button\" title=\"Baixar modelo\"> <span class=\"glyphicon glyphicon-download-alt\" aria-hidden=\"true\"></span> FAZER DOWNLOAD </button></a>
			";
			if(verificaFuncaoUsuario(FUNCAO_MODELO_EDIT)){
				echo " &nbsp;<a href=\"edit_modelo.php?p=$id&r=index_modelo.php\"><button class=\"btn btn-success \" aria-label=\"Left Align\" type=\"button\" title=\"Editar modelo\"> <span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span></button></a>";
			}
			if(verificaFuncaoUsuario(FUNCAO_MODELO_DEL)){
				echo " &nbsp;<button class=\"btn btn-warning del_modelo\" aria-label=\"Left Align\" type=\"button\" title=\"Remover modelo\"> <span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span></button>";
			}

			echo "</nobr></td></tr>";

		}

	}
	echo '</tbody></table>';

	//PAGINACAO - Agora vamos criar os botões "Anterior e Próximo" ao final da página
	include_once("../paginacao.php");

}else{
	echo 'Nenhum Modelo encontrado!<br><br>';
}

echo '</div></div>';

?>

	<div class="form-group">
	  <div class="col-lg-10">
	    <button type="reset" id="cancelar" class="btn btn-default back">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
	  </div>
	</div>
<?php
echo "<br>";
include_once("../menu_rodape.php");
?>
