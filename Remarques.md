# Propositions / Améliorations

## Edition profil d'un utilisateur

Est-ce nécessaire d'envoyer un PDF lorsqu'on modifie les information d'un compte ? 

# Remarques après lecture du code

## Mercredi 7 Octobre 2020

* Champs "present" dans la table User. Pourquoi ? 


## Vendredi 9 Octobre 2020

* ✔️ Suppresion d'un User ne marche pas, revoir la fonction delete du UserController .php

* Problème avec le menu déroulant de la navBar
* Dans le UserController -> fonction edit: variable check déclarer vide mais pas utiliser
* Dans User.php, @UniqueConstraint devient @UniqueEntity

* L'mport de user ne marche pas 