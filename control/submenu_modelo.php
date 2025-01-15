<?php
//SUBMENU DE AÇÕES

//botão ADD modelo
if(verificaFuncaoUsuario(FUNCAO_MODELO_ADD)!==false){
echo "<div id=\"conteudo_borda\" class=\"noprint\"><div class=\"well submenu\" title=\"Submenu de ações dos modelos de documentos\">";
  echo "  <button class=\"";
  //se estiver na pagina referente, desabilita o botão
  if($paginaAtual=="add_modelo.php"){ echo "disabled "; }
  echo "btn btn-info\" value=\"add_modelo.php\" title=\"Adicionar Modelo de Documento\" type=\"button\" aria-label=\"Left Align\">		
	  		<span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> <strong> MODELO</strong>
	  	</button>";
echo "</div></div>";
}

//FIM SUBMENU DE AÇÕES
?>