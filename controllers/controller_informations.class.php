<?php

/**
 * @author Thibault Latxague
 * @describe Controller des informations du site (A propos, Conditions générales d'utilisation et Contact)
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
     * @param string $section qui sera affichée par la twig à la demande de l'utilisateur
     * @return void
     */
    public function affichageTwig(string $section) {
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
     * Fonction qui envoie un mail à tous les administrateurs du site
     * Le mail contiendra le sujet de la demande ainsi qu'une description remplie par le demandeur
     * @return void
     */
    public function envoyer() {

    }
}