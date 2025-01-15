<?php
//SUBMENU DE AÇÕES

//botão ADD subsecao
if(verificaFuncaoUsuario(FUNCAO_SUBSECAO_ADD)!==false){
echo "<div id=\"conteudo_borda\" class=\"noprint\"><div class=\"well submenu\" title=\"Submenu de ações das subseções\">";
  echo "  <button class=\"";
  //se estiver na pagina referente, desabilita o botão
  if($paginaAtual=="add_subsecao.php"){ echo "disabled "; }
  echo "btn btn-info\" value=\"add_subsecao.php\" title=\"Adicionar Subseção\" type=\"button\" aria-label=\"Left Align\">    
          <span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> <strong>SUBSEÇÃO</strong>
        </button>";
echo "</div></div>";
}

//FIM SUBMENU DE AÇÕES
?>