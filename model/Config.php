<?php
class Config{

	private $idconfig;
	private $nome;
    private $dtatualizacao;	
	
    public function getId() {
        return $this->idconfig;
    } 
    public function setId($v) {
        $this->idconfig = $v;
        return $this;
    }

    public function getNome() {
        return $this->nome;
    } 
    public function setNome($v) {
        $this->nome = $v;
        return $this;
    }

    public function getDtAtualizacao() {
        return $this->dtatualizacao;
    } 
    public function setDtAtualizacao($v) {
        $this->dtatualizacao = $v;
        return $this;
    }
    
} ?>