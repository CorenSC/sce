<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");
require_once("../menu_topo.php");

if(verificaFuncaoUsuario(FUNCAO_ETAPA_ADD)!==false){

	//SUBMENU DE AÇÕES
	require_once('submenu_etapa.php');
	//FIM SUBMENU DE AÇÕES

	//carrega as bibliotecas para recuperar informações do BD
	//conecta no banco e instacia uma conexão com o Registry
	require_once("../conexao.php");
	require_once("../model/Registry.php");
	// Armazenar essa instância (conexão) no Registry - conecta uma só vez
	$registry = Registry::getInstance();
	$registry->set('Connection', $myBD);
	//carrega DAO's das chaves estrangeiras
	require_once('../dao/EtapaDAO.php');

	echo "<div id=\"conteudo_borda\">
	<div  id=\"conteudo\"><h5 class='onlyprint'>Listagem de Etapas</h5>";

	//Paginação:
	if(isset($_GET['pagina']) && $_GET['pagina']>0){
		$pc = $_GET['pagina'];
	}else{
		$pc = "1";
	}
	$paginacao_inicio = $pc - 1; 
	$paginacao_inicio = $paginacao_inicio * APP_MAX_PAGE_ROWS;

	// Instanciar o DAO e retornar dados do banco
	$EtapaDAO = new EtapaDAO();
	if(isset($_GET["order"]) && ($_GET["order"]=="nome" || $_GET["order"]=="modo"  || $_GET["order"]=="fluxo" || $_GET["order"]=="ordem")&&($_GET["ascdesc"]=="ASC" || $_GET["ascdesc"]=="DESC") ){
		$dados = $EtapaDAO->index($paginacao_inicio,$_GET["order"],$_GET["ascdesc"]);
	}else{
		$dados = $EtapaDAO->index($paginacao_inicio,NULL,NULL);
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
	        <th><a class="reordenar_padrao" href="#" rel="index_etapa|ordem">'.exibeFlagReordenacao('ordem').'Ordem</a></th>
	        <th><a class="reordenar_padrao" href="#" rel="index_etapa|fluxo">'.exibeFlagReordenacao('fluxo').'Fluxo</a></th>
	        <th><a class="reordenar_padrao" href="#" rel="index_etapa|modo">'.exibeFlagReordenacao('modo').'Modo</a></th>
	        <th><a class="reordenar_padrao" href="#" rel="index_etapa|nome">'.exibeFlagReordenacao('nome').'Nome</a></th>';
			if(verificaFuncaoUsuario(FUNCAO_ETAPA_EDIT) || verificaFuncaoUsuario(FUNCAO_ETAPA_DEL)){
				echo '	<th class="noprint">Ações </th>';
			}
	echo '	</tr>
		  </thead>
		  <tbody>';
		$numregistros=0;
		for($i=0;$i<sizeof($dados);$i++){
			if(isset($dados[$i]["nome"])){
				$id=$dados[$i]["idetapa"];
				$nome=$dados[$i]["nome"];
				$ordem=$dados[$i]["ordem"];
				$modo=$dados[$i]["modo"];
				//fluxo = 0 (principal) && fluxo = 1 (alternativo)
				if($dados[$i]["fluxo"] == 1){
					$fluxo = '<button type="button" class="btn btn-danger" disabled="disabled">Secundário</button>';
				}else{
					$fluxo = '<button type="button" class="btn btn-primary" disabled="disabled">Principal</button>';
				}
				//modo = 0 (normal) = 1 (c/eleições) 
            	//modo = 2 (s/eleições) = 3 (Não Militar)
				if($dados[$i]["modo"] == 0){
					$modo = '<button type="button" class="btn btn-default" disabled="disabled">Normal</button>';
				}elseif($dados[$i]["modo"] == 1){
					$modo = '<button type="button" class="btn btn-default" disabled="disabled">Com Eleições</button>';
				}elseif($dados[$i]["modo"] == 2){
					$modo = '<button type="button" class="btn btn-default" disabled="disabled">Sem Eleições</button>';
				}else{
					$modo = '<button type="button" class="btn btn-default" disabled="disabled">Não Militar</button>';
				}
				$numregistros++;
				echo "
				<tr id=\"id_".$id."\">
				<th scope=\"row\">".$ordem."</th>
				<th scope=\"row\">".$fluxo."</th>
				<th scope=\"row\">".$modo."</th>
				<th scope=\"row\">".exibeTexto($nome,145)."</th>";

				if(verificaFuncaoUsuario(FUNCAO_ETAPA_EDIT) || verificaFuncaoUsuario(FUNCAO_ETAPA_DEL)){
					echo "<td class=\"noprint\"><nobr>";
					//verifica as permissóes do usuário
					if(verificaFuncaoUsuario(FUNCAO_ETAPA_EDIT) && $id!=ID_LAST_ETAPA){
						echo "&nbsp;<button title=\"Editar informações da Etapa \" value=\"edit_etapa.php?p=".$id."\" class=\"btn btn-success\" aria-label=\"Left Align\" type=\"button\"> <span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span> </button> ";
					}
					if(verificaFuncaoUsuario(FUNCAO_ETAPA_DEL) && $id!=ID_LAST_ETAPA){
						echo "&nbsp;<button title=\"Remover Etapa \" class=\"btn btn-warning del_etapa\" aria-label=\"Left Align\" type=\"button\"> <span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span> </button> ";
					}			
					echo "</nobr></td>";
				}

				echo "</tr>";

			}
		}

		if($numregistros>0){
			echo '</tbody></table>';
		}else{
			echo 'Nenhuma etapa encontrada!<br><br>';
		}

		//PAGINACAO - Agora vamos criar os botões "Anterior e Próximo" ao final da página
		include_once("../paginacao.php");

	}else{
		echo 'Nenhuma etapa encontrada!<br><br>';
	}

	echo "</div></div>";

	include_once("../menu_rodape.php");

}
?>