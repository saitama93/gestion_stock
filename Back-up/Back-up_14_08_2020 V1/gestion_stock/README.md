# Application gestion de stock

##Lancer le serveur
Pour lancer le serveur symfony il faut effectuer la commande suivante dans un terminal :
* symfony server:start

Pour arrêter le serveur, il faut effectuer la commande : 
* symfony server:stop
* ou faire un Ctrl+C dans le terminal

## Gestion des mails
Les addresses d'envoies de mail se trouve dans Controller/ :
* InterventionController.php : 
    * Ligne 450
    * Ligne 578
    * Ligne 654
    * Ligne 757
* UserController.php : 
    * Ligne 94

## Modification dans le php.ini

Penser a activer les extensions suivantes dans le php.ini :
* mbstring
* pdo_mysql
* openssl
* curl
* gd2

Dans [ mail function ] vous pouvez changer le mail de l'envoyeur si jamais les changements précédent ne marche pas.

## Base de donnée
Le script SQL du site est inclut (script.sql à la source).
La base de donnée peut être importé dans le dossier .env dans URL_DATABASE.

## Utilisateurs de base
* Compte Admin :
    * ROOT :
        * username : root
        * Mot de passe : rootroot1234
* Compte Public :
    * public :
        * username : public
        * Mot de passe : testtest1234*
##Documentation
* Framework utilisé : Symfony
* Base de donnée : MySql
* Version de PHP utilisé : 7.3.7
* Documentation en ligne (en anglais) : https://symfony.com/doc/current/index.html
## Credits
Première version de l'application développé par Laine Lucas.