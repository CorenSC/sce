<?php
require_once("config.php");

$current_host = $_SERVER['HTTP_HOST'];

if ($current_host == "localhost:8030" || $current_host == "127.0.0.1") {
    $url = "http://localhost:8030/login.php";
} elseif ($current_host == "jurere-homolog.corensc.gov.br") {
    $url = "http://jurere-homolog.corensc.gov.br/sistemas/sce/login.php";
} else {
    $url = "http://sistemas.corensc.gov.br/control/index.php";
}

echo "<meta http-equiv=\"refresh\" content=\"0; url=$url\">";
?>