<?php
class Encaminhamento{

	private $idencaminhamento;
    private $dt;
    private $idpad;
    private $idusuario;
    private $idsetororigem;
    private $idsetordestino;
    private $email;
    private $mensagem;

    //transforma informações para serem inseridas no LOG:
    public function toLog(){
        $toLog="";
        if($this->getId()!=NULL){
            $toLog.="Código deste Encaminhamento: ".$this->getId().APP_LINE_BREAK;
        }
        if($this->getSetorOrigem()!=NULL){
            $toLog.="Setor Origem: ".$this->getSetorOrigem().APP_LINE_BREAK;
        }  
        if($this->getSetorDestino()!=NULL){
            $toLog.="Setor Destino: ".$this->getSetorDestino().APP_LINE_BREAK;
        }  
        if($this->getEmail()!=NULL){
            $toLog.="Enviado e-mail para os usuários do setor: ".$this->getEmail().APP_LINE_BREAK;
        }  
        if($this->getMensagem()!=NULL){
            $toLog.="Mensagem deste encaminhamento: ".$this->getMensagem().APP_LINE_BREAK;
        }  
        return $toLog;
    }
	
    public function getId() {
        return $this->idencaminhamento;
    }
 
    public function setId($valor) {
        $this->idencaminhamento = $valor;
        return $this;
    }
	
    public function getDt() {
        return $this->dt;
    }
 
    public function setDt($valor) {
        $this->dt = $valor;
        return $this;
    }	
	
    public function getPad() {
        return $this->idpad;
    }
 
    public function setPad($valor) {
        $this->idpad = $valor;
        return $this;
    }

    public function getUsuario() {
        return $this->idusuario;
    }
 
    public function setUsuario($valor) {
        $this->idusuario = $valor;
        return $this;
    }

    public function getSetorOrigem() {
        return $this->idsetororigem;
    }
 
    public function setSetorOrigem($valor) {
        $this->idsetororigem = $valor;
        return $this;
    }

    public function getSetorDestino() {
        return $this->idsetordestino;
    }
 
    public function setSetorDestino($valor) {
        $this->idsetordestino = $valor;
        return $this;
    }

    public function getEmail() {
        return $this->email;
    }
 
    public function setEmail($valor) {
        $this->email = $valor;
        return $this;
    }    

    public function getMensagem() {
        return $this->mensagem;
    }
 
    public function setMensagem($valor) {
        $this->mensagem = $valor;
        return $this;
    }   


} ?>