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
        if(empty($prenom)){
            $messagesErreurs[] = "Le prénom est obligatoire";
            $valide = false;
        }

        // 2. Type de données : vérifier que le prenom est une chaine de caractères
        if(!is_string($prenom)){
            $messagesErreurs[] = "Le prénom doit être une chaine de caractères";
            $valide = false;
        }

        // 3. Longueur de la chaine : vérifier que le prenom est compris entre 2 et 50 caractères
        if(strlen($prenom) < 2 || strlen($prenom) > 50){
            $messagesErreurs[] = "Le prénom doit être compris entre 2 et 50 caractères";
            $valide = false;
        }

        // 4. Format des données : vérifier le format du prénom
        if(!preg_match("/^[a-zA-ZÀ-ÿ-]+$/", $prenom)){
            $messagesErreurs[] = "Le prénom doit être composé de lettres et de tirets";
            $valide = false;
        }

        // 5. Plage des valeurs - non pertinent

        // 6. Fichiers uploadés - non pertinent

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
        if(empty($nom)){
            $messagesErreurs[] = "Le prénom est obligatoire";
            $valide = false;
        }

        // 2. Type de données : vérifier que le nom est une chaine de caractères
        if(!is_string($nom)){
            $messagesErreurs[] = "Le prénom doit être une chaine de caractères";
            $valide = false;
        }

        // 3. Longueur de la chaine : vérifier que le nom est compris entre 2 et 50 caractères
        if(strlen($nom) < 2 || strlen($nom) > 50){
            $messagesErreurs[] = "Le prénom doit être compris entre 2 et 50 caractères";
            $valide = false;
        }

        // 4. Format des données : vérifier le format du nom
        if(!preg_match("/^[a-zA-ZÀ-ÿ-]+$/", $nom)){
            $messagesErreurs[] = "Le prénom doit être composé de lettres et de tirets";
            $valide = false;
        }

        // 5. Plage des valeurs - non pertinent

        // 6. Fichiers uploadés - non pertinent

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
        if(empty($email)){
            $messagesErreurs[] = "L'email est obligatoire";
            $valide = false;
        }

        // 2. Type de données : vérifier que l'email est une chaine de caractères
        if(!is_string($email)){
            $messagesErreurs[] = "L'email doit être une chaine de caractères";
            $valide = false;
        }

        // 3. Longueur de la chaine : vérifier que l'email est compris entre 5 et 255 caractères
        if(strlen($email) < 5 || strlen($email) > 255){
            $messagesErreurs[] = "L'email doit être compris entre 5 et 255 caractères";
            $valide = false;
        }

        // 4. Format des données : vérifier le format de l'email
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $messagesErreurs[] = "L'email n'est pas valide";
            $valide = false;
        }

        // 5. Plage des valeurs - non pertinent

        // 6. Fichiers uploadés - non pertinent

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
        if(empty($urlAgenda)){
            $messagesErreurs[] = "L'URL de l'agenda est obligatoire";
            $valide = false;
        }

        // 2. Type de données : vérifier que l'URL est une chaine de caractères
        if(!is_string($urlAgenda)){
            $messagesErreurs[] = "L'URL de l'agenda doit être une chaine de caractères";
            $valide = false;
        }

        // 3. Longueur de la chaine - non pertinent

        // 4. Format des données : vérifier le format de l'URL
        if (!filter_var($urlAgenda, FILTER_VALIDATE_URL)) {
            $messagesErreurs[] = "L'URL de l'agenda n'est pas valide.";
            $valide = false;
        } else if (!preg_match('/^https?:\/\/calendar\.google\.com\/calendar\/ical\/.+\/basic\.ics$/', $urlAgenda)) {
            $messagesErreurs[] = "L'URL doit être une URL Google Agenda iCal valide.";
            $valide = false;
        }

        // 5. Plage des valeurs - non pertinent

        // 6. Fichiers uploadés - non pertinent

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
        if(empty($couleurAgenda)){
            $messagesErreurs[] = "La couleur de l'agenda est obligatoire";
            $valide = false;
        }

        // 2. Type de données : vérifier que le prenom est une chaine de caractères
        if(!is_string($couleurAgenda)){
            $messagesErreurs[] = "La couleur de l'agenda doit être une chaine de caractères";
            $valide = false;
        }

        // 3. Longueur de la chaine : vérifier que le prenom a exactement 7 caractères
        if(strlen($couleurAgenda) != 7){
            $messagesErreurs[] = "La couleur de l'agenda doit être composée de 7 caractères";
            $valide = false;
        }

        // 4. Format des données : vérifier le format de la couleur
        if (!preg_match('/^#[a-fA-F0-9]{6}$/', $couleurAgenda)) {
            $messagesErreurs[] = "La couleur sélectionnée n'est pas valide.";
            $valide = false;
        }

        // 5. Plage des valeurs - non pertinent

        // 6. Fichiers uploadés - non pertinent

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
    public static function validerMotDePasse(?string $motDePasse, string $motDePasse2, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        if(empty($motDePasse) || empty($motDePasse2)){
            $messagesErreurs[] = "Le mot de passe est obligatoire";
            $valide = false;
        }

        // 2. Type de données : vérifier que le mot de passe est une chaine de caractères
        if(!is_string($motDePasse) || !is_string($motDePasse2)){
            $messagesErreurs[] = "Le mot de passe doit être une chaine de caractères";
            $valide = false;
        }

        // 3. Longueur de la chaine : vérifier que le mot de passe est compris entre 8 et 25 caractères
        if(strlen($motDePasse) < 8 || strlen($motDePasse) > 25){
            $messagesErreurs[] = "Le mot de passe doit être compris entre 8 et 25 caractères";
            $valide = false;
        }

        // 4. Format des données : vérifier le format du mdp avec preg preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[a-zA-Z\d\W]{8,25}$/'
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[a-zA-Z\d\W]{8,25}$/', $motDePasse)) {
            $messagesErreurs[] = "Le mot de passe doit contenir au moins une lettre minuscule, une lettre majuscule, un chiffre et un caractère spécial.";
            $valide = false;
        }

        // 5. Plage des valeurs : vérifier que les mots de passe sont les mêmes
        if($motDePasse != $motDePasse2){
            $messagesErreurs[] = "Les mots de passe ne correspondent pas";
            $valide = false;
        }

        // 6. Fichiers uploadés - non pertinent

        return $valide;
    }
}
