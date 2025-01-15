<?php
class Perfil{

	private $idperfil;
	private $nome;
	private $flag;

    //transforma informações para serem inseridas no LOG:
    public function toLog(){
        $toLog="";
        if($this->getId()!=NULL){
            $toLog.="Código do perfil: ".$this->getId().APP_LINE_BREAK;
        }
        if($this->getNome()!=NULL){
            $toLog.="Nome: ".$this->getNome().APP_LINE_BREAK;
        }  
        return $toLog;
    }
	
    public function getId() {
        return $this->idperfil;
    }
 
    public function setId($v) {
        $this->idperfil = $v;
        return $this;
    }
	
    public function getNome() {
        return $this->nome;
    }
 
    public function setNome($v) {
        $this->nome = $v;
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