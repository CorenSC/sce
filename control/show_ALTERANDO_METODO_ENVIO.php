<?php


ini_set( 'display_errors', TRUE );
error_reporting( E_ALL | E_STRICT );
ini_set('memory_limit', '800M');//memória máxima de 800 MB
//ini_set('max_execution_time', '3600000');//1 hora

//256M é o tamanho máximo de arquivo

//inclui variaveis e informações gerais do sistema
require_once('../config.php');
//carrega as bibliotecas para recuperar informações do BD
require_once("../conexao.php");
require_once("../login_verifica.php");
require_once('../model/Registry.php');
require_once('../model/Documento.php');
require_once('../model/Pad.php');
require_once('../model/Usuario.php');
require_once('../dao/DocumentoDAO.php');
require_once('../bin/functions.php');
// Armazenar essa instância (conexão) no Registry - conecta uma só vez
$registry = Registry::getInstance();
$registry->set('Connection', $myBD);
// Instanciar o DAO para retornar infos da base
$documentoDAO = new DocumentoDAO();

//key_encrypt(time().'_'.$iddocumento);
$aux = explode("_", key_decrypt(urldecode($_GET["f"])));
$iddocumento = $aux[1];
$documento = new Documento();
$documento->setId($iddocumento);
$results = $documentoDAO->show($documento);
if($results["arlink"]!==NULL){
    $LinkAR=$results["arlink"];
}else{
    $LinkAR=false;
}

//infos do Documento
$documento->setPad($results['idpad']);
$documento->setDtRecebimento($results['dtrecebimento']);
$documento->setVolume($results['volume']);
$documento->setLink($results['link']);
$documento->setDocumentoTipo($results['nome']);
$documento->setUsuario($results['nomeusuario']);
//1 é TRUE, 2 é FALSE
$documento->setPaginaBranca($results['paginabranca']);
$documento->setCarimboNum($results['carimbonum']);
$documento->setCarimboAss($results['carimboass']);
$documento->setCarimboOriginal($results['carimbooriginal']);

//padrão de "retirada para impressão" das páginas em branco:
if(isset($_GET["pb"]) && !empty($_GET["pb"])){
    $documento->setPaginaBranca(2);
}

//infos do PAD
$pad = new Pad();
$pad->setId($results['idpad']);
$pad->setAno($results['ano']);
$pad->setNumero($results['numero']);
//infos do Usuario
$usuario = new Usuario();
$usuario->setNome($results['nomeusuario']);
$usuario->setRubrica(APP_URL_UPLOAD.'carimbos/'.$results['rubrica']);
$usuario->setAssinatura(APP_URL_UPLOAD.'carimbos/'.$results['assinatura']);
//$usuario->setPaginaAssinatura(APP_URL_UPLOAD.'carimbos/'.$results['paginaassinatura']);
$usuario->setFuncao($results['funcao']);
$usuario->setMatricula($results['matriculausuario']);
//infos do Usuario do Carimbo "Conforme Original"
$usuarioCO = new Usuario();
$usuarioCO->setNome($results['usuarioconformeoriginal']);
$usuarioCO->setAssinatura(APP_URL_UPLOAD.'carimbos/'.$results['assinaturaconformeoriginal']);
$usuarioCO->setSetor($results['nomesetorconformeoriginal']);
$usuarioCO->setMatricula($results['matricula']);
//outras infos
$documentoUsuarioDataConferencia=exibeDataSemTimestamp($results['dtconformeoriginal']);

//número de folhas antes de apresentar este (para saber qual é a numeração inicial)
//"O número da folha inicial de um documento, é sempre a soma das folhas anteriores +1"
$numfolha = $documentoDAO->showFolhasAnteriores($documento);
if($numfolha['totalfolhasanteriores']!=NULL){
    $documentoTotalFolhasAnteriores = ($numfolha['totalfolhasanteriores']+1);    
}else{
    $documentoTotalFolhasAnteriores = 1;
}

    //inclui as bibliotecas para gerar o PDF
    require_once('../bin/tcpdf/config/tcpdf_config.php');
    require_once('../bin/tcpdf/tcpdf.php');
    require_once('../bin/tcpdf/tcpdi.php');
    //inclui a classe de geração de PDF's do Coren
    require_once('../model/PDF.php');

    // Cria um novo documento PDF.
    $pdf = new PDFCoren(PDF_PAGE_ORIENTATION, 'mm', PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $arquivo = APP_URL_UPLOAD.$documento->getPad().'/'.$documento->getLink();

    //armazena em pagecount as paginas do arquivo atual
    $pagecount = $pdf->setSourceData(file_get_contents($arquivo));

    //DEFINE AS INFORMAÇÕES DO PDF
        //flags => 1 é TRUE e 2 é FALSE
            //se insere o carimbo conforme o original ou NÃO
            $pdf->setFlagConformeOriginal($documento->getCarimboOriginal());
            //se insere o carimbo número da folha com rubrica ou NÃO
            $pdf->setFlagNumeroFolha($documento->getCarimboNum());
            //se insere a assinatura do usuário ou NÃO
            $pdf->setFlagPaginaAssinatura($documento->getCarimboAss());
            //se insere o carimbo página em branco ou NÃO
            $pdf->setFlagPaginaEmBranco($documento->getPaginaBranca());

        //se tiver inserido
        if($pdf->getFlagNumeroFolha()==1){
            //link para rubrica do usuário
            $pdf->setRubrica($usuario->getRubrica());
        }
        
        //nome do usuário que gerou o documento
        $pdf->setUsuario($usuario->getNome());
        //função do usuário que gerou o documento
        $pdf->setUsuarioFuncao($usuario->getFuncao());
        //Matricula do usuário que gerou o documento
        $pdf->setUsuarioMatricula($usuario->getMatricula());
        //Assinatura do usuário que gerou o documento
        $pdf->setUsuarioAssinatura($usuario->getAssinatura());
        //nome do documento
        $pdf->setTitulo($documento->getDocumentoTipo());
        //ano e número do PAD (para gerar o nome do arquivo .pdf)
        $pdf->setPADAno($pad->getAno());
        $pdf->setPADNumero($pad->getNumero());

        //carimbo conforme original
            if($pdf->getFlagConformeOriginal()==1 || $usuarioCO->getNome()!=null){
                //link para assinatura do usuário (conforme o original)
                $pdf->setAssinaturaCO($usuarioCO->getAssinatura());
                //setor do usuário
                $pdf->setSetorCO($usuarioCO->getSetor());
                //matrícula do usuário
                $pdf->setMatriculaCO($usuarioCO->getMatricula());
                //data de conferência (conforme o original)
                $pdf->setDataCO($documentoUsuarioDataConferencia);
            }

        if($pdf->getFlagPaginaAssinatura()==1){
            //link para página com assinatura do usuário
            //$pdf->setPaginaAssinatura($usuario->getPaginaAssinatura());
            $pdf->setPaginaAssinatura($usuario->getAssinatura());
        }

        //numero da folha - sempre relativo ao total de folhas no PAD
        //o número da folha inicial de um documento é sempre a soma das anteriores
        //aí, na primeira iteração, o número da página é $pdf->setNumeroFolha($pdf->getNumeroFolha() + 1);
        //setNumeroFolha recebe ZERO pois este controle é interno, para saber se a página é impar, posição no DOCUMENTO
        //setTotalFolhasAnteriores recebe o total de folhas dos documentos anteriores para exibir corretamente o número da folha nos carimbos
        $pdf->setNumeroFolha(0);
        $pdf->setTotalFolhasAnteriores($documentoTotalFolhasAnteriores);


    //para cada página faça
    for ($i = 1; $i <= $pagecount; $i++) {

        //importe a página para o template padrão
        $tplidx = $pdf->importPage($i);
        //adicione a página ao template
        $pdf->AddPage();
        //adiciona nova página
        $pdf->setNumeroFolha($pdf->getNumeroFolha() + 1);
        //insere o doc enviado com o tamanho reduzido
        $pdf->useTemplate($tplidx,10,21,'190','269',true);
        //OLD:  $pdf->useTemplate($tplidx,5,15,'185','277',true);
        //TAMANHO MAXIMO (TODA TELA):
        //$pdf->useTemplate($tplidx,0,0,'210','297',true);

        
        //inserção do carimbo NÚMERO DA FOLHA COM RUBRICA
        //se igual a 1 é TRUE, se igual a 2 é FALSE
        if($pdf->getFlagNumeroFolha() == 1){
            $pdf->carimboNumFolha();
        }
        
        //inserção do carimbo CONFORME O ORIGINAL
        //se igual a 1 é TRUE, se igual a 2 é FALSE
        if($pdf->getFlagConformeOriginal() == 1 || $usuarioCO->getNome()!=null){
            $pdf->carimboConfereComOriginal();
        }        

        //inserção da PÁGINA COM ASSINATURA DO USUÁRIO
        //se igual a 1 é TRUE, se igual a 2 é FALSE
        if($pdf->getFlagPaginaAssinatura() == 1){
            //Se $i == $pagecount então é a última folha após a última folha insira a página de assinatura
            if($i == $pagecount){
                //$pdf->inserePaginaAssinaturaAutor();
                $pdf->insereAssinaturaAutor();
            }
        }

        //insere uma página em branco com o carimbo "PAGINA EM BRANCO"
        //se flag estiver ativa (igual a 1 é TRUE, se igual a 2 é FALSE)
        //OU se o doc tiver 1 só folha e a opção "remover página em branco" estiver inativa = insere página em branco
        if($pdf->getFlagPaginaEmBranco() == 1 || ( $pagecount==1 && !isset($_GET["pb"]) ) ){
            $pdf->inserePaginaEmBranco();
        }

        //Limpa o buffer de saída para permitir e agilizar a geração do PDF
        ob_clean();
    }


    //TRECHO AR
    if($LinkAR!==false){

        $arquivo = APP_URL_UPLOAD.$documento->getPad().'/'.$LinkAR;
        //armazena em pagecount as paginas do arquivo atual
        $pagecount = $pdf->setSourceData(file_get_contents($arquivo));
        
        //para cada página faça
        for ($i = 1; $i <= $pagecount; $i++) {

            //importe a página para o template padrão
            $tplidx = $pdf->importPage($i);
            //adicione a página ao template
            $pdf->AddPage();
            //adiciona nova página
            $pdf->setNumeroFolha($pdf->getNumeroFolha() + 1);
            //insere o doc enviado com o tamanho reduzido
            $pdf->useTemplate($tplidx,10,21,'190','269',true);
            //OLD:  $pdf->useTemplate($tplidx,5,15,'185','277',true);
            //TAMANHO MAXIMO (TODA TELA):
            //$pdf->useTemplate($tplidx,0,0,'210','297',true);

            
            //inserção do carimbo NÚMERO DA FOLHA COM RUBRICA
            //se igual a 1 é TRUE, se igual a 2 é FALSE
            if($pdf->getFlagNumeroFolha() == 1){
                $pdf->carimboNumFolha();
            }
            
            //inserção do carimbo CONFORME O ORIGINAL
            //se igual a 1 é TRUE, se igual a 2 é FALSE
            if($pdf->getFlagConformeOriginal() == 1 || $usuarioCO->getNome()!=null){
                $pdf->carimboConfereComOriginal();
            }        

            //insere uma página em branco com o carimbo "PAGINA EM BRANCO"
            //se igual a 1 é TRUE, se igual a 2 é FALSE
            if($pdf->getFlagPaginaEmBranco() == 1){
                $pdf->inserePaginaEmBranco();
            }
            
            /*
            AR NÃO TEM INSERÇÃO DE ASSINATURA
            //inserção da PÁGINA COM ASSINATURA DO USUÁRIO
            //se igual a 1 é TRUE, se igual a 2 é FALSE
            if($pdf->getFlagPaginaAssinatura() == 1){
                //Se $i == $pagecount então é a última folha após a última folha insira a página de assinatura
                if($i == $pagecount){
                    //$pdf->inserePaginaAssinaturaAutor();
                    $pdf->insereAssinaturaAutor();
                }
            }
            */

            //Limpa o buffer de saída para permitir e agilizar a geração do PDF
            ob_clean();
        }
    }
    

    //Adiciona ao arquivo PDF informações de autoria do documento
    $pdf->addInformacoesAutoria();
    //Insere opções ou configurações para que o arquivo PDF fique da melhor maneira possível
    $pdf->formataArquivo();
    //Limpa o buffer de saída para permitir e agilizar a geração do PDF
    ob_clean();
    //Manda exibir na tela o PDF (e define um texto dentro do Output para que o arquivo tenha o nome correto do documento)
    //$pdf->Output('PAD'.$pad->getAno().$pad->getNumero().'__'.$documento->getDocumentoTipo().'.pdf');
    $nomePDF='PAD'.$pad->getAno().$pad->getNumero().'__'.$documento->getDocumentoTipo();
    $nomeTemp=APP_URL_UPLOAD.'temp/'.md5(rand(1,9999).$nomePDF).'.pdf';
    $pdf->Output($nomeTemp, "F");
    unset($pdf);

    header('Content-Type: application/pdf');
    header("Content-Disposition: inline; filename=\"".$nomePDF.".pdf\";");
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    readfile($nomeTemp);
?>