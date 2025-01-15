<?php
class Uf{

	private $iduf;
    private $sigla;
    private $nome;	
	
    public function getId() {
        return $this->iduf;
    } 
    public function setId($v) {
        $this->iduf = $v;
        return $this;
    }

    public function getSigla() {
        return $this->sigla;
    }
    public function setSigla($v) {
        $this->sigla = $v;
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