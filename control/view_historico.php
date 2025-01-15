<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");


//verifica se o usuário possui a função de visualizar o histórico geral
if(verificaFuncaoUsuario(FUNCAO_HISTORICO_ALL)){

//conecta no banco e instacia uma conexão com o Registry
require_once("../conexao.php");
require_once("../model/Registry.php");
//carrega DAO's das chaves estrangeiras
require_once("../dao/UsuarioDAO.php");
require_once("../dao/ProcessoDAO.php");

// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);

require_once("../menu_topo.php");


?>

<div id="conteudo_borda">
    <div  id="conteudo">
            

    	<?php
		if(!empty($_REQUEST["tipo"])){
			//armazena dados do formulario
			if(isset($_REQUEST["tipo"])){
				$form_tipo=$_REQUEST["tipo"];
			}else{
				$form_tipo="";
			}
			if(isset($_REQUEST["idusuario"])){
				$form_idusuario=$_REQUEST["idusuario"];
			}else{
				$form_idusuario="";
			}
			if(isset($_REQUEST["idprocesso"])){
				$form_idprocesso=$_REQUEST["idprocesso"];
			}else{
				$form_idprocesso="";
			}
			if(isset($_REQUEST["periodo_de"])){
				$form_dtde=$_REQUEST["periodo_de"];
			}else{
				$form_dtde="";
			}
			if(isset($_REQUEST["periodo_ate"])){
				$form_dtate=$_REQUEST["periodo_ate"];
			}else{
				$form_dtate="";
			}		
			
			
			
			$geral=false;
			$porUsuario=false;
			$porProcesso=false;
			//requisita e instancia o DAO
			require_once("../dao/HistoricoDAO.php");
			$HistoricoDAO = new HistoricoDAO();
			$tipoVisualizacao="todos os logs";		
			$tipoVisualizacaoId="";
			//define as informações de acordo com o selecionado no formulario
			$ordem=NULL;
			$ascdesc=NULL;
			if(isset($_GET["order"]) && ($_GET["order"]=="dtlog" || $_GET["order"]=="idusuario" || $_GET["order"]=="idacao" || $_GET["order"]=="iddocumento" || $_GET["order"]=="idprocesso" || $_GET["order"]=="obs" || $_GET["order"]=="ip")&&($_GET["ascdesc"]=="ASC" || $_GET["ascdesc"]=="DESC") ){
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


			if($form_tipo=="usuario"){		
				$porUsuario=true;
				$results = $HistoricoDAO->getHistorico($paginacao_inicio,$form_tipo,$form_idusuario,$form_dtde,$form_dtate,$ordem,$ascdesc);				
			}elseif($form_tipo=="processo"){
				$porProcesso=true;
				$results = $HistoricoDAO->getHistorico($paginacao_inicio,$form_tipo,$form_idprocesso,$form_dtde,$form_dtate,$ordem,$ascdesc);
			}else{
				$geral=true;
				//$results = $HistoricoDAO->getGeral($form_dtde,$form_dtate);
				$results = $HistoricoDAO->getHistorico($paginacao_inicio,'geral',NULL,$form_dtde,$form_dtate,$ordem,$ascdesc);
			}
			

			//Paginação:
			$tr = $results[0]["paginacao_numlinhas"]; // verifica o número total de registros 
			$tp = $tr / APP_MAX_PAGE_ROWS; // verifica o número total de páginas
			$tp2 = ceil($tp); //arredonda para cima o número de páginas, ao inves de ser 1.6 é 2.
			if(isset($_GET["pagina"]) && $_GET["pagina"]>$tp2){
				echo '<script>location.href="index_pro.php";</script>';
				exit();
			}

			if($form_tipo=="usuario"){	
				if($tr>0){
					$tipoVisualizacao="histórico de ações de ";
					$tipoVisualizacaoId="<strong>".$results[0]["nomeusuario"]."</strong>";
				}
			}
			if($form_tipo=="processo"){
				if($tr>0){
					$tipoVisualizacao="logs de:";
					$tipoVisualizacaoId="<strong>Processo ".$results[0]["numero"]."</strong>";
				}
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

			//se o total de linhas for maior que zero:
			if($tr>0){ 

				echo "<div class=\"div_sup_documento\" style=\"height:30px !important;\">Visualizando $tipoVisualizacao ";
				
				if(isset($tipoVisualizacaoId)){
					echo $tipoVisualizacaoId;
				}

				echo "</div>";

				//define o cabeçalho da tabela
				echo '<table class="table table-condensed table-responsive table-hover">
					    <thead>
					      <tr>
					      	<th style="width:163px !important;"><a class="reordenar_historico" href="#" rel="dtlog|'.$form_tipo.'|'.$form_idusuario.'|'.$form_idprocesso.'|'.$form_dtde.'|'.$form_dtate.'">'.exibeFlagReordenacao('dtlog').'Data</a></th>';

				if($porUsuario!==true){
					echo '<th><a class="reordenar_historico" href="#" rel="idusuario|'.$form_tipo.'|'.$form_idusuario.'|'.$form_idprocesso.'|'.$form_dtde.'|'.$form_dtate.'">'.exibeFlagReordenacao('idusuario').'Usuário</a></th>';
				}

				echo "		<th style=\"width:170px !important;\"><a class=\"reordenar_historico\" href=\"#\" rel=\"idacao|$form_tipo|$form_idusuario|$form_idprocesso|$form_dtde|$form_dtate\">".exibeFlagReordenacao('idacao')."Ação</a></th>";
				
				if($porProcesso!==true){
					echo '<th><a class="reordenar_historico" href="#" rel="idprocesso|'.$form_tipo.'|'.$form_idusuario.'|'.$form_idprocesso.'|'.$form_dtde.'|'.$form_dtate.'">'.exibeFlagReordenacao('idprocesso').'Processo</a></th>';
				}

				echo '      <th><a class="reordenar_historico" href="#" rel="iddocumento|'.$form_tipo.'|'.$form_idusuario.'|'.$form_idprocesso.'|'.$form_dtde.'|'.$form_dtate.'">'.exibeFlagReordenacao('iddocumento').'Documento</a></th>
					        <th><a class="reordenar_historico" href="#" rel="obs|'.$form_tipo.'|'.$form_idusuario.'|'.$form_idprocesso.'|'.$form_dtde.'|'.$form_dtate.'">'.exibeFlagReordenacao('obs').'Detalhes</a></th>
					        <th><a class="reordenar_historico" href="#" rel="ip|'.$form_tipo.'|'.$form_idusuario.'|'.$form_idprocesso.'|'.$form_dtde.'|'.$form_dtate.'">'.exibeFlagReordenacao('ip').'IP</a></th>
					      </tr>
					    </thead>
					    <tbody>
					';
				for($i=0;$i<sizeof($results);$i++){
					//se o usuário puder visualizar estas informações:
					if(verificaProcessoUsuario($results[$i]["idprocesso"])){
						$dtlog = exibeDataTimestamp($results[$i]["dtlog"]);
						$usuario = $results[$i]["nomeusuario"];
						$acao = $results[$i]["nomeacao"];
						if($results[$i]["numero"]){
							$processo = "<a target='_blank' href='index_doc.php?p=".$results[$i]["idprocesso"]."'>".$results[$i]["numero"]."</a>";	
						}else{
							$processo="";
						}
						if($results[$i]["documento"]){
							$documento = $results[$i]["documento"];
						}else{
							$documento="";
						}				

						$detalhes = exibeDetalhesLog($results[$i]["obs"]);
						$ip = $results[$i]["ip"];

						echo "	<tr>
									<th scope=\"row\">".$dtlog."</th>";

						if($porUsuario!==true){
						echo "<th scope=\"row\">".$usuario."</th>";	
						}

						echo "
									<td>".$acao."</td>";

						if($porProcesso!==true){
						echo "<td>".exibeTexto($processo)."</td>";	
						}

						echo "			
									<td>".exibeDetalhesLog($documento)."</td>
									<td>".exibeDetalhesLog($detalhes)."</td>
									<td>".$ip."</td>
								</tr>";
					}
				}		

				echo '</tbody></table>';
				
				//Paginação:
				//Pode-se definir outrosParametrosPaginacao que são informações à ser passadas por GET
				$outrosParametrosPaginacao='&tipo='.$form_tipo.'&idusuario='.$form_idusuario.'&idprocesso='.$form_idprocesso.'&periodo_de='.$form_dtde.'&periodo_ate='.$form_dtate;
				//Aqui criamos os botões "Anterior e Próximo" ao final da página
				include_once("../paginacao.php"); 
			}else{
				echo 'Nenhum dado encontrado com o filtro utilizado. <br><br>';
			}


?>

		<form id="view_historico" name="view_historico" action="view_historico.php" method="post" class="form-horizontal noprint" style="margin-left:15px;">

	            <div class="form-group"  style="margin-right:0px;">
	              <div class="col-lg-10">
	                <button type="reset" id="cancelar" class="btn btn-default view_historico.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
	              </div>
	            </div>

	        </form>

<?php		
		//se não, exibe o form para pesquisa dos logs
		}else{
    	?>
    		<!-- <form name="view_historico" action="view_historico.php" method="get" class="form-horizontal" onSubmit="return validaForm('view_historico','tipo','idusuario','idprocesso');" > -->
	        <form id="view_historico" name="view_historico" action="view_historico.php" method="get" class="form-horizontal">

	        	<input type="hidden" name="p" id="p" value="<?php if(isset($_GET["p"])){ echo $_GET["p"]; } ?>">

	            <div class="form-group">
	              <label class="col-sm-10 control-label">Tipo de Histórico</label>
	                <div class="col-lg-5">
	                  <select id="tipo"  name="tipo" class="form-control" onchange="showCampoViaSelect(this.value);">
						<option value="geral">Geral</option>
						<option value="usuario">Por Usuário</option>
						<option value="processo">Por Processo</option>
	                  </select>
	                </div>
	            </div>

	            <div class="form-group campoviaselect" id="campoviaselectusuario">
	              <label class="col-sm-10 control-label">Usuário</label>
	                <div class="col-lg-5">
	                  <select id="idusuario"  name="idusuario" class="form-control">
						<option value="-1">Selecione um usuário</option>
						<?php
							// Recuperar infos do banco
							$usuarioDAO = new UsuarioDAO();
							$usuario = $usuarioDAO->getAll();
			                if(sizeof($usuario)>0){
			                    for($i=0;$i<sizeof($usuario);$i++){
									echo "<option value=\"".$usuario[$i]["idusuario"]."\">".$usuario[$i]["nomeusuario"]." (".$usuario[$i]["nomeperfil"];
									if($usuario[$i]["idperfil"]==PERFIL_IDINSTITUICAO && isset($usuario[$i]["nome_instituicao"]) && !empty($usuario[$i]["nome_instituicao"])){
										echo ' / '.$usuario[$i]["nome_instituicao"];
									}
									echo ")</option>";
			                    }
			                }
			            ?>
	                  </select>
	                </div>
	            </div>

	            <div class="form-group campoviaselect" id="campoviaselectprocesso">
	              <label class="col-sm-10 control-label">Processo</label>
	                <div class="col-lg-5">
	                  <select id="idprocesso"  name="idprocesso" class="form-control">
						<option value="-1">Selecione um processo</option>
						<?php
							// Recuperar infos do banco
							$processoDAO = new ProcessoDAO();
							$processo = $processoDAO->getAll();
			                if(sizeof($processo)>0){
			                    for($i=0;$i<sizeof($processo);$i++){
			                    	//se o usuário puder visualizar o processo:
									if(verificaProcessoUsuario($processo[$i]["idprocesso"])){
			                    		echo "<option value=\"".$processo[$i]["idprocesso"]."\">Processo ".$processo[$i]["numero"]."</option>";
			                    	}
			                    }
			                }
			            ?>
	                  </select>
	                </div>
	            </div>


		        <div class="form-group" id="div_relatorio">
		        	<label class="col-sm-12 control-label">Período</label>
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
	                <button type="reset" id="cancelar" class="btn btn-default index_pro.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
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

}else{

	echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php?s=21\">";
	exit();

}

include_once("../menu_rodape.php");
?>
