<?php

use Twig\Profiler\Dumper\BaseDumper;

require_once 'include.php';

/**
 * @author Thibault Latxague
 * @brief Controller de la page des utilisateur
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

        $emailValide = utilitaire::validerEmail(htmlspecialchars($_POST['email']), $tableauErreurs);
        $nomValide = utilitaire::validerNom(htmlspecialchars($_POST['nom']), $tableauErreurs);
        $prenomValide = utilitaire::validerPrenom(htmlspecialchars($_POST['prenom']), $tableauErreurs);
        $mdpValide = utilitaire::validerMotDePasseInscription(htmlspecialchars($_POST['pwd']), $tableauErreurs, $_POST['pwdConfirme']);

        if ($emailValide && $nomValide && $prenomValide && $mdpValide) {
            $email = htmlspecialchars($_POST['email']);
            $nom = htmlspecialchars($_POST['nom']);
            $prenom = htmlspecialchars($_POST['prenom']);
            $mdp = htmlspecialchars($_POST['pwd']);

            $manager = new UtilisateurDao($pdo); //Lien avec PDO
            /**
             * Verifie que l'utilisateur n'existe pas.
             * On hash le mdp, on crée un nouvel utilisateur et on l'ajoute dans la bd
             */
            $utilisateurExiste = $manager->findMail($email);
            if (!$utilisateurExiste) {
                $mdpHache = password_hash($mdp, PASSWORD_DEFAULT);
                $nouvelUtilisateur = Utilisateur::createAvecParam(null, $nom, $prenom, $email, $mdpHache, "utilisateurBase.png", false);
                // $tokenActivation = $nouvelUtilisateur->genererTokenActivationCompte();
                // $nouvelUtilisateur->setCompteEstActif(false); // On peut supprimer car par défaut, la valeur est desactive
                $manager->ajouterUtilisateur($nouvelUtilisateur);
                $this->envoyerMailActivationCompte($nouvelUtilisateur->getEmail());
                $tableauErreurs[] = "Inscription réussie !";
            } else {
                // Si l'utilisateur existe deja
                $tableauErreurs[] = "L'utilisateur existe déjà ! Connectez-vous !";
            }
        } else {
            $manager = new UtilisateurDao($pdo); //Lien avec PDO
            $email = htmlspecialchars($_POST['email']);
            $utilisateurExiste = $manager->findMail($email);
            if ($utilisateurExiste) {
                $tableauErreurs[] = "L'utilisateur existe déjà ! Connectez-vous !";
            }
        }
        // Affichage de la page avec les erreurs ou le message de succès. Utilisation du @ car si l'utilisateur n'a pas renseigné de mail, cette action génère une erreur
        $email = htmlspecialchars($_POST['email']);
        @$this->genererVue($email, null, $tableauErreurs);
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
        $emailValide = utilitaire::validerEmail(htmlspecialchars($_POST['email']), $tableauErreurs);
        $passwdValide = utilitaire::validerMotDePasseInscription(htmlspecialchars($_POST['pwd']), $tableauErreurs);

        $manager = new UtilisateurDao($pdo);
        $compteUtilisateurCorrespondant = $manager->getObjetUtilisateur(htmlspecialchars($_POST['email']));

        if($compteUtilisateurCorrespondant == null) {
            // Échec de validation des entrées
            $tableauErreurs[] = "Aucun compte avec cette adresse mail n'existe";
            $this->genererVueConnexion($tableauErreurs, null);
        } else {
            $compteActif = $compteUtilisateurCorrespondant->getStatutCompte() != "desactive";
            if ($emailValide && $passwdValide && $compteActif) {
                // Reactivation compte
                $compteUtilisateurCorrespondant->reactiverCompte();

                $email = htmlspecialchars($_POST['email']);
                $pwd = htmlspecialchars($_POST['pwd']);
                
                // On recupere un tuple avec un booleen et le mdp hache
                $motDePasse = $manager->connexionReussie($email);
    
                if ($compteUtilisateurCorrespondant->getStatutCompte() === "actif") {
                    if ($motDePasse[0] && password_verify($pwd, $motDePasse[1])) {
                        // Connexion réussie
                        $utilisateur = $manager->getObjetUtilisateur($email);
                        $utilisateur->getDemandes();
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
                $tableauErreurs[] = "Le compte n'a pas été activé";
                $this->genererVueConnexion($tableauErreurs, null);
            }
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
        // @ Car nous n'utilisons pas tout le temps les pages ce qui peut entrainer des erreurs
        @$page = $_GET['page'];
        $this->getTwig()->addGlobal('utilisateurGlobal', null);
        unset($_SESSION['utilisateur']);
        if ($page != null) {$this->genererVueVide($page);}
        //$this->genererVueVide('index');
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
     * 
     * @param array|null $tableauDErreurs tableau des erreurs
     * @param bool|null $contientErreurs true si le tableau contient des erreurs, false sinon
     * @return void
     */
    public function lister(?array $tableauDErreurs = null, ?bool $contientErreurs = false)
    {
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $utilisateurs = $manager->findAll();
        $utilisateurCourant = $_SESSION['utilisateur'];
        if($utilisateurCourant->getEstAdmin()) {
            $template = $this->getTwig()->load('administration.html.twig');
            echo $template->render(
                array(
                    'listeUtilisateurs' => $utilisateurs,
                    'message' => $tableauDErreurs,
                    'utilisateurCourant' => $utilisateurCourant,
                    'contientErreurs' => $contientErreurs
                )
            );
        }
        else {
            $this->deconnecter();
            $this->genererVueVide('connexion');
        }
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
        $utilisateurSupprime = $manager->find($id);
        $message[] = "L'utilisateur " . $utilisateurSupprime->getNom() . " " . $utilisateurSupprime->getPrenom() . " a été supprimé avec succès !";
        $manager->supprimerUtilisateur($id);
        if($id == $_SESSION['utilisateur']->getId()) {
            $this->deconnecter();
            $this->genererVueVide('index');
        } else {
            $this->lister($message, false);
        }
    }

    /**
     * Fonction appellee par le bouton de mise a jour d'un utilisateur (panel admin)
     *
     * @return void
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
            $messageErreurs[] = "L'utilisateur " . $utilisateurTemporaire->getNom() . " " . $utilisateurTemporaire->getPrenom() . " a été modifié avec succès !";
            $this->afficherProfil($messageErreurs, false);
        } else {
            $this->afficherProfil($messageErreurs, true);
        }
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
        $statutValide = utilitaire::validerStatut($_POST['statut'], $messageErreurs);
        @$photoValide = utilitaire::validerPhoto($_FILES['photo'], $messageErreurs);

        if ($nomValide && $prenomValide && $roleValide && $emailValide && $statutValide) {
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
            $manager->modifierUtilisateur($id, $_POST['nom'], $_POST['prenom'], $_POST['email'], $role, $nomFichier, strtolower($_POST['statut']));
            $utilisateurTemporaire = $manager->find($id);
            
            if ($utilisateurTemporaire->getId() == $_SESSION['utilisateur']->getId() && strtolower($_POST['statut']) == 'actif') {
                $_SESSION['utilisateur'] = $utilisateurTemporaire;
                $this->getTwig()->addGlobal('utilisateurGlobal', $utilisateurTemporaire);
            } elseif ($utilisateurTemporaire->getId() == $_SESSION['utilisateur']->getId()) {
                $this->deconnecter();
                $this->genererVueVide('index');
            }

            $messageErreurs[] = "L'utilisateur " . $utilisateurTemporaire->getNom() . " " . $utilisateurTemporaire->getPrenom() . " a été modifié avec succès !";
            $this->lister($messageErreurs, false);
        } else {
            $this->lister($messageErreurs, true);
        }
        // if(isset($_SESSION['utilisateur'])) {
        //     $_SESSION['utilisateur']->getEstAdmin() == false ? $this->afficherProfil($messageErreurs, false) : $this->lister($messageErreurs, false);
        // }
    }


    /**
     * Affiche le profil de l'utilisateur connecté (page profil)
     *
     * @param array|null $messagesErreur tableau des erreurs
     * @param bool|null $contientErreurs true si le tableau contient des erreurs, false sinon
     * @return void
     */
    public function afficherProfil(?array $messagesErreur = null, ?bool $contientErreurs = false): void
    {
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $utilisateur = $manager->getUserMail($_SESSION['utilisateur']->getEmail());
        $template = $this->getTwig()->load('profil.html.twig');
        echo $template->render(
            array(
                'utilisateur' => $utilisateur,
                'message' => $messagesErreur,
                'contientErreurs' => $contientErreurs
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
                    <p>Vous avez fait une demande de réinitialisation de votre mot de passe</p>
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
                $messageErreur[] = "Erreur : L'e-mail n'a pas pu être envoyé à $destinataire.";
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
        $this->deconnecter();
        $token = $_GET['token'];
        $email = $_GET['email'];
        $manager = new UtilisateurDAO($this->getPdo());
        $utilisateur = $manager->getObjetUtilisateur($email);
        $tokenUtilisateur = $utilisateur->getTokenReinitialisation();
        $dateExpiration = $utilisateur->getDateExpirationToken();
        $dateActuelle = new DateTime();
        $tableauMessages = [];

        if($tokenUtilisateur == $token && $email == $utilisateur->getEmail() && $dateExpiration >= $dateActuelle) {
            $template = $this->getTwig()->load('reinitialisationMdp.html.twig');
            echo $template->render(
                array(
                    'reinitialise' => false,
                    'email' => $email,
                )
            );
        } else {
            $tableauMessages[] = "Votre tentative de réinitialisation de mot de passea échouée";
            $template = $this->getTwig()->load('connexion.html.twig');
            echo $template->render(
                array(
                    'message' => $tableauMessages,
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
        $mdpValide = utilitaire::validerMotDePasseInscription(htmlspecialchars($_POST['pwd']), $tableauErreurs, htmlspecialchars($_POST['pwdConfirme']));

        if ($mdpValide) {
            $utilisateur->setTokenReinitialisation(null);
            $utilisateur->setDateExpirationToken(null);
            $manager->miseAJourUtilisateur($utilisateur);

            $mdpHache = password_hash(htmlspecialchars($_POST['pwd']), PASSWORD_DEFAULT);
            $manager->reinitialiserMotDePasse($utilisateur->getId(), $mdpHache);
            $this->getTwig()->addGlobal('utilisateurGlobal', null);
            unset($_SESSION['utilisateur']);
            $tableauErreurs[] = "Votre mot de passe a été réinitialisé avec succès ! Reconnectez-vous !";
            $this->genererVueConnexion($tableauErreurs, null);
        } else {
            $template = $this->getTwig()->load('reinitialisationMdp.html.twig');
            echo $template->render(
                array(
                    'reinitialise' => false,
                    'message' => $tableauErreurs,
                    'email' => $_GET['email'],
                    'token' => $_GET['token']
                )
            );
        }
    }

    /**
     * Fonction qui active le compte de l'utilisateur
     * @return void
     */
    public function activerCompte(): void {
        $tableauMessages = [];
        $token = $_GET['token'];
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $utilisateur = $manager->getObjetUtilisateur($_GET['email']);
        $tokenUtilisateur = $utilisateur->getTokenActivationCompte();
        $emailUtilisateur = $utilisateur->getEmail();
        $dateExpiration = $utilisateur->getDateExpirationTokenActivationCompte();
        $dateActuelle = new DateTime();

        if($tokenUtilisateur == $token && $emailUtilisateur == $_GET['email'] && $dateExpiration >= $dateActuelle) {
            $utilisateur->setStatutCompte("actif");
            $utilisateur->setTokenActivationCompte(null);
            $utilisateur->setDateExpirationTokenActivationCompte(null);
            $manager->miseAJourUtilisateur($utilisateur);
            $tableauMessages[] = "Votre compte a été validé avec succès, tentez de vous connecter !";
            $template = $this->getTwig()->load('connexion.html.twig');
            echo $template->render(
                array(
                    'message' => $tableauMessages,
                    'email' => $emailUtilisateur,
                )
            );
        } else {
            $tableauMessages[] = "Une erreur est survenue lors de l'activation de votre compte";
            $template = $this->getTwig()->load('connexion.html.twig');
            echo $template->render(
                array(
                    'message' => $tableauMessages,
                )
            );
        }
    }

    /**
     * Fonction qui envoie un mail d'activation de compte à l'utilisateur
     * @param string|null $email de l'utilisateur
     * @return void
     */
    public function envoyerMailActivationCompte(?string $email): void {
        $tableauErreurs = [];

        $manager = new UtilisateurDAO($this->getPdo());
        $utilisateur = $manager->getObjetUtilisateur($email);
        $token = $utilisateur->genererTokenActivationCompte();
        $utilisateur->setTokenActivationCompte($token);
        $manager->miseAJourUtilisateur($utilisateur);

        // En-têtes du mail
        $headers = "From: no-reply@timeharmony.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        $sujet = "Activation de votre compte";
        $destinataire = $email;
        $lien = "http://lakartxela.iutbayonne.univ-pau.fr/~tlatxague/TimeHarmony/index.php?controleur=utilisateur&methode=activerCompte&token=$token&email=$destinataire";

        // Corps du message (format HTML)
        $message = "
        <html>
            <head>
                <title>$sujet</title>
            </head>
            <body>
                <h3>Bonjour $destinataire,</h3>
                <p>Vous avez fait une demande de création de compte sur notre site TimeHarmony</p>
                <p>Pour cela, cliquez sur le lien ci-dessous et suivez les instructions :</p>
                <p>
                    <a href='$lien' style='color: #1a0dab; font-size: 16px; text-decoration: none;'>Accéder au site</a>
                </p>
                <p>Merci et à bientôt !</p>
            </body>
        </html>";

        // On met un @ car sur localhost, pas de serveur de mail
        if (@mail($destinataire, $sujet, $message, $headers)) {
            $messageErreur[] = "L'e-mail de confirmation de création de compte a été envoyé avec succès à $destinataire.";
        } else {
            $messageErreur[] = "Erreur : L'e-mail n'a pas pu être envoyé à $destinataire.";
        }

        $template = $this->getTwig()->load('connexion.html.twig');
        echo $template->render(array('message' => $messageErreur));
    }
}
