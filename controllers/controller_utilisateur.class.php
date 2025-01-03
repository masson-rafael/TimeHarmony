<?php

use Twig\Profiler\Dumper\BaseDumper;

require_once 'include.php';

/**
 * @author Thibault Latxague
 * @describe Controller de la page des utilisateur
 * @version 0.1
 * @todo POURQUOI LA MESSAGE BOX NE FONCTIONNE PAS ?????? (a cause de l'insertion des scripts ? dans base_template) + detailler doxygen nav connexion inscription
 */

class ControllerUtilisateur extends Controller
{
    /**
     * Constructeur par défaut
     *
     * @param \Twig\Environment $twig Environnement twig
     * @param \Twig\Loader\FilesystemLoader $loader Loader de fichier
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    /**
     * Inscription de l'utilisateur à la BD. On utilise les fonctions de validation pour vérifier les champs
     * et ensuite on vérifie si l'utilisateur existe déjà dans la BD
     * avant de procéder à l'inscription de l'utilisateur dans la BD
     * @return void
     */
    public function inscription()
    {
        $pdo = $this->getPdo();
        $tableauErreurs = [];

        $emailValide = utilitaire::validerEmail($_POST['email'], $tableauErreurs);
        $nomValide = utilitaire::validerNom($_POST['nom'], $tableauErreurs);
        $prenomValide = utilitaire::validerPrenom($_POST['prenom'], $tableauErreurs);
        $mdpValide = utilitaire::validerMotDePasseInscription($_POST['pwd'], $tableauErreurs, $_POST['pwdConfirme']);

        if ($emailValide && $nomValide && $prenomValide && $mdpValide) {
            $manager = new UtilisateurDao($pdo); //Lien avec PDO
            /**
             * Verifie que l'utilisateur n'existe pas.
             * On hash le mdp, on crée un nouvel utilisateur et on l'ajoute dans la bd
             */
            $utilisateurExiste = $manager->findMail($_POST['email']);
            if (!$utilisateurExiste) {
                $mdpHache = password_hash($_POST['pwd'], PASSWORD_DEFAULT);
                $nouvelUtilisateur = Utilisateur::createAvecParam(null, $_POST['nom'], $_POST['prenom'], $_POST['email'], $mdpHache, "utilisateurBase.png", false);
                $manager->ajouterUtilisateur($nouvelUtilisateur);
                $tableauErreurs[] = "Inscription réussie !";
            } else {
                // Si l'utilisateur existe deja
                $tableauErreurs[] = "L'utilisateur existe déjà ! Connectez-vous !";
            }
        }
        // Affichage de la page avec les erreurs ou le message de succès. Utilisation du @ car si l'utilisateur n'a pas renseigné de mail, cette action génère une erreur
        @$this->genererVue($_POST['email'], null, $tableauErreurs);
    }

    /**
     * Fonction de connexion au site à partir de la BD.
     * On utilise les fonctions de validation pour vérifier les champs email et mdp
     * et ensuite on vérifie si l'utilisateur existe dans la BD avec le bon mot de passe via password_verify
     *
     * @return void
     */
    public function connexion()
    {
        $pdo = $this->getPdo();
        $tableauErreurs = [];
        
        // Validation des entrées
        $emailValide = utilitaire::validerEmail($_POST['email'], $tableauErreurs);
        $passwdValide = utilitaire::validerMotDePasseInscription($_POST['pwd'], $tableauErreurs);

        $manager = new UtilisateurDao($pdo);
        $compteUtilisateurCorrespondant = $manager->getObjetUtilisateur($_POST['email']);
        
        if ($emailValide && $passwdValide && $compteUtilisateurCorrespondant != null) {
            // Reactivation compte
            $compteUtilisateurCorrespondant->reactiverCompte();
            
            // On recupere un tuple avec un booleen et le mdp hache
            $motDePasse = $manager->connexionReussie($_POST['email']);
            
            if ($compteUtilisateurCorrespondant->getStatutCompte() === "actif") {
                if ($motDePasse[0] && password_verify($_POST['pwd'], $motDePasse[1])) {
                    // Connexion réussie
                    $utilisateur = $manager->getUserMail($_POST['email']);
                    $tableauErreurs[] = "Connexion réussie !";
                    $compteUtilisateurCorrespondant->reinitialiserTentativesConnexion();
                    $compteUtilisateurCorrespondant->reactiverCompte();
                    $manager->miseAJourUtilisateur($compteUtilisateurCorrespondant);
                    $this->genererVueConnecte($utilisateur, $tableauErreurs);
                } else {
                    // Échec de connexion - mauvais mot de passe
                    $tableauErreurs[] = "Mot de passe incorrect. Essayez de réinitialisez votre mot de passe";
                    $compteUtilisateurCorrespondant->gererEchecConnexion();
                    $manager->miseAJourUtilisateur($compteUtilisateurCorrespondant);
                    $this->genererVueConnexion($tableauErreurs, null);
                }
            } else {
                // Compte inactif
                $tableauErreurs[] = "Votre compte est bloqué. Temps restant avec deblocage : " . 
                    (string) abs($compteUtilisateurCorrespondant->tempsRestantAvantReactivationCompte()) . " secondes.";
                $manager->miseAJourUtilisateur($compteUtilisateurCorrespondant);
                $this->genererVueConnexion($tableauErreurs, null);
            }
        } else {
            // Échec de validation des entrées
            $tableauErreurs[] = "Aucun compte avec cette adresse mail n'existe";
            $this->genererVueConnexion($tableauErreurs, null);
        }
    }

    /**
     * Cette fonction est appelée lorsqu'on clique sur le bouton "Inscription" sur la navbar
     * Son but est de générer le page nommée inscription : celle ou l'utilisateur peut remplir le formulaire
     * @return void
     */
    public function premiereInscription()
    {
        $this->genererVueVide('inscription');
    }

    /**
     * Cette fonction est appelée lorsqu'on clique sur le bouton "Connexion" sur la navbar
     * Son but est de générer le page nommée connexion : celle ou l'utilisateur peut remplir le formulaire
     * @return void
     */
    public function premiereConnexion()
    {
        $this->genererVueVide('connexion');
    }

    /**
     * Cette fonction est appelée lorsqu'on clique sur le logo dans  la navbar
     * Elle nous envoie donc à la page d'accueil où on peut sélectionner les menus
     * @return void
     */
    public function menuConnecte()
    {
        $this->genererVueVide('menu');
    }

    /**
     * Deconnexion de l'utilisateur et unset de la session
     * L'utilisateur est ensuite redirigé vers la page d'accueil où figure une vidéo de présentation de l'application
     * @return void
     */
    public function deconnecter()
    {
        $this->getTwig()->addGlobal('utilisateurGlobal', null);
        unset($_SESSION['utilisateur']);
        $this->genererVueVide('index');
    }

    /**
     * Genere la vue du twig
     * @todo verifier si useless
     * @param string|null $mail de la personne qui s'inscrit
     * @param boolean|null $existe si l'utilisateur existe deja
     * @param string|null $message le message renvoyé par les fonctions (erreur détaillée ou reussite)
     * @return void
     */
    public function genererVue(?string $mail, ?bool $existe, ?array $messages)
    {
        //Génération de la vue
        $template = $this->getTwig()->load('inscription.html.twig');
        echo $template->render(
            array(
                'mail' => $mail,
                'existe' => $existe,
                'message' => $messages,
            )
        );
    }

    /**
     * Genere le twig de la page vide
     * @todo verifier si sous cote. Peut etre utilisé partout si envoi de param dans lien et $_GET
     * @param string|null $page web dont on veut generer le twig
     * @return void
     */
    public function genererVueVide(?string $page)
    {
        //Génération de la vue
        $template = $this->getTwig()->load($page . '.html.twig');
        echo $template->render(
            array()
        );
    }

    public function genererVueConnecte(?Utilisateur $utilisateur, ?array $messages)
    {
        if ($utilisateur !== null) {
            // Stockage en session et définition de la variable globale
            $utilisateur = Utilisateur::createWithCopy($utilisateur);
            $_SESSION['utilisateur'] = $utilisateur;
            $this->getTwig()->addGlobal('utilisateurGlobal', $utilisateur);
        }

        $template = $this->getTwig()->load('menu.html.twig');
        echo $template->render([
            'message' => $messages
        ]);
    }

    /**
     * Genere la vue de la connexion et créé une session utilisateur globale
     *
     * @param Array|null $tableau de messages d'erreurs ou de reussite
     * @return void
     */
    // Dans votre contrôleur ou gestionnaire de connexion
    public function genererVueConnexion(?array $message, ?Utilisateur $utilisateur): void
    {
        if ($utilisateur !== null) {
            // Stockage en session et définition de la variable globale
            $utilisateur = Utilisateur::createWithCopy($utilisateur);
            $_SESSION['utilisateur'] = $utilisateur;
            $this->getTwig()->addGlobal('utilisateurGlobal', $utilisateur);
        }

        $template = $this->getTwig()->load('connexion.html.twig');
        echo $template->render([
            'message' => $message
        ]);
    }

    /**
     * Listage de l'utilisateur ayant l'id 2
     * @todo après merge : vérifier si utilisée sinon supprimer
     * @return void
     */
    public function listerContacts()
    {
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $utilisateurs = $manager->find(2);
        $template = $this->getTwig()->load('creneauLibre.html.twig');
        echo $template->render(
            array(
                'res' => $utilisateurs,
            )
        );
    }

    /**
     * Listage de tous les utilisateurs
     * Redirection vers la page d'administration
     * @return void
     */
    public function lister(?array $tableauDErreurs = null)
    {
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $utilisateurs = $manager->findAll();
        $template = $this->getTwig()->load('administration.html.twig');
        echo $template->render(
            array(
                'listeUtilisateurs' => $utilisateurs,
                'message' => $tableauDErreurs,
            )
        );
    }

    /**
     * Fonction appellee par la corbeille pour supprimer un utilisateur (panel admin)
     * Ici, pas besoin de vérifier les paramètres car ils sont passés par un lien
     * @todo vérifier la ligne else {$this->deconnecter();}. Suspecte car si la personne n'est pas admin on se déconnecte
     * @return void
     */
    public function supprimer()
    {
        $id = $_GET['id'];
        $type = $_GET['type'];

        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $manager->supprimerUtilisateur($id);
        $this->lister();
    }

    /**
     * Fonction appellee par le bouton de mise a jour d'un utilisateur (panel admin)
     *
     * @return void
     * @todo Modifier utilisateur ne fonctionne plus. Why ? 
     */
    public function modifier()
    {
        $id = $_GET['id'];              // Pas de vérification d'id car transmis par le lien d'accès à la page
        $type = $_GET['type'];          // Pas de vérification du type car transmis par le lien d'accès à la page

        $messageErreurs = [];
        $nomValide = utilitaire::validerNom($_POST['nom'], $messageErreurs);
        $prenomValide = utilitaire::validerPrenom($_POST['prenom'], $messageErreurs);
        @$photoValide = utilitaire::validerPhoto($_FILES['photo'], $messageErreurs);

        if ($nomValide && $prenomValide) {//} && $roleValide) {
            $pdo = $this->getPdo();
            $manager = new UtilisateurDao($pdo);

            // Gestion de l'upload de la photo de profil
            $cheminPhoto = $_SESSION['utilisateur']->getPhotoDeProfil(); // Récupérer l'ancien chemin
            if ($photoValide) {
                $dossierDestination = 'image/photo_user/';
                $nomFichier = 'profil_' . $id . '_' . basename($_FILES['photo']['name']);
                $cheminPhoto = $dossierDestination . $nomFichier;

                // Déplacer le fichier uploadé dans le répertoire cible
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $cheminPhoto)) {
                    // Mettre à jour le chemin de la photo dans la base de données
                    $manager->modifierPhotoProfil($id, $nomFichier);
                    $_SESSION['utilisateur']->setPhotoDeProfil($nomFichier);
                }
            }

            $role = $_POST['role'];
            // Mise à jour du profil utilisateur
            if ($role == '1' || $role == 'Admin') {
                $role = true;
            } else {
                $role = false;
            }

            @$nomFichier == null ? $nomFichier = $_SESSION['utilisateur']->getPhotoDeProfil() : $nomFichier;
            $email = $_SESSION['utilisateur']->getEmail();
            $manager->modifierUtilisateur($id, $_POST['nom'], $_POST['prenom'], $email, $role, $nomFichier);
            $utilisateurTemporaire = $manager->find($id);
            $_SESSION['utilisateur'] = $utilisateurTemporaire;
            $this->getTwig()->addGlobal('utilisateurGlobal', $utilisateurTemporaire);
        }
        $this->afficherProfil($messageErreurs);
    }

    /**
     * Fonction appellee par le bouton de mise a jour d'un utilisateur (panel admin)
     *
     * @return void
     * @todo
     */
    public function modifierAdmin()
    {
        $id = $_GET['id'];              // Pas de vérification d'id car transmis par le lien d'accès à la page
        $type = $_GET['type'];          // Pas de vérification du type car transmis par le lien d'accès à la page

        $messageErreurs = [];
        $nomValide = utilitaire::validerNom($_POST['nom'], $messageErreurs);
        $prenomValide = utilitaire::validerPrenom($_POST['prenom'], $messageErreurs);
        $roleValide = utilitaire::validerRole($_POST['role'], $messageErreurs);
        $emailValide = utilitaire::validerEmail($_POST['email'], $messageErreurs);
        @$photoValide = utilitaire::validerPhoto($_FILES['photo'], $messageErreurs);

        if ($nomValide && $prenomValide && $roleValide) {
            $pdo = $this->getPdo();
            $manager = new UtilisateurDao($pdo);

            // Gestion de l'upload de la photo de profil
            $utilisateurConcerne = $manager->find($id);
            $cheminPhoto = $utilisateurConcerne->getPhotoDeProfil(); // Récupérer l'ancien chemin
            if ($photoValide) {
                $dossierDestination = 'image/photo_user/';
                $nomFichier = 'profil_' . $id . '_' . basename($_FILES['photo']['name']);
                $cheminPhoto = $dossierDestination . $nomFichier;

                // Déplacer le fichier uploadé dans le répertoire cible
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $cheminPhoto)) {
                    // Mettre à jour le chemin de la photo dans la base de données
                    $manager->modifierPhotoProfil($id, $nomFichier);
                    $utilisateurConcerne->setPhotoDeProfil($nomFichier);
                }
            }

            $role = $_POST['role'] == 'Admin' ? 1 : 0;
            
            // Mise à jour du chemin de l'image
            $nomFichier = empty($nomFichier) ? $utilisateurConcerne->getPhotoDeProfil() : $nomFichier;
            
            // Mise à jour du profil utilisateur
            $manager->modifierUtilisateur($id, $_POST['nom'], $_POST['prenom'], $_POST['email'], $role, $nomFichier);
            $utilisateurTemporaire = $manager->find($id);
            
            if ($utilisateurTemporaire->getId() == $_SESSION['utilisateur']->getId()) {
                $_SESSION['utilisateur'] = $utilisateurTemporaire;
                $this->getTwig()->addGlobal('utilisateurGlobal', $utilisateurTemporaire);
            }
        }
        $_SESSION['utilisateur']->getEstAdmin() == false ? $this->afficherProfil($messageErreurs) : $this->lister($messageErreurs);
    }


    /**
     * Affiche le profil de l'utilisateur connecté (page profil)
     *
     * @return void
     */
    public function afficherProfil(?array $messagesErreur = null): void
    {
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $utilisateur = $manager->getUserMail($_SESSION['utilisateur']->getEmail());
        $template = $this->getTwig()->load('profil.html.twig');
        echo $template->render(
            array(
                'utilisateur' => $utilisateur,
                'message' => $messagesErreur,
            )
        );
    }

    /**
     * Modifie le profil de l'utilisateur connecté (page modifier profil)
     *
     * @return void
     */
    public function modifierProfil()
    {
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $utilisateur = $manager->getUserMail($_SESSION['utilisateur']->getEmail());
        $template = $this->getTwig()->load('modifierProfil.html.twig');
        echo $template->render(
            array(
                'utilisateur' => $utilisateur,
            )
        );
    }

    /**
     * Fonction de réinitialisation du mot de passe
     * Envoie du mail de réinitialisation à l'utilisateur
     * @return void
     */
    public function demandeReinitialisation() {
        $tableauErreurs = [];
        $emailValide = utilitaire::validerEmail($_POST['email'], $tableauErreurs);

        if ($emailValide) {
            $manager = new UtilisateurDAO($this->getPdo());
            $utilisateur = $manager->getObjetUtilisateur($_POST['email']);
            $token = $utilisateur->genererTokenReinitialisation();
            $manager->miseAJourUtilisateur($utilisateur);

            // En-têtes du mail
            $headers = "From: no-reply@timeharmony.com\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            $sujet = "Reinitialisation de votre mot de passe";
            $destinataire = $_POST['email'];
            $lien = "http://lakartxela.iutbayonne.univ-pau.fr/~tlatxague/TimeHarmony/index.php?controleur=utilisateur&methode=mailRecu&token=$token&email=$destinataire";

            // Corps du message (format HTML)
            $message = "
            <html>
                <head>
                    <title>$sujet</title>
                </head>
                <body>
                    <h3>Bonjour $destinataire,</h3>
                    <p>Vous avez fait une demandé de réinitialisation de votre mot de passe</p> <br>
                    <p>Pour cela, cliquez sur le lien ci-dessous et suivez les instructions :</p>
                    <p>
                        <a href='$lien' style='color: #1a0dab; font-size: 16px; text-decoration: none;'>Accéder au site</a>
                    </p>
                    <p>Merci et à bientôt !</p>
                </body>
            </html>";

            if (mail($destinataire, $sujet, $message, $headers)) {
                $messageErreur[] = "L'e-mail a été envoyé avec succès à $destinataire.";
            } else {
                $messageErreur[] = "Erreur : L'e-mail n'a pas pu être envoyé.";
            }

            $template = $this->getTwig()->load('connexion.html.twig');
            echo $template->render(array('message' => $messageErreur));
        }
    }

    /**
     * Fonction qui génère la page de réinitialisation du mot de passe
     * On y passe un booléen qui, à false, affichera l'input du mail relié au compte qui demande la réinitialisation
     * @return void
     */
    public function demanderReinitialisationMail(){
        $template = $this->getTwig()->load('reinitialisationMdp.html.twig');
        echo $template->render(
            array(
                'reinitialise' => true,
            )
        );
    }

    /**
     * Fonction qui génère la page de réinitialisation du mot de passe
     * On y passe un booléen qui, à true, affichera l'input des 2 mots de passe pour la réinitialisation
     * On envoie également l'email du destinataire pour lequel on réinitialise le mot de passe
     * On récupère le mail passé dans le lien d'accès
     * @return void
     */
    public function mailRecu() {
        $token = $_GET['token'];
        $email = $_GET['email'];
        $manager = new UtilisateurDAO($this->getPdo());
        $tokenUtilisateur = $manager->getObjetUtilisateur($email);

        if($tokenUtilisateur == $token) {
            $template = $this->getTwig()->load('reinitialisationMdp.html.twig');
            echo $template->render(
                array(
                    'reinitialise' => false,
                    'email' => $email,
                )
            );
        } else {
            $template = $this->getTwig()->load('connexion.html.twig');
            echo $template->render(
                array(
                )
            );
        }
    }

    /**
     * Fonction qui vérifie l'input des 2 mots de passes et change le mot de passe de l'utilisateur dans la BD
     * Quand réinitialisation mdp, deconnexion puis redirection vers page connexion pour se reconnecter
     * @todo refaire fonction deconnexion pour qu'elle renvoie sur la page de connexion
     * @return void
     */
    public function reinitialiserMotDePasse() {
        $tableauErreurs = [];
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $utilisateur = $manager->getObjetUtilisateur($_GET['email']);

        // Cette methode verifie la valeur des champs et si les mdp sont les memes 
        $mdpValide = utilitaire::validerMotDePasseInscription($_POST['pwd'], $tableauErreurs, $_POST['pwdConfirme']);

        if ($mdpValide) {
            /**
             * @todo mauvais pressentiment. Verifier le fonctionnement
             */
            $utilisateur->setTokenReinitialisation(null);
            $utilisateur->setDateExpirationToken(null);
            $manager->miseAJourUtilisateur($utilisateur);

            $mdpHache = password_hash($_POST['pwd'], PASSWORD_DEFAULT);
            $manager->reinitialiserMotDePasse($utilisateur->getId(), $mdpHache);
            $this->getTwig()->addGlobal('utilisateurGlobal', null);
            unset($_SESSION['utilisateur']);
            $this->genererVueConnexion($tableauErreurs, null);
        } else {
            $template = $this->getTwig()->load('profil.html.twig'); // Generer la page de réinitialisation mdp avec tableau d'erreurs
            echo $template->render(array('message' => $tableauErreurs, 'reinitialise' => true));
        }
    }
}
