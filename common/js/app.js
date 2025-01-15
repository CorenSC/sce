/*	
	*	@author
	*	SISTEMA DESENVOLVIDO PELO COREN/SC
	*	DEPARTAMENTO DE TECNOLOGIA DA INFORMAÇÃO, 2017
*/
//INÍCIO CONFIGURAÇÕES GERAIS
//Obs: tais configs precisam ser iguais as do arquivo de configuração na raiz do sistema
	//IDInstituição
		var idEntidadeTipoInstituicao = 3;
	//IDEtapaAprovada
		var idEtapaAprovada = 2;
	//Tempo de sessão após o login/navegação entre páginas (10800 segs = 3 horas de sessão = x 1000 pq aqui é em MILISSEGS)		
		var appSessionLifetime = (10800*1000);
//FIM CONFIGURAÇÕES GERAIS
/*caracteres obrigatórios de senha, ao menos 1 de cada*/
//1 letra maiúscula
var senha_c_obg1="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
//1 letra minúscula
var senha_c_obg2="abcdefghijklmnopqrstuvwxyz";
//1 número
var senha_c_obg3="0123456789";
//1 caracter especial - exceto aspas simples/duplas
var senha_c_obg4="ÁÀÃÂÄáàãâäËÉÈÊëéèêÏÍÌÎïíìîÖÓÒÕÔöóòõôÜÚÙÛüúùû !¹@²#³$£%¢¨¬§&*[]({~^´`})-_=+Ççªº°:;<,.>?\\|/";
//caracteres aceitos 
var senha_c_aceitos = senha_c_obg1+senha_c_obg2+senha_c_obg3+senha_c_obg4;

function novaValidaForm(f){

	//variaveis iniciais para cada validação de formulário
	var enviaForm = true;
	//array de campos a pular
	var pulaCampo = [];
	//nome do form
	var nomeForm = f.name;
	//variavel para controlar a exibição do MASK (div que trava a edição da tela)
	var exibeMask = true;

	//$('select, input, textarea').each(function (index, obj) {
	$('form#'+nomeForm+' select, form#'+nomeForm+' :input,form#'+nomeForm+' textarea').each(function (index, obj) {
	    var $this = $(obj);

	    var valor=$this[0].value; 		//VALOR: 	-1,20160101,Alexandre ...
	    var nome=$this[0].name;   		//NOME: 	dtcriacao,idprocesso,...
	    var tipo1=$this[0].tagName; 	//TIPO1: 	SELECT,INPUT
	    var tipo2=$this.attr('type');	//TIPO2: 	radio,text,hidden,undefined(p/SELECT)
	    var rel = $(this).attr("rel");
	    
	    //se houver algum pedido de não bloqueio da tela ao enviar, esconde a Mask
	    if(nome=="nao_bloquear_tela" && valor==1){
			exibeMask = false;
		}

		//se não for um campo escondido
	    if(tipo2!="hidden" && nome!="" && nome!=null){
	    	if(nomeForm=="view_historico_pro" || nomeForm=="view_historico" || nomeForm=="index_relatorios"){
	    		insereNoArray("periodo_de",pulaCampo);
	    		insereNoArray("periodo_ate",pulaCampo);
	    	}
	    	if(nomeForm=="add_pro" || nomeForm=="edit_pro"){
	    		insereNoArray("idtipo",pulaCampo);
	    		insereNoArray("dtposse1",pulaCampo);
	    		insereNoArray("dtposse2",pulaCampo);
	    		insereNoArray("dtposse3",pulaCampo);
	    		insereNoArray("obsposse",pulaCampo);
	    		insereNoArray("dtprazo",pulaCampo);
	    		insereNoArray("dtfim",pulaCampo);
	    		insereNoArray("dtaviso",pulaCampo);
	    		if(nome=="posse" && valor=="2"){
	    			insereNoArray("dtescolhida",pulaCampo);
	    		}
	    	}
	    	if(nomeForm=="iniciar"){
	    		insereNoArray("email2",pulaCampo);
	    	}
	    	if(nomeForm=="index_pro"){
	    		if(nome=="idtipo" && valor=="numero"){
	    			insereNoArray("entidade_nome",pulaCampo);
	    			insereNoArray("entidade_cidade",pulaCampo);
	    			insereNoArray("busca_responsavel",pulaCampo);
	    		}
	    		if(nome=="idtipo" && valor=="entidade"){
	    			insereNoArray("numero",pulaCampo);
	    			insereNoArray("busca_responsavel",pulaCampo);
	    			insereNoArray("entidade_nome",pulaCampo);
	    			insereNoArray("entidade_cidade",pulaCampo);
	    		}
	    		if(nome=="idtipo" && valor=="busca_responsavel"){
	    			insereNoArray("numero",pulaCampo);
	    			insereNoArray("entidade_nome",pulaCampo);
	    			insereNoArray("entidade_cidade",pulaCampo);
	    		}
	    	}
	    	if(nomeForm=="add_perfiluser" || nomeForm=="edit_perfiluser"){
	    		insereNoArray("funcoes[]",pulaCampo);
	    	}
		    if(nomeForm=="add_user" || nomeForm=="edit_user"){
		    	//pula campos não obrigatórios
		    	insereNoArray("email2",pulaCampo);
	    		insereNoArray("telefone",pulaCampo);
				//caso no select "PERFIL" não tenha sido escolhido o tipo da INSTITUIÇÃO, não valida o nome da instituição
				if(nome=="idperfil" && valor!=idEntidadeTipoInstituicao){
					insereNoArray("nome_instituicao",pulaCampo);
				}
				//caso o select "EXPIRA" tenha valor "NAO", não valida a data inserida em "NOVOATO"
				if(nome=="expira" && valor=="nao"){
					insereNoArray("novoato",pulaCampo);
				}
				//caso o select limita processos estiver com valor NAO, não valida o limitaprocessos[]
				if(nome=="limita" && valor=="nao"){
					insereNoArray("limitaprocessos[]",pulaCampo);
				}
			}
			if(nomeForm=="add_user" || nomeForm=="edit_user" || nomeForm=="change_password"){
		    	if(nome=="login"){
					var login = valor;
					//verificando se o login possui nome.sobrenome & >7 caracteres
					var loginOk=false;
					var aux = login.split(".");
					if(aux[0] && aux[0].length>1 && aux[1] && aux[1].length>1){
						if(login.length>7){
							loginOk=true;
						}
					}
					if(!loginOk){
						eval("document."+nomeForm+"."+nome+".focus()");
						alert("O login precisa ter no mínimo 8 caracteres e seguir o padrão nome.sobrenome");
						enviaForm=false;
						return enviaForm;
					}
				}
			}
		    if(nomeForm=="add_user" || nomeForm=="change_password"){
				if(nome=="rsenha"){
					var senha1 = eval("document."+nomeForm+".senha");
					var senha = senha1.value;
					var repeticaoSenha = valor;
					//verifica se há ao menos 1 caracter de cada obrigatorio:					
						if(!testeObrigatorio(senha_c_obg1,senha)){
							eval("document."+nomeForm+".senha.focus()");
							alert("A senha deve ser constituída com ao menos 1 letra maiúscula");
							enviaForm=false;
							return enviaForm;
						}
						if(!testeObrigatorio(senha_c_obg2,senha)){
							eval("document."+nomeForm+".senha.focus()");
							alert("A senha deve ser constituída com ao menos 1 letra minúscula");
							enviaForm=false;
							return enviaForm;
						}
						if(!testeObrigatorio(senha_c_obg3,senha)){
							eval("document."+nomeForm+".senha.focus()");
							alert("A senha deve ser constituída com ao menos 1 número");
							enviaForm=false;
							return enviaForm;
						}
						if(!testeObrigatorio(senha_c_obg4,senha)){
							eval("document."+nomeForm+".senha.focus()");
							alert("A senha deve ser constituída com ao menos 1 caracter especial, exceto aspas simples ou aspas duplas");
							enviaForm=false;
							return enviaForm;
						}
					//varre os caracteres da senha procurando algum inválido
					for(var y=0;y<senha.length;y++){
						// indexOf retorna -1 quando NÃO encontra
					    if (senha_c_aceitos.indexOf(senha[y]) == -1){
					    	eval("document."+nomeForm+".senha.focus()");
							alert("A senha deve ser constituída com ao menos 1 letra maiúscula, 1 letra minúscula, 1 número e 1 caracter especial exceto aspas simples ou aspas duplas).");
							enviaForm=false;
							return enviaForm;
					  	}
					}
					//tamanho senha
					if(senha.length<6 || senha.length>50){
						eval("document."+nomeForm+".senha.focus()");
						alert("A senha precisa ter no mínimo 6 caracteres e no máximo 50.");
						enviaForm=false;
						return enviaForm;
					}
					//se senhas são idênticas
					if(senha!=repeticaoSenha){
						eval("document."+nomeForm+"."+nome+".focus()");
						alert("As senhas digitadas não são idênticas.");
						enviaForm=false;
						return enviaForm;
					}
				}
		    }
		    if(nomeForm=="edit_modelo"){
				//caso o select "ALTERA" tenha valor "NAO", não valida campo USERFILE
				if(nome=="altera" && valor=="nao"){
					insereNoArray("userfile",pulaCampo);
				}
			}
		    if(nomeForm=="edit_user"){
				//caso o select "ALTERASENHA" tenha valor "NAO", não valida valores de SENHA e RSENHA
				if(nome=="alterasenha" && valor=="nao"){
					insereNoArray("senha",pulaCampo);
					insereNoArray("rsenha",pulaCampo);
				}
				var alteraSenha = eval("document."+nomeForm+".alterasenha");
				if(alteraSenha.value=="sim" && nome=="rsenha"){
					var senha1 = eval("document."+nomeForm+".senha");
					var senha = senha1.value;
					var repeticaoSenha = valor;
					//verifica se há ao menos 1 caracter de cada obrigatorio:					
						if(!testeObrigatorio(senha_c_obg1,senha)){
							eval("document."+nomeForm+".senha.focus()");
							alert("A senha deve ser constituída com ao menos 1 letra maiúscula");
							enviaForm=false;
							return enviaForm;
						}
						if(!testeObrigatorio(senha_c_obg2,senha)){
							eval("document."+nomeForm+".senha.focus()");
							alert("A senha deve ser constituída com ao menos 1 letra minúscula");
							enviaForm=false;
							return enviaForm;
						}
						if(!testeObrigatorio(senha_c_obg3,senha)){
							eval("document."+nomeForm+".senha.focus()");
							alert("A senha deve ser constituída com ao menos 1 número");
							enviaForm=false;
							return enviaForm;
						}
						if(!testeObrigatorio(senha_c_obg4,senha)){
							eval("document."+nomeForm+".senha.focus()");
							alert("A senha deve ser constituída com ao menos 1 caracter especial, exceto aspas simples ou aspas duplas");
							enviaForm=false;
							return enviaForm;
						}
					//varre os caracteres da senha procurando algum inválido
					for(var y=0;y<senha.length;y++){
						// indexOf retorna -1 quando NÃO encontra
					    if (senha_c_aceitos.indexOf(senha[y]) == -1){
					    	eval("document."+nomeForm+".senha.focus()");
							alert("A senha deve ser constituída com ao menos 1 letra maiúscula, 1 letra minúscula, 1 número e 1 caracter especial exceto aspas simples ou aspas duplas).");
							enviaForm=false;
							return enviaForm;
					  	}
					}
					//tamanho senha
					if(senha.length<6 || senha.length>50){
						eval("document."+nomeForm+".senha.focus()");
						alert("A senha precisa ter no mínimo 6 caracteres e no máximo 50.");
						enviaForm=false;
						return enviaForm;
					}
					//se senhas são idênticas
					if(senha!=repeticaoSenha){
						eval("document."+nomeForm+"."+nome+".focus()");
						alert("As senhas digitadas não são idênticas.");
						enviaForm=false;
						return enviaForm;
					}
				}
			}
			//verifica o usuário selecionou uma data para posse (index_doc)
			if(nomeForm=="index_doc_datas"){
				//não valida obsposse se alguma data for aprovada
				if(nome=="aprova" && valor==idEtapaAprovada){
					insereNoArray("justificativa",pulaCampo);
				}
			}
			//verifica o usuário definiu alguma data para posse ou observação (index_doc)
			if(nomeForm=="index_doc_datas_define"){
				if((nome=="dtposse1" && valor.length<=0) || (nome=="dtposse2" && valor.length<=0) || (nome=="dtposse3" && valor.length<=0)){
					var obsposse = eval("document."+nomeForm+".obsposse");
					var obs = obsposse.value;
					if(obs.length>0){
						insereNoArray("dtposse1",pulaCampo);
						insereNoArray("dtposse2",pulaCampo);
						insereNoArray("dtposse3",pulaCampo);
					}
				}
				//se escolheu alguma data de posse, NÃO obriga a preencher outras datas / observação de posse
				if(nome=="dtposse1" && valor.length>0){
					insereNoArray("dtposse2",pulaCampo);
					insereNoArray("dtposse3",pulaCampo);
					insereNoArray("obsposse",pulaCampo);
				}
				if(nome=="dtposse2" && valor.length>0){
					insereNoArray("dtposse1",pulaCampo);
					insereNoArray("dtposse3",pulaCampo);
					insereNoArray("obsposse",pulaCampo);
				}
				if(nome=="dtposse3" && valor.length>0){
					insereNoArray("dtposse1",pulaCampo);
					insereNoArray("dtposse2",pulaCampo);
					insereNoArray("obsposse",pulaCampo);
				}
			}
			if(nomeForm=="add_doc"){
				//se preencher o campo observação libera para NÃO preencher as datas de posse
				if((nome=="dtposse1" && valor.length<=0) || (nome=="dtposse2" && valor.length<=0) || (nome=="dtposse3" && valor.length<=0)){
					var obsposse = eval("document."+nomeForm+".obsposse");
					var obs = obsposse.value;
					if(obs.length>0){
						insereNoArray("dtposse1",pulaCampo);
						insereNoArray("dtposse2",pulaCampo);
						insereNoArray("dtposse3",pulaCampo);
					}
				}
				//se escolheu alguma data de posse, NÃO obriga a preencher outras datas / observação de posse
				if(nome=="dtposse1" && valor.length>0){
					insereNoArray("dtposse2",pulaCampo);
					insereNoArray("dtposse3",pulaCampo);
					insereNoArray("obsposse",pulaCampo);
				}
				if(nome=="dtposse2" && valor.length>0){
					insereNoArray("dtposse1",pulaCampo);
					insereNoArray("dtposse3",pulaCampo);
					insereNoArray("obsposse",pulaCampo);
				}
				if(nome=="dtposse3" && valor.length>0){
					insereNoArray("dtposse1",pulaCampo);
					insereNoArray("dtposse2",pulaCampo);
					insereNoArray("obsposse",pulaCampo);
				}
			}
			if(nomeForm=="edit_doc" || nomeForm=="add_doc"){
				insereNoArray("obs",pulaCampo);
				//se atualizar = nao PULA VALIDAÇÃO DO CAMPO userfile
				if(nome=="atualizar" && valor=="nao"){
					insereNoArray("userfile",pulaCampo);
				}
				//se for alguma etapa com APROVAÇÃO, se aprovou não precisa validar o campo justificativa
				if(nome=="aprova" && valor==idEtapaAprovada){
					insereNoArray("justificativa",pulaCampo);
				}
			}
			if(nomeForm=="view_historico"){
				//se tipo = geral PULA VALIDAÇÃO DO CAMPO idprocesso e idusuario
				if(nome=="tipo" && valor=="geral"){
					insereNoArray("idprocesso",pulaCampo);
					insereNoArray("idusuario",pulaCampo);
				}
				//se tipo = usuario PULA VALIDAÇÃO DO CAMPO idprocesso
				if(nome=="tipo" && valor=="usuario"){
					insereNoArray("idprocesso",pulaCampo);
				}
				//se tipo = processo PULA VALIDAÇÃO DO CAMPO idusuario
				if(nome=="tipo" && valor=="processo"){
					insereNoArray("idusuario",pulaCampo);
				}
			}
			if(nomeForm=="add_etapa" || nomeForm=="edit_etapa"){
				insereNoArray("msgadd",pulaCampo);
				insereNoArray("msgcapa",pulaCampo);
				//nenhum doc a enviar
				if(nome=="numdocs" && valor==0){
					insereNoArray("documentotipo_1",pulaCampo);
					insereNoArray("documentotipo_1obrigatorio",pulaCampo);
					insereNoArray("documentotipo_2",pulaCampo);
					insereNoArray("documentotipo_2obrigatorio",pulaCampo);
					insereNoArray("documentotipo_3",pulaCampo);
					insereNoArray("documentotipo_3obrigatorio",pulaCampo);
				}
				//um doc a enviar
				if(nome=="numdocs" && valor==1){
					insereNoArray("documentotipo_2",pulaCampo);
					insereNoArray("documentotipo_2obrigatorio",pulaCampo);
					insereNoArray("documentotipo_3",pulaCampo);
					insereNoArray("documentotipo_3obrigatorio",pulaCampo);
				}
				//dois docs
				if(nome=="numdocs" && valor==2){
					insereNoArray("documentotipo_1",pulaCampo);
					insereNoArray("documentotipo_1obrigatorio",pulaCampo);
				}
				//nenhum email
				if(nome=="emails" && valor==0){
					insereNoArray("email1_tipo",pulaCampo);
					insereNoArray("email1_perfil",pulaCampo);
					insereNoArray("email1_msg",pulaCampo);
					insereNoArray("email1_usuario[]",pulaCampo);
					insereNoArray("email2_tipo",pulaCampo);
					insereNoArray("email2_perfil",pulaCampo);
					insereNoArray("email2_usuario[]",pulaCampo);
					insereNoArray("email2_msg",pulaCampo);
					insereNoArray("email3_tipo",pulaCampo);
					insereNoArray("email3_perfil",pulaCampo);
					insereNoArray("email3_usuario[]",pulaCampo);
					insereNoArray("email3_msg",pulaCampo);
				}
				//UM E-MAIL
					//geral 1 email
					if(nome=="emails" && valor==1){
						insereNoArray("email2_tipo",pulaCampo);
						insereNoArray("email2_perfil",pulaCampo);
						insereNoArray("email2_usuario[]",pulaCampo);
						insereNoArray("email2_msg",pulaCampo);
						insereNoArray("email3_tipo",pulaCampo);
						insereNoArray("email3_perfil",pulaCampo);
						insereNoArray("email3_usuario[]",pulaCampo);
						insereNoArray("email3_msg",pulaCampo);
					}
					//envio para instituição
					if(nome=="email1_tipo" && valor=="1"){
						insereNoArray("email1_perfil",pulaCampo);
						insereNoArray("email1_usuario[]",pulaCampo);
					}
					//envio para perfil específico
					if(nome=="email1_tipo" && valor=="2"){
						insereNoArray("email1_usuario[]",pulaCampo);
					}
					//envio para users específicos
					if(nome=="email1_tipo" && valor=="3"){
						insereNoArray("email1_perfil",pulaCampo);
					}
				//DOIS E-MAILS
					//geral 2 emails
					if(nome=="emails" && valor==2){
						insereNoArray("email1_tipo",pulaCampo);
						insereNoArray("email1_perfil",pulaCampo);
						insereNoArray("email1_usuario[]",pulaCampo);
						insereNoArray("email1_msg",pulaCampo);
					}
					//01 - envio para instituição
					if(nome=="email2_tipo" && valor=="1"){
						insereNoArray("email2_perfil",pulaCampo);
						insereNoArray("email2_usuario[]",pulaCampo);
					}
					//01 - envio para perfil específico
					if(nome=="email2_tipo" && valor=="2"){
						insereNoArray("email2_usuario[]",pulaCampo);
					}
					//01 - envio para users específicos
					if(nome=="email2_tipo" && valor=="3"){
						insereNoArray("email2_perfil",pulaCampo);
					}
					//02 - envio para instituição
					if(nome=="email3_tipo" && valor=="1"){
						insereNoArray("email3_perfil",pulaCampo);
						insereNoArray("email3_usuario[]",pulaCampo);
					}
					//02 - envio para perfil específico
					if(nome=="email3_tipo" && valor=="2"){
						insereNoArray("email3_usuario[]",pulaCampo);
					}
					//02 - envio para users específicos
					if(nome=="email3_tipo" && valor=="3"){
						insereNoArray("email3_perfil",pulaCampo);
					}
			}
			//se for etapa de aprovacao do DOC: 
			if(nomeForm=="index_doc_aprova"){
				//se o doc for aprovado não valida campo justificativa
				if(nome=="aprova_doc" && valor==idEtapaAprovada){
					insereNoArray("justificativa",pulaCampo);
				}else{
					insereNoArray("userfile",pulaCampo);
				}
			}						
			//se tiver campo telefone || celular: valida se não for p/ pular -> obriga o DDD
			if((nome=="telefone" || nome=="celular") && (!arrayContem(nome,pulaCampo) || valor.length > 0)){
				if(valor.length<14){
					$this[0].focus();
					alert("O "+nome+" precisa ter o DDD com 2 dígitos seguidos pelo número do "+nome+" com 8 ou 9 dígitos. Ex: (48) 98765-4321.");
					enviaForm=false;
					return enviaForm;
				}
			}
			//Campos de e-mail (primário || secundário): valida se não for p/ pular
			if((nome=="email1" || nome=="email2") && (!arrayContem(nome,pulaCampo) || valor.length > 0 )){
				if(!validaEmail(valor)){
					alert("O e-mail informado é inválido, por favor confira se digitou corretamente ou insira outro endereço de e-mail");
					$this[0].focus();
					enviaForm=false;
					return enviaForm;
				}else{
				}
			}

			//se for um campo do tipo RADIO (tratamento diferente) e não for para pulá-lo
			if(tipo2=="radio" && !$("input[type='radio'][name='"+nome+"']").is(':checked') && !arrayContem(nome,pulaCampo)){

				if(nome=="dtcriacao")	nome="Data de Criação";
				if(nome=="dtposse")		nome="Data de Posse, mesmo que você não esteja de acordo com ela";
				if(nome=="idusuario")	nome="Nome do Responsável";

				$this[0].focus();
				alert("Voce precisa selecionar algum(a) "+nome+".");
				enviaForm=false;
				return enviaForm;

			}else{

				//se for um campo do tipo checkbox e não for para pulá-lo
				if(tipo2=="checkbox" && !$("input[type='checkbox'][name='"+nome+"']").is(':checked') && !arrayContem(nome,pulaCampo)){
					
					if(nome=="jurisdicao[]")	nome="Município abrangido";

					$this[0].focus();
					alert("Voce precisa selecionar algum(a) "+nome+".");
					enviaForm=false;
					return enviaForm;
				}else{			
					//se o campo tiver sido deixado em branco e não for para pulá-lo
					if( (valor == "" || valor == -1)  && !arrayContem(nome,pulaCampo) ){ //!pulaCampo.includes(nome)
						if(nome=="modo")				nome="Modo do processo";
						if(nome=="militar")				nome="Instituição Militar";
						if(nome=="dtescolhida")			nome="Data e hora escolhida";
						if(nome=="dtposse1")			nome="Data e hora de posse nº 1";
						if(nome=="dtposse2")			nome="Data e hora de posse nº 2";
						if(nome=="dtposse3")			nome="Data e hora de posse nº 3";
						if(nome=="entidade_nome")		nome="Nome da instituição";
						if(nome=="documentotipo_1")		nome="Tipo do Documento";
						if(nome=="documentotipo_1obrigatorio")	nome="Regra de envio do Documento";
						if(nome=="documentotipo_2")		nome="Tipo do Documento nº 1";
						if(nome=="documentotipo_2obrigatorio")	nome="Regra de envio do Documento nº 1";
						if(nome=="documentotipo_3")		nome="Tipo do Documento nº 2";
						if(nome=="documentotipo_3obrigatorio")	nome="Regra de envio do Documento nº 2";
						if(nome=="email1_tipo")			nome="Tipo do Email";
						if(nome=="email1_perfil")		nome="Perfil";
						if(nome=="email1_usuario[]")	nome="Usuários";
						if(nome=="email1_msg")			nome="Texto da mensagem";
						if(nome=="email2_tipo")			nome="Tipo do Email nº 1";
						if(nome=="email2_perfil")		nome="Perfil nº 1";
						if(nome=="email2_usuario[]")	nome="Usuários nº 1";
						if(nome=="email2_msg")			nome="Texto da mensagem nº 1";
						if(nome=="email3_tipo")			nome="Tipo do Email nº 2";
						if(nome=="email3_perfil")		nome="Perfil nº 2";
						if(nome=="email3_usuario[]")	nome="Usuários nº 2";
						if(nome=="email3_msg")			nome="Texto da mensagem nº 2";
						if(nome=="numdocs")				nome="Número de Documentos";
						if(nome=="perfis[]")			nome="Perfis";
						if(nome=="descricao")			nome="Descrição";
						if(nome=="iddocumentotipo")		nome="Tipo do Documento";
						if(nome=="subsecao")			nome="Subseção";
						if(nome=="idmunicipio")			nome="Município";
						if(nome=="processotipo")		nome="Tipo do Processo";
						if(nome=="instituicao")			nome="Instituição";
						if(nome=="nome_instituicao")	nome="Nome da Instituição";
						if(nome=="email1")				nome="E-mail Principal";
						if(nome=="email2")				nome="E-mail Secundário";
						if(nome=="login")				nome="Usuário";
						if(nome=="senha")				nome="Senha";
						if(nome=="rsenha")				nome="Redigite a Senha";
						if(nome=="nome")				nome="Nome";
						if(nome=="idtipo")				nome="Tipo";
						if(nome=="idperfil")			nome="Perfil";
						if(nome=="numero")				nome="Número";
						if(nome=="idprocesso")			nome="Processo";
						if(nome=="idusuario")			nome="Usuário";
						if(nome=="limitaprocessos[]")	nome="de Processos";
						if(nome=="aprova")				nome="Aprovação";
						if(nome=="aprova_doc")			nome="Aprovação";
						if(nome=="idprocessotipo")		nome="Tipo do Processo";
						if(nome=="busca_responsavel")	nome="Responsável";
						if(nome=="g-recaptcha-response")nome="(clicar no quadrado) de comprovação de usuário real (não robô)";
						if(nome=="novoato"){
							if(nomeForm=="add_user" || nomeForm=="edit_user"){ 
								nome="Data Limite"; 
							}
						}
						var testeResponsavel = arrayContem("re_idusuario_",nome);
						if(testeResponsavel===true){
							nome="Usuário responsável"; 
						}
						//se é um campo de arquivo a ser enviado
						if((nome=="userfile" || nome=="userfile[]")){
							//só se for OBRIGATORIO, (rel == 1) do contrário pula
							if(rel=="1"){
								alert("Voce precisa selecionar um documento para ser enviado.");	
							}else{
								enviaForm=true;
								return enviaForm;
							}
						}else{
							alert("Voce precisa preencher o campo "+nome+".");
						}
						$this[0].focus();
						enviaForm=false;
						return enviaForm;
					}else{
						//se for campo de envio de arquivo, não for para pulá-lo && REL=1 (obrigatorio)
						if((nome=="userfile" || nome=="userfile[]") && !arrayContem(nome,pulaCampo) && rel=="1"){
							//verifica se não é dos tipos aceitos
							var extensao = valor.substring(valor.length-4, valor.length).toLowerCase();
							if (extensao!=".pdf" && extensao!=".doc" && extensao!="docx" && extensao!=".xls" && extensao!="xlsx" && extensao!=".odt"){
								alert("Apenas documentos no formato DOC, DOCX, PDF, XLS, XLSX ou ODT podem ser enviados");
								$this[0].focus();
								enviaForm=false;
								return enviaForm;
							}
						}
					}
				}
			}			
		}
	});	
	if(enviaForm){
		//se for pra exibir o mask
		if(exibeMask){
			$("#mask").show();	
		}
		//se chegar aqui é pq está tudo validado, pode enviar o formulário	e desabilitar todos os "redirecionamentos" ou reenvios de dados
		$("a").unbind("click");
		$("button").attr("disabled","disabled");
		$(":submit").html("&nbsp;&nbsp;Enviando, aguarde...&nbsp;&nbsp;");
	}
	return enviaForm;
}

function validaEmail(e){
	var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  	return regex.test(e);
}


/* função INATIVA de validação de campos dos formulários */
function validaForm(){
	alert("função errada - avisar o administrador do sistema");
}//fim Funçao validaForm()

//Função para descobrir se um valor está no array
function arrayContem(value, array) {
	if(array.indexOf(value) > -1){
		return true;
	}else{
		return false;
	}
}

function insereNoArray(value, array){
	//só insere se o valor ainda não existe no array
	if(array.indexOf(value) <= -1){
		array.push(value);
	}
}

//Função que alinha ao centro uma div específica
function centralizaDiv(id){	
	var winH = $(window).height();
	var winW = $(window).width();	
	$(id).css('margin-top',  (((winH/2) - ($(id).height()/2)))-75);
	$(id).css('margin-left', (winW/2) - ($(id).width()/2));				
	$(window).resize(function () {
		var winH = $(window).height();
		var winW = $(window).width();		
		$(id).css('margin-top',  (((winH/2) - ($(id).height()/2)))-75);
		$(id).css('margin-left', (winW/2) - ($(id).width()/2));
	});	
	return true;
}
//Verifica se CPF é válido - retirado do site da Receita Federal (http://www.receita.fazenda.gov.br/aplicacoes/atcta/cpf/funcoes.js)
function TestaCPF(strCPF) {
    var Soma;
    var Resto;
    Soma = 0;
    if (strCPF == "00000000000")
		return false;
    for (i=1; i<=9; i++)
	Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i); 
    Resto = (Soma * 10) % 11;
    if ((Resto == 10) || (Resto == 11)) 
	Resto = 0;
    if (Resto != parseInt(strCPF.substring(9, 10)) )
	return false;
	Soma = 0;
    for (i = 1; i <= 10; i++)
       Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i);
    Resto = (Soma * 10) % 11;
    if ((Resto == 10) || (Resto == 11)) 
	Resto = 0;
    if (Resto != parseInt(strCPF.substring(10, 11) ) )
        return false;
    return true;
}
//função que retorna TRUE caso encontre algum VALORES em CAMPO
function testeObrigatorio(valores,campo){
	var numOcorrencias=0;
	for(var y=0;y<campo.length;y++){
		// indexOf retorna > -1 quando encontra
	    if (valores.indexOf(campo[y]) > -1){
	    	numOcorrencias++;
	  	}
	}
	if(numOcorrencias==0){
		numOcorrencias = false;
	}
	return numOcorrencias;
}
//exibe determinado campo Via Select
function showCampoViaSelect(campo){
	$('.campoviaselect').slideUp('slow');
	$('#campoviaselect'+campo).slideDown('slow');
}
//mensagem de ERRO com texto em negrito e texto normal por tempo fixo
function showMsgErro(txtNegrito, txtNormal){
	$(document).ready(function(e) {
		$( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );
		$('#msg_erro').html("<strong>"+txtNegrito+"</strong> "+txtNormal);
		if(!$('#msg_erro').is(":visible")){
			$('#msg_erro').slideDown().delay(22000).slideUp();
		}
	});
}
//mensagem de ERRO com texto em negrito e texto normal por tempo fixo + parametro "campo" => será focused
function showMsgErro(txtNegrito, txtNormal,campo){
	$(document).ready(function(e) {
		$('#'+campo).focus();
		$( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );
		$('#msg_erro').html("<strong>"+txtNegrito+"</strong> "+txtNormal);
		if(!$('#msg_erro').is(":visible")){
			$('#msg_erro').slideDown().delay(22000).slideUp();
		}

	});
}
//mensagem de SUCESSO com texto em negrito e texto normal exibido por tempo fixo
function showMsgSucesso(txtNegrito, txtNormal){
	$(document).ready(function(e) {
		$( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );
		$('#msg_sucesso').html("<strong>"+txtNegrito+"</strong> "+txtNormal);
		if(!$('#msg_sucesso').is(":visible")){
			$('#msg_sucesso').slideDown().delay(18000).slideUp();
		}
	});
}
//mensagem de ATENÇÃO com texto em negrito e texto normal exibido por tempo fixo
function showMsgAtencao(txtNegrito, txtNormal){
	$(document).ready(function(e) {
		$( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );
		$('#msg_atencao').html("<strong>"+txtNegrito+"</strong> "+txtNormal);
		if(!$('#msg_atencao').is(":visible")){
			$('#msg_atencao').slideDown().delay(4500).slideUp();
		}
	});
}
//mensagem de ATENÇÃO com texto em negrito e texto normal exibido por determinado tempo
function showMsgAtencao(txtNegrito, txtNormal,tempo){
	$(document).ready(function(e) {
		$( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );
		$('#msg_atencao').html("<strong>"+txtNegrito+"</strong> "+txtNormal);
		if(!$('#msg_atencao').is(":visible")){
			$('#msg_atencao').slideDown().delay(tempo).slideUp();
		}
	});
}
//exibe determinado elemento após determinado tempo
function exibeAposTempo(classe, tempo){
	$(document).ready(function(e) {
			$('.'+classe).delay(tempo).slideDown();
	});
}
//some determinado elemento após determinado tempo
function someAposTempo(classe, tempo){
	$(document).ready(function(e) {
			$('.'+classe).delay(tempo).slideUp();
	});
}

//SORTABLE QUE PERMITE REEORDENAÇÃO DE DIVS
$(function() {
	$( "#sortable" ).sortable({
	  revert: true
	});
	$( "ul, li" ).disableSelection();
});

//verifica se um valor é SIM, se for exibe DIV 1, se for NÃO exibe div 2
function verificaValor(valor,nome){
	$(".div_"+nome).slideUp();
	if(valor=='sim' || valor=='1'){
		$("#"+nome+"_1").slideDown();
		$("#"+nome+"_sim").slideDown();
	}else{
		if(valor=='nao' || valor=='2'){
			$("#"+nome+"_2").slideDown();
			$("#"+nome+"_nao").slideDown();
		}
	}
	return true;
}

//exibe div/elemento que tiver a classe e classe_valor passados como parâmetro
function exibeDiv(classe,exibir){
	$("."+classe).slideUp();
	if(exibir!=""){
		$("."+classe+"_"+exibir).slideDown();
	}
	return true;
}

//TRECHOS EXECUTADOS SOMENTE APÓS O CARREGAMENTO DA PÁGINA
$(document).ready(function(e) {
//funções/códigos que são carregados/executados após carregar a página:
	$(this).finish();//finaliza quaisquer animações da página	
	var divConteudo = '#conteudo';
	$(divConteudo).slideDown(500);

	//definindo o estilo active para o item do menu de acordo com a página atual
	var urlAtual = window.location.href;
	var paginaAtual = urlAtual.replace(/\./g, '_');
	var pagActive="index";
	//para cada pagina atual diferente da index_php, testa e seta a pagina correspondente

	//se não for a página de login/iniciar, controla tempo de sessão (conforme valor do appSessionLifetime)
	if(paginaAtual.indexOf("login_php")==-1 && paginaAtual.indexOf("iniciar_php")==-1){
		let myGreeting = setTimeout(function() {
		  alert('Sessão expirada, por favor refaça o login no sistema!');
		  location=('index.php');
		}, appSessionLifetime);
	}
	
	novaActive = "window_php";
	if(paginaAtual.indexOf(novaActive)!=-1){
		pagActive=novaActive;
	}	

	//TELA DE INICIO
	novaActive = "index";
	if(paginaAtual.indexOf(novaActive)!=-1){
		pagActive=novaActive;
	}

	//TELA DE ADD DOCUMENTOS - se entrar nela remover estilo "cliqueAqui"
	novaActive = "add_doc";
	if(paginaAtual.indexOf(novaActive)!=-1){
		$('.btn-info').removeClass('CliqueAqui');
	}

	//USUARIOS E PERFIS
	if(paginaAtual.indexOf("index_user")!=-1
		|| paginaAtual.indexOf("add_user")!=-1
		|| paginaAtual.indexOf("edit_user")!=-1
		|| paginaAtual.indexOf("index_perfiluser")!=-1
		|| paginaAtual.indexOf("add_perfiluser")!=-1
		|| paginaAtual.indexOf("edit_perfiluser")!=-1){
		novaActive="index_user";
		pagActive="index_user";
	}
	//PROCESSOS
	if(paginaAtual.indexOf("index_pro")!=-1
		|| paginaAtual.indexOf("add_pro")!=-1
		|| paginaAtual.indexOf("edit_pro")!=-1
		|| paginaAtual.indexOf("index_doc")!=-1
		|| paginaAtual.indexOf("add_doc")!=-1
		|| paginaAtual.indexOf("edit_doc")!=-1
		|| paginaAtual.indexOf("view_historico_pro")!=-1){
		novaActive="index_pro";
		pagActive="index_pro";
	}
	//OUTROS
	if(paginaAtual.indexOf("index_modelo")!=-1
		||  paginaAtual.indexOf("add_modelo")!=-1
		||  paginaAtual.indexOf("edit_modelo")!=-1
		||  paginaAtual.indexOf("index_etapa")!=-1
		||  paginaAtual.indexOf("add_etapa")!=-1
		||  paginaAtual.indexOf("edit_etapa")!=-1
		||  paginaAtual.indexOf("index_subsecao")!=-1
		||  paginaAtual.indexOf("add_subsecao")!=-1
		||  paginaAtual.indexOf("edit_subsecao")!=-1
		||  paginaAtual.indexOf("index_doctipo")!=-1
		||  paginaAtual.indexOf("add_doctipo")!=-1
		||  paginaAtual.indexOf("edit_doctipo")!=-1
		||  paginaAtual.indexOf("index_relatorios")!=-1
		||  paginaAtual.indexOf("view_relatorios")!=-1
		|| (paginaAtual.indexOf("view_historico")!=-1 && paginaAtual.indexOf("view_historico_pro")==-1 )
		){
			novaActive="index_outros";
			pagActive="index_outros";
	}
	
	if(pagActive!=""){
		$('.navbar-left > li').removeClass("active");
		$('#'+pagActive).addClass("active");
		//faz as opções do submenu OUTROS ficarem "active" ao selecioná-las
		if(novaActive=="index_outros"){
			//primeira posicao e ultima posicao pertinente
			var outroPosicao1=paginaAtual.indexOf('/control/');
			//var outroPosicao2=paginaAtual.lastIndexOf('_php');
			var outroPosicao2=paginaAtual.indexOf('_php');
			//primeiras posicoes
			//remove o trecho entre a posicao1 e posicao2
			var nomeOutroAtual = paginaAtual.substr(outroPosicao1+9,(outroPosicao2-(outroPosicao1+9)));
			//aplica estilo ACTIVE ao item do menu pertinente
			$('#'+pagActive+" > ul.dropdown-menu > ."+nomeOutroAtual).addClass("active");

		}
	}
	
	function abreNovaJanela(parametros){
		//adicionado esta variavel pois, sem a data ao clicar duas vezes não abrirá duas novas janelas
		var data = new Date();
		var maskHeight = screen.height-100;
		var maskWidth = screen.width;
		window.open("window.php?"+parametros,data,"STATUS=NO, TOOLBAR=NO, LOCATION=NO, DIRECTORIES=NO, SCROLLBARS=YES, TOP=0, LEFT=0, WIDTH="+maskWidth+", HEIGHT="+maskHeight+"");
		
	}

	$('.sem_paginas_em_branco').unbind('click');
	$('.sem_paginas_em_branco').bind('click',function(e){
		var icone = $(this).find('span').first();
		//se estiver como OK, deve trocar para REMOVE
		if(icone.hasClass("glyphicon-ok")){
			$(this).attr("title","Sem páginas em branco! Clique novamente para recolocar páginas em branco");
			icone.removeClass("glyphicon glyphicon-ok");
			icone.addClass("glyphicon glyphicon-remove");
		}else{
			$(this).attr("title","Com páginas em branco! Clique novamente para retirar páginas em branco");
			icone.removeClass("glyphicon glyphicon-remove");
			icone.addClass("glyphicon glyphicon-ok");
		}
	});

	//modificado recentemente
	//envolve o menu_topo.php / spe.js / spe.css / jquery-ui.css e imagens da pasta /images
	//cliques nas opções visualizar / editar e deletar na listagem de documentos
	$('.index_doc_view, .index_doc_edit, .index_doc_del').unbind('click');
	$('.index_doc_view, .index_doc_edit, .index_doc_del').bind('click',function(e){
		
			e.preventDefault();
			var acao =	"";
			if($(this).hasClass("index_doc_view")){
				acao = "view";
			}else{
				if($(this).hasClass("index_doc_del")){
					acao = "del";
				}else{
					acao = "edit";
				}			
			}

			var showAcao = "visualizar";
			if(acao=="del"){
				showAcao = "remover";
			}else{
				if(acao=="edit"){
					showAcao = "editar";
				}
			}

			var infos = $(this).parents("tr");
			var aux = infos.attr('id').split("|");
			
			var idprocesso = aux[0];
			var iddocumento = aux[1];

			//se ação for VIEW, verifica a nova funcionalidade (sem páginas em branco)
			if(acao=="view"){
				var marcouSemPaginasBrancas=$(this).closest('td').next().find('span').first();
				if(marcouSemPaginasBrancas.hasClass("glyphicon-remove")){
					iddocumento +="&pb=1";
				}				
			}

			efetuaAcao(acao,idprocesso,iddocumento);

			function efetuaAcao(tipo,processo,iddocumento){
				var nomeCompleto = "documento";

				if(tipo=="view"){
					abreNovaJanela("a=view&p=doc&id="+iddocumento);
				}
				if(tipo=="edit"){	
					location=('edit_doc.php?p='+processo+'&c='+iddocumento+'&r=index_doc_php');
				}
				if(tipo=="del"){

					var deleta = confirm("Este documento será removido, confirma a operação?");
					if(deleta===true){
						var resposta = $.ajax({
							type: 'POST',
							url: 'del_doc.php',
							async: false,
							dataType: 'text',
							data: {idprocesso:processo, iddocumento:iddocumento} }).responseText;
							if(resposta!==false && resposta!=null && resposta!=""){	
								location=("index_doc.php?p="+processo);
							}
					}else{
						return false;
					}
					
				}
			}
		
	});
	
	$('#cancelar').unbind("click");
	$('#cancelar').click(function(e){
		e.preventDefault();
		var classe = $(this).attr('class');
		var aux = classe.split(" ");
		var ultimaPos = aux.length - 1;
		var endereco = aux[ultimaPos];
		if(endereco){
			if(endereco=="back"){
				history.back(-1);
			}else{
				if(paginaAtual.indexOf("r=index_doc_php")!=-1 || pagActive=="add_doc_php" || pagActive=="edit_doc_php"){
				var processo = $(this).parent("div");
				var idprocesso = processo.attr('id').replace("processo_","");
				location.href=('index_doc.php?p='+idprocesso+'&r=index_doc_php');
				}else{
					location.href=(endereco);
				}
			}			
		}else{
			location.href=('index_pro.php');
		}
	});
	
	/*clique no botão EDITAR na listagem de processos*/
	$('.documento_index').unbind('click');
	$('.documento_index').bind('click',function(){
		var processo = $(this).parents("tr");
		var idprocesso = processo.attr('id').replace("processo_","");
		location.href=('index_doc.php?p='+idprocesso);
	});

	
	/*clique no botão EDITAR na listagem de usuarios*/
	$('.edit_usuario').unbind('click');
	$('.edit_usuario').bind('click',function(){
		var user = $(this).parents("tr");
		var iduser = user.attr('id').replace("usuario_","");
		location.href=('edit_user.php?id='+iduser);
	});

	/*clique no botão EDITAR na listagem de usuarios*/
	$('.edit_perfil').unbind('click');
	$('.edit_perfil').bind('click',function(){
		var perfil = $(this).parents("tr");
		var idperfil = perfil.attr('id').replace("perfil_","");
		location.href=('edit_perfiluser.php?id='+idperfil);
	});
	
	$('#cancelar_window').unbind('click');
	$('#cancelar_window').bind('click',function(){
		window.close();
	});

	/*clique no botão DELETAR na listagem da index_pro*/
	$('.window_open').unbind('click');
	$('.window_open').bind('click',function(){
		var processo = $(this).attr("id");
		var aux = processo.split("|");
		var idprocesso = aux[0];
		var action = aux[1];
		var page = aux[2];
		var size = aux.length;
		if(size==4){
			abreNovaJanela("id="+idprocesso+"&a="+action+"&p="+page+"&d="+aux[3]);
		}else{
			if(size==6){
				abreNovaJanela("id="+idprocesso+"&a="+action+"&p="+page+"&d="+aux[3]+"&c="+aux[4]+"&t="+aux[5]);
			}else{
				abreNovaJanela("id="+idprocesso+"&a="+action+"&p="+page);
			}
		}
		
	});

	/*clique no botão DELETAR na listagem da index_pro*/
	$('.processo_del').unbind('click');
	$('.processo_del').bind('click',function(){
		var idprocesso = $(this).attr('id').replace("processo_","");
		var op = confirm("Você está prestes a deletar este processo. Confirma a operação?");
		if(op===true){
			var resposta = $.ajax({
				type: 'POST',
				url: 'del_pro.php',
				async: false,
				dataType: 'text',
				data: {id:idprocesso} }).responseText;
			if(resposta.indexOf("sucesso")!=-1){
				if(paginaAtual.indexOf('/control/index_pro_php') >=0 ){
					showMsgSucesso('Processo excluído com sucesso','');
					$(this).parents('tr').fadeOut();				
				}else{
					enviaMsg("index_pro.php","sucesso","Processo excluído com sucesso","");					
				}
				
			}else{
				showMsgErro('O Processo não pôde ser excluído','Tente novamente mais tarde');
			}
		}
	});

	//envio de formulário, só envia se retornar true da função de validação
	$("form").bind('submit',function(event) { 
		event.preventDefault();
		var ok = novaValidaForm(this);
		if(ok){
			$("form").unbind("submit");
			$("form").submit();
		}
	});

	//não permitindo aspas simples ou duplas em campo senha
	$(document).on('keyup','.camposenha',function(e){
		var vCampo = $(this).val();//valor do campo
		var tCampo = $(this).val().length;//tamanho do campo
		var campoOk = true;//var para saber se há caracter inválido
		for(var y=0;y<tCampo;y++){
			// indexOf retorna -1 quando NÃO encontra
		    if (senha_c_aceitos.indexOf(vCampo[y]) == -1){		    	
		    	//substitui valor não aceito por ""
		    	var auxCampo = vCampo.replace(vCampo[y],"");
		    	//atribui ao campo o valor sem este caracter
		    	$(this).val(auxCampo);
		    	//atribui false para cair no alert abaixo
		    	campoOk=false;
		  	}
		}
		//se encontrar algum caracter não aceito
		if(!campoOk){
			alert('Não é permitido o uso de aspas simples ou aspas duplas neste campo');
			return false;
		}
	});

	function enviaMsg(urlRedirect,tipoMsg,msg_1,msg_2){
		var resposta = $.ajax({
				type: 'POST',
				url: 'ajax_msg.php',
				async: false,
				dataType: 'text',	
				data: {tipoMsg:tipoMsg,msg_1:msg_1,msg_2:msg_2} }).responseText;
			if(resposta.indexOf("erro_ajax")!=-1){
				showMsgErro("Erro","Tente efetuar a ação novamente");
			}else{
				location=(urlRedirect); 
			}		
	}

	/*clique no botão DELETAR na listagem da index_perfiluser */
	$('.perfil_del').unbind('click');
	$('.perfil_del').bind('click',function(){
		var perfil = $(this).parents("tr");
		var idperfil = perfil.attr('id').replace("perfil_","");
		var op = confirm("Você está prestes a deletar este perfil. Confirma a operação?");
		if(op===true){		
			var resposta = $.ajax({
				type: 'POST',
				url: 'del_perfil.php',
				async: false,
				dataType: 'text',	
				data: {idperfil:idperfil} }).responseText;
			if(resposta.indexOf("sucesso")!=-1){
				showMsgSucesso('Perfil excluído com sucesso','');
				perfil.fadeOut();
			}else{
				showMsgErro('O Perfil não pôde ser excluído','Operação não permitida ou problemas na manipulação dos dados');
			}			
		}
	});

	
	//tratamento da filtragem por STATUS
	function getStatusPro(){


		var nome = paginaAtual.indexOf('status%5B%5D');
		var status = "";
		//se o valor retornado for 0 ou mais, foi selecionado alguma origem para filtragem
		if(nome>=0){

			//primeira posicao e ultima posicao com IDSTATUS
			var posicao1=paginaAtual.indexOf('status%5B%5D=');
			var posicao2=paginaAtual.lastIndexOf('status%5B%5D=');
			//primeiras posicoes
			//remove o trecho entre a posicao1 e posicao2
			var aux1 = paginaAtual.substr(posicao1,(posicao2-posicao1));
			var posicao3 = aux1.lastIndexOf('&');
			var comecoStatus = aux1.substr(0,posicao3);

			//ultimas posicoes
			var aux2 = paginaAtual.substr(posicao2);
			var posicao4 = aux2.indexOf('&');
			//se posicao3 menor que 0, então nas primeiras posicoes temos só 1 registro	
			if(posicao4<0){
				var fimStatus=aux2;
			//se for >= 0, fazemos os calculos subtraindo até posicao3, armazenando somente o IDORIGEM
			}else{				
				var fimStatus=aux2.substr(0, posicao4);
			}

			if(comecoStatus.length>0){
				status+='&'+comecoStatus;
			}
			if(fimStatus.length>0){
				status+='&'+fimStatus;
			}
		}

		return status;
	}
	//tratamento da filtragem por ORIGEM
	function getOrigemPro(){

		var nomeOrigem = paginaAtual.indexOf('origem%5B%5D');
		var origem = "";

		//se o valor retornado for 0 ou mais, foi selecionado alguma origem para filtragem
		if(nomeOrigem>=0){

			//primeira posicao e ultima posicao com IDORIGEM
			var posicao1=paginaAtual.indexOf('origem%5B%5D=');
			var posicao2=paginaAtual.lastIndexOf('origem%5B%5D=');

			//primeiras posicoes
			//remove o trecho entre a posicao1 e posicao2
			var aux1 = paginaAtual.substr(posicao1,(posicao2-posicao1));
			var posicao3 = aux1.lastIndexOf('&');
			var comecoOrigem = aux1.substr(0,posicao3);
			
			//ultimas posicoes
			var aux2 = paginaAtual.substr(posicao2);
			var posicao4 = aux2.indexOf('&');
			//se posicao3 menor que 0, então nas primeiras posicoes temos só 1 registro	
			if(posicao4<0){
				var fimOrigem=aux2;
			//se for >= 0, fazemos os calculos subtraindo até posicao3, armazenando somente o IDORIGEM
			}else{			
				var fimOrigem=aux2.substr(0, posicao4);
			}

			if(comecoOrigem.length>0){
				origem+='&'+comecoOrigem;
			}
			if(fimOrigem.length>0){
				origem+='&'+fimOrigem;
			}

		}

		return origem;
	}

	$('#botao_busca_processo').on("click",function(e){
		e.preventDefault();
		$(this).addClass('disabled');
		$('.div_busca_processo').slideDown();
		//$('.btn-info').slideUp();		
		$('.submenu > .btn-info, .submenu > .btn-primary').slideUp();
	});
	$('#botao_cancela_busca_processo').on("click",function(e){
		e.preventDefault();
		$('#botao_busca_processo').removeClass('disabled');
		$('.div_busca_processo').slideUp();
		//$('.btn-info').slideDown();
		$('.btn-info, .btn-primary').slideDown();
	});
	$('#botao_remove_busca_processo').on("click",function(e){
		e.preventDefault();
		var status = getStatusPro();
		var origem = getOrigemPro();
		var exibeTodos = paginaAtual.indexOf('showAllRecords=true');
		if(exibeTodos>=0){
			showAllRecords='&showAllRecords=true';
		}else{
			showAllRecords='';
		}

		location=("index_pro.php?c=no"+showAllRecords+status+origem);

	});
	$('#botao_remove_busca_usuario').on("click",function(e){
		e.preventDefault();
		var exibeTodos = paginaAtual.indexOf('showAllRecords=true');
		if(exibeTodos>=0){
			showAllRecords='?showAllRecords=true';
		}else{
			showAllRecords='';
		}
		location=("index_user.php"+showAllRecords);
	});

	//tratamento da filtragem devido a BUSCAS
	function getBuscasPro(){
		//posicoes das URL de busca
		var posicao1=paginaAtual.indexOf('idtipo=');
		var posicao2=paginaAtual.indexOf('entidade_nome=');
		var posicao3=paginaAtual.indexOf('entidade_cidade=');
		var busca 	= "";
		//se o valor retornado for 0 ou mais, há uma busca pré-definida
		if(posicao1>=0 && posicao2>=0 && posicao3>=0){
			//recebe todo o conteúdo a partir da posicao1 até o final da string
			var auxbusca = paginaAtual.substr(posicao1,paginaAtual.length);
			busca+='&'+auxbusca;
		}
		return busca;
	}

	//REORDENACAO PADRÃO DAS LISTAGENS
	/*clique nas colunas de ordenação na listagem*/
	$('.reordenar_padrao').unbind('click');
	$('.reordenar_padrao').bind('click',function(e){
		e.preventDefault();
		//padrão do atributo REL da tag A: pagina|nomeColunaOrdenacao
		var auxRel =  $(this).attr('rel').split("|");
		var nomepagina = auxRel[0];
		var order = "order="+auxRel[1];
		//por padrão ordenação por ordem crescente (ASC)
		var ascdesc = "ascdesc=ASC";
		//se já se ordenou pela coluna atual e encontrou valor ASC na URL, troca para ordenação DESC
		if(paginaAtual.indexOf(order)>=0 && paginaAtual.indexOf(ascdesc)!=-1){
			ascdesc="ascdesc=DESC";
		}		
		//se encontrou na URL indicação para exibir todos os registros
		if(paginaAtual.indexOf('showAllRecords=true')>=0){
			showAllRecords='&showAllRecords=true';
		}else{
			showAllRecords='';
		}
		location=(nomepagina+".php?"+order+"&"+ascdesc+showAllRecords);
	});

	/*clique nas colunas de ordenação na listagem da index_pro*/
	$('.reordenar_pro').unbind('click');
	$('.reordenar_pro').bind('click',function(e){
		e.preventDefault();	
		var order = $(this).attr('rel');
		var ascdesc = "ASC";
		if(paginaAtual.indexOf(ascdesc)!=-1){
			ascdesc="DESC";
		}
		var exibeTodos = paginaAtual.indexOf('showAllRecords');
		if(exibeTodos>=0){
			showAllRecords='&showAllRecords=true';
		}else{
			showAllRecords='';
		}
		//tratamento dos filtros
		var buscas = getBuscasPro();
		//redireciona
		location=("index_pro.php?order="+order+"&ascdesc="+ascdesc+showAllRecords+buscas);
	});

	
	/*clique nas colunas de ordenação na listagem da index_doc*/
	$('.reordenar_doc').unbind('click');
	$('.reordenar_doc').bind('click',function(e){
		e.preventDefault();
		var paginaAtual = urlAtual.substr(urlAtual.lastIndexOf("/") + 1).replace(".","_");		
		var aux = $(this).attr('href').split("|");
		var idprocesso = aux[0];
		var order = aux[1];
		var ascdesc = "ASC";
		if(paginaAtual.indexOf(ascdesc)!=-1){
			ascdesc="DESC";
		}		
		location=("index_doc.php?p="+idprocesso+"&order="+order+"&ascdesc="+ascdesc);
	});
	/*clique nas colunas de ordenação na listagem da view_historico*/
	$('.reordenar_historico').unbind('click');
	$('.reordenar_historico').bind('click',function(e){
		e.preventDefault();
		var paginaAtual = urlAtual.substr(urlAtual.lastIndexOf(".php?") + 1).replace(".","_");		
		var aux = $(this).attr('rel').split("|");
		var order = aux[0];
		var tipo = aux[1];
		var idusuario = aux[2];
		var idprocesso = aux[3];
		var periodo_de = aux[4];
		var periodo_ate = aux[5];
		var ascdesc = "ASC";
		if(paginaAtual.indexOf(ascdesc)!=-1){
			ascdesc="DESC";
		}
		//se for para exibir todos
		if(paginaAtual.indexOf('showAllRecords')>=0){
			ascdesc+='&showAllRecords=true';
		}
		location=("view_historico.php?order="+order+"&ascdesc="+ascdesc+"&tipo="+tipo+"&idusuario="+idusuario+"&idprocesso="+idprocesso+"&periodo_de="+periodo_de+"&periodo_ate="+periodo_ate);
	});

	//botões "brilhantes" -> acendem e apagam
		//aplicação inicial do estilo
		$(".brilhante:not(.naobrilhante)")
			.animate({opacity:'0.1'}, 1800)
			.animate({opacity:'1'}, 1800);
		//intervalo de tempo em que o estilo será reaplicado
		timerId = setInterval(function() {
	     $(".brilhante:not(.naobrilhante)")
	     	.animate({opacity:'0.1'}, 1500)
	     	.animate({opacity:'1'}, 1500);
	    }, 4000);
	    //remove o efeito brilhante após a entrada do mouse nele
	    $('.brilhante').on({
			mouseenter:function(){
				$(this).removeClass('brilhante');
				$(this).addClass('naobrilhante').animate({opacity:'0.5'}, 800);
			}
		});

	//botões "CliqueAqui" -> recebem borda vermelha, acendem e apagam até o mouse ir neles
		//aplicação inicial do estilo
		$(".CliqueAqui:not(.naoCliqueAqui)")
			.animate({opacity:'0.1'}, 1100)
			.animate({opacity:'1'}, 1100)
			.css('border', '1px solid red');
		//intervalo de tempo em que o estilo será reaplicado
		timerId = setInterval(function() {
	     $(".CliqueAqui:not(.naoCliqueAqui)")
	     	.animate({opacity:'0.1'}, 1100)
	     	.animate({opacity:'1'}, 1100);
	    }, 4000);
	    //remove o efeito brilhante após a entrada do mouse nele
	    $('.CliqueAqui').on({
			mouseenter:function(){
				$(this).removeClass('CliqueAqui');
				$(this).addClass('naoCliqueAqui').animate({opacity:'1'}, 800).css('border', '1px solid black');
			}
		});

    //se encontrar algum elemento com data-toggle="tooltip", aplica a configuração de Tooltip!
	$('[data-toggle="tooltip"]').tooltip();   

	/* q_fluxo */
		$('.q_fluxo_nav .botao').click(function(){
			$('.q_fluxo_nav .botao').removeClass("bActive");
			$(this).addClass("bActive");
			$('.q_fluxo .texto_botao').hide();
			$('.q_fluxo .seta').hide();
			var botao = $(this).attr('id');
			$('.q_fluxo #texto_'+botao).slideDown();
			$('.q_fluxo #seta_'+botao).slideDown();
		});
	/* fim q_fluxo */

	
	$('.glyphicon-print').unbind('click');
	$('.glyphicon-print').bind('click',function(e){
		e.preventDefault();
		window.print();
	});

	$('.show_all_records').unbind('click');
	$('.show_all_records').bind('click',function(e){
		e.preventDefault();
		var redirecionamento = urlAtual;
		var fimUrl = urlAtual.indexOf('.php');
		var semParametros = urlAtual.length-4;
		if(fimUrl==semParametros){
			redirecionamento+='?';
		}else{
			redirecionamento+='&';
		}
		//esconde mensagem possivelmente atribuida via GET (s=ALGUMNUMERO Ex.: s=1)
		var redirecionamento = redirecionamento.replace("s=","");
		location=(redirecionamento+"showAllRecords=true");
	});

	/*clique nas colunas de ordenação na listagem da view_historico_pro*/
	$('.reordenar_historico_pro').unbind('click');
	$('.reordenar_historico_pro').bind('click',function(e){
		e.preventDefault();
		var paginaAtual = urlAtual.substr(urlAtual.lastIndexOf(".php?") + 1).replace(".","_");		
		var aux = $(this).attr('rel').split("|");
		var order = aux[0];
		var requestp = aux[1];
		var tipo = aux[2];
		var idusuario = aux[3];
		var idprocesso = aux[4];
		var periodo_de = aux[5];
		var periodo_ate = aux[6];
		var ascdesc = "ASC";		
		if(paginaAtual.indexOf(ascdesc)!=-1){
			ascdesc="DESC";
		}
		//se for para exibir todos
		if(paginaAtual.indexOf('showAllRecords')>=0){
			ascdesc+='&showAllRecords=true';
		}
		location=("view_historico_pro.php?order="+order+"&p="+requestp+"&tipo="+tipo+"&idusuario="+idusuario+"&idprocesso="+idprocesso+"&periodo_de="+periodo_de+"&periodo_ate="+periodo_ate+"&ascdesc="+ascdesc);
	});

	$('.link_sem_acao').unbind('click');
	$('.link_sem_acao').bind('click',function(e){
		e.preventDefault();
	});
		
	//MODELO
		/*clique no botão DELETAR na listagem*/
			$('.del_modelo').unbind('click');
			$('.del_modelo').bind('click',function(){
				var aux = $(this).parents("tr");
				var id = aux.attr('id').replace("id_","");
				var op = confirm("Você está prestes a deletar este modelo. Confirma a operação?");
				if(op===true){		
					var resposta = $.ajax({
						type: 'POST',
						url: 'del_modelo.php',
						async: false,
						dataType: 'text',	
						data: {id:id} }).responseText;
					if(resposta.indexOf("sucesso")!=-1){				
						showMsgSucesso('Modelo de documento excluído com sucesso','');
						aux.fadeOut();
					}else{
						showMsgErro('O Modelo de documento não pôde ser excluído','Tente novamente mais tarde');						
					}
				}
			});
	//SUBSECAO
		/*clique no botão DELETAR na listagem*/
			$('.del_subsecao').unbind('click');
			$('.del_subsecao').bind('click',function(){
				var aux = $(this).parents("tr");
				var id = aux.attr('id').replace("id_","");
				var op = confirm("Você está prestes a deletar esta subseção. Confirma a operação?");
				if(op===true){		
					var resposta = $.ajax({
						type: 'POST',
						url: 'del_subsecao.php',
						async: false,
						dataType: 'text',
						data: {id:id} }).responseText;
					if(resposta.indexOf("sucesso")!=-1){				
						showMsgSucesso('Subseção excluída com sucesso','');
						aux.fadeOut();
					}else{
						showMsgErro('A Subseção não pôde ser excluída','Tente novamente mais tarde');						
					}
				}
			});
	//DOCUMENTO TIPO
		/*clique no botão DELETAR na listagem*/
			$('.del_documentotipo').unbind('click');
			$('.del_documentotipo').bind('click',function(){
				var aux = $(this).parents("tr");
				var id = aux.attr('id').replace("id_","");
				var op = confirm("Você está prestes a deletar este tipo de documento. Confirma a operação?");
				if(op===true){		
					var resposta = $.ajax({
						type: 'POST',
						url: 'del_doctipo.php',
						async: false,
						dataType: 'text',
						data: {id:id} }).responseText;
					if(resposta.indexOf("sucesso")!=-1){				
						showMsgSucesso('Tipo de documento excluído com sucesso','');
						aux.fadeOut();
					}else{
						showMsgErro('O tipo de documento não pôde ser excluído','Tente novamente mais tarde');						
					}
				}
			});
	
	//ETAPAS
		/*clique nas colunas de ordenação na listagem*/
			$('.reordenar_etapa').unbind('click');
			$('.reordenar_etapa').bind('click',function(e){
				e.preventDefault();
				var order = $(this).attr('rel');
				var ascdesc = "ASC";
				if(paginaAtual.indexOf(ascdesc)!=-1){
					ascdesc="DESC";
				}
				//se for para exibir todos
				if(paginaAtual.indexOf('showAllRecords')>=0){
					ascdesc+='&showAllRecords=true';
				}
				location=("index_etapa.php?order="+order+"&ascdesc="+ascdesc);
			});
		/*clique no botão DELETAR na listagem*/
			$('.del_etapa').unbind('click');
			$('.del_etapa').bind('click',function(){
				var aux = $(this).parents("tr");
				var id = aux.attr('id').replace("id_","");
				var op = confirm("Você está prestes a deletar esta etapa. Confirma a operação?");
				if(op===true){		
					var resposta = $.ajax({
						type: 'POST',
						url: 'del_etapa.php',
						async: false,
						dataType: 'text',
						data: {id:id} }).responseText;
					if(resposta.indexOf("sucesso")!=-1){				
						showMsgSucesso('Etapa excluída com sucesso','');
						aux.fadeOut();
					}else{
						showMsgErro('A etapa não pôde ser excluída','Tente novamente mais tarde');						
					}
				}
			});
	







	
	var num_re = 0;
	$('.add_re').unbind("click");
	$('.add_re').bind("click",function(){
		num_re++;
		var add = addResponsavel(num_re,'');
		$('#responsavel').prepend(add);		
		$('.del_re').unbind("click");
		$('.del_re').bind("click",function(){
			var idre = $(this).attr("id").replace("del_","");
			$('.'+idre).slideUp();
			$('.'+idre).html('');
		});
	});









	/*clique no botão EDITAR PROCESSO na listagem da index_doc*/
	$('.edit_processo').unbind('click');
	$('.edit_processo').bind('click',function(){
		var idprocesso = $(this).attr('id').replace("processo_","");
		location=("edit_pro.php?p="+idprocesso+"&r="+pagActive);	
	});

	$('.brilhante').unbind("click");
	$('.brilhante').bind("click",function(){
		var obs = $(this).attr('itemprop');
		$('#myModalContent').html(obs);		
	});
	

	/*Paginação: clique nas colunas de navegação*/
	$('.paginacao_nav').unbind('click');
	$('.paginacao_nav').bind('click',function(e){
		e.preventDefault();
		var link = $(this).attr('rel');
		location=(link);
	});

	/*clique no botão DELETAR na listagem do index_user*/
	$('.usuario_del').unbind('click');
	$('.usuario_del').bind('click',function(){
		var usuario = $(this).parents("tr");
		var idusuario = usuario.attr('id').replace("usuario_","");
		var op = confirm("Você está prestes a deletar este usuário. Confirma a operação?");
		if(op===true){		
			var resposta = $.ajax({
				type: 'POST',
				url: 'del_user.php',
				async: false,
				dataType: 'text',	
				data: {idusuario:idusuario} }).responseText;
			if(resposta.indexOf("sucesso")!=-1){
				showMsgSucesso('Usuário excluído com sucesso','');
				usuario.fadeOut();
			}else{
				showMsgErro('O Usuário não pôde ser excluído','Tente novamente mais tarde');
			}
		}
	});

	/* clique no botão FILTRAR (status) na listagem de processos */
	$('#filtro_pro_status').unbind('click');
	$('#filtro_pro_status').bind('click',function(e){
		e.preventDefault(); 
		var resposta = $.ajax({
			type: 'POST',
			url: 'ajax.php',
			async: false,
			dataType: 'text',
			data: {tipo:'filtro_pro_status',status:getStatusPro()} }).responseText;
		if(resposta.indexOf("erro_filtro")!=-1){
			/*se der problema*/
			resposta="Um erro ocorreu, tente novamente mais tarde";
		}
		$(this).parent().append('<div class="box_filtro">'+resposta+'</div>');
		$('#filtro_pro_status').slideUp();
		$('.box_filtro, .close_filtro').slideDown();
		$('.close_filtro').unbind("click");
		$('.close_filtro').on("click",function(){
			$('.close_filtro, .box_filtro').slideUp();
			$('#filtro_pro_status').slideDown();
			$(".box_filtro").detach();
		});
	});

	jQuery.datetimepicker.setLocale('pt-BR');
	jQuery('.datetimepicker > input').datetimepicker({
		format:'d/m/Y H:i',
				i18n:{
		  de:{
		   months:[
		    'Janeiro','Fevereiro','Março','Abril',
		    'Maio','Junho','Julho','Agosto',
		    'Setembro','Outubro','Novembro','Dezembro',
		   ],
		   dayOfWeek:[
		    "Seg", "Ter", "Qua", "Qui", 
		    "Sex", "Sab", "Dom",
		   ]
		  }
		 },
	});

	$(document).delegate('input','focus',function(){
		$('.input-group.date').datepicker({
			inline: true,
		    format: "dd/mm/yyyy",
		    language: "pt-BR",
		    autoclose: true
		});
		$('.date > input').mask("00/00/0000", {placeholder: "__/__/____"});
	});
	
	$("form").delegate('input','focus',function(){
		/*mascara para telefones*/
		$('.campocelular').mask("(99) 99999-9999");
	   	$('.campotelefone').mask("(99) 9999-9999");
		/*mascara para apenas Strings 0-9 e a-Z (50 caracteres)*/
		$('.camposomente09AZ50').mask('AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA');
		/*mascara para apenas Strings 0-9 e a-Z (200 caracteres)*/
		$('.camposomente09AZ200').mask('AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA');
		/*mascara para apenas Strings 0-9 e a-Z (20 caracteres)*/
		$('.camposomente09AZ20').mask('AAAAAAAAAAAAAAAAAAAA');
		/*mascara para aceitar só:  0-9 (50 caracteres)*/
		$('.camposomente0950').mask('YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY', {
		    translation: {
		      'Y': {
		        pattern: /[0-9]/, optional: true
		      }
		    }
		  });
		$('.camposomente09AZponto50').mask('YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY', {
		    translation: {
		      'Y': {
		        pattern: /[0-9a-zA-Z.]/, optional: true
		      }
		    }
		});
		$('.campocpf').mask('YYY.YYY.YYY-YY', {
		    translation: {
		      'Y': {
		        pattern: /[0-9]/, optional: true
		      }
		    }
		  });
		$('.campocnpj').mask('YY.YYY.YYY/YYYY-YY', {
		    translation: {
		      'Y': {
		        pattern: /[0-9]/, optional: true
		      }
		    }
		  });
		$('.camposomente09pontos5').mask('YYYYY', {
		    translation: {
		      'Y': {
		        pattern: /[0-9.,]/, optional: true
		      }
		    }
		  });
		$('.camposomente09pontos7').mask('YYYYYYY', {
		    translation: {
		      'Y': {
		        pattern: /[0-9.,]/, optional: true
		      }
		    }
		  });
		$('.camposomente0910').mask('YYYYYYYYYY', {
		    translation: {
		      'Y': {
		        pattern: /[0-9]/, optional: true
		      }
		    }
		  });
		$('.camposomente094').mask('YYYY', {
		    translation: {
		      'Y': {
		        pattern: /[0-9]/, optional: true
		      }
		    }
		  });
		$('.camposomente0920').mask('YYYYYYYYYYYYYYYYYYYY', {
		    translation: {
		      'Y': {
		        pattern: /[0-9]/, optional: true
		      }
		    }
		  });
		/*mascara para aceitar só:  0-9, a-Z, @ e . (255 caracteres)*/
		$('.campodeemail255').mask('ZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZ', {
		    translation: {
		      'Z': {
		        pattern: /[0-9a-zA-Z@._-]/, optional: true
		      }
		    }
		  });



	});

	/*SPAF "NOVAS" --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	/*SPAF "NOVAS" --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	/*SPAF "NOVAS" --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	$(".show_div").click(function(e){
		e.preventDefault();
		var iddiv 	= $(this).attr('id');
		//se tiver uma ID na div "show_div"
		if(iddiv!=null){
			//exibe/esconde exatamente a div referente ao ID
			$("#"+iddiv+"_escondida").toggle();
		}else{
			$(".show_div_escondida").toggle();
		}    	
	});
	//função faz com que botões com "values" façam redirecionar a página de acordo com este valor
	$(".btn").click(function(e){
		if(this.value!==false && this.value!=null && this.value!=""){
			var url = this.value;
			location=(url);
		}
	});
	
	//ENVIO DE EMAILS
		//variavel para controlar o número de cliques no botão escolher usuario, só pode 1 clique por vez
		var email_clique=0;
		/* clique no botão escolher usuario para enviar email */
		$(document).on('click','#envia_email_processo',function(e){
			e.preventDefault();
			//se for o primeiro clique
			if(email_clique==0){
				email_clique++;
				//pega o idprocesso da div em que o botão está inserido
				var aux 	= $(this).parents("div");
				var idprocesso 	= aux.attr('id');
				//pega o campo imediatamente anterior (idusuario hidden), para só exibir na listagem a instituição atual do processo
				var idinstituicao 	= $(this).prev().val();
				var resposta = $.ajax({
					type: 'POST',
					url: 'ajax.php',
					async: false,
					dataType: 'text',
					data: {tipo:'dados_envio_email',idprocesso:idprocesso,idinstituicao:idinstituicao} }).responseText;
				if(resposta.indexOf("erro_filtro")!=-1){
					/*se der problema*/
					resposta="Um erro ocorreu, tente novamente mais tarde";
				}

				$(this).next('#dados_envio_email').html(resposta);
				$('#dados_envio_email').slideDown();
			}
		});

		/* clique no botão enviar email */
		$(document).on('click','#encaminha_email',function(e){
			e.preventDefault();
			var erro = false;
			var destinatario = "";
			var mensagem = "";
			var idprocesso = "";
			destinatario 	= document.envia_email.destinatario.value;
			mensagem 		= document.envia_email.mensagem.value;
			idprocesso 			= document.envia_email.idprocesso.value;
			//nesse caso usamos as funções aninhadas pois trata-se de um ID (#), se fosse uma classe (vários elementos) não seria recomendável!
			$('#dados_envio_email').hide(100,function(){
				$('#dados_envio_email').html("<div class=\"well\"><strong>Enviando e-mail, aguarde por favor...</strong></div>");
				$('#dados_envio_email').fadeIn(800,function(){
					$('#dados_envio_email').slideDown(800,function(){
						var resposta = $.ajax({
							type: 'POST',
							url: 'ajax_mail.php',
							async: false,
							dataType: 'text',
							data: {destinatario:destinatario,mensagem:mensagem,idprocesso:idprocesso} }).responseText;
						if(resposta.indexOf("erro")!=-1){
							$('#dados_envio_email').html('<div class=\"well\"><strong>Erro ao enviar e-mail, tente novamente mais tarde!</strong></div>');
						}else{
							$('#dados_envio_email').html(resposta);
						}				
						//permite o clique novamente no botão "escolher usuário para enviar e-mail"	
						email_clique=0;
					});
				});
			});

		});

		/* clique no botão cancelar envio email */
		$(document).on('click','#cancela_email',function(e){
			e.preventDefault();
			$('#dados_envio_email').slideUp();
			//permite clicar novamente
			email_clique=0;
		});

});
//FIM TRECHO EXECUTADO SOMENTE APÓS CARREGAMENTO DA PÁGINA

//Responsáveis pelo processo
function addResponsavel(num_re, idusuario){
	var saida 	= "<div class='divresponsavel responsavel_"+num_re+"'><br>";
	saida		+="<label class='col-sm-2 control-label'>Usuário responsável</label>";
	saida		+="<div class='col-sm-12'>";
	saida		+="<select class='form-control'  name='re_idusuario_"+num_re+"' id='re_idusuario_"+num_re+"'>";
	saida		+="<option value='-1'>Selecione</option>";
	//recupera valores do INPUTHIDDEN, que armazena os usuários da Comissão de Instrução
	$('input[name^="usuariosresponsaveis"]').each(function() {
		var vaux = $(this).val().split('||||||');
		var id = vaux[0];
		var nome = vaux[1];
	    saida	+="<option value='"+id+"' ";
					if(idusuario==id){
		saida	+="selected='selected'";
					}
		saida	+=">"+nome+"</option>";
	});
	saida		+="</select>";
	saida		+="</div>";
	saida		+="<center><button type='button' class='btn btn-default btn-sm del_re' id='del_responsavel_"+num_re+"' style='margin-top:12px;'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span> Remover Responsável</button></center>";
	saida		+="</div>";
	return  saida;
}