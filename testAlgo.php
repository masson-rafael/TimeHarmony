<?php

function genererSousTableauxBinairesOptimise($lettres, $tailleFixe = null, $seuil = 2) {
    $n = count($lettres);
    $seuil = floor($n / $seuil);

    // Parcourir uniquement les combinaisons pertinentes
    for ($i = 0; $i < (1 << $n); $i++) {
        $somme = 0;
        $sousTableau = [];

        // Compter les bits à 1 et construire le sous-tableau en une seule passe
        for ($j = 0; $j < $n; $j++) {
            if ($i & (1 << $j)) {
                $somme++;
                $sousTableau[$j] = 1;
            } else {
                $sousTableau[$j] = 0;
            }
        }

        // Vérifier si la combinaison respecte la tailleFixe ou le seuil
        if (($tailleFixe !== null && $somme == $tailleFixe) || ($tailleFixe === null && $somme >= $seuil)) {
            yield $sousTableau;
        }
    }
}

// Exemple d'utilisation
$lettres = range('A', 'T'); // Génère les lettres A à T - taille 20
$tailleFixe = 18; // Taille des combinaisons souhaitée

$resultat = [];
$indexCombinaison = 0;

foreach (genererSousTableauxBinairesOptimise($lettres, $tailleFixe) as $sousTableau) {
    $lettresAssociees = [];
    foreach ($sousTableau as $key => $value) {
        if ($value === 1) {
            $lettresAssociees[] = $lettres[$key];
        }
    }
    $resultat[] = [
        'combinaison' => $indexCombinaison++,
        'lettres' => $lettresAssociees
    ];
}

// Affichage des résultats
foreach ($resultat as $combinaison) {
    echo "Combinaison {$combinaison['combinaison']} : (" . implode(", ", $combinaison['lettres']) . ")\n<br>";
}