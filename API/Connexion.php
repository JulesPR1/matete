<?php

include("Emplacement.php");
include("Vendeur.php");
include("Categorie.php");
include("Annonce.php");

class Connexion{
    private $dbh;

    function __construct($bd){
        try {
            $this->dbh = new PDO('mysql:host=localhost;dbname='.$bd.';charset=utf8',"user","btssio");
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    function getAllAnnonces(){
        $stmt = $this->dbh->prepare('SELECT *, annonce.id as annonceID, annonce.nom as nomAnnonce, annonce.description as descriptionAnnonce FROM annonce INNER JOIN user ON annonce.le_user_id = user.id JOIN emplacement ON annonce.emplacement_id = emplacement.id JOIN categorie ON annonce.la_categorie_id = categorie.id');
        $stmt->execute();
        $annoncePOO = array();
        while ($row = $stmt->fetch()) {
            $emplacement = new Emplacement($row['adresse'], $row['code_postal']);
            $categorie = new Categorie($row['nom'], $row['seuilmini']);
            $vendeur = new Vendeur($row['le_user_id'], $row['username'], $row['num_tel'], $row['email']);
            $annonce = new Annonce($row['annonceID'],$row['nomAnnonce'],$row['descriptionAnnonce'],$row['quantite'],$row["image"],$vendeur,$categorie,$emplacement);
            array_push($annoncePOO, $annonce);
        }
        return $annoncePOO;
    }

    function getAllCategories(){
        $stmt2 = $this->dbh->prepare('SELECT * FROM categorie');
        $stmt2->execute();
        $categoriesPOO = array();
        while ($row = $stmt2->fetch()) {
            $categorie = new Categorie($row['nom'], $row['seuilmini']);
            array_push($categoriesPOO, $categorie);
        }
        return $categoriesPOO;
    }
}   