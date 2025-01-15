<?php
//SUBMENU DE AÇÕES
if(verificaFuncaoUsuario(FUNCAO_ETAPA_ADD)!==false){
echo "<div id=\"conteudo_borda\" class=\"noprint\"><div class=\"well submenu\" title=\"Submenu de ações das etapas\">";
  echo "  <button class=\"";
  //se estiver na pagina referente, desabilita o botão
  if($paginaAtual=="add_etapa.php"){ echo "disabled "; }
  echo "btn btn-info\" value=\"add_etapa.php\" title=\"Adicionar Etapa\" type=\"button\" aria-label=\"Left Align\">    
          <span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> <strong>ETAPA</strong>
        </button>";
echo "</div></div>";
}
//FIM SUBMENU DE AÇÕES
?>