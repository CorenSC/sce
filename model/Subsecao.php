<?php
class Subsecao{

	private $idsubsecao;
    private $idmunicipio;
	private $nome;
	private $flag;

    //transforma informações para serem inseridas no LOG:
    public function toLog(){
        $toLog="";
        if($this->getId()!=NULL){
            $toLog.="Código da Subseção: ".$this->getId().APP_LINE_BREAK;
        }
        if($this->getMunicipio()!=NULL){
            $toLog.="Cidade: ".$this->getMunicipio().APP_LINE_BREAK;
        }
        if($this->getNome()!=NULL){
            $toLog.="Nome: ".$this->getNome().APP_LINE_BREAK;
        }
        return $toLog;
    }
	
    public function getId() {
        return $this->idsubsecao;
    }
    public function setId($v) {
        $this->idsubsecao = $v;
        return $this;
    }

    public function getMunicipio() {
        return $this->idmunicipio;
    } 
    public function setMunicipio($v) {
        $this->idmunicipio = $v;
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