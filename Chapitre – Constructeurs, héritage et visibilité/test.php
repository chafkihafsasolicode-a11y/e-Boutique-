<?php
require 'BlogArticle.php';

$article = new BlogArticle("POO en PHP", "Découvrir l'héritage.", "Alice");
echo $article->afficher();

?>