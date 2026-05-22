<?php
require_once 'Produit.php'; 

$database = new Database();
$conn = $database->getConnection();

if(isset($_GET['id'])){
    $Id = $_GET['id'];
    $modifier = new Produit($conn);
    $modifier->setIdProduit($Id);
    $afficher = $modifier->lire();
}

if(isset($_POST['modifier'])){ 
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $prix = $_POST['prix'];
    $stock = $_POST['stock'];

    $update = new Produit($conn);
    $update->setIdProduit($id);
    $update->setNom($nom);
    $update->setPrix($prix);
    $update->setStock($stock);

    if($update->update()){
        unset($_SESSION['idProduit']); 
        header('Location: Produit.php');
        exit;
    } else {
        echo "Erreur de modification";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Produit</title>
</head>
<body>
    <h2>Modifier le produit</h2>
    <?php if($afficher){ ?>
    <form method="post">
        <input type="hidden" name="id" value="<?= $afficher['idProduit'] ?>">
        <input type="text" name="nom" value="<?= $afficher['nom'] ?>">
        <input type="number" name="prix" value="<?= $afficher['prix'] ?>">
        <input type="number" name="stock" value="<?= $afficher['stock'] ?>">
        <button type="submit" name="modifier">Enregistrer les modifications</button>
    </form>
    <?php }?>
</body>
</html>