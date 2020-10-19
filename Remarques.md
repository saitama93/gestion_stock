# Propositions / Améliorations

* Mise en place d'une page dédié au statistique
* View "plus de détails" pour afficher plus de détail lier à un matériel



# Tâches à faire

* Réussir à fusionner l'envoie de mail et la conversion en fichier PDF des informations de l'utilisateur
* Sécuriser la page de création de compte @isGranted('ROLE_ADMIN')
* Message/fenêtre de confirmation lors de la suppression 
* Mise en place d'une pagination pour l'administration
* Mettre en place un margin botton et top aux boutons ajouter et supprimer de chaque élément des listes comme avec les spécificités (voir view /admin/specificite/list)
* Petit menu en haut de chaque liste bug 
* Rajouter favicon
* Faire une redirection après connection en fonction du rôle de l'utilisateur
* S'occuper du champ "present" de la table User
* S'occuper du réglage fuseau horraire des champs date


# Questions

* Suppréssion d'un matériel => 
* Modification d'un matériel => 
* Champ "nom" de la table Materiel incompris => Dernier user manip
* Fonctionnalité de mot clé => champs mot clé avec recherche LIKE % % 
* Onglet Archive => Onglet "archive" <=> "gestion

## Edition profil d'un utilisateur

Est-ce nécessaire d'envoyer un PDF lorsqu'on modifie les informations d'un compte ? 

# Remarques après lecture du code

* Champs "present" dans la table User. Pourquoi ?
* Pour les interventions, plusieurs cas possible:

    * Départ en intervention avec du matériel (intervention classique)
    * Départ en intervention sans du matériel
    * Retour d'intervention avec le matériel prit lors du départ
    * Retour d'intervention sans le matériel prit lors du départ
    * Retour d'intervention avec matériel inconnu lors du départ


# Avancement projet

## Jeudi 8 Octobre 2020

* Début de factorisation du UserController.php


## Vendredi 9 Octobre 2020
php
 ✔️ Suppresion d'un User ne marche pas, revoir la fonction delete du UserController .php

* ✔️ Problème avec le menu déroulant de la navBar
* ✔️ NavBar pas centraliser, maintenabilité de celle-ci difficile car répétition de balise nav dans         différents fichier
* ❌ Dans le UserController -> fonction edit: variable check déclarer vide mais pas utiliser
* ✔️ Dans User.php, @UniqueConstraint devient @UniqueEntity

* L'mport de user ne marche pas 
* Fonction goBack ne marche pas
* Factorisier le ParamatreController de façon à avoir un Controller par Entité
* Disparition du générateur de code barre lors de la création d'un compte utilisateur

## Lundi 12 Octobre 2020

* ✔️ Création des FormType
* ✔️ Création des Repository
* ✔️ Factorisation du UserController.php
* ✔️ Création des services: 
    * CsvService.php
    * GeneratePdfService
    * MailerService.php


## Mardi 13 Octobre 2020

* ✔️ Centralisation de la NavBar de l'application
* ✔️ Séparation de la gestion des entité => Chaque entité aura sa propre page avec un système de       pagination
* ✔️ Administration des utilisateurs
* ✔️ Controller AdminUserController.php créé
* ✔️ Mise en place administration des lieu
* ✔️ Ajout/edition/suppression d'une marque


## Mercredi 14 Octobre 2020

* ✔️ Ajout/edition/suppression d'un lieu
* ✔️ AdminLieuController
* ✔️ Ajout/edition/suppression d'un lieu
* ✔️ AdminStatutController
* ✔️ Mise en place du model ApplicationType
* ✔️ Sécurisation des routes commençant par '/admin'
* ✔️ Ajout/edition/suppression d'un specificité
* ✔️ Ajout/edition/suppression d'un type


## Jeudi 15 Octobre 2020

* ✔️ Doublon de Statut/Lieu/Marque/Spécificité/Type possible pour le moment 
* ✔️ Mise en place du system de pagination et du PaginationService.php 
* ✔️ Mise en place des messages pour les Exceptions du PaginationService.php  
* ✔️ Factoriser le code twig pour la pagination
* ✔️ Type du champ date de la table Materiel changé en datetime


## Vendredi 16 Octobre 2020

* ❌ Création et mise en place du CRUD de l'entité Materiel
    * ✔️ Ajout d'un matériel
    * ✔️ Suppréssion d'un matériel
    * ❌ Mise à jour d'un matériel

* ✔️ Champ "supprimer" => présence du matériel


## Lundi 19 Octobre 2020

* ✔️ Création d'une view pour les archives
* ✔️ Ajout/edition/suppression d'un materiel
* ❌ Filter les user pour avoir que les user présent
* ❌ Fonctionnalité de mot clé


## Crédits

Première version de l'application développé par Laine Lucas.
Projet reprit par ILMI AMIR Igal