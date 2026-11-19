<?php
$serveur = "localhost";
$utilisateur = "root";
$mot_de_passe = "";
$base_de_donnees = "bank";

$connexion = mysqli_connect($serveur, $utilisateur,$mot_de_passe,$base_de_donnees);
mysqli_select_db($connexion, "bank");

if (!$connexion){
    die("Echec de la connexion: ". mysqli_connect_error());
}
?>