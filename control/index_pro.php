<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../login_verifica.php");
require_once("../bin/js-css.php");
require_once("../menu_topo.php");

//carrega as bibliotecas para recuperar informações do BD
//conecta no banco e instacia uma conexão com o Registry
require_once("../conexao.php");
require_once("../model/Registry.php");
// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);
//carrega DAO's das chaves estrangeiras
require_once('../dao/ProcessoDAO.php');
require_once('../dao/ResponsavelDAO.php');
require_once('../model/Processo.php');
require_once('../model/Usuario.php');

//verifica se é uma instituição, se for, redireciona para index_doc do seu último processo (mesmo se houver mais de um)
if(isInstituicao()){
	$ProcessoDAO = new ProcessoDAO();
	$result = $ProcessoDAO->getLastFromInstituicao($_SESSION["USUARIO"]["idusuario"]);
    //verifica se o usuário NÃO tem um processo ativo
    if(!$result){
    	enviaMsg("erro","Requisição negada","Seu usuário não possui um processo ativo atrelado.");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index.php\">";
		exit();
    //verifica se tem permissão, se tiver redireciona
    }elseif(verificaProcessoUsuario($result["idprocesso"])){
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doc.php?p=".$result["idprocesso"]."\">";
		exit();
	}
}

// Instanciar o DAO e retornar dados do banco
$ResponsavelDAO = new ResponsavelDAO();
//retorna todos os responsáveis de todos os processos
$responsaveis=$ResponsavelDAO->getAll();
//cria array para associar [idprocesso] aos [nomeusuario] (responsáveis)
$array_responsaveis=array();
foreach ($responsaveis as $r) {
	$array_responsaveis[$r["idprocesso"]][]["nomeusuario"]=$r["nomeusuario"];
}


//SUBMENU DE AÇÕES PROCESSO
require_once('submenu_pro.php');
//FIM SUBMENU DE AÇÕES

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
$ProcessoDAO = new ProcessoDAO();
//$results = $processoDAO->index(); 
if(isset($_GET["order"]) && 
		($_GET["order"]=="numero" 
		|| $_GET["order"]=="etapa" || $_GET["order"]=="instituicao" || $_GET["order"]=="municipio" || $_GET["order"]=="dtprazo") 
		&& ($_GET["ascdesc"]=="ASC" || $_GET["ascdesc"]=="DESC") ){
	$results = $ProcessoDAO->index($paginacao_inicio,$busca,$_GET["order"],$_GET["ascdesc"],$status,$origem);
}else{
	$results = $ProcessoDAO->index($paginacao_inicio,$busca,NULL,NULL,$status,$origem);
}

//Paginação:
$tr = $results[0]["paginacao_numlinhas"]; // verifica o número total de registros 
$tp = $tr / APP_MAX_PAGE_ROWS; // verifica o número total de páginas
$tp2 = ceil($tp); //arredonda para cima o número de páginas, ao inves de ser 1.6 é 2.

echo "<div id=\"conteudo_borda\">
<div  id=\"conteudo\"><h5 class='onlyprint'>Listagem de Processos</h5>";

//se o total de linhas for maior que zero:
if($tr>0){

//faz um pré-varrimento do array de processos p/ verificar se tem acesso
	$numprocessos=0;
	for($i=0;$i<sizeof($results);$i++){				
		if(isset($results[$i]["idprocesso"]) && isset($results[$i]["numero"]) && isset($results[$i]["nomeetapa"]) && verificaProcessoUsuario($results[$i]["idprocesso"])){
				$numprocessos++;
		}
	}

	if($numprocessos>0){

	echo '
	<table class="table table-condensed table-responsive table-hover">
	    <thead>
	      <tr>
	        <th><a title="Clique para ordenar os registros por esta coluna" class="reordenar_pro" href="#" rel="numero">'.exibeFlagReordenacao('numero').'Número</a></th>
	        <th><a title="Clique para ordenar os registros por esta coluna" class="reordenar_pro" href="#" rel="etapa">'.exibeFlagReordenacao('etapa').'Etapa</a></th>
	        <th><a title="Clique para ordenar os registros por esta coluna" class="reordenar_pro" href="#" rel="dtprazo">'.exibeFlagReordenacao('dtprazo').'Prazo</a></th>
			<th><a title="Clique para ordenar os registros por esta coluna" class="reordenar_pro" href="#" rel="instituicao">'.exibeFlagReordenacao('instituicao').'Instituição</a></th>
			<th><a title="Clique para ordenar os registros por esta coluna" class="reordenar_pro" href="#" rel="municipio">'.exibeFlagReordenacao('municipio').'Cidade</a></th>
			<th>Responsável</th>
			<th class="noprint">Ações </th>
	      </tr>
	    </thead>
	    <tbody>
		';
		$numprocessos=0;
		for($i=0;$i<sizeof($results);$i++){
			
			if(isset($results[$i]["idprocesso"]) && isset($results[$i]["numero"]) && isset($results[$i]["nomeetapa"])){
				if(verificaProcessoUsuario($results[$i]["idprocesso"])){
					
					$pro_id=$results[$i]["idprocesso"];
					$pro_numero=$results[$i]["numero"];
					$pro_etapa=$results[$i]["ordem"]." - ".$results[$i]["nomeetapa"];
					$pro_bloquear=$results[$i]["bloquear"];
					$pro_instituicao=$results[$i]["nome_instituicao"];
					$pro_municipio=$results[$i]["nomemunicipio"];
					$pro_dtprazo=$results[$i]["dtprazo"];
					if($pro_dtprazo>0){
						$pro_dtprazo=exibeData($pro_dtprazo);
					}else{
						$pro_dtprazo="Indefinido";
					}
					$pro_resp="";
					//se o processo tiver responsavel definido
					if(isset($array_responsaveis[$pro_id]) && !empty($array_responsaveis[$pro_id])){
						//pro_resp recebe os dados separados com , ou E
						$pro_resp=exibeTextoComVirgulaOuE($array_responsaveis[$pro_id],"nomeusuario");
					}else{
						$pro_resp="Indefinido";
					}
					$numprocessos++;
										
					echo "
					<tr id=\"processo_".$pro_id."\">
					<td>".$pro_numero."</td>
					<td>".$pro_etapa."</td>
					<td>".$pro_dtprazo."</td>
					<td>".$pro_instituicao."</td>
					<td>".$pro_municipio."</td>
					<td>".$pro_resp."</td>
					<td class=\"noprint\"><nobr>

					<button class=\"btn btn-primary documento_index\" aria-label=\"Left Align\" type=\"button\"  title=\"Visualizar processo\"> <span class=\"glyphicon glyphicon-zoom-in\" aria-hidden=\"true\"></span> </button> ";
					
					//verifica as permissóes do usuário e exibe botão de EDITAR e REMOVER
					if(verificaFuncaoUsuario(FUNCAO_PROCESSO_EDIT) && ($pro_bloquear!=ETAPA_BLOQUEIA_PROCESSO || isAdmin())){
						echo "&nbsp;<button class=\"btn btn-success edit_processo\" id=\"processo_".$pro_id."\" aria-label=\"Left Align\" type=\"button\" title=\"Editar processo\"> <span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span> </button> ";
					}
					if(verificaFuncaoUsuario(FUNCAO_PROCESSO_DEL)){
						echo "&nbsp;<button class=\"btn btn-warning processo_del\" id=\"processo_".$pro_id."\" aria-label=\"Left Align\" type=\"button\" title=\"Excluir processo\"> <span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span> </button> ";
					}
					
					echo "</nobr></td></tr>";
				}
			}
		}
		echo '</tbody></table>';
	}else{
		echo 'Nenhum processo encontrado!<br><br>';
		exit();
	}
}else{
	echo 'Nenhum processo encontrado!<br><br>';
	exit();
}

	//Paginação:
	$outrosParametrosPaginacao="";
	//Agora vamos criar os botões "Anterior e Próximo" ao final da página
	include_once("../paginacao.php");

//INSERIDO NO RODAPÉ DE PÁGINAS COM MAIOR ACESSO (INDEX/INDEX_PRO/INDEX_DOC) - FUNÇÕES AUTOMÁTICAS DE CONFIG DO SISTEMA
require_once("@config.php");

include_once("../menu_rodape.php"); 
?>