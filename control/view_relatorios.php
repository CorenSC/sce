<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");

//verifica se o usuário possui a função de visualizar o histórico geral
if(verificaFuncaoUsuario(FUNCAO_HISTORICO_ALL)){

require_once("../menu_topo.php");

//carrega as bibliotecas para recuperar informações do BD
//conecta no banco e instacia uma conexão com o Registry
require_once("../conexao.php");
require_once("../model/Registry.php");
// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);
//carrega DAO's das chaves estrangeiras
require_once("../dao/RelatorioDAO.php");

// Instanciar o DAO e retornar dados do banco
$RelatorioDAO = new RelatorioDAO();

if(isset($_REQUEST["tipo"])){

	//se houver um periodo para o relatório
	if(isset($_REQUEST["periodo_de"])){
		$periodo_de=$_REQUEST["periodo_de"];	
	}else{
		$periodo_de=NULL;
	}
	if(isset($_REQUEST["periodo_ate"])){
		$periodo_ate=$_REQUEST["periodo_ate"];
	}else{
		$periodo_ate=NULL;
	}
	
	

	//REL1-------------------------------------------------------------------------------------------------------------------------------------------------REL1
	if($_REQUEST["tipo"] == "rel1"){

		//funções específicas do relatório:
		//calculo dos totais
		// Compara se $a é maior que $b
		function ordenaEtapa($a, $b) {
			return $a["nomeetapa"] > $b["nomeetapa"];
		}
		function ordenaMunicipio($a, $b) {
			return $a["nomemunicipio"] > $b["nomemunicipio"];
		}

		$results = $RelatorioDAO->rel1ativos($periodo_de,$periodo_ate);

		echo "<div id=\"conteudo_borda\"><div  id=\"conteudo\">";

		if($periodo_de!=NULL || $periodo_ate!=NULL){
			echo '<p>Período do Relatório: ';
			if($periodo_de!=NULL && $periodo_ate!=NULL){
				echo $periodo_de.' a '.$periodo_ate;
			}
			if($periodo_de!=NULL && $periodo_ate==NULL){
				echo 'a partir de '.$periodo_de;
			}
			if($periodo_ate!=NULL && $periodo_de==NULL){
				echo 'até '.$periodo_ate;
			}
			echo '</p>';
		}

		//se o total de linhas for maior que zero:
		if(sizeof($results)>0 && $results!==false){ 
		echo "<h5 class='onlyprint'>Relatório de Comissões de Ética Ativas</h5>";

		$tipo		=array();//renovação / implantação
		$etapa 		=array();//etapas
		$municipio 	=array();//municipio

		$tipo_num		=array();
		$etapa_num		=array();
		$municipio_num	=array();

		$tipo_ant 		="";
		$etapa_ant 		="";
		$municipio_ant 	="";

		$tipo_cont=0;
		$etapa_cont=-1;
		$municipio_cont=-1;

		$dados=array();  


		//numero de tipos
		$implantacao_num=0;
		$renovacao_num=0;
		//arrays
		//implantação
		$i_etapa_nome=array();
		$i_etapa_total=array();
		$i_municipio_nome=array();
		$i_municipio_total=array();
		//renovação
		$r_etapa_nome=array();
		$r_etapa_total=array();
		$r_municipio_nome=array();
		$r_municipio_total=array();
		//numero de etapas por ordem
		$i_etapa_cont=-1;
		$i_etapa_num=0;
		$r_etapa_cont=-1;
		$r_etapa_num=0;
		//contadores municipio
		$i_municipio_cont=-1;
		$i_municipio_num=0;
		$r_municipio_cont=-1;
		$r_municipio_num=0;
		//vars de controle
		$i_etapa_anterior="";
		$i_municipio_anterior="";
		$r_etapa_anterior="";
		$r_municipio_anterior="";

		for($i=0;$i<sizeof($results);$i++){
				
			//implantação
			if($results[$i]["nometipo"]=="Implantação"){ 		
				$implantacao_num++;
				//se etapa atual dirente do anterior
				if($results[$i]["nomeetapa"]!=$i_etapa_anterior){
					$i_municipio_cont=-1;
					++$i_etapa_cont;
					$i_etapa_num=1;
					$i_etapa_nome[$i_etapa_cont]=$results[$i]["nomeetapa"];
					$i_etapa_total[$i_etapa_cont]=$i_etapa_num;
				//se nomeetapa igual
				}else{
					++$i_etapa_num;
					$i_etapa_total[$i_etapa_cont]=$i_etapa_num;
				}
				//exibe e conta os municipios
				if($results[$i]["nomemunicipio"]!=$i_municipio_anterior){
					++$i_municipio_cont;
					$i_municipio_num=1;
					$i_municipio_nome[$i_etapa_cont][$i_municipio_cont]=$results[$i]["nomemunicipio"];
					$i_municipio_total[$i_etapa_cont][$i_municipio_cont]=$i_municipio_num;
				//se municipio igual
				}else{
					++$i_municipio_num;
					//se for o mesmo municipio mas tiver mudado a etapa, zera o indice de municipios dessa etapa e seta que é o primeiro municipio dessa etapa no contador
					if($results[$i]["nomeetapa"]!=$i_etapa_anterior){
						$i_municipio_cont=0;
						$i_municipio_num=1;
					}
					$i_municipio_nome[$i_etapa_cont][$i_municipio_cont]=$results[$i]["nomemunicipio"];
					$i_municipio_total[$i_etapa_cont][$i_municipio_cont]=$i_municipio_num;
				}
				//define novos "últimos valores"
				$i_etapa_anterior=$results[$i]["nomeetapa"];
				$i_municipio_anterior=$results[$i]["nomemunicipio"];
			}

			//renovação
			if($results[$i]["nometipo"]=="Renovação"){ 
				$renovacao_num++; 
				//se nomeetapa dirente do anterior
				if($results[$i]["nomeetapa"]!=$r_etapa_anterior){
					$r_municipio_cont=-1;
					++$r_etapa_cont;
					$r_etapa_num=1;
					$r_etapa_nome[$r_etapa_cont]=$results[$i]["nomeetapa"];
					$r_etapa_total[$r_etapa_cont]=$r_etapa_num;			
				//se nomeetapa igual
				}else{
					++$r_etapa_num;
					$r_etapa_total[$r_etapa_cont]=$r_etapa_num;
				}
				//exibe e conta nomemunicipio
				if($results[$i]["nomemunicipio"]!=$r_municipio_anterior){
					++$r_municipio_cont;
					$r_municipio_num=1;
					$r_municipio_nome[$r_etapa_cont][$r_municipio_cont]=$results[$i]["nomemunicipio"];
					$r_municipio_total[$r_etapa_cont][$r_municipio_cont]=$r_municipio_num;
				//se nomemunicipio igual
				}else{
					++$r_municipio_num;
					//se for o mesmo municipio mas tiver mudado a etapa, zera o indice de municipios dessa etapa e seta que é o primeiro municipio dessa etapa no contador
					if($results[$i]["nomeetapa"]!=$r_etapa_anterior){
						$r_municipio_cont=0;
						$r_municipio_num=1;
					}
					$r_municipio_nome[$r_etapa_cont][$r_municipio_cont]=$results[$i]["nomemunicipio"];
					$r_municipio_total[$r_etapa_cont][$r_municipio_cont]=$r_municipio_num;
				}
				//define novos "últimos valores"
				$r_etapa_anterior=$results[$i]["nomeetapa"];
				$r_municipio_anterior=$results[$i]["nomemunicipio"];
			}

		}

		//sorteia agora pelo nomemunicipio
		usort($results,'ordenaMunicipio');

		for($i=0;$i<sizeof($results);$i++){
			if($municipio_ant!=$results[$i]["nomemunicipio"]){
				if(!in_array($results[$i]["nomemunicipio"],$municipio)){
					++$municipio_cont;
					$municipio[$municipio_cont]=$results[$i]["nomemunicipio"];
					$municipio_num[$municipio_cont]=1;
					$municipio_ant=$results[$i]["nomemunicipio"];			
				}else{
					//encontra posicao do status e adiciona mais um
					$posicao=array_search($results[$i]["nomemunicipio"],$municipio);
					$municipio_num[$posicao]=($municipio_num[$posicao]+1);
				}
			}else{
				$municipio_num[$municipio_cont]=($municipio_num[$municipio_cont]+1);
			}
		}

		//sorteia dados pelo nomeetapa
		usort($results,'ordenaEtapa');

		for($i=0;$i<sizeof($results);$i++){
			if($etapa_ant!=$results[$i]["nomeetapa"]){
				if(!in_array($results[$i]["nomeetapa"],$etapa)){
					++$etapa_cont;
					$etapa[$etapa_cont]=$results[$i]["nomeetapa"];
					$etapa_num[$etapa_cont]=1;
					$etapa_ant=$results[$i]["nomeetapa"];
				}else{
					//encontra posicao do status e adiciona mais um
					$posicao=array_search($results[$i]["nomeetapa"],$etapa);
					$etapa_num[$posicao]=($etapa_num[$posicao]+1);
				}
			}else{
				$etapa_num[$etapa_cont]=($etapa_num[$etapa_cont]+1);
			}
		}

		echo '
		<table width="100%" class="table table-condensed table-responsive table-hover">
		';

		//total geral
			
			echo '<tr style="background-color:#BCDAED;"><td colspan=3; valign="top"><strong>Total de Comissões de Ética Ativas</strong></td></td></tr>
			<tr>
			<th width="20%">Quantidade/Tipo</th>
			<th width="30%">Quantidade/Etapa</th>
			<th width="50%">Quantidade/Município</th>
			</tr>';
			//tipo
			echo '<tr><td width="20%" valign="top">'.($renovacao_num+$implantacao_num).' Implantações / Renovações</td><td valign="top">';
			//etapa
			for($i=0;$i<sizeof($etapa);$i++){
				echo $etapa_num[$i].' '.$etapa[$i].'<br>';
			}
			echo '</td><td>';
			//municipio
			for($i=0;$i<sizeof($municipio);$i++){
				echo $municipio_num[$i].' '.$municipio[$i].'<br>';
			}
			echo '</td></tr>';

		//totais por tipo	

			echo '<tr style="background-color:#BCDAED;"><td colspan=3; valign="top"><strong>Total de Comissões de Ética Ativas por tipo</strong></td></td></tr>
			<tr>
			<th width="20%">Quantidade/Tipo</th>
			<th width="30%">Quantidade/Etapa</th>
			<th width="50%">Quantidade/Municipio</th>
			</tr>
			<tr>
			';

			//implantações:
				//tipo
				echo '<td width="20%" valign="top">'.$implantacao_num.' Implantações</td><td valign="top" width="80%" colspan=2>';
				for($i=0;$i<sizeof($i_etapa_nome);$i++){
					//etapa
					echo '<table width="100%"><tr><td valign="top" width="30%" rowspan='.sizeof($i_municipio_nome[$i]).'>'.$i_etapa_total[$i].' '.$i_etapa_nome[$i].' </td><td width="50%"><table>';
					for($j=0;$j<sizeof($i_municipio_nome[$i]);$j++){
						//municipio
						echo '<tr><td valign="top" style="padding-left:7px;">'.$i_municipio_total[$i][$j].' '.$i_municipio_nome[$i][$j].'</td></tr>';
					}
					echo '</table><br><br></td></table>';
				}
				echo '</td></tr>';

			//renovações:
				//tipo
				echo '<tr><td width="20%" valign="top">'.$renovacao_num.' Renovações</td><td valign="top" colspan=2>';
				for($i=0;$i<sizeof($r_etapa_nome);$i++){
					//etapa
					echo '<table width="100%"><tr><td valign="top" width="30%" rowspan='.sizeof($r_municipio_nome[$i]).'>'.$r_etapa_total[$i].' '.$r_etapa_nome[$i].' </td><td width="50%"><table>';
					for($j=0;$j<sizeof($r_municipio_nome[$i]);$j++){
						//municipio
						echo '<tr><td valign="top" style="padding-left:7px;">'.$r_municipio_total[$i][$j].' '.$r_municipio_nome[$i][$j].'</td></tr>';
					}
					echo '</table><br><br></td></table>';
				}
				echo '</td></tr>';

		//fim tabela
		echo '</table><br><hr>';

		}


		//RELATORIO HISTORICO DE COMISSOES

		$results = $RelatorioDAO->rel1($periodo_de,$periodo_ate);

		//se o total de linhas for maior que zero:
		if(sizeof($results)>0 && $results!==false){ 
		echo "<h5 class='onlyprint'>Relatório Histórico de Comissões de Ética (Ativas e Inativas)</h5>";

		$tipo		=array();//renovação / implantação
		$etapa 		=array();//etapas
		$municipio 	=array();//municipio

		$tipo_num		=array();
		$etapa_num		=array();
		$municipio_num	=array();

		$tipo_ant 		="";
		$etapa_ant 		="";
		$municipio_ant 	="";

		$tipo_cont=0;
		$etapa_cont=-1;
		$municipio_cont=-1;

		$dados=array();  


		//numero de tipos
		$implantacao_num=0;
		$renovacao_num=0;
		//arrays
		//denuncia
		$i_etapa_nome=array();
		$i_etapa_total=array();
		$i_municipio_nome=array();
		$i_municipio_total=array();
		//processo
		$r_etapa_nome=array();
		$r_etapa_total=array();
		$r_municipio_nome=array();
		$r_municipio_total=array();
		//numero de etapas por ordem
		$i_etapa_cont=-1;
		$i_etapa_num=0;
		$r_etapa_cont=-1;
		$r_etapa_num=0;
		//contadores municipio
		$i_municipio_cont=-1;
		$i_municipio_num=0;
		$r_municipio_cont=-1;
		$r_municipio_num=0;
		//vars de controle
		$i_etapa_anterior="";
		$i_municipio_anterior="";
		$r_etapa_anterior="";
		$r_municipio_anterior="";

		for($i=0;$i<sizeof($results);$i++){
				
			//implantação
			if($results[$i]["nometipo"]=="Implantação"){ 		
				$implantacao_num++;
				//se etapa atual dirente do anterior
				if($results[$i]["nomeetapa"]!=$i_etapa_anterior){
					$i_municipio_cont=-1;
					++$i_etapa_cont;
					$i_etapa_num=1;
					$i_etapa_nome[$i_etapa_cont]=$results[$i]["nomeetapa"];
					$i_etapa_total[$i_etapa_cont]=$i_etapa_num;
				//se nomeetapa igual
				}else{
					++$i_etapa_num;
					$i_etapa_total[$i_etapa_cont]=$i_etapa_num;
				}
				//exibe e conta os municipios
				if($results[$i]["nomemunicipio"]!=$i_municipio_anterior){
					++$i_municipio_cont;
					$i_municipio_num=1;
					$i_municipio_nome[$i_etapa_cont][$i_municipio_cont]=$results[$i]["nomemunicipio"];
					$i_municipio_total[$i_etapa_cont][$i_municipio_cont]=$i_municipio_num;
				//se municipio igual
				}else{
					++$i_municipio_num;
					//se for o mesmo municipio mas tiver mudado a etapa, zera o indice de municipios dessa etapa e seta que é o primeiro municipio dessa etapa no contador
					if($results[$i]["nomeetapa"]!=$i_etapa_anterior){
						$i_municipio_cont=0;
						$i_municipio_num=1;
					}
					$i_municipio_nome[$i_etapa_cont][$i_municipio_cont]=$results[$i]["nomemunicipio"];
					$i_municipio_total[$i_etapa_cont][$i_municipio_cont]=$i_municipio_num;
				}
				//define novos "últimos valores"
				$i_etapa_anterior=$results[$i]["nomeetapa"];
				$i_municipio_anterior=$results[$i]["nomemunicipio"];
			}

			//renovação
			if($results[$i]["nometipo"]=="Renovação"){ 
				$renovacao_num++; 
				//se nomeetapa dirente do anterior
				if($results[$i]["nomeetapa"]!=$r_etapa_anterior){
					$r_municipio_cont=-1;
					++$r_etapa_cont;
					$r_etapa_num=1;
					$r_etapa_nome[$r_etapa_cont]=$results[$i]["nomeetapa"];
					$r_etapa_total[$r_etapa_cont]=$r_etapa_num;			
				//se nomeetapa igual
				}else{
					++$r_etapa_num;
					$r_etapa_total[$r_etapa_cont]=$r_etapa_num;
				}
				//exibe e conta nomemunicipio
				if($results[$i]["nomemunicipio"]!=$r_municipio_anterior){
					++$r_municipio_cont;
					$r_municipio_num=1;
					$r_municipio_nome[$r_etapa_cont][$r_municipio_cont]=$results[$i]["nomemunicipio"];
					$r_municipio_total[$r_etapa_cont][$r_municipio_cont]=$r_municipio_num;
				//se nomemunicipio igual
				}else{
					++$r_municipio_num;
					//se for o mesmo municipio mas tiver mudado a etapa, zera o indice de municipios dessa etapa e seta que é o primeiro municipio dessa etapa no contador
					if($results[$i]["nomeetapa"]!=$r_etapa_anterior){
						$r_municipio_cont=0;
						$r_municipio_num=1;
					}
					$r_municipio_nome[$r_etapa_cont][$r_municipio_cont]=$results[$i]["nomemunicipio"];
					$r_municipio_total[$r_etapa_cont][$r_municipio_cont]=$r_municipio_num;
				}
				//define novos "últimos valores"
				$r_etapa_anterior=$results[$i]["nomeetapa"];
				$r_municipio_anterior=$results[$i]["nomemunicipio"];
			}
		}

		//sorteia agora pelo nomemunicipio
		usort($results,'ordenaMunicipio');

		for($i=0;$i<sizeof($results);$i++){
			if($municipio_ant!=$results[$i]["nomemunicipio"]){
				if(!in_array($results[$i]["nomemunicipio"],$municipio)){
					++$municipio_cont;
					$municipio[$municipio_cont]=$results[$i]["nomemunicipio"];
					$municipio_num[$municipio_cont]=1;
					$municipio_ant=$results[$i]["nomemunicipio"];			
				}else{
					//encontra posicao do status e adiciona mais um
					$posicao=array_search($results[$i]["nomemunicipio"],$municipio);
					$municipio_num[$posicao]=($municipio_num[$posicao]+1);
				}
			}else{
				$municipio_num[$municipio_cont]=($municipio_num[$municipio_cont]+1);
			}
		}

		//sorteia dados pelo nomeetapa
		usort($results,'ordenaEtapa');

		for($i=0;$i<sizeof($results);$i++){
			if($etapa_ant!=$results[$i]["nomeetapa"]){
				if(!in_array($results[$i]["nomeetapa"],$etapa)){
					++$etapa_cont;
					$etapa[$etapa_cont]=$results[$i]["nomeetapa"];
					$etapa_num[$etapa_cont]=1;
					$etapa_ant=$results[$i]["nomeetapa"];
				}else{
					//encontra posicao do status e adiciona mais um
					$posicao=array_search($results[$i]["nomeetapa"],$etapa);
					$etapa_num[$posicao]=($etapa_num[$posicao]+1);
				}
			}else{
				$etapa_num[$etapa_cont]=($etapa_num[$etapa_cont]+1);
			}
		}

		echo '
		<table width="100%" class="table table-condensed table-responsive table-hover">
		';

		//total geral
			
			echo '<tr style="background-color:#BCDAED;"><td colspan=3; valign="top"><strong>Total Histórico de Comissões de Ética (Ativas e Inativas)</strong></td></td></tr>
			<tr>
			<th width="20%">Quantidade/Tipo</th>
			<th width="30%">Quantidade/Etapa</th>
			<th width="50%">Quantidade/Município</th>
			</tr>';
			//tipo
			echo '<tr><td width="20%" valign="top">'.($renovacao_num+$implantacao_num).' Implantações / Renovações</td><td valign="top">';
			//etapa
			for($i=0;$i<sizeof($etapa);$i++){
				echo $etapa_num[$i].' '.$etapa[$i].'<br>';
			}
			echo '</td><td>';
			//municipio
			for($i=0;$i<sizeof($municipio);$i++){
				echo $municipio_num[$i].' '.$municipio[$i].'<br>';
			}
			echo '</td></tr>';

		//totais por tipo	

			echo '<tr style="background-color:#BCDAED;"><td colspan=3; valign="top"><strong>Total Histórico de Comissões de Ética (Ativas e Inativas) por tipo</strong></td></td></tr>
			<tr>
			<th width="20%">Quantidade/Tipo</th>
			<th width="30%">Quantidade/Etapa</th>
			<th width="50%">Quantidade/Municipio</th>
			</tr>
			<tr>
			';

			//implantações:
				//tipo
				echo '<td width="20%" valign="top">'.$implantacao_num.' Implantações</td><td valign="top" width="80%" colspan=2>';
				for($i=0;$i<sizeof($i_etapa_nome);$i++){
					//etapa
					echo '<table width="100%"><tr><td valign="top" width="30%" rowspan='.sizeof($i_municipio_nome[$i]).'>'.$i_etapa_total[$i].' '.$i_etapa_nome[$i].' </td><td width="50%"><table>';
					for($j=0;$j<sizeof($i_municipio_nome[$i]);$j++){
						//municipio
						echo '<tr><td valign="top" style="padding-left:7px;">'.$i_municipio_total[$i][$j].' '.$i_municipio_nome[$i][$j].'</td></tr>';
					}
					echo '</table><br><br></td></table>';
				}
				echo '</td></tr>';

			//renovações:
				//tipo
				echo '<tr><td width="20%" valign="top">'.$renovacao_num.' Renovações</td><td valign="top" colspan=2>';
				for($i=0;$i<sizeof($r_etapa_nome);$i++){
					//etapa
					echo '<table width="100%"><tr><td valign="top" width="30%" rowspan='.sizeof($r_municipio_nome[$i]).'>'.$r_etapa_total[$i].' '.$r_etapa_nome[$i].' </td><td width="50%"><table>';
					for($j=0;$j<sizeof($r_municipio_nome[$i]);$j++){
						//municipio
						echo '<tr><td valign="top" style="padding-left:7px;">'.$r_municipio_total[$i][$j].' '.$r_municipio_nome[$i][$j].'</td></tr>';
					}
					echo '</table><br><br></td></table>';
				}
				echo '</td></tr>';

		//fim tabela
		echo '</table><br><hr>';

		}else{
			echo 'Nenhuma comissão de ética encontrada!<br><br>';
		}


		?>

		<form id="view_relatorios" name="view_relatorios" action="view_relatorios.php" method="post" class="form-horizontal noprint">
			<div class="form-group" style="margin-top:40px;">
		      <div class="col-lg-10">
		      	<button type="reset" id="cancelar" class="btn btn-default index_relatorios.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
		      </div>
		    </div>
		</form>

		<?php
		//fecha divs de conteúdo
		echo "</div></div>";




	}
	//FIM REL1------------------------------------------------------------------------------------------------------------------------------------------------- FIM REL1








	//REL2-------------------------------------------------------------------------------------------------------------------------------------------------REL2
	if($_POST["tipo"] == "rel2"){

		echo "<div id=\"conteudo_borda\"><div  id=\"conteudo\"><h5 class='onlyprint'>Gráficos Estatísticos</h5>";

		if($periodo_de!=NULL || $periodo_ate!=NULL){
			echo '<p>Período do Relatório: ';
			if($periodo_de!=NULL && $periodo_ate!=NULL){
				echo $periodo_de.' a '.$periodo_ate;
			}
			if($periodo_de!=NULL && $periodo_ate==NULL){
				echo 'a partir de '.$periodo_de;
			}
			if($periodo_ate!=NULL && $periodo_de==NULL){
				echo 'até '.$periodo_ate;
			}
			echo '</p>';
		}

		//definições para todos os gráficos
		$cores=	'	\'#3366CC\',\'#DC3912\',\'#FF9900\',\'#109618\',\'#b2cedc\', 
          			\'#990099\',\'#7b7b7b\',\'#0099C6\',\'#DD4477\',\'#66AA00\',
          			\'#B82E2E\',\'#316395\',\'#994499\',\'#CCCCCC\',\'#6b4409\',
          			\'#22AA99\',\'#AAAA11\',\'#6633CC\',\'#efcbf4\',\'#603102\',
          			\'#f2eeab\',\'#efe44c\',\'#c6b601\',\'#f7aaaa\',\'#6f95e2\',
          			\'#d88572\'';
		
		//Carrega Biblioteca do Google 
		//https://google-developers.appspot.com/chart/interactive/docs/datatables_dataviews
		echo '<script type="text/javascript" src="https://www.google.com/jsapi"></script>';
		//Produz os graficos
		echo '	<script type="text/javascript">

				google.load("visualization", "1", {packages:["corechart"]});

				//chama os gráficos criados abaixo ao carregar a página
				google.setOnLoadCallback(rel2_grafico1c);
				google.setOnLoadCallback(rel2_grafico1c_andamento);
				google.setOnLoadCallback(rel2_grafico1b);
				google.setOnLoadCallback(rel2_grafico1b_andamento);
				google.setOnLoadCallback(rel2_grafico2);
				google.setOnLoadCallback(rel2_grafico2_andamento);
				google.setOnLoadCallback(rel2_grafico22);
				google.setOnLoadCallback(rel2_grafico22_andamento);
				google.setOnLoadCallback(rel2_grafico3);
				google.setOnLoadCallback(rel2_grafico4);
				google.setOnLoadCallback(rel2_grafico5);
				google.setOnLoadCallback(rel2_grafico6);
				google.setOnLoadCallback(rel2_grafico8);
				google.setOnLoadCallback(rel2_grafico9);
				google.setOnLoadCallback(rel2_grafico10);
				google.setOnLoadCallback(rel2_grafico11);
				google.setOnLoadCallback(rel2_grafico15);
				google.setOnLoadCallback(rel2_grafico1);


				//Cria o gráfico 1
			      function rel2_grafico1() {
			      	//var para receber os dados do banco
			      	var dados = new Array(0,0);
					var resposta = $.ajax({
							type: \'POST\',
							url: \'ajax.php\',
							async: false,
							dataType: \'text\',
							data: {tipo:\'rel2_grafico1\',periodo_de:\''.$periodo_de.'\',periodo_ate:\''.$periodo_ate.'\'} }).responseText;
			  		//se cair aqui não encontrou a palavra "erro_filtro"
			  		if(resposta.indexOf("erro_filtro")==-1){
						dados = resposta.split("|||");
					}else{
						$(\'#rel2_grafico1\').prev(\'hr\').hide()
						$(\'#rel2_grafico1\').hide();
					}
					// Inicializa a variável de dados
					var data = new google.visualization.DataTable();
					// Adiciona as colunas
					data.addColumn(\'string\', \'Cidade\');
					data.addColumn(\'number\', \'Número de Processos\');
					// Adiciona o numero de linhas do resultado
					data.addRows(dados.length);
					//varre o array para inserir o nome do campo / valor
					for(var i=0; i < dados.length; i++){
						var aux2 = dados[i].split("&&&");
						data.setCell(i, 0, aux2[0]);//nome do campo
						data.setCell(i, 1, aux2[1]);//valor
					}
					//opções do gráfico
					//https://google-developers.appspot.com/chart/interactive/docs/gallery/piechart
			        var options = {
			          //título do gráfico
			          title: \'TOTAL HISTÓRICO DE COMISSÕES DE ÉTICA POR MUNICÍPIO\',
			          //define que o gráfico será 3d
			          is3D: true,
			          //texto exibido no gráfico
			          pieSliceText: true,
			          colors: ['.$cores.'],
			          //texto exibido abaixo do nome ao entrar com o mouse no gráfico
			          tooltip: {text: \'percentage\'},
			          //definições da legenda:
			          legend: {
			          	//posição
			          	position:\'right\',
			          	//tamanho da fonte
			          	textStyle: {fontSize: 13} 
			          }
			        };
			        //inicializa e desenha o gráfico
			        var chart = new google.visualization.PieChart(document.getElementById(\'rel2_grafico1\'));
			        chart.draw(data, options);
			      }


			    //Cria o gráfico 1b
			      function rel2_grafico1b() {
			      	//var para receber os dados do banco
			      	var dados = new Array(0,0);
					var resposta = $.ajax({
							type: \'POST\',
							url: \'ajax.php\',
							async: false,
							dataType: \'text\',
							data: {tipo:\'rel2_grafico1b\',periodo_de:\''.$periodo_de.'\',periodo_ate:\''.$periodo_ate.'\'} }).responseText;
			  		//se cair aqui não encontrou a palavra "erro_filtro"
			  		if(resposta.indexOf("erro_filtro")==-1){
						dados = resposta.split("|||");
					}else{
						$(\'#rel2_grafico1b\').prev(\'hr\').hide()
						$(\'#rel2_grafico1b\').hide();
					}
					// Inicializa a variável de dados
					var data = new google.visualization.DataTable();
					// Adiciona as colunas
					data.addColumn(\'string\', \'Cidade\');
					data.addColumn(\'number\', \'Número de Processos\');
					// Adiciona o numero de linhas do resultado
					data.addRows(dados.length);
					//varre o array para inserir o nome do campo / valor
					for(var i=0; i < dados.length; i++){
						var aux2 = dados[i].split("&&&");
						data.setCell(i, 0, aux2[0]);//nome do campo
						data.setCell(i, 1, aux2[1]);//valor
					}
					//opções do gráfico					
			        var options = {
			          //título do gráfico
			          title: \'TOTAL DE COMISSÕES DE ÉTICA ATIVAS POR MUNICÍPIO\',
			          //define que o gráfico será 3d
			          is3D: true,
			          //texto exibido no gráfico
			          pieSliceText: true,
			          colors: ['.$cores.'],
			          //texto exibido abaixo do nome ao entrar com o mouse no gráfico
			          tooltip: {text: \'percentage\'},
			          //definições da legenda:
			          legend: {
			          	//posição
			          	position:\'right\',
			          	//tamanho da fonte
			          	textStyle: {fontSize: 13} 
			          }
			        };
			        //inicializa e desenha o gráfico
			        var chart = new google.visualization.PieChart(document.getElementById(\'rel2_grafico1b\'));
			        chart.draw(data, options);
			      }

			    //Cria o gráfico 1b EM ANDAMENTO
			      function rel2_grafico1b_andamento() {
			      	//var para receber os dados do banco
			      	var dados = new Array(0,0);
					var resposta = $.ajax({
							type: \'POST\',
							url: \'ajax.php\',
							async: false,
							dataType: \'text\',
							data: {tipo:\'rel2_grafico1b_andamento\',periodo_de:\''.$periodo_de.'\',periodo_ate:\''.$periodo_ate.'\'} }).responseText;
			  		//se cair aqui não encontrou a palavra "erro_filtro"
			  		if(resposta.indexOf("erro_filtro")==-1){
						dados = resposta.split("|||");
					}else{
						$(\'#rel2_grafico1b_andamento\').prev(\'hr\').hide()
						$(\'#rel2_grafico1b_andamento\').hide();
					}
					// Inicializa a variável de dados
					var data = new google.visualization.DataTable();
					// Adiciona as colunas
					data.addColumn(\'string\', \'Cidade\');
					data.addColumn(\'number\', \'Número de Processos\');
					// Adiciona o numero de linhas do resultado
					data.addRows(dados.length);
					//varre o array para inserir o nome do campo / valor
					for(var i=0; i < dados.length; i++){
						var aux2 = dados[i].split("&&&");
						data.setCell(i, 0, aux2[0]);//nome do campo
						data.setCell(i, 1, aux2[1]);//valor
					}
					//opções do gráfico					
			        var options = {
			          //título do gráfico
			          title: \'TOTAL DE COMISSÕES DE ÉTICA EM ANDAMENTO POR MUNICÍPIO\',
			          //define que o gráfico será 3d
			          is3D: true,
			          //texto exibido no gráfico
			          pieSliceText: true,
			          colors: ['.$cores.'],
			          //texto exibido abaixo do nome ao entrar com o mouse no gráfico
			          tooltip: {text: \'percentage\'},
			          //definições da legenda:
			          legend: {
			          	//posição
			          	position:\'right\',
			          	//tamanho da fonte
			          	textStyle: {fontSize: 13} 
			          }
			        };
			        //inicializa e desenha o gráfico
			        var chart = new google.visualization.PieChart(document.getElementById(\'rel2_grafico1b_andamento\'));
			        chart.draw(data, options);
			      }

			    //Cria o gráfico 1c
			      function rel2_grafico1c() {
			      	//var para receber os dados do banco
			      	var dados = new Array(0,0);
					var resposta = $.ajax({
							type: \'POST\',
							url: \'ajax.php\',
							async: false,
							dataType: \'text\',
							data: {tipo:\'rel2_grafico1c\',periodo_de:\''.$periodo_de.'\',periodo_ate:\''.$periodo_ate.'\'} }).responseText;
			  		//se cair aqui não encontrou a palavra "erro_filtro"
			  		if(resposta.indexOf("erro_filtro")==-1){
						dados = resposta.split("|||");
					}else{
						$(\'#rel2_grafico1c\').prev(\'hr\').hide()
						$(\'#rel2_grafico1c\').hide();
					}
					// Inicializa a variável de dados
					var data = new google.visualization.DataTable();
					// Adiciona as colunas
					data.addColumn(\'string\', \'Subseção\');
					data.addColumn(\'number\', \'Número de Processos\');
					// Adiciona o numero de linhas do resultado
					data.addRows(dados.length);
					//varre o array para inserir o nome do campo / valor
					for(var i=0; i < dados.length; i++){
						var aux2 = dados[i].split("&&&");
						data.setCell(i, 0, aux2[0]);//nome do campo
						data.setCell(i, 1, aux2[1]);//valor
					}
					//opções do gráfico					
			        var options = {
			          //título do gráfico
			          title: \'TOTAL DE COMISSÕES DE ÉTICA ATIVAS POR SUBSEÇÃO\',
			          //define que o gráfico será 3d
			          is3D: true,
			          //texto exibido no gráfico
			          pieSliceText: true,
			          colors: ['.$cores.'],
			          //texto exibido abaixo do nome ao entrar com o mouse no gráfico
			          tooltip: {text: \'percentage\'},
			          //definições da legenda:
			          legend: {
			          	//posição
			          	position:\'right\',
			          	//tamanho da fonte
			          	textStyle: {fontSize: 13} 
			          }
			        };
			        //inicializa e desenha o gráfico
			        var chart = new google.visualization.PieChart(document.getElementById(\'rel2_grafico1c\'));
			        chart.draw(data, options);
			      }

			    //Cria o gráfico 1c (EM ANDAMENTO)
			      function rel2_grafico1c_andamento() {
			      	//var para receber os dados do banco
			      	var dados = new Array(0,0);
					var resposta = $.ajax({
							type: \'POST\',
							url: \'ajax.php\',
							async: false,
							dataType: \'text\',
							data: {tipo:\'rel2_grafico1c_andamento\',periodo_de:\''.$periodo_de.'\',periodo_ate:\''.$periodo_ate.'\'} }).responseText;
			  		//se cair aqui não encontrou a palavra "erro_filtro"
			  		if(resposta.indexOf("erro_filtro")==-1){
						dados = resposta.split("|||");
					}else{
						$(\'#rel2_grafico1c_andamento\').prev(\'hr\').hide()
						$(\'#rel2_grafico1c_andamento\').hide();
					}
					// Inicializa a variável de dados
					var data = new google.visualization.DataTable();
					// Adiciona as colunas
					data.addColumn(\'string\', \'Subseção\');
					data.addColumn(\'number\', \'Número de Processos\');
					// Adiciona o numero de linhas do resultado
					data.addRows(dados.length);
					//varre o array para inserir o nome do campo / valor
					for(var i=0; i < dados.length; i++){
						var aux2 = dados[i].split("&&&");
						data.setCell(i, 0, aux2[0]);//nome do campo
						data.setCell(i, 1, aux2[1]);//valor
					}
					//opções do gráfico					
			        var options = {
			          //título do gráfico
			          title: \'TOTAL DE COMISSÕES DE ÉTICA EM ANDAMENTO POR SUBSEÇÃO\',
			          //define que o gráfico será 3d
			          is3D: true,
			          //texto exibido no gráfico
			          pieSliceText: true,
			          colors: ['.$cores.'],
			          //texto exibido abaixo do nome ao entrar com o mouse no gráfico
			          tooltip: {text: \'percentage\'},
			          //definições da legenda:
			          legend: {
			          	//posição
			          	position:\'right\',
			          	//tamanho da fonte
			          	textStyle: {fontSize: 13} 
			          }
			        };
			        //inicializa e desenha o gráfico
			        var chart = new google.visualization.PieChart(document.getElementById(\'rel2_grafico1c_andamento\'));
			        chart.draw(data, options);
			      }


				//Cria o gráfico2
			      function rel2_grafico2() {
			      	//var para receber os dados do banco
			      	var dados = new Array(0,0);
					var resposta = $.ajax({
							type: \'POST\',
							url: \'ajax.php\',
							async: false,
							dataType: \'text\',
							data: {tipo:\'rel2_grafico2\',periodo_de:\''.$periodo_de.'\',periodo_ate:\''.$periodo_ate.'\'} }).responseText;
			  		//se cair aqui não encontrou a palavra "erro_filtro"
			  		if(resposta.indexOf("erro_filtro")==-1){
						dados = resposta.split("|||");
					}else{
						$(\'#rel2_grafico2\').prev(\'hr\').hide()
						$(\'#rel2_grafico2\').hide();
					}
					// Inicializa a variável de dados
					var data = new google.visualization.DataTable();
					// Adiciona as colunas
					data.addColumn(\'string\', \'Tipo\');
					data.addColumn(\'number\', \'Número de Processos\');
					// Adiciona o numero de linhas do resultado
					data.addRows(dados.length);
					//varre o array para inserir o nome do campo / valor
					for(var i=0; i < dados.length; i++){
						var aux2 = dados[i].split("&&&");
						data.setCell(i, 0, aux2[0]);//nome do campo
						data.setCell(i, 1, aux2[1]);//valor
					}
					//opções do gráfico					
			        var options = {
			          //título do gráfico
			          title: \'TOTAL DE COMISSÕES DE ÉTICA ATIVAS POR TIPO\',
			          //define que o gráfico será 3d
			          is3D: true,
			          //texto exibido no gráfico
			          pieSliceText: true,
			          colors: ['.$cores.'],
			          //texto exibido abaixo do nome ao entrar com o mouse no gráfico
			          tooltip: {text: \'percentage\'},
			          //definições da legenda:
			          legend: {
			          	//posição
			          	position:\'right\',
			          	//tamanho da fonte
			          	textStyle: {fontSize: 13} 
			          }
			        };
			        //inicializa e desenha o gráfico
			        var chart = new google.visualization.PieChart(document.getElementById(\'rel2_grafico2\'));
			        chart.draw(data, options);
			      }

			    //Cria o gráfico2 ANDAMENTO
			      function rel2_grafico2_andamento() {
			      	//var para receber os dados do banco
			      	var dados = new Array(0,0);
					var resposta = $.ajax({
							type: \'POST\',
							url: \'ajax.php\',
							async: false,
							dataType: \'text\',
							data: {tipo:\'rel2_grafico2_andamento\',periodo_de:\''.$periodo_de.'\',periodo_ate:\''.$periodo_ate.'\'} }).responseText;
			  		//se cair aqui não encontrou a palavra "erro_filtro"
			  		if(resposta.indexOf("erro_filtro")==-1){
						dados = resposta.split("|||");
					}else{
						$(\'#rel2_grafico2_andamento\').prev(\'hr\').hide()
						$(\'#rel2_grafico2_andamento\').hide();
					}
					// Inicializa a variável de dados
					var data = new google.visualization.DataTable();
					// Adiciona as colunas
					data.addColumn(\'string\', \'Tipo\');
					data.addColumn(\'number\', \'Número de Processos\');
					// Adiciona o numero de linhas do resultado
					data.addRows(dados.length);
					//varre o array para inserir o nome do campo / valor
					for(var i=0; i < dados.length; i++){
						var aux2 = dados[i].split("&&&");
						data.setCell(i, 0, aux2[0]);//nome do campo
						data.setCell(i, 1, aux2[1]);//valor
					}
					//opções do gráfico					
			        var options = {
			          //título do gráfico
			          title: \'TOTAL DE COMISSÕES DE ÉTICA EM ANDAMENTO POR TIPO\',
			          //define que o gráfico será 3d
			          is3D: true,
			          //texto exibido no gráfico
			          pieSliceText: true,
			          colors: ['.$cores.'],
			          //texto exibido abaixo do nome ao entrar com o mouse no gráfico
			          tooltip: {text: \'percentage\'},
			          //definições da legenda:
			          legend: {
			          	//posição
			          	position:\'right\',
			          	//tamanho da fonte
			          	textStyle: {fontSize: 13} 
			          }
			        };
			        //inicializa e desenha o gráfico
			        var chart = new google.visualization.PieChart(document.getElementById(\'rel2_grafico2_andamento\'));
			        chart.draw(data, options);
			      }


			    //Cria o gráfico22
			      function rel2_grafico22() {
			      	//var para receber os dados do banco
			      	var dados = new Array(0,0);
					var resposta = $.ajax({
							type: \'POST\',
							url: \'ajax.php\',
							async: false,
							dataType: \'text\',
							data: {tipo:\'rel2_grafico22\',periodo_de:\''.$periodo_de.'\',periodo_ate:\''.$periodo_ate.'\'} }).responseText;
			  		//se cair aqui não encontrou a palavra "erro_filtro"
			  		if(resposta.indexOf("erro_filtro")==-1){
						dados = resposta.split("|||");
					}else{
						$(\'#rel2_grafico22\').prev(\'hr\').hide()
						$(\'#rel2_grafico22\').hide();
					}
					// Inicializa a variável de dados
					var data = new google.visualization.DataTable();
					// Adiciona as colunas
					data.addColumn(\'string\', \'Dia\');
					data.addColumn(\'number\', \'Quantidade\');
					// Adiciona o numero de linhas do resultado
					data.addRows(dados.length);
					//varre o array para inserir o nome do campo / valor
					for(var i=0; i < dados.length; i++){
						var aux2 = dados[i].split("&&&");
						data.setCell(i, 0, aux2[0]);//nome do campo
						data.setCell(i, 1, aux2[1]);//valor
					}
					//opções do gráfico					
			        var options = {
			          //título do gráfico
			          title: \'TOTAL DE COMISSÕES DE ÉTICA ATIVAS POR ETAPA\',
			          //define que o gráfico será 3d
			          is3D: true,
			          //texto exibido no gráfico
			          pieSliceText: true,
			          colors: ['.$cores.'],
			          //texto exibido abaixo do nome ao entrar com o mouse no gráfico
			          tooltip: {text: \'percentage\'},
			          //definições da legenda:
			          legend: {
			          	//posição
			          	position:\'right\',
			          	//tamanho da fonte
			          	textStyle: {fontSize: 13} 
			          }
			        };
			        //inicializa e desenha o gráfico
			        var chart = new google.visualization.PieChart(document.getElementById(\'rel2_grafico22\'));
			        chart.draw(data, options);
			      }

				//Cria o gráfico22
			      function rel2_grafico22_andamento() {
			      	//var para receber os dados do banco
			      	var dados = new Array(0,0);
					var resposta = $.ajax({
							type: \'POST\',
							url: \'ajax.php\',
							async: false,
							dataType: \'text\',
							data: {tipo:\'rel2_grafico22_andamento\',periodo_de:\''.$periodo_de.'\',periodo_ate:\''.$periodo_ate.'\'} }).responseText;
			  		//se cair aqui não encontrou a palavra "erro_filtro"
			  		if(resposta.indexOf("erro_filtro")==-1){
						dados = resposta.split("|||");
					}else{
						$(\'#rel2_grafico22_andamento\').prev(\'hr\').hide()
						$(\'#rel2_grafico22_andamento\').hide();
					}
					// Inicializa a variável de dados
					var data = new google.visualization.DataTable();
					// Adiciona as colunas
					data.addColumn(\'string\', \'Dia\');
					data.addColumn(\'number\', \'Quantidade\');
					// Adiciona o numero de linhas do resultado
					data.addRows(dados.length);
					//varre o array para inserir o nome do campo / valor
					for(var i=0; i < dados.length; i++){
						var aux2 = dados[i].split("&&&");
						data.setCell(i, 0, aux2[0]);//nome do campo
						data.setCell(i, 1, aux2[1]);//valor
					}
					//opções do gráfico					
			        var options = {
			          //título do gráfico
			          title: \'TOTAL DE COMISSÕES DE ÉTICA EM ANDAMENTO POR ETAPA\',
			          //define que o gráfico será 3d
			          is3D: true,
			          //texto exibido no gráfico
			          pieSliceText: true,
			          colors: ['.$cores.'],
			          //texto exibido abaixo do nome ao entrar com o mouse no gráfico
			          tooltip: {text: \'percentage\'},
			          //definições da legenda:
			          legend: {
			          	//posição
			          	position:\'right\',
			          	//tamanho da fonte
			          	textStyle: {fontSize: 13} 
			          }
			        };
			        //inicializa e desenha o gráfico
			        var chart = new google.visualization.PieChart(document.getElementById(\'rel2_grafico22_andamento\'));
			        chart.draw(data, options);
			      }

			    //Cria o gráfico3
			      function rel2_grafico3() {
			      	//var para receber os dados do banco
			      	var dados = new Array();
					var resposta = $.ajax({
							type: \'POST\',
							url: \'ajax.php\',
							async: false,
							dataType: \'text\',
							data: {tipo:\'rel2_grafico3\',periodo_de:\''.$periodo_de.'\',periodo_ate:\''.$periodo_ate.'\'} }).responseText;
			  		//se cair aqui não encontrou a palavra "erro_filtro"
			  		if(resposta.indexOf("erro_filtro")==-1){
						dados = resposta.split("|||");
					}else{
						$(\'#rel2_grafico3\').prev(\'hr\').hide()
						$(\'#rel2_grafico3\').hide();
					}
					// Inicializa a variável de dados
					var data = new google.visualization.DataTable();
					// Adiciona as colunas
					data.addColumn(\'string\', \'Usuário\');
					data.addColumn(\'number\', \'Número de Ações\');
					// Adiciona o numero de linhas do resultado
					data.addRows(dados.length);
					//varre o array para inserir o nome do campo / valor
					for(var i=0; i < dados.length; i++){
						var aux2 = dados[i].split("&&&");
						data.setCell(i, 0, aux2[0]);//nome do campo
						data.setCell(i, 1, aux2[1]);//valor
					}
					//opções do gráfico					
			        var options = {
			          //título do gráfico
			          title: \'TOTAL DE AÇÕES POR USUÁRIO\',
			          //define que o gráfico será 3d
			          is3D: true,
			          //texto exibido no gráfico
			          pieSliceText: true,
			          colors: ['.$cores.'],
			          //texto exibido abaixo do nome ao entrar com o mouse no gráfico
			          tooltip: {text: \'percentage\'},
			          //definições da legenda:
			          legend: {
			          	//posição
			          	position:\'right\',
			          	//tamanho da fonte
			          	textStyle: {fontSize: 13} 
			          }
			        };
			        //inicializa e desenha o gráfico
			        var chart = new google.visualization.PieChart(document.getElementById(\'rel2_grafico3\'));
			        chart.draw(data, options);
			      }

				//Cria o gráfico4
			      function rel2_grafico4() {
			      	//var para receber os dados do banco
			      	var dados = new Array(0,0);
					var resposta = $.ajax({
							type: \'POST\',
							url: \'ajax.php\',
							async: false,
							dataType: \'text\',
							data: {tipo:\'rel2_grafico4\',periodo_de:\''.$periodo_de.'\',periodo_ate:\''.$periodo_ate.'\'} }).responseText;
			  		//se cair aqui não encontrou a palavra "erro_filtro"
			  		if(resposta.indexOf("erro_filtro")==-1){
						dados = resposta.split("|||");
					}else{
						$(\'#rel2_grafico4\').prev(\'hr\').hide()
						$(\'#rel2_grafico4\').hide();
					}
					// Inicializa a variável de dados
					var data = new google.visualization.DataTable();
					// Adiciona as colunas
					data.addColumn(\'string\', \'Perfil\');
					data.addColumn(\'number\', \'Número de Ações\');
					// Adiciona o numero de linhas do resultado
					data.addRows(dados.length);
					//varre o array para inserir o nome do campo / valor
					for(var i=0; i < dados.length; i++){
						var aux2 = dados[i].split("&&&");
						data.setCell(i, 0, aux2[0]);//nome do campo
						data.setCell(i, 1, aux2[1]);//valor
					}
					//opções do gráfico					
			        var options = {
			          //título do gráfico
			          title: \'TOTAL DE AÇÕES POR PERFIL\',
			          //define que o gráfico será 3d
			          is3D: true,
			          //texto exibido no gráfico
			          pieSliceText: true,
			          colors: ['.$cores.'],
			          //texto exibido abaixo do nome ao entrar com o mouse no gráfico
			          tooltip: {text: \'percentage\'},
			          //definições da legenda:
			          legend: {
			          	//posição
			          	position:\'right\',
			          	//tamanho da fonte
			          	textStyle: {fontSize: 13} 
			          }
			        };
			        //inicializa e desenha o gráfico
			        var chart = new google.visualization.PieChart(document.getElementById(\'rel2_grafico4\'));
			        chart.draw(data, options);
			      }

				//Cria o gráfico5
			      function rel2_grafico5() {
			      	//var para receber os dados do banco
			      	var dados = new Array(0,0);
					var resposta = $.ajax({
							type: \'POST\',
							url: \'ajax.php\',
							async: false,
							dataType: \'text\',
							data: {tipo:\'rel2_grafico5\',periodo_de:\''.$periodo_de.'\',periodo_ate:\''.$periodo_ate.'\'} }).responseText;
			  		//se cair aqui não encontrou a palavra "erro_filtro"
			  		if(resposta.indexOf("erro_filtro")==-1){
						dados = resposta.split("|||");
					}else{
						$(\'#rel2_grafico5\').prev(\'hr\').hide()
						$(\'#rel2_grafico5\').hide();
					}
					// Inicializa a variável de dados
					var data = new google.visualization.DataTable();
					// Adiciona as colunas
					data.addColumn(\'string\', \'Documento\');
					data.addColumn(\'number\', \'Quantidade\');
					// Adiciona o numero de linhas do resultado
					data.addRows(dados.length);
					//varre o array para inserir o nome do campo / valor
					for(var i=0; i < dados.length; i++){
						var aux2 = dados[i].split("&&&");
						data.setCell(i, 0, aux2[0]);//nome do campo
						data.setCell(i, 1, aux2[1]);//valor
					}
					//opções do gráfico					
			        var options = {
			          //título do gráfico
			          title: \'TOTAL DE DOCUMENTOS POR TIPO\',
			          //define que o gráfico será 3d
			          is3D: true,
			          //texto exibido no gráfico
			          pieSliceText: true,
			          colors: ['.$cores.'],
			          //texto exibido abaixo do nome ao entrar com o mouse no gráfico
			          tooltip: {text: \'percentage\'},
			          //definições da legenda:
			          legend: {
			          	//posição
			          	position:\'right\',
			          	//tamanho da fonte
			          	textStyle: {fontSize: 13} 
			          }
			        };
			        //inicializa e desenha o gráfico
			        var chart = new google.visualization.PieChart(document.getElementById(\'rel2_grafico5\'));
			        chart.draw(data, options);
			      }

			    //Cria o gráfico6
			      function rel2_grafico6() {
			      	//var para receber os dados do banco
			      	var dados = new Array(0,0);
					var resposta = $.ajax({
							type: \'POST\',
							url: \'ajax.php\',
							async: false,
							dataType: \'text\',
							data: {tipo:\'rel2_grafico6\',periodo_de:\''.$periodo_de.'\',periodo_ate:\''.$periodo_ate.'\'} }).responseText;
			  		//se cair aqui não encontrou a palavra "erro_filtro"
			  		if(resposta.indexOf("erro_filtro")==-1){
						dados = resposta.split("|||");
					}else{
						$(\'#rel2_grafico6\').prev(\'hr\').hide()
						$(\'#rel2_grafico6\').hide();
					}
					// Inicializa a variável de dados
					var data = new google.visualization.DataTable();
					// Adiciona as colunas
					data.addColumn(\'string\', \'Mês\');
					data.addColumn(\'number\', \'Quantidade\');
					// Adiciona o numero de linhas do resultado
					data.addRows(dados.length);
					//varre o array para inserir o nome do campo / valor
					for(var i=0; i < dados.length; i++){
						var aux2 = dados[i].split("&&&");
						data.setCell(i, 0, aux2[0]);//nome do campo
						data.setCell(i, 1, aux2[1]);//valor
					}
					//opções do gráfico					
			        var options = {
			          //título do gráfico
			          title: \'TOTAL DE AÇÕES REALIZADAS NOS ÚLTIMOS MESES\',
			          //define que o gráfico será 3d
			          is3D: true,
			          //texto exibido no gráfico
			          pieSliceText: true,
			          colors: ['.$cores.'],
			          //texto exibido abaixo do nome ao entrar com o mouse no gráfico
			          tooltip: {text: \'percentage\'},
			          //definições da legenda:
			          legend: {
			          	//posição
			          	position:\'right\',
			          	//tamanho da fonte
			          	textStyle: {fontSize: 13} 
			          }
			        };
			        //inicializa e desenha o gráfico
			        var chart = new google.visualization.PieChart(document.getElementById(\'rel2_grafico6\'));
			        chart.draw(data, options);
			      }


			      //Cria o gráfico - PERFIL COMISSAO DE ETICA
			      function rel2_grafico8() {
			      	//var para receber os dados do banco
			      	var dados = new Array(0,0);
					var resposta = $.ajax({
							type: \'POST\',
							url: \'ajax.php\',
							async: false,
							dataType: \'text\',
							data: {tipo:\'rel2_grafico8\',perfil:\''.PERFIL_IDCOMISSAOETICA.'\',periodo_de:\''.$periodo_de.'\',periodo_ate:\''.$periodo_ate.'\'} }).responseText;
			  		//se cair aqui não encontrou a palavra "erro_filtro"
			  		if(resposta.indexOf("erro_filtro")==-1){
						dados = resposta.split("|||");
					}else{
						$(\'#rel2_grafico8\').prev(\'hr\').hide()
						$(\'#rel2_grafico8\').hide();
					}
					// Inicializa a variável de dados
					var data = new google.visualization.DataTable();
					// Adiciona as colunas
					data.addColumn(\'string\', \'Usuário\');
					data.addColumn(\'number\', \'Quantidade\');
					// Adiciona o numero de linhas do resultado
					data.addRows(dados.length);
					//varre o array para inserir o nome do campo / valor
					for(var i=0; i < dados.length; i++){
						var aux2 = dados[i].split("&&&");
						data.setCell(i, 0, aux2[0]);//nome do campo
						data.setCell(i, 1, aux2[1]);//valor
					}
					//opções do gráfico					
			        var options = {
			          //título do gráfico
			          title: \'TOTAL DE DOCUMENTOS VISUALIZADOS PELA COMISSÃO DE ÉTICA\',
			          //define que o gráfico será 3d
			          is3D: true,
			          //texto exibido no gráfico
			          pieSliceText: true,
			          colors: ['.$cores.'],
			          //texto exibido abaixo do nome ao entrar com o mouse no gráfico
			          tooltip: {text: \'percentage\'},
			          //definições da legenda:
			          legend: {
			          	//posição
			          	position:\'right\',
			          	//tamanho da fonte
			          	textStyle: {fontSize: 13} 
			          }
			        };
			        //inicializa e desenha o gráfico
			        var chart = new google.visualization.PieChart(document.getElementById(\'rel2_grafico8\'));
			        chart.draw(data, options);
			      }


				//Cria o gráfico - PERFIL INSTITUICAO
			      function rel2_grafico9() {
			      	//var para receber os dados do banco
			      	var dados = new Array(0,0);
					var resposta = $.ajax({
							type: \'POST\',
							url: \'ajax.php\',
							async: false,
							dataType: \'text\',
							data: {tipo:\'rel2_grafico8\',perfil:\''.PERFIL_IDINSTITUICAO.'\',periodo_de:\''.$periodo_de.'\',periodo_ate:\''.$periodo_ate.'\'} }).responseText;
			  		//se cair aqui não encontrou a palavra "erro_filtro"
			  		if(resposta.indexOf("erro_filtro")==-1){
						dados = resposta.split("|||");
					}else{
						$(\'#rel2_grafico9\').prev(\'hr\').hide()
						$(\'#rel2_grafico9\').hide();
					}
					// Inicializa a variável de dados
					var data = new google.visualization.DataTable();
					// Adiciona as colunas
					data.addColumn(\'string\', \'Usuário\');
					data.addColumn(\'number\', \'Quantidade\');
					// Adiciona o numero de linhas do resultado
					data.addRows(dados.length);
					//varre o array para inserir o nome do campo / valor
					for(var i=0; i < dados.length; i++){
						var aux2 = dados[i].split("&&&");
						data.setCell(i, 0, aux2[0]);//nome do campo
						data.setCell(i, 1, aux2[1]);//valor
					}
					//opções do gráfico					
			        var options = {
			          //título do gráfico
			          title: \'TOTAL DE DOCUMENTOS VISUALIZADOS PELAS INSTITUIÇÕES\',
			          //define que o gráfico será 3d
			          is3D: true,
			          //texto exibido no gráfico
			          pieSliceText: true,
			          colors: ['.$cores.'],
			          //texto exibido abaixo do nome ao entrar com o mouse no gráfico
			          tooltip: {text: \'percentage\'},
			          //definições da legenda:
			          legend: {
			          	//posição
			          	position:\'right\',
			          	//tamanho da fonte
			          	textStyle: {fontSize: 13} 
			          }
			        };
			        //inicializa e desenha o gráfico
			        var chart = new google.visualization.PieChart(document.getElementById(\'rel2_grafico9\'));
			        chart.draw(data, options);
			      }

			    //Cria o gráfico - PERFIL FISCALIZAÇÃO
			      function rel2_grafico10() {
			      	//var para receber os dados do banco
			      	var dados = new Array(0,0);
					var resposta = $.ajax({
							type: \'POST\',
							url: \'ajax.php\',
							async: false,
							dataType: \'text\',
							data: {tipo:\'rel2_grafico8\',perfil:\''.PERFIL_IDFISCALIZACAO.'\',periodo_de:\''.$periodo_de.'\',periodo_ate:\''.$periodo_ate.'\'} }).responseText;
			  		//se cair aqui não encontrou a palavra "erro_filtro"
			  		if(resposta.indexOf("erro_filtro")==-1){
						dados = resposta.split("|||");
					}else{
						dados = "";
						$(\'#rel2_grafico10\').prev(\'hr\').hide()
						$(\'#rel2_grafico10\').hide();
					}
					// Inicializa a variável de dados
					var data = new google.visualization.DataTable();
					// Adiciona as colunas
					data.addColumn(\'string\', \'Usuário\');
					data.addColumn(\'number\', \'Quantidade\');
					// Adiciona o numero de linhas do resultado
					data.addRows(dados.length);
					//varre o array para inserir o nome do campo / valor
					for(var i=0; i < dados.length; i++){
						var aux2 = dados[i].split("&&&");
						data.setCell(i, 0, aux2[0]);//nome do campo
						data.setCell(i, 1, aux2[1]);//valor
					}
					//opções do gráfico					
			        var options = {
			          //título do gráfico
			          title: \'TOTAL DE DOCUMENTOS VISUALIZADOS PELA FISCALIZAÇÃO\',
			          //define que o gráfico será 3d
			          is3D: true,
			          //texto exibido no gráfico
			          pieSliceText: true,
			          colors: ['.$cores.'],
			          //texto exibido abaixo do nome ao entrar com o mouse no gráfico
			          tooltip: {text: \'percentage\'},
			          //definições da legenda:
			          legend: {
			          	//posição
			          	position:\'right\',
			          	//tamanho da fonte
			          	textStyle: {fontSize: 13} 
			          }
			        };
			        //inicializa e desenha o gráfico
			        var chart = new google.visualization.PieChart(document.getElementById(\'rel2_grafico10\'));
			        chart.draw(data, options);
			      }

			    //Cria o gráfico - PERFIL SECRETARIA
			      function rel2_grafico11() {
			      	//var para receber os dados do banco
			      	var dados = new Array(0,0);
					var resposta = $.ajax({
							type: \'POST\',
							url: \'ajax.php\',
							async: false,
							dataType: \'text\',
							data: {tipo:\'rel2_grafico8\',perfil:\''.PERFIL_IDSECRETARIA.'\',periodo_de:\''.$periodo_de.'\',periodo_ate:\''.$periodo_ate.'\'} }).responseText;
			  		//se cair aqui não encontrou a palavra "erro_filtro"
			  		if(resposta.indexOf("erro_filtro")==-1){
						dados = resposta.split("|||");
					}else{
						dados = "";
						$(\'#rel2_grafico11\').prev(\'hr\').hide()
						$(\'#rel2_grafico11\').hide();
					}
					// Inicializa a variável de dados
					var data = new google.visualization.DataTable();
					// Adiciona as colunas
					data.addColumn(\'string\', \'Usuário\');
					data.addColumn(\'number\', \'Quantidade\');
					// Adiciona o numero de linhas do resultado
					data.addRows(dados.length);
					//varre o array para inserir o nome do campo / valor
					for(var i=0; i < dados.length; i++){
						var aux2 = dados[i].split("&&&");
						data.setCell(i, 0, aux2[0]);//nome do campo
						data.setCell(i, 1, aux2[1]);//valor
					}
					//opções do gráfico					
			        var options = {
			          //título do gráfico
			          title: \'TOTAL DE DOCUMENTOS VISUALIZADOS PELA SECRETARIA\',
			          //define que o gráfico será 3d
			          is3D: true,
			          //texto exibido no gráfico
			          pieSliceText: true,
			          colors: ['.$cores.'],
			          //texto exibido abaixo do nome ao entrar com o mouse no gráfico
			          tooltip: {text: \'percentage\'},
			          //definições da legenda:
			          legend: {
			          	//posição
			          	position:\'right\',
			          	//tamanho da fonte
			          	textStyle: {fontSize: 13} 
			          }
			        };
			        //inicializa e desenha o gráfico
			        var chart = new google.visualization.PieChart(document.getElementById(\'rel2_grafico11\'));
			        chart.draw(data, options);
			      }


			    //Cria o gráfico - LOGINS POR DISPOSITIVOS DIFERENTES
			      function rel2_grafico15() {
			      	//var para receber os dados do banco
			      	var dados = new Array(0,0);
					var resposta = $.ajax({
							type: \'POST\',
							url: \'ajax.php\',
							async: false,
							dataType: \'text\',
							data: {tipo:\'rel2_grafico15\',periodo_de:\''.$periodo_de.'\',periodo_ate:\''.$periodo_ate.'\'} }).responseText;
			  		//se cair aqui não encontrou a palavra "erro_filtro"
			  		if(resposta.indexOf("erro_filtro")==-1){
						dados = resposta.split("|||");
					}else{
						dados = "";
						$(\'#rel2_grafico15\').prev(\'hr\').hide()
						$(\'#rel2_grafico15\').hide();
					}
					// Inicializa a variável de dados
					var data = new google.visualization.DataTable();
					// Adiciona as colunas
					data.addColumn(\'string\', \'Usuário\');
					data.addColumn(\'number\', \'Quantidade\');
					// Adiciona o numero de linhas do resultado
					data.addRows(dados.length);
					//varre o array para inserir o nome do campo / valor
					for(var i=0; i < dados.length; i++){
						var aux2 = dados[i].split("&&&");
						data.setCell(i, 0, aux2[0]);//nome do campo
						data.setCell(i, 1, aux2[1]);//valor
					}
					//opções do gráfico					
			        var options = {
			          //título do gráfico
			          title: \'DISPOSITIVOS UTILIZADOS PARA ACESSO AO SISTEMA\',
			          //define que o gráfico será 3d
			          is3D: true,
			          //texto exibido no gráfico
			          pieSliceText: true,
			          colors: ['.$cores.'],
			          //texto exibido abaixo do nome ao entrar com o mouse no gráfico
			          tooltip: {text: \'percentage\'},
			          //definições da legenda:
			          legend: {
			          	//posição
			          	position:\'right\',
			          	//tamanho da fonte
			          	textStyle: {fontSize: 13} 
			          }
			        };
			        //inicializa e desenha o gráfico
			        var chart = new google.visualization.PieChart(document.getElementById(\'rel2_grafico15\'));
			        chart.draw(data, options);
			      }

      </script>

			<center>
				<div id="rel2_grafico1c" style="width: 850px; height: 650px;"></div>
				<hr>
				<div id="rel2_grafico1c_andamento" style="width: 850px; height: 650px;"></div>
				<hr>
				<div id="rel2_grafico1b" style="width: 850px; height: 650px;"></div>
				<hr>
				<div id="rel2_grafico1b_andamento" style="width: 850px; height: 650px;"></div>
				<hr>
				<div id="rel2_grafico2" style="width: 850px; height: 500px;"></div>
				<hr>
				<div id="rel2_grafico2_andamento" style="width: 850px; height: 500px;"></div>
				<hr>
				<div id="rel2_grafico22" style="width: 850px; height: 500px;"></div>
				<hr>
				<div id="rel2_grafico22_andamento" style="width: 850px; height: 500px;"></div>
				<hr>
				<div id="rel2_grafico3" style="width: 850px; height: 500px;"></div>
				<hr>
				<div id="rel2_grafico4" style="width: 850px; height: 500px;"></div>
				<hr>
				<div id="rel2_grafico5" style="width: 850px; height: 500px;"></div>
				<hr>
				<div id="rel2_grafico6" style="width: 850px; height: 500px;"></div>
				<hr>
				<div id="rel2_grafico8" style="width: 850px; height: 500px;"></div>
				<hr>
				<div id="rel2_grafico9" style="width: 850px; height: 500px;"></div>
				<hr>
				<div id="rel2_grafico10" style="width: 850px; height: 500px;"></div>
				<hr>
				<div id="rel2_grafico11" style="width: 850px; height: 500px;"></div>
				<hr>
				<div id="rel2_grafico15" style="width: 850px; height: 500px;"></div>
				<hr>
				<div id="rel2_grafico1" style="width: 850px; height: 650px;"></div>
			</center>
		';

		?>

		<form id="view_relatorios" name="view_relatorios" action="view_relatorios.php" method="post" class="form-horizontal noprint">
			<div class="form-group" style="margin-top:40px;">
		      <div class="col-lg-10">
		      	<button type="reset" id="cancelar" class="btn btn-default index_relatorios.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
		      </div>
		    </div>
		</form>
    
    
<?php
	}
	//FIM REL2------------------------------------------------------------------------------------------------------------------------------------------------- FIM REL2





//se não tiver sido informado nada na variavel $_POST["tipo"], o usuário é redirecionado p/ index_relatorios.php
}else{
	echo "<meta http-equiv=\"refresh\" content=\"0; url=index_relatorios.php\">";
	exit();
}

include_once("../menu_rodape.php"); 

//else do verificaFuncaoUsuario(FUNCAO_HISTORICO_ALL)
}else{

		//acesso negado
		enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou foram dados inválidos");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
		exit();

}
?>