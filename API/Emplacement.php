<?php

class Emplacement implements JsonSerializable{
    private $adresse;

    function __construct($adresse, $codePostale){
        $this->adresse = $adresse;
        $this->codePostale = $codePostale;
    }

    public function getAdresse(){
        return $this->adresse;
    }

    public function getCodePostale(){
        return $this->codePostale;
    }
    
    public function jsonSerialize (){
        return [
            "adresse" => $this->adresse,
            "codePostale" => $this->codePostale
        ];
    }
}