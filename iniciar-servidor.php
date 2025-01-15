<?php

$host = 'localhost';
$port = 8030;

try {
    echo "Iniciando o servidor php no enredeço $host:$port... \n";

    $comando = "php -S $host:$port";
    shell_exec($comando);
} catch (Exception $exception) {
    echo "Não foi possivel iniciar o servidor $host:$port. \n";
    echo $exception->getMessage();
}


