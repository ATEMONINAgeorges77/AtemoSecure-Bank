<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Récupération et nettoyage de l'e-mail saisi
    if (isset($_POST['email_client']) && !empty($_POST['email_client'])) {
        $email = mysqli_real_escape_string($connexion, trim($_POST['email_client']));

        // 2. Recherche automatique de l'ID associé à cet e-mail dans la table 'utilisateurs'
        $req_find_user = "SELECT id FROM utilisateurs WHERE email = '$email'";
        $res_find_user = mysqli_query($connexion, $req_find_user);

        if ($res_find_user && mysqli_num_rows($res_find_user) > 0) {
            $user_data = mysqli_fetch_assoc($res_find_user);
            $id_utilisateur = intval($user_data['id']);
        } else {
            // Si l'e-mail n'existe pas dans la base de données
            echo "<script>
                    alert('Erreur : Aucun compte client trouvé avec l\'adresse e-mail \"$email\".');
                    window.location.href = 'Formulaire_transaction.php';
                  </script>";
            exit();
        }
    } else {
        echo "<script>
                alert('Veuillez renseigner votre adresse e-mail.');
                window.location.href = 'Formulaire_transaction.php';
              </script>";
        exit();
    }

    // 3. Sauvegarde de l'ID trouvé en session pour charger son Dashboard
    $_SESSION['user_id'] = $id_utilisateur;

    // 4. Récupération des données du formulaire
    $type_formulaire = mysqli_real_escape_string($connexion, $_POST['type_transaction']);
    $montant = floatval($_POST['montant']);

    
    // Intégration du modèle IA(FLASK)
    $data_ia = array("montant" => (float)$montant);
    $json_data = json_encode($data_ia);

    $ch = curl_init("http://127.0.0.1:5000/predict");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    $response = curl_exec($ch);
    curl_close($ch);

    $resultat_ia = json_decode($response, true);
    
    // La variable $statut recevra "Conforme" ou "Suspect" renvoyé par Python
    //$statut = isset($resultat_ia['statut']) ? $resultat_ia['statut'] : 'Conforme';

    if ($montant >= 1000000){
        $statut="suspect";
   } else{
       $statut = "conforme";
    }

    // Récuoération Dynamique du statut renvoyé

    //if (isset($resulatat_ia['statut'])) {
      // $statut= $resulatat_ia['statut'];
   // } else{
       // $statut= "conforme";
   // }



    // Normalisation du type pour le champ ENUM
    if (in_array(strtolower($type_formulaire), ['transfert', 'transfer'])) {
        $type = 'Transfer';
    } else {
        $type = 'Retrait';
    }

    // 5. Calcul du dernier solde depuis la table 'transactions'
    $req_dernier_solde = "SELECT montant FROM transactions 
                          WHERE utilisateur_id = $id_utilisateur 
                          ORDER BY id DESC LIMIT 1";
    $res_solde = mysqli_query($connexion, $req_dernier_solde);

    if ($res_solde && mysqli_num_rows($res_solde) > 0) {
        $data_solde = mysqli_fetch_assoc($res_solde);
        $solde_actuel = floatval($data_solde['montant']);
    } else {
        $solde_actuel = 0.00;
    }

    // 6. Insertion de la transaction dans la table 'transactions'
    $req_insert = "INSERT INTO transactions (utilisateur_id, type_transaction, montant, statut, date_opération) 
                   VALUES ($id_utilisateur, '$type', $montant, '$statut', NOW())";

    $res_insert = mysqli_query($connexion, $req_insert);

    // 7. Contrôle et Redirection vers le Dashboard Users
    if (!$res_insert) {
        die("Erreur SQL lors de l'enregistrement : " . mysqli_error($connexion));
    } else {
        header("Location: Dashboard Users.php");
        exit();
    }

} else {
    header("Location: Formulaire_transaction.php");
    exit();
}
?>

