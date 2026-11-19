<?php
include 'db.php';
$req_membres= "SELECT COUNT(*) AS total_users FROM utilisateurs";
$res_membres= mysqli_query($connexion,$req_membres) or die ("Erreur SQL : ".mysqli_error($connexion));
$data_membres= mysqli_fetch_assoc($res_membres);
$total_utilisateurs=$data_membres['total_users'];

$req_somme= "SELECT SUM(montant_initial) AS somme_solde FROM utilisateurs";
$res_somme= mysqli_query($connexion,$req_somme) or die ("Erreur SQL ligne 10: ". mysqli_error($connexion));
$data_somme= mysqli_fetch_assoc($res_somme);
$volume_total= isset($data_somme['somme_solde'])? $data_somme['somme_solde'] : 0;

$req_valides= "SELECT COUNT(*) AS total_valides FROM transactions WHERE statut= 'conforme' OR statut= 'valide' OR statut IS NULL OR statut= ''";
$res_valides= mysqli_query($connexion,$req_valides) or die ("Erreur SQL Validées:". mysqli_error($connexion));
$data_valides= mysqli_fetch_assoc($res_valides);
$operations_validees= $data_valides['total_valides'];

$req_bloquees= "SELECT COUNT(*) AS total_bloquees FROM transactions WHERE statut= 'suspect' OR statut= 'Fraude'";
$res_bloquees= mysqli_query($connexion,$req_bloquees) or die ("Erreur SQL Bloquées: ".mysqli_error($connexion));
$data_bloquees= mysqli_fetch_assoc($res_bloquees);
$operations_bloquees= $data_bloquees['total_bloquees'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrateur - Premium Dark</title>
    <link rel="stylesheet" href="Admin.css">
</head>
<body>

    <div class="admin-container">
        
        <nav class="admin-sidebar">
            <div class="sidebar-logo">
                <h2>VUE<span></span></h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="#" class="active"><span class="menu-icon"></span> Vue générale</a></li>
            </ul>
        </nav>

        <main class="admin-main">
            
            <header class="main-header">
                <div>
                    <h1>Tableau de bord Admin</h1>
                    <p class="subtitle">Statistiques globales de la plateforme</p>
                </div>
                <div class="admin-profile">
                    <span class="badge-role">Super Admin</span>
                </div>
            </header>

            <section class="admin-stats">
                <div class="stat-box members">
                    <h3>Membres Inscrits</h3>
                    <p class="stat-number"><h3><?php echo $total_utilisateurs; ?> </h3></p>
                </div>
                <div class="stat-box volume">
                    <h3>Volume Total</h3>
                    <p class="stat-number"><h3><?php echo number_format($volume_total, 0, ',', ' '); ?> FC</h3></p>
                </div>
                <div class="stat-box validated">
                    <h3>Opérations Validées</h3>
                    <p class="stat-number"> <?php echo number_format($operations_validees, 0, ',', '')?></p>
                </div>
                <div class="stat-box blocked">
                    <h3>Opérations Bloquées</h3>
                    <p class="stat-number"><?php echo $operations_bloquees; ?></p>
                </div>
            </section>

                
    <?php
    // Requête SQL avec une sous-requête pour récupérer le statut de la toute dernière transaction de chaque utilisateur
    $req_liste = "SELECT u.*, 
                    (SELECT t.statut 
                     FROM transactions t 
                     WHERE utilisateur_id= u.id 
                     ORDER BY t.date_opération DESC LIMIT 1) AS dernier_statut
                  FROM utilisateurs u 
                  ORDER BY u.id DESC";
                  
    $res_liste = mysqli_query($connexion, $req_liste) or die("Erreur SQL Liste : " . mysqli_error($connexion));

    

    ?>



                        </tbody>
                    </table>
                </div>
            </section>

        </main>
    </div>

</body>
</html>