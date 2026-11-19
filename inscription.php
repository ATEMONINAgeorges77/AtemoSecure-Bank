<?php
// 1. DÉMARRAGE DE LA SESSION (Doit être la toute première ligne)
session_start();

// 2. Connexion à la base de données
$host = "localhost";
$user = "root";
$password = "";
$dbname = "bank"; // ⚠️ Mets ici le vrai nom de ta base de données

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("La connexion a échoué : " . mysqli_connect_error());
}

// 3. Récupération des données du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nom = $_POST['Nom'];
    $prenom = $_POST['Prenom'];
    $email = $_POST['email'];
    $tel = $_POST['tel'];
    $montant_initial= $_POST['montant_initial'];

    // ⚠️ Remplace 'utilisateurs' par le nom exact de ta table
    // ⚠️ Remplace 'montant' par le nom exact de ta colonne montant si tu l'as modifié
    $sql = "INSERT INTO utilisateurs (nom, prenom, email, tel, montant_initial) 
            VALUES ('$nom', '$prenom', '$email', '$tel', '$montant_initial')";

    if (mysqli_query($conn, $sql)) {
        
        // 4. CRÉATION DE LA SESSION UTILISATEUR
        // mysqli_insert_id récupère l'ID automatique que MySQL vient de générer
        $_SESSION['user_id'] = mysqli_insert_id($conn);
        $_SESSION['user_nom'] = $nom;
        $_SESSION['user_prenom'] = $prenom;

        // 5. REDIRECTION DIRECTE VERS SON ESPACE PERSONNEL
        // ⚠️ Remplace le chemin ci-dessous par le nom exact de ton fichier de tableau de bord
        header("Location: Dashboard Users.php");
        exit(); // Arrête le script pour forcer la redirection
        
    } else {
        echo "Erreur : " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>

