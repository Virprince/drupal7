# Trucs et astuces pour DRUPAL 7

*Override du template souvent utilisé (fichier template-override-utile.php)*
Plusieurs override ici 

## BLOCK RENDER
Pouvoir rendre un block directement dans un fichier tpl.php sans avoir à créer une région. Le block sera dans la liste des inactifs pour la configuration.

## Pouvoir utiliser un template de page en fonction d'un type de node.
Par exemple pour un type de node appelé fiche_produit, pouvoir créer un fichier page--fiche_produit.tpl.php

## Pouvoir rendre un field hors du node auquel il appartient dans une page.
Rien de plus à ajouter, si pour une raison x ou y le field doit être rendu ailleur que dans la partie node, cet override fonctionne.

## Ajouter une class CSS en fonction de la taxonomie.
Cet Override ajoute une classe au body de la page en fonction des termes de taxonomie appliqué. 

## Ajouter une class au ul d'un menu.
La première partie permet d'ajouter une class à tout les ul d'un menu drupal. 
La seconde partie permet de différencier le premier niveau ex: `<ul class="menu"> </ul>` 
des enfants ex: `<ul class="sous-menu"> </ul>` .

## Modifier les caractéristiques du Search-block-form
Plusieurs options:
* changer le texte du label
* définir la taille du textfield
* changer le texte de submit
* ajouter une image pour le button
* ajouter un placeholder
* etc...
