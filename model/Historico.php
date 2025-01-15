<?php
class Historico{
	
	private $idhistorico;
    private $dt;
	private $idusuario;
	private $idacao;
    private $idprocesso;
    private $iddocumento;
    private $obs;
    private $ip;
	
    public function getId() {
        return $this->idhistorico;
    } 
    public function setId($idhistorico) {
        $this->idhistorico = $idhistorico;
        return $this;
    }	
    
    public function getDt() {
        return $this->dt;
    } 
    public function setDt($dt) {
        $this->dt = $dt;
        return $this;
    }

    public function getUsuario() {
        return $this->idusuario;
    } 
    public function setUsuario($idusuario) {
        $this->idusuario = $idusuario;
        return $this;
    }

    public function getAcao() {
        return $this->idacao;
    } 
    public function setAcao($v) {
        $this->idacao = $v;
        return $this;
    }

    public function getProcesso() {
        return $this->idprocesso;
    }
    public function setProcesso($v) {
        $this->idprocesso = $v;
        return $this;
    }

    public function getDocumento() {
        return $this->iddocumento;
    }
    public function setDocumento($v) {
        $this->iddocumento = $v;
        return $this;
    }

    public function getObs() {
        return $this->obs;
    } 
    public function setObs($obs) {
        $this->obs = $obs;
        return $this;
    }

    public function getIp() {
        return $this->ip;
    } 
    public function setIp($v) {
        $this->ip = $v;
        return $this;
    }

} ?>