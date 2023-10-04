<?php

class Categorie implements JsonSerializable{
    private $libellé;
    private $seuilmini;

    function __construct($libellé, $seuilmini){
        $this->libellé = $libellé;
        $this->seuilmini = $seuilmini;
    }

    public function getLibellé(){
        return $this->libellé;
    }

    public function getSeuilmini(){
        return $this->seuilmini;
    }

    public function jsonSerialize (){
        return [
            "libelle" => $this->libellé,
            "seuilmini" => $this->seuilmini
        ];
    }
}