<?php

function genererSousTableauxBinairesOptimise($lettres) {
    $n = count($lettres);
    $seuil = floor($n / 2);

    // Parcourir uniquement les combinaisons pertinentes
    for ($i = 0; $i < (1 << $n); $i++) { // Utilisation de bitwise pour éviter `pow(2, $n)`
        // Compter les bits à 1 directement (plus rapide que `array_sum`)
        $somme = 0;
        for ($j = 0; $j < $n; $j++) {
            if ($i & (1 << $j)) { // Teste si le bit $j est à 1
                $somme++;
            }
        }

        // Si le seuil est respecté, générer la combinaison
        if ($somme >= $seuil) {
            $sousTableau = [];
            for ($j = 0; $j < $n; $j++) {
                $sousTableau[] = ($i & (1 << $j)) ? 1 : 0;
            }
            yield $sousTableau; // Génération paresseuse
            var_dump(intval($i));
        }
    }
}

// Exemple d'utilisation
$lettres = range('A', 'F'); // Génère les lettres A à T
foreach (genererSousTableauxBinairesOptimise($lettres) as $index => $sousTableau) {
    $lettresAssociees = [];
    foreach ($sousTableau as $key => $value) {
        if ($value === 1) {
            $lettresAssociees[] = $lettres[$key];
        }
    }
    echo "Combinaison $index : (" . implode(", ", $lettresAssociees) . ")\n<br>";
}
