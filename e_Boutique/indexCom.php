<?php
require 'class.php';

$database = new Database();
$conn     = $database->getConnection();

$message = "";

if (isset($_POST['creer_commande'])) {
    $idClient = trim($_POST['idClient']); 

    if (empty($idClient)) {
        $message = "Veuillez sélectionner un client.";
    } else {
        $cmd = new Commande($conn);
        $cmd->setIdClient($idClient);
        $cmd->setDateCommande(date('Y-m-d'));
        $newId = $cmd->create();

        if ($newId) {
            $newId = preg_replace('/[\r\n\t]/', '', $newId);
            $_SESSION['idCmd'] = $newId; 
            $message = "Commande n°$newId créée avec succès ! Vous pouvez ajouter des produits.";
        } else {
            $message = "Erreur lors de la création de la commande.";
        }
    }
}

if (isset($_POST['addProduit'])) {
    if (!isset($_SESSION['idCmd'])) {
        $message = "Veuillez d'abord créer ou sélectionner une commande.";
    } else {
        $idCmd     = $_SESSION['idCmd'];
        $idProduit = $_POST['idproduit'];
        $qte       = trim($_POST['qte']);

        if (empty($idProduit) || empty($qte)) {
            $message = "Veuillez choisir un produit et une quantité.";
        } else {
            
            try {
                $query = "INSERT INTO lignecommande (idCmd, idProduit, qte) VALUES (:idCmd, :idProduit, :qte)";
                $stmt = $conn->prepare($query);
                
                $stmt->bindParam(':idCmd', $idCmd);
                $stmt->bindParam(':idProduit', $idProduit);
                $stmt->bindParam(':qte', $qte);
                
                if ($stmt->execute()) {
                    $message = "Produit ajouté avec succès à la commande n°$idCmd !";
                } else {
                    $message = "Erreur lors de l'ajout du produit.";
                }
            } catch (PDOException $e) {
                $message = "Erreur : " . $e->getMessage();
            }
        }
    }
}

if (isset($_POST['supprimer'])) {
    $id  = trim($_POST['idCmd']);
    $del = new Commande($conn);
    $del->setIdCmd($id);
    $del->delete();
    
    if (isset($_SESSION['idCmd']) && $_SESSION['idCmd'] == $id) {
        unset($_SESSION['idCmd']);
    }
    $message = "Commande supprimée.";
}

$clientObj = new Client($conn);
$clients   = $clientObj->read();

$cmdObj    = new Commande($conn);
$commandes = $cmdObj->read();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Commandes</title>
</head>
<body>
    <h2>Nouvelle Commande</h2>

    <?php if ($message){ ?>
        <p><?= $message ?></p>
    <?php }; ?>

    <form method="post">
        <label>Client :</label>
        <select name="idClient" required>
            <option value="">-- Sélectionner --</option>
            <?php foreach ($clients as $c): ?>
                <option value="<?= $c['idClient'] ?>">
                    <?= $c['prenom'] . ' ' . $c['nom'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="creer_commande">Créer la commande</button>
    </form>

    <?php if (isset($_SESSION['idCmd'])){ ?>
    <div class='Commande' style="margin-top: 20px; padding: 15px; border: 1px dashed #007bff; background: #f8f9fa;">
        <form method="post">
            <h2>Ajouter produit à la commande active n°[ <?= $_SESSION['idCmd'] ?> ]</h2>
            
            <select name="idproduit" required>
                <option value="">-Choisir un produit-</option>
                <?php
                    $produitsObj = new Produit($conn); 
                    $produit = $produitsObj->read();
                    foreach($produit as $pr){
                ?>
                <option value="<?=$pr['idProduit']?>"><?= $pr['nom'] ?></option>
                <?php } ?>
            </select>
            
            <input type="number" name="qte" placeholder="Quantité" >
            <button type="submit" name="addProduit">Ajouter le produit</button>
        </form>
    </div>
    <?php }; ?>

    <br>
    <h2>Liste des Commandes</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Client</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($commandes as $cmd){ ?>
            <tr>
                <td><?= $cmd['idCmd'] ?></td>
                <td><?= $cmd['prenom'] . ' ' . $cmd['nom'] ?></td>
                <td><?= $cmd['dateCommande'] ?></td>
                <td>
                    <a href="detail.php?idCmd=<?= $cmd['idCmd'] ?>">Détails</a>
                    &nbsp;|&nbsp;
                    <form method="post" style="display:inline">
                        <input type="hidden" name="idCmd" value="<?= $cmd['idCmd'] ?>">
                        <button type="submit" name="supprimer" onclick="return confirm('Supprimer cette commande ?')">
                            Supprimer
                        </button>
                    </form>
                </td>
            </tr>
        <?php }; ?>
    </table>
</body>
</html>