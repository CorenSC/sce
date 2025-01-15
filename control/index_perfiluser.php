<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");


if(verificaFuncaoUsuario(FUNCAO_USUARIO_ADD)!==false || verificaFuncaoUsuario(FUNCAO_USUARIO_EDIT)!==false || verificaFuncaoUsuario(FUNCAO_USUARIO_DEL)!==false){

	//carrega as bibliotecas para recuperar informações do BD
	//conecta no banco e instacia uma conexão com o Registry
	require_once("../conexao.php");
	require_once("../model/Registry.php");
	// Armazenar essa instância (conexão) no Registry - conecta uma só vez
	$registry = Registry::getInstance();
	$registry->set('Connection', $myBD);
	//carrega DAO's das chaves estrangeiras
	require_once('../dao/PerfilDAO.php');
	require_once('../model/Perfil.php');

	require_once("../menu_topo.php");

	//SUBMENU DE AÇÕES
	require_once('submenu_user.php');
	//FIM SUBMENU DE AÇÕES

	// Instanciar o DAO e retornar dados do banco
	$perfilDAO = new PerfilDAO();
	if(isset($_GET["order"]) && ($_GET["order"]=="nomeperfil")&&($_GET["ascdesc"]=="ASC" || $_GET["ascdesc"]=="DESC") ){
		$results = $perfilDAO->index($_GET["order"],$_GET["ascdesc"]);
	}else{
		$results = $perfilDAO->index();
	}

	echo "<div id=\"conteudo_borda\"><div  id=\"conteudo\"><h5 class='onlyprint'>Listagem de Perfis</h5>";


	if(sizeof($results)>0){ 
		$dados = array();
		$perfil_anterior="";
		$perfis = array();
		$idperfis=array();
		for($i=0;$i<sizeof($results);$i++){
			if($perfil_anterior!=$results[$i]["nomeperfil"]){
				$perfis[]=$results[$i]["nomeperfil"];
				$idperfis[]=$results[$i]["idperfil"];
				$perfil_anterior=$results[$i]["nomeperfil"];
			}
			$dados[$results[$i]["nomeperfil"]][]=$results[$i]["nomefunc"];
		}

	echo '
	<table class="table table-condensed table-responsive table-hover">
	    <thead>
	      <tr>
	        <th>Perfil</th>
	        <th>Funções Atreladas</th>
	        <th class="noprint">Ações</th>
	      </tr>
	    </thead>
	    <tbody>
		';

		
		for($i=0;$i<sizeof($perfis);$i++){
			
				echo "
				<tr id=\"perfil_".$idperfis[$i]."\">
				<td scope=\"row\">".$perfis[$i]."</td><td>";

				if($dados[$perfis[$i]][0]!=""){
					//imprime o nome das funções que o perfil está atrelado
					for($j=0;$j<sizeof($dados[$perfis[$i]]);$j++){
						echo ($j+1).' - '.$dados[$perfis[$i]][$j].';<br>';
					}

				}else{
					echo '-';
				}

				echo "</td><td><nobr>&nbsp;";
				
				//verifica as permissóes do usuário
				if(verificaFuncaoUsuario(FUNCAO_PERFIL_EDIT)){
					echo "<button class=\"btn btn-success edit_perfil\" aria-label=\"Left Align\" type=\"button\" title=\"Editar perfil\" id=\"perfil_".$idperfis[$i]."\"> <span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span> </button> ";
				}
				if(verificaFuncaoUsuario(FUNCAO_PERFIL_DEL) && $idperfis[$i]>19){
					echo "&nbsp;<button class=\"btn btn-warning perfil_del\" aria-label=\"Left Align\" type=\"button\" title=\"Excluir perfil\"> <span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span> </button> ";
				}
				
				echo "</nobr></td></tr>";

		}
		echo '</tbody>
	    </table>';
	}else{
		echo 'Nenhum perfil encontrado!';
	}

	?>

	<?php

		echo '</div><div class="form-group">
	  <div class="col-lg-10">
	    <button type="reset" id="cancelar" class="btn btn-default index_user.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
	  </div>
	</div>';

	include_once("../menu_rodape.php");

//else do if(verificaFuncaoUsuario(FUNCAO_USUARIO_ADD)!==false || verificaFuncaoUsuario(FUNCAO_USUARIO_EDIT)!==false || verificaFuncaoUsuario(FUNCAO_USUARIO_DEL)!==false){
}else{

	enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou dados inválidos");
	echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
	exit();

}
?>