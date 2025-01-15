<?php
/*

ARQUIVO COM FUNÇÕES GERAIS DO SISTEMA
				+
PEQUENAS FUNÇÕES DE CONTROLLER NO FINAL DO ARQUIVO (devido a falta de divisão da View/Controller)

*/
//script para codificação de dados (sem possibilidade de decodificação):
function codifica($valor){
	return md5(sha1(md5($valor.'@-+.').'!$%*').'ç^@#');
}

//função para validar LITERAIS antes de inserir na SQL
function sqlTrataString($string){
	//se a string possui algum valor
	if(isset($string)){
		//tira espaços no inicio e fim da string
		$string = trim($string);
		//retira caracteres específicos da string (injeção SQL)
		$encontre = 	array("'",'&quot;','"');
		$troquepor = 	array('','','');
		$string = str_replace($encontre, $troquepor, $string);
		return $string;//retorna a string
	}
	return false;//nas páginas com interação com o banco de dados, se retornar false de sqlTrataString() a consulta não é enviada ao banco de dados
}

//função para validar INTEIROS antes de inserir na SQL
function sqlTrataInteiro($inteiro){
	//se a variável $inteiro possui algum valor
	if(isset($inteiro)){
		if(is_numeric($inteiro)){//só entra aqui se o valor for número
			return $inteiro;//retorna o inteiro
		}
	}
	return false;//nas páginas com interação com o banco de dados, se retornar false de sqlTrataInteiro() a consulta não é enviada ao banco de dados
}

/* 	Função para validar entradas de inteiros não confiáveis
	Retorna TRUE caso seja um inteiro válido, FALSE caso contrário */
function validaInteiro($inteiro, $tamanho){
	$inteiro=trim($inteiro);
	//se as variaveis inteiro e tamanho possuem algum valor
	if(isset($inteiro) && isset($tamanho)){
		//se o inteiro for número
		if(is_numeric($inteiro)){
			//se o inteiro tiver tamanho menor ou igual ao que precisa ter ou se o argumento passado para $tamanho for zero (não tem limite de tamanho)
			if(strlen($inteiro)<=$tamanho || $tamanho == 0){
				//caso passe pelas validações acima, retorna TRUE
				return $inteiro;
			}
		}
	}
	//se chegar aqui é por que não é um inteiro válido, portanto return FALSE
	return false;
}
/* 	Função para validar entradas de literais não confiáveis
	Retorna TRUE caso seja um literal válido, FALSE caso contrário */
function validaLiteral($literal, $tamanho){
	$literal=trim($literal);
	//se as variaveis literal e tamanho possuem algum valor
	if(isset($literal) && isset($tamanho)){
		//se o literal tiver tamanho menor ou igual ao que precisa ter ou se o argumento passado para $tamanho for zero (não tem limite de tamanho)
		if(strlen($literal)<=$tamanho || $tamanho == 0){
			//caso passe pelas validações acima, retorna TRUE
			return $literal;
		}
	}
	//se chegar aqui é por que não é um literal válido, portanto return FALSE
	return false;
}
//função que retorna TRUE caso o usuário tenha acesso à função, FALSE caso contrário
function verificaFuncaoUsuario($idfuncao){
	$resultado=false;
	if(isset($_SESSION['USUARIO']['funcoes'])){
		if(in_array($idfuncao,$_SESSION['USUARIO']['funcoes'])){
						$resultado=true;
		}
	}
	return $resultado;		
}
//função que retorna TRUE caso o usuário tenha acesso ao processo, FALSE caso contrário
function verificaProcessoUsuario($idprocesso){
	$resultado=false;
	if(isset($_SESSION['USUARIO']['processos'])){
		if(in_array($idprocesso,$_SESSION['USUARIO']['processos']) || $idprocesso==0){
						$resultado=true;
		}
	}else{
	//se cair aqui é porque o usuário não tem restrições de exibição por processo
		$resultado=true;
	}
	return $resultado;		
}

function exibeData($dt){
	$novaData = 0;
	if(strlen($dt)==8){
		$ano=substr($dt,0,-4);
		$mes=substr($dt,4,-2);
		$dia=substr($dt,-2);
		$novaData=$dia.'/'.$mes.'/'.$ano;
	}else{
		return " - ";
	}
	return $novaData;
}

function exibeDataTimestamp($dt){
	$novaData = 0;
	if(strlen($dt)==19){
		$ano=substr($dt,0,4);
		$mes=substr($dt,5,2);
		$dia=substr($dt,8,2);
		$hora=substr($dt,11,2);
		$minuto=substr($dt,14,2);
		$segundos=substr($dt,17,2);
		//se data zerada então exibe um valor vazio ""
		if($ano==0000 && $mes==00 && $dia==00 && $hora==00 && $minuto==00 && $segundos==00){
			$novaData="";
		}else{
			$novaData=$dia.'/'.$mes.'/'.$ano.' '.$hora.':'.$minuto.':'.$segundos;
		}
	}else{
		return "";
	}
	return $novaData;
}

function exibeDataSemTimestamp($dt){
	$novaData = " - ";
	if(strlen($dt)==19){
		$ano=substr($dt,0,4);
		$mes=substr($dt,5,2);
		$dia=substr($dt,8,2);
		$novaData=$dia.'/'.$mes.'/'.$ano;
	}
	return $novaData;
}

function isLetraNumero($texto){
	if (preg_match('/^[a-z A-Z0-9]+$/', $texto)) {
		return true;
	}else{
		return false;
	}
}

/* Recebe uma data no padrão 31/12/2015 (dd/mm/yyyy) e transforma em 20151231 (yyyymmdd) */
function transformaDataBanco($dt){
	$novaData = explode("/",$dt);
	if(strlen($dt)==10 && !empty($novaData[0]) && !empty($novaData[1]) && !empty($novaData[2]) ){
		$ano=$novaData[2];
		$mes=$novaData[1];
		$dia=$novaData[0];
		$novaData=$ano.$mes.$dia;
	}else{
		return false;
	}
	return $novaData;
}
/* Recebe uma data no padrão 31/12/2015 (dd/mm/yyyy) e transforma em 2015/12/31 (yyyy/mm/dd) */
function transformaDataTimestamp($dt){
	$novaData = explode("/",$dt);
	if(strlen($dt)==10 && !empty($novaData[0]) && !empty($novaData[1]) && !empty($novaData[2]) ){
		$ano=$novaData[2];
		$mes=$novaData[1];
		$dia=$novaData[0];
		$novaData=$ano.'/'.$mes.'/'.$dia;
	}else{
		return false;
	}
	return $novaData;
}

function transformaDataTimestampBanco($dt){
	$novaData = 0;
	if(strlen($dt)==19){
		$dia=substr($dt,0,2);
		$mes=substr($dt,3,2);
		$ano=substr($dt,6,4);
		$hora=substr($dt,11,2);
		$minuto=substr($dt,14,2);
		$segundos=substr($dt,17,2);
		$novaData=$ano.'-'.$mes.'-'.$dia.' '.$hora.':'.$minuto.':'.$segundos;
	}elseif(strlen($dt)==16){
		$dia=substr($dt,0,2);
		$mes=substr($dt,3,2);
		$ano=substr($dt,6,4);
		$hora=substr($dt,11,2);
		$minuto=substr($dt,14,2);
		$novaData=$ano.'-'.$mes.'-'.$dia.' '.$hora.':'.$minuto.':00';
	}else{
		return " - ";
	}
	return $novaData;
}


function exibeTexto($txt,$limite=NULL){
	if(strlen($txt)>0){
		if($limite!=NULL){
			if(strlen($txt)>$limite){
				return substr( $txt, 0, $limite ).'...';
			}else{
				return $txt;
			}
		}else{
			return $txt;
		}
	}else{
		return "-";
	}
}

function insereInfo($tipo){
	$retorno="";//retorno que será enviado
	$msg="";//texto da mensagem
	$icone="";//ícone que será exibido
	if($tipo="sem_perfil"){
		$msg="Usuário sem acesso ao sistema pois o perfil em que estava atrelado foi excluído";
		$icone="glyphicon glyphicon-ban-circle";
		$retorno="<span style='color:red;' class='$icone' title='$msg'></span> ";
	}
	return $retorno;
}

function verificaExtensaoArquivo($nome,$extensaocorreta){
	$partida=strripos($nome,".")+1;
	$final=strlen($nome)-1;
	$extensaoencontrada=substr(strtolower($nome),$partida,$final);
	if($extensaoencontrada==strtolower($extensaocorreta)){
		return true;
	}else{
		return false;
	}
}

function retornaExtensaoArquivo($nome){
	$partida=strripos($nome,".")+1;
	$final=strlen($nome)-1;
	$extensaoencontrada=substr(strtolower($nome),$partida,$final);
	return $extensaoencontrada;
}

function getIpUsuario(){  
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) //se possível, obtém o endereço ip da máquina do cliente  
	{  
		$ip=$_SERVER['HTTP_CLIENT_IP'];  
	}  
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) //verifica se o ip está passando pelo proxy  
	{  
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];  
	}  
	else  
	{  
		$ip=$_SERVER['REMOTE_ADDR'];  
	}  
 return $ip;  
 }

//função que verifica o navegador do usuário.
function getUserBrowser(){
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	if (preg_match('|MSIE ([0-9].[0-9]{1,2})|',$useragent,$matched)) {
		//$browser_version=$matched[1];
		$browser = 'ie';
	} elseif (preg_match( '|Opera/([0-9].[0-9]{1,2})|',$useragent,$matched)) {
		//$browser_version=$matched[1];
		$browser = 'opera';
	} elseif(preg_match('|Firefox/([0-9\.]+)|',$useragent,$matched)) {
		//$browser_version=$matched[1];
		$browser = 'firefox';
	} elseif(preg_match('|Chrome/([0-9\.]+)|',$useragent,$matched)) {
		//$browser_version=$matched[1];
		$browser = 'chrome';
	} elseif(preg_match('|Safari/([0-9\.]+)|',$useragent,$matched)) {
		//$browser_version=$matched[1];
		$browser = 'safari';
	} else {
		// navegador do usuário não identificado
		//$browser_version = 0;
		$browser= 'other';
	}
	return $browser;
}
  
function exibeMes($inteiro){
	if($inteiro==1){
		return 'Janeiro';
	}elseif($inteiro==2){
		return 'Fevereiro';
	}elseif($inteiro==3){
		return 'Março';
	}elseif($inteiro==4){
		return 'Abril';
	}elseif($inteiro==5){
		return 'Maio';
	}elseif($inteiro==6){
		return 'Junho';
	}elseif($inteiro==7){
		return 'Julho';
	}elseif($inteiro==8){
		return 'Agosto';
	}elseif($inteiro==9){
		return 'Setembro';
	}elseif($inteiro==10){
		return 'Outubro';
	}elseif($inteiro==11){
		return 'Novembro';
	}elseif($inteiro==12){
		return 'Dezembro';
	}else{
		return 'ErroExibiçãoMês';
	}
}

function exibeVar($var){
	echo '<br><br><pre>';
	print_r($var);
	echo '</pre><br><br>';
}

function exibeVar2($var){
	echo '<script> console.log('.$var.'); </script>';
}

function key_encrypt($str){
	$tr = strtr( 
	    $str, 
	    array ( 
	      '1' => 'a', '2' => 'c', '3' => 'e', '4' => 'y', '5' => 'g',
	      '6' => 'b', '7' => 'v', '8' => 'w', '9' => 'z', '0' => 'h',
	      'a' => '8', 'j' => '9', 's' => 'n',
	      'b' => '1', 'k' => '3', 't' => 's',
	      'c' => '5', 'l' => '2', 'u' => 'm',
	      'd' => 'd', 'm' => 'l', 'v' => 'u',
	      'e' => '4', 'n' => 'j', 'w' => 'r',
	      'f' => 'f', 'o' => 'k', 'x' => 'o',
	      'g' => '6', 'p' => 'p', 'y' => 't',
	      'h' => '7', 'q' => 'i', 'z' => 'q',
	      'i' => '0', 'r' => 'x'
	    )
	);
	return $tr;
}
function key_decrypt($str){
	$tr = strtr( 
	    $str, 
	    array (
	      'a' => '1', 'c' => '2', 'e' => '3', 'y' => '4', 'g' => '5',
	      'b' => '6', 'v' => '7', 'w' => '8', 'z' => '9', 'h' => '0',
	      '8' => 'a', '9' => 'j', 'n' => 's',
	      '1' => 'b', '3' => 'k', 's' => 't',
	      '5' => 'c', '2' => 'l', 'm' => 'u',
	      'd' => 'd', 'l' => 'm', 'u' => 'v',
	      '4' => 'e', 'j' => 'n', 'r' => 'w',
	      'f' => 'f', 'k' => 'o', 'o' => 'x',
	      '6' => 'g', 'p' => 'p', 't' => 'y',
	      '7' => 'h', 'i' => 'q', 'q' => 'z',
	      '0' => 'i', 'x' => 'r'
	    )
	);
	return $tr;
}

function textoMaiusculo ($str) {
	$str = mb_strtoupper($str);
	$caracteresEspeciais = array(
	    'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'Ä'=>'A', 
	    'Å'=>'A', 'Æ'=>'A',  
	    'Ï'=>'I', 'Ñ'=>'N', 'Ø'=>'O', 
	    'Þ'=>'B', 'ß'=>'Ss','à'=>'À', 'á'=>'Á', 'â'=>'Â', 'ã'=>'Ã', 'ä'=>'Ä', 
	    'å'=>'A', 'æ'=>'A', 'ç'=>'Ç', 'è'=>'È', 'é'=>'É', 'ê'=>'Ê', 'ë'=>'Ë', 'ì'=>'Ì', 'í'=>'Í', 'î'=>'Î', 
	    'ï'=>'Ï', 'ð'=>'O', 'ñ'=>'Ñ', 'ò'=>'Ò', 'ó'=>'Ó', 'ô'=>'Ô', 'õ'=>'O', 'ö'=>'Ö', 'ø'=>'O', 'ù'=>'Ù', 
	    'ú'=>'Ú', 'û'=>'Û', 'ý'=>'Ý', 'ý'=>'Ý', 'þ'=>'B', 'ÿ'=>'Y', 'ƒ'=>'F','ü'=>'Ü'
	);
	return strtr($str, $caracteresEspeciais);
}

function removeCaracteresEspeciais($str) {
	$caracteresEspeciais = array(	     
	    'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A',
	    'Ä'=>'A', 'Å'=>'A','Æ'=>'A',
	    'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 
	    'ä'=>'a', 'å'=>'a', 'æ'=>'a',
	    'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E',
	    'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e',
	    'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I',
	    'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i',
	    'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 
	    'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 	    
	    'ø'=>'o', 'Ø'=>'o', 'ð'=>'o',
	    'Ù'=>'U', 'Ú'=>'U', 'Ü'=>'U', 'Û'=>'U', 
	    'ù'=>'u', 'ú'=>'u', 'ü'=>'u', 'û'=>'u',
	    'Ç'=>'C', 'ý'=>'y', 'ý'=>'y', 'ÿ'=>'y', 
	    'ƒ'=>'f', 'þ'=>'b', 'ñ'=>'n', 
	    'ç'=>'c', 'Š'=>'s', 'š'=>'s', 
	    'Ð'=>'d', 'Ž'=>'z', 'ž'=>'z', 
	    'Ï'=>'i', 'Ñ'=>'N', 'Þ'=>'b', 'ß'=>'s',
		'!'=>'','¹'=>'','@'=>'','²'=>'',
		'#'=>'','³'=>'','$'=>'','£'=>'',
		'%'=>'','¢'=>'','¨'=>'','¬'=>'',
		'§'=>'','&'=>'','*'=>'','['=>'',
		']'=>'','('=>'','{'=>'','~'=>'',
		'^'=>'','´'=>'','`'=>'','}'=>'',
		')'=>'','-'=>'','_'=>'','='=>'',
		'+'=>'','ª'=>'',
		'º'=>'','°'=>'',':'=>'',';'=>'',
		'<'=>'',','=>'','.'=>'','>'=>'',
		'?'=>'','\\'=>'','|'=>'','/'=>'' 
	);
	return strtr($str, $caracteresEspeciais);
}

function soLetrasNumeros($v){
	return htmlentities(trim(preg_replace("/[^0-9A-Za-z]/", "", $v)));
}

function exibeTextoComVirgulaOuE($arrayDados,$nomeCampo){
	$textoFinal="";
	if(sizeof($arrayDados)>0){
		for($i=0;$i<sizeof($arrayDados);$i++){
			$textoFinal.=$arrayDados[$i][$nomeCampo].' ';
			if(sizeof($arrayDados)==2 && $i<1){
				$textoFinal.='e ';
			}
			if(sizeof($arrayDados)>2){
				$tam=sizeof($arrayDados)-2;
				if($i == sizeof($arrayDados)-2){
					$textoFinal.='e ';
				}else{
					$textoFinal=substr($textoFinal,0,-1);
					$textoFinal.=', ';
				}
				
			}
		}
		if(sizeof($arrayDados)>2){
			$textoFinal=substr($textoFinal,0,-2);
		}else{
			$textoFinal=substr($textoFinal,0,-1);
		}	
	}	
	return $textoFinal;
}

function quebraLinhaDadosNumerados($texto){

	$numeros = array();
	$substituicao = array();

	for($i=2;$i<100;$i++){
		$numeros[]='; '.$i.'.';
		$substituicao[]=';<br><br>---'.$i.'.';
	}
	for($i=2;$i<sizeof($numeros);$i++){
		$encontra=$numeros[$i];
		$substitui=$substituicao[$i];
		$texto=str_replace($encontra,$substitui,$texto);
	}

	$texto=str_replace(';',';<br>',$texto);
	return $texto;
}

//saudação de Bom dia, boa tarde e boa noite.
function saudacao( $nome = '' ) {
   $hora = date('H');
   if( $hora >= 6 && $hora <= 12 )
      return 'Bom dia' . (empty($nome) ? '' : ' ' . $nome);
   else if ( $hora > 12 && $hora <=18  )
      return 'Boa tarde' . (empty($nome) ? '' : ' ' . $nome);
   else
      return 'Boa noite' . (empty($nome) ? '' : ' ' . $nome);
}

function exibeDetalhesLog($texto){

	if($texto!=NULL){
		$encontre = 	array(APP_LINE_BREAK,'\"',"\'");
		$troquepor = 	array("<br>",'"','"');

		$retorno = str_replace($encontre, $troquepor, $texto);
	}else{
		$retorno = exibeTexto('');
	}

	return $retorno;

}

function fundoColorido($valor){
	$fundo_colorido='bg-success';
	if($valor<100){
		if($valor<51){
			$fundo_colorido='bg-danger';
		}else{
			$fundo_colorido='bg-warning';
		}
	}
	return $fundo_colorido;
}

function exibePorcentagem($valor){
	return number_format($valor, 2, ',', '');
}

function exibeCpf($valor){
	if($valor!=NULL && strlen($valor)==11){
		$aux_1=substr($valor,0,3);
		$aux_2=substr($valor,3,3);
		$aux_3=substr($valor,6,3);
		$aux_4=substr($valor,9,2);
		if(!empty($aux_1) && !empty($aux_2) && !empty($aux_3) && !empty($aux_4)){
			return $aux_1.'.'.$aux_2.'.'.$aux_3.'-'.$aux_4;	
		}else{
			return exibeTexto('0');
		}
		
	}else{
		return exibeTexto('');
	}	
}

function exibeCnpj($valor){
	if($valor!=NULL && strlen($valor)==14 ){
		$aux_1=substr($valor,0,2);
		$aux_2=substr($valor,2,3);
		$aux_3=substr($valor,5,3);
		$aux_4=substr($valor,8,4);
		$aux_5=substr($valor,12,2);
		if(!empty($aux_1) && !empty($aux_2) && !empty($aux_3) && !empty($aux_4) && !empty($aux_5)){
			return $aux_1.'.'.$aux_2.'.'.$aux_3.'/'.$aux_4.'-'.$aux_5;	
		}else{
			return exibeTexto('');
		}		
	}else{
		return exibeTexto('');
	}	
}

function exibeTelefone($telefone){
	$telefoneFormatado = "";
	if(strlen($telefone)==10 || strlen($telefone)==11){
		$ddd=substr($telefone,0,2);
		$numero=substr($telefone,2);
		if(strlen($numero)<=8){
			$aux1=substr($numero,0,4);
			$aux2=substr($numero,4);
			$numero=$aux1.'-'.$aux2;
		}elseif(strlen($numero)>=9){
			$aux1=substr($numero,0,5);
			$aux2=substr($numero,5);
			$numero=$aux1.'-'.$aux2;
		}
		$telefoneFormatado='('.$ddd.') '.$numero;
	}else{
		if(sizeof($telefone)>0){
			return $telefone;
		}else{
			return "";
		}
		
	}
	return $telefoneFormatado;
}


function isAdmin(){
	if(isset($_SESSION["USUARIO"]["idperfil"]) && $_SESSION["USUARIO"]["idperfil"]==PERFIL_IDADMIN){
		return true;
	}else{
		return false;
	}
}

function isPresidente(){
	if(isset($_SESSION["USUARIO"]["idperfil"]) && $_SESSION["USUARIO"]["idperfil"]==PERFIL_IDPRESIDENTE){
		return true;
	}else{
		return false;
	}
}

function isComissaoEtica(){
	if(isset($_SESSION["USUARIO"]["idperfil"]) && $_SESSION["USUARIO"]["idperfil"]==PERFIL_IDCOMISSAOETICA){
		return true;
	}else{
		return false;
	}
}

function isMembroComissaoEtica(){
	if(isset($_SESSION["USUARIO"]["idperfil"]) && $_SESSION["USUARIO"]["idperfil"]==PERFIL_IDRESPONSAVEL){
		return true;
	}else{
		return false;
	}
}

function isInstituicao(){
	if(isset($_SESSION["USUARIO"]["idperfil"]) && $_SESSION["USUARIO"]["idperfil"]==PERFIL_IDINSTITUICAO){
		return true;
	}else{
		return false;
	}
}

//ordena um array de acordo com a chave de ordenação e ordem desejada (Ex.: $arrayOrdenado = arraySort($arrayDesordenado, 'nome');)
function arraySort($array, $on, $order=SORT_ASC){
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

//função para exibir coluna que está ordenando registros
function exibeFlagReordenacao($coluna){
	$retorno="";
	//quando os dados estiverem ordenados por tal coluna
	if(isset($_GET["order"]) && !empty($_GET["order"]) && isset($_GET["ascdesc"]) && !empty($_GET["ascdesc"]) && $_GET["order"] == $coluna){
		$coluna_get=$_GET["order"];
		$ordem_get=$_GET["ascdesc"];		
		if($ordem_get == "DESC"){
			$retorno='<span class="glyphicon glyphicon-sort-by-alphabet-alt btn-xs preto" title="Exibindo dados ordenados por esta coluna de forma DECRESCENTE"></span>';
		}else{
			$retorno='<span class="glyphicon glyphicon-sort-by-alphabet btn-xs preto" title="Exibindo dados ordenados por esta coluna de forma CRESCENTE"></span>';
		}
	}
	return $retorno;
}

//funções de exibição das Mensagens de Erro/Sucesso/Atenção
function exibeMsg(){
	echo "<script>";
	echo "showMsg".$_SESSION["msg_tipo"]."(";
    if(isset($_SESSION["msg_1"]) && !empty($_SESSION["msg_1"])){
      echo "'".$_SESSION["msg_1"]."',";
    }else{
      echo "'',";
    }
    if(isset($_SESSION["msg_2"]) && !empty($_SESSION["msg_2"])){
      echo "'".$_SESSION["msg_2"]."'";
    }else{
      echo "''";
    }
    echo ");"; 
    echo "</script>";  
    limpaMsg();
}
function enviaMsg($tipo,$msg_1="",$msg_2=""){
	if($tipo=="sucesso" || $tipo=="ok"){
		$tipo="Sucesso";
	}
	if($tipo=="erro"){
		$tipo="Erro";
		//salva no log de erros
		$fp = fopen(APP_FILE_ERROR_LOG.date("Ymd")."_".codifica(date("Ymd")).".txt", "a");
		$texto = date("d/m/Y H:i:s")." ".$_SERVER['REQUEST_URI']." ".getIpUsuario();
		if(isset($_SESSION["USUARIO"]["idusuario"]) && !empty($_SESSION["USUARIO"]["idusuario"])){
			$texto.=" (".$_SESSION["USUARIO"]["login"].")";
		}
		$texto .=" ".$msg_1." - ".$msg_2."\r\n";
		$texto = str_replace("<strong>", "", $texto);
		$texto = str_replace("</strong>", "", $texto);
		$texto = str_replace("<em>", "", $texto);
		$texto = str_replace("</em>", "", $texto);
		$escreve = fwrite($fp, $texto);
		fclose($fp);
	}

	$_SESSION["msg_tipo"] = $tipo;
	$_SESSION["msg_1"] = $msg_1;
	$_SESSION["msg_2"] = $msg_2;
}
function limpaMsg(){
	$_SESSION["msg_tipo"] = NULL;
	$_SESSION["msg_1"] = NULL;
	$_SESSION["msg_2"] = NULL;
}
function temMsg(){
	if(isset($_SESSION["msg_tipo"]) && !empty($_SESSION["msg_tipo"]) &&
		( isset($_SESSION["msg_1"]) || isset($_SESSION["msg_2"]) ) ){
		return true;
	}else{
		return false;
	}
}

//funções de CONTROLLER (como não há divisão entre VIEW/CONTROLLER), vem pra cá:
		
		//ETAPAS
			//função que retorna a próxima etapa baseada na que já foi inserida em etapa_processo
			//retorna uma NovaEtapa com id = -1 se não houver próxima etapa
			/*REGRAS:
				1 - se aprovada / não aplicável, deve ir pra próxima do fluxo principal
				2 - se não aprovada
					2.1 SE for do f.principal, deve ir pra próxima do fluxo alternativo
					2.2 SE for do f.alternativo, deve retornar para a anterior do fluxo principal
				3 - aguardando ação = retornar etapa com id -1 (inexistente)
				4 - modos da etapa atual
					se o modoprocesso = militar
						as proximas etapas sempre serão diferente de c/eleição e não militar
					se modoprocesso = normal
						& modoetapa = normal >> avança para próxima normal (ou não militar, se instituição não for militar = a primeira encontrada)
					se tiver modo (em processo) = c/ eleições
						X
					se tiver modo (em processo) = c/ eleições
						X

					se for 0 (normal), avança pra próxima normal
					se for 1 (C/eleições),
					se for 2 (S/eleições),
					se for 3 (não militar),
		
			OLD:
			function proximaEtapaProcesso($arrayEtapas,$dadosEtapaAnterior,$idprocesso){
				//variavel que depende da importacao da Model ETAPA
				$NovaEtapa = new Etapa();
				//varre array de etapas para encontrar a posição da etapa em questão
				for($i=0;$i<sizeof($arrayEtapas);$i++) {
					//quando encontrar a etapa atual
					if($arrayEtapas[$i]["idetapa"] == $dadosEtapaAnterior["idetapa"]){
						//verifica se ela foi aprovada (ou se é não aprovavel, passando pra próxima principal)
						if($dadosEtapaAnterior["fluxo"]==ETAPA_PRINCIPAL && ($dadosEtapaAnterior["aprovacao"] == ETAPA_APROVADA || $dadosEtapaAnterior["aprovacao"] == ETAPA_NAO_APROVAVEL)){
							//se ela foi aprovada, é preciso passar para a próxima do fluxo principal, exceto se já estivermos na última etapa
								//se não for a última etapa, então pega dados da proxima etapa PRINCIPAL
								if( $i < (sizeof($arrayEtapas)-1) ){
									//varre as próximas etapas do array para pegar a primeira que aparecer do fluxo principal
									for($j=($i+1);$j<sizeof($arrayEtapas);$j++){
										if($arrayEtapas[$j]["fluxo"] == ETAPA_PRINCIPAL){
											//atribuimos valor ao objeto etapa que dá informações da próxima etapa
											$NovaEtapa->setId($arrayEtapas[$j]["idetapa"]);
											$NovaEtapa->setProcesso($idprocesso);
											$NovaEtapa->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
											$NovaEtapa->setAprova($arrayEtapas[$j]["aprova"]);
											$NovaEtapa->setAprovaMsg(NULL);
											$NovaEtapa->setPrazo($arrayEtapas[$j]["prazo"]);
											//seta J com o tamanho máximo para sair do for nesse momento
											$j=sizeof($arrayEtapas);
										}
									}
								//se for a última etapa, então não há próxima etapa
								}else{
									$NovaEtapa->setId(-1);
								}
						//se não foi aprovada ou é do fluxo alternativo
						}elseif($dadosEtapaAnterior["aprovacao"] == ETAPA_NAO_APROVADA || $dadosEtapaAnterior["fluxo"] == ETAPA_ALTERNATIVA){
							//se a etapa for do fluxo principal e não tiver sido aprovada, a próxima é a alternativa
							if($dadosEtapaAnterior["fluxo"]==ETAPA_PRINCIPAL){
								//novaetapa é a próxima etapa com fluxo alternativo
								for($i=0;$i<sizeof($arrayEtapas);$i++) {
									//se a ordem da etapa for maior que a da anterior, e esta for de fluxo alternativo, é esta que será a próxima etapa
									if($arrayEtapas[$i]["ordem"] > $dadosEtapaAnterior["ordem"] && $arrayEtapas[$i]["fluxo"] == ETAPA_ALTERNATIVA){
										//atribuimos valor ao objeto etapa que dá informações da próxima etapa
										$NovaEtapa->setId($arrayEtapas[$i]["idetapa"]);
										$NovaEtapa->setProcesso($idprocesso);
										$NovaEtapa->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
										$NovaEtapa->setAprova($arrayEtapas[$i]["aprova"]);
										$NovaEtapa->setAprovaMsg(NULL);
										$NovaEtapa->setPrazo($arrayEtapas[$i]["prazo"]);
										//setamos o I com o valor máximo para sair do for
										$i=sizeof($arrayEtapas);
									}
								}
							//se a etapa for do fluxo alternativo e não tiver sido aprovada, a próxima é a na verdade a etapa anterior do fluxo principal
							}else{
								//novaetapa é a etapa anterior com fluxo principal
								for($i=0;$i<sizeof($arrayEtapas);$i++) {
									//se a ordem da etapa for menor que a da anterior, e esta for de fluxo principal ...
									//vamos sobreescrevendo essa informação até pegar a última etapa do fluxo principal que antecede a etapa em questão
									if($arrayEtapas[$i]["ordem"] < $dadosEtapaAnterior["ordem"] && $arrayEtapas[$i]["fluxo"] == ETAPA_PRINCIPAL){
										//atribuimos valor ao objeto etapa que dá informações da próxima etapa
										$NovaEtapa->setId($arrayEtapas[$i]["idetapa"]);
										$NovaEtapa->setProcesso($idprocesso);
										$NovaEtapa->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
										$NovaEtapa->setAprova($arrayEtapas[$i]["aprova"]);
										$NovaEtapa->setAprovaMsg(NULL);
										$NovaEtapa->setPrazo($arrayEtapas[$i]["prazo"]);
									}
								}
							}
						//caso não caia em nenhum caso acima, não pode trocar de etapa pois está aguardando alguma ação
						}else{
							$NovaEtapa->setId(-1);
						}
					//se não houver dados da etapa anterior, a etapa é a primeira, então define estes dados
					}elseif(!$dadosEtapaAnterior || empty($dadosEtapaAnterior)){
						$NovaEtapa->setId($arrayEtapas[0]["idetapa"]);
						$NovaEtapa->setProcesso($idprocesso);
						$NovaEtapa->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
						$NovaEtapa->setAprova($arrayEtapas[0]["aprova"]);
						$NovaEtapa->setAprovaMsg(NULL);
						$NovaEtapa->setPrazo($arrayEtapas[0]["prazo"]);
					}
				}
				return $NovaEtapa;
			}


			*/
			function proximaEtapaProcesso($arrayEtapas,$dadosEtapaAnterior,$idprocesso){
				//variavel que depende da importacao da Model ETAPA
				$NovaEtapa = new Etapa();
				//varre array de etapas para encontrar a posição da etapa em questão
				for($i=0;$i<sizeof($arrayEtapas);$i++) {
					//quando encontrar a etapa atual
					if($arrayEtapas[$i]["idetapa"] == $dadosEtapaAnterior["idetapa"]){
						//verifica se ela foi aprovada (ou se é não aprovavel, passando pra próxima principal)
						if($dadosEtapaAnterior["fluxo"]==ETAPA_PRINCIPAL && ($dadosEtapaAnterior["aprovacao"] == ETAPA_APROVADA || $dadosEtapaAnterior["aprovacao"] == ETAPA_NAO_APROVAVEL)){
							//se ela foi aprovada, é preciso passar para a próxima do fluxo principal, exceto se já estivermos na última etapa
								//se não for a última etapa, então pega dados da proxima etapa PRINCIPAL
								if( $i < (sizeof($arrayEtapas)-1) ){
									//varre as próximas etapas do array para pegar a primeira que aparecer do fluxo principal
									for($j=($i+1);$j<sizeof($arrayEtapas);$j++){
										if($arrayEtapas[$j]["fluxo"] == ETAPA_PRINCIPAL){
											//atribuimos valor ao objeto etapa que dá informações da próxima etapa
											$NovaEtapa->setId($arrayEtapas[$j]["idetapa"]);
											$NovaEtapa->setProcesso($idprocesso);
											$NovaEtapa->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
											$NovaEtapa->setAprova($arrayEtapas[$j]["aprova"]);
											$NovaEtapa->setAprovaMsg(NULL);
											$NovaEtapa->setPrazo($arrayEtapas[$j]["prazo"]);
											//seta J com o tamanho máximo para sair do for nesse momento
											$j=sizeof($arrayEtapas);
										}
									}
								//se for a última etapa, então não há próxima etapa
								}else{
									$NovaEtapa->setId(-1);
								}
						//se não foi aprovada ou é do fluxo alternativo
						}elseif($dadosEtapaAnterior["aprovacao"] == ETAPA_NAO_APROVADA || $dadosEtapaAnterior["fluxo"] == ETAPA_ALTERNATIVA){
							//se a etapa for do fluxo principal e não tiver sido aprovada, a próxima é a alternativa
							if($dadosEtapaAnterior["fluxo"]==ETAPA_PRINCIPAL){
								//novaetapa é a próxima etapa com fluxo alternativo
								for($i=0;$i<sizeof($arrayEtapas);$i++) {
									//se a ordem da etapa for maior que a da anterior, e esta for de fluxo alternativo, é esta que será a próxima etapa
									if($arrayEtapas[$i]["ordem"] > $dadosEtapaAnterior["ordem"] && $arrayEtapas[$i]["fluxo"] == ETAPA_ALTERNATIVA){
										//atribuimos valor ao objeto etapa que dá informações da próxima etapa
										$NovaEtapa->setId($arrayEtapas[$i]["idetapa"]);
										$NovaEtapa->setProcesso($idprocesso);
										$NovaEtapa->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
										$NovaEtapa->setAprova($arrayEtapas[$i]["aprova"]);
										$NovaEtapa->setAprovaMsg(NULL);
										$NovaEtapa->setPrazo($arrayEtapas[$i]["prazo"]);
										//setamos o I com o valor máximo para sair do for
										$i=sizeof($arrayEtapas);
									}
								}
							//se a etapa for do fluxo alternativo e não tiver sido aprovada, a próxima é a na verdade a etapa anterior do fluxo principal
							}else{
								//novaetapa é a etapa anterior com fluxo principal
								for($i=0;$i<sizeof($arrayEtapas);$i++) {
									//se a ordem da etapa for menor que a da anterior, e esta for de fluxo principal ...
									//vamos sobreescrevendo essa informação até pegar a última etapa do fluxo principal que antecede a etapa em questão
									if($arrayEtapas[$i]["ordem"] < $dadosEtapaAnterior["ordem"] && $arrayEtapas[$i]["fluxo"] == ETAPA_PRINCIPAL){
										//atribuimos valor ao objeto etapa que dá informações da próxima etapa
										$NovaEtapa->setId($arrayEtapas[$i]["idetapa"]);
										$NovaEtapa->setProcesso($idprocesso);
										$NovaEtapa->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
										$NovaEtapa->setAprova($arrayEtapas[$i]["aprova"]);
										$NovaEtapa->setAprovaMsg(NULL);
										$NovaEtapa->setPrazo($arrayEtapas[$i]["prazo"]);
									}
								}
							}
						//caso não caia em nenhum caso acima, não pode trocar de etapa pois está aguardando alguma ação
						}else{
							$NovaEtapa->setId(-1);
						}
					//se não houver dados da etapa anterior, a etapa é a primeira, então define estes dados
					}elseif(!$dadosEtapaAnterior || empty($dadosEtapaAnterior)){
						$NovaEtapa->setId($arrayEtapas[0]["idetapa"]);
						$NovaEtapa->setProcesso($idprocesso);
						$NovaEtapa->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
						$NovaEtapa->setAprova($arrayEtapas[0]["aprova"]);
						$NovaEtapa->setAprovaMsg(NULL);
						$NovaEtapa->setPrazo($arrayEtapas[0]["prazo"]);
					}
				}
				return $NovaEtapa;
			}

			function proximaEtapaProcesso2($arrayEtapas,$dadosEtapaAtual,$Processo){
				//variavel que depende da importacao da Model ETAPA
				$ProximaEtapa = new Etapa();
				//varre array de etapas para saber qual vem depois dela
				for ($i=0; $i < sizeof($arrayEtapas); $i++) { 
					//echo "<br><br>i=".$i;
					//quando encontrar a etapa atual, efetua verificações
					if($arrayEtapas[$i]["idetapa"] == $dadosEtapaAtual["idetapa"]){
							//echo "<br>achou etapa atual";
							//echo "<br><strong>CASO 1</strong>: ".$dadosEtapaAtual["fluxo"]."==".ETAPA_PRINCIPAL."&&"."(".$dadosEtapaAtual["aprovacao"] ."==". ETAPA_APROVADA ."||". $dadosEtapaAtual["aprovacao"] ."==". ETAPA_NAO_APROVAVEL.")";
						  //CASO 1 = se for do FLUXO PRINCIPAL e foi aprovada (ou não é aprovável)
						  if($dadosEtapaAtual["fluxo"]==ETAPA_PRINCIPAL && ($dadosEtapaAtual["aprovacao"] == ETAPA_APROVADA || $dadosEtapaAtual["aprovacao"] == ETAPA_NAO_APROVAVEL)){
						  	//echo "<br>entrou CASO1!";
						    //se não for a última etapa
						    if( $i < (sizeof($arrayEtapas)-1) ){
						      //varre as próximas etapas do array para pegar a primeira que aparecer do fluxo principal que respeite as regras citadas abaixo
						      for($j=($i+1);$j<sizeof($arrayEtapas);$j++){
						      	//echo "<br>Verificando condições: if(".$arrayEtapas[$j]["fluxo"]." == ".ETAPA_PRINCIPAL ."&& ((".$Processo->getMilitar()." == ".PROCESSO_MILITAR."&&".$arrayEtapas[$j]["modo"]." != ".ETAPA_NAOMILITAR ."&&". $arrayEtapas[$j]["modo"]." != ".PROCESSOETAPA_COMELEICOES.") || (".$Processo->getModo()." == ".PROCESSOETAPA_COMELEICOES ."&&". $arrayEtapas[$j]["modo"]." != ".PROCESSOETAPA_SEMELEICOES.") || (".$Processo->getModo()." == ".PROCESSOETAPA_SEMELEICOES ."&&". $arrayEtapas[$j]["modo"]." != ".PROCESSOETAPA_COMELEICOES ."&& (".$Processo->getMilitar()." != ".PROCESSO_MILITAR ." || (".$Processo->getMilitar()." == ".PROCESSO_MILITAR ."&&". $arrayEtapas[$j]["modo"]." != ".ETAPA_NAOMILITAR.") ))))";

						        //PROCESSO NÃO MILITAR, NORMAL

						        if($arrayEtapas[$j]["fluxo"] == ETAPA_PRINCIPAL 
						        &&  (  
						        //se processo = militar, próxima etapa NÃO PODE ser c/eleições nem não militar                    
						          ($Processo->getMilitar()==PROCESSO_MILITAR && $arrayEtapas[$j]["modo"]!=ETAPA_NAOMILITAR && $arrayEtapas[$j]["modo"]!=PROCESSOETAPA_COMELEICOES)
						        ||
						        //se processo = modo COM eleições = próxima etapa não pode ser S/ eleições
						          ($Processo->getModo()==PROCESSOETAPA_COMELEICOES && $arrayEtapas[$j]["modo"]!=PROCESSOETAPA_SEMELEICOES)
						        ||
						        //se processo = modo SEM eleições = próxima etapa não pode ser C/ eleições e se processo militar = a etapa tbm não pode ser "n militar"
						          ($Processo->getModo()==PROCESSOETAPA_SEMELEICOES 
						            && $arrayEtapas[$j]["modo"]!=PROCESSOETAPA_COMELEICOES 
						            && ($Processo->getMilitar()!=PROCESSO_MILITAR || ($Processo->getMilitar()==PROCESSO_MILITAR && $arrayEtapas[$j]["modo"]!=ETAPA_NAOMILITAR))
						          )
						        ||
						        //se Modoprocesso = NORMAL e processo NÃO MILITAR, a próxima etapa tem que ser normal ou não militar
						          ($Processo->getModo()==PROCESSOETAPA_NORMAL 
						            && $Processo->getMilitar()!=PROCESSO_MILITAR && 
						            ($arrayEtapas[$j]["modo"]==ETAPA_NAOMILITAR || $arrayEtapas[$j]["modo"]==PROCESSOETAPA_NORMAL)
						          )
						        )){

						        	//echo "<br>achou uma etapa dentro do CASO1!";

						          //atribuimos valor ao objeto etapa que dá informações da próxima etapa
						          $ProximaEtapa->setId($arrayEtapas[$j]["idetapa"]);
						          $ProximaEtapa->setProcesso($Processo->getId());
						          $ProximaEtapa->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
						          $ProximaEtapa->setAprova($arrayEtapas[$j]["aprova"]);
						          $ProximaEtapa->setAprovaMsg(NULL);
						          $ProximaEtapa->setPrazo($arrayEtapas[$j]["prazo"]);
						          //seta J com o tamanho máximo para sair do for nesse momento
						          $j=sizeof($arrayEtapas);
						        //FIM IF $arrayEtapas[$j]["fluxo"] == ETAPA_PRINCIPAL
						        }
						      //FIM for($j=($i+1);$j<sizeof($arrayEtapas);$j++){
						      }
						    //FIM if( $i < (sizeof($arrayEtapas)-1) ){
						    //se for a última etapa, então não há próxima etapa
						    }else{
						    	//echo "<br><strong>é a ultima etapa!</strong>";
						      $ProximaEtapa->setId(-1);
						    }

						  //FIM if CASO 1
						  //CASO2 = etapa do fluxo principal, que NAO FOI APROVADA, devendo ir pra próxima do fluxo alternativo
						  }elseif($dadosEtapaAtual["fluxo"]==ETAPA_PRINCIPAL && $dadosEtapaAtual["aprovacao"] == ETAPA_NAO_APROVADA){

						  	//echo "<br>CASO2 : etapa do fluxo principal, que NAO FOI APROVADA, devendo ir pra próxima do fluxo alternativo<br>";

						    //se a etapa for do fluxo principal e não tiver sido aprovada, a próxima é a alternativa
							if($dadosEtapaAtual["fluxo"]==ETAPA_PRINCIPAL){
								//novaetapa é a próxima etapa com fluxo alternativo
								for($i=0;$i<sizeof($arrayEtapas);$i++) {
									//se a ordem da etapa for maior que a da anterior, e esta for de fluxo alternativo, é esta que será a próxima etapa
									if($arrayEtapas[$i]["ordem"] > $dadosEtapaAtual["ordem"] && $arrayEtapas[$i]["fluxo"] == ETAPA_ALTERNATIVA){
										//atribuimos valor ao objeto etapa que dá informações da próxima etapa
										$ProximaEtapa->setId($arrayEtapas[$i]["idetapa"]);
										$ProximaEtapa->setProcesso($Processo->getId());
										$ProximaEtapa->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
										$ProximaEtapa->setAprova($arrayEtapas[$i]["aprova"]);
										$ProximaEtapa->setAprovaMsg(NULL);
										$ProximaEtapa->setPrazo($arrayEtapas[$i]["prazo"]);
										//setamos o I com o valor máximo para sair do for
										$i=sizeof($arrayEtapas);
									} //FIM if($arrayEtapas[$i]["ordem"]
								} //FIM FOR
							}//fim if($dadosEtapaAtual["fluxo"]==ETAPA_PRINCIPAL){

							/*
								INSTRUÇÃO REMOVIDA POR ACREDITAR QUE É UMA CONDIÇÃO IMPOSSÍVEL
								(SE ESTIVER CERTO, REMOVER O IF ACIMA!!! "if($dadosEtapaAtual["fluxo"]==ETAPA_PRINCIPAL){")

								//FIM if($dadosEtapaAnterior["fluxo"]==ETAPA_PRINCIPAL){
							//se a etapa for do fluxo alternativo e não tiver sido aprovada, a próxima é a na verdade a etapa anterior do fluxo principal
							}else{
								echo "<br><h2>CASO 3:</h2> não eh do fluxo principal!!<br>";
								//novaetapa é a etapa anterior com fluxo principal
								for($i=0;$i<sizeof($arrayEtapas);$i++) {
									echo "<br>ARRAYETAPAS ($i)!!<br>";
									//se a ordem da etapa for menor que a da anterior, e esta for de fluxo principal ...
									//vamos sobreescrevendo essa informação até pegar a última etapa do fluxo principal que antecede a etapa em questão
									if($arrayEtapas[$i]["ordem"] < $dadosEtapaAtual["ordem"] && $arrayEtapas[$i]["fluxo"] == ETAPA_PRINCIPAL){
										echo "<br>atribuiu novo valor ao ProximaEtapa ($i)!!<br>";
										//atribuimos valor ao objeto etapa que dá informações da próxima etapa
										$ProximaEtapa->setId($arrayEtapas[$i]["idetapa"]);
										$ProximaEtapa->setProcesso($Processo->getId());
										$ProximaEtapa->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
										$ProximaEtapa->setAprova($arrayEtapas[$i]["aprova"]);
										$ProximaEtapa->setAprovaMsg(NULL);
										$ProximaEtapa->setPrazo($arrayEtapas[$i]["prazo"]);
									} //FIM if($arrayEtapas[$i]["ordem"]) 
								} //FIM for
							}//fim ELSE
							*/

						//FIM CASO 2
					  	//CASO 3: se for etapa alternativa, sempre vai voltar para a etapa principal anterior à ela
						}elseif($dadosEtapaAtual["fluxo"]==ETAPA_ALTERNATIVA){
							
							//novaetapa é a etapa anterior com fluxo principal
							for($i=0;$i<sizeof($arrayEtapas);$i++) {
								//se a ordem da etapa for menor que a da anterior, e esta for de fluxo principal ...
								//vamos sobreescrevendo essa informação até pegar a última etapa do fluxo principal que antecede a etapa em questão
								if($arrayEtapas[$i]["ordem"] < $dadosEtapaAtual["ordem"] && $arrayEtapas[$i]["fluxo"] == ETAPA_PRINCIPAL){
									//atribuimos valor ao objeto etapa que dá informações da próxima etapa
									$ProximaEtapa->setId($arrayEtapas[$i]["idetapa"]);
									$ProximaEtapa->setProcesso($Processo->getId());
									$ProximaEtapa->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
									$ProximaEtapa->setAprova($arrayEtapas[$i]["aprova"]);
									$ProximaEtapa->setAprovaMsg(NULL);
									$ProximaEtapa->setPrazo($arrayEtapas[$i]["prazo"]);
								}
							}

						//FIM CASO 3
					  	//caso não caia em nenhum caso acima, não pode trocar de etapa pois está aguardando alguma ação
						}else{
							$ProximaEtapa->setId(-1);
						}//fim else
					}//fim if($arrayEtapas[$i]["idetapa"]
				}//fim for
				return $ProximaEtapa;
			}//fim função


			/*OLD


			FUNÇÃO ABAIXO ESTÁ COM ERRO:
			quando processo NORMAL, etapa alternativa (3.1 (id=7)), ao enviar arquivo não acha a próxima etapa (add_doc)

			function proximaEtapaProcesso2($arrayEtapas,$dadosEtapaAtual,$Processo){
				//variavel que depende da importacao da Model ETAPA
				$ProximaEtapa = new Etapa();
				//varre array de etapas para saber qual vem depois dela
				for ($i=0; $i < sizeof($arrayEtapas); $i++) { 
					//echo "<br><br>i=".$i;
					//quando encontrar a etapa atual, efetua verificações
					if($arrayEtapas[$i]["idetapa"] == $dadosEtapaAtual["idetapa"]){
							//echo "<br>achou etapa atual";
							//echo "<br><strong>CASO 1</strong>: ".$dadosEtapaAtual["fluxo"]."==".ETAPA_PRINCIPAL."&&"."(".$dadosEtapaAtual["aprovacao"] ."==". ETAPA_APROVADA ."||". $dadosEtapaAtual["aprovacao"] ."==". ETAPA_NAO_APROVAVEL.")";
						  //CASO 1 = se for do FLUXO PRINCIPAL e foi aprovada (ou não é aprovável)
						  if($dadosEtapaAtual["fluxo"]==ETAPA_PRINCIPAL && ($dadosEtapaAtual["aprovacao"] == ETAPA_APROVADA || $dadosEtapaAtual["aprovacao"] == ETAPA_NAO_APROVAVEL)){
						  	//echo "<br>entrou CASO1!";
						    //se não for a última etapa
						    if( $i < (sizeof($arrayEtapas)-1) ){
						      //varre as próximas etapas do array para pegar a primeira que aparecer do fluxo principal que respeite as regras citadas abaixo
						      for($j=($i+1);$j<sizeof($arrayEtapas);$j++){
						      	//echo "<br>Verificando condições: if(".$arrayEtapas[$j]["fluxo"]." == ".ETAPA_PRINCIPAL ."&& ((".$Processo->getMilitar()." == ".PROCESSO_MILITAR."&&".$arrayEtapas[$j]["modo"]." != ".ETAPA_NAOMILITAR ."&&". $arrayEtapas[$j]["modo"]." != ".PROCESSOETAPA_COMELEICOES.") || (".$Processo->getModo()." == ".PROCESSOETAPA_COMELEICOES ."&&". $arrayEtapas[$j]["modo"]." != ".PROCESSOETAPA_SEMELEICOES.") || (".$Processo->getModo()." == ".PROCESSOETAPA_SEMELEICOES ."&&". $arrayEtapas[$j]["modo"]." != ".PROCESSOETAPA_COMELEICOES ."&& (".$Processo->getMilitar()." != ".PROCESSO_MILITAR ." || (".$Processo->getMilitar()." == ".PROCESSO_MILITAR ."&&". $arrayEtapas[$j]["modo"]." != ".ETAPA_NAOMILITAR.") ))))";

						        //PROCESSO NÃO MILITAR, NORMAL

						        if($arrayEtapas[$j]["fluxo"] == ETAPA_PRINCIPAL 
						        &&  (  
						        //se processo = militar, próxima etapa NÃO PODE ser c/eleições nem não militar                    
						          ($Processo->getMilitar()==PROCESSO_MILITAR && $arrayEtapas[$j]["modo"]!=ETAPA_NAOMILITAR && $arrayEtapas[$j]["modo"]!=PROCESSOETAPA_COMELEICOES)
						        ||
						        //se processo = modo COM eleições = próxima etapa não pode ser S/ eleições
						          ($Processo->getModo()==PROCESSOETAPA_COMELEICOES && $arrayEtapas[$j]["modo"]!=PROCESSOETAPA_SEMELEICOES)
						        ||
						        //se processo = modo SEM eleições = próxima etapa não pode ser C/ eleições e se processo militar = a etapa tbm não pode ser "n militar"
						          ($Processo->getModo()==PROCESSOETAPA_SEMELEICOES 
						            && $arrayEtapas[$j]["modo"]!=PROCESSOETAPA_COMELEICOES 
						            && ($Processo->getMilitar()!=PROCESSO_MILITAR || ($Processo->getMilitar()==PROCESSO_MILITAR && $arrayEtapas[$j]["modo"]!=ETAPA_NAOMILITAR))
						          )
						        ||
						        //se Modoprocesso = NORMAL e processo NÃO MILITAR, a próxima etapa tem que ser normal ou não militar
						          ($Processo->getModo()==PROCESSOETAPA_NORMAL 
						            && $Processo->getMilitar()!=PROCESSO_MILITAR && 
						            ($arrayEtapas[$j]["modo"]==ETAPA_NAOMILITAR || $arrayEtapas[$j]["modo"]==PROCESSOETAPA_NORMAL)
						          )
						        )){

						        	//echo "<br>achou uma etapa dentro do CASO1!";

						          //atribuimos valor ao objeto etapa que dá informações da próxima etapa
						          $ProximaEtapa->setId($arrayEtapas[$j]["idetapa"]);
						          $ProximaEtapa->setProcesso($Processo->getId());
						          $ProximaEtapa->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
						          $ProximaEtapa->setAprova($arrayEtapas[$j]["aprova"]);
						          $ProximaEtapa->setAprovaMsg(NULL);
						          $ProximaEtapa->setPrazo($arrayEtapas[$j]["prazo"]);
						          //seta J com o tamanho máximo para sair do for nesse momento
						          $j=sizeof($arrayEtapas);
						        //FIM IF $arrayEtapas[$j]["fluxo"] == ETAPA_PRINCIPAL
						        }
						      //FIM for($j=($i+1);$j<sizeof($arrayEtapas);$j++){
						      }
						    //FIM if( $i < (sizeof($arrayEtapas)-1) ){
						    //se for a última etapa, então não há próxima etapa
						    }else{
						    	//echo "<br><strong>é a ultima etapa!</strong>";
						      $ProximaEtapa->setId(-1);
						    }

						  //FIM if CASO 1
						  //CASO2 = etapa do fluxo principal, que NAO FOI APROVADA, devendo ir pra próxima do fluxo alternativo
						  }elseif($dadosEtapaAtual["fluxo"]==ETAPA_PRINCIPAL && $dadosEtapaAtual["aprovacao"] == ETAPA_NAO_APROVADA){

						  	//echo "<br>CASO2 : etapa do fluxo principal, que NAO FOI APROVADA, devendo ir pra próxima do fluxo alternativo<br>";

						    //se a etapa for do fluxo principal e não tiver sido aprovada, a próxima é a alternativa
							if($dadosEtapaAtual["fluxo"]==ETAPA_PRINCIPAL){
								//novaetapa é a próxima etapa com fluxo alternativo
								for($i=0;$i<sizeof($arrayEtapas);$i++) {
									//se a ordem da etapa for maior que a da anterior, e esta for de fluxo alternativo, é esta que será a próxima etapa
									if($arrayEtapas[$i]["ordem"] > $dadosEtapaAtual["ordem"] && $arrayEtapas[$i]["fluxo"] == ETAPA_ALTERNATIVA){
										//atribuimos valor ao objeto etapa que dá informações da próxima etapa
										$ProximaEtapa->setId($arrayEtapas[$i]["idetapa"]);
										$ProximaEtapa->setProcesso($Processo->getId());
										$ProximaEtapa->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
										$ProximaEtapa->setAprova($arrayEtapas[$i]["aprova"]);
										$ProximaEtapa->setAprovaMsg(NULL);
										$ProximaEtapa->setPrazo($arrayEtapas[$i]["prazo"]);
										//setamos o I com o valor máximo para sair do for
										$i=sizeof($arrayEtapas);
									} //FIM if($arrayEtapas[$i]["ordem"]
								} //FIM FOR

							//FIM if($dadosEtapaAnterior["fluxo"]==ETAPA_PRINCIPAL){
							//se a etapa for do fluxo alternativo e não tiver sido aprovada, a próxima é a na verdade a etapa anterior do fluxo principal
							}else{
								//novaetapa é a etapa anterior com fluxo principal
								for($i=0;$i<sizeof($arrayEtapas);$i++) {
									//se a ordem da etapa for menor que a da anterior, e esta for de fluxo principal ...
									//vamos sobreescrevendo essa informação até pegar a última etapa do fluxo principal que antecede a etapa em questão
									if($arrayEtapas[$i]["ordem"] < $dadosEtapaAtual["ordem"] && $arrayEtapas[$i]["fluxo"] == ETAPA_PRINCIPAL){
										//atribuimos valor ao objeto etapa que dá informações da próxima etapa
										$ProximaEtapa->setId($arrayEtapas[$i]["idetapa"]);
										$ProximaEtapa->setProcesso($Processo->getId());
										$ProximaEtapa->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
										$ProximaEtapa->setAprova($arrayEtapas[$i]["aprova"]);
										$ProximaEtapa->setAprovaMsg(NULL);
										$ProximaEtapa->setPrazo($arrayEtapas[$i]["prazo"]);
									} //FIM if($arrayEtapas[$i]["ordem"]) 
								} //FIM for
							}//fim ELSE
						//FIM CASO 2
					  	//caso não caia em nenhum caso acima, não pode trocar de etapa pois está aguardando alguma ação
						}else{
							$ProximaEtapa->setId(-1);
						}//fim else
					}//fim if($arrayEtapas[$i]["idetapa"]
				}//fim for
				return $ProximaEtapa;
			}//fim função










			
			FUNÇÃO ABAIXO ESTA COM ERRO:
			quando processo NORMAL, não acha próxima etapa.

			function proximaEtapaProcesso2($arrayEtapas,$dadosEtapaAtual,$Processo){
				//variavel que depende da importacao da Model ETAPA
				$ProximaEtapa = new Etapa();
				//varre array de etapas para saber qual vem depois dela
				for ($i=0; $i < sizeof($arrayEtapas); $i++) { 
					echo "<br><br>i=".$i;
					//quando encontrar a etapa atual, efetua verificações
					if($arrayEtapas[$i]["idetapa"] == $dadosEtapaAtual["idetapa"]){
							echo "<br>achou etapa atual";
							echo "<br><strong>CASO 1</strong>: ".$dadosEtapaAtual["fluxo"]."==".ETAPA_PRINCIPAL."&&"."(".$dadosEtapaAtual["aprovacao"] ."==". ETAPA_APROVADA ."||". $dadosEtapaAtual["aprovacao"] ."==". ETAPA_NAO_APROVAVEL.")";
						  //CASO 1 = se for do FLUXO PRINCIPAL e foi aprovada (ou não é aprovável)
						  if($dadosEtapaAtual["fluxo"]==ETAPA_PRINCIPAL && ($dadosEtapaAtual["aprovacao"] == ETAPA_APROVADA || $dadosEtapaAtual["aprovacao"] == ETAPA_NAO_APROVAVEL)){
						  	echo "<br>entrou CASO1!";
						    //se não for a última etapa
						    if( $i < (sizeof($arrayEtapas)-1) ){
						      //varre as próximas etapas do array para pegar a primeira que aparecer do fluxo principal que respeite as regras citadas abaixo
						      for($j=($i+1);$j<sizeof($arrayEtapas);$j++){
						      	echo "<br>Verificando condições: if(".$arrayEtapas[$j]["fluxo"]." == ".ETAPA_PRINCIPAL ."&& (                    
						          (".$Processo->getMilitar()." == ".PROCESSO_MILITAR."&&".$arrayEtapas[$j]["modo"]." != ".ETAPA_NAOMILITAR ."&&". $arrayEtapas[$j]["modo"]." != ".PROCESSOETAPA_COMELEICOES.") || (".$Processo->getModo()." == ".PROCESSOETAPA_COMELEICOES ."&&". $arrayEtapas[$j]["modo"]." != ".PROCESSOETAPA_SEMELEICOES.") || (".$Processo->getModo()." == ".PROCESSOETAPA_SEMELEICOES 
						            ."&&". $arrayEtapas[$j]["modo"]." != ".PROCESSOETAPA_COMELEICOES 
						            ."&& (".$Processo->getMilitar()." != ".PROCESSO_MILITAR ." || (".$Processo->getMilitar()." == ".PROCESSO_MILITAR ."&&". $arrayEtapas[$j]["modo"]." != ".ETAPA_NAOMILITAR.") )
						          )
						        ))";

						        if($arrayEtapas[$j]["fluxo"] == ETAPA_PRINCIPAL 
						        &&  (  
						        //se processo = militar, próxima etapa NÃO PODE ser c/eleições nem não militar                    
						          ($Processo->getMilitar()==PROCESSO_MILITAR && $arrayEtapas[$j]["modo"]!=ETAPA_NAOMILITAR && $arrayEtapas[$j]["modo"]!=PROCESSOETAPA_COMELEICOES)
						        ||
						        //se processo = modo COM eleições = próxima etapa não pode ser S/ eleições
						          ($Processo->getModo()==PROCESSOETAPA_COMELEICOES && $arrayEtapas[$j]["modo"]!=PROCESSOETAPA_SEMELEICOES)
						        ||
						        //se processo = modo SEM eleições = próxima etapa não pode ser C/ eleições e se processo militar = a etapa tbm não pode ser "n militar"
						          ($Processo->getModo()==PROCESSOETAPA_SEMELEICOES 
						            && $arrayEtapas[$j]["modo"]!=PROCESSOETAPA_COMELEICOES 
						            && ($Processo->getMilitar()!=PROCESSO_MILITAR || ($Processo->getMilitar()==PROCESSO_MILITAR && $arrayEtapas[$j]["modo"]!=ETAPA_NAOMILITAR))
						          )
						        )){

						        	echo "<br>achou uma etapa dentro do CASO1!";

						          //atribuimos valor ao objeto etapa que dá informações da próxima etapa
						          $ProximaEtapa->setId($arrayEtapas[$j]["idetapa"]);
						          $ProximaEtapa->setProcesso($Processo->getId());
						          $ProximaEtapa->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
						          $ProximaEtapa->setAprova($arrayEtapas[$j]["aprova"]);
						          $ProximaEtapa->setAprovaMsg(NULL);
						          $ProximaEtapa->setPrazo($arrayEtapas[$j]["prazo"]);
						          //seta J com o tamanho máximo para sair do for nesse momento
						          $j=sizeof($arrayEtapas);
						        //FIM IF $arrayEtapas[$j]["fluxo"] == ETAPA_PRINCIPAL
						        }
						      //FIM for($j=($i+1);$j<sizeof($arrayEtapas);$j++){
						      }
						    //FIM if( $i < (sizeof($arrayEtapas)-1) ){
						    //se for a última etapa, então não há próxima etapa
						    }else{
						    	echo "<br><strong>é a ultima etapa!</strong>";
						      $ProximaEtapa->setId(-1);
						    }

						  //FIM if CASO 1
						  //CASO2 = etapa do fluxo principal, que NAO FOI APROVADA, devendo ir pra próxima do fluxo alternativo
						  }elseif($dadosEtapaAtual["fluxo"]==ETAPA_PRINCIPAL && $dadosEtapaAtual["aprovacao"] == ETAPA_NAO_APROVADA){

						  	echo "<br>CASO2 : etapa do fluxo principal, que NAO FOI APROVADA, devendo ir pra próxima do fluxo alternativo<br>";

						    //se a etapa for do fluxo principal e não tiver sido aprovada, a próxima é a alternativa
							if($dadosEtapaAtual["fluxo"]==ETAPA_PRINCIPAL){
								//novaetapa é a próxima etapa com fluxo alternativo
								for($i=0;$i<sizeof($arrayEtapas);$i++) {
									//se a ordem da etapa for maior que a da anterior, e esta for de fluxo alternativo, é esta que será a próxima etapa
									if($arrayEtapas[$i]["ordem"] > $dadosEtapaAtual["ordem"] && $arrayEtapas[$i]["fluxo"] == ETAPA_ALTERNATIVA){
										//atribuimos valor ao objeto etapa que dá informações da próxima etapa
										$ProximaEtapa->setId($arrayEtapas[$i]["idetapa"]);
										$ProximaEtapa->setProcesso($Processo->getId());
										$ProximaEtapa->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
										$ProximaEtapa->setAprova($arrayEtapas[$i]["aprova"]);
										$ProximaEtapa->setAprovaMsg(NULL);
										$ProximaEtapa->setPrazo($arrayEtapas[$i]["prazo"]);
										//setamos o I com o valor máximo para sair do for
										$i=sizeof($arrayEtapas);
									} //FIM if($arrayEtapas[$i]["ordem"]
								} //FIM FOR

							//FIM if($dadosEtapaAnterior["fluxo"]==ETAPA_PRINCIPAL){
							//se a etapa for do fluxo alternativo e não tiver sido aprovada, a próxima é a na verdade a etapa anterior do fluxo principal
							}else{
								//novaetapa é a etapa anterior com fluxo principal
								for($i=0;$i<sizeof($arrayEtapas);$i++) {
									//se a ordem da etapa for menor que a da anterior, e esta for de fluxo principal ...
									//vamos sobreescrevendo essa informação até pegar a última etapa do fluxo principal que antecede a etapa em questão
									if($arrayEtapas[$i]["ordem"] < $dadosEtapaAtual["ordem"] && $arrayEtapas[$i]["fluxo"] == ETAPA_PRINCIPAL){
										//atribuimos valor ao objeto etapa que dá informações da próxima etapa
										$ProximaEtapa->setId($arrayEtapas[$i]["idetapa"]);
										$ProximaEtapa->setProcesso($Processo->getId());
										$ProximaEtapa->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
										$ProximaEtapa->setAprova($arrayEtapas[$i]["aprova"]);
										$ProximaEtapa->setAprovaMsg(NULL);
										$ProximaEtapa->setPrazo($arrayEtapas[$i]["prazo"]);
									} //FIM if($arrayEtapas[$i]["ordem"]) 
								} //FIM for
							}//fim ELSE
						//FIM CASO 2
					  	//caso não caia em nenhum caso acima, não pode trocar de etapa pois está aguardando alguma ação
						}else{
							$NovaEtapa->setId(-1);
						}//fim else
					}//fim if($arrayEtapas[$i]["idetapa"]
				}//fim for
				return $ProximaEtapa;
			}//fim função





			FUNÇÃO ABAIXO ESTA COM ERRO:
			processo/etapa com eleiçoes, está indo parar em uma etapa alternativa de outra etapa principal pois é a primeira subsequente com eleições
			
			function proximaEtapaProcesso2($arrayEtapas,$dadosEtapaAtual,$Processo){
				//variavel que depende da importacao da Model ETAPA
				$ProximaEtapa = new Etapa();
				//varre array de etapas para saber qual vem depois dela
				for ($i=0; $i < sizeof($arrayEtapas); $i++) { 
					echo "<br><br>i=".$i;
					//quando encontrar a etapa atual, efetua verificações
					if($arrayEtapas[$i]["idetapa"] == $dadosEtapaAtual["idetapa"]){
							echo "<br>achou etapa atual";
							echo "<br><strong>CASO 1</strong>: ".$dadosEtapaAtual["fluxo"]."==".ETAPA_PRINCIPAL."&&"."(".$dadosEtapaAtual["aprovacao"] ."==". ETAPA_APROVADA ."||". $dadosEtapaAtual["aprovacao"] ."==". ETAPA_NAO_APROVAVEL.")";
						  //CASO 1 = se for do FLUXO PRINCIPAL e foi aprovada (ou não é aprovável)
						  if($dadosEtapaAtual["fluxo"]==ETAPA_PRINCIPAL && ($dadosEtapaAtual["aprovacao"] == ETAPA_APROVADA || $dadosEtapaAtual["aprovacao"] == ETAPA_NAO_APROVAVEL)){
						  	echo "<br>entrou CASO1!";
						    //se não for a última etapa
						    if( $i < (sizeof($arrayEtapas)-1) ){
						      //varre as próximas etapas do array para pegar a primeira que aparecer do fluxo principal que respeite as regras citadas abaixo
						      for($j=($i+1);$j<sizeof($arrayEtapas);$j++){
						      	echo "<br>Verificando condições: if(".$arrayEtapas[$j]["fluxo"]." == ".ETAPA_PRINCIPAL ."&&                     
						          (".$Processo->getMilitar()." == ".PROCESSO_MILITAR."&&".$arrayEtapas[$j]["modo"]." != ".ETAPA_NAOMILITAR ."&&". $arrayEtapas[$j]["modo"]." != ".PROCESSOETAPA_COMELEICOES.") || (".$Processo->getModo()." == ".PROCESSOETAPA_COMELEICOES ."&&". $arrayEtapas[$j]["modo"]." != ".PROCESSOETAPA_SEMELEICOES.") || (".$Processo->getModo()." == ".PROCESSOETAPA_SEMELEICOES 
						            ."&&". $arrayEtapas[$j]["modo"]." != ".PROCESSOETAPA_COMELEICOES 
						            ."&& (".$Processo->getMilitar()." != ".PROCESSO_MILITAR ." || (".$Processo->getMilitar()." == ".PROCESSO_MILITAR ."&&". $arrayEtapas[$j]["modo"]." != ".ETAPA_NAOMILITAR.") )
						          )
						        )";

						        if($arrayEtapas[$j]["fluxo"] == ETAPA_PRINCIPAL 
						        &&    
						        //se processo = militar, próxima etapa NÃO PODE ser c/eleições nem não militar                    
						          ($Processo->getMilitar()==PROCESSO_MILITAR && $arrayEtapas[$j]["modo"]!=ETAPA_NAOMILITAR && $arrayEtapas[$j]["modo"]!=PROCESSOETAPA_COMELEICOES)
						        ||
						        //se processo = modo COM eleições = próxima etapa não pode ser S/ eleições
						          ($Processo->getModo()==PROCESSOETAPA_COMELEICOES && $arrayEtapas[$j]["modo"]!=PROCESSOETAPA_SEMELEICOES)
						        ||
						        //se processo = modo SEM eleições = próxima etapa não pode ser C/ eleições e se processo militar = a etapa tbm não pode ser "n militar"
						          ($Processo->getModo()==PROCESSOETAPA_SEMELEICOES 
						            && $arrayEtapas[$j]["modo"]!=PROCESSOETAPA_COMELEICOES 
						            && ($Processo->getMilitar()!=PROCESSO_MILITAR || ($Processo->getMilitar()==PROCESSO_MILITAR && $arrayEtapas[$j]["modo"]!=ETAPA_NAOMILITAR))
						          )
						        ){

						        	echo "<br>achou uma etapa dentro do CASO1!";

						          //atribuimos valor ao objeto etapa que dá informações da próxima etapa
						          $ProximaEtapa->setId($arrayEtapas[$j]["idetapa"]);
						          $ProximaEtapa->setProcesso($Processo->getId());
						          $ProximaEtapa->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
						          $ProximaEtapa->setAprova($arrayEtapas[$j]["aprova"]);
						          $ProximaEtapa->setAprovaMsg(NULL);
						          $ProximaEtapa->setPrazo($arrayEtapas[$j]["prazo"]);
						          //seta J com o tamanho máximo para sair do for nesse momento
						          $j=sizeof($arrayEtapas);
						        //FIM IF $arrayEtapas[$j]["fluxo"] == ETAPA_PRINCIPAL
						        }
						      //FIM for($j=($i+1);$j<sizeof($arrayEtapas);$j++){
						      }
						    //FIM if( $i < (sizeof($arrayEtapas)-1) ){
						    //se for a última etapa, então não há próxima etapa
						    }else{
						    	echo "<br><strong>é a ultima etapa!</strong>";
						      $ProximaEtapa->setId(-1);
						    }

						  //FIM if CASO 1
						  //CASO2 = etapa do fluxo principal, que NAO FOI APROVADA, devendo ir pra próxima do fluxo alternativo
						  }elseif($dadosEtapaAtual["fluxo"]==ETAPA_PRINCIPAL && $dadosEtapaAtual["aprovacao"] == ETAPA_NAO_APROVADA){

						  	echo "<br>CASO2 : etapa do fluxo principal, que NAO FOI APROVADA, devendo ir pra próxima do fluxo alternativo<br>";

						    //se a etapa for do fluxo principal e não tiver sido aprovada, a próxima é a alternativa
							if($dadosEtapaAtual["fluxo"]==ETAPA_PRINCIPAL){
								//novaetapa é a próxima etapa com fluxo alternativo
								for($i=0;$i<sizeof($arrayEtapas);$i++) {
									//se a ordem da etapa for maior que a da anterior, e esta for de fluxo alternativo, é esta que será a próxima etapa
									if($arrayEtapas[$i]["ordem"] > $dadosEtapaAtual["ordem"] && $arrayEtapas[$i]["fluxo"] == ETAPA_ALTERNATIVA){
										//atribuimos valor ao objeto etapa que dá informações da próxima etapa
										$ProximaEtapa->setId($arrayEtapas[$i]["idetapa"]);
										$ProximaEtapa->setProcesso($Processo->getId());
										$ProximaEtapa->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
										$ProximaEtapa->setAprova($arrayEtapas[$i]["aprova"]);
										$ProximaEtapa->setAprovaMsg(NULL);
										$ProximaEtapa->setPrazo($arrayEtapas[$i]["prazo"]);
										//setamos o I com o valor máximo para sair do for
										$i=sizeof($arrayEtapas);
									} //FIM if($arrayEtapas[$i]["ordem"]
								} //FIM FOR

							//FIM if($dadosEtapaAnterior["fluxo"]==ETAPA_PRINCIPAL){
							//se a etapa for do fluxo alternativo e não tiver sido aprovada, a próxima é a na verdade a etapa anterior do fluxo principal
							}else{
								//novaetapa é a etapa anterior com fluxo principal
								for($i=0;$i<sizeof($arrayEtapas);$i++) {
									//se a ordem da etapa for menor que a da anterior, e esta for de fluxo principal ...
									//vamos sobreescrevendo essa informação até pegar a última etapa do fluxo principal que antecede a etapa em questão
									if($arrayEtapas[$i]["ordem"] < $dadosEtapaAtual["ordem"] && $arrayEtapas[$i]["fluxo"] == ETAPA_PRINCIPAL){
										//atribuimos valor ao objeto etapa que dá informações da próxima etapa
										$ProximaEtapa->setId($arrayEtapas[$i]["idetapa"]);
										$ProximaEtapa->setProcesso($Processo->getId());
										$ProximaEtapa->setUsuario1($_SESSION["USUARIO"]["idusuario"]);
										$ProximaEtapa->setAprova($arrayEtapas[$i]["aprova"]);
										$ProximaEtapa->setAprovaMsg(NULL);
										$ProximaEtapa->setPrazo($arrayEtapas[$i]["prazo"]);
									} //FIM if($arrayEtapas[$i]["ordem"]) 
								} //FIM for
							}//fim ELSE
						//FIM CASO 2
					  	//caso não caia em nenhum caso acima, não pode trocar de etapa pois está aguardando alguma ação
						}else{
							$NovaEtapa->setId(-1);
						}//fim else
					}//fim if($arrayEtapas[$i]["idetapa"]
				}//fim for
				return $ProximaEtapa;
			}//fim função
				
			


			*/
?>