<?php
/**
 * Undocumented class
 */

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
    public static function redimage(?string $img_src, ?string $img_dest, ?string $dst_w, ?string $dst_h): ?string
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
    public static function validerPrenom(?string $prenom, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($prenom, $messagesErreurs);

        // 2. Type de données : vérifier que le prenom est une chaine de caractères
        $valide = utilitaire::validerType($prenom, $messagesErreurs);

        // 3. Longueur de la chaine : vérifier que le prenom est compris entre 2 et 50 caractères
        $valide = utilitaire::validerTaille($prenom, 2, 50, $messagesErreurs);

        // 4. Format des données : vérifier le format du prénom
        $valide = utilitaire::validerPreg($prenom, "/^[a-zA-ZÀ-ÿ-]+$/", $messagesErreurs);

        return $valide;
    }

    /**
     * Valide le nom de l'utilisateur s'il est valide, après série de vérifications
     *
     * @param string $nom Le nom de l'utilisateur
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra ajouter si erreur détectée
     * @return boolean Retourne vrai si le nom est valide, faux sinon
     */
    public static function validerNom(?string $nom, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($nom, $messagesErreurs);

        // 2. Type de données : vérifier que le nom est une chaine de caractères
        $valide = utilitaire::validerType($nom, $messagesErreurs);

        // 3. Longueur de la chaine : vérifier que le nom est compris entre 2 et 50 caractères
        $valide = utilitaire::validerTaille($nom, 2, 50, $messagesErreurs);

        // 4. Format des données : vérifier le format du nom
        $valide = utilitaire::validerPreg($nom, "/^[a-zA-ZÀ-ÿ-]+$/", $messagesErreurs);

        return $valide;
    }

    /**
     * Valide l'email s'il est valide, après série de vérifications
     *
     * @param string $email L'email de l'utilisateur
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra ajouter si erreur détectée
     * @return boolean Retourne vrai si l'email est valide, faux sinon
     */
    public static function validerEmail(?string $email, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($email, $messagesErreurs);

        // 2. Type de données : vérifier que l'email est une chaine de caractères
        $valide = utilitaire::validerType($email, $messagesErreurs);

        // 3. Longueur de la chaine : vérifier que l'email est compris entre 5 et 255 caractères
        $valide = utilitaire::validerTaille($email, 5, 255, $messagesErreurs);

        // 4. Format des données : vérifier le format de l'email
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $messagesErreurs[] = "L'email n'est pas valide";
            $valide = false;
        }

        return $valide;
    }

    /**
     * Valide l'URL de l'agenda est valide, après série de vérifications
     *
     * @param string $urlAgenda L'URL de l'agenda
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra ajouter si erreur détectée
     * @return boolean Retourne vrai si l'URL de l'agenda est valide, faux sinon
     */
    public static function validerURLAgenda(?string $urlAgenda, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($urlAgenda, $messagesErreurs);

        // 2. Type de données : vérifier que l'URL est une chaine de caractères
        $valide = utilitaire::validerType($urlAgenda, $messagesErreurs);

        // 3. Longueur de la chaine - non pertinent

        // 4. Format des données : vérifier le format de l'URL
        if (!filter_var($urlAgenda, FILTER_VALIDATE_URL)) {
            $messagesErreurs[] = "L'URL de l'agenda n'est pas valide.";
            $valide = false;
        } else {
            $valide = utilitaire::validerPreg($urlAgenda, '/^https?:\/\/calendar\.google\.com\/calendar\/ical\/.+\/basic\.ics$/', $messagesErreurs);
        }

        return $valide;
    }

    /**
     * Valide la couleur de l'agenda, après série de vérifications
     *
     * @param string $couleurAgenda La couleur de l'agenda
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra ajouter si erreur détectée
     * @return boolean Retourne vrai si la couleur de l'agenda est valide, faux sinon
     */
    public static function validerCouleurAgenda(?string $couleurAgenda, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($couleurAgenda, $messagesErreurs);

        // 2. Type de données : vérifier que le prenom est une chaine de caractères
        $valide = utilitaire::validerType($couleurAgenda, $messagesErreurs);

        // 3. Longueur de la chaine : vérifier que le prenom a exactement 7 caractères
        $valide = utilitaire::validerTaille($couleurAgenda, 7, 7, $messagesErreurs);

        // 4. Format des données : vérifier le format de la couleur
        $valide = utilitaire::validerPreg($couleurAgenda, "/^#[a-fA-F0-9]{6}$/", $messagesErreurs);

        return $valide;
    }

    /**
     * Valide le mot de passe de l'utilisateur s'il est valide, après série de vérifications
     * Pour les points 3 et 4, comparer les 2 mots de passes est inutile car comparaison à la fin s'ils sont identiques
     * @param string $motDePasse Le mot de passe de l'utilisateur
     * @param string $motDePasse2 Le mot de passe confirmé de l'utilisateur
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra ajouter si erreur détectée
     * @return boolean Retourne vrai si le mot de passe est valide, faux sinon
     */
    public static function validerMotDePasseInscription(?string $motDePasse, array &$messagesErreurs, ?string $motDePasse2 = null): bool {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($motDePasse, $messagesErreurs);
        $motDePasse2 != null ? $valide = utilitaire::validerPresence($motDePasse2, $messagesErreurs) : $valide = true;

        // 2. Type de données : vérifier que le mot de passe est une chaine de caractères
        $valide = utilitaire::validerType($motDePasse, $messagesErreurs);
        $motDePasse2 != null ? $valide = utilitaire::validerType($motDePasse2, $messagesErreurs) : $valide = true;

        // 3. Longueur de la chaine : vérifier que le mot de passe est compris entre 8 et 25 caractères
        $valide = utilitaire::validerTaille($motDePasse, 8, 25, $messagesErreurs);
        $motDePasse2 != null ? $valide = utilitaire::validerTaille($motDePasse2, 8, 25, $messagesErreurs) : $valide = true;

        // 4. Format des données : vérifier le format du mdp avec preg preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[a-zA-Z\d\W]{8,25}$/'
        $valide = utilitaire::validerPreg($motDePasse, '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[a-zA-Z\d\W]{8,25}$/', $messagesErreurs);
        $motDePasse2 != null ? $valide = utilitaire::validerPreg($motDePasse2, '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[a-zA-Z\d\W]{8,25}$/', $messagesErreurs) : $valide = true;

        // 5. Plage des valeurs : vérifier que les mots de passe sont les mêmes
        if(($motDePasse != $motDePasse2) && $motDePasse2 != null){
            $messagesErreurs[] = "Les mots de passe ne correspondent pas";
            $valide = false;
        }

        return $valide;
    }

    /**
     * Valide le mot de passe de l'utilisateur, s'il est valide, après série de vérifications
     *
     * @param string|null $motDePasse Le mot de passe de l'utilisateur donné dans le formulaire
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra retourner si erreur détectée
     * @return boolean Retourne vrai si le mot de passe est valide, faux sinon
     */
    public static function validerMotDePasse(?string $motDePasse, array &$messagesErreurs): bool {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($motDePasse, $messagesErreurs);

        // 2. Type de données : vérifier que le mot de passe est une chaine de caractères
        $valide = utilitaire::validerType($motDePasse, $messagesErreurs);

        // 3. Longueur de la chaine : vérifier que le mot de passe est compris entre 8 et 25 caractères
        $valide = utilitaire::validerTaille($motDePasse, 8, 25, $messagesErreurs);

        // 4. Format des données : vérifier le format du mdp avec preg preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[a-zA-Z\d\W]{8,25}$/'
        $valide = utilitaire::validerPreg($motDePasse, '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[a-zA-Z\d\W]{8,25}$/', $messagesErreurs);

        return $valide;
    }

    /**
     * Valide la taille de la chaine donnée en paramètre
     *
     * @param string|null $chaine La chaine à valider
     * @param integer $min La taille minimale de la chaine
     * @param integer $max La taille maximale de la chaine
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra renvoyer si erreur détectée
     * @return boolean Retourne vrai si la taille est valide, faux sinon
     */
    public static function validerTaille(?string $chaine, int $min, int $max, array &$messagesErreurs): bool {
        $valide = true;
        // 3. Longueur de la chaine : vérifier que le champ est compris entre min et max caractères
        if(strlen($chaine) < $min || strlen($chaine) > $max){
            $messagesErreurs[] = "Le champ doit être compris entre $min et $max caractères";
            $valide = false;
        }
        return $valide;
    }

    /**
     * Valide la présence du champ donné en paramètre
     *
     * @param string|null $chaine La chaine à valider
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra renvoyer si erreur détectée
     * @return boolean Retourne vrai si le champ est présent, faux sinon
     */
    public static function validerPresence(?string $chaine, array &$messagesErreurs): bool {
        $valide = true;
        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        if(empty($chaine)){
            $messagesErreurs[] = "Le champ est obligatoire";
            $valide = false;
        }
        return $valide;
    }

    /**
     * Valide le type du paramètre donné en paramètre
     *
     * @param string|null $chaine La chaine à valider
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra renvoyer si erreur détectée
     * @return boolean Retourne vrai si le type est valide, faux sinon
     */
    public static function validerType(?string $chaine, array &$messagesErreurs): bool {
        $valide = true;
        // 2. Type de données : vérifier que le champ est une chaine de caractères
        if(!is_string($chaine)){
            $messagesErreurs[] = "Le champ doit être une chaine de caractères";
            $valide = false;
        }
        return $valide;
    }

    /**
     * Valide le format du paramètre donné en paramètre
     *
     * @param string|null $chaine La chaine à valider
     * @param string $pattern Le pattern à respecter que l'on donne au preg_match
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra renvoyer si erreur détectée
     * @return boolean Retourne vrai si le format est valide, faux sinon
     */
    public static function validerPreg(?string $chaine, string $pattern, array &$messagesErreurs): bool {
        $valide = true;
        // 2. Type de données : vérifier que le champ est une chaine de caractères
        if(!preg_match($pattern, $chaine)){
            $messagesErreurs[] = "Le champ ne respecte pas le format attendu";
            $valide = false;
        }
        return $valide;
    }
}
