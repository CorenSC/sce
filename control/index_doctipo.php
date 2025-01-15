<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");
require_once("../menu_topo.php");

if(verificaFuncaoUsuario(FUNCAO_DOCUMENTOTIPO_ADD)!==false){

	//carrega as bibliotecas para recuperar informações do BD
	//conecta no banco e instacia uma conexão com o Registry
	require_once("../conexao.php");
	require_once("../model/Registry.php");
	// Armazenar essa instância (conexão) no Registry - conecta uma só vez
	$registry = Registry::getInstance();
	$registry->set('Connection', $myBD);
	//carrega DAO's das chaves estrangeiras
	require_once('../dao/DocumentoTipoDAO.php');
	require_once('../model/DocumentoTipo.php');


	//SUBMENU DE AÇÕES
	require_once('submenu_doctipo.php');
	//FIM SUBMENU DE AÇÕES


	echo "<div id=\"conteudo_borda\">
	<div  id=\"conteudo\"><h5 class='onlyprint'>Listagem de tipos de documento</h5>";

	//Paginação:
	if(isset($_GET['pagina']) && $_GET['pagina']>0){
		$pc = $_GET['pagina'];
	}else{
		$pc = "1";
	}
	$paginacao_inicio = $pc - 1; 
	$paginacao_inicio = $paginacao_inicio * APP_MAX_PAGE_ROWS;

	// Instanciar o DAO e retornar dados do banco
	$DocumentoTipoDAO = new DocumentoTipoDAO();
	if(isset($_GET["order"]) && ($_GET["order"]=="nome")&&($_GET["ascdesc"]=="ASC" || $_GET["ascdesc"]=="DESC") ){
		$dados = $DocumentoTipoDAO->index($paginacao_inicio,$_GET["order"],$_GET["ascdesc"]);
	}else{
		$dados = $DocumentoTipoDAO->index($paginacao_inicio,NULL,NULL);
	}

	//Paginação:
	$tr = $dados[0]["paginacao_numlinhas"]; // verifica o número total de registros 
	$tp = $tr / APP_MAX_PAGE_ROWS; // verifica o número total de páginas
	$tp2 = ceil($tp); //arredonda para cima o número de páginas, ao inves de ser 1.6 é 2.

	//se o total de linhas for maior que zero:
	if($tr>0){ 

			echo '
		<table class="table table-condensed table-responsive table-hover">
	    <thead>
	      <tr>
	        <th><a class="reordenar_padrao" href="#" rel="index_doctipo|nome">'.exibeFlagReordenacao('nome').'Nome</a></th>';
			if(verificaFuncaoUsuario(FUNCAO_DOCUMENTOTIPO_EDIT) || verificaFuncaoUsuario(FUNCAO_DOCUMENTOTIPO_DEL)){
				echo '	<th class="noprint">Ações </th>';
			}
	echo '	</tr>
		  </thead>
		  <tbody>';
		$numregistros=0;
		for($i=0;$i<sizeof($dados);$i++){
			if(isset($dados[$i]["nome"])){
				$id=$dados[$i]["iddocumentotipo"];
				$nome=$dados[$i]["nome"];
				$numregistros++;
				echo "
				<tr id=\"id_".$id."\">
				<th scope=\"row\">".$nome."</th>";

				if(verificaFuncaoUsuario(FUNCAO_DOCUMENTOTIPO_EDIT) || verificaFuncaoUsuario(FUNCAO_DOCUMENTOTIPO_DEL)){
					echo "<td class=\"noprint\"><nobr>";
					//verifica as permissóes do usuário
					if(verificaFuncaoUsuario(FUNCAO_DOCUMENTOTIPO_EDIT)){
						echo "&nbsp;<button title=\"Editar informações do tipo de documento\" value=\"edit_doctipo.php?p=".$id."\" class=\"btn btn-success\" aria-label=\"Left Align\" type=\"button\"> <span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span> </button> ";
					}
					if(verificaFuncaoUsuario(FUNCAO_DOCUMENTOTIPO_DEL)){
						echo "&nbsp;<button title=\"Remover tipo de documento\" class=\"btn btn-warning del_documentotipo\" aria-label=\"Left Align\" type=\"button\"> <span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span> </button> ";
					}			
					echo "</nobr></td>";
				}

				echo "</tr>";

			}
		}

		if($numregistros>0){
			echo '</tbody></table>';
		}else{
			echo 'Nenhum tipo de documento encontrado!<br><br>';
		}

		//PAGINACAO - Agora vamos criar os botões "Anterior e Próximo" ao final da página
		include_once("../paginacao.php");

	}else{
		echo 'Nenhum tipo de documento encontrado!<br><br>';
	}

	echo "</div></div>";

	include_once("../menu_rodape.php");

}else{
	enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou dados inválidos");
	echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
	exit();
}
?>