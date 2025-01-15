<?php
require_once("../config.php");
require_once("../bin/errors.php");
require_once("../bin/functions.php");
require_once("../bin/js-css.php");
require_once("../login_verifica.php");

if(verificaFuncaoUsuario(FUNCAO_ETAPA_ADD)!==false){

//conecta no banco e instacia uma conexão com o Registry
require_once("../conexao.php");
require_once("../model/Registry.php");
//carrega DAO's das chaves estrangeiras
require_once("../dao/EtapaDAO.php");
require_once("../dao/PerfilDAO.php");
require_once("../dao/DocumentoTipoDAO.php");
require_once("../dao/UsuarioDAO.php");
//carrega Models
require_once('../model/Etapa.php');

// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);

require_once("../menu_topo.php");

//SUBMENU DE AÇÕES
require_once('submenu_etapa.php');
//FIM SUBMENU DE AÇÕES

//se tiver enviado o formulário
if(isset($_POST) && !empty($_POST) ){


	/* DESCRIÇÃO DADOS DO POST:
	-----------------------------------------------
	fluxo = 0 (principal) e 1 (alternativo)
	etapatipo = 0 (normal), 1 (escolhe se houve ou não candidatos)
	modo = 0 (normal), 1 (com eleições), 2 (sem eleições) ou 3 (não militar)
	ordem = 0 (primeira etapa) outros números: se 1, e principal, vira 2. se 3 e alternativa vira 3.1, se 3.1 vira 3.2
	aprova = 0 (nao aprovavel) e 1 (aprovavel)
	numdocs = 1 (1 tipo de documento só) e 2 (2 tipos)
	//1 doc só:
	documentotipo_1 = algum id do tipo de documento (ou -1)
	//2 docs:
	documentotipo_2 = algum id do tipo de documento (ou -1)
	documentotipo_3 = algum id do tipo de documento (ou -1)
	emails = 1 (1 email só) e 2 (2 emails)
	//1 email só:
	email1_tipo = 1 (instituicao), 2 (perfil específico) e 3 (usuarios especificos)
	email1_perfil = algum id de perfil (ou -1)
	email1_usuario (ARRAY) = id dos usuarios que devem receber o email
	email1_msg = texto email 1
	//2 emails:
	email2_tipo = 1 (instituicao), 2 (perfil específico) e 3 (usuarios especificos)
	email2_perfil = algum id de perfil (ou -1)
	email2_usuario (ARRAY) = id dos usuarios que devem receber o email
	email2_msg = texto email 2
	email3_tipo = 1 (instituicao), 2 (perfil específico) e 3 (usuarios especificos)
	email3_perfil = algum id de perfil (ou -1)
	email3_usuario (ARRAY) = id dos usuarios que devem receber o email
	email3_msg = texto email 3

	escolhedata = 0 (não) e 1 (sim), para exibir escolha de data na capa do processo
	msgcapa = texto a ser exibido na capa do processo que estiver nesta etapa
	msgadd = texto a ser exibido no momento de adicionar um documento nesta etapa
	*/

	//valida entradas do usuário
	$nome=validaLiteral($_POST["nome"],ETAPA_NOME_SIZE);
	$descricao=sqlTrataString($_POST["descricao"]);
	$fluxo=validaInteiro($_POST["fluxo"],ETAPA_FLUXO_SIZE);
	$ordem=validaInteiro($_POST["ordem"],ETAPA_ORDEM_SIZE);
	$aprova=validaInteiro($_POST["aprova"],1);
	$numdocs=validaInteiro($_POST["numdocs"],ETAPA_NUMDOCS_SIZE);
	$documentotipo_1=validaInteiro($_POST["documentotipo_1"],DOCUMENTOTIPO_ID_SIZE);
	$documentotipo_1obrigatorio=validaInteiro($_POST["documentotipo_1obrigatorio"],1);
	$documentotipo_2=validaInteiro($_POST["documentotipo_2"],DOCUMENTOTIPO_ID_SIZE);
	$documentotipo_2obrigatorio=validaInteiro($_POST["documentotipo_2obrigatorio"],1);
	$documentotipo_3=validaInteiro($_POST["documentotipo_3"],DOCUMENTOTIPO_ID_SIZE);
	$documentotipo_3obrigatorio=validaInteiro($_POST["documentotipo_3obrigatorio"],1);
	$emails=validaInteiro($_POST["emails"],1);
	$email1_tipo=validaInteiro($_POST["email1_tipo"],1);
	$email1_perfil=validaInteiro($_POST["email1_perfil"],PERFIL_ID_SIZE);
	if(isset($_POST["email1_usuario"])){
		$email1_usuario=$_POST["email1_usuario"];
	}else{
		$email1_usuario=NULL;
	}	
	$email1_msg=sqlTrataString($_POST["email1_msg"]);
	$email2_tipo=validaInteiro($_POST["email2_tipo"],1);
	$email2_perfil=validaInteiro($_POST["email2_perfil"],PERFIL_ID_SIZE);
	if(isset($_POST["email2_usuario"])){
		$email2_usuario=$_POST["email2_usuario"];
	}else{
		$email2_usuario=NULL;
	}
	$email2_msg=sqlTrataString($_POST["email2_msg"]);
	$email3_tipo=validaInteiro($_POST["email3_tipo"],1);
	$email3_perfil=validaInteiro($_POST["email3_perfil"],PERFIL_ID_SIZE);
	if(isset($_POST["email3_usuario"])){
		$email3_usuario=$_POST["email3_usuario"];
	}else{
		$email3_usuario=NULL;
	}
	$email3_msg=sqlTrataString($_POST["email3_msg"]);
	$msgadd=sqlTrataString($_POST["msgadd"]);
	$msgcapa=sqlTrataString($_POST["msgcapa"]);
	$escolhedata=validaInteiro($_POST["escolhedata"],ETAPA_ESCOLHEDATA_SIZE);
	$bloquear=validaInteiro($_POST["bloquear"],ETAPA_BLOQUEAR_SIZE);
	$expira=validaInteiro($_POST["expira"],ETAPA_EXPIRA_SIZE);
	$prazo=validaInteiro($_POST["prazo"],ETAPA_PRAZO_SIZE);
	if(isset($_POST["perfis"])){
		$perfis=$_POST["perfis"];
	}else{
		$perfis=0;
	}
	$modo=validaInteiro($_POST["modo"],ETAPA_MODO_SIZE);
	$etapatipo=validaInteiro($_POST["etapatipo"],ETAPA_ETAPATIPO_SIZE);

	// se os campos obrigatórios passarem na validação
	if( $nome!==false && $descricao!==false && $fluxo!==false && $ordem!==false && $aprova!==false && $numdocs!==false && $emails!==false && $escolhedata!==false && $expira!==false && $bloquear!==false && $modo!==false && $etapatipo!==false){

		//valida para entrar na área de segurança da SQL
		$nome=sqlTrataString($nome);
		$fluxo=sqlTrataInteiro($fluxo);
		$ordem=sqlTrataInteiro($ordem);
		$aprova=sqlTrataInteiro($aprova);
		$numdocs=sqlTrataInteiro($numdocs);
		$emails=sqlTrataInteiro($emails);
		$escolhedata=sqlTrataInteiro($escolhedata);
		$bloquear=sqlTrataInteiro($bloquear);
		$expira=sqlTrataInteiro($expira);
		$prazo=sqlTrataInteiro($prazo);
		$modo=sqlTrataInteiro($modo);
		$etapatipo=sqlTrataInteiro($etapatipo);

		//se entrar nesse IF os dados já podem ser inseridos no banco
		if( $nome!==false && $fluxo!==false && $ordem!==false && $aprova!==false && $numdocs!==false && $emails!==false && $escolhedata!==false && $expira!==false && $bloquear!==false && $modo!==false && $etapatipo!==false){

			$Etapa = new Etapa();
			$Etapa->setNome($nome);
			$Etapa->setDescricao($descricao);
			$Etapa->setFluxo($fluxo);
			$Etapa->setAprova($aprova);
			$Etapa->setNumDocs($numdocs);
			$Etapa->setMsgAdd($msgadd);
			$Etapa->setMsgCapa($msgcapa);
			$Etapa->setEscolheData($escolhedata);
			$Etapa->setBloquear($bloquear);
			$Etapa->setExpira($expira);
			$Etapa->setPrazo($prazo);
			$Etapa->setModo($modo);
			$Etapa->setEtapaTipo($etapatipo);

			//verifica dados dos campos dinâmicos
			//calcula ordem da etapa:
				$ordem_antecedente=$ordem;
				//fluxo principal (+1)
				if($fluxo==0){
					//2.3 vira 2, e acrescenta mais 1
					$ordem=floor($ordem)+1;
				//fluxo alternativo (+0.1)
				}else{
					$ordem+=0.1;
				}

			//definição emails
				//1 email só
				if($emails == 1){
					$Etapa->setMsgEmail1(sqlTrataString($email1_msg));
					$Etapa->setMsgEmail2(NULL);
				//2 emails
				}elseif($emails == 2){
					$Etapa->setMsgEmail1(sqlTrataString($email2_msg));
					$Etapa->setMsgEmail2(sqlTrataString($email3_msg));
				}else{
					$Etapa->setMsgEmail1(NULL);
					$Etapa->setMsgEmail2(NULL);
				}

			$Etapa->setOrdem($ordem);
				  
			// Instanciar o DAO para inserir dados na base
			$EtapaDAO = new EtapaDAO();
			// Chama a função que verifica duplicidade do registro
			$possuiDuplicidade = $EtapaDAO->isDuplicated($Etapa);
			if($possuiDuplicidade!==false){
				enviaMsg("erro","Etapa não cadastrada","O nome escolhido ($nome) já existe ");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_etapa.php?showAllRecords=true\">";
				exit();
			}

			//teste fluxo alternativo (aceita que a etapa anterior no máximo termine com ,9 -> 1,9 ; 2,9 e etc..)
			if($fluxo==1){
				$etapasAnteriores = $EtapaDAO->getAll();
				//limite é 10 etapas por número
				$cont_array = array();
				foreach ($etapasAnteriores as $etapa) {
					$teste_fluxo_alternativo=explode(".", $etapa["ordem"]);
					if(isset($teste_fluxo_alternativo[1]) && !empty($teste_fluxo_alternativo[1])){
						if(!isset($cont_array[$teste_fluxo_alternativo[0]])){
							$cont_array[$teste_fluxo_alternativo[0]]=1;
						}else{
							$cont_array[$teste_fluxo_alternativo[0]]++;	
						}						
					}
				}
				$teste_fluxo_alternativo=explode(".", $ordem_antecedente);
				if(isset($teste_fluxo_alternativo[0]) 
					&& !empty($teste_fluxo_alternativo[0])
					&& isset($cont_array[$teste_fluxo_alternativo[0]])
					&& $cont_array[$teste_fluxo_alternativo[0]]>8){
						enviaMsg("erro","Etapa não cadastrada","O limite máximo é de 9 etapas alternativas por etapa principal (ex.: 1.9, 2.9, 3.9 e etc)");
						echo "<meta http-equiv=\"refresh\" content=\"0; url=add_etapa.php\">";
						exit();
				}
			}
			
			//insere registro
			$idetapa = $EtapaDAO->insert($Etapa);
			//atribui ID via método do Usuário
			$Etapa->setId($idetapa);
			if($idetapa){

				//atribui um ou mais perfis que efetuam esta etapa
				if($perfis!=0 && sizeof($perfis)>0){
					for($i=0;$i<sizeof($perfis);$i++){
						$Etapa->setPerfil1($perfis[$i]);
						$idetapaperfil = $EtapaDAO->insertEtapaPerfil($Etapa);
						if(!$idetapaperfil){
							enviaMsg("erro","Erro","As funções do perfil não foram cadastradas corretamente, tente novamente mais tarde");
							echo "<meta http-equiv=\"refresh\" content=\"0; url=add_perfiluser.php\">";
							exit();
						}
					}
				}


				//reordena ordem das etapas anteriores e subsequentes ($ordem)
				if($ordem > 0){

					//ordenação atual (já com a nova ordem inserida)
					$listaEtapas = $EtapaDAO->getAll();
					
					//variaveis p/ criação da lista organizada
					$novaLista = array();
					$valorOrdemAtual = 0;
					$valorOrdemAnterior = 0;
					for($i=0;$i<sizeof($listaEtapas);$i++){
						if($listaEtapas[$i]["fluxo"] == 0){
							$valorOrdemAtual=floor($valorOrdemAtual)+1;
						}else{
							$valorOrdemAtual+=0.1;
						}


						//Trocou ordem anterior: 1.5 vira 1.6 (35)
						//echo "<br>(".$listaEtapas[$i]["idetapa"].") ".round($listaEtapas[$i]["ordem"], 2)." - ".round($valorOrdemAnterior, 2)." = 0 ? ";
						if(round($listaEtapas[$i]["ordem"], 2)-round($valorOrdemAnterior, 2)==0){
							//echo "sim <br>";
							$idetapa_inverter=false;
							//se o repetido encontrado não for a etapa atual troca o valor da etapa anterior
							if($idetapa!=$listaEtapas[$i]["idetapa"]){
								//echo "Trocou ordem anterior: ".$listaEtapas[$i-1]["ordem"]." vira ".$valorOrdemAtual." (".$listaEtapas[$i-1]["idetapa"].")<br>";
								$listaEtapas[$i-1]["ordem"]=$valorOrdemAtual;
								//armazena ID da ordem atual que for igual a nova, para inverter posições no final
								if(round($listaEtapas[$i-1]["ordem"],2)==round($ordem,2)){
									$idetapa_inverter=$i-1;
								}
							}else{
								//inverte a ordem com quem tem a mesma ordem
								if($idetapa_inverter!==false){
									//echo "Pega o valor (".$listaEtapas[$idetapa_inverter]["ordem"].") da etapa ".$listaEtapas[$idetapa_inverter]["idetapa"]." e põe na criada agora (".$listaEtapas[$i]["idetapa"].") substituindo o valor ".$listaEtapas[$i]["ordem"]."<br>";
									$listaEtapas[$i]["ordem"]=$listaEtapas[$idetapa_inverter]["ordem"];
									$listaEtapas[$idetapa_inverter]["ordem"]=$valorOrdemAtual;	
								}else{
									//echo "Põe o valor $valorOrdemAtual que antes era ".$listaEtapas[$i]["ordem"]." (etapa ".$listaEtapas[$i]["idetapa"].")<br>";
									$listaEtapas[$i]["ordem"]=$valorOrdemAtual;
								}
							}
						}

						//se a ordematual for diferente da ordem da listagem, adiciona valor correto da ordem ao array
						if((round($valorOrdemAtual, 2)-round($listaEtapas[$i]["ordem"], 2))!=0){
							$novaLista[$i]["idetapa"]=$listaEtapas[$i]["idetapa"];
							$novaLista[$i]["ordem"]=$valorOrdemAtual;
							//echo "Diferente: ".$novaLista[$i]["ordem"]." vira ".$valorOrdemAtual." (".$novaLista[$i]["idetapa"].")<br>";
						//se forem iguais, apenas acrescenta o registro a listagem
						}else{
							$novaLista[$i]["idetapa"]=$listaEtapas[$i]["idetapa"];
							$novaLista[$i]["ordem"]=$listaEtapas[$i]["ordem"];	
							//echo "Igual: ".$novaLista[$i]["ordem"]." && ".$listaEtapas[$i]["ordem"]." (".$novaLista[$i]["idetapa"].")<br>";
						}		


						
						$valorOrdemAnterior = $listaEtapas[$i]["ordem"];
					}

					//dá update nas ordens que estavam erradas
					for($i=0;$i<sizeof($novaLista);$i++){
						for($j=0;$j<sizeof($listaEtapas);$j++){
							if($novaLista[$i]["idetapa"]==$listaEtapas[$j]["idetapa"] && $novaLista[$i]["ordem"]!=$listaEtapas[$j]["ordem"]){
								$atualizaOrdem = $EtapaDAO->updateOrdem($novaLista[$i]["idetapa"],$novaLista[$i]["ordem"]);
								if(!$atualizaOrdem){
									enviaMsg("erro","Etapa atualizada com erros","Não foi possível gravar o envio de e-mail para um perfil específico");
									echo "<meta http-equiv=\"refresh\" content=\"0; url=index_etapa.php?showAllRecords=true\">";
									exit();
								}								
							}
						}
						
					}
				}

				//relacionamento etapa_email (envio de e-mails)
				//1 email só
				if($emails == 1){

					//tipo 1 (instituicao), 2 (perfil específico) e 3 (usuarios especificos)
					$Etapa->setTipoEmail1($email1_tipo);
					//seta o numero do email (de qual campo será puxada a mensagem - msgEmail1 ou msgEmail2)
					$Etapa->setNumero(1);
					//envio de e-mail para a instituição 
					if($email1_tipo==1){
						$Etapa->setPerfil1(NULL);
						$Etapa->setUsuario1(NULL);						
						$inseriuEmail = $EtapaDAO->insertEmailEtapa($Etapa);
						if(!$inseriuEmail){
							enviaMsg("erro","Etapa cadastrada com erros","Não foi possível gravar o envio de e-mail para a instituição");
							echo "<meta http-equiv=\"refresh\" content=\"0; url=index_etapa.php?showAllRecords=true\">";
							exit();
						}

					//envio de e-mail para um perfil específico
					}elseif($email1_tipo==2){
						$Etapa->setPerfil1($email1_perfil);
						$Etapa->setUsuario1(NULL);
						$inseriuEmail = $EtapaDAO->insertEmailEtapa($Etapa);
						if(!$inseriuEmail){
							enviaMsg("erro","Etapa cadastrada com erros","Não foi possível gravar o envio de e-mail para um perfil específico");
							echo "<meta http-equiv=\"refresh\" content=\"0; url=index_etapa.php?showAllRecords=true\">";
							exit();
						}

					//envio de e-mail para usuários específicos
					}elseif($email1_tipo==3){
						$Etapa->setPerfil1(NULL);						
						if(sizeof($email1_usuario)>0 && $email1_usuario!=0){
							foreach($email1_usuario as $idusuario){
								$Etapa->setUsuario1($idusuario);
								$inseriuEmail = $EtapaDAO->insertEmailEtapa($Etapa);
								if(!$inseriuEmail){
									enviaMsg("erro","Etapa cadastrada com erros","Não foi possível gravar o envio de e-mail para usuários específicos");
									echo "<meta http-equiv=\"refresh\" content=\"0; url=index_etapa.php?showAllRecords=true\">";
									exit();
								}
							}
						}
					}

				//2 emails
				}elseif($emails==2){

					//tipo 1 (instituicao), 2 (perfil específico) e 3 (usuarios especificos)
					$Etapa->setTipoEmail1($email2_tipo);
					//seta o numero do email (de qual campo será puxada a mensagem - msgEmail1 ou msgEmail2)
					$Etapa->setNumero(1);
					//envio de e-mail para a instituição 
					if($email2_tipo==1){
						$Etapa->setPerfil1(NULL);
						$Etapa->setUsuario1(NULL);						
						$inseriuEmail = $EtapaDAO->insertEmailEtapa($Etapa);
						if(!$inseriuEmail){
							enviaMsg("erro","Etapa cadastrada com erros","Não foi possível gravar o envio de e-mail para a instituição");
							echo "<meta http-equiv=\"refresh\" content=\"0; url=index_etapa.php?showAllRecords=true\">";
							exit();
						}

					//envio de e-mail para um perfil específico
					}elseif($email2_tipo==2){
						$Etapa->setPerfil1($email2_perfil);
						$Etapa->setUsuario1(NULL);
						$inseriuEmail = $EtapaDAO->insertEmailEtapa($Etapa);
						if(!$inseriuEmail){
							enviaMsg("erro","Etapa cadastrada com erros","Não foi possível gravar o envio de e-mail para um perfil específico");
							echo "<meta http-equiv=\"refresh\" content=\"0; url=index_etapa.php?showAllRecords=true\">";
							exit();
						}

					//envio de e-mail para usuários específicos
					}elseif($email2_tipo==3){
						$Etapa->setPerfil1(NULL);						
						if(sizeof($email2_usuario)>0 && $email2_usuario!=0){
							foreach($email2_usuario as $idusuario){
								$Etapa->setUsuario1($idusuario);
								$inseriuEmail = $EtapaDAO->insertEmailEtapa($Etapa);
								if(!$inseriuEmail){
									enviaMsg("erro","Etapa cadastrada com erros","Não foi possível gravar o envio de e-mail para usuários específicos");
									echo "<meta http-equiv=\"refresh\" content=\"0; url=index_etapa.php?showAllRecords=true\">";
									exit();
								}
							}
						}

					}

					//tipo 1 (instituicao), 2 (perfil específico) e 3 (usuarios especificos)
					$Etapa->setTipoEmail1($email3_tipo);
					//seta o numero do email (de qual campo será puxada a mensagem - msgEmail1 ou msgEmail2)
					$Etapa->setNumero(2);
					//envio de e-mail para a instituição 
					if($email3_tipo==1){
						$Etapa->setPerfil1(NULL);
						$Etapa->setUsuario1(NULL);						
						$inseriuEmail = $EtapaDAO->insertEmailEtapa($Etapa);
						if(!$inseriuEmail){
							enviaMsg("erro","Etapa cadastrada com erros","Não foi possível gravar o envio de e-mail para a instituição");
							echo "<meta http-equiv=\"refresh\" content=\"0; url=index_etapa.php?showAllRecords=true\">";
							exit();
						}

					//envio de e-mail para um perfil específico
					}elseif($email3_tipo==2){
						$Etapa->setPerfil1($email3_perfil);
						$Etapa->setUsuario1(NULL);
						$inseriuEmail = $EtapaDAO->insertEmailEtapa($Etapa);
						if(!$inseriuEmail){
							enviaMsg("erro","Etapa cadastrada com erros","Não foi possível gravar o envio de e-mail para um perfil específico");
							echo "<meta http-equiv=\"refresh\" content=\"0; url=index_etapa.php?showAllRecords=true\">";
							exit();
						}

					//envio de e-mail para usuários específicos
					}elseif($email3_tipo==3){
						$Etapa->setPerfil1(NULL);						
						if(sizeof($email3_usuario)>0 && $email3_usuario!=0){
							foreach($email3_usuario as $idusuario){
								$Etapa->setUsuario1($idusuario);
								$inseriuEmail = $EtapaDAO->insertEmailEtapa($Etapa);
								if(!$inseriuEmail){
									enviaMsg("erro","Etapa cadastrada com erros","Não foi possível gravar o envio de e-mail para usuários específicos");
									echo "<meta http-equiv=\"refresh\" content=\"0; url=index_etapa.php?showAllRecords=true\">";
									exit();
								}
							}
						}

					}

				//fim envio dos 2 emails
				}
				//fim envio de emails

				//salvar tipos de documento esperados nesta etapa
				//se é só 1 tipo de documento
				if($numdocs==1){

					if($documentotipo_1>0){
						$Etapa->setDocumentoTipo1(sqlTrataInteiro($documentotipo_1));
						$Etapa->setDocumentoTipo1Obrigatorio(sqlTrataInteiro($documentotipo_1obrigatorio));
						$inseriuNumDocs = $EtapaDAO->insertDocumentoTipoEtapa($Etapa);
						if(!$inseriuNumDocs){
							enviaMsg("erro","Etapa cadastrada com erros","Não foi possível gravar o tipo de documento aceito na etapa");
							echo "<meta http-equiv=\"refresh\" content=\"0; url=index_etapa.php?showAllRecords=true\">";
							exit();
						}
					}

				//se é 2 tipos de documento
				}elseif($numdocs==2){

					if($documentotipo_2>0){
						$Etapa->setDocumentoTipo1(sqlTrataInteiro($documentotipo_2));
						$Etapa->setDocumentoTipo1Obrigatorio(sqlTrataInteiro($documentotipo_2obrigatorio));
						$inseriuNumDocs = $EtapaDAO->insertDocumentoTipoEtapa($Etapa);
						if(!$inseriuNumDocs){
							enviaMsg("erro","Etapa cadastrada com erros","Não foi possível gravar o tipo de documento nº 1 aceito na etapa");
							echo "<meta http-equiv=\"refresh\" content=\"0; url=index_etapa.php?showAllRecords=true\">";
							exit();
						}
					}

					if($documentotipo_3>0){
						$Etapa->setDocumentoTipo1(sqlTrataInteiro($documentotipo_3));
						$Etapa->setDocumentoTipo1Obrigatorio(sqlTrataInteiro($documentotipo_3obrigatorio));
						$inseriuNumDocs = $EtapaDAO->insertDocumentoTipoEtapa($Etapa);
						if(!$inseriuNumDocs){
							enviaMsg("erro","Etapa cadastrada com erros","Não foi possível gravar o tipo de documento nº 2 aceito na etapa");
							echo "<meta http-equiv=\"refresh\" content=\"0; url=index_etapa.php?showAllRecords=true\">";
							exit();
						}
					}
				}
				//fim salvar tipos de documento esperados na etapa

				//registra ação no log				
				$log_obs = $Etapa->toLog();
				
				require_once("../dao/HistoricoDAO.php");
				require_once("../model/Historico.php");
				$Historico = new Historico();
				$Historico->setAcao(LOG_ADD_ETAPA);
				$Historico->setProcesso(0);
				$Historico->setDocumento(0);
				$Historico->setObs($log_obs);
				$HistoricoDAO = new HistoricoDAO();
				$HistoricoDAO->insert($Historico);

			}//fim if($idprocesso)			
			
				
			if($idetapa && $HistoricoDAO){
				//cadastrado
				enviaMsg("sucesso","Etapa cadastrada com sucesso");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_etapa.php?showAllRecords=true\">";
				exit();
			}else{
				//não cadastrado
				enviaMsg("erro","Etapa cadastrada com erros","O histórico não pôde ser salvo");
				echo "<meta http-equiv=\"refresh\" content=\"0; url=index_etapa.php?showAllRecords=true\">";
				exit();
			}

		}else{
			//não cadastrado - dados invalidos
			enviaMsg("erro","Etapa não cadastrada","Os dados enviados estavam inválidos");
			echo "<meta http-equiv=\"refresh\" content=\"0; url=index_etapa.php?showAllRecords=true\">";
			exit();
		}
		
	}else{
		
		//não cadastrado - dados invalidos
		enviaMsg("erro","Etapa não cadastrada","Os dados enviados foram invalidados");
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index_etapa.php?showAllRecords=true\">";
		exit();
		
	}

}//fim do IF do POST
else{

?>

<div id="conteudo_borda">
    <div  id="conteudo">
        <!-- <form enctype="multipart/form-data" name="add_etapa" action="add_etapa.php" method="post" class="form-horizontal" onSubmit="return validaForm('add_etapa','nome','descricao','fluxo','ordem','aprova','numdocs');" >    -->
        <form id="add_etapa" name="add_etapa" action="add_etapa.php" method="post" class="form-horizontal">

            
        	<div class="form-group">
                <label class="col-sm-10 control-label">Nome da Etapa</label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome da Etapa" maxlength="<?php echo ETAPA_NOME_SIZE-20; ?>">
                    <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
                </div>
            </div>

            <div class="form-group">
            	<label for="descricao" class="col-sm-10 control-label">Descrição sucinta</label>
	        	<div class="col-sm-5">
	        		<textarea class="form-control" name="descricao" id="descricao" rows="2"></textarea>
					<span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
	        	</div>
	        </div>

            <div class="form-group">
	            <label class="col-sm-12 control-label">Fluxo da etapa</label>
	            <div class="col-lg-4">
	              <select id="fluxo"  name="fluxo" class="form-control">
	              	<option value="-1" selected="selected">Selecione a qual fluxo esta etapa pertence</option>
	                <option value="0">Principal (1 - 2 - 3 ...)</option>
	                <option value="1">Alternativo (1.1 - 1.2 - 1.3 ...)</option>
	              </select>
	              <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
	            </div>
	        </div>

	        <div class="form-group">
	            <label class="col-sm-12 control-label">Modo da etapa</label>
	            <div class="col-lg-4">
	              <select id="modo" name="modo" class="form-control">
	              	<option value="-1" selected="selected">Selecione a qual modo esta etapa pertence</option>
	                <option value="0">Normal (independente de eleições)</option>
	                <option value="1">Com Eleições</option>
	                <option value="2">Sem Eleições</option>
	                <option value="3">Não Militar (não aparece para instituições militares)</option>
	              </select>
	              <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
	            </div>
	        </div>

            <div class="form-group">
                <label class="col-sm-10 control-label">Ordem da Etapa (etapa antecedente)</label>
                <div class="col-lg-5">
                  <select id="ordem"  name="ordem" class="form-control">
					<option value="-1" selected="selected">Selecione qual etapa antecede a etapa atual</option>
					<option value="0">Esta é a primeira etapa (sem etapas antecedentes)</option>
					<?php
                    // Instanciar o DAO e retornar dados da tabela
                    $EtapaDAO = new EtapaDAO();
                    $dados = $EtapaDAO->getAll();
                    if(sizeof($dados)>0){
						for($i=0;$i<sizeof($dados);$i++){							
								echo "<option value=\"".
								$dados[$i]["ordem"]."\">".
								$dados[$i]["ordem"]." - ".
								$dados[$i]["nome"]."</option>";
						}
					}
					?>
                  </select>
                  <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?><br>** Limite de 9 etapas alternativas por etapa principal (ex.: 1.9, 2.9, 3.9 e etc)</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-11 control-label">Selecione um ou mais perfis que podem realizar ações nesta etapa</label>
                <div class="col-lg-4">
	              <select multiple id="perfis[]"  name="perfis[]" class="form-control">
	              	<?php
                    // Instanciar o DAO e retornar dados da tabela
                    $perfilDAO = new PerfilDAO();
                    $perfil = $perfilDAO->getAll();
                    if(sizeof($perfil)>0){
						for($i=0;$i<sizeof($perfil);$i++){							
								echo "<option value=\"".
								$perfil[$i]["idperfil"]."\">".
								$perfil[$i]["nome"]."</option>";
						}
					}
					?>
	              </select>
                <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?><br>** Segure a tecla <em>CTRL</em> para selecionar mais de um perfil</span>
                </div>
            </div>

            <div class="form-group">
	            <label class="col-sm-11 control-label">Aprovação do documento/outra informação</label>
	            <div class="col-lg-5">
	              <select id="aprova"  name="aprova" class="form-control">
	              	<option value="-1" selected="selected">Selecione se o documento enviado ou alguma informação desta etapa exige aprovação</option>
	                <option value="0">Não aprovável</option>
	                <option value="1">Aprovação obrigatória</option>
	              </select>
	              <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
	            </div>
	        </div> 

	        <div class="form-group">
            	<label for="msgadd" class="col-sm-10 control-label">Texto a exibir na tela de inserção de documento(s)</label>
	        	<div class="col-sm-5">
	        		<textarea class="form-control" name="msgadd" id="msgadd" rows="3"></textarea>
					<span id="helpBlock" class="help-block">Digite aqui um texto indicando o que é feito nesta etapa</span>
	        	</div>
	        </div>

	        <div class="form-group">
            	<label for="msgcapa" class="col-sm-10 control-label">Texto a exibir na capa do processo</label>
	        	<div class="col-sm-5">
	        		<textarea class="form-control" name="msgcapa" id="msgcapa" rows="3"></textarea>
					<span id="helpBlock" class="help-block">Digite aqui alguma mensagem que precise exibir na capa do processo</span>
	        	</div>
	        </div>

	        <div class="form-group">
	            <label class="col-sm-11 control-label">Número de documentos enviados nesta etapa</label>
	            <div class="col-lg-2">
	              <select id="numdocs" name="numdocs" class="form-control" onchange="exibeDiv('documentos',this.value);">
	                <option value="-1" selected="selected">Selecione</option>
	                <option value="0">Nenhum</option>
	                <option value="1">1 documento</option>
	                <option value="2">2 documentos</option>
	              </select>
	              <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
	            </div>
	        </div> 

	        <div class="campoviaselect documentos documentos_1">
	        	<div class="well">
			        <div class="form-group campoviaselect documentos documentos_1">
			            <label class="col-sm-10 control-label">Tipo do Documento</label>
			            <div class="col-lg-10">
			              <select id="documentotipo_1"  name="documentotipo_1" class="form-control">
			                <option value="-1">Selecione o tipo do documento</option>
			                <?php
							// Recuperar infos do checklist
							$DocumentoTipoDAO = new DocumentoTipoDAO();
							$dados = $DocumentoTipoDAO->getAll();
			                if(sizeof($dados)>0){
								for($i=0;$i<sizeof($dados);$i++){	
									echo "<option value=\"".$dados[$i]["iddocumentotipo"]."\">".$dados[$i]["nome"]."</option>";
			                    }
			                }
			                ?>
			              </select>
			              <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
			            </div>

			            <label class="col-sm-10 control-label">Regra de envio do Documento</label>
			            <div class="col-lg-10">
			              <select id="documentotipo_1obrigatorio"  name="documentotipo_1obrigatorio" class="form-control">
			                <option value="-1" selected="selected">Selecione a obrigatoriedade</option>
			                <option value="1">Envio obrigatório</option>
			                <option value="2">Envio opcional</option>
			              </select>
			              <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
			            </div>
			        </div> 
			    </div>
			</div>

			<div class="campoviaselect documentos documentos_2">
	        	<div class="well">
			        <div class="form-group campoviaselect documentos documentos_2">
			            <label class="col-sm-10 control-label">Documento nº 01: Tipo do Documento</label>
			            <div class="col-lg-10">
			              <select id="documentotipo_2"  name="documentotipo_2" class="form-control">
			                <option value="-1">Selecione o tipo do documento</option>
			                <?php
							// Recuperar infos do checklist
							$DocumentoTipoDAO = new DocumentoTipoDAO();
							$dados = $DocumentoTipoDAO->getAll();
			                if(sizeof($dados)>0){
								for($i=0;$i<sizeof($dados);$i++){	
									echo "<option value=\"".$dados[$i]["iddocumentotipo"]."\">".$dados[$i]["nome"]."</option>";
			                    }
			                }
			                ?>
			              </select>
			              <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
			            </div>
			            <label class="col-sm-10 control-label">Documento nº 01: Regra de envio do Documento</label>
			            <div class="col-lg-10">
			              <select id="documentotipo_2obrigatorio"  name="documentotipo_2obrigatorio" class="form-control">
			                <option value="-1" selected="selected">Selecione a obrigatoriedade</option>
			                <option value="1">Envio obrigatório</option>
			                <option value="2">Envio opcional</option>
			              </select>
			              <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
			            </div>
			        </div> 
			        <div class="form-group campoviaselect documentos documentos_2">
			            <label class="col-sm-10 control-label">Documento nº 02: Tipo do Documento</label>
			            <div class="col-lg-10">
			              <select id="documentotipo_3"  name="documentotipo_3" class="form-control">
			                <option value="-1">Selecione o tipo do documento</option>
			                <?php
							// Recuperar infos do checklist
							$DocumentoTipoDAO = new DocumentoTipoDAO();
							$dados = $DocumentoTipoDAO->getAll();
			                if(sizeof($dados)>0){
								for($i=0;$i<sizeof($dados);$i++){	
									echo "<option value=\"".$dados[$i]["iddocumentotipo"]."\">".$dados[$i]["nome"]."</option>";
			                    }
			                }
			                ?>
			              </select>
			              <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
			            </div>
			            <label class="col-sm-10 control-label">Documento nº 02: Regra de envio do Documento</label>
			            <div class="col-lg-10">
			              <select id="documentotipo_3obrigatorio"  name="documentotipo_3obrigatorio" class="form-control">
			                <option value="-1" selected="selected">Selecione a obrigatoriedade</option>
			                <option value="1">Envio obrigatório</option>
			                <option value="2">Envio opcional</option>
			              </select>
			              <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
			            </div>
			        </div> 
			    </div>
			</div>


            <div class="form-group">
	            <label class="col-sm-11 control-label">Após o envio de documento (ou aprovação/reprovação de um documento) nesta etapa o sistema deve enviar e-mails?</label>
	            <div class="col-lg-2">
	              <select id="emails"  name="emails" class="form-control" onchange="exibeDiv('emails',this.value);">
	                <option value="0" selected="selected">Não</option>
	                <option value="1">Sim, 1 e-mail apenas</option>
	                <option value="2">Sim, 2 e-mails</option>
	              </select>
	            </div>
	        </div> 


	        <div class="campoviaselect emails emails_1">
	        	<div class="well">
			        <div class="form-group campoviaselect emails emails_1">
			            <label class="col-sm-11 control-label">Tipo de E-mail</label>
			            <div class="col-lg-4">
			              <select id="email1_tipo"  name="email1_tipo" class="form-control" onchange="exibeDiv('emails_tipo',this.value);">
			                <option value="-1">Selecione o tipo de e-mail que será enviado</option>
			                <option value="1">Envia e-mail para a instituição</option>
			                <option value="2">Envia e-mail para um perfil específico</option>
			                <option value="3">Envia e-mail para usuários específicos</option>
			              </select>
			              <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
			            </div>
			        </div> 
			        <div class="form-group campoviaselect emails_tipo emails_tipo_2">
			            <label class="col-sm-11 control-label">Escolha o perfil de usuário que receberá este e-mail</label>
			            <div class="col-lg-4">
			              <select id="email1_perfil"  name="email1_perfil" class="form-control">
			                <option value="-1">Selecione o perfil</option>
							<?php
		                    // Instanciar o DAO e retornar dados da tabela
		                    $perfilDAO = new PerfilDAO();
		                    $perfil = $perfilDAO->getAll();
		                    if(sizeof($perfil)>0){
								for($i=0;$i<sizeof($perfil);$i++){							
										echo "<option value=\"".
										$perfil[$i]["idperfil"]."\">".
										$perfil[$i]["nome"]."</option>";
								}
							}
							?>
			              </select>
			              <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
			            </div>
			        </div> 
			        <div class="form-group campoviaselect emails_tipo emails_tipo_3">
			            <label class="col-sm-11 control-label">Selecione os usuários que receberão este e-mail</label>
			            <div class="col-lg-4">
			              <select multiple id="email1_usuario[]"  name="email1_usuario[]" class="form-control">
							<?php
		                    // Instanciar o DAO e retornar dados da tabela
		                    $UsuarioDAO = new UsuarioDAO();
		                    $dados = $UsuarioDAO->getAll(false,false,PERFIL_IDINSTITUICAO);
		                    if(sizeof($dados)>0){
								for($i=0;$i<sizeof($dados);$i++){							
										echo "<option value=\"".
										$dados[$i]["idusuario"]."\">".
										$dados[$i]["nomeusuario"]." (".$dados[$i]["nomeperfil"].")</option>";
								}
							}
							?>
			              </select>
			              <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
			            </div>
			        </div> 
			        <div class="form-group campoviaselect emails emails_1">
		            	<label for="email1_msg" class="col-sm-10 control-label">Texto do E-mail</label>
			        	<div class="col-sm-5">
			        		<textarea class="form-control" name="email1_msg" id="email1_msg" rows="3"></textarea>
							<span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?><br>** Digite aqui o texto que será enviado por e-mail</span>
			        	</div>
			        </div>
		        </div><!--fim well emails_1 -->
	    	</div><!--fim emails_1 -->


	    	<div class="campoviaselect emails emails_2">
	        	<div class="well">
			        <div class="form-group campoviaselect emails emails_2">
			            <label class="col-sm-11 control-label">Email nº 01: Tipo de E-mail</label>
			            <div class="col-lg-4">
			              <select id="email2_tipo"  name="email2_tipo" class="form-control" onchange="exibeDiv('emails2_tipo',this.value);">
			                <option value="-1">Selecione o tipo de e-mail que será enviado</option>
			                <option value="1">Envia e-mail para a instituição</option>
			                <option value="2">Envia e-mail para um perfil específico</option>
			                <option value="3">Envia e-mail para usuários específicos</option>
			              </select>
			              <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
			            </div>
			        </div> 
			        <div class="form-group campoviaselect emails2_tipo emails2_tipo_2">
			            <label class="col-sm-11 control-label">Email nº 01: Escolha o perfil de usuário que receberá este e-mail</label>
			            <div class="col-lg-4">
			              <select id="email2_perfil"  name="email2_perfil" class="form-control">
			                <option value="-1">Selecione o perfil</option>
							<?php
		                    // Instanciar o DAO e retornar dados da tabela
		                    $perfilDAO = new PerfilDAO();
		                    $perfil = $perfilDAO->getAll();
		                    if(sizeof($perfil)>0){
								for($i=0;$i<sizeof($perfil);$i++){							
										echo "<option value=\"".
										$perfil[$i]["idperfil"]."\">".
										$perfil[$i]["nome"]."</option>";
								}
							}
							?>
			              </select>
			              <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
			            </div>
			        </div> 
			        <div class="form-group campoviaselect emails2_tipo emails2_tipo_3">
			            <label class="col-sm-11 control-label">Email nº 01: Selecione os usuários que receberão este e-mail</label>
			            <div class="col-lg-4">
			              <select multiple id="email2_usuario[]"  name="email2_usuario[]" class="form-control">
							<?php
		                    // Instanciar o DAO e retornar dados da tabela
		                    $UsuarioDAO = new UsuarioDAO();
		                    $dados = $UsuarioDAO->getAll(false,false,PERFIL_IDINSTITUICAO);
		                    if(sizeof($dados)>0){
								for($i=0;$i<sizeof($dados);$i++){							
										echo "<option value=\"".
										$dados[$i]["idusuario"]."\">".
										$dados[$i]["nomeusuario"]." (".$dados[$i]["nomeperfil"].")</option>";
								}
							}
							?>
			              </select>
			              <span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?></span>
			            </div>
			        </div> 
			        <div class="form-group campoviaselect emails emails_2">
		            	<label for="email2_msg" class="col-sm-10 control-label">Email nº 01: Texto do E-mail</label>
			        	<div class="col-sm-5">
			        		<textarea class="form-control" name="email2_msg" id="email2_msg" rows="3"></textarea>
							<span id="helpBlock" class="help-block"><?php echo APP_MSG_REQUIRED; ?><br>** Digite aqui o texto que será enviado por e-mail</span>
			        	</div>
			        </div>
		        </div><!--fim well emails_2 -->
	    	</div><!--fim emails_2 -->
	    	<div class="campoviaselect emails emails_2">
	        	<div class="well">
			        <div class="form-group campoviaselect emails emails_2">
			            <label class="col-sm-11 control-label">Email nº 02: Tipo de E-mail</label>
			            <div class="col-lg-4">
			              <select id="email3_tipo"  name="email3_tipo" class="form-control" onchange="exibeDiv('emails3_tipo',this.value);">
			                <option value="-1">Selecione o tipo de e-mail que será enviado</option>
			                <option value="1">Envia e-mail para a instituição</option>
			                <option value="2">Envia e-mail para um perfil específico</option>
			                <option value="3">Envia e-mail para usuários específicos</option>
			              </select>
			            </div>
			        </div> 
			        <div class="form-group campoviaselect emails3_tipo emails3_tipo_2">
			            <label class="col-sm-11 control-label">Email nº 02: Escolha o perfil de usuário que receberá este e-mail</label>
			            <div class="col-lg-4">
			              <select id="email3_perfil"  name="email3_perfil" class="form-control">
			                <option value="-1">Selecione o perfil</option>
							<?php
		                    // Instanciar o DAO e retornar dados da tabela
		                    $perfilDAO = new PerfilDAO();
		                    $perfil = $perfilDAO->getAll();
		                    if(sizeof($perfil)>0){
								for($i=0;$i<sizeof($perfil);$i++){							
										echo "<option value=\"".
										$perfil[$i]["idperfil"]."\">".
										$perfil[$i]["nome"]."</option>";
								}
							}
							?>
			              </select>
			            </div>
			        </div> 
			        <div class="form-group campoviaselect emails3_tipo emails3_tipo_3">
			            <label class="col-sm-11 control-label">Email nº 02: Selecione os usuários que receberão este e-mail</label>
			            <div class="col-lg-4">
			              <select multiple id="email3_usuario[]"  name="email3_usuario[]" class="form-control">
							<?php
		                    // Instanciar o DAO e retornar dados da tabela
		                    $UsuarioDAO = new UsuarioDAO();
		                    $dados = $UsuarioDAO->getAll(false,false,PERFIL_IDINSTITUICAO);
		                    if(sizeof($dados)>0){
								for($i=0;$i<sizeof($dados);$i++){							
										echo "<option value=\"".
										$dados[$i]["idusuario"]."\">".
										$dados[$i]["nomeusuario"]." (".$dados[$i]["nomeperfil"].")</option>";
								}
							}
							?>
			              </select>
			            </div>
			        </div> 
			        <div class="form-group campoviaselect emails emails_2">
		            	<label for="email3_msg" class="col-sm-10 control-label">Email nº 02: Texto do E-mail</label>
			        	<div class="col-sm-5">
			        		<textarea class="form-control" name="email3_msg" id="email3_msg" rows="3"></textarea>
							<span id="helpBlock" class="help-block">Digite aqui o texto que será enviado por e-mail</span>
			        	</div>
			        </div>
		        </div><!--fim well emails_2 -->
	    	</div><!--fim emails_2 -->

	    	<div class="form-group">
	            <label class="col-sm-11 control-label">Exibir campo para escolha de datas</label>
	            <div class="col-lg-2">
	              <select id="escolhedata"  name="escolhedata" class="form-control">
	                <option value="0" selected="selected">Não</option>
	                <option value="1">Sim</option>
	              </select>
	            </div>
	        </div> 

	        <div class="form-group">
	            <label class="col-sm-11 control-label">A etapa deve bloquear modificações no processo?</label>
	            <div class="col-lg-3">
	              <select id="bloquear" name="bloquear" class="form-control">
	              	<option value="0" selected="selected">Não</option>
	                <option value="1">Sim, esta etapa bloqueia modificações</option>
	              </select>
	            </div>
	        </div> 

	        <div class="form-group">
	            <label class="col-sm-11 control-label">A etapa deve impor uma data de expiração no login da instituição?</label>
	            <div class="col-lg-5">
	              <select id="expira"  name="expira" class="form-control">
	              	<option value="0" selected="selected">Não</option>
	                <option value="30">Sim, ao chegar nela o login da instituição expira em 30 dias</option>
	                <option value="60">Sim, ao chegar nela o login da instituição expira em 60 dias</option>
	                <option value="90">Sim, ao chegar nela o login da instituição expira em 90 dias</option>
	                <option value="120">Sim, ao chegar nela o login da instituição expira em 120 dias</option>
	                <option value="380">Sim, ao chegar nela o login da instituição expira em 1 ano</option>
	              </select>
	            </div>
	        </div>

	        <div class="form-group">
	            <label class="col-sm-12 control-label">Tipo da etapa</label>
	            <div class="col-lg-6">
	              <select id="etapatipo" name="etapatipo" class="form-control">
	                <option value="0" selected="selected">Etapa Normal</option>
	                <option value="<?php echo ETAPA_ESCOLHE_TIPO; ?>">Etapa para escolha se houve ou não candidatos</option>
	                <option value="<?php echo ETAPA_ESCOLHE_RECURSO; ?>">Etapa para escolha se houve ou não recursos/questionamentos ao pleito eleitoral</option>
	              </select>
	            </div>
	        </div>

	        <div class="form-group">
	            <label class="col-sm-11 control-label">A etapa possui um prazo para ser concluída?</label>
	            <div class="col-lg-5">
	              <select id="prazo"  name="prazo" class="form-control">
	              	<option value="0" selected="selected">Não</option>
	                <option value="5">Sim, 5 dias</option>
	                <option value="7">Sim, 7 dias</option>
	                <option value="10">Sim, 10 dias</option>
	                <option value="15">Sim, 15 dias</option>
	                <option value="20">Sim, 20 dias</option>
	                <option value="25">Sim, 25 dias</option>
	                <option value="30">Sim, 30 dias</option>
	                <option value="45">Sim, 45 dias</option>
	              </select>
	              <span id="helpBlock" class="help-block">Escolhendo <strong>sim</strong> para esse campo, o sistema envia um lembrete aos e-mails dos responsáveis pela etapa 3 dias antes do fim do prazo</span>
	            </div>
	        </div>

	    	<div class="form-group">
              <div class="col-lg-10">
                <button type="reset" id="cancelar" class="btn btn-default index_etapa.php?showAllRecords=true">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voltar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
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
