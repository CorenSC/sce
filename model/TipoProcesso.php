<?php
class TipoProcesso{
	
	private $idtipoprocesso;
	private $nome;
	private $flag;
	
    public function getId() {
        return $this->idtipoprocesso;
    } 
    public function setId($v) {
        $this->idtipoprocesso = $v;
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