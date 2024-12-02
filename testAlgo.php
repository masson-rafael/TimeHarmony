<?php

function genererSousTableauxBinairesOptimise($lettre, $jsonFilePath) {
    $lettres = [$lettre];
    $n = count($lettres);
    $seuil = floor($n / 2);

    // Charger le fichier JSON
    $jsonData = json_decode(file_get_contents($jsonFilePath), true);

    if (!isset($jsonData['combinaisons'])) {
        $jsonData['combinaisons'] = [];
    }

    // Parcourir uniquement les combinaisons pertinentes
    for ($i = 0; $i < (1 << $n); $i++) { 
        $somme = 0;

        // Compter les bits à 1 directement
        for ($j = 0; $j < $n; $j++) {
            if ($i & (1 << $j)) {
                $somme++;
            }
        }

        // Si le seuil est respecté, générer la combinaison
        if ($somme >= $seuil) {
            $tailleTableau = $n;
            $valEntiere = intval($i);

            // Trouver ou créer une entrée pour la taille actuelle
            $tailleKey = strval($tailleTableau);
            $found = false;

            foreach ($jsonData['combinaisons'] as &$combinaison) {
                if (isset($combinaison[$tailleKey])) {
                    // Ajouter la valeur entière à l'entrée existante
                    if (!in_array($valEntiere, $combinaison[$tailleKey])) { 
                        $combinaison[$tailleKey][] = $valEntiere;
                    }
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                // Ajouter une nouvelle entrée
                $jsonData['combinaisons'][] = [
                    $tailleKey => [$valEntiere]
                ];
            }
        }
    }

    // Sauvegarder les modifications dans le fichier JSON
    file_put_contents($jsonFilePath, json_encode($jsonData, JSON_PRETTY_PRINT));
}

// Fonction principale pour traiter une lettre unique
function genererPourUneLettreUnique($lettre) {
    $jsonFilePath = 'combinaisons.json'; 

    // Assurez-vous que le fichier JSON existe avant de commencer
    if (!file_exists($jsonFilePath)) {
        file_put_contents($jsonFilePath, json_encode(["combinaisons" => []], JSON_PRETTY_PRINT));
    }

    // Traiter uniquement la lettre passée en paramètre
    genererSousTableauxBinairesOptimise($lettre, $jsonFilePath);
}

// Exemple d'utilisation : traiter la lettre que l'on veut
genererPourUneLettreUnique('R');