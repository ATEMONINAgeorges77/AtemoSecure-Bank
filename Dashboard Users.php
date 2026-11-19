<?php
session_start(); 
include 'db.php';

if (!isset($_SESSION['user_id'])){
    header("Location: ../Main Page/Main Page.html.php");
    exit();
}
$id_utilisateur= intval($_SESSION['user_id']);

// 1. RÉCUPÉRATION DE L'UTILISATEUR
$requete_user = "SELECT * FROM utilisateurs WHERE id = $id_utilisateur";
$resultat_user = mysqli_query($connexion, $requete_user);
$user = mysqli_fetch_assoc($resultat_user);


$prenom = $user['prenom'];

// 2. CALCUL DU TOTAL DES TRANSFERTS
$req_transferts = "SELECT SUM(montant) AS total FROM transactions WHERE utilisateur_id = $id_utilisateur AND type_transaction = 'Transfer'";
$res_transferts = mysqli_query($connexion, $req_transferts);
$data_transferts = mysqli_fetch_assoc($res_transferts);
$total_transferts = $data_transferts['total'] ? $data_transferts['total'] : 0;

// 3. CALCUL DU TOTAL DES RETRAITS
$req_retraits = "SELECT SUM(montant) AS total FROM transactions WHERE utilisateur_id = $id_utilisateur AND type_transaction = 'Retrait'";
$res_retraits = mysqli_query($connexion, $req_retraits);
$data_retraits = mysqli_fetch_assoc($res_retraits);
$total_retraits = $data_retraits['total'] ? $data_retraits['total'] : 0;
$solde_depart= isset($user['montant_initial']) ? floatval($user['montant_initial']) : 5000;
$solde= $solde_depart+$total_transferts-$total_retraits;

// 4. RÉCUPÉRATION DE L'HISTORIQUE DES OPERATIONS
$req_historique = "SELECT * FROM transactions WHERE utilisateur_id = $id_utilisateur ORDER BY date_opération DESC";
$historique = mysqli_query($connexion, $req_historique);
if (!$historique) {
    echo '<div style="color: red; background: white; padding: 10px; font-weight: bold;">
    Erreur SQL Historique: ' . mysqli_error($connexion) .'
    </div>';
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mon Espace</title>
    <link rel="stylesheet" href="Dashboard User.css">
</head>
<body>

    <div class="dashboard-container">
        
        <header class="dashboard-header">
            <h1>Mon Espace Bancaire</h1>
            <div class="user-info">
                <span>Bienvenue, <strong><?php echo $prenom; ?></strong></span>
                <a href="deconnexion.php" class="logout-btn">Déconnexion</a>
            </div>
        </header>

        <section class="summary-cards">
            <div class="card solde">
                <h3>Solde Disponible</h3>
                <p class="balance-amount"><?php echo isset($solde)? number_format($solde, 0, ', ', ''): '250000';?> FC</p>
            </div>
            <div class="card transferts">
                <h3>Total Transferts</h3>
                <p class="amount"><?php echo number_format($total_transferts, 0, ',', '');?> FC</p>
            </div>
            <div class="card retraits">
                <h3>Total Retraits</h3>
                <p class="amount"> <?php echo number_format($total_retraits, 0, ',', ' '); ?> FC</p>
            </div>
        </section>

        <section class="transactions-section">
            <h2>Historique des opérations</h2>
            <p style="color: yellow; background: #333; padding: 5px; text-align: center;">
                ID utilisateur actif: <?php echo $id_utilisateur; ?>
            </p>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Montant</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                   <tbody>
    <?php if ($historique && mysqli_num_rows($historique) > 0): ?>
        <?php while($cle = mysqli_fetch_assoc($historique)): ?>
            <tr>
                <td><?php echo date('d/m/Y', strtotime($cle['date_opération'])); ?></td>
                <td>
                    <span class="badge badge-<?php echo ($cle['type_transaction'] == 'Transfert') ? 'transfert' : 'retrait'; ?>">
                        <?php echo $cle['type_transaction']; ?>
                    </span>
                </td>
                <td class="text-red">
                    - <?php echo number_format($cle['montant'], 0, ',', ' '); ?> FCFA
                </td>
                <td>
                    <span class="status success"><?php echo $cle['statut']; ?></span>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="3" style="text-align: center; padding: 20px;">Aucune opération effectuée.</td>
        </tr>
    <?php endif; ?>
</tbody>
                </table>
            </div>
        </section>

        <div class="dashboard-actions">
            <a href="Formulaire_transaction.php" class="action-btn">Faire une nouvelle opération</a>
        </div>

    </div>

</body>
</html>