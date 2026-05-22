<?php
require_once 'Database.php';

class Client {
    private $conn;
    private $table = "Client";

    private $idClient;
    private $nom;
    private $prenom;
    private $email;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function setIdClient($idClient){
        $this->idClient= $idClient;
    }
    public function getIdClient() {
        return $this->idClient;
    }


    public function setNom($nom){
        $this->nom=$nom;
    }
    public function getNom(){
        return $this->nom;
    }


    public function setPrenom($prenom){
        $this->prenom=$prenom;
    }
    public function getPrenom(){
        return $this->prenom;
    }


    public function setEmail($email){
        $this->email=$email;
    }
    public function getEmail(){
        return $this->email;
    }

    


    public function create(){
        $sql= "INSERT INTO {$this->table} (nom, prenom, email) VALUES (:nom, :prenom, :email)";
        $stmt= $this->conn->prepare($sql);
        return $stmt->execute([
            'nom'=>$this->nom,
            'prenom'=>$this->prenom,
            'email'=>$this->email
        ]);
    }

    public function read(){
        $sql= "SELECT * FROM {$this->table}";
        $stmt= $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function update(){
        $sql= "UPDATE {$this->table} SET nom=:nom, prenom=:prenom, email=:email WHERE idClient=:idClient";
        $stmt= $this->conn->prepare($sql);
        return $stmt->execute([
            'idClient'=>$this->idClient,
            'nom'=>$this->nom,
            'prenom'=>$this->prenom,
            'email'=>$this->email
        ]);
    }  
}class Produit {
    private $conn;
    private $table = "produit";

    private $idProduit;
    private $nom;
    private $prix;
    private $stock;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function setIdProduit($idProduit){ $this->idProduit = $idProduit; }
    public function getIdProduit() { return $this->idProduit; }

    public function setNom($nom){ $this->nom = $nom; }
    public function getNom(){ return $this->nom; }

    public function setPrix($prix){ $this->prix = $prix; }
    public function getPrix(){ return $this->prix; }

    public function setStock($stock){ $this->stock = $stock; }
    public function getStock(){ return $this->stock; }

    public function create(){
        $sql = "INSERT INTO {$this->table} (nom, prix, stock) VALUES (:nom, :prix, :stock)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'nom'   => $this->nom,
            'prix'  => $this->prix,
            'stock' => $this->stock
        ]);
    }

    public function read(){
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function lire(){
        $sql = "SELECT * FROM {$this->table} WHERE idProduit = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $this->idProduit]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isAvailable($qte_demande){
        $sql = "SELECT stock FROM {$this->table} WHERE idProduit = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $this->idProduit]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($row && $row['stock'] >= $qte_demande);
    }

    public function updateStock($qte_vendu){
        $sql = "UPDATE {$this->table} SET stock = stock - :qte WHERE idProduit = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
           'qte' => $qte_vendu,
           'id'  => $this->idProduit
        ]);
    }

    public function update(){
        $sql = "UPDATE {$this->table} SET nom = :nom, prix = :prix, stock = :stock WHERE idProduit = :idProduit";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'idProduit' => $this->idProduit,
            'nom'       => $this->nom,
            'prix'      => $this->prix,
            'stock'     => $this->stock
        ]);
    }

    public function delete(){
        $sql = "DELETE FROM {$this->table} WHERE idProduit = :idProduit";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['idProduit' => $this->idProduit]);
    }
}

class Commande {
    private $conn;
    private $table = "Commande";

    private $idCmd;
    private $idClient;
    private $dateCommande;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function setIdCmd($idCmd)           { $this->idCmd = $idCmd; }
    public function getIdCmd()                 { return $this->idCmd; }

    public function setIdClient($idClient)     { $this->idClient = $idClient; }
    public function getIdClient()              { return $this->idClient; }

    public function setDateCommande($date)     { $this->dateCommande = $date; }
    public function getDateCommande()          { return $this->dateCommande; }

    // Créer une nouvelle commande, retourne le nouvel idCmd
    public function create() {
        $sql  = "INSERT INTO {$this->table} (idClient, dateCommande) VALUES (:idClient, :dateCommande)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'idClient'      => $this->idClient,
            'dateCommande'  => $this->dateCommande
        ]);
        return $this->conn->lastInsertId();
    }

    // Lire toutes les commandes avec le nom du client
    public function read() {
        $sql  = "SELECT c.*, cl.nom, cl.prenom 
                 FROM {$this->table} c
                 JOIN Client cl ON c.idClient = cl.idClient
                 ORDER BY c.dateCommande DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lire une seule commande
    public function lire() {
        $sql  = "SELECT c.*, cl.nom, cl.prenom 
                 FROM {$this->table} c
                 JOIN Client cl ON c.idClient = cl.idClient
                 WHERE c.idCmd = :idCmd";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['idCmd' => $this->idCmd]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Calculer le total d'une commande
    public function calculerTotal() {
        $sql  = "SELECT SUM(p.prix * lc.qte) AS total
                 FROM LigneCommande lc
                 JOIN Produit p ON lc.idProduit = p.idProduit
                 WHERE lc.idCmd = :idCmd";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['idCmd' => $this->idCmd]);
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    public function delete() {
        $sql  = "DELETE FROM {$this->table} WHERE idCmd = :idCmd";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['idCmd' => $this->idCmd]);
    }
}
class CommandeProduit {
    private $conn;
    private $table = "LigneCommande";

    private $idLigne;
    private $idCmd;
    private $idProduit;
    private $qte;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function setIdLigne($idLigne)     { $this->idLigne = $idLigne; }
    public function getIdLigne()             { return $this->idLigne; }

    public function setIdCmd($idCmd)         { $this->idCmd = $idCmd; }
    public function getIdCmd()               { return $this->idCmd; }

    public function setIdProduit($idProduit) { $this->idProduit = $idProduit; }
    public function getIdProduit()           { return $this->idProduit; }

    public function setQte($qte)             { $this->qte = $qte; }
    public function getQte()                 { return $this->qte; }

    // Ajouter une ligne + décrémenter le stock
    public function create() {
        // Vérifier stock disponible
        $produit = new Produit($this->conn);
        $produit->setIdProduit($this->idProduit);

        if (!$produit->isAvailable($this->qte)) {
            return false; // Rupture de stock
        }

        // Insérer la ligne de commande
        $sql  = "INSERT INTO {$this->table} (idCmd, idProduit, qte) 
                 VALUES (:idCmd, :idProduit, :qte)";
        $stmt = $this->conn->prepare($sql);
        $ok   = $stmt->execute([
            'idCmd'     => $this->idCmd,
            'idProduit' => $this->idProduit,
            'qte'       => $this->qte
        ]);

        // Décrémenter le stock si insertion réussie
        if ($ok) {
            $produit->updateStock($this->qte);
        }

        return $ok;
    }

    // Lire les lignes d'une commande avec détails produit
    public function readByCommande() {
        $sql  = "SELECT lc.*, p.nom, p.prix, (p.prix * lc.qte) AS sous_total
                 FROM {$this->table} lc
                 JOIN Produit p ON lc.idProduit = p.idProduit
                 WHERE lc.idCmd = :idCmd";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['idCmd' => $this->idCmd]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete() {
        $sql  = "DELETE FROM {$this->table} WHERE idLigne = :idLigne";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['idLigne' => $this->idLigne]);
    }
}

?>