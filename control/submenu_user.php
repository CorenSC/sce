<?php
//SUBMENU DE AÇÕES
	echo "<div id=\"conteudo_borda\" class=\"noprint\"><div class=\"well submenu\" title=\"Submenu de ações dos usuários\">";

	//verifica se há alguma busca já enviada
	if( isset($_GET["busca"]) && !empty($_GET["busca"]) ){

		//armazena na variavel busca o valor de busca
		$busca = $_GET["busca"];

	//se não tiver uma busca definida:
	}else{
		$busca=NULL;
	}


	//adiciona busca
	if($busca != NULL){
		echo 'Exibindo resultados da busca  <a href="#" id="botao_busca_processo">( <span class="glyphicon glyphicon-eye-open"></span> clique aqui para visualizar ou alterar os parâmetros)</a>';

	}else{
		echo '<button type="submit" class="btn btn-primary" id="botao_busca_processo"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> <strong>BUSCAR USUÁRIO</strong></button>&nbsp;&nbsp;';
	}

echo '		<form name="index_user" method="get" action="index_user.php" class="form-inline div_busca_processo">
			
			<div class="form-group">
				<input placeholder="Nome do Usuário" style="width:250px;" class="form-control" maxlenght="'.USUARIO_NOME_SIZE.'" type="text" id="busca" name="busca" value="'.$busca.'">
			</div>

				<div class="div_busca_processo">';
if($busca != NULL){

	echo '		<button id="botao_cancela_busca_processo" type="reset" class="btn btn-default"><span class="glyphicon glyphicon-eye-close"></span> Ocultar parâmetros</button>';
	echo '		&nbsp;&nbsp;<button id="botao_remove_busca_usuario" type="reset" class="btn btn-warning"><span class="glyphicon glyphicon-remove"></span> Remover parâmetros</button>&nbsp;&nbsp;';
}else{
	echo '		<button id="botao_cancela_busca_processo" type="reset" class="btn btn-default">Cancelar</button>&nbsp;&nbsp;';	
}
	echo '<button id="botao_envia_busca_processo" type="submit" class="btn btn-primary">Buscar</button>
		        </div>
			</form>';


	if(verificaFuncaoUsuario(FUNCAO_USUARIO_ADD)!==false){
		echo "  <button class=\"";
		if($paginaAtual=="add_user.php"){ echo "disabled "; }
	 	echo "btn btn-info\" value=\"add_user.php\" title=\"Adicionar Usuário\" type=\"button\" aria-label=\"Left Align\">		
			  		<span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> <strong> USUÁRIO</strong>
			  	</button>";
	}
	echo "&nbsp;&nbsp;";
	echo "  <button class=\"";
	if($paginaAtual=="index_perfiluser.php"){ echo "disabled "; }
	echo "btn btn-primary\" value=\"index_perfiluser.php\" title=\"Visualizar Perfis\" type=\"button\" aria-label=\"Left Align\">		
			<span class=\"glyphicon glyphicon-th-list\" aria-hidden=\"true\"></span> <strong>LISTAR PERFIS</strong>
		</button>";
	if(verificaFuncaoUsuario(FUNCAO_PERFIL_ADD)!==false){
	echo "&nbsp;&nbsp<button class=\"";
	if($paginaAtual=="add_perfiluser.php"){ echo "disabled "; }
	 		echo "btn btn-info\" value=\"add_perfiluser.php\" title=\"Adicionar Perfil\" type=\"button\" aria-label=\"Left Align\">		
			  		<span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> <strong> PERFIL</strong>
			  	</button>";
	}
	echo "</div></div>";
//FIM SUBMENU DE AÇÕES
?>