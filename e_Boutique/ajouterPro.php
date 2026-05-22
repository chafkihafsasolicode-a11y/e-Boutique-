<?php
session_start();
require_once 'Database.php';
require 'class.php';

$database = new Database();
$conn     = $database->getConnection();

$message = "";
$idCmd   = $_GET['idCmd'] ?? null;

if (!$idCmd) {
    header("Location: commande.php");
    exit;
}

if (isset($_POST['ajouter_ligne'])) {
    $idProduit = $_POST['idProduit'];
    $qte       = (int)$_POST['qte'];

    if ($qte <= 0) {
        $message = "La quantité doit être supérieure à 0.";
    } else {
        $ligne = new CommandeProduit($conn);
        $ligne->setIdCmd($idCmd);
        $ligne->setIdProduit($idProduit);
        $ligne->setQte($qte);

        if ($ligne->create()) {
            $message = "Produit ajouté avec succès.";
        } else {
            $message = "Erreur : stock insuffisant pour ce produit.";
        }
    }
}


$produitObj = new Produit($conn);
$produits   = array_filter(
    $produitObj->read(),
    fn($p) => $p['stock'] > 0
);

$ligneObj = new CommandeProduit($conn);
$ligneObj->setIdCmd($idCmd);
$lignes   = $ligneObj->readByCommande();

$cmdObj = new Commande($conn);
$cmdObj->setIdCmd($idCmd);
$total  = $cmdObj->calculerTotal();
$infos  = $cmdObj->lire();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter produits — Commande #<?= $idCmd ?></title>
</head>
<body>
    <h2>Commande #<?= $idCmd ?>
        — <?= $infos['prenom'] . ' ' . $infos['nom'] ?>
        (<?= $infos['dateCommande'] ?>)
    </h2>

    <?php if ($message){ ?>
    <p><?= $message ?></p>
      <?php }; ?>

    
    <form method="post">
        <label>Produit :</label>
        <select name="idProduit">
            <option value="">-- Sélectionner --</option>
            <?php foreach ($produits as $p){?>
                <option value="<?= $p['idProduit'] ?>">
                    <?= $p['nom'] ?> — <?= $p['prix'] ?> DH
                    (stock: <?= $p['stock'] ?>)
                </option>
            <?php }; ?>
        </select>
        <label>Quantité :</label>
        <input type="number" name="qte"  value="1">
        <button type="submit" name="ajouter_ligne">Ajouter</button>
    </form>

    <br>
    
    <h3>Produits dans cette commande</h3>
    <?php if (empty($lignes)){ ?>
        <p>Aucun produit ajouté pour l'instant.</p>
    <?php }else{ ?>
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
                <td >Total</td>
                <td><?= number_format($total, 2) ?> DH</td>
            </tr>
        </table>
    <?php }; ?>

    <br>
    <a href="commande.php">← Terminer et revenir aux commandes</a>
</body>
</html>