<?php

$aux = explode("/",$_SERVER['PHP_SELF']);
$paginaAtual = $aux[count($aux)-1];//ex: add_user.php

header("Content-Type: text/html; charset=".APP_CHARSET,true);

?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo APP_CHARSET; ?>" />
<title><?php echo APP_TITLE; ?></title>
<link rel="shortcut icon" href="<?php echo APP_URL.'/favicon.ico'; ?>" />
</head>
<body>
<div id="msg_erro" class="alert alert-danger noprint" role="alert"></div>
<div id="msg_sucesso" class="alert alert-success noprint" role="alert"></div>
<div id="msg_atencao" class="alert alert-warning noprint" role="alert"></div>
<?php
  //se houver mensagens exibe as mesmas aqui (veja arquivo \bin\functions.php)
  if(temMsg()){
    exibeMsg();
  }
?>
<div class="navbar navbar-default">
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a href="index.php"><span class="navbar-brand" title="<?php echo APP_TITLE; ?>"><?php echo APP_SYS_LOGO; ?></span></a>
  </div>
  <div class="navbar-collapse collapse navbar-responsive-collapse">
    <ul class="nav navbar-nav navbar-left">
      <li id="index" title="Início">
        <a href="index.php"><span class="glyphicon glyphicon-home" aria-hidden="true"></span> Início</a>
      </li>
<?php

//item PROCESSO - visualização diferenciada para perfis do tipo instituição
  if(isInstituicao()){
    echo "<li id=\"index_pro\" title=\"Visualizar Meu Processo\">
            <a href=\"index_pro.php\"><span class=\"glyphicon glyphicon-screenshot\" aria-hidden=\"true\"></span> Meu Processo</a>
          </li>";
  }else{
    echo "<li id=\"index_pro\" title=\"Visualizar Processos\">
          <a href=\"index_pro.php\"><span class=\"glyphicon glyphicon-screenshot\" aria-hidden=\"true\"></span> Processo</a>
          </li>";
  }

//item USUARIOS
/*
if( 
        strpos(trim($paginaAtual),"index.php")!==false  
    ||  strpos(trim($paginaAtual),"index_user.php")!==false  
    ||  strpos(trim($paginaAtual),"add_user.php")!==false  
    ||  strpos(trim($paginaAtual),"edit_user.php")!==false  
    ||  strpos(trim($paginaAtual),"index_perfiluser.php")!==false  
    ||  strpos(trim($paginaAtual),"add_perfiluser.php")!==false  
    ||  strpos(trim($paginaAtual),"edit_perfiluser.php")!==false  
){
*/  
  if(verificaFuncaoUsuario(FUNCAO_USUARIO_ADD)!==false 
    || verificaFuncaoUsuario(FUNCAO_USUARIO_EDIT)!==false 
    || verificaFuncaoUsuario(FUNCAO_USUARIO_DEL)!==false){
    echo '<li id="index_user" title="Visualizar usuários">
        <a href="index_user.php"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Usuários</a>
      </li>';
  }
/*}*/


//item OUTROS
/*
if(   strpos(trim($paginaAtual),"add_pro.php")
  || strpos(trim($paginaAtual),"t_pro.php") 
  || strpos(trim($paginaAtual),"historico_pro.php")
  || strpos(trim($paginaAtual),"entidadetipo.php") 
  || strpos(trim($paginaAtual),"modelo.php") ){
*/
  
//se não for instituição exibe dropdown para escolha das opções "Outros"
if(!isInstituicao()){

  echo "
    <li id=\"index_outros\" class=\"dropdown\" title=\"Outras opções - Clique para expandi-las\">
      <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">&nbsp;<span class=\"glyphicon glyphicon-cog\" aria-hidden=\"true\"></span>&nbsp;</a>
      <ul class=\"dropdown-menu\">";

        //HISTORICO GERAL
        if(verificaFuncaoUsuario(FUNCAO_HISTORICO_ALL)){
          echo "<li class=\"view_historico\"><a href=\"view_historico.php\" title=\"Visualizar histórico geral do sistema\"><button class=\"btn btn-info\" aria-label=\"Left Align\" type=\"button\"><span class=\"glyphicon glyphicon-book\" aria-hidden=\"true\"></span> Histórico do sistema</button></a></li>";
        }

        //RELATÓRIOS
        if(verificaFuncaoUsuario(FUNCAO_HISTORICO_ALL)){
          echo "<li role=\"separator\" class=\"divider\"></li>
          <li class=\"index_relatorios view_relatorios\"><a href=\"index_relatorios.php\" title=\"Relatórios\"><button class=\"btn btn-info\" aria-label=\"Left Align\" type=\"button\"><span class=\"glyphicon glyphicon-signal\" aria-hidden=\"true\"></span> Relatórios</button></a></li>";
        }

        //ETAPAS
        if(verificaFuncaoUsuario(FUNCAO_ETAPA_EDIT)){
          echo "<li role=\"separator\" class=\"divider\"></li>
          <li class=\"index_etapa\"><a href=\"index_etapa.php?showAllRecords=true\" title=\"Etapas do processo\"><button class=\"btn btn-info\" aria-label=\"Left Align\" type=\"button\"><span class=\"glyphicon glyphicon-flag\" aria-hidden=\"true\"></span> Etapas</button></a></li>";
        }

        //MODELOS DE DOCUMENTO
        echo "<li role=\"separator\" class=\"divider\"></li>
              <li class=\"index_modelo add_modelo edit_modelo\"><a href=\"index_modelo.php?showAllRecords=true\" title=\"Modelos de Documentos\"><button class=\"btn btn-info\" aria-label=\"Left Align\" type=\"button\"><span class=\"glyphicon glyphicon-inbox\" aria-hidden=\"true\"></span> Modelos de Documentos</button></a></li>";

        //SUBSEÇÃO
        if(verificaFuncaoUsuario(FUNCAO_SUBSECAO_ADD)){
          echo "<li role=\"separator\" class=\"divider\"></li>
                <li class=\"index_subsecao add_subsecao edit_subsecao\"><a href=\"index_subsecao.php\" title=\"Subseções\"><button class=\"btn btn-info\" aria-label=\"Left Align\" type=\"button\"><span class=\"glyphicon glyphicon-th\" aria-hidden=\"true\"></span> Subseções</button></a></li>";
        }        
        
        //TIPO DE DOCUMENTO
        if(verificaFuncaoUsuario(FUNCAO_DOCUMENTOTIPO_ADD)){
          echo "<li role=\"separator\" class=\"divider\"></li>
                <li class=\"index_doctipo add_doctipo edit_doctipo\"><a href=\"index_doctipo.php?showAllRecords=true\" title=\"Tipos de Documento\"><button class=\"btn btn-info\" aria-label=\"Left Align\" type=\"button\"><span class=\"glyphicon glyphicon-tags\" aria-hidden=\"true\"></span> Tipos de Documento</button></a></li>";
        }

  echo "</ul></li>";
}  
	  ?>
    </ul>
    <ul class="nav navbar-nav navbar-right">
      <?php

    echo "<li class=\"topo_acoes\" style=\"margin-top:2px !important;\" title=\"Ajuda - clique para ver o manual de utilização do sistema\">
              <a target=\"_blank\" href=\"../Manual_SCE_20180207.pdf\">
                <span class=\"glyphicon glyphicon-question-sign\" aria-hidden=\"true\"></span>
              </a>
          </li>
          <li class=\"topo_acoes\" title=\"Imprimir página\">
          <a>
            <span class=\"glyphicon glyphicon-print small\" aria-hidden=\"true\"></span>
          </a>
          </li>
          <li style=\"cursor:default !important;\" id=\"edit_user_php\"  title=\"Clique aqui para alterar sua senha ou e-mail.\">
          
          <a href=\"edit_user.php\" style=\"color:#337ab7;\">
            <span style=\"cursor:default !important;color:#337ab7;\" class=\"glyphicon glyphicon-log-in\" aria-hidden=\"true\"></span> ".$_SESSION["USUARIO"]["login"]."
          </a>
          </li>";
      ?>
      <li>
        <a class="sair" href="<?php echo APP_URL; ?>/logout.php"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span> Sair</a>
      </li>
    </ul>
  </div>
</div>