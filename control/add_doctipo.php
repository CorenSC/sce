<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");

if(verificaFuncaoUsuario(FUNCAO_DOCUMENTOTIPO_ADD)!==false){

//conecta no banco e instacia uma conexão com o Registry
require_once("../conexao.php");
require_once("../model/Registry.php");
//carrega DAO's das chaves estrangeiras
require_once("../dao/DocumentoTipoDAO.php");
//carrega Models
require_once('../model/DocumentoTipo.php');

// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);

require_once("../menu_topo.php");

//SUBMENU DE AÇÕES
require_once('submenu_doctipo.php');
//FIM SUBMENU DE AÇÕES

//se tiver enviado o formulário
if(isset($_POST) && !empty($_POST) ){

	//valida entradas do usuário
	$nome=validaLiteral($_POST["nome"],DOCUMENTOTIPO_NOME_SIZE);

	// se os campos obrigatórios passarem na validação
	if( $nome!==false){

		//valida para entrar na área de segurança da SQL
		$nome=sqlTrataString($nome);

		//se entrar nesse IF os dados já podem ser inseridos no banco
		if($nome!==false){

				  
			// Instanciar as infos do usuario
			$DocumentoTipo = new DocumentoTipo();
			$DocumentoTipo->setNome($nome);		
			// Instanciar o DAO para inserir o usuario na base
			$DocumentoTipoDAO = new DocumentoTipoDAO();
			// Chama a função que verifica duplicidade do registro
			$possuiDuplicidade = $DocumentoTipoDAO->isDuplicated($DocumentoTipo);
			if($possuiDuplicidade!==false){
				enviaMsg("erro","Tipo de documento não cadastrado","O nome <strong>".$nome."</strong> já existe, por favor escolha outro nome");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doctipo.php\">";
				exit();
			}
			
			//insere registro
			$iddocumentotipo = $DocumentoTipoDAO->insert($DocumentoTipo);
			//atribui ID via método do Usuário
			$DocumentoTipo->setId($iddocumentotipo);
			if($iddocumentotipo){				

				//registra ação no log				
				$log_obs = $DocumentoTipo->toLog();
				
				require_once("../dao/HistoricoDAO.php");
				require_once("../model/Historico.php");
				$Historico = new Historico();
				$Historico->setAcao(LOG_ADD_TIPODOC);
				$Historico->setProcesso(0);
				$Historico->setDocumento(0);
				$Historico->setObs(sqlTrataString($log_obs));
				$HistoricoDAO = new HistoricoDAO();
				$HistoricoDAO->insert($Historico);

			}//fim if($idprocesso)			
			
			//cadastrado
			if($iddocumentotipo && $HistoricoDAO){				
				enviaMsg("sucesso","Tipo de documento cadastrado com sucesso");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doctipo.php\">";
				exit();
			//não cadastrado
			}else{				
				enviaMsg("erro","Tipo de documento cadastrado com erros","O histórico não pôde ser salvo");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doctipo.php\">";
				exit();
			}
			
			

			
			
		}else{

			//não cadastrado - dados invalidos
			enviaMsg("erro","Tipo de documento não cadastrado","Os dados fornecidos estavam inválidos, tente novamente.");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doctipo.php\">";
			exit();

		}
		
	}else{
		
		//não cadastrado - dados invalidos
		enviaMsg("erro","Tipo de documento não cadastrado","Os dados fornecidos são inválidos, tente novamente.");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doctipo.php\">";
		exit();
		
	}

}//fim do IF do POST
else{

?>

<div id="conteudo_borda">
    <div  id="conteudo">
		<!-- <form enctype="multipart/form-data" name="add_documentotipo" action="add_doctipo.php" method="post" class="form-horizontal" onSubmit="return validaForm('add_documentotipo','nome');" > -->
        <form id="add_documentotipo" name="add_documentotipo" action="add_doctipo.php" method="post" class="form-horizontal">

            <div class="form-group">
                <label class="col-sm-10 control-label">Nome do Tipo de Documento</label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome do Tipo de Documento" maxlength="<?php echo DOCUMENTOTIPO_NOME_SIZE-20; ?>">
                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
                </div>
            </div>
            
            <div class="form-group">
              <div class="col-lg-10">
                <button type="reset" id="cancelar" class="btn btn-default index_doctipo.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                <button type="submit" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salvar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
              </div>
            </div>

        </form>
	</div>
</div>
<?php
include_once("../menu_rodape.php");

}//fim do else do POST

//else do verificaFuncaoUsuario
}else{

		enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou dados inválidos");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
		exit();

}
?>
