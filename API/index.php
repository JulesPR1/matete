<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require("Connexion.php");

if(isset($_GET["action"]) && $_GET["action"]== "all"){
    $c = new Connexion("matete");
    $annonces = $c->getAllAnnonces();
    echo '{"annonces":'.json_encode($annonces)."}";
}
if(isset($_GET["action"]) && $_GET["action"]== "cat"){
    $c = new Connexion("matete");
    $categories = $c->getAllCategories();
    echo '{"categories":'.json_encode($categories)."}";
}
?>
