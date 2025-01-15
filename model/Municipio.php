<?php
class Municipio{

	private $idmunicipio;
	private $iduf;
    private $nome;	
	
    public function getId() {
        return $this->idsae;
    } 
    public function setId($v) {
        $this->idsae = $v;
        return $this;
    }

    public function getUf() {
        return $this->iduf;
    } 
    public function setUf($v) {
        $this->iduf = $v;
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