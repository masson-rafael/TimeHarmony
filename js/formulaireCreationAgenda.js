let formulaire = document.getElementById('formAjouteAgenda');
let btn = document.getElementById('boutonAjoutAgenda');

let nom = formulaire[0];
let nomError = document.getElementById('nomError');
let url = formulaire[1];
let urlError = document.getElementById('urlError');
let couleur = formulaire[2];
let couleurError = document.getElementById('couleurError');

// Ajout des evenements qui declechent les fonctions de validation à chaque input
nom.addEventListener('input', verifierTousLesChamps);
url.addEventListener('input', verifierTousLesChamps);
couleur.addEventListener('change', verifierTousLesChamps);

// Préparation des champs au chargement du formulaire
preparationChamps();

// Fonction de préparation des champs
function preparationChamps() {
    btn.disabled = true;
    nom.focus();
    verifierTousLesChamps();
}

// Fonction de vérication de l'adresse nom
function verifierPatternNom(nom) {
    const motifnom = /^[a-zA-ZÀ-ÿ0-9- ]+$/;

    if (nom.value === '') {
        nom.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        nomError.textContent = 'Veuillez saisir un nom.';
        return false;
    }

    if (!motifnom.test(nom.value)) {
        nom.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        nomError.textContent = 'Le nom ne correspond pas au format attendu.';
        return false;
    }

    nom.style.borderColor = ''; // Bordure par défaut (succès)
    nomError.textContent = '';
    return true;
}

function verifierPatternURL(URL) {
    const motifProtocol = /^https?:\/\//;
    const motifDomain = /calendar\.google\.com/;
    const motifFile = /\/basic\.ics$/;
    URL.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
    var erreursURL = [];

    if(URL.value === '') {
        urlError.textContent = 'Veuillez saisir une URL.';
        return false;
    }

    if (!motifProtocol.test(URL.value)) {
        erreursURL.push('Le début de l\'URL doit être http ou https.');
    } if (!motifDomain.test(URL.value)) {
        erreursURL.push('L\'URL doit être celle de Google Calendar.');
    } if (!motifFile.test(URL.value)) {
        erreursURL.push('L\'URL doit pointer vers un fichier .ics.');
    }

    urlError.innerHTML = erreursURL.join('<br>');

    if (motifDomain.test(URL.value) && motifFile.test(URL.value) && motifProtocol.test(URL.value)) {
        URL.style.borderColor = ''; // Bordure par défaut (succès)
        urlError.textContent = '';
        return true;
    }
}

function verifierPatternCouleur(couleur) {
    const motifcouleur = /^#[a-fA-F0-9]{6}$/;
    if (!motifcouleur.test(couleur.value)) {
        couleur.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        couleurError.textContent = 'La couleur doit être au format hexadecimal.';
        return false;
    }
    couleur.style.borderColor = ''; // Bordure par défaut (succès)
    couleurError.textContent = '';
    return true;
}

// Fonction de vérification de la présence des champs
function verifierPresence() {
    if (nom.value === '' || url.value === '' || couleur.value === '') {
        return false;
    }
    return true;
}

// Fonction générale de vérification de tous les champs
function verifierTousLesChamps() {
    const nomCorrect = verifierPatternNom(nom);
    const urlCorrect = verifierPatternURL(url);
    const couleurCorrect = verifierPatternCouleur(couleur);
    const presenceCorrect = verifierPresence();

    // Activer ou désactiver le bouton en fonction des validations
    btn.disabled = !(nomCorrect && urlCorrect && presenceCorrect && couleurCorrect);
}