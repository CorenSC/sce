<?php

//SUBMENU DE AÇÕES
echo "<div id=\"conteudo_borda\" class=\"noprint\"><div class=\"well submenu\" title=\"Submenu de ações de um processo\">";
//botão EDITAR PROCESSO
if( verificaFuncaoUsuario(FUNCAO_PROCESSO_EDIT) ){
  //só exibe botão se o processo não estiver bloqueado ou for admin
  if(!isset($bloquearProcesso) || !$bloquearProcesso || isAdmin()){
	echo "<button class=\"";
	//se estiver na pagina referente, desabilita o botão
 	if($paginaAtual=="edit_pro.php" ){ echo "disabled "; }
  echo "btn btn-success\"  value=\"edit_pro.php?p=".$idprocesso."&r=index_doc.php\" title=\"Alterar informações do Processo\" aria-label=\"Left Align\" type=\"button\"> 
          <span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span> <strong>EDITAR</strong>
        </button>";
  }
}
//botão EXCLUIR PROCESSO
if( verificaFuncaoUsuario(FUNCAO_PROCESSO_DEL) ){
  echo " &nbsp;
        <button class=\"btn btn-warning processo_del\"  id=\"processo_".$idprocesso."\" title=\"Remover Processo\" aria-label=\"Left Align\" type=\"button\"> 
          <span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span> <strong>REMOVER</strong>
        </button>";
}
//botão ADD DOCUMENTO
if( verificaFuncaoUsuario(FUNCAO_DOCUMENTO_ADD) ){
  //se o usuário pode mesmo ver este botão
  if((!isset($bloquearProcesso) || !$bloquearProcesso) && (!isset($usuarioEfetuaAcoes) || $usuarioEfetuaAcoes) && (isset($numdocs) && sizeof($numdocs)>=1)){
  	echo "&nbsp;&nbsp;<button class=\"";
  	//se estiver na pagina referente, desabilita o botão
   	if($paginaAtual=="add_doc.php"){ echo "disabled "; }
    echo "btn btn-info CliqueAqui\" value=\"add_doc.php?p=".$idprocesso."\" title=\"Adicione um documento ao processo para avançar à próxima etapa\" type=\"button\" aria-label=\"Left Align\">   
            <span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> <strong> DOCUMENTO</strong>
          </button>";
  }
}
//botão HISTORICO DO PROCESSO
if( verificaFuncaoUsuario(FUNCAO_HISTORICO_PRO) ){
	echo "&nbsp;&nbsp;<button class=\"";
	//se estiver na pagina referente, desabilita o botão
 	if($paginaAtual=="view_historico_pro.php"){ echo "disabled "; }
	echo "btn btn-primary\" value=\"view_historico_pro.php?p=".$idprocesso."\" title=\"Visualizar Histórico do Processo\" type=\"button\" aria-label=\"Left Align\">    
          <span class=\"glyphicon glyphicon-book\" aria-hidden=\"true\"></span> <strong>HISTÓRICO</strong>
        </button>";
}
//botão MODELOS DE DOCUMENTOS

echo "&nbsp;&nbsp;
    <button class=\"btn btn-primary\" value=\"index_modelo.php?p=".$idprocesso."&r=index_doc.php&showAllRecords=true\" title=\"Visualizar Modelos de Documentos\" type=\"button\" aria-label=\"Left Align\">   
        <span class=\"glyphicon glyphicon-inbox\" aria-hidden=\"true\"></span> <strong>MODELOS DE DOCUMENTOS</strong>
      </button>";

echo "</div></div>";
//FIM SUBMENU DE AÇÕES

?>