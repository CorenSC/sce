<?php
class Usuario {
    
    private $idusuario;
    private $idperfil;
    private $idmunicipio;
    private $idsubsecao;
    private $nome;
    private $nomeInstituicao;    
    private $login;
    private $senha;
    private $email1;
    private $email2;
    private $celular;
    private $telefone;
    private $dtcriacao;
    private $dtexpiracao;
    private $flag;
    private $tentativas_num;
    //private $tentativas_time;

    //transforma informações para serem inseridas no LOG:
    public function toLog(){
        $toLog="";

        if($this->getId()!=NULL){
            $toLog.="Código do usuário: ".$this->getId().APP_LINE_BREAK;
        }
        if($this->getPerfil()!=NULL){
            $toLog.="Perfil: ".$this->getPerfil().APP_LINE_BREAK;
        }
        if($this->getMunicipio()!=NULL){
            $toLog.="Município: ".$this->getMunicipio().APP_LINE_BREAK;
        } 
        if($this->getSubsecao()!=NULL){
            $toLog.="Subseção: ".$this->getSubsecao().APP_LINE_BREAK;
        }
        if($this->getNome()!=NULL){
            $toLog.="Nome do usuário: ".$this->getNome().APP_LINE_BREAK;
        }
        if($this->getLogin()!=NULL){
            $toLog.="Login: ".$this->getLogin().APP_LINE_BREAK;
        }
        if($this->getEmail1()!=NULL){
            $toLog.="E-mail Principal: ".$this->getEmail1().APP_LINE_BREAK;
        }
        if($this->getEmail2()!=NULL){
            $toLog.="E-mail Secundário: ".$this->getEmail2().APP_LINE_BREAK;
        }        
        if($this->getCelular()!=NULL){
            $toLog.="Celular: ".exibeTelefone($this->getCelular()).APP_LINE_BREAK;
        }
        if($this->getTelefone()!=NULL){
            $toLog.="Telefone: ".exibeTelefone($this->getTelefone()).APP_LINE_BREAK;
        }
        if($this->getNomeInstituicao()!=NULL){
            $toLog.="Nome da Instituição: ".$this->getNomeInstituicao().APP_LINE_BREAK;
        }
        if($this->getDtExpiracao()!=NULL && $this->getDtExpiracao()!=0){
            $toLog.="Data de expiração do acesso: ".exibeData($this->getDtExpiracao()).APP_LINE_BREAK;
        }
        
        return $toLog;
    }
 
    public function getId() {
        return $this->idusuario;
    } 
    public function setId($idusuario) {
        $this->idusuario = $idusuario;
        return $this;
    }
 
    public function getPerfil() {
        return $this->idperfil;
    } 
    public function setPerfil($idperfil) {
        $this->idperfil = $idperfil;
        return $this;
    }

    public function getMunicipio() {
        return $this->idmunicipio;
    } 
    public function setMunicipio($v) {
        $this->idmunicipio = $v;
        return $this;
    }

    public function getSubsecao() {
        return $this->idsubsecao;
    } 
    public function setSubsecao($v) {
        $this->idsubsecao = $v;
        return $this;
    }
 
    public function getNome() {
        return $this->nome;
    } 
    public function setNome($nome) {
        $this->nome = $nome;
        return $this;
    }

    public function getEmail1() {
        return $this->email1;
    } 
    public function setEmail1($v) {
        $this->email1 = $v;
        return $this;
    }

    public function getEmail2() {
        return $this->email2;
    } 
    public function setEmail2($v) {
        $this->email2 = $v;
        return $this;
    }

    public function getLogin() {
        return $this->login;
    } 
    public function setLogin($login) {
        $this->login = $login;
        return $this;
    }

    public function getSenha() {
        return $this->senha;
    } 
    public function setSenha($senha) {
        $this->senha = $senha;
        return $this;
    }

    public function getDtCriacao() {
        return $this->dtcriacao;
    } 
    public function setDtCriacao($dtcriacao) {
        $this->dtcriacao = $dtcriacao;
        return $this;
    }
    
    public function getDtExpiracao() {
        return $this->dtexpiracao;
    } 
    public function setDtExpiracao($dtexpiracao) {
        $this->dtexpiracao = $dtexpiracao;
        return $this;
    }

    public function getCelular() {
        return $this->celular;
    } 
    public function setCelular($v) {
        $this->celular = $v;
        return $this;
    }

    public function getTelefone() {
        return $this->telefone;
    } 
    public function setTelefone($v) {
        $this->telefone = $v;
        return $this;
    }

    public function getNomeInstituicao() {
        return $this->nomeInstituicao;
    } 
    public function setNomeInstituicao($v) {
        $this->nomeInstituicao = $v;
        return $this;
    }

    public function getTentativasNum() {
        return $this->tentativas_num;
    } 
    public function setTentativasNum($v) {
        $this->tentativas_num = $v;
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