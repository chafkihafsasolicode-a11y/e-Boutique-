<?php
require 'Database.php';
require 'class.php';

$database = new Database();
$conn = $database->getConnection();


if(isset($_POST['ajouter'])){
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    if(empty($nom) || empty($prenom) || empty($email)){
        echo "Tous les champs sont obligatoires";
    } else {
        $client = new Client($conn);
        $client->setNom($nom);
        $client->setPrenom($prenom);
        $client->setEmail($email);
        $client->create();
    }
}




$listes = new Client($conn);
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
    <a href="Produit.php">Produit</a>
    <a href="indexCom.php">Commande</a>
    <form method="post">
        <input type="text" name="nom" placeholder="Nom de client" required>
        <input type="text" name="prenom" placeholder="prenom" required>
        <input type="email" name="email" placeholder="email" required>
        <button type="submit" name="ajouter">Ajouter</button>
    </form>

    <br>
    <table border='1'>
        <tr>
            <th>Nom Client</th>
            <th>Prenom</th>
            <th>Email</th>
        </tr>
        <?php foreach ($afficher as $aff){?>
        <tr>
            <td><?= $aff['nom'] ?></td>
            <td><?= $aff['prenom'] ?> </td>
            <td><?= $aff['email'] ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>



