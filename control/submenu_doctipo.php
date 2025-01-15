<?php
//SUBMENU DE AÇÕES
//botão ADD
if(verificaFuncaoUsuario(FUNCAO_DOCUMENTOTIPO_ADD)!==false){
	echo "<div id=\"conteudo_borda\" class=\"noprint\"><div class=\"well submenu\" title=\"Submenu de ações dos tipos de documentos\">";
	echo "<button class=\"";
	//se estiver na pagina referente, desabilita o botão
	if($paginaAtual=="add_doctipo.php"){ echo "disabled "; }
	echo "btn btn-info\" value=\"add_doctipo.php\" title=\"Adicionar Tipo de Documento\" type=\"button\" aria-label=\"Left Align\">		
					<span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> <strong>TIPO DE DOCUMENTO</strong>
				</button>
			</div></div>";
}
//FIM SUBMENU DE AÇÕES
?>