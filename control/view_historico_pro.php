<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");

if(isset($_REQUEST["p"]) && !empty($_REQUEST["p"])){
	$idprocesso=$_REQUEST["p"];		
}
if(isset($idprocesso) && !empty($idprocesso) && verificaProcessoUsuario($idprocesso) && verificaFuncaoUsuario(FUNCAO_HISTORICO_PRO) ){
$idprocesso=$_REQUEST["p"];	


//conecta no banco e instacia uma conexão com o Registry
require_once("../conexao.php");
require_once("../model/Registry.php");
// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);
//carrega DAO's das chaves estrangeiras
require_once("../dao/ProcessoDAO.php");
require_once("../model/Processo.php");
require_once("../model/Usuario.php");
$Processo = new Processo();
$Processo->setId($idprocesso);
$ProcessoDAO = new ProcessoDAO();
$dados = $ProcessoDAO->getInfosCapa($Processo);
if(sizeof($dados)>0 && $dados!==false){

require_once("../menu_topo.php");


//SUBMENU DE AÇÕES
require_once('submenu_doc.php');
//FIM SUBMENU DE AÇÕES

?>

<div id="conteudo_borda">
    <div  id="conteudo">
            

    	<?php

		if( isset($_REQUEST["periodo_de"]) ){
			//armazena dados do formulario
			$form_tipo="processo";
			$form_idusuario=NULL;
			$form_idprocesso=$_REQUEST["p"];
			$form_dtde="";
			if(isset($_REQUEST["periodo_de"])){
				$form_dtde=$_REQUEST["periodo_de"];
			}
			$form_dtate="";
			if(isset($_REQUEST["periodo_ate"])){
				$form_dtate=$_REQUEST["periodo_ate"];
			}

			//requisita e instancia o DAO
			require_once("../dao/HistoricoDAO.php");
			$HistoricoDAO = new HistoricoDAO();		
			$ordem=NULL;
			$ascdesc=NULL;
			if(isset($_GET["order"]) && ($_GET["order"]=="dtlog" || $_GET["order"]=="idusuario" || $_GET["order"]=="idacao" || $_GET["order"]=="iddocumento" || $_GET["order"]=="idprocesso" || $_GET["order"]=="obs" || $_GET["order"]=="ip")&&(isset($_GET["ascdesc"]) && ( $_GET["ascdesc"]=="ASC" || $_GET["ascdesc"]=="DESC") ) ){
				$ordem=$_GET["order"];
				$ascdesc=$_GET["ascdesc"];
			}

			//Paginação:
			if(isset($_GET['pagina']) && $_GET['pagina']>0){
				$pc = $_GET['pagina'];
			}else{
				$pc = "1";
			}
			$paginacao_inicio = $pc - 1; 
			$paginacao_inicio = $paginacao_inicio * APP_MAX_PAGE_ROWS;




			$results = $HistoricoDAO->getHistorico($paginacao_inicio,$form_tipo,$form_idprocesso,$form_dtde,$form_dtate,$ordem,$ascdesc);
			$tipoVisualizacaoId="";
			if(sizeof($results)>1){
				$tipoVisualizacaoId="<strong> Processo de ".$results[0]["nometipo"].' nº '.$results[0]["numero"]."</strong>";					
			}
			//quando houver data de inicio ou data de fim
			if(!empty($form_dtde) || !empty($form_dtate)){
				//quando houver as 2 datas
				if(!empty($form_dtde) && !empty($form_dtate)){
					$tipoVisualizacaoId.=" no período de ".$form_dtde." à ".$form_dtate;
				}elseif(!empty($form_dtde)){
					$tipoVisualizacaoId.=" em qualquer período superior ou igual à ".$form_dtde;
				}else{
					$tipoVisualizacaoId.=" em qualquer período inferior ou igual à ".$form_dtate;
				}
			}	


			//Paginação:
			$tr = $results[0]["paginacao_numlinhas"]; // verifica o número total de registros 
			$tp = $tr / APP_MAX_PAGE_ROWS; // verifica o número total de páginas
			$tp2 = ceil($tp); //arredonda para cima o número de páginas, ao inves de ser 1.6 é 2.
			if(isset($_GET["pagina"]) && $_GET["pagina"]>$tp2){
				echo '<script>location.href="index_pro.php";</script>';
				exit();
			}
			
			
			//Verifica se o usuário verá os IPS
			//se o usuário for dos perfis TOPS, vê o IP dos usuários no histórico do processo, caso contrário não			        
			if( isAdmin() ||  isPresidente() || isComissaoEtica() ){
				$visualizaIP=true;
			}else{
				//qualquer outro perfil, não vê
				$visualizaIP=false;
			}
							
			

			if($tr>0){

				echo "<div class=\"div_sup_documento\" style=\"height:30px !important;\">Visualizando Histórico: $tipoVisualizacaoId</div>";

				//define o cabeçalho da tabela
				echo '<table class="table table-condensed table-responsive table-hover">
					    <thead>
					      <tr>
					      	<th style="width:163px !important;"><a class="reordenar_historico_pro" href="#" rel="dtlog|'.$idprocesso.'|'.$form_tipo.'|'.$form_idusuario.'|'.$form_idprocesso.'|'.$form_dtde.'|'.$form_dtate.'">Data</a></th>';

					echo '<th><a class="reordenar_historico_pro" href="#" rel="idusuario|'.$idprocesso.'|'.$form_tipo.'|'.$form_idusuario.'|'.$form_idprocesso.'|'.$form_dtde.'|'.$form_dtate.'">Usuário</a></th>';

				echo '		<th style="width:170px !important;"><a class="reordenar_historico_pro" href="#" rel="idacao|'.$idprocesso.'|'.$form_tipo.'|'.$form_idusuario.'|'.$form_idprocesso.'|'.$form_dtde.'|'.$form_dtate.'">Ação</a></th>';
				
				echo '      <th><a class="reordenar_historico_pro" href="#" rel="iddocumento|'.$idprocesso.'|'.$form_tipo.'|'.$form_idusuario.'|'.$form_idprocesso.'|'.$form_dtde.'|'.$form_dtate.'">Documento</a></th>
					        <th><a class="reordenar_historico_pro" href="#" rel="obs|'.$idprocesso.'|'.$form_tipo.'|'.$form_idusuario.'|'.$form_idprocesso.'|'.$form_dtde.'|'.$form_dtate.'">Detalhes</a></th>';
				
				//se o usuário poder visualizar os IPS adiciona coluna ao HTML
				if($visualizaIP){
					echo '		<th><a class="reordenar_historico_pro" href="#" rel="ip|'.$idprocesso.'|'.$form_tipo.'|'.$form_idusuario.'|'.$form_idprocesso.'|'.$form_dtde.'|'.$form_dtate.'">IP</a></th>';
				}

				echo '		</tr>
					    </thead>
					    <tbody>
					';

				for($i=0;$i<sizeof($results);$i++){
					$dtlog = exibeDataTimestamp($results[$i]["dtlog"]);
					$usuario = $results[$i]["nomeusuario"];
					$acao = $results[$i]["nomeacao"];
					if($results[$i]["documento"]){
						$documento = $results[$i]["documento"];
					}else{
						$documento = "";
					}
					$detalhes = $results[$i]["obs"];
					$ip = $results[$i]["ip"];

					echo "	<tr>
								<th scope=\"row\">".$dtlog."</th>";

					echo "<th scope=\"row\">".$usuario."</th>";	

					echo "
								<td>".$acao."</td>";

					echo "			
								<td>".$documento."</td>
								<td>".exibeDetalhesLog($detalhes)."</td>";

					//se o usuário poder visualizar os IPS adiciona coluna ao HTML
					if($visualizaIP){
						echo "		<td>".$ip."</td>";
					}

					echo "	</tr>";
				}		


			}

			if($tr>0){
				echo '</tbody></table>';

				//Paginação:
				//Pode-se definir outrosParametrosPaginacao que são informações à ser passadas por GET
				$outrosParametrosPaginacao='&tipo='.$form_tipo.'&idusuario='.$form_idusuario.'&p='.$idprocesso.'&periodo_de='.$form_dtde.'&periodo_ate='.$form_dtate;
				//Aqui criamos os botões "Anterior e Próximo" ao final da página
				include_once("../paginacao.php"); 

			}else{
				echo 'Nenhum dado foi encontrado com o filtro utilizado. <br><br>';
			}

?>

	    <div  id="conteudo_borda">
		<form id="view_historico_pro" name="view_historico_pro" action="view_historico_pro.php" method="post" class="form-horizontal">

	            <div class="form-group pull-left" style="margin-right:0px;">
	              <div class="col-lg-10">
	                <button type="reset" id="cancelar" class="btn btn-default index_doc.php?p=<?php echo $_REQUEST["p"]; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
	              </div>
	            </div>

        </form>
    	</div>

<?php
		//se não, exibe o form para pesquisa dos logs
		}else{

    	?>

	        <form id="view_historico_pro" name="view_historico_pro" action="view_historico_pro.php" method="get" class="form-horizontal">

	        	<input type="hidden" name="p" id="p" value="<?php echo $_REQUEST["p"]; ?>">

	            <div class="form-group">
	              <label class="col-sm-10 control-label">Histórico do Processo de <?php echo $dados["nometipo"].' '.$dados["numero"]; ?></option>
	            </div>


		        <div class="form-group" id="div_relatorio">
		        	<label class="col-sm-12 control-label">Período do Relatório</label>
		        	<div class="col-lg-3">
		        	<div class="input-group date">
			            <input type="text" class="form-control" id="periodo_de" name="periodo_de" placeholder="De..." maxlength="<?php echo USUARIO_DTEXPIRACAO_SIZE+2; ?>" autocomplete="off">
			            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
		        	</div>
		        	<div class="input-group date">
			            <input type="text" class="form-control" id="periodo_ate" name="periodo_ate" placeholder="Até..." maxlength="<?php echo USUARIO_DTEXPIRACAO_SIZE+2; ?>" autocomplete="off">
			            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
		        	</div>
		        	</div>
		        </div>
		        <span id="helpBlock" class="help-block">Deixe em branco para pesquisar independente do período</span>
	            
	            <div class="form-group" style="margin-top:40px;">
	              <div class="col-lg-10">
	                <button type="reset" id="cancelar" class="btn btn-default index_doc.php?p=<?php echo $_REQUEST["p"]; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
	                <button type="submit" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Visualizar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
	              </div>
	            </div>

	        </form>

        <?php
    	}//fim do else - se o usuário não tiver enviado a consulta
        ?>

	</div>
</div>
<?php

include_once("../menu_rodape.php");

}else{//ELSE do if sizeof($dados)>0

	  enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou dados inválidos");
      echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
      exit();

}

}//ELSE DO if(verificaPadUsuario($idprocesso)){
else{

	  enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou dados inválidos");
      echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
      exit();

}
?>
