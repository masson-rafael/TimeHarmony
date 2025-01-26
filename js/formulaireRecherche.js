let btn = document.getElementById('boutonLancerRecherche');
btn.disabled = true;

let dateDebut = document.getElementById('dateDebut');
let dateDebutError = document.getElementById('dateDebutError');
let dateFin = document.getElementById('dateFin');
let dateFinError = document.getElementById('dateFinError');
let duree = document.getElementById('duree');
let dureeError = document.getElementById('dureeError');

// Ajout des evenements qui declechent les fonctions de validation à chaque input
dateDebut.addEventListener('change', verifierTousLesChamps);
dateFin.addEventListener('change', verifierTousLesChamps);
duree.addEventListener('change', verifierTousLesChamps);
btn.addEventListener('click', sauvegarderVariables);

const date = new Date();
preparationDates(date, dateDebut, 0); // Remplissage du formulaire par valeur de base (jour actuel)
preparationDates(date, dateFin, 7);
verifierTousLesChamps();

function preparationDates(dateActuelle, date, jourEnPlus) {
    // Création d'une nouvelle date en ajoutant le nombre de jours
    let nouvelleDate = new Date(dateActuelle);
    nouvelleDate.setDate(dateActuelle.getDate() + jourEnPlus);

    let jour = nouvelleDate.getDate().toString().padStart(2, '0'); // padStart pour ajouter un 0 au début si le mois est inférieur à 10
    let mois = (nouvelleDate.getMonth() + 1).toString().padStart(2, '0');  // +1 car les mois commencent à 0 et padStart pour ajouter un 0 au début si le mois est inférieur à 10
    let annee = nouvelleDate.getFullYear();

    date.value = annee + '-' + mois + '-' + jour;
}


// Sauvegarde de la duree uniquement
function sauvegarderVariables() {
    sessionStorage.setItem('duree', duree.value);
}

// Fonction de vérication du dateDebut / dateFin
function verifierDate(champ, error, secondeDate = null) {
    let dateActuelle = new Date();
    let jour = dateActuelle.getDate().toString().padStart(2, '0'); // padStart pour ajouter un 0 au début si le mois est inférieur à 10
    let mois = (dateActuelle.getMonth() + 1).toString().padStart(2, '0');  // +1 car les mois commencent à 0 et padStart pour ajouter un 0 au début si le mois est inférieur à 10
    let annee = dateActuelle.getFullYear();
    dateActuelle.value = annee + '-' + mois + '-' + jour;

    let correct = true;

    const motifdateDebut = /^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/;
    if (!motifdateDebut.test(champ.value)) {
        champ.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        error.textContent = 'La date ne respecte pas le format attendu (AAAA-MM-JJ)';
        correct = false;
    }
    if (secondeDate !== null && champ.value > secondeDate.value) {
        champ.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        error.textContent = 'La date de début doit être inférieure à la date de fin';
        correct = false;
    }
    if (champ.value < dateActuelle.value) {
        champ.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        error.textContent = 'La date de début doit être supérieure ou égale à la date actuelle';
        correct = false;
    }
    if (correct === true) {
        champ.style.borderColor = ''; // Bordure par défaut (succès)
        error.textContent = '';
        return true;
    }
    return false;
}

// Fonction de vérification de la durée
function verifierDuree(champ, error) {
    const motifDuree = /^[1-9][0-9]*$/;
    const motifRespectTime = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/;
    let correct = true;

    if (champ.value < '00:05') {
        champ.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        error.textContent = 'La durée doit être supérieure à 5 minutes';
        correct = false;
    } if (!motifRespectTime.test(champ.value)) {
        champ.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        error.textContent = 'La durée doit être au format HH:MM';
        correct = false;
    }    
    
    if (correct === true) {
        champ.style.borderColor = ''; // Bordure par défaut (succès)
        error.textContent = '';
        return true;
    }
    return false;
}

// Fonction de vérification de la présence des champs
function verifierPresence() {
    if (dateDebut.value === '' || dateFin.value === '' || duree.value === '') {
        return false;
    }
    return true;
}

// Fonction générale de vérification de tous les champs
function verifierTousLesChamps() {
    console.log('Verification des champs');
    const dateDebutCorrect = verifierDate(dateDebut, dateDebutError, dateFin);
    const dateFinCorrect = verifierDate(dateFin, dateFinError, dateFin);
    const dureeCorrect = verifierDuree(duree, dureeError);
    const presenceCorrect = verifierPresence();

    // Activer ou désactiver le bouton en fonction des validations
    btn.disabled = !(dateDebutCorrect && dateFinCorrect && presenceCorrect && dureeCorrect);
}
