<?php
require_once("../config.php");
require_once("../bin/functions.php");
require_once("../bin/errors.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");
require_once("../menu_topo.php");

//SUBMENU DE AÇÕES
echo "<div id=\"conteudo_borda\" class=\"noprint\"><div class=\"well submenu\">";
if(isset($_GET) && !empty($_GET)){
	$disabled="";
}else{
	$disabled=" disabled ";
}
echo "<button class=\"btn btn-primary ".$disabled."\" value=\"search_pro.php\" title=\"Buscar PAD\" type=\"button\" aria-label=\"Left Align\">		
			<span class=\"glyphicon glyphicon-filter\" aria-hidden=\"true\"></span> <strong>BUSCAR PAD</strong>
		</button>";
if(verificaFuncaoUsuario(FUNCAO_PROCESSO_ADD)!==false){
 	echo "&nbsp;&nbsp;
 			<button class=\"btn btn-info\" value=\"add_pro.php\" title=\"Adicionar PAD\" type=\"button\" aria-label=\"Left Align\">		
		  		<span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> <strong>ADICIONAR PAD</strong>
		  	</button>";
}
echo "</div></div>";
//FIM SUBMENU DE AÇÕES

//busca na pagina index_pro
if(isset($_GET) && !empty($_GET)){
	
	//se for setado um ano, atribui, se não, permite consulta a todos os anos
	if(isset($_GET["ano"]) && !empty($_GET["ano"])){
		$ano=$_GET["ano"];
	}else{
		$ano="0";
	}
	//se for setado um número, atribui, se não, permite consulta a todos os números
	if(isset($_GET["numero"]) && !empty($_GET["numero"])){
		$numero=$_GET["numero"];
	}else{
		$numero="0";
	}

	//armazena na variavel os dois valores separados pelo "quebrador de linha" (atualmente os caracteres @@@)
	$busca = $ano.APP_LINE_BREAK.$numero;

	/* ANTERIOR

	//recebe o tipo da pesquisa (numero OU denunciado)
	$idtipo=$_GET["idtipo"];
	//numero do processo
	if(isset($_GET["numero"]) && !empty($_GET["numero"])){
		$numero=$_GET["numero"];
	}else{
		$numero='%';//procura por qualquer numero de processo
	}
	//categoria do denunciado
	if(isset($_GET["categoria"]) && !empty($_GET["categoria"])){
		$categoria=$_GET["categoria"];
	}else{
		$categoria='%';//procura por qualquer categoria
	}
	//inscricao do denunciado
	if(isset($_GET["inscricao"]) && !empty($_GET["inscricao"])){
		$inscricao=$_GET["inscricao"];
	}else{
		$inscricao='%';//procura por qualquer inscricao
	}
	//cria variável que irá conduzir a busca em cada caso (numero ou denunciado)
	if($idtipo=="denunciado"){
		$busca = $categoria.'----------'.$inscricao;//caracteres ---------- permitem o explode dentro do ProcessoDAO.
	}else{
		$busca = $numero;
	}
	*/

	//exibe mensagens conforme o passado na URL:
	if(!empty($_GET["a"]) || !empty($_GET["s"])){
		echo "<script>";

		//mensagens da pagina control/edit.php
		if($_GET["s"]==3){
			echo "showMsgErro('Acesso negado','O processo requisitado foi removido');";
		}
		if($_GET["s"]==4){
			echo "showMsgErro('Acesso negado','O processo requisitado não permite sua visualização');";
		}
		if($_GET["s"]==5){
			echo "showMsgSucesso('Processo excluído com sucesso','');";
		}
		if($_GET["s"]==6){
			echo "showMsgErro('O Processo não pôde ser excluído','Tente novamente mais tarde');";
		}
		if($_GET["s"]==7){
			echo "showMsgErro('Acesso negado','Você não pode editar este processo');";
		}
		if($_GET["s"]==8){
			echo "showMsgErro('Erro','A ação requisitada não pôde ser concluída, tente novamente mais tarde');";
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
	require_once('../dao/PadDAO.php');
	require_once('../model/Pad.php');
	require_once('../model/Usuario.php');



	//Paginação:
	if(isset($_GET['pagina']) && $_GET['pagina']>0){
		$pc = $_GET['pagina'];
	}else{
		$pc = "1";
	}
	$paginacao_inicio = $pc - 1; 
	$paginacao_inicio = $paginacao_inicio * APP_MAX_PAGE_ROWS;

	//status
	if(isset($_GET["status"])){
		$status=$_GET["status"];
	}else{
		$status=NULL;
	}
	//origem
	if(isset($_GET["origem"])){
		$origem=$_GET["origem"];
	}else{
		$origem=NULL;
	}

	// Instanciar o DAO e retornar dados do banco
	$PadDAO = new PadDAO();
	//$results = $processoDAO->index(); 
	if(isset($_GET["order"]) && ($_GET["order"]=="estadoatual" || $_GET["order"]=="numero" || $_GET["order"]=="ano" || $_GET["order"]=="setor" || $_GET["order"]=="prazo")&&($_GET["ascdesc"]=="ASC" || $_GET["ascdesc"]=="DESC") ){
		$results = $PadDAO->indexSearch($paginacao_inicio,$busca,$_GET["order"],$_GET["ascdesc"]);
	}else{
		$results = $PadDAO->indexSearch($paginacao_inicio,$busca);
	}


	//Paginação:
	$tr = $results[0]["paginacao_numlinhas"]; // verifica o número total de registros 
	$tp = $tr / APP_MAX_PAGE_ROWS; // verifica o número total de páginas
	$tp2 = ceil($tp); //arredonda para cima o número de páginas, ao inves de ser 1.6 é 2.
	//se o usuário digitar uma página maior que a última página de conteúdo, redireciona para a tela inicial da search_pro
	if(isset($_GET["pagina"]) && $_GET["pagina"]>$tp2){
		echo '<script>location.href="search_pro.php";</script>';
		exit();
	}

	echo "<div id=\"conteudo_borda\"><div  id=\"conteudo\"><h5 class='onlyprint'>Listagem de PAD's</h5>";
	//se o total de linhas for maior que zero:
	if($tr>0){ 

echo '
<table class="table table-condensed table-responsive table-hover">
    <thead>
      <tr>
        <th><a class="reordenar_searchpro" href="#" rel="ano'.APP_LINE_BREAK.$ano.APP_LINE_BREAK.$numero.'">Ano</a></th>
        <th><a class="reordenar_searchpro" href="#" rel="numero'.APP_LINE_BREAK.$ano.APP_LINE_BREAK.$numero.'">Número</a></th>
        <th><a class="reordenar_searchpro" href="#" rel="setor'.APP_LINE_BREAK.$ano.APP_LINE_BREAK.$numero.'">Setor</a>';
echo '	</th>
		<th><a class="reordenar_searchpro" href="#" rel="estadoatual'.APP_LINE_BREAK.$ano.APP_LINE_BREAK.$numero.'">Status</a>';
echo '	</th>
		<th><a class="reordenar_searchpro" href="#" rel="prazo'.APP_LINE_BREAK.$ano.APP_LINE_BREAK.$numero.'">Prazo</a></th>
		<th class="noprint">Ações </th>
      </tr>
    </thead>
    <tbody>
	';
	$numprocessos=0;
	for($i=0;$i<sizeof($results);$i++){		
		
		if(isset($results[$i]["idpad"]) && isset($results[$i]["ano"]) && isset($results[$i]["numero"]) && isset($results[$i]["nomeestadoatual"]) && isset($results[$i]["nomesetor"]) ){
			if(verificaProcessoUsuario($results[$i]["idpad"])){
				$pro_id=$results[$i]["idpad"];
				$pro_ano=$results[$i]["ano"];
				$pro_numero=$results[$i]["numero"];
				$pro_setor=$results[$i]["nomesetor"];
				$pro_estadoatual=$results[$i]["nomeestadoatual"];		
				$pro_idstatus=$results[$i]["idestadoatual"];			
				$pro_prazo=$results[$i]["prazo"];
				$numprocessos++;
				echo "
				<tr id=\"processo_".$pro_id."\">
				<th scope=\"row\">".$pro_ano."</th>
				<td>".$pro_numero."</td>
				<td>".$pro_setor."</td>
				<td>".$pro_estadoatual."</td>				
				<td>";

				//calcula se o prazo está atrasado ou não e exibe na tela
				if($pro_prazo<date("Ymd") && $pro_prazo!=0 && $pro_idstatus!=$GLOBALS["estadoatual_idarquivado"]){
					//atrasado
					echo "<strong class=\"alert-danger\">".exibeData($pro_prazo)."</strong>";
				}else{
					if($pro_prazo==0){
						//sem prazo
						echo "-";
					}else{
						//no prazo
						echo "<strong class=\"alert-success\">".exibeData($pro_prazo)."</strong>";
					}
				}

				echo "</td>
				<td class=\"noprint\">

				<button class=\"btn btn-primary documento_index\" aria-label=\"Left Align\" type=\"button\"  title=\"Visualizar processo\"> <span class=\"glyphicon glyphicon-zoom-in\" aria-hidden=\"true\"></span> </button> ";
				
				//verifica as permissóes do usuário
				if(verificaFuncaoUsuario(FUNCAO_PROCESSO_EDIT)){
					echo "&nbsp;<button class=\"btn btn-success edit_processo\" id=\"processo_".$pro_id."\" aria-label=\"Left Align\" type=\"button\" title=\"Editar processo\"> <span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span> </button> ";
				}
				if(verificaFuncaoUsuario(FUNCAO_PROCESSO_DEL)){
					echo "&nbsp;<button class=\"btn btn-warning processo_del\" id=\"processo_".$pro_id."\" aria-label=\"Left Align\" type=\"button\" title=\"Excluir processo\"> <span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span> </button> ";
				}
				
				echo "</td></tr>";
			}
		}
	}

	if($numprocessos>0){
		echo '</tbody></table>';
	}else{
		echo 'Nenhum PAD encontrado!<br><br>';
	}

}else{
	echo 'Nenhum PAD encontrado!<br><br>';
}

	//Agora vamos criar os botões "Anterior e Próximo" ao final da página
	include_once("../paginacao.php");
echo '</div></div>
		<div class="form-group">
			<div class="col-lg-10">
				<button type="reset" id="cancelar" class="btn btn-default back">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
			</div>
		</div>';

}else{
	echo "<div id=\"conteudo_borda\"><div  id=\"conteudo\">";
	echo '
			<form id="search_pro" name="search_pro" class="form-horizontal"  method="get" action="search_pro.php" >';
	?>

		<!--
		<div class="form-group">
          <label class="col-sm-10 control-label">Tipo de Busca</label>
            <div class="col-lg-5">
              <select id="idtipo"  name="idtipo" class="form-control" onchange="showCampoViaSelect(this.value);">
              	<option value="-1">Selecione</option>
				<option value="numero">Por Número do Processo</option>
				<option value="denunciado">Por Denunciado</option>
              </select>
            </div>
        </div>
        <div class="form-group campoviaselect"  id="campoviaselectnumero">
        	<div class="col-lg-10">
	            <label class="control-label">Número do Processo / Denúncia</label>
                <input class="form-control" type="text" id="numero" name="numero" style="width:200px;">
        	</div>
        </div>
        -->


		<div class="form-group">
			<div class="col-lg-3">
				<label for="ano" class="control-label">Ano</label>	        	
			    <input type="text" class="form-control" id="ano" name="ano" placeholder="Ex.: 2015" maxlength="<?php echo $GLOBALS["pad_ano_size"]; ?>">
			    <span id="helpBlock" class="help-block">Deixe o campo em branco se desejar</span>
			</div>
		</div>
		<div class="form-group">
			<div class="col-lg-3">
				<label for="numero" class="control-label">Número</label>	        	
			    <input type="text" class="form-control" id="numero" name="numero" placeholder="Ex.: 008" maxlength="<?php echo $GLOBALS["pad_numero_size"]; ?>">
			    <span id="helpBlock" class="help-block">Deixe o campo em branco se desejar</span>
			</div>
		</div>

        <div class="form-group" style="margin-top:30px">
          <div class="col-lg-10">
            <button type="reset" id="cancelar" class="btn btn-default index_pro.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
            <button type="submit" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Consultar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
          </div>
        </div>

<?php
	echo '	</form>';
	echo "</div></div>";
}

include_once("../menu_rodape.php"); 
?>