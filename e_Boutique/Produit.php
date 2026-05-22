<?php
require_once 'Database.php';
require 'class.php';
$database = new Database();
$conn = $database->getConnection();


if(isset($_POST['ajouter'])){
    $nom = $_POST['nom'];
    $prix = $_POST['prix'];
    $stock = $_POST['stock'];
    if(empty($nom) || empty($prix) || empty($stock)){
        echo "Tous les champs sont obligatoires";
    } else {
        $produit = new Produit($conn);
        $produit->setNom($nom);
        $produit->setPrix($prix);
        $produit->setStock($stock);
        $produit->create();
    }
}

if(isset($_POST['update'])){
    $id = $_POST['idPrd'];
    header("Location: modifier.php?id=$id");
    exit;
}

if(isset($_POST['supprimer'])){
    $id = $_POST['idPrd'];
    $deletes = new Produit($conn);
    $deletes->setIdProduit($id);
    $deletes->delete();
}

$listes = new Produit($conn);
$afficher = $listes->read();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <a href="Client.php">   Client</a>
    <a href="indexCom.php">Commande</a>
    <form method="post">
        <input type="text" name="nom" placeholder="Nom du produit" required>
        <input type="number" name="prix" placeholder="Prix (DH)" required>
        <input type="number" name="stock" placeholder="Quantité en stock" required>
        <button type="submit" name="ajouter">Ajouter</button>
    </form>

    <br>
    <table border='1'>
        <tr>
            <th>Nom Produit</th>
            <th>Prix</th>
            <th>Stock</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($afficher as $aff){?>
        <tr>
            <td><?= $aff['nom'] ?></td>
            <td><?= $aff['prix'] ?> DH</td>
            <td><?= $aff['stock'] ?></td>
            <td>
                <form method='post'>
                    <input type='hidden' name='idPrd' value='<?= $aff['idProduit'] ?>'>
                    <button name='update'>Modifier</button>
                    <button name='supprimer' >Supprimer</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>