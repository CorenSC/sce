<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");

if(verificaFuncaoUsuario(FUNCAO_PROCESSO_ADD)!==false){

//conecta no banco e instacia uma conexão com o Registry
require_once("../conexao.php");
require_once("../model/Registry.php");
// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);
//carrega DAO
require_once('../dao/ProcessoDAO.php');
require_once("../dao/UsuarioDAO.php");
require_once("../dao/HistoricoDAO.php");
require_once("../dao/DocumentoDAO.php");
require_once("../dao/EtapaDAO.php");
require_once("../dao/ResponsavelDAO.php");
//carrega Model
require_once('../model/Etapa.php');
require_once('../model/Processo.php');	
require_once("../model/Historico.php");
require_once("../model/Documento.php");
require_once("../model/Responsavel.php");

require_once("../menu_topo.php");

//SUBMENU DE AÇÕES PROCESSO
require_once('submenu_pro.php');
//FIM SUBMENU DE AÇÕES

//consulta número correto para seguir a ordem de inserção
$ProcessoDAO = new ProcessoDAO();
//descobre o novo número para o processo
$numero = $ProcessoDAO->getNumero();
$numero = $numero["numero"];
if(!$numero || $numero<1){
	$numero=1;
}

//se tiver enviado o formulário (IF Nº1)
if( !empty($_POST) && isset($_POST) ){
	
	//verificação dos dados obrigatórios (se formato e tamanho conferem)
	$processotipo=		validaInteiro($_POST["processotipo"],	PROCESSOTIPO_ID_SIZE);
	$etapa=				validaInteiro($_POST["etapa"],			ETAPA_ID_SIZE);
	$usuario=			validaInteiro($_POST["instituicao"],	USUARIO_ID_SIZE);
	$posse=				validaInteiro($_POST["posse"],	1);
	$militar=			validaInteiro($_POST["militar"],		PROCESSO_MILITAR_SIZE);
	$modo=				validaInteiro($_POST["modo"],			ETAPA_MODO_SIZE);

	//se os dados obrigatórios passarem na validação: (IF Nº2)
	if( $processotipo!==false && $etapa!==false && $usuario!==false && $posse!==false){
				
		$dtescolhida=false;
		$dtposse1=false;
		$dtposse2=false;
		$dtposse3=false;
		$obsposse=false;

		//se for escolhido SIM para pergunta "possui data de posse" 
		if($posse == 1){
			if(isset($_POST["dtescolhida"]) && sizeof($_POST["dtescolhida"])>0){
				$dtescolhida=sqlTrataString($_POST["dtescolhida"]);
			}	

		//se for escolhido NAO para pergunta "possui data de posse"
		}else{
			if(isset($_POST["dtposse1"]) && sizeof($_POST["dtposse1"])>0){
				$dtposse1=sqlTrataString($_POST["dtposse1"]);
			}
			if(isset($_POST["dtposse2"]) && sizeof($_POST["dtposse2"])>0){
				$dtposse2=sqlTrataString($_POST["dtposse2"]);
			}
			if(isset($_POST["dtposse3"]) && sizeof($_POST["dtposse3"])>0){
				$dtposse3=sqlTrataString($_POST["dtposse3"]);
			}
			if(isset($_POST["obsposse"]) && sizeof($_POST["obsposse"])>0){
				$obsposse=sqlTrataString($_POST["obsposse"]);
			}
		}

		//valida para entrar na área de segurança da SQL
		$processotipo=	sqlTrataInteiro($processotipo);
		$etapa=			sqlTrataInteiro($etapa);
		$usuario=		sqlTrataInteiro($usuario);
		$posse=			sqlTrataInteiro($posse);
		$militar=		sqlTrataInteiro($militar);
		$modo=			sqlTrataInteiro($modo);

		//se os dados obrigatórios passarem na validação de SQL: (IF Nº3)
		if( $processotipo!==false && $etapa!==false && $usuario!==false && $posse!==false && $militar!==false && $modo!==false){

			// Instanciar as infos do processo
			$Processo = new Processo();
			// Instanciar o DAO para inserir na base
			$ProcessoDAO = new ProcessoDAO();

			$Processo->setUsuario($usuario);
			$Processo->setProcessoTipo($processotipo);
			$Processo->setEtapa($etapa);
			$Processo->setNumero($numero);
			$Processo->setDtCriacao(date("Ymd"));
			$Processo->setModo($modo);
			$Processo->setMilitar($militar);
			//se é instituição "militar", altera modo para "sem eleições"
			if($militar==PROCESSO_MILITAR){
				$Processo->setModo(PROCESSOETAPA_SEMELEICOES);
			}

			//se for escolhido SIM para pergunta "possui data de posse" 
			if($posse == 1){

				if($dtescolhida!==false && $dtescolhida>0){
					$Processo->setDtEscolhida(transformaDataTimestampBanco($dtescolhida));
				}else{
					$Processo->setDtEscolhida(NULL);
				}

			//se for escolhido NAO para pergunta "possui data de posse"
			}else{

				if($dtposse1!==false && $dtposse1>0){
					$Processo->setDtPosse1(transformaDataTimestampBanco($dtposse1));
				}else{
					$Processo->setDtPosse1(NULL);
				}
				if($dtposse2!==false && $dtposse2>0){
					$Processo->setDtPosse2(transformaDataTimestampBanco($dtposse2));
				}else{
					$Processo->setDtPosse2(NULL);
				}
				if($dtposse3!==false && $dtposse3>0){
					$Processo->setDtPosse3(transformaDataTimestampBanco($dtposse3));
				}else{
					$Processo->setDtPosse3(NULL);
				}
				if($obsposse!==false && strlen($obsposse)>0){
					$Processo->setObsPosse($obsposse);
				}else{
					$Processo->setObsPosse(NULL);
				}

			}
			
			// Chama a função de inserção que só resulta FALSE caso dê algum problema.
			// retorna para $idprocesso o último ID inserido, no caso o idprocesso
			$idprocesso = $ProcessoDAO->insert($Processo);
			//variáveis para Log:
			$responsavel_log="";
			$responsavel_num=0;
			$responsavel_array=array();
			if($idprocesso!==false){

				$Processo->setId($idprocesso);

				//depois de inserir o processo, insere o andamento do processo em etapa_processo
				$EtapaDAO = new EtapaDAO();
				$arrayEtapas = $EtapaDAO->getAll();
				$EtapaProcesso = new Etapa();
				//varre o array de etapas para encontrar a posicao da etapa escolhida
				foreach ($arrayEtapas as $e) {
					//ao encontrar a etapa, atribui informações ao objeto Etapa e insere este histórico
					if($e["idetapa"] == $etapa){
						$EtapaProcesso->setId($e["idetapa"]);
						$EtapaProcesso->setProcesso($idprocesso);
						$EtapaProcesso->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
						$EtapaProcesso->setAprova($e["aprova"]);
						$EtapaProcesso->setAprovaMsg(NULL);
						$EtapaProcesso->setPrazo($e["prazo"]);
						//insere etapa_processo
						$inseriuEtapaProcesso = $EtapaDAO->insertEtapaProcesso($EtapaProcesso);
						//insere prazo da etapa para o processo
						//calcula o prazo de acordo com o valor em dias que vier do getPrazo
						if($EtapaProcesso->getPrazo()>0){
							$dtprazo=date('Ymd', strtotime('+'.$EtapaProcesso->getPrazo().' days'));
						}else{
							$dtprazo=0;
						}							
						$Processo->setPrazo($dtprazo);
			            $atualizouDtPrazo = $ProcessoDAO->updateDtPrazo($Processo);
						//da break para sair do foreach
						break;
					}
				}

				//verifica se o usuário escolheu responsaveis
				for($i=1;$i<50;$i++){
					//se selecionou algum RESPONSAVEL, e esse Responsável não foi adicionado ainda (evita duplicados)
					if( isset($_POST["re_idusuario_".$i]) && $_POST["re_idusuario_".$i]>0 && !in_array($_POST["re_idusuario_".$i], $responsavel_array)){
						//armazenar informações
						$idusuario=$_POST["re_idusuario_".$i];

						// Instanciar as infos do Responsavel
						$Responsavel = new Responsavel();
						$Responsavel->setProcesso($idprocesso);
						$Responsavel->setUsuario($idusuario);
						// Instanciar o DAO para inserir na base
						$ResponsavelDAO = new ResponsavelDAO();
						// Chama a função de inserção que só resulta FALSE caso dê algum problema.
						$inseriu = $ResponsavelDAO->insert($Responsavel);
						if(!$inseriu){
							enviaMsg("erro","Erro","O processo foi cadastrado porém não foi possível salvar os responsáveis pelo mesmo");
							echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doc.php?p=$idprocesso\">";
							exit();
						}

						//se Membro da CEC e SE inseriu como responsável, adiciona ao array de Processos do $_SESSION esse novo processo, assim ele poderá acessar o processo
						if(isMembroComissaoEtica() && $idusuario == $_SESSION['USUARIO']['idusuario']){
							$_SESSION['USUARIO']['processos'][]=$idprocesso;
						}


						//adiciona ao array de responsáveis
						$responsavel_array[]=$idusuario;
						//armazena infos para acrescentar ao log:
						$responsavel_num++;
						$responsavel_log.=" Responsável ".$responsavel_num.": ID ".$idusuario.APP_LINE_BREAK;
					}
				}//fim for varrendo responsaveis
				
				//Recupera e altera dados para salvar os valores e não os IDS no histórico
				$UsuarioDAO = new UsuarioDAO();
				$dado=$UsuarioDAO->getOne($usuario);
				$Processo->setUsuario($dado["nome"]);
				
				$dado=$ProcessoDAO->getOneTipo($processotipo);
				$Processo->setProcessoTipo($dado["nome"]);

				$dado=$EtapaDAO->getOne($etapa);
				$Processo->setEtapa($dado["ordem"]." - ".$dado["nome"]);

				//salva OBS para registro no LOG:
				$obs_log=$Processo->toLog();
				//adiciona trecho dos responsáveis ao log
				if($responsavel_num>0){
					$obs_log.=APP_LINE_BREAK."Responsáveis:".APP_LINE_BREAK.$responsavel_log;
				}else{
					$obs_log.=APP_LINE_BREAK."Nenhum responsável definido";
				}
				
				//se inseriu o idprocesso
				//inseriu (quando houver) irregularidades e fiscais
				//o processo todo foi finalizado com sucesso => SALVAR NO HISTÓRICO
				$Historico = new Historico();
				$Historico->setAcao(LOG_ADD_PRO);
				$Historico->setProcesso($idprocesso);
				$Historico->setObs(sqlTrataString($obs_log));
				$HistoricoDAO = new HistoricoDAO();
				$inseriuLog=$HistoricoDAO->insert($Historico);
				//se cair aqui é pq DEU TUDO CERTO!
				if($inseriuLog){

					enviaMsg("sucesso","Processo inserido com sucesso");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doc.php?p=$idprocesso\">";
					exit();				

				//se cair aqui é pq não inseriu o log					
				}else{
					enviaMsg("erro","Erro","O processo foi cadastrado porém não foi possível salvar o histórico");
					echo "<meta http-equiv=\"refresh\" content=\"0; url=index_doc.php?p=$idprocesso\">";
					exit();
				}

			}else{
				//se cair aqui é pq não inseriu o processo
				enviaMsg("erro","Erro","O processo não pôde ser inserido");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
				exit();
			}
			


		//fim (IF Nº3)
		}else{

			enviaMsg("erro","Processo não inserido","Os campos obrigatórios não foram preenchidos corretamente.");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
			exit();

		}




	//fim do if validação dos campos obrigatórios passou (IF Nº2)
	}else{

		enviaMsg("erro","Processo não inserido","Os campos obrigatórios não foram preenchidos corretamente. Tente novamente.");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
		exit();

	}

//fim do IF Nº1
//se não tiver sido enviado nenhum POST para página, exiba as informações abaixo:
}else{

?>

<div id="conteudo_borda">
    <div  id="conteudo">
        <!-- <form name="add_pro" action="add_pro.php" method="post" class="form-horizontal" onSubmit="return validaForm('add_pro','numero','processotipo','etapa','instituicao','posse');" > -->
        <form id="add_pro" name="add_pro" action="add_pro.php" method="post" class="form-horizontal">

        	<div class="form-group">
	            <label for="numero" class="col-sm-12 control-label">Número do Processo</label>
			    <div class="col-sm-2">
			      <input type="text" class="form-control" id="numero" name="numero" value="<?php echo $numero; ?>" disabled="disabled">
			      <br>
			    </div>
			</div>

            <div class="form-group">
            	<label for="processotipo" class="col-sm-12 control-label">Tipo do Processo</label>
			    <div class="col-sm-2">
			      <select id="processotipo" name="processotipo" class="form-control">
					<option value="-1">Selecione o tipo do processo&nbsp;&nbsp;&nbsp;&nbsp;</option>
					<?php
                    // Instanciar o DAO e retornar dados da tabela
                    $ProcessoDAO = new ProcessoDAO();
                    $result = $ProcessoDAO->getTipos();
                    if(sizeof($result)>0){
						for($i=0;$i<sizeof($result);$i++){
								echo "<option value=\"".
								$result[$i]["idprocessotipo"]."\">".
								$result[$i]["nome"]."</option>";
						}
					}
					?>
                  </select>
			      <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
			    </div>
            </div>

            <div class="form-group">
            	<label for="etapa" class="col-sm-12 control-label">Etapa</label>
			    <div class="col-sm-2">
			      <select id="etapa" name="etapa" class="form-control">
					<option value="-1">Selecione a etapa atual do processo&nbsp;&nbsp;&nbsp;&nbsp;</option>
					<?php
                    // Instanciar o DAO e retornar dados da tabela TIPO
                    $EtapaDAO = new EtapaDAO();
                    //getAll(1) pois só queremos etapas do fluxo principal
                    $result = $EtapaDAO->getAll();
                    if(sizeof($result)>0){
						for($i=0;$i<sizeof($result);$i++){		
							echo "<option value=\"".$result[$i]["idetapa"]."\">";
							//se é do fluxo alternativo dá um espaço antes de exibir o número da ordem
							if($result[$i]["fluxo"]==1){
								echo "&nbsp;&nbsp;&nbsp;";
							}
							echo $result[$i]["ordem"]." - ".$result[$i]["nome"]."</option>";
						}
					}
					?>
                  </select>
			      <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
			    </div>			    
            </div>

            <div class="form-group">
            	<label for="instituicao" class="col-sm-12 control-label">Instituição</label>
			    <div class="col-sm-2">
			      <select id="instituicao" name="instituicao" class="form-control">
					<option value="-1">Selecione a instituição&nbsp;&nbsp;&nbsp;&nbsp;</option>
					<?php
                    // Instanciar o DAO e retornar dados da tabela TIPO
                    $UsuarioDAO = new UsuarioDAO();
                    //chama GETALL passando um valor, no caso o id do perfil de instituições, para retornar só usuários do perfil INSTITUIÇÃO
                    //em seguida 2 falses para não permitir exibição de usuarios expirados e perfis excessão e o número 1 para ordenar pelo nome da instituição
                    $result = $UsuarioDAO->getAll(PERFIL_IDINSTITUICAO,false,false,1);
                    if(sizeof($result)>0){
						for($i=0;$i<sizeof($result);$i++){							
							echo "<option value=\"".
							$result[$i]["idusuario"]."\">".
							$result[$i]["nome_instituicao"]."</option>";
						}
					}
					?>
                  </select>
			      <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
			    </div>			    
            </div>

            <div class="form-group">

            	<label for="modo" class="col-sm-12 control-label">Modo do processo</label>
			    <div class="col-sm-2">
			      <select id="modo" name="modo" class="form-control">
					<option value="-1">Selecione uma opção</option>
					<?php
						echo "<option value=\"".PROCESSOETAPA_NORMAL."\">Normal (vê todas as etapas, até escolher outro modo)</option>";
						echo "<option value=\"".PROCESSOETAPA_COMELEICOES."\">Com eleições</option>";
						echo "<option value=\"".PROCESSOETAPA_SEMELEICOES."\">Sem eleições</option>";
					?>
                  </select>
			      <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
			    </div>
			    
            </div>

            <div class="form-group">

            	<label for="militar" class="col-sm-12 control-label">Instituição Militar</label>
			    <div class="col-sm-2">
			      <select id="militar" name="militar" class="form-control">
					<option value="-1">Selecione uma opção</option>
					<?php
						echo "<option value=\"".PROCESSO_MILITAR."\">Sim (modo do processo vira 'Sem eleições')</option>";
						echo "<option value=\"".PROCESSOETAPA_NORMAL."\">Não</option>";
					?>
                  </select>
			      <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
			    </div>
			    
            </div>

            <div class="form-group">
            	<label for="posse" class="col-sm-12 control-label">Data/Hora da posse já definida?</label>
			    <div class="col-sm-2">
			      <select id="posse" name="posse" class="form-control"  onChange="verificaValor(this.value,this.name);">
					<option value="-1">Selecione a resposta</option>
					<option value="1">Sim</option>
					<option value="2">Não</option>
                  </select>
			      <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
			    </div>			    
            </div>

            

		        <div class="form-group well campoviaselect div_posse" id="posse_sim">		        	
	            	<label for="dtescolhida" class="col-sm-12 control-label">Informe a data e hora escolhida</label>
		        	<div class="col-sm-3">
			        	<div class="input-group datetimepicker">
				            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar" style="cursor:default !important;"></i></span>
				            <input type="text" class="form-control" id="dtescolhida" name="dtescolhida" placeholder="Ex.: 20/08/2025 07:00" maxlength="<?php echo $GLOBALS["processo_dtposse_size"]+4; ?>" autocomplete="off">
			        	</div>				        		        	
		        		<span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
		        	</div>
		        </div>


		    <div class="form-group well">
                <label class="col-lg-2 control-label">Responsável pelo processo</label>
                <div class="col-lg-10">
                    <button type="button" class="btn btn-info btn-sm add_re">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true">
                        </span> Adicionar Responsável
                    </button>
                    <div id="responsavel"></div>
                </div>
                <?php
					//carrega do BD os usuários "responsáveis"
					$UsuarioDAO = new UsuarioDAO();
					$usuariosresponsaveis = $UsuarioDAO->getAllResponsaveis();
					//gera inputs hidden aninhados com [] com os valores
					foreach ($usuariosresponsaveis as $t) {
						echo '<input type="hidden" name="usuariosresponsaveis[]" id="usuariosresponsaveis[]" value="'.$t["idusuario"].'||||||'.$t["nome"].'" />';
					}
				?>
            </div>

            <div class="form-group" style="margin-top:40px;">
              <div class="col-lg-10">
                <button type="reset" id="cancelar" class="btn btn-default index_pro.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                <button type="submit" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salvar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
              </div>
            </div>
        </form>
	</div>
</div>
<?php

}//fim do else do "IF FOI ENVIADO ALGO - post"

include_once("../menu_rodape.php");

//else do verificaFuncaoUsuario(FUNCAO_PROCESSO_ADD)
}else{

		enviaMsg("erro","Acesso negado","Sem permissão de acesso, link quebrado ou dados inválidos");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_pro.php\">";
		exit();

}
?>