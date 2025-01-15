<?php

class Funcao{
	
	private $idfuncao;
	private $nome;
	private $flag;
	
    public function getId() {
        return $this->idfuncao;
    }
 
    public function setId($idfuncao) {
        $this->idfuncao = $idfuncao;
        return $this;
    }
	
    public function getNome() {
        return $this->nome;
    }
 
    public function setNome($nome) {
        $this->nome = $nome;
        return $this;
    }
	
    public function getFlag() {
        return $this->flag;
    }
 
    public function setFlag($flag) {
        $this->flag = $flag;
        return $this;
    }
}

?>