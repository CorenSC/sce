<?php

	$anterior = $pc-1; 
	$proximo = $pc+1; 
	if(isset($_GET["order"])){
		$order="&order=".$_GET["order"];
	}else{
		$order="";
	}
	if(isset($_GET["ascdesc"])){
		$ascdesc="&ascdesc=".$_GET["ascdesc"];
	}else{
		$ascdesc="";
	}
	//verifica se não há outros parametros, se não tiver, apenas seta a variavel com valor ''
	if(!isset($outrosParametrosPaginacao) || empty($outrosParametrosPaginacao)){
		$outrosParametrosPaginacao='';
	}
	echo "</div></div><div class='div_paginacao'>Total: <strong>$tr</strong> registro";
	if($tr>1 || $tr==0){
		echo "s";//para ficar registroS quando forem mais de 1.
	}
	echo ". ";

	if(!isset($_GET["showAllRecords"])){

		//caso não tenha sido setado para exibir todos os registros e haja mais de uma página, exiba:
		if(!isset($_GET["showAllRecords"]) && $tp>1){
			echo "<a class='show_all_records noprint'>(Não paginar)</a>";
		}

		echo "<br> </div>";
		if ($pc>1 || $pc<$tp) {
			echo "<div class='div_paginacao'>";
		}	
		if ($pc>1){
			//se a página atual NÃO for apenas 1 só da primeira página exibe o <<
			if(!($pc==2)){
				echo "<a class='btn-primary paginacao_nav noprint' href='#' rel='?pagina=1$order$ascdesc".$outrosParametrosPaginacao."'><button type='button' class='btn btn-primary' title='Primeira Página'><span class='glyphicon glyphicon-chevron-left'></span><span class='glyphicon glyphicon-chevron-left'></span></button></a> ";
			}
			echo "<a class='btn-primary paginacao_nav noprint' href='#' rel='?pagina=$anterior$order$ascdesc".$outrosParametrosPaginacao."'><button type='button' class='btn btn-primary' title='Página Anterior'><span class='glyphicon glyphicon-chevron-left'></span></button></a> ";		
		}
		if ($pc>1 || $pc<$tp) {
			echo "<button type='button' class='btn btn-default bt_pag_atual' title='Você está na página número $pc.'>Página $pc de $tp2</button> ";
		}
		if ($pc<$tp){
			echo "<a class='btn-primary paginacao_nav noprint' href='#' rel='?pagina=$proximo$order$ascdesc".$outrosParametrosPaginacao."'><button type='button' class='btn btn-primary' title='Próxima Página'><span class='glyphicon glyphicon-chevron-right'></span></button></a> ";
			//se a página atual NÃO for apenas 1 só da última página exibe o >>
			if(!($pc==$tp2-1)){
				echo " <a class='btn-primary paginacao_nav noprint' href='#' rel='?pagina=$tp2$order$ascdesc".$outrosParametrosPaginacao."'><button type='button' class='btn btn-primary' title='Última Página'><span class='glyphicon glyphicon-chevron-right'></span><span class='glyphicon glyphicon-chevron-right'></span></button></a> ";
			}
		}

	}

	if ($pc>1 || $pc<$tp) {
		echo "</div>";
	}
?>