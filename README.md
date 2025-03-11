# ⌚ TimeHarmony

## 📄 A propos
TimeHarmony est un projet de développement d'application web. L'application a pour but de trouver un créneau libre commun à un groupe de personnes; que ce soit pour des rendez-vous professionnels ou pour faire une sortie entre amis. Pour ce faire, elle compare les agendas de toutes les personnes participantes dans le but de trouver les créneaux libres et les en informer.

Le code de notre application est organisé selon le patron de conception MVC.

## 📁 Arborescence
A la racine du dépôt se trouvent la page index.php qui va être déservie à chaque fois, include.php qui permet de centraliser le code, ce fichier readme, des json de librairies, un fichier sql contenant l'export de notre base de donnée, un fichier Doxyfile permettant de générer la documentation, ainsi qu'un fichier .gitignore, servant à exclure les données sensibles du dépôt.

On retrouve également 9 dossiers :
  - config/ -> contient les constantes et l'initialisation de twig
  - controllers/ -> contient les controlleurs de l'application
  - css/ -> contient les feuilles de style css
  - docs/ -> contient la documentation de l'application générée par doxygen 
#### Vous pouvez la retrouver sur cette page : https://masson-rafael.github.io/TimeHarmony/index.html
  - image/ -> contient les images
  - js/ -> contient les scripts JavaScript
  - modeles/ -> contient les modeles de l'application
  - scss/ -> contient le scss, pour modifier la librairie bootstrap
  - templates/ -> contient les vues de l'application, les pages twig

## 💻 Technologies

[![My Skills](https://skillicons.dev/icons?i=php,html,css,js,bootstrap,git,mysql)](https://skillicons.dev)

PHP / HTML / CSS / JavaScript / Bootstrap / Git / MySQL / Twig / Sass

## 🔧 Configuration

Pour installer TimeHarmony sur un serveur, il faut :
  - Cloner le dépôt du projet sur un serveur **Apache** doté d'une version PHP >= 8.0.20 ```git clone https://github.com/masson-rafael/TimeHarmony```
  - Importer la base de donnée sur un serveur **MySQL** (fichier timeharmony.sql)
  - Modifier l'extention de config_app *.txt* -> *.php* dans le dossier config/
  - Mettre les logins de votre base de données aux endroits indiqués dans ce fichier config_app.php
  - Installer les libraires que l'application utilise avec les commandes suivantes :
    - Composer pour Twig https://twig.symfony.com/ , ics-parser https://github.com/u01jmg3/ics-parser , phpmailer https://github.com/PHPMailer/PHPMailer et sabre/vobject https://sabre.io/vobject/ ```composer install```
    - Npm pour Bootstrap https://getbootstrap.com/ et Sass https://sass-lang.com/ ```npm install``` (attention si vous n'avez pas Npm il vous faudra d'abord installer node.js)
  - Vous pouvez maintenant travailler ! 🎆
