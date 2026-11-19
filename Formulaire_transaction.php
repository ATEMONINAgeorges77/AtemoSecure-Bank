<!DOCTYPE html>
<html lang="fr"> <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRANSACTIONS</title>
    <link rel="stylesheet" href="transaction.css">
</head>
<body>

    <form method="POST" action="traitement_transaction.php">
        <input type="hidden" name="utilisateur_id" value="<?php echo isset($_SESSION['utilisateur_id']) ? $_SESSION['utilisateur_id'] : 1;?>">

        <div class="form-group">
            <label for="operation">Type D'opération :</label>
            <select name="type_transaction" id="operation" required>
                <option value="transfert">Transfert</option> <option value="retrait">Retrait</option>
            </select>
        </div>

        <div class="form-group">
            <label for="montant">Montant :</label>
            <input type="number" name="montant" min="4000" id="montant">
        </div>

        <div class="form-group">
            <label for="email-client">Adresse E-mail:</E-mail></label>
            <input type="email" id="email_client" name="email_client" required>

        </div>

       <!-- <div id="solde-destinataire">
            <label for="destinataire">Solde Destinataire :</label>
            <input type="number" name="destinataire" id="destinataire" min="5000" required>
        </div> -->

        <div class="form-buttons">
            <input type="submit" value="Soumettre">
            <input type="reset" value="Annuler">
        </div>

    </form>

    <script>
        // 1. On cible les éléments HTML par leur ID exact
        const selectOperation = document.getElementById('operation');
        const blocDestinataire = document.getElementById('solde-destinataire');

        // 2. On écoute le changement d'option
        selectOperation.addEventListener('change', function() {
            if (selectOperation.value === 'retrait') {
                blocDestinataire.style.display = 'none';   /* Cache le bloc si retrait */
            } else {
                blocDestinataire.style.display = 'block';  /* Réaffiche le bloc sinon (transfert) */
            }
        });
    </script>

</body>
</html>