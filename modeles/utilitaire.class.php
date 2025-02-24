<?php
/**
 * @author Thibault Latxague
 * @brief Classe de vérifcation et des champs de formulaire et d'autres fonctions
 * @version 0.4
 */

use ICal\ICal;

class Utilitaire
{

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
     * @return bool Retourne vrai si le prénom est valide, faux sinon
     */
    public static function validerPrenom(?string $prenom, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($prenom, $messagesErreurs, "prenom");

        // 2. Type de données : vérifier que le prenom est une chaine de caractères
        $valide = utilitaire::validerType($prenom, $messagesErreurs, "prenom");

        // 3. Longueur de la chaine : vérifier que le prenom est compris entre 2 et 50 caractères
        $valide = utilitaire::validerTaille($prenom, 2, 50, $messagesErreurs, "prenom");

        // 4. Format des données : vérifier le format du prénom
        $valide = utilitaire::validerPreg($prenom, "/^[a-zA-ZÀ-ÿ-]+$/", $messagesErreurs, "prenom");

        return $valide;
    }

    /**
     * Valide le nom de l'utilisateur s'il est valide, après série de vérifications
     *
     * @param string $nom Le nom de l'utilisateur
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra ajouter si erreur détectée
     * @return bool Retourne vrai si le nom est valide, faux sinon
     */
    public static function validerNom(?string $nom, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($nom, $messagesErreurs, "nom");

        // 2. Type de données : vérifier que le nom est une chaine de caractères
        $valide = utilitaire::validerType($nom, $messagesErreurs, "nom");

        // 3. Longueur de la chaine : vérifier que le nom est compris entre 2 et 50 caractères
        $valide = utilitaire::validerTaille($nom, 2, 50, $messagesErreurs, "nom");

        // 4. Format des données : vérifier le format du nom
        $valide = utilitaire::validerPreg($nom, "/^[a-zA-ZÀ-ÿ0-9- ]+$/", $messagesErreurs, "nom");

        return $valide;
    }

    /**
     * Valide le nom d'un agenda s'il est valide, après série de vérifications
     *
     * @param string $nom Le nom de l'utilisateur
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra ajouter si erreur détectée
     * @return bool Retourne vrai si le nom est valide, faux sinon
     */
    public static function validerNomAgenda(?string $nom, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($nom, $messagesErreurs, "nom agenda");

        // 2. Type de données : vérifier que le nom est une chaine de caractères
        $valide = utilitaire::validerType($nom, $messagesErreurs, "nom agenda");

        // 3. Longueur de la chaine : vérifier que le nom est compris entre 2 et 50 caractères
        $valide = utilitaire::validerTaille($nom, 2, 50, $messagesErreurs, "nom agenda");

        // 4. Format des données : - non pertinent

        return $valide;
    }

    /**
     * Valide l'email s'il est valide, après série de vérifications
     *
     * @param string $email L'email de l'utilisateur
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra ajouter si erreur détectée
     * @return bool Retourne vrai si l'email est valide, faux sinon
     */
    public static function validerEmail(?string $email, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($email, $messagesErreurs, "email");

        // 2. Type de données : vérifier que l'email est une chaine de caractères
        $valide = utilitaire::validerType($email, $messagesErreurs, "email");

        // 3. Longueur de la chaine : vérifier que l'email est compris entre 5 et 255 caractères
        $valide = utilitaire::validerTaille($email, 5, 255, $messagesErreurs, "mail");

        // 4. Format des données : vérifier le format de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
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
     * @return bool Retourne vrai si l'URL de l'agenda est valide, faux sinon
     */
    public static function validerURLAgenda(?string $urlAgenda, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($urlAgenda, $messagesErreurs, "URL agenda");

        // 2. Type de données : vérifier que l'URL est une chaine de caractères
        $valide = utilitaire::validerType($urlAgenda, $messagesErreurs, "URL agenda");

        // 3. Longueur de la chaine - non pertinent

        // 4. Format des données : vérifier le format de l'URL
        if (!filter_var($urlAgenda, FILTER_VALIDATE_URL) && !utilitaire::validerPreg($urlAgenda, '/^http/', $messagesErreurs, "URL agenda")) {
            $messagesErreurs[] = "L'URL de l'agenda n'est pas valide.";
            $valide = false;
        } else {
            // Vérification du type MIME du fichier
            try {
                // Récupère les en-têtes de l'URL (avec @ pour supprimer les avertissements)
                @$headers = get_headers($urlAgenda, 1);
            
                // Vérifie si les en-têtes sont valides et si 'Content-Type' existe
                if (!$headers || !isset($headers['Content-Type'])) {
                    $messagesErreurs[] = "Impossible de récupérer les informations du fichier à partir de l'URL";
                    $valide = false;
                } else {
                    // Vérifie si le type de contenu est 'text/calendar'
                    if (strpos($headers['Content-Type'], 'text/calendar') === false) {
                        $messagesErreurs[] = "Le fichier obtenu à partir de l'URL n'est pas un agenda";
                        $valide = false;
                    }
                }
            } catch (Exception $e) {
                $messagesErreurs[] = "Impossible de vérifier le type du fichier à partir de l'URL";
                $valide = false;
            }

            // Test de création de l'objet ICal à partir de l'URL si vérification précédente réussie
            if ($valide) {
                try {
                    // @ nécessaire pour enlever les erreurs
                    @$calendrier = new ICal($urlAgenda);
                } catch (Exception $e) {
                    $messagesErreurs[] = "Impossible d'importer les données ";
                    $valide = false;
                }
            }
        }

        return $valide;
    }

    /**
     * Valide la couleur de l'agenda, après série de vérifications
     *
     * @param string $couleurAgenda La couleur de l'agenda
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra ajouter si erreur détectée
     * @return bool Retourne vrai si la couleur de l'agenda est valide, faux sinon
     */
    public static function validerCouleurAgenda(?string $couleurAgenda, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($couleurAgenda, $messagesErreurs, "couleur agenda");

        // 2. Type de données : vérifier que le prenom est une chaine de caractères
        $valide = utilitaire::validerType($couleurAgenda, $messagesErreurs, "couleur agenda");

        // 3. Longueur de la chaine : vérifier que le prenom a exactement 7 caractères
        $valide = utilitaire::validerTaille($couleurAgenda, 7, 7, $messagesErreurs, "couleur agenda");

        // 4. Format des données : vérifier le format de la couleur
        $valide = utilitaire::validerPreg($couleurAgenda, "/^#[a-fA-F0-9]{6}$/", $messagesErreurs, "couleur agenda");

        return $valide;
    }

    /**
     * Valide le mot de passe de l'utilisateur s'il est valide, après série de vérifications
     * Pour les points 3 et 4, comparer les 2 mots de passes est inutile car comparaison à la fin s'ils sont identiques
     * @param string $motDePasse Le mot de passe de l'utilisateur
     * @param string $motDePasse2 Le mot de passe confirmé de l'utilisateur
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra ajouter si erreur détectée
     * @return bool Retourne vrai si le mot de passe est valide, faux sinon
     */
    public static function validerMotDePasseInscription(?string $motDePasse, array &$messagesErreurs, ?string $motDePasse2 = null): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($motDePasse, $messagesErreurs, "mot de passe");
        $motDePasse2 != null ? $valide = utilitaire::validerPresence($motDePasse2, $messagesErreurs, "mot de passe") : $valide = true;

        // 2. Type de données : vérifier que le mot de passe est une chaine de caractères
        $valide = utilitaire::validerType($motDePasse, $messagesErreurs, "mot de passe");
        $motDePasse2 != null ? $valide = utilitaire::validerType($motDePasse2, $messagesErreurs, "mot de passe") : $valide = true;

        // 3. Longueur de la chaine : vérifier que le mot de passe est compris entre 8 et 25 caractères
        $valide = utilitaire::validerTaille($motDePasse, 8, 25, $messagesErreurs, "mot de passe");
        $motDePasse2 != null ? $valide = utilitaire::validerTaille($motDePasse2, 8, 25, $messagesErreurs, "mot de passe") : $valide = true;

        // 4. Format des données : vérifier le format du mdp avec preg preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[a-zA-Z\d\W]{8,25}$/'
        $valide = utilitaire::validerPreg($motDePasse, '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[a-zA-Z\d\W]{8,25}$/', $messagesErreurs, "mot de passe");
        $motDePasse2 != null ? $valide = utilitaire::validerPreg($motDePasse2, '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[a-zA-Z\d\W]{8,25}$/', $messagesErreurs, "mot de passe") : $valide = true;

        // 5. Plage des valeurs : vérifier que les mots de passe sont les mêmes
        if (($motDePasse != $motDePasse2) && $motDePasse2 != null) {
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
     * @return bool Retourne vrai si le mot de passe est valide, faux sinon
     */
    public static function validerMotDePasse(?string $motDePasse, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($motDePasse, $messagesErreurs, "mot de passe");

        // 2. Type de données : vérifier que le mot de passe est une chaine de caractères
        $valide = utilitaire::validerType($motDePasse, $messagesErreurs, "mot de passe");

        // 3. Longueur de la chaine : vérifier que le mot de passe est compris entre 8 et 25 caractères
        $valide = utilitaire::validerTaille($motDePasse, 8, 25, $messagesErreurs, "mot de passe");

        // 4. Format des données : vérifier le format du mdp avec preg preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[a-zA-Z\d\W]{8,25}$/'
        $valide = utilitaire::validerPreg($motDePasse, '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[a-zA-Z\d\W]{8,25}$/', $messagesErreurs, "mot de passe");

        return $valide;
    }

    /**
     * Valide le role de l'utilisateur, s'il est valide, après série de vérifications. Changé par les administrateurs
     *
     * @param string|null $role Le role de l'utilisateur donné dans le formulaire de modification
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra retourner si erreur détectée
     * @return bool Retourne vrai si le role est valide, faux sinon
     */
    public static function validerRole(?string $role, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        // $valide = utilitaire::validerPresence($role, $messagesErreurs);
        // Le champ n'est pas obligatoire

        // 2. Type de données : vérifier que le mot de passe est une chaine de caractères
        $valide = utilitaire::validerType($role, $messagesErreurs, "role");

        // 3. Longueur de la chaine - non pertinent

        // 4. Format de données : vérifier si le role est utilisateur ou admin
        if (($role != '0' && $role != '1') && ($role != 'Admin' && $role != 'User')) {
            $messagesErreurs[] = "Le role n'est pas valide";
            $valide = false;
        }

        return $valide;
    }

    /**
     * Valide la taille de la chaine donnée en paramètre
     *
     * @param string|null $chaine La chaine à valider
     * @param integer $min La taille minimale de la chaine
     * @param integer $max La taille maximale de la chaine
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra renvoyer si erreur détectée
     * @return bool Retourne vrai si la taille est valide, faux sinon
     */
    public static function validerTaille(?string $chaine, int $min, int $max, array &$messagesErreurs, ?string $champ): bool
    {
        $valide = true;
        // 3. Longueur de la chaine : vérifier que le champ est compris entre min et max caractères
        if (strlen($chaine) < $min || strlen($chaine) > $max) {
            $messagesErreurs[] = "Le " . $champ . " doit être compris entre $min et $max caractères";
            $valide = false;
        }
        return $valide;
    }

    /**
     * Valide la présence du champ donné en paramètre
     *
     * @param string|null $chaine La chaine à valider
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra renvoyer si erreur détectée
     * @param string|null $nomChamp le nom du champ pour personnaliser le message d'erreur
     * @return bool Retourne vrai si le champ est présent, faux sinon
     */
    public static function validerPresence(?string $chaine, array &$messagesErreurs, ?string $nomChamp): bool
    {
        $valide = true;
        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        if (empty($chaine)) {
            $messagesErreurs[] = "Le " . $nomChamp . " est obligatoire";
            $valide = false;
        }
        return $valide;
    }

    /**
     * Valide le type du paramètre donné en paramètre
     *
     * @param string|null $chaine La chaine à valider
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra renvoyer si erreur détectée
     * @param string|null $nomChamp le nom du champ pour personnaliser le message d'erreur
     * @return bool Retourne vrai si le type est valide, faux sinon
     */
    public static function validerType(?string $chaine, array &$messagesErreurs, ?string $nomChamp): bool
    {
        $valide = true;
        // 2. Type de données : vérifier que le champ est une chaine de caractères
        if (!is_string($chaine)) {
            $messagesErreurs[] = "Le " . $nomChamp . " doit être une chaine de caractères";
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
     * @return bool Retourne vrai si le format est valide, faux sinon
     */
    public static function validerPreg(?string $chaine, string $pattern, array &$messagesErreurs, ?string $nomChamp): bool
    {
        $valide = true;
        // 2. Type de données : vérifier que le champ est une chaine de caractères
        if (!preg_match($pattern, $chaine)) {
            $messagesErreurs[] = "Le " . $nomChamp . " ne respecte pas le format attendu";
            $valide = false;
        }
        return $valide;
    }

    /**
     * Fonction qui valide une photo de profil dans un formulaire (modification utilisateur ou admin)
     *
     * @param array|null $photo La photo de profil de l'utilisateur
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra renvoyer si erreur détectée
     * @return bool Retourne vrai si la photo est valide, faux sinon
     */
    public static function validerPhoto(?array $photo, array &$messagesErreurs): bool
    {
        $valide = true;
        // 1. Champs obligatoires : vérifier la présence du champ (pas obligatoire mais sert d'indication)
        if ($photo['name'] == '') {
            $valide = false;
        } else {
            // 2. Type de données : vérifier que le nom est une chaine de caractères
            if (!is_array($photo)) {
                $messagesErreurs[] = "La photo doit être un fichier";
                $valide = false;
            }

            // 3. Longueur de la chaine (cf. taille) 2 Mo maximum
            if (sizeof($photo) > 2000000) {
                $valide = false;
                $messagesErreurs[] = "La taille de la photo est trop grande (2Mo maximum)";
            }

            // 4. Format des données : vérifier le format de la photo
            if ($photo['error'] != UPLOAD_ERR_OK) {
                $valide = false;
                $messagesErreurs[] = "Erreur lors du chargement de la photo";
            }
        }
        return $valide;
    }

    /**
     * Fonction qui valide une date dans le formulaire de recherche
     *
     * @param string|null $date La date de début de la recherche
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra renvoyer si erreur détectée
     * @return bool Retourne vrai si la date est valide, faux sinon
     */
    // public static function validerDate(?string $date, array &$messagesErreurs): bool
    // {
    //     $valide = true;

    //     // Extraire les parties de la date : année, mois, jour
    //     list($year, $month, $day) = explode('-', $date);

    //     // Vérifier si la date est valide dans le calendrier
    //     if (!checkdate((int) $month, (int) $day, (int) $year)) {
    //         $messagesErreurs[] = "Une date n'est pas valide dans le calendrier";
    //         $valide = false;
    //     }

    //     return $valide;
    // }

    public static function validerDate(?string $date, array &$messagesErreurs): bool
{
    $valide = true;

    // Extraire les parties de la date : année, mois, jour
    list($datetime) = explode('T', $date);
    list($year, $month, $day) = explode('-', $datetime);

    // Vérifier si la date est valide dans le calendrier
    if (!checkdate((int) $month, (int) $day, (int) $year)) {
        $messagesErreurs[] = "La date n'est pas valide dans le calendrier.";
        $valide = false;
    }

    return $valide;
}





    /**
     * Fonction qui valide une heure dans le formulaire de recherche
     *
     * @param string|null $heure L'heure de début de la recherche
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra renvoyer si erreur détectée
     * @return bool Retourne vrai si l'heure est valide, faux sinon
     */
    public static function validerDureeMinimale(?string $heure, array &$messagesErreurs): bool
    {
        $valide = true;
        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($heure, $messagesErreurs, "duree minimale");

        // 2. Type de données : vérifier que le champ est une chaine de caractères
        $valide = utilitaire::validerType($heure, $messagesErreurs, "duree minimale");

        // 3. Longueur de la chaine - non pertinent

        // 4. Format des données : vérifier le format de l'heure
        $valide = utilitaire::validerPreg($heure, "/^(2[0-3]|[01]\d):[0-5]\d$/", $messagesErreurs, "duree minimale");

        // 5. Plage des valeurs
        $dureeMinSeconds = strtotime($heure);

        $valide = $dureeMinSeconds > 5 * 60;
        if (!$valide) {
            $messagesErreurs[] = "La durée minimale doit être supérieure à 5 minutes";
        }

        return $valide;
    }

    /**
     * Fonction qui valide une heure dans le formulaire de recherche
     *
     * @param string|null $debutHoraire L'heure du début de la plage horaire
     * @param string|null $finHoraire L'heure de fin de la plage horaire
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra renvoyer si erreur détectée
     * @return bool Retourne vrai si l'heure est valide, faux sinon
     */
    public static function validerPlageHoraire(?string $debutHoraire, ?string $finHoraire, array &$messagesErreurs): bool
    {
        $valide = true;
        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($debutHoraire, $messagesErreurs, "début de la plage horaire");
        $valide = utilitaire::validerPresence($finHoraire, $messagesErreurs, "fin de la plage horaire");

        // 2. Type de données : vérifier que le champ est une chaine de caractères
        $valide = utilitaire::validerType($debutHoraire, $messagesErreurs, "début de la plage horaire");
        $valide = utilitaire::validerType($finHoraire, $messagesErreurs, "fin de la plage horaire");

        // 3. Longueur de la chaine - non pertinent

        // 4. Format des données : vérifier le format de l'heure
        $valide = utilitaire::validerPreg($debutHoraire, "/^(2[0-3]|[01]\d):[0-5]\d$/", $messagesErreurs, "début de la plage horaire");
        $valide = utilitaire::validerPreg($finHoraire, "/^(2[0-3]|[01]\d):[0-5]\d$/", $messagesErreurs, "fin de la plage horaire");

        return $valide;
    }

    /**
     * Fonction qui valide la couleur d'un agenda lors de sa création
     *
     * @param string|null $couleur La couleur de l'agenda
     * @param array $messagesErreurs Les messages d'erreurs que l'on pourra renvoyer si erreur détectée
     * @return bool Retourne vrai si la couleur est valide, faux sinon
     */
    public static function validerCouleur(?string $couleur, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($couleur, $messagesErreurs, "couleur agenda");

        // 2. Type de données : vérifier que le prenom est une chaine de caractères
        $valide = utilitaire::validerType($couleur, $messagesErreurs, "couleur agenda");

        // 3. Longueur de la chaine : vérifier que le prenom a exactement 7 caractères
        $valide = utilitaire::validerTaille($couleur, 7, 7, $messagesErreurs, "couleur agenda");

        // 4. Format des données : vérifier le format de la couleur
        $valide = utilitaire::validerPreg($couleur, "/^#[a-fA-F0-9]{6}$/", $messagesErreurs, "couleur agenda");

        return $valide;
    }

    /**
     * Fonction qui valide la description d'un groupe
     * 
     * @param string|null $description la description du message
     * @param array $messagesErreurs le tableau qui contiendra les messages d'erreur
     * @return bool si le champ est valide ou non
     */
    public static function validerDescription(?string $description, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($description, $messagesErreurs, "description d'un groupe");

        // 2. Type de données : vérifier que la description est une chaine de caractères
        $valide = utilitaire::validerType($description, $messagesErreurs, "description d'un groupe");

        // 3. Longueur de la chaine
        $valide = utilitaire::validerTaille($description, 10, 200, $messagesErreurs, "description d'un groupe");

        // 4. Format des données
        $valide = utilitaire::validerPreg($description, "/^[a-zA-ZÀ-ÿ0-9- `',.]+$/", $messagesErreurs, "description d'un groupe");

        return $valide;
    }

    /**
     * Fonction qui valide la description d'une demande de contact de l'équipe TH
     * 
     * @param string|null $description la description du message
     * @param array $messagesErreurs le tableau qui contiendra les messages d'erreur
     * @return bool si le champ est valide ou non
     */
    public static function validerDescriptionFormContact(?string $description, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($description, $messagesErreurs, "description de la demande");

        // 2. Type de données : vérifier que la description est une chaine de caractères
        $valide = utilitaire::validerType($description, $messagesErreurs, "description de la demande");

        // 3. Longueur de la chaine
        $valide = utilitaire::validerTaille($description, 10, 200, $messagesErreurs, "description de la demande");

        // 4. Format des données
        $valide = utilitaire::validerPreg($description, "/^[a-zA-ZÀ-ÿ0-9 \"\'?,!ùéèàçÉÈÙ]+$/u", $messagesErreurs, "description de la demande");

        return $valide;
    }

    /**
     * Fonction qui valide le sujet d'une demande de contact de l'équipe TH
     * 
     * @param string|null $sujet le sujet de la demande
     * @param array $messagesErreurs le tableau des messages d'erreur
     * @return bool retourne vrai si le champ est valide
     */
    public static function validerSujet(?string $sujet, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($sujet, $messagesErreurs, "sujet de la demande");

        // 2. Type de données : vérifier que le prenom est une chaine de caractères
        $valide = utilitaire::validerType($sujet, $messagesErreurs, "sujet de la demande");

        // 3. Longueur de la chaine - non pertinent
        // 4. Format des données - non pertinent

        // 5. Plage des valeurs
        $valeursPossibles = ['Demande generale d\'information', 'Question conditions generales d\'utilisation', 'Question politique de confidentialite', 'Commentaires ou Suggestions', 'Consulter ses donnees', 'Rectifier ses donnees', 'Supprimer ses donnees', 'Autre']; // + autres valeurs possibles
        if ($sujet != null && !in_array($sujet, $valeursPossibles)) {
            $messagesErreurs[] = "Le sujet n'est pas valide";
            $valide = false;
        }

        return $valide;
    }

    /**
     * Fonction qui permet de valider le statut de l'utilisateur dans la page admin
     * 
     * @param string|null $statut le statut de l'utilisateur
     * @param array $messagesErreurs le tabeleau de message d'erreurs
     * @return bool si le statut est valide ou non
     */
    public static function validerStatut(?string $statut, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($statut, $messagesErreurs, "statut de compte de l'utilisateur");

        // 2. Type de données : vérifier que le prenom est une chaine de caractères
        $valide = utilitaire::validerType($statut, $messagesErreurs, "statut de compte de l'utilisateur");

        // 3. Longueur de la chaine - non pertinent
        // 4. Format des données - non pertinent

        // 5. Plage des valeurs
        $statut = strtolower($statut);
        $valeursPossibles = ['desactive', 'actif', 'bloque', 'Actif', 'Désactivé', 'Bloqué']; // + autres valeurs possibles
        if ($statut != null && !in_array($statut, $valeursPossibles)) {
            $messagesErreurs[] = "Le statut n'est pas valide";
            $valide = false;
        }

        return $valide;
    }

    /**
     * Fonction qui permet de valider la durée de recherche
     * 
     * @param string|null $debut le debut de la recherche
     * @param string|null $fin la fin de la recherche
     * @param array $tableauErreurs le tableau des erreurs
     * @return bool si le champ est correct ou non
     */
    public static function validerDuree(?string $debut, ?string $fin, array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        $valide = utilitaire::validerPresence($debut, $messagesErreurs, "debut du créneau");
        $valide = utilitaire::validerPresence($fin, $messagesErreurs, "fin du créneau");
        $valide = utilitaire::validerType($debut, $messagesErreurs, "début de la recherche");
        $valide = utilitaire::validerType($fin, $messagesErreurs, "fin de la recherche");
        $valide = utilitaire::validerPreg($debut, "/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])T([01]\d|2[0-3]):([0-5]\d)$/", $messagesErreurs, "début de la recherche");
        $valide = utilitaire::validerPreg($fin, "/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])T([01]\d|2[0-3]):([0-5]\d)$/", $messagesErreurs, "fin de la recherche");
        $valide = utilitaire::validerDate($debut, $messagesErreurs);
        $valide = utilitaire::validerDate($fin, $messagesErreurs);
        // 5. Plage des valeurs
        $dateDebut = new DateTime($debut);
        $dateFin = new DateTime($fin);

        if ($dateDebut >= $dateFin) {
            $valide = false;
            $messagesErreurs[] = "La date de début doit être inférieure à la date de fin";
        }

        return $valide;
    }

    /**
     * Fonction qui permet de valider les contacts de recherche
     * 
     * @param array|null $contacts le tableau des contacts de recherche
     * @param array $tableauErreurs le tableau des erreurs
     * @return bool si le champ est correct ou non
     */
    public static function validerContacts(?array $contacts,array &$messagesErreurs): bool
    {
        $valide = true;

        // 1. Champs obligatoires : vérifier la présence du champ (obligatoire)
        if (empty($contacts) && empty($groupes)) {
            $messagesErreurs[] = "Aucun contact renseigné";
            $valide = false;
        }

        return $valide;
    }


}
