<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");
require_once("../menu_topo.php");

if(verificaFuncaoUsuario(FUNCAO_SUBSECAO_ADD)){

	//SUBMENU DE AÇÕES SUBSEÇÃO
	require_once('submenu_subsecao.php');
	//FIM SUBMENU DE AÇÕES SUBSEÇÃO
	

	//carrega as bibliotecas para recuperar informações do BD
	//conecta no banco e instacia uma conexão com o Registry
	require_once("../conexao.php");
	require_once("../model/Registry.php");
	// Armazenar essa instância (conexão) no Registry - conecta uma só vez
	$registry = Registry::getInstance();
	$registry->set('Connection', $myBD);
	//carrega DAO's das chaves estrangeiras
	require_once('../dao/SubsecaoDAO.php');
	require_once('../model/Subsecao.php');

	echo "<div id=\"conteudo_borda\">
	<div  id=\"conteudo\"><h5 class='onlyprint'>Listagem de Subseções</h5>";

	//Paginação:
	if(isset($_GET['pagina']) && $_GET['pagina']>0){
		$pc = $_GET['pagina'];
	}else{
		$pc = "1";
	}
	$paginacao_inicio = $pc - 1; 
	$paginacao_inicio = $paginacao_inicio * APP_MAX_PAGE_ROWS;

	// Instanciar o DAO e retornar dados do banco
	$SubsecaoDAO = new SubsecaoDAO();
	if(isset($_GET["order"]) && ($_GET["order"]=="nome" || $_GET["order"]=="nomecidade")&&($_GET["ascdesc"]=="ASC" || $_GET["ascdesc"]=="DESC") ){
		$dados = $SubsecaoDAO->index($paginacao_inicio,$_GET["order"],$_GET["ascdesc"]);
	}else{
		$dados = $SubsecaoDAO->index($paginacao_inicio,NULL,NULL);
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
	        <th><a class="reordenar_padrao" href="#" rel="index_subsecao|nome">'.exibeFlagReordenacao('nome').'Nome</a></th>
	        <th><a class="reordenar_padrao" href="#" rel="index_subsecao|nomecidade">'.exibeFlagReordenacao('nomecidade').'Cidade</a></th>';
			if(verificaFuncaoUsuario(FUNCAO_SUBSECAO_EDIT) || verificaFuncaoUsuario(FUNCAO_SUBSECAO_DEL)){
				echo '	<th class="noprint">Ações </th>';
			}
	echo '	</tr>
		  </thead>
		  <tbody>';
		$numregistros=0;
		for($i=0;$i<sizeof($dados);$i++){
			if(isset($dados[$i]["nome"])){
				$id=$dados[$i]["idsubsecao"];
				$nome=$dados[$i]["nome"];
				$cidade=$dados[$i]["nomecidade"];
				$numregistros++;
				echo "
				<tr id=\"id_".$id."\">
				<th scope=\"row\">".$nome."</th>
				<td scope=\"row\">".$cidade."</td>";

				if(verificaFuncaoUsuario(FUNCAO_SUBSECAO_EDIT) || verificaFuncaoUsuario(FUNCAO_SUBSECAO_DEL)){
					echo "<td class=\"noprint\"><nobr>";
					//verifica as permissóes do usuário
					if(verificaFuncaoUsuario(FUNCAO_SUBSECAO_EDIT)){
						echo "&nbsp;<button title=\"Editar informações da Subseção\" value=\"edit_subsecao.php?p=".$id."\" class=\"btn btn-success\" aria-label=\"Left Align\" type=\"button\"> <span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span> </button> ";
					}
					if(verificaFuncaoUsuario(FUNCAO_SUBSECAO_DEL)){
						echo "&nbsp;<button title=\"Remover Subseção\" class=\"btn btn-warning del_subsecao\" aria-label=\"Left Align\" type=\"button\"> <span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span> </button> ";
					}			
					echo "</nobr></td>";
				}

				echo "</tr>";

			}
		}

		if($numregistros>0){
			echo '</tbody></table>';
		}else{
			echo 'Nenhuma Subseção encontrada!<br><br>';
		}

		//PAGINACAO - Agora vamos criar os botões "Anterior e Próximo" ao final da página
		include_once("../paginacao.php");

	}else{
		echo 'Nenhuma Subseção encontrada!<br><br>';
	}

	echo "</div></div>";

	include_once("../menu_rodape.php");

}
?>