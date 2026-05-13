<?php
class Article {
    protected $titre;
    protected $contenu;

    public function __construct($titre, $contenu) {
        $this->titre = $titre;
        $this->contenu = $contenu;
    }


    public function afficher() {
        return "Titre : " . $this->titre . " - Contenu : " . $this->contenu;
    }
}



?>
