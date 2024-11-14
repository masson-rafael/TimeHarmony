<?php

use ICal\ICal;

/**
 * @author Félix Autant
 * @describe Controller de la page de recherche de créneaux libres
 * @todo Verifier que le undocumented class soit pas à remplir. S'il existe même
 * @version 0.1
 */

/**
 * Undocumented class
 */
class ControllerCreneauRDV extends Controller
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


    function genererVueRecherche() {
        $contacts = $this->recupererContact(1);
        $groupes = $this->recupererGroupe(1);
        //Génération de la vue
        $template = $this->getTwig()->load('recherche.html.twig');
        echo $template->render(array(
            'contacts' => $contacts,
            'groupes' => $groupes
        ));        

    }

    function recupererContact($idUtilisateur): array {
        $pdo = $this->getPdo();

        $managerUtilisateur = new UtilisateurDao($pdo);
        $contactsId = $managerUtilisateur->findAllContact($idUtilisateur);  
        $contacts=array();     
        foreach ($contactsId as $contact) {
            $contacts[] = $managerUtilisateur->find($contact['idUtilisateur2']);
        }
        return $contacts;
    }

    function recupererGroupe($idUtilisateur): array {
        $pdo = $this->getPdo();

        $managerGroupe = new GroupeDao($pdo);
        $groupesId = $managerGroupe->findAll($idUtilisateur);  
        $groupes=array();     
        foreach ($groupesId as $groupe) {
            $groupes[] = $managerGroupe->find($groupe['idGroupe']);
        }
        return $groupes;
    }


    /**
     * Fonction d'obtention des créneaux libres
     *
     * @return void
     */
    function obtenir() {
        $pdo = $this->getPdo();
        $managerCreneau = new CreneauLibreDao($pdo);
        // Vider la table pour éviter les récurrences
        $managerCreneau->supprimerCreneauxLibres();

        if (isset($_POST['urlIcs']) && !empty($_POST['urlIcs'])) {
            // Récupérer les données du formulaire
            extract($_POST, EXTR_OVERWRITE);
            // Récupérer les événements de l'agenda
            $evenements = $this->recuperationEvenementsAgenda($urlIcs, $debut, $fin);
            // Trier les événements par date de début
            $evenements = $this->triEvenementsOrdreArrivee($evenements);
            // Recherche des créneaux libres
            $this->recherche($managerCreneau, 'Europe/Paris', $debut, $fin, $evenements);

            $this->genererVueCreneaux($managerCreneau);
        } else {
            $this->genererVue();
        }
    }

    /**
     * Recupere les eveneùent des agendas
     *
     * @param string|null $url de l'agenda
     * @param string|null $debut date de debut de la recherche
     * @param string|null $fin date de fin de la recherche
     * @return array|null tableau des evenements
     */
    function recuperationEvenementsAgenda(?string $url, ?string $debut, ?string $fin): ?array
    {
        // Charger le calendrier ICS via l'URL
        if (!$this->testerValiditeUrl($url)) {
            $this->genererVue();
            throw new Exception("L'URL n'est pas valide");
        } else {
            $calendrier = new ICal($url);
            // Récupérer tous les événements
            $evenements = $calendrier->eventsFromRange($debut, $fin);
        }
        return $evenements;
    }

    /**
     * Trie des evenements par ordre d'arrivee
     *
     * @param array|null $evenement tableau d'evenements bruts a trier
     * @return array|null tableau d'evenements tries
     */
    function triEvenementsOrdreArrivee(?array $evenement): ?array
    {
        // Trier les événements par date de début
        usort($evenement, function ($a, $b) {
            $dateDebutA = new DateTime($a->dtstart);
            $dateDebutB = new DateTime($b->dtstart);
            return $dateDebutA <=> $dateDebutB;
            /*L'opérateur <=> compare les deux valeurs et renvoie :
        -1 si $dateDebutA est inférieur à $dateDebutB,
        0 si $dateDebutA est égal à $dateDebutB,
        1 si $dateDebutA est supérieur à $dateDebutB.*/
        });
        return $evenement;
    }

    /**
     * Fonction de recherche des créneaux libres
     *
     * @param CreneauLibreDao|null $managerCreneau Manager de créneaux libres afin d'appeler les méthodes DAO
     * @param string|null $timeZone Fuseau horaire de la recherche
     * @param string|null $debut date de debut de la recherche
     * @param string|null $fin date de fin de la recherche
     * @param array|null $evenements tableau d'evenements triés
     * @return void
     */
    function recherche(?CreneauLibreDao $managerCreneau, ?string $timeZone, ?string $debut, ?string $fin, ?array $evenements)
    {
        $fuseauHoraire = new DateTimeZone($timeZone);
        $debutCourant = new DateTime($debut, $fuseauHoraire);
        $finCourant = new DateTime($fin, $fuseauHoraire);

        foreach ($evenements as $evenement) {
            // Convertir les heures d'événements en fuseau horaire local
            $debutEvenement = new DateTime($evenement->dtstart, $fuseauHoraire);
            $finEvenement = new DateTime($evenement->dtend, $fuseauHoraire);
            $debutEvenement->setTimezone($fuseauHoraire);
            $finEvenement->setTimezone($fuseauHoraire);

            if ($debutEvenement > $debutCourant) {
                $managerCreneau->ajouterCreneauLibre(new CreneauLibre(null, $debutCourant, $debutEvenement, 1));
            }
            $debutCourant = max($debutCourant, $finEvenement);
        }

        // Vérifier s'il reste des créneaux libres après le dernier événement
        if ($debutCourant < $finCourant) {
            $managerCreneau->ajouterCreneauLibre(new CreneauLibre(null, $debutCourant, $finCourant, 1));
        }
    }

    /**
     * Tester la validité de l'URL d'un Agenda
     *
     * @param string|null $url de l'agenda a verifier
     * @throws Exception erreur de l'url si il n'est pas valide
     * @return boolean true si l'url est valide, false sinon
     */
    function testerValiditeUrl(?string $url): bool {
        try {
            // @ nécessaire pour enlever les erreurs
            @$calendrier = new ICal($url);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Generer la vue avec les resultats des creneaux
     *
     * @param CreneauLibreDao|null $managerCreneau lien avec le manager de creneaux
     * @return void
     */
    function genererVueCreneaux(?CreneauLibreDao $managerCreneau) {
        // Récupérer les créneaux libres
        $tableau = $managerCreneau->findAllAssoc();
        // Création en objet des créneaux libres
        $creneaux = $managerCreneau->hydrateAll($tableau);

        //Génération de la vue
        $template = $this->getTwig()->load('resultat.html.twig');
        echo $template->render(array(
            'creneauxLibres' => $creneaux
        ));
    }

    function genererVue() {
        //Génération de la vue
        $template = $this->getTwig()->load('index.html.twig');
        echo $template->render(array());
    }
}