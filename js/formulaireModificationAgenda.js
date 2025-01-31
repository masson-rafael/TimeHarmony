let formModification = document.getElementById('formModifierAgenda');
let bouton = document.getElementById('boutonModifierAgenda');

let nomAgenda = formModification[0];
let nomAgendaError = document.getElementById('nomAgendaError');
let urlAgenda = formModification[1];
let urlAgendaError = document.getElementById('urlAgendaError');
let couleurAgenda = formModification[2];
let couleurAgendaError = document.getElementById('couleurAgendaError');

// Verif des champs car normalement tout est rempli
verifierTousLesChamps();
nomAgenda.focus();

// Ajout des evenements qui declechent les fonctions de validation à chaque input
nomAgenda.addEventListener('input', verifierTousLesChamps);
urlAgenda.addEventListener('input', verifierTousLesChamps);
couleurAgenda.addEventListener('change', verifierTousLesChamps);

// Fonction de vérication de l'adresse nomAgenda
function verifierPatternNomAgenda(nomAgenda) {
    const motifnomAgenda = /^[a-zA-ZÀ-ÿ0-9- ]+$/;

    if (nomAgenda.value === '') {
        nomAgenda.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        nomAgendaError.textContent = 'Veuillez saisir un nom.';
        return false;
    }

    if (!motifnomAgenda.test(nomAgenda.value)) {
        nomAgenda.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        nomAgendaError.textContent = 'Le nom de l\'agenda ne correspond pas au format attendu.';
        return false;
    }

    nomAgenda.style.borderColor = ''; // Bordure par défaut (succès)
    nomAgendaError.textContent = '';
    return true;
}

function verifierPatternURLAgenda(urlAgenda) {
    const motifProtocol = /^http/;
    URL.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
    var erreursURL = [];

    if(URL.value === '') {
        urlError.textContent = 'Veuillez saisir une URL.';
        return false;
    }

    if (!motifProtocol.test(URL.value)) {
        erreursURL.push('Le début de l\'URL doit être http/https.');
    }

    urlError.innerHTML = erreursURL.join('<br>');

    if (motifProtocol.test(URL.value)) {
        URL.style.borderColor = ''; // Bordure par défaut (succès)
        urlError.textContent = '';
        return true;
    }
}

function verifierPatterncouleurAgenda(couleurAgenda) {
    const motifcouleurAgenda = /^#[a-fA-F0-9]{6}$/;
    if (!motifcouleurAgenda.test(couleurAgenda.value)) {
        couleurAgenda.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        couleurAgendaError.textContent = 'La couleurAgenda doit être au format hexadecimal.';
        return false;
    }
    couleurAgenda.style.borderColor = ''; // Bordure par défaut (succès)
    couleurAgendaError.textContent = '';
    return true;
}

// Fonction de vérification de la présence des champs
function verifierPresence() {
    if (nomAgenda.value === '' || urlAgenda.value === '' || couleurAgenda.value === '') {
        return false;
    }
    return true;
}

// Fonction générale de vérification de tous les champs
function verifierTousLesChamps() {
    const nomAgendaCorrect = verifierPatternNomAgenda(nomAgenda);
    const urlAgendaCorrect = verifierPatternURLAgenda(urlAgenda);
    const couleurAgendaCorrect = verifierPatterncouleurAgenda(couleurAgenda);
    const presenceCorrect = verifierPresence();

    // Activer ou désactiver le bouton en fonction des validations
    bouton.disabled = !(nomAgendaCorrect && urlAgendaCorrect && presenceCorrect && couleurAgendaCorrect);
}