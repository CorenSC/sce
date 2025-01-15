<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");
require_once("../menu_topo.php");

if(verificaFuncaoUsuario($GLOBALS["f_irregularidade_add"])!==false){

	//SUBMENU DE AÇÕES
	echo "	<div id=\"conteudo_borda\" class=\"noprint\"><div class=\"well submenu\">
				<button class=\"btn btn-info\" value=\"add_irregularidade.php\" title=\"Adicionar Irregularidade \" type=\"button\" aria-label=\"Left Align\">		
					<span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> <strong> IRREGULARIDADE</strong>
				</button>
			</div></div>";
	//FIM SUBMENU DE AÇÕES

	//exibe mensagens conforme o passado na URL:
	if(isset($_GET["s"]) && !empty($_GET["s"])){
		echo "<script>";

		//ADD E EDIT
		if($_GET["s"]==119 && isset($_GET["n"])){
			echo "showMsgErro('Erro', 'Uma Irregularidade chamada <strong>\"".$_GET["n"]."\"</strong> já existe, por favor, utilize outro nome');";	
		}

		//ADD
		if($_GET["s"]==100){
			echo "showMsgSucesso('Irregularidade cadastrada com sucesso', '');";
		}
		if($_GET["s"]==109){
			echo "showMsgErro('Irregularidade não cadastrada', 'Os dados fornecidos estavam inválidos, tente novamente.');";	
		}
		if($_GET["s"]==110){
			echo "showMsgErro('Irregularidade cadastrada com erros', 'O histórico não pôde ser salvo.');";	
		}

		//EDIT
		if($_GET["s"]==200){
			echo "showMsgSucesso('Irregularidade atualizada com sucesso', '');";
		}
		if($_GET["s"]==209){
			echo "showMsgErro('Irregularidade não atualizada', 'Os dados fornecidos estavam inválidos, tente novamente.');";	
		}
		if($_GET["s"]==210){
			echo "showMsgErro('Irregularidade atualizada com erros', 'O histórico não pôde ser salvo.');";	
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
	require_once('../dao/IrregularidadeDAO.php');
	require_once('../model/Irregularidade.php');

	echo "<div id=\"conteudo_borda\">
	<div  id=\"conteudo\"><h5 class='onlyprint'>Listagem de Irregularidades</h5>";

	//Paginação:
	if(isset($_GET['pagina']) && $_GET['pagina']>0){
		$pc = $_GET['pagina'];
	}else{
		$pc = "1";
	}
	$paginacao_inicio = $pc - 1; 
	$paginacao_inicio = $paginacao_inicio * APP_MAX_PAGE_ROWS;

	// Instanciar o DAO e retornar dados do banco
	$IrregularidadeDAO = new IrregularidadeDAO();
	if(isset($_GET["order"]) && ($_GET["order"]=="nome")&&($_GET["ascdesc"]=="ASC" || $_GET["ascdesc"]=="DESC") ){
		$dados = $IrregularidadeDAO->index($paginacao_inicio,$_GET["order"],$_GET["ascdesc"]);
	}else{
		$dados = $IrregularidadeDAO->index($paginacao_inicio,NULL,NULL);
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
	        <th><a class="reordenar_irregularidade" href="#" rel="nome">Nome</a></th>';
			if(verificaFuncaoUsuario($GLOBALS["f_irregularidade_edit"]) || verificaFuncaoUsuario($GLOBALS["f_irregularidade_del"])){
				echo '	<th class="noprint">Ações </th>';
			}
	echo '	</tr>
		  </thead>
		  <tbody>';
		$numregistros=0;
		for($i=0;$i<sizeof($dados);$i++){
			if(isset($dados[$i]["nome"])){
				$id=$dados[$i]["idirregularidade"];
				$nome=$dados[$i]["nome"];
				$numregistros++;
				echo "
				<tr id=\"id_".$id."\">
				<th scope=\"row\">".exibeTexto($nome,93)."</th>";

				if(verificaFuncaoUsuario($GLOBALS["f_irregularidade_edit"]) || verificaFuncaoUsuario($GLOBALS["f_irregularidade_del"])){
					echo "<td class=\"noprint\"><nobr>";
					//verifica as permissóes do usuário
					if(verificaFuncaoUsuario($GLOBALS["f_irregularidade_edit"])){
						echo "&nbsp;<button title=\"Editar informações do Irregularidade \" value=\"edit_irregularidade.php?p=".$id."\" class=\"btn btn-success\" aria-label=\"Left Align\" type=\"button\"> <span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span> </button> ";
					}
					if(verificaFuncaoUsuario($GLOBALS["f_irregularidade_del"])){
						echo "&nbsp;<button title=\"Remover Irregularidade \" class=\"btn btn-warning del_irregularidade\" aria-label=\"Left Align\" type=\"button\"> <span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span> </button> ";
					}			
					echo "</nobr></td>";
				}

				echo "</tr>";

			}
		}

		if($numregistros>0){
			echo '</tbody></table>';
		}else{
			echo 'Nenhuma Irregularidade encontrada!<br><br>';
		}

		//PAGINACAO - Agora vamos criar os botões "Anterior e Próximo" ao final da página
		include_once("../paginacao.php");

	}else{
		echo 'Nenhuma Irregularidade encontrada!<br><br>';
	}

	echo "</div></div>";

	include_once("../menu_rodape.php");

}
?>