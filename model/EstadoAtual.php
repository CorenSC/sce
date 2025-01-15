<?php
class EstadoAtual{
	
	private $idestadoatual;
	private $nome;
	private $flag;
	
    public function getId() {
        return $this->idestadoatual;
    } 
    public function setId($valor) {
        $this->idestadoatual = $valor;
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

} ?>