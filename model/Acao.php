<?php
class Acao{

	private $idacao;
	private $nome;
	
    public function getId() {
        return $this->idacao;
    } 
    public function setId($v) {
        $this->idacao = $v;
        return $this;
    }
	
    public function getNome() {
        return $this->nome;
    } 
    public function setNome($v) {
        $this->nome = $v;
        return $this;
    }	

} ?>