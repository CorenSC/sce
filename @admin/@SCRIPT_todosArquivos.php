<?php

/*
$arquivoAtual = "menu_topo.php";
echo "<br><strong>-- Arquivo ".$arquivoAtual."</strong> <br><br><br>";
highlight_file($arquivoAtual);
echo "<br><br><br><strong>-- Fim do Arquivo ".$arquivoAtual."</strong><br><br><br>";

echo '<br><br>METODO SHOW_SOURCE:<br><br><div style="width:800px;word-wrap: break-word;">';
show_source('menu_topo.php');
echo '</div>';


BASE:
$arquivoAtual = "menu_topo.php";
echo "<br><strong>-- Arquivo ".$arquivoAtual."</strong> <br><br><br>";
$page=highlight_file($arquivoAtual, TRUE);  
$page=str_replace(  
array('<code>','/code>',' ','</font>','<font color="'),  
array('<div style="padding:1em;border:2px solid black;word-wrap: break-word;width:960px;">','/div>',' ','</span>','<span style="color:'),$page);  
echo utf8_decode($page);
echo "<br><br><br><strong>-- Fim do Arquivo ".$arquivoAtual."</strong><br><br><br>";

*/

echo utf8_decode("
	<strong style='color:red;'>ORGANIZAÇÃO DAS PASTAS (ESTRUTURA):</strong>
<br>/
<br>/bin
<br>/common
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/css
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/fonts
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/images
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/js
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/locales
<br>/control
<br>/dao
<br>/model
<br>/uploads
	");

echo "<br><br><br><br><strong style='color:red;'>ARQUIVOS DA RAIZ / (SEM PASTA):</strong><br><br><br><br>";

$p = opendir(".");
while(($dado = readdir($p))!==false) {

	if(strpos($dado,'.php') || strpos($dado,'.html') || strpos($dado,'.htm') || strpos($dado,'.txt') || strpos($dado,'.css') || strpos($dado,'.js')){
		echo "<br><strong>-- Arquivo ".$dado."</strong> <br><br><br>";
		$page=highlight_file($dado, TRUE);  
		$page=str_replace(  
		array('<code>','/code>',' ','</font>','<font color="'),  
		array('<div style="padding:1em;border:2px solid black;word-wrap: break-word;width:960px;">','/div>',' ','</span>','<span style="color:'),$page);  
		echo utf8_decode($page);
		echo "<br><br><br><strong>-- Fim do Arquivo ".$dado."</strong><br><br><br>";
	}


}
closedir($p);

$pasta = 'bin';echo "<br><br><br><br><strong style='color:red;'>ARQUIVOS DA PASTA /".$pasta.":</strong><br><br><br><br>";
$p = opendir("./".$pasta);while(($dado = readdir($p))!==false) {	if(strpos($dado,'.php') || strpos($dado,'.html') || strpos($dado,'.htm') || strpos($dado,'.txt') || strpos($dado,'.css') || strpos($dado,'.js')){		echo "<br><strong>-- Arquivo ".$dado."</strong> <br><br><br>";		$page=highlight_file('/'.$pasta.'/'.$dado, TRUE);  		$page=str_replace(  		array('<code>','/code>',' ','</font>','<font color="'),  		array('<div style="padding:1em;border:2px solid black;word-wrap: break-word;width:960px;">','/div>',' ','</span>','<span style="color:'),$page);  		echo utf8_decode($page);		echo "<br><br><br><strong>-- Fim do Arquivo ".$dado."</strong><br><br><br>";	}}closedir($p);

$pasta = 'common';echo "<br><br><br><br><strong style='color:red;'>ARQUIVOS DA PASTA /".$pasta.":</strong><br><br><br><br>";
$p = opendir("./".$pasta);while(($dado = readdir($p))!==false) {	if(strpos($dado,'.php') || strpos($dado,'.html') || strpos($dado,'.htm') || strpos($dado,'.txt') || strpos($dado,'.css') || strpos($dado,'.js')){		echo "<br><strong>-- Arquivo ".$dado."</strong> <br><br><br>";		$page=highlight_file('/'.$pasta.'/'.$dado, TRUE);  		$page=str_replace(  		array('<code>','/code>',' ','</font>','<font color="'),  		array('<div style="padding:1em;border:2px solid black;word-wrap: break-word;width:960px;">','/div>',' ','</span>','<span style="color:'),$page);  		echo utf8_decode($page);		echo "<br><br><br><strong>-- Fim do Arquivo ".$dado."</strong><br><br><br>";	}}closedir($p);

$pasta = 'common/css';echo "<br><br><br><br><strong style='color:red;'>ARQUIVOS DA PASTA /".$pasta.":</strong><br><br><br><br>";
$p = opendir("./".$pasta);while(($dado = readdir($p))!==false) {	if(strpos($dado,'.php') || strpos($dado,'.html') || strpos($dado,'.htm') || strpos($dado,'.txt') || strpos($dado,'.css') || strpos($dado,'.js')){		echo "<br><strong>-- Arquivo ".$dado."</strong> <br><br><br>";		$page=highlight_file('/'.$pasta.'/'.$dado, TRUE);  		$page=str_replace(  		array('<code>','/code>',' ','</font>','<font color="'),  		array('<div style="padding:1em;border:2px solid black;word-wrap: break-word;width:960px;">','/div>',' ','</span>','<span style="color:'),$page);  		echo utf8_decode($page);		echo "<br><br><br><strong>-- Fim do Arquivo ".$dado."</strong><br><br><br>";	}}closedir($p);

$pasta = 'common/fonts';echo "<br><br><br><br><strong style='color:red;'>ARQUIVOS DA PASTA /".$pasta.":</strong><br><br><br><br>";
$p = opendir("./".$pasta);while(($dado = readdir($p))!==false) {	if(strpos($dado,'.php') || strpos($dado,'.html') || strpos($dado,'.htm') || strpos($dado,'.txt') || strpos($dado,'.css') || strpos($dado,'.js')){		echo "<br><strong>-- Arquivo ".$dado."</strong> <br><br><br>";		$page=highlight_file('/'.$pasta.'/'.$dado, TRUE);  		$page=str_replace(  		array('<code>','/code>',' ','</font>','<font color="'),  		array('<div style="padding:1em;border:2px solid black;word-wrap: break-word;width:960px;">','/div>',' ','</span>','<span style="color:'),$page);  		echo utf8_decode($page);		echo "<br><br><br><strong>-- Fim do Arquivo ".$dado."</strong><br><br><br>";	}}closedir($p);

$pasta = 'common/images';echo "<br><br><br><br><strong style='color:red;'>ARQUIVOS DA PASTA /".$pasta.":</strong><br><br><br><br>";
$p = opendir("./".$pasta);while(($dado = readdir($p))!==false) {	if(strpos($dado,'.php') || strpos($dado,'.html') || strpos($dado,'.htm') || strpos($dado,'.txt') || strpos($dado,'.css') || strpos($dado,'.js')){		echo "<br><strong>-- Arquivo ".$dado."</strong> <br><br><br>";		$page=highlight_file('/'.$pasta.'/'.$dado, TRUE);  		$page=str_replace(  		array('<code>','/code>',' ','</font>','<font color="'),  		array('<div style="padding:1em;border:2px solid black;word-wrap: break-word;width:960px;">','/div>',' ','</span>','<span style="color:'),$page);  		echo utf8_decode($page);		echo "<br><br><br><strong>-- Fim do Arquivo ".$dado."</strong><br><br><br>";	}}closedir($p);

$pasta = 'common/js';echo "<br><br><br><br><strong style='color:red;'>ARQUIVOS DA PASTA /".$pasta.":</strong><br><br><br><br>";
$p = opendir("./".$pasta);while(($dado = readdir($p))!==false) {	if(strpos($dado,'.php') || strpos($dado,'.html') || strpos($dado,'.htm') || strpos($dado,'.txt') || strpos($dado,'.css') || strpos($dado,'.js')){		echo "<br><strong>-- Arquivo ".$dado."</strong> <br><br><br>";		$page=highlight_file('/'.$pasta.'/'.$dado, TRUE);  		$page=str_replace(  		array('<code>','/code>',' ','</font>','<font color="'),  		array('<div style="padding:1em;border:2px solid black;word-wrap: break-word;width:960px;">','/div>',' ','</span>','<span style="color:'),$page);  		echo utf8_decode($page);		echo "<br><br><br><strong>-- Fim do Arquivo ".$dado."</strong><br><br><br>";	}}closedir($p);

$pasta = 'common/js/locales';echo "<br><br><br><br><strong style='color:red;'>ARQUIVOS DA PASTA /".$pasta.":</strong><br><br><br><br>";
$p = opendir("./".$pasta);while(($dado = readdir($p))!==false) {	if(strpos($dado,'.php') || strpos($dado,'.html') || strpos($dado,'.htm') || strpos($dado,'.txt') || strpos($dado,'.css') || strpos($dado,'.js')){		echo "<br><strong>-- Arquivo ".$dado."</strong> <br><br><br>";		$page=highlight_file('/'.$pasta.'/'.$dado, TRUE);  		$page=str_replace(  		array('<code>','/code>',' ','</font>','<font color="'),  		array('<div style="padding:1em;border:2px solid black;word-wrap: break-word;width:960px;">','/div>',' ','</span>','<span style="color:'),$page);  		echo utf8_decode($page);		echo "<br><br><br><strong>-- Fim do Arquivo ".$dado."</strong><br><br><br>";	}}closedir($p);

$pasta = 'control';echo "<br><br><br><br><strong style='color:red;'>ARQUIVOS DA PASTA /".$pasta.":</strong><br><br><br><br>";
$p = opendir("./".$pasta);while(($dado = readdir($p))!==false) {	if(strpos($dado,'.php') || strpos($dado,'.html') || strpos($dado,'.htm') || strpos($dado,'.txt') || strpos($dado,'.css') || strpos($dado,'.js')){		echo "<br><strong>-- Arquivo ".$dado."</strong> <br><br><br>";		$page=highlight_file('/'.$pasta.'/'.$dado, TRUE);  		$page=str_replace(  		array('<code>','/code>',' ','</font>','<font color="'),  		array('<div style="padding:1em;border:2px solid black;word-wrap: break-word;width:960px;">','/div>',' ','</span>','<span style="color:'),$page);  		echo utf8_decode($page);		echo "<br><br><br><strong>-- Fim do Arquivo ".$dado."</strong><br><br><br>";	}}closedir($p);

$pasta = 'dao';echo "<br><br><br><br><strong style='color:red;'>ARQUIVOS DA PASTA /".$pasta.":</strong><br><br><br><br>";
$p = opendir("./".$pasta);while(($dado = readdir($p))!==false) {	if(strpos($dado,'.php') || strpos($dado,'.html') || strpos($dado,'.htm') || strpos($dado,'.txt') || strpos($dado,'.css') || strpos($dado,'.js')){		echo "<br><strong>-- Arquivo ".$dado."</strong> <br><br><br>";		$page=highlight_file('/'.$pasta.'/'.$dado, TRUE);  		$page=str_replace(  		array('<code>','/code>',' ','</font>','<font color="'),  		array('<div style="padding:1em;border:2px solid black;word-wrap: break-word;width:960px;">','/div>',' ','</span>','<span style="color:'),$page);  		echo utf8_decode($page);		echo "<br><br><br><strong>-- Fim do Arquivo ".$dado."</strong><br><br><br>";	}}closedir($p);

$pasta = 'model';echo "<br><br><br><br><strong style='color:red;'>ARQUIVOS DA PASTA /".$pasta.":</strong><br><br><br><br>";
$p = opendir("./".$pasta);while(($dado = readdir($p))!==false) {	if(strpos($dado,'.php') || strpos($dado,'.html') || strpos($dado,'.htm') || strpos($dado,'.txt') || strpos($dado,'.css') || strpos($dado,'.js')){		echo "<br><strong>-- Arquivo ".$dado."</strong> <br><br><br>";		$page=highlight_file('/'.$pasta.'/'.$dado, TRUE);  		$page=str_replace(  		array('<code>','/code>',' ','</font>','<font color="'),  		array('<div style="padding:1em;border:2px solid black;word-wrap: break-word;width:960px;">','/div>',' ','</span>','<span style="color:'),$page);  		echo utf8_decode($page);		echo "<br><br><br><strong>-- Fim do Arquivo ".$dado."</strong><br><br><br>";	}}closedir($p);

$pasta = 'uploads';echo "<br><br><br><br><strong style='color:red;'>ARQUIVOS DA PASTA /".$pasta.":</strong><br><br><br><br>";
$p = opendir("./".$pasta);while(($dado = readdir($p))!==false) {	if(strpos($dado,'.php') || strpos($dado,'.html') || strpos($dado,'.htm') || strpos($dado,'.txt') || strpos($dado,'.css') || strpos($dado,'.js')){		echo "<br><strong>-- Arquivo ".$dado."</strong> <br><br><br>";		$page=highlight_file('/'.$pasta.'/'.$dado, TRUE);  		$page=str_replace(  		array('<code>','/code>',' ','</font>','<font color="'),  		array('<div style="padding:1em;border:2px solid black;word-wrap: break-word;width:960px;">','/div>',' ','</span>','<span style="color:'),$page);  		echo utf8_decode($page);		echo "<br><br><br><strong>-- Fim do Arquivo ".$dado."</strong><br><br><br>";	}}closedir($p);


/*
$arquivoAtual = "menu_topo.php";
echo "<br><strong>-- Arquivo ".$arquivoAtual."</strong> <br><br><br>";
$page=highlight_file($arquivoAtual, TRUE);  
$page=str_replace(  
array('<code>','/code>',' ','</font>','<font color="'),  
array('<div style="padding:1em;border:2px solid black;word-wrap: break-word;width:960px;">','/div>',' ','</span>','<span style="color:'),$page);  
echo utf8_decode($page);
echo "<br><br><br><strong>-- Fim do Arquivo ".$arquivoAtual."</strong><br><br><br>";
*/




?>