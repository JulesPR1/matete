<?php

class Vendeur implements JsonSerializable{
    private $id;
    private $nom;
    private $numero;
    private $mail;


    function __construct($id, $nom, $numero, $mail){
        $this->id = $id;
        $this->nom = $nom;
        $this->numero = $numero;
        $this->mail = $mail;
    }

    public function getId(){
        return $this->id;
    }
    public function getNom(){
        return $this->nom;
    }
    public function getNumero(){
        return $this->numero;
    }
    public function getMail(){
        return $this->mail;
    }


    public function jsonSerialize (){ // Fournit les fonctionnalités permettant de sérialiser des objets ou des types valeur en JSON et de désérialiser JSON en objets ou types valeur.
        return [
            "id" => $this->id,
            "nom" => $this->nom,
            "numero" => $this->numero,
            "mail" => $this->mail,
        ];
    }
}