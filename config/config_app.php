<?php

// Connexion à la base de données (logins wamp)
define('DB_HOST', ''); // adresse de votre base de données
define('DB_NAME', ''); // nom de votre base de données
define('DB_USER', ''); // identifiant de votre base de données
define('DB_PASS', ''); // mot de passe de votre base de données

// Préfixe des tables de la base de données (facultatif si vous n'en avez pas besoin)
define('PREFIXE_TABLE', '');

// Constante de la partie vue
define('WEBSITE_TITLE', 'TimeHarmony');
define('WEBSITE_VERSION', '1');
define('WEBSITE_DESCRIPTION', 'Une application web qui permet de trouver des créneaux de rendez-vous facilement');
define('WEBSITE_LANGUAGE', 'fr');

// Constantes connexion
define('MAX_CONNEXION_ECHOUEES', 3);
define('DELAI_ATTENTE_CONNEXION', 10*60); // 10 minutes