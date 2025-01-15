<?php
class Responsavel{	
	private $idresponsavel;
    private $idprocesso;
    private $idusuario;
    public function getId() {
        return $this->idresponsavel;
    } 
    public function setId($v) {
        $this->idresponsavel = $v;
        return $this;
    }
    public function getProcesso() {
        return $this->idprocesso;
    } 
    public function setProcesso($v) {
        $this->idprocesso = $v;
        return $this;
    }
    public function getUsuario() {
        return $this->idusuario;
    } 
    public function setUsuario($v) {
        $this->idusuario = $v;
        return $this;
    }
}
?>