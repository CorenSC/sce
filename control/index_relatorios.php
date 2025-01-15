<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");

//verifica se o usuário possui a função de visualizar o histórico geral
if(verificaFuncaoUsuario(FUNCAO_HISTORICO_ALL)){

require_once("../menu_topo.php");

?>
<div id="conteudo_borda">
	<div  id="conteudo">
		<h5 class="onlyprint">Relatórios</h5>
			<form name="index_relatorios" id="index_relatorios" action="view_relatorios.php" method="post" class="form-horizontal">
			    <div class="form-group">
	              <label class="col-sm-10 control-label">Tipo de Relatório</label>
	                <div class="col-lg-5">
	                  <select id="tipo"  name="tipo" class="form-control" onchange="showCampoViaSelect(this.value);">
						<option value="-1">Selecione</option>
						<option value="rel1">Total de Processos/Status/Município</option>
						<option value="rel2">Gráficos</option>
	                  </select>
	                </div>
	            </div>
	            <div class="form-group" id="div_relatorio">
		        	<label class="col-sm-12 control-label">Período</label>
		        	<div class="col-lg-3">
		        	<div class="input-group date">
			            <input type="text" class="form-control" id="periodo_de" name="periodo_de" placeholder="De..." maxlength="<?php echo $GLOBALS["usuario_dtexpiracao_size"]+2; ?>" autocomplete="off">
			            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
		        	</div>
		        	<div class="input-group date">
			            <input type="text" class="form-control" id="periodo_ate" name="periodo_ate" placeholder="Até..." maxlength="<?php echo $GLOBALS["usuario_dtexpiracao_size"]+2; ?>" autocomplete="off">
			            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
		        	</div>
		        	</div>
		        </div>
		        <span id="helpBlock" class="help-block">Deixe em branco para gerar o relatório independente do período</span>
	            
	            <div class="form-group" style="margin-top:40px;">
	              <div class="col-lg-10">
	                <button type="submit" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Visualizar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
	              </div>
	            </div>
	        </form>


<?php
include_once("../menu_rodape.php"); 

//else do verificaFuncaoUsuario($GLOBALS["f_historico_all"])
}else{

		//acesso negado
		enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou foram dados inválidos");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
		exit();

}
?>