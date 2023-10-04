<?php

class Annonce implements JsonSerializable{
    private $id;
    private $nom;
    private $description;
    private $quantite;
    private $vendeur;
    private $categorie;
    private $image;
    private $emplacement;

    function __construct($id, $nom, $description, $quantite, $image, $vendeur, $categorie, $emplacement){
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->quantite = $quantite;
        $this->vendeur = $vendeur;
        $this->categorie = $categorie;
        $this->emplacement = $emplacement;
        $this->image = $image;
    }

    public function getId(){
        return $this->id;
    }
    public function getNom(){
        return $this->nom;
    }
    public function getDescription(){
        return $this->description;
    }
    public function getQuantite(){
        return $this->quantite;
    }
    public function getVendeur(){
        return $this->vendeur;
    }
    public function getCategorie(){
        return $this->categorie;
    }
    public function getEmplacement(){
        return $this->emplacement;
    }
    public function getImage(){
        return $this->image;
    }
    public function jsonSerialize (){
        return [
            "id" => $this->id,
            "nom" => $this->nom,
            "description" => $this->description,
            "quantite" => $this->quantite,
            "vendeur" => $this->vendeur,
            "categorie" => $this->categorie,
            "emplacement" => $this->emplacement,
            "image" => $this->image
        ];
    }
}