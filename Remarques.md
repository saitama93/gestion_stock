# Propositions / Améliorations

* Réussir à fusionner l'envoie de mail et la conversion en fichier PDF des informations de l'utilisateur

## Edition profil d'un utilisateur

Est-ce nécessaire d'envoyer un PDF lorsqu'on modifie les informations d'un compte ? 

# Remarques après lecture du code

## Mercredi 7 Octobre 2020 

* Champs "present" dans la table User. Pourquoi ? 

## Jeudi 8 Octobre 2020

* Début de factorisation du UserController.php


## Vendredi 9 Octobre 2020

* ✔️ Suppresion d'un User ne marche pas, revoir la fonction delete du UserController .php

* Problème avec le menu déroulant de la navBar
* NavBar pas centraliser, maintenabilité de celle-ci difficile car répétition de balise nav dans         différents fichier
* Dans le UserController -> fonction edit: variable check déclarer vide mais pas utiliser
* Dans User.php, @UniqueConstraint devient @UniqueEntity

* L'mport de user ne marche pas 
* Fonction goBack ne marche pas
* Factorisier le ParamatreController de façon à avoir un Controller par Entité
* Disparition du générateur de code barre lors de la création d'un compte utilisateur

## Lundi 12 Ocotbre 2020

* ✔️ Création des FormType
* ✔️ Création des Repository
* ✔️ Factorisation du UserController.php
* ✔️ Création des services: 
    * CsvService.php
    * GeneratePdfService
    * MailerService.php


## Mardi 13 Octobre 2020

* Centralisation de la NavBar de l'application