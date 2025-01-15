<?php
/*	

Sistema Desenvolvido pelo DTI - COREN / SC - 2015

*/

$myDB_dsn = 'mysql:host=localhost;dbname=coren_sce';
/* local
$myDB_user = 'root';
$myDB_pass = 'root';
/* fim local */
/* servidor */
$myDB_user = 'coren_sce';
$myDB_pass = '7.Nm@-(),44x';
/* fim servidor */

$options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,);
if( defined('PDO::MYSQL_ATTR_INIT_COMMAND') ){
	$options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES utf8';
}
$myDB_dsn .= ';charset=utf8';

try {
    $myBD = @new PDO($myDB_dsn, $myDB_user, $myDB_pass, $options);

} catch (PDOException $e) {
    echo $e->getMessage();
}

/* ORIENTAÇÕES GERAIS / DICAS GERAIS

------------- PDO ------------- 


***		fonte: http://www3.di.uminho.pt/~jcr/TUTORIAL/tutorial-php-mysql.html
***		manual: http://php.net/manual/pt_BR/book.pdo.php
***		tut: http://www.diogomatheus.com.br/blog/php/trabalhando-com-pdo-no-php/

INFOS / CONSTANTES / DICAS DO PDO
1) EXIBINDO_ERROS:
	$bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


2) PARA "FECHAR" UMA CONEXÃO:
	$conn=null; OU $this->conn = null;
---------
	
*/

?>