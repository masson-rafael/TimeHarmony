<?php

/**
 * @author Thibault Latxague
 * @brief Controller des informations du site (A propos, Conditions générales d'utilisation et Contact)
 * @version 0.1
 */

/**
 * Undocumented class
 */
class ControllerInformations extends Controller {
    /**
     * Affiche les conditions générales d'utilisation
     * @return void
     */
    public function afficherCGDU() {
        $this->affichageTwig('CGDU');
    }

    /**
     * Affiche un formulaire de contact
     * @return void
     */
    public function afficherContact() {
        $this->affichageTwig('contact');
    }

    /**
     * Affiche les informations "À propos"
     * @return void
     */
    public function afficherAPropos() {
        $this->affichageTwig('aPropos');
    }

    /**
     * Affiche la politique de confidentialité
     * @return void
     */
    public function afficherPDC() {
        $this->affichageTwig('PDC');
    }

    /**
     * Méthode de rendu Twig pour une section donnée
     * @param string|null $section qui sera affichée par la twig à la demande de l'utilisateur
     * @return void
     */
    public function affichageTwig(?string $section) {
        $validSections = ['CGDU', 'contact', 'aPropos', 'PDC'];
        $tableauExceptions = [];

        if (!in_array($section, $validSections)) {
            $tableauExceptions = 'Erreur : cette page n\'existe pas';
        }

        $template = $this->getTwig()->load('informations.html.twig');
        echo $template->render(
            array(
                'CGDU' => $section === 'CGDU',
                'contact' => $section === 'contact',
                'aPropos' => $section === 'aPropos',
                'PDC' => $section === 'PDC',
                'message' => $tableauExceptions,
            )
        );
    }

    /**
     * Fonction qui envoie un mail à tous les administrateurs du site.
     * Le mail contiendra le sujet de la demande ainsi qu'une description remplie par le demandeur.
     * @return void
     */
    public function envoyer() {
        $tableauErreurs = [];

        // Validation des champs envoyés par le formulaire
        $valideMail = utilitaire::validerEmail($_POST['email'], $tableauErreurs);
        $valideDescription = utilitaire::validerDescription($_POST['description'], $tableauErreurs);
        $valideSujet = utilitaire::validerSujet($_POST['motif'], $tableauErreurs);

        if ($valideMail && $valideDescription && $valideSujet) {
            $mailExpediteur = $_POST['email'];
            $description = $_POST['description'];
            $sujet = $_POST['motif'];

            // Adresse principale à no-reply
            $emailPrincipal = 'no-reply@timeharmony.com';

            // Récupérer les emails des administrateurs
            $pdo = $this->getPdo();
            $manager = new UtilisateurDao($pdo);
            $mails = $manager->getAdministrateurs();

            // Extraire les emails des administrateurs
            foreach ($mails as $user) {
                $emailsAdmin[] = $user->getEmail();
            }

            // Construire les en-têtes
            $headers = 'From: ' . $mailExpediteur . "\r\n";
            $headers .= 'Reply-To: ' . $mailExpediteur . "\r\n";
            $headers .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
            $headers .= 'Cc: ' . implode(",", $emailsAdmin) . "\r\n";

            // Envoyer l'email
            if (mail($emailPrincipal, $sujet, $description, $headers)) {
                $tableauErreurs[] = "Email envoyé avec succès à no-reply@timeharmony.com avec les administrateurs en copie.";
            } else {
                $tableauErreurs[] =  "Échec de l'envoi de l'email.";
            }

            $template = $this->getTwig()->load('menu.html.twig');
            echo $template->render(
                array(
                    'message' => $tableauErreurs,
                )
            );
        } else {
            $tableauErreurs[] =  "Formulaire invalide : " . implode(", ", $tableauErreurs);
        }
    }
}