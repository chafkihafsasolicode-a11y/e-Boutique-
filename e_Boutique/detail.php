<?php
require_once 'Database.php';
require 'class.php';
$database = new Database();
$conn     = $database->getConnection();

$idCmd = $_GET['idCmd'] ?? null;
if (!$idCmd) {
    header("Location: commande.php");
    exit;
}

$cmdObj = new Commande($conn);
$cmdObj->setIdCmd($idCmd);
$infos  = $cmdObj->lire();
$total  = $cmdObj->calculerTotal();

$ligneObj = new CommandeProduit($conn);
$ligneObj->setIdCmd($idCmd);
$lignes   = $ligneObj->readByCommande();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détail Commande #<?= $idCmd ?></title>
</head>
<body>
    <h2>Détail — Commande #<?= $idCmd ?></h2>
    <p>
        <strong>Client :</strong> <?= $infos['prenom'] . ' ' . $infos['nom'] ?><br>
        <strong>Date :</strong> <?= $infos['dateCommande'] ?>
    </p>

    <table border="1">
        <tr>
            <th>Produit</th>
            <th>Prix unitaire</th>
            <th>Quantité</th>
            <th>Sous-total</th>
        </tr>
        <?php foreach ($lignes as $l){ ?>
            <tr>
                <td><?= $l['nom'] ?></td>
                <td><?= $l['prix'] ?> DH</td>
                <td><?= $l['qte'] ?></td>
                <td><?= number_format($l['sous_total'], 2) ?> DH</td>
            </tr>
        <?php }; ?>
        <tr>
            <td>Total</td>
            <td><?= number_format($total, 2) ?> DH</td>
        </tr>
    </table>

    <br>
    <a href="indexCom.php">← Retour aux commandes</a>
</body>
</html>