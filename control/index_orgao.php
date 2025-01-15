<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");
require_once("../menu_topo.php");


	//SUBMENU DE AÇÕES
	echo "	<div id=\"conteudo_borda\" class=\"noprint\"><div class=\"well submenu\">
				<button class=\"btn btn-info\" value=\"add_orgao.php\" title=\"Adicionar Órgão Externo\" type=\"button\" aria-label=\"Left Align\">		
					<span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> <strong>ÓRGÃO EXTERNO</strong>
				</button>
			</div></div>";
	//FIM SUBMENU DE AÇÕES

	//exibe mensagens conforme o passado na URL:
	if(isset($_GET["s"]) && !empty($_GET["s"])){
		echo "<script>";

		//ADD
		if($_GET["s"]==100){
			echo "showMsgSucesso('Órgão cadastrado com sucesso', '');";
		}
		if($_GET["s"]==109){
			echo "showMsgErro('Órgão não cadastrado', 'Os dados fornecidos estavam inválidos, tente novamente.');";	
		}
		if($_GET["s"]==110){
			echo "showMsgErro('Órgão cadastrado com erros', 'O histórico não pôde ser salvo.');";	
		}

		//ADD E EDIT
		if($_GET["s"]==119 && isset($_GET["n"])){
			echo "showMsgErro('Erro', 'Um órgão chamado <strong>\"".$_GET["n"]."\"</strong> já existe, por favor, utilize outro nome');";
		}

		//EDIT
		if($_GET["s"]==200){
			echo "showMsgSucesso('Órgão atualizado com sucesso', '');";
		}
		if($_GET["s"]==209){
			echo "showMsgErro('Órgão não atualizado', 'Os dados fornecidos estavam inválidos, tente novamente.');";	
		}
		if($_GET["s"]==210){
			echo "showMsgErro('Órgão atualizado com erros', 'O histórico não pôde ser salvo.');";	
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
	require_once('../dao/OrgaoDAO.php');
	require_once('../model/Orgao.php');

	echo "<div id=\"conteudo_borda\">
	<div  id=\"conteudo\"><h5 class='onlyprint'>Listagem de Órgãos</h5>";

	//Paginação:
	if(isset($_GET['pagina']) && $_GET['pagina']>0){
		$pc = $_GET['pagina'];
	}else{
		$pc = "1";
	}
	$paginacao_inicio = $pc - 1; 
	$paginacao_inicio = $paginacao_inicio * APP_MAX_PAGE_ROWS;

	// Instanciar o DAO e retornar dados do banco
	$OrgaoDAO = new OrgaoDAO();
	if(isset($_GET["order"]) && ($_GET["order"]=="nome")&&($_GET["ascdesc"]=="ASC" || $_GET["ascdesc"]=="DESC") ){
		$dados = $OrgaoDAO->index($paginacao_inicio,$_GET["order"],$_GET["ascdesc"]);
	}else{
		$dados = $OrgaoDAO->index($paginacao_inicio,NULL,NULL);
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
	        <th><a class="reordenar_orgao" href="#" rel="nome">Nome</a></th>';
			if(verificaFuncaoUsuario($GLOBALS["f_orgao_edit"]) || verificaFuncaoUsuario($GLOBALS["f_orgao_del"])){
				echo '	<th class="noprint">Ações </th>';
			}
	echo '	</tr>
		  </thead>
		  <tbody>';
		$numregistros=0;
		for($i=0;$i<sizeof($dados);$i++){
			if(isset($dados[$i]["nome"])){
				$id=$dados[$i]["idorgao"];
				$nome=$dados[$i]["nome"];
				$numregistros++;
				echo "
				<tr id=\"id_".$id."\">
				<th scope=\"row\">".$nome."</th>";

				if(verificaFuncaoUsuario($GLOBALS["f_orgao_edit"]) || verificaFuncaoUsuario($GLOBALS["f_orgao_del"])){
					echo "<td class=\"noprint\"><nobr>";
					//verifica as permissóes do usuário
					if(verificaFuncaoUsuario($GLOBALS["f_orgao_edit"])){
						echo "&nbsp;<button title=\"Editar informações do Órgão\" value=\"edit_orgao.php?p=".$id."\" class=\"btn btn-success\" aria-label=\"Left Align\" type=\"button\"> <span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span> </button> ";
					}
					if(verificaFuncaoUsuario($GLOBALS["f_orgao_del"])){
						echo "&nbsp;<button title=\"Remover Órgão\" class=\"btn btn-warning del_orgao\" aria-label=\"Left Align\" type=\"button\"> <span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span> </button> ";
					}			
					echo "</nobr></td>";
				}

				echo "</tr>";

			}
		}

		if($numregistros>0){
			echo '</tbody></table>';
		}else{
			echo 'Nenhum órgão encontrado!<br><br>';
		}

		//PAGINACAO - Agora vamos criar os botões "Anterior e Próximo" ao final da página
		include_once("../paginacao.php");

	}else{
		echo 'Nenhum órgão encontrado!<br><br>';
	}

	echo "</div></div>";

	include_once("../menu_rodape.php");

?>