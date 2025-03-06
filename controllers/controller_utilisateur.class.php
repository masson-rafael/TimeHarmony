<?php

use LDAP\Result;
use Twig\Profiler\Dumper\BaseDumper;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'include.php';

/**
 * @author Thibault Latxague
 * @brief Controller de la page des utilisateur
 * @version 0.1
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
        $mailEnvoye = true;

        $emailValide = utilitaire::validerEmail(htmlspecialchars($_POST['email']), $tableauErreurs);
        $nomValide = utilitaire::validerNom(htmlspecialchars($_POST['nom'],ENT_NOQUOTES), $tableauErreurs);
        $prenomValide = utilitaire::validerPrenom(htmlspecialchars($_POST['prenom'],ENT_NOQUOTES), $tableauErreurs);
        $mdpValide = utilitaire::validerMotDePasseInscription(htmlspecialchars($_POST['pwd'],ENT_NOQUOTES), $tableauErreurs, $_POST['pwdConfirme']);

        if ($emailValide && $nomValide && $prenomValide && $mdpValide) {
            $email = htmlspecialchars($_POST['email']);
            $nom = htmlspecialchars($_POST['nom'],ENT_NOQUOTES);
            $prenom = htmlspecialchars($_POST['prenom'],ENT_NOQUOTES);
            $mdp = htmlspecialchars($_POST['pwd'],ENT_NOQUOTES);

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
                $envoieMail = $this->envoyerMailActivationCompte($nouvelUtilisateur->getEmail());
                if($envoieMail[1]) {
                    $tableauErreurs[] = "Un mail d'activation de votre compte a été envoyé !";
                    $this->genererVueConnexion($tableauErreurs, null, false);
                } else {
                    $tableauErreurs[] = $envoieMail[0][0];
                    $user = $manager->getObjetUtilisateur($email);
                    $manager->supprimerUtilisateur($user->getId());
                    $this->genererVue($email, $utilisateurExiste, $tableauErreurs, true);
                }
            } else {
                // Si l'utilisateur existe deja
                $tableauErreurs[] = "L'utilisateur existe déjà ! Connectez-vous !";
                $this->genererVue($email, $utilisateurExiste, $tableauErreurs, true);
            }
        } else {
            $manager = new UtilisateurDao($pdo); //Lien avec PDO
            $email = htmlspecialchars($_POST['email']);
            $utilisateurExiste = $manager->findMail($email);
            if ($utilisateurExiste) {
                $tableauErreurs[] = "L'utilisateur existe déjà ! Connectez-vous !";
            }
            $this->genererVue($email, null, $tableauErreurs, true);
        }
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
        $passwdValide = utilitaire::validerMotDePasseInscription(htmlspecialchars($_POST['pwd'],ENT_NOQUOTES), $tableauErreurs);

        $manager = new UtilisateurDao($pdo);
        $compteUtilisateurCorrespondant = $manager->getObjetUtilisateur(htmlspecialchars($_POST['email']));

        if($compteUtilisateurCorrespondant == null) {
            // Échec de validation des entrées
            $tableauErreurs[] = "Aucun compte avec cette adresse mail n'existe. Essayez de créer un compte";
            $this->genererVueConnexion($tableauErreurs, null, true);
        } else {
            $compteActif = $compteUtilisateurCorrespondant->getStatutCompte() != "desactive";
            if ($emailValide && $passwdValide && $compteActif) {
                // Reactivation compte
                $compteUtilisateurCorrespondant->reactiverCompte();

                $email = htmlspecialchars($_POST['email']);
                $pwd = htmlspecialchars($_POST['pwd'],ENT_NOQUOTES);
                
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
                        $compteUtilisateurCorrespondant->setDateDerniereConnexion(new DateTime());
                        $manager->miseAJourUtilisateur($compteUtilisateurCorrespondant);
                        $this->genererVueConnecte($utilisateur, $tableauErreurs);
                    } else {
                        // Échec de connexion - mauvais mot de passe
                        $tableauErreurs[] = "Mot de passe incorrect. Essayez de réinitialisez votre mot de passe";
                        $compteUtilisateurCorrespondant->gererEchecConnexion();
                        $manager->miseAJourUtilisateur($compteUtilisateurCorrespondant);
                        $this->genererVueConnexion($tableauErreurs, null, true);
                    }
                } else {
                    // Compte inactif
                    $tableauErreurs[] = "Votre compte est bloqué. Temps restant avec deblocage : " . 
                        (string) abs($compteUtilisateurCorrespondant->tempsRestantAvantReactivationCompte()) . " secondes.";
                    $manager->miseAJourUtilisateur($compteUtilisateurCorrespondant);
                    $this->genererVueConnexion($tableauErreurs, null, true);
                }
            } else {
                // Échec de validation des entrées
                $tableauErreurs[] = "Le compte n'a pas été activé";
                $this->genererVueConnexion($tableauErreurs, null, true);
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
    public function genererVue(?string $mail, ?bool $existe, ?array $messages, ?bool $contientErreurs = false)
    {
        //Génération de la vue
        $template = $this->getTwig()->load('inscription.html.twig');
        echo $template->render(
            array(
                'mail' => $mail,
                'existe' => $existe,
                'message' => $messages,
                'contientErreurs' => $contientErreurs
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
            $_SESSION['utilisateur'] = $utilisateur->getId();

            $utilisateurCourant = new Utilisateur(
                $utilisateur->getId(),
                $utilisateur->getNom(),
                $utilisateur->getPrenom(),
                $utilisateur->getEmail(),
                null,
                $utilisateur->getPhotoDeProfil(),
                $utilisateur->getEstAdmin(),
                $utilisateur->getDateDerniereConnexion());

            $this->getTwig()->addGlobal('utilisateurGlobal', $utilisateurCourant);
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
    public function genererVueConnexion(?array $message, ?Utilisateur $utilisateur, ?bool $contientErreurs): void
    {
        if ($utilisateur !== null) {
            // Stockage en session et définition de la variable globale
            $utilisateur = Utilisateur::createWithCopy($utilisateur);
            $_SESSION['utilisateur'] = $utilisateur->getId();

            $utilisateurCourant = new Utilisateur(
                $utilisateur->getId(),
                $utilisateur->getNom(),
                $utilisateur->getPrenom(),
                $utilisateur->getEmail(),
                null,
                $utilisateur->getPhotoDeProfil(),
                $utilisateur->getEstAdmin(),
                $utilisateur->getDateDerniereConnexion());

            $this->getTwig()->addGlobal('utilisateurGlobal', $utilisateurCourant);
        }

        $template = $this->getTwig()->load('connexion.html.twig');
        echo $template->render([
            'message' => $message,
            'contientErreurs' => $contientErreurs
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
     * Fonction permettant d'afficher le twig correspondant à la page des notifications
     * @return void
     */
    public function afficherPageNotifications(?array $tableauMessage = null, ?bool $contientErreurs = false): void {
        /**
         * Step 1 : Appel de la fonction qui trouve ET RENVOIE les contacts que j'ai envoyé
         * Step 2 : Appel de la fonction qui trouve ET RENVOIE les demandes de contact d'autres utilisateurs
         * Step 4 : Affichage du twig
         */
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $demandesContactEnvoyees = $manager->getDemandesContactEnvoyees($_SESSION['utilisateur']);
        $demandesContactRecues = $manager->getDemandesContactRecues($_SESSION['utilisateur']);

        $template = $this->getTwig()->load('notifications.html.twig');
        echo $template->render(array(
            'demandesContactEnvoyees' => $demandesContactEnvoyees,
            'demandesContactRecues' => $demandesContactRecues,
            'message' => $tableauMessage,
            'contientErreurs' => $contientErreurs
        ));
    }

    
    /**
     * Fonction qui supprime la demande de contact dans la BD
     * @return void
     */
    public function supprimerDemandeContactEmise(): void {
        $idReceveur = $_GET['id'];
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $utilisateurEnvoieDemande = $manager->find($idReceveur);
        $tabDemandesPourMoi = $manager->supprimerDemandeContactEnvoyee($_SESSION['utilisateur'], $idReceveur);
        $tableauMessages[] = "Demande de " . $utilisateurEnvoieDemande->getNom() . " " . $utilisateurEnvoieDemande->getPrenom() . " supprimée avec succès !";
        $this->afficherPageNotifications($tableauMessages, false);
    }

    /**
     * Fonction qui refuse la demande de contact dans la BD
     * @return void
     */
    public function refuserDemandeContactRecue(): void {
        $idReceveur = $_GET['id'];
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $tabDemandesPourMoi = $manager->refuserDemandeContact($_SESSION['utilisateur'], $idReceveur);
        $utilisateur = $manager->getObjetUtilisateur($_SESSION['utilisateur']->getEmail());
        $utilisateur->getDemandes();
        $manager->miseAJourUtilisateur($utilisateur);
        $_SESSION['utilisateur'] = $utilisateur;
        $utilisateurEnvoieDemande = $manager->find($idReceveur);

        $utilisateurCourant = new Utilisateur(
            $utilisateur->getId(),
            $utilisateur->getNom(),
            $utilisateur->getPrenom(),
            $utilisateur->getEmail(),
            null,
            $utilisateur->getPhotoDeProfil(),
            $utilisateur->getEstAdmin(),
            $utilisateur->getDateDerniereConnexion());

        $this->getTwig()->addGlobal('utilisateurGlobal', $utilisateurCourant);
        $tableauMessages[] = "Demande de " . $utilisateurEnvoieDemande->getNom() . " " . $utilisateurEnvoieDemande->getPrenom() . " refusée avec succès !";
        $this->afficherPageNotifications($tableauMessages, false);
    }

    /**
     * Fonction qui accepte la demande de contact dans la BD
     * @return void
     */
    public function accepterDemandeContactRecue(): void {
        $idReceveur = $_GET['id'];
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $tabDemandesPourMoi = $manager->accepterDemandeContact($idReceveur, $_SESSION['utilisateur']);
        $utilisateur = $manager->getObjetUtilisateur($_SESSION['utilisateur']->getEmail());
        $utilisateur->getDemandes();
        $manager->miseAJourUtilisateur($utilisateur);
        $utilisateurEnvoieDemande = $manager->find($idReceveur);
        $_SESSION['utilisateur'] = $utilisateur;

        $utilisateurCourant = new Utilisateur(
            $utilisateur->getId(),
            $utilisateur->getNom(),
            $utilisateur->getPrenom(),
            $utilisateur->getEmail(),
            null,
            $utilisateur->getPhotoDeProfil(),
            $utilisateur->getEstAdmin(),
            $utilisateur->getDateDerniereConnexion());

        $this->getTwig()->addGlobal('utilisateurGlobal', $utilisateurCourant);
        $tableauMessages[] = "Demande de " . $utilisateurEnvoieDemande->getNom() . " " . $utilisateurEnvoieDemande->getPrenom() . " acceptée avec succès !";
        $this->afficherPageNotifications($tableauMessages, false);
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

        $utilisateurCourant = $manager->find($_SESSION['utilisateur']);

        // $utilisateurCourant = $_SESSION['utilisateur'];
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
        if($id == $_SESSION['utilisateur']) {
            $this->deconnecter();
            $this->genererVueVide('index');
        } else {
            $this->lister($message, false);
        }
    }

    /**
     * Fonction appellee par la modification d'un utilisateur (profil ou panel admin)
     * @return void
     */
    public function modifierUtilisateur(): void {
        $id = $_GET['id'];
        $type = $_GET['type'];

        // Ajout des @ car notre fonction est utilisée par user classique et admin
        $messageErreurs = [];
        $messageErreursAdmin = [];
        $nomValide = utilitaire::validerNom($_POST['nom'], $messageErreurs);
        $prenomValide = utilitaire::validerPrenom($_POST['prenom'], $messageErreurs);
        @$roleValide = utilitaire::validerRole($_POST['role'], $messageErreursAdmin);
        @$emailValide = utilitaire::validerEmail($_POST['email'], $messageErreursAdmin);
        @$statutValide = utilitaire::validerStatut($_POST['statut'], $messageErreursAdmin);
        @$photoValide = utilitaire::validerPhoto($_FILES['photo'], $messageErreurs);

        if($nomValide && $prenomValide) {
            $pdo = $this->getPdo();
            $manager = new UtilisateurDao($pdo);
            
            // Gestion de l'upload de la photo de profil
            $utilisateurConcerne = $manager->find($id);
            $cheminPhoto = $utilisateurConcerne->getPhotoDeProfil(); // Récupérer l'ancien chemin

            if ($photoValide) {
                $dossierDestination = 'image/photo_user/';
                $nomFichier = 'profil_' . $id . '_' . basename($_FILES['photo']['name']);
                $cheminPhoto = $dossierDestination . $nomFichier;

                $user = $manager->find($id);
                $user->supprimerAnciennesPhotos();

                // Déplacer le fichier uploadé dans le répertoire cible
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $cheminPhoto)) {
                    // Mettre à jour le chemin de la photo dans la base de données
                    $manager->modifierPhotoProfil($id, $nomFichier);
                    $utilisateurConcerne->setPhotoDeProfil($nomFichier);
                }
            }

            // Mise à jour du chemin de l'image
            $nomFichier = empty($nomFichier) ? $utilisateurConcerne->getPhotoDeProfil() : $nomFichier;
            
            if ($roleValide && $emailValide && $statutValide) {
                $role = $_POST['role'] == 'Admin' ? 1 : 0;
                $manager->modifierUtilisateur($id, $_POST['nom'], $_POST['prenom'], $_POST['email'], $role, $nomFichier, strtolower($_POST['statut']));
            } else {
                $role = $utilisateurConcerne->getEstAdmin();
                $manager->modifierUtilisateur($id, $_POST['nom'], $_POST['prenom'], $utilisateurConcerne->getEmail(), $role, $nomFichier); 
            }

            $utilisateurConcerne = $manager->find($id);
            $utilisateur = $manager->find($_SESSION['utilisateur']);
            if ($utilisateurConcerne->getId() == $utilisateur->getId() && $utilisateurConcerne->getStatutCompte() == 'actif') {
                $_SESSION['utilisateur'] = $utilisateurConcerne->getId();

                $utilisateurCourant = new Utilisateur(
                    $utilisateurConcerne->getId(),
                    $utilisateurConcerne->getNom(),
                    $utilisateurConcerne->getPrenom(),
                    $utilisateurConcerne->getEmail(),
                    null,
                    $utilisateurConcerne->getPhotoDeProfil(),
                    $utilisateurConcerne->getEstAdmin(),
                    $utilisateurConcerne->getDateDerniereConnexion());

                $this->getTwig()->addGlobal('utilisateurGlobal', $utilisateurCourant);
                $messageErreurs[] = "L'utilisateur " . $utilisateurConcerne->getNom() . " " . $utilisateurConcerne->getPrenom() . " a été modifié avec succès !";
                $this->afficherProfil($messageErreurs, false);
            } else {
                $messageErreurs[] = "L'utilisateur " . $utilisateurConcerne->getNom() . " " . $utilisateurConcerne->getPrenom() . " a été modifié avec succès !";
                $messageErreurs = array_merge($messageErreurs, $messageErreursAdmin);
                $this->lister($messageErreurs, false);
            }
        } else {
            $messageErreurs = array_merge($messageErreurs, $messageErreursAdmin);
            $this->lister($messageErreurs, true);
        }
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
        $utilisateur = $manager->find($_SESSION['utilisateur']);
        // $utilisateur = $manager->getUserMail($_SESSION['utilisateur']->getEmail());
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
            $template = $this->getTwig()->load('connexion.html.twig');
            $destinataire = $_POST['email'];

            // Création de l'objet mail
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = MAIL_ADDRESS;
            $mail->Password = MAIL_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Cryptage TLS
            $mail->Port = 587; // Port 587 pour TLS            
            $mail->setFrom(MAIL_ADDRESS, 'TimeHarmony');
            $mail->addAddress($destinataire);
            $mail->isHTML(true);

            // Ajout du contenu du mail
            $sujet = "Reinitialisation de votre mot de passe";
            $mail->Subject = $sujet;

            // En-têtes du mail
            if(DB_HOST != 'localhost') {
                $lien = "http://lakartxela.iutbayonne.univ-pau.fr/~tlatxague/TimeHarmony/index.php?controleur=utilisateur&methode=mailRecu&token=$token&email=$destinataire";
            } else {
                $lien = "http://localhost/TimeHarmony/index.php?controleur=utilisateur&methode=mailRecu&token=$token&email=$destinataire";
            }

            // Corps du message (format HTML)
            $message = "
            <html>
                <head>
                    <title>$sujet</title>
                </head>
                <body style='background-color: #f6f6f6; font-family: Arial, sans-serif; text-align: center; padding: 20px;'>
                    
                    <table align='center' width='100%' cellspacing='0' cellpadding='0' border='0'>
                        <tr>
                            <td align='center'>
                                <table width='500' cellspacing='0' cellpadding='0' border='0' style='background: #F5F5DC; padding: 20px; border-radius: 8px;'>
                                    <tr>
                                        <td align='center' style='background-color: #64a19d; padding: 20px; border-radius: 5px;'>
                                            <img src='https://i.imgur.com/V6Rf8oL.png' alt='Logo TimeHarmony' style='width: 100px; margin-bottom: 10px; display: block;'>
                                            <h2 style='color: #f6f6f6; margin: 0;'>TimeHarmony</h2>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align='center' style='padding: 20px;'>
                                            <h3 style='color: #64a19d;'>Bonjour $destinataire,</h3>
                                            <p style='color: #64a19d; white-space: pre-line;'>Vous avez fait une demande de réinitialisation de votre mot de passe.</p>
                                            <p style='color: #64a19d; white-space: pre-line;'>Pour cela, cliquez sur le bouton ci-dessous et suivez les instructions :</p>
                                            <a href='$lien' style='display: inline-block; background-color: #64a19d; color: #f6f6f6; padding: 12px 20px; border-radius: 5px; font-size: 16px; text-decoration: none; margin-top: 10px;'>Réinitialiser le mot de passe</a>
                                            <p style='margin-top: 20px; font-size: 14px; color: #bbbbbb;'>Si vous n'êtes pas à l'origine de cette demande, ignorez ce message.</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </body>
            </html>";

            $mail->Body = $message;

            if ($mail->send()) {
                $messageErreur[] = "L'e-mail a été envoyé avec succès à $destinataire.";
                $this->deconnecter();
            } else {
                $messageErreur[] = "Erreur : L'e-mail n'a pas pu être envoyé à $destinataire.";
            }

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
            $this->genererVueConnexion($tableauErreurs, null, false);
        } else {
            $template = $this->getTwig()->load('reinitialisationMdp.html.twig');
            echo $template->render(
                array(
                    'reinitialise' => false,
                    'message' => $tableauErreurs,
                    'email' => $_GET['email'],
                    'token' => $_GET['token'],
                    'contientErreurs' => true,
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
                    'contientErreurs' => false,
                )
            );
        } else {
            $tableauMessages[] = "Une erreur est survenue lors de l'activation de votre compte";
            $template = $this->getTwig()->load('connexion.html.twig');
            echo $template->render(
                array(
                    'message' => $tableauMessages,
                    'contientErreurs' => true,
                )
            );
        }
    }

    /**
     * Fonction qui envoie un mail d'activation de compte à l'utilisateur
     * @param string|null $email de l'utilisateur
     * @return array
     */
    public function envoyerMailActivationCompte(?string $email): ?array {
        $messageErreur = [];
        $mailEnvoye = true;

        $manager = new UtilisateurDAO($this->getPdo());
        $utilisateur = $manager->getObjetUtilisateur($email);
        $token = $utilisateur->genererTokenActivationCompte();
        $utilisateur->setTokenActivationCompte($token);
        $manager->miseAJourUtilisateur($utilisateur);

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_ADDRESS;
        $mail->Password = MAIL_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Cryptage TLS
        $mail->Port = 587; // Port 587 pour TLS            
        $mail->setFrom(MAIL_ADDRESS, 'TimeHarmony');
        $mail->addAddress($email);
        $mail->isHTML(true);

        // En-têtes du mail
        $headers = "From: no-reply@timeharmony.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        $sujet = "Activation de votre compte";
        if(DB_HOST != 'localhost') {
            $lien = "http://lakartxela.iutbayonne.univ-pau.fr/~tlatxague/TimeHarmony/index.php?controleur=utilisateur&methode=activerCompte&token=$token&email=$email";
        } else {
            $lien = "http://localhost/TimeHarmony/index.php?controleur=utilisateur&methode=activerCompte&token=$token&email=$email";
        }
        $mail->Subject = $sujet;

        // Corps du message (format HTML)
        $message = "
        <html>
            <head>
                <title>$sujet</title>
            </head>
            <body style='background-color: #f6f6f6; font-family: Arial, sans-serif; text-align: center; padding: 20px;'>
                
                <table align='center' width='100%' cellspacing='0' cellpadding='0' border='0'>
                    <tr>
                        <td align='center'>
                            <table width='500' cellspacing='0' cellpadding='0' border='0' style='background: #F5F5DC; padding: 20px; border-radius: 8px;'>
                                <tr>
                                    <td align='center' style='background-color: #64a19d; padding: 20px; border-radius: 5px;'>
                                        <img src='https://i.imgur.com/V6Rf8oL.png' alt='Logo TimeHarmony' style='width: 100px; margin-bottom: 10px; display: block;'>
                                        <h2 style='color: #f6f6f6; margin: 0;'>TimeHarmony</h2>
                                    </td>
                                </tr>
                                <tr>
                                    <td align='center' style='padding: 20px;'>
                                        <h3 style='color: #64a19d;'>Bonjour $email,</h3>
                                        <p style='color: #64a19d; font-size: 16px;'>Nous avons bien reçu votre demande de création de compte sur TimeHarmony. Nous sommes ravis de vous accueillir parmi nous !</p>
                                        <p style='color: #64a19d; font-size: 16px;'>Pour finaliser la création de votre compte et accéder à toutes nos fonctionnalités, veuillez cliquer sur le lien ci-dessous :</p>
                                        <a href='$lien' style='display: inline-block; background-color: #64a19d; color: #f6f6f6; padding: 12px 20px; border-radius: 5px; font-size: 16px; text-decoration: none; margin-top: 10px;'>Accéder au site</a>
                                        <p style='color: #64a19d; font-size: 16px; margin-top: 20px;'>Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer ce message.</p>
                                        <p style='margin-top: 20px; font-size: 14px; color: #bbbbbb;'>Merci pour votre confiance et à bientôt sur TimeHarmony !</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
        
            </body>
        </html>";

        $mail->Body = $message;

        // On met un @ car sur localhost, pas de serveur de mail
        if ($mail->send()) {
            $messageErreur[] = "L'e-mail de confirmation de création de compte a été envoyé avec succès à $email.";
            $mailEnvoye = true;
        } else {
            $messageErreur[] = "Erreur : L'e-mail n'a pas pu être envoyé à $email.";
            $mailEnvoye = false;
        }

        $result[0] = $messageErreur;
        $result[1] = $mailEnvoye;

        return $result;
    }

    /**
     * Fonction qui génère la vue du menu
     * 
     * @param array|null $tabErreurs tableau des erreurs
     * @param bool|null $contientErreurs true si le tableau contient des erreurs, false sinon
     * @return void
     */
    public function genererVueMenu(?array $tabErreurs, ?bool $contientErreurs = false): void {
        $template = $this->getTwig()->load('menu.html.twig');
        echo $template->render(
            array(
                'message' => $tabErreurs,
                'contientErreurs' => $contientErreurs
            )
        );
    }

    public function nettoyerUtilisateur(): void {
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $utilisateurs = $manager->findAll();

        foreach ($utilisateurs as $utilisateur) {
            $dateDerniereConnexion = $utilisateur->getDateDerniereConnexion();
            $interval = $dateDerniereConnexion->diff(new DateTime());
            if($interval->years >= 10) {
                $manager->supprimerUtilisateur($utilisateur->getId());
            }
        }
    }
}
