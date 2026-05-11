<?php 
class Article {
    public $titre;
    public $contenu;


    public function afficher() {
        return " Titre : " . $this->titre . " - Contenu : " . $this->contenu;

     }
}


$article1 = new Article();
$article1->titre = "Introduction à PHP";
$article1->contenu = "PHP est un language de script coté serveur.";


echo $article1->afficher();




$article2 = new Article();
$article2->titre = "Programmation orientée objet";
$article2->contenu = "La POO facilite la modularité et la maintenance.";



echo "<br>" . $article2->afficher();