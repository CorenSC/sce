<?php
/*


Classe para gerar PDF personalizados do CorenSC


*/

class PDFCoren extends TCPDI { 

	//Page header 
	public function Header() { } 
	//Page footer 
	public function Footer() { } 

	//ATRIBUTOS PADRÃO
		//link para rubrica padrão
		public $rubricaPadrao='../carimbos/rubricapadrao.png';
		//link para assinatura padrão
		public $assinaturaPadrao='../carimbos/asspadrao.png';	
		//link para pagina com assinatura padrão
		public $paginaAssinaturaPadrao='../carimbos/paginaasspadrao.pdf';
		//setor padrão
		public $setorPadrao='NÃO IDENTIFICADO';
		//matricula padrão
		public $matriculaPadrao='0';
		//data padrão
		public $dataPadrao='SEM DATA';
	//INFORMAÇÕES DO PDF
		//titulo do pdf
		private $documentoTitulo = NULL;
		//nome do usuário que enviou o documento
		private $documentoUsuarioNome = NULL;
		//rubrica do usuário
		private $documentoUsuarioRubrica = NULL;
		//página de assinatura do usuário
		private $documentoUsuarioPaginaAssinatura = NULL;
		//assinatura do usuário que inseriu o carimbo "Conforme o Original"
		private $documentoAssinaturaCO = NULL;
		//setor do usuário que inseriu o carimbo "Conforme o Original"
		private $documentoSetorCO = NULL;
		//matrícula do usuário que inseriu o carimbo "Conforme o Original"
		private $documentoMatriculaCO = NULL;
		//data em que o usuário inseriu o carimbo "Conforme o Original"
		private $documentoDataCO = NULL;

	/*
	-	Título do documento
	-	@type:	String - Se trata do título do documento
	-	@use:	Usado no nome do documento e nas propriedades do mesmo
	*/
		function setTitulo($titulo=NULL){
			$this->documentoTitulo=$titulo;
		}
		function getTitulo(){
			return $this->documentoTitulo;
		}

	/*
	-	Nome do usuário que enviou o documento
	-	@type:	String - Se trata do Nome do usuário que enviou o documento
	-	@use:	Usado nas propriedades do documento
	*/
		function setUsuario($usuario=NULL){
			$this->documentoUsuarioNome=$usuario;
		}
		function getUsuario(){
			return $this->documentoUsuarioNome;
		}

	/*
	-	Rubrica do documento
	-	@type:	String - Se trata de um link para a imagem que será inserida como Rubrica do usuário
	-	@use:	A Rubrica é usada junto ao carimbo de numeração de folhas
	*/
		function setRubrica($rubrica=NULL){
			$this->documentoUsuarioRubrica=$rubrica;
		}
		function getRubrica(){
			return $this->documentoUsuarioRubrica;
		}

	/*
	-	Página de Assinatura do documento
	-	@type:	String - Se trata de um link para o PDF que será inserido como Página de Assinatura do usuário
	-	@use:	A Página de Assinatura é usada como última página de um documento, sempre que esta opção for ativada
	*/
		function setPaginaAssinatura($assinatura=NULL){
			$this->documentoUsuarioPaginaAssinatura=$assinatura;
		}
		function getPaginaAssinatura(){
			return $this->documentoUsuarioPaginaAssinatura;
		}

	/*
	-	Assinatura do usuário "Conforme o Original"
	-	@type:	String - Se trata de um link para a imagem que será inserida como Assinatura do usuário
	-	@use:	A Assinatura é usada junto ao carimbo de "Conforme o Original"
	*/
		function setAssinaturaCO($assinatura=NULL){
			$this->documentoAssinaturaCO=$assinatura;
		}
		function getAssinaturaCO(){
			return $this->documentoAssinaturaCO;
		}

	/*
	-	Setor do Usuário que definiu que o documento é "Conforme Original"
	-	@type:	String
	-	@use:	Campo do SETOR usado para o carimbo "Conforme o Original"
	*/
		function setSetorCO($setor=NULL){
			$this->documentoSetorCO=$setor;
		}
		function getSetorCO(){
			return $this->documentoSetorCO;
		}

	/*
	-	Matrícula do Usuário que definiu que o documento é "Conforme Original"
	-	@type:	String
	-	@use:	Campo de MATRÍCULA usado para o carimbo "Conforme o Original"
	*/
		function setMatriculaCO($matricula=NULL){
			$this->documentoMatriculaCO=$matricula;
		}
		function getMatriculaCO(){
			return $this->documentoMatriculaCO;
		}

	/*
	-	Data em que o Usuário definiu que o documento é "Conforme Original"
	-	@type:	String
	-	@use: Campo de DATA usado para o carimbo "Conforme o Original"
	*/
		function setDataCO($data=NULL){
			$this->documentoDataCO=$data;
		}
		function getDataCO(){
			return $this->documentoDataCO;
		}

	//Funções de carimbo e etc:
		//função que insere uma página em branco com o carimbo "Página em Branco"
	    function inserePaginaEmBranco(){
	    		//insere página
	            $this->AddPage();

	            //avisa que vai iniciar uma transformação no documento
	            $this->StartTransform();
	            //faz a transformação do tipo Rotação em -45 graus
	            $this->Rotate(-45);
	            //define a fonte que será utilizada
	            $this->SetFont('','B',30);
	            //define a posição do texto
	            $this->SetXY(85,20);
	            //insere uma célula com borda e texto
	            $this->Cell(150,30,'PÁGINA EM BRANCO',1,1,'C',0,'');
	            //termina a transformação (rotação do documento)
	            $this->StopTransform();
	    }
	    //função que insere informações do Autor do documento no PDF.
	    function addInformacoesAutoria($autor='SPAF - Coren/SC',$titulo='SPAF - Coren/SC',$pad='Sem número'){
		    	//funções auto-explicativas: define autor, título, assunto, palavras-chave e o criador.
		        $this->SetAuthor($autor);
		        $this->SetTitle($titulo);
		        $this->SetSubject($titulo);
		        $this->SetKeywords('Documento enviado por '.$autor.' para o PAD '.$pad.' através do SPAF (Sistema de Processos Administrativos da Fiscalização) do Coren/SC');
		        $this->SetCreator('SPAF - Sistema de Processos Administrativos da Fiscalização do Coren/SC');
	    }
	    //função que formata o PDF com caracteristicas e definições no padrão desejado
	    function formataArquivo(){
		    	//define que o modo de visualização do arquivo (ao baixá-lo) será do tamanho da largura da tela
		    	$this->SetDisplayMode('fullwidth');
	    }
	    //função que insere o carimbo "Número da folha com Rubrica"
	    function carimboNumFolha($linkrubrica=NULL,$numfolha='1',$verso=false){
	    		//seta valores padrão
	    		if($linkrubrica==NULL){ $linkrubrica=$rubricaPadrao; }

	            $folhaAtual = 'Fls. nº ';
	            if($verso===true){

					//se o número da folha for ímpar, sem o V, se for par, mantêm o número anterior e insere o V
	                $calc1 = (int) $numfolha;
	                $calc2 = $calc1 % 2;
	                //se for ÍMPAR
	                if($calc2!=0){
		            	if($numfolha<10){
		                $folhaAtual.='00'.$numfolha;
			            }elseif($numfolha<100){
			                $folhaAtual.='0'.$numfolha;
			            }else{
			                $folhaAtual.=$numfolha;
			            }
			        //se for PAR
	                }else{
	                	--$numfolha;
		            	if($numfolha<10){
		                $folhaAtual.='00'.$numfolha;
			            }elseif($numfolha<100){
			                $folhaAtual.='0'.$numfolha;
			            }else{
			                $folhaAtual.=$numfolha;
			            }
	                	$folhaAtual.='v';
	                }
	                
	            }else{
	            	if($numfolha<10){
	                $folhaAtual.='00'.$numfolha;
		            }elseif($numfolha<100){
		                $folhaAtual.='0'.$numfolha;
		            }else{
		                $folhaAtual.=$numfolha;
		            }
	            }

	            //define a fonte que será utilizada
	            $this->SetFont('','',9);
	            //define a posição do texto
	            $this->SetXY(186,12);
	            //insere uma célula sem borda e com o texto definido na variavel "folhaAtual"
	            $this->Cell(0, 0, $folhaAtual , 0, 'R', false, false, '', '', true, 0, false);

	            //adiciona a rubrica do usuário
	            $this->Image($this->getRubrica(),185,4,20,20);
	    }
	    //função que insere o carimbo "Confere com o Original"
	    function carimboConfereComOriginal($linkAssinatura=NULL,$setor=NULL, $matricula=NULL, $dt=NULL){
	    		//seta valores padrão
	    		if($linkAssinatura==NULL){ 	$linkAssinatura=$assinaturaPadrao; 	}
	    		if($setor==NULL){ 			$setor=$setorPadrao; 				}
	    		if($matricula==NULL){ 		$matricula=$matriculaPadrao; 		}
	    		if($dt==NULL){ 				$dt=$dataPadrao; 					}
	    		//define a espessura da linha que será utilizada
		        $this->SetLineWidth(0.5);
		        //define a forma de um retângulo
		        $this->Rect(6, 5, 120, 10, 'D');

		        //seta a fonte que será utilizada
		        $this->SetFont('','B',10);
		        //define a posição do texto
		        $this->SetXY(6,5);
		        //insere uma célula sem borda e com o texto definido
		        $this->Cell(0, 0, 'CONFERE COM O ORIGINAL' , 0, 'R', false, false, '', '', true, 0, false,'T','M');
		        
		        //seta a fonte que será utilizada
		        $this->SetFont('','B',10);
		        //define a posição do texto
		        $this->SetXY(60,5);
		        //define o texto que será inserido ao lado do "Confere com o Original"
		        $texto1='Coren/SC - Setor: '.$setor;
		        //insere uma célula sem borda e com o texto definido na variavel "texto1"
		        $this->Cell(0, 0, $texto1, 0, 'R', false, false, '', '', true, 0, false,'T','M');
		        
		        //seta a fonte que será utilizada
		        $this->SetFont('','B',10);
		        //define a posição do texto
		        $this->SetXY(60,10);
		        //define o texto que será inserido abaixo do setor
		        $texto2='Em '.$dt.' - Matrícula: '.$matricula;
		        //insere uma célula sem borda e com o texto definido na variavel "texto2"
		        $this->Cell(0, 0, $texto2 , 0, 'R', false, false, '', '', true, 0, false,'T','M');
		        
		        //adiciona a assinatura do usuário
		        $this->Image($linkAssinatura,19,5,23,12);
	    }
	    //função que insere uma página com a assinatura do usuário
	    function inserePaginaAssinaturaAutor($linkRubrica=NULL, $linkPdfAssinatura=NULL,$numfolha='1',$opcaoPaginaEmBranco=false, $opcaoNumeroFolha=false, $carimboConformeOriginal=false, $linkAssinatura=NULL, $setor=NULL, $matricula=NULL, $dt=NULL){

	    		//seta valores padrão
		    	if($linkRubrica==NULL){ 		$linkRubrica=$rubricaPadrao; 				}
		    	if($linkPdfAssinatura==NULL){ 	$linkPdfAssinatura=$paginaAssinaturaPadrao;	}
	    		if($linkAssinatura==NULL){ 		$linkAssinatura=$assinaturaPadrao; 			}
	    		if($setor==NULL){ 				$setor=$setorPadrao; 						}
	    		if($matricula==NULL){ 			$matricula=$matriculaPadrao; 				}
	    		if($dt==NULL){ 					$dt=$dataPadrao; 							}
	    		if($opcaoPaginaEmBranco){ 		$insereV=false; 	}else{ 	$insereV=true; 	}
          
      			//define que o conteúdo desta página do PDF atual será a do arquivo $linkPdfAssinatura
                $this->setSourceData(file_get_contents($linkPdfAssinatura));
                //importe a página para o template padrão
                $tplidx = $this->importPage(1);
                //adicione a página ao template
                $this->AddPage();

                //insere o doc com o tamanho reduzido
                $this->useTemplate($tplidx,5,15,'185','277',true);

                //se for para inserir o carimbo com o número da folha com rubrica, faça:
                if($opcaoNumeroFolha){
	                //insere carimboNumFolha(rubrica do usuario, o número da folha, se vai o "v" ou não no número)
	                $this->carimboNumFolha($linkRubrica,$numfolha+1,$insereV);                
            	}

                //se for para inserir o carimbo de "conforme original", faça:
                if($carimboConformeOriginal){
                    //insere carimboConfereComOriginal(assinatura, setor, matricula, data da conferência)
                    $this->carimboConfereComOriginal($linkAssinatura,$setor,$matricula,$dt);
                }

                //calcula se o número da folha é impar
                $calc1 = (int) $numfolha+1;
                $calc2 = $calc1 % 2;
                //se o resto da divisão for diferente de zero é porque o número da página é IMPAR
                if($calc2!=0){
                	$paginaPar=false;
                	$paginaImpar=true;                	
                }else{
                	$paginaPar=true;
                	$paginaImpar=false;
                }
                
                //Se a opcaoPaginaEmBranco for TRUE, coloque a página em branco
                if($opcaoPaginaEmBranco || ($opcaoPaginaEmBranco===false && $paginaImpar)){
                	//insere uma página em branco com o carimbo "Página em Branco"
                	$this->inserePaginaEmBranco();	
                }
                
	    }

}
?>