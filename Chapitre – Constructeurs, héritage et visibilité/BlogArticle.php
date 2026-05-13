<?php
require 'Article.php';

class BlogArticle extends Article {
    private $auteur;

    public function __construct($titre, $contenu, $auteur) {
        parent::__construct($titre, $contenu);
        $this->auteur = $auteur;
    }

    public function afficher() {
        return parent::afficher() . " - Auteur : " . $this->auteur;
    }
}

?>