<?php

function genererSousTableauxBinairesOptimise($lettres, $jsonFilePath) {
    $n = count($lettres);
    $seuil = floor($n / 2);

    // Charger le fichier JSON
    $jsonData = json_decode(file_get_contents($jsonFilePath), true);

    if (!isset($jsonData['combinaisons'])) {
        $jsonData['combinaisons'] = [];
    }

    // Parcourir uniquement les combinaisons pertinentes
    for ($i = 0; $i < (1 << $n); $i++) { // Utilisation de bitwise pour éviter `pow(2, $n)`
        $somme = 0;

        // Compter les bits à 1 directement
        for ($j = 0; $j < $n; $j++) {
            if ($i & (1 << $j)) {
                $somme++;
            }
        }

        // Si le seuil est respecté, générer la combinaison
        if ($somme >= $seuil) {
            $tailleTableau = $n; // La taille est toujours le nombre de lettres (fixe ici)
            $valEntiere = intval($i);

            // Trouver ou créer une entrée pour la taille actuelle
            $tailleKey = strval($tailleTableau);
            $found = false;

            foreach ($jsonData['combinaisons'] as &$combinaison) {
                if (isset($combinaison[$tailleKey])) {
                    // Ajouter la valeur entière à l'entrée existante
                    if (!in_array($valEntiere, $combinaison[$tailleKey])) { // Éviter les doublons
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

// Exemple d'utilisation
$lettres = range('A', 'J'); // Génère les lettres A à J
$jsonFilePath = 'combinaisons.json'; // Chemin vers le fichier JSON

// Assurez-vous que le fichier JSON existe avant d'exécuter la fonction
if (!file_exists($jsonFilePath)) {
    file_put_contents($jsonFilePath, json_encode(["combinaisons" => []], JSON_PRETTY_PRINT));
}

genererSousTableauxBinairesOptimise($lettres, $jsonFilePath);
