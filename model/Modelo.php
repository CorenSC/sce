<?php
class Modelo{

	private $idmodelo;
	private $nome;
    private $dtcriacao;
    private $dtatualizacao;
    private $link;
	private $flag;

    //transforma informações para serem inseridas no LOG:
    public function toLog(){
        $toLog="";
        if($this->getId()!=NULL){
            $toLog.="Código do Modelo de Documento: ".$this->getId().APP_LINE_BREAK;
        }
        if($this->getNome()!=NULL){
            $toLog.="Nome: ".$this->getNome().APP_LINE_BREAK;
        }
        return $toLog;
    }
	
    public function getId() {
        return $this->idmodelo;
    } 
    public function setId($v) {
        $this->idmodelo = $v;
        return $this;
    }
	
    public function getNome() {
        return $this->nome;
    } 
    public function setNome($v) {
        $this->nome = $v;
        return $this;
    }	

    public function getDtCriacao() {
        return $this->dtcriacao;
    } 
    public function setDtCriacao($v) {
        $this->dtcriacao = $v;
        return $this;
    }

    public function getDtAtualizacao() {
        return $this->dtatualizacao;
    } 
    public function setDtAtualizacao($v) {
        $this->dtatualizacao = $v;
        return $this;
    }

    public function getLink() {
        return $this->link;
    } 
    public function setLink($v) {
        $this->link = $v;
        return $this;
    }
	
    public function getFlag() {
        return $this->flag;
    } 
    public function setFlag($v) {
        $this->flag = $v;
        return $this;
    }

} ?>