<?php

class utilitaire {
    /**
     * Redimensionne une image
     *
     * @param string|null $img_src Chemin de l'image source
     * @param string|null $img_dest Chemin de l'image de destination
     * @param string|null $dst_w Largeur de l'image de destination
     * @param string|null $dst_h Hauteur de l'image de destination
     * @return string|null Chemin de l'image recadree
     */
    static function redimage(?string $img_src, ?string $img_dest, ?string $dst_w, ?string $dst_h): ?string
    {
        // Lit les dimensions de l'image
        $size = GetImageSize("$img_src");
        $src_w = $size[0];
        $src_h = $size[1];

        // Teste les dimensions tenant dans la zone
        $test_h = round(($dst_w / $src_w) * $src_h);
        $test_w = round(($dst_h / $src_h) * $src_w);

        // Crée une image vierge aux bonnes dimensions
        $dst_im = ImageCreateTrueColor($dst_w, $dst_h);

        // Copie l'image initiale redimensionnée
        $src_im = ImageCreateFromJpeg("$img_src");

        ImageCopyResampled($dst_im, $src_im, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);

        // Sauvegarde la nouvelle image
        ImageJpeg($dst_im, "$img_dest");

        // Détruis les tampons
        ImageDestroy($dst_im);
        ImageDestroy($src_im);

        // Return le chemin de l'image recadree
        return $img_dest;
    }

    /**
     * Valide le prenom de l'utilisateur s'il est valide, après série de vérifications
     *
     * @param string $prenom Le prénom de l'utilisateur
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra ajouter si erreur détectée
     * @return boolean Retourne vrai si le prénom est valide, faux sinon
     */
    static function validerPrenom(string $prenom, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ

        // 2. Type de données : vérifier que le prenom est une chaine de caractères

        // 3. Longueur de la chaine : vérifier que le prenom est compris entre 2 et 50 caractères

        // 4. Format des données : vérifier le format du prénom

        // 5. Plage des valeurs

        // 6. Fichiers uploadés

        return $valide;
    }

    /**
     * Valide le nom de l'utilisateur s'il est valide, après série de vérifications
     *
     * @param string $nom Le nom de l'utilisateur
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra ajouter si erreur détectée
     * @return boolean Retourne vrai si le nom est valide, faux sinon
     */
    static function validerNom(string $nom, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ

        // 2. Type de données : vérifier que le prenom est une chaine de caractères

        // 3. Longueur de la chaine : vérifier que le prenom est compris entre 2 et 50 caractères

        // 4. Format des données : vérifier le format du prénom

        // 5. Plage des valeurs

        // 6. Fichiers uploadés

        return $valide;
    }

    /**
     * Valide l'email s'il est valide, après série de vérifications
     *
     * @param string $email L'email de l'utilisateur
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra ajouter si erreur détectée
     * @return boolean Retourne vrai si l'email est valide, faux sinon
     */
    static function validerEmail(string $email, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ

        // 2. Type de données : vérifier que le prenom est une chaine de caractères

        // 3. Longueur de la chaine : vérifier que le prenom est compris entre 2 et 50 caractères

        // 4. Format des données : vérifier le format du prénom

        // 5. Plage des valeurs

        // 6. Fichiers uploadés

        return $valide;
    }

    /**
     * Valide l'URL de l'agenda est valide, après série de vérifications
     *
     * @param string $urlAgenda L'URL de l'agenda
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra ajouter si erreur détectée
     * @return boolean Retourne vrai si l'URL de l'agenda est valide, faux sinon
     */
    static function validerURLAgenda(string $urlAgenda, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ

        // 2. Type de données : vérifier que le prenom est une chaine de caractères

        // 3. Longueur de la chaine : vérifier que le prenom est compris entre 2 et 50 caractères

        // 4. Format des données : vérifier le format du prénom

        // 5. Plage des valeurs

        // 6. Fichiers uploadés

        return $valide;
    }

    /**
     * Valide la couleur de l'agenda, après série de vérifications
     *
     * @param string $couleurAgenda La couleur de l'agenda
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra ajouter si erreur détectée
     * @return boolean Retourne vrai si la couleur de l'agenda est valide, faux sinon
     */
    static function validerCouleurAgenda(string $couleurAgenda, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ

        // 2. Type de données : vérifier que le prenom est une chaine de caractères

        // 3. Longueur de la chaine : vérifier que le prenom est compris entre 2 et 50 caractères

        // 4. Format des données : vérifier le format du prénom

        // 5. Plage des valeurs

        // 6. Fichiers uploadés

        return $valide;
    }

    /**
     * Valide le mot de passe de l'utilisateur s'il est valide, après série de vérifications
     *
     * @param string $motDePasse Le mot de passe de l'utilisateur
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra ajouter si erreur détectée
     * @return boolean Retourne vrai si le mot de passe est valide, faux sinon
     */
    static function validerMotDePasse(string $motDePasse, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ

        // 2. Type de données : vérifier que le prenom est une chaine de caractères

        // 3. Longueur de la chaine : vérifier que le prenom est compris entre 2 et 50 caractères

        // 4. Format des données : vérifier le format du prénom

        // 5. Plage des valeurs

        // 6. Fichiers uploadés

        return $valide;
    }
}
