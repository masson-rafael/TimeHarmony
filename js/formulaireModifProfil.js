let formulaire = document.getElementById('formProfil');
let btn = document.getElementById('boutonModification');

let nom = formulaire[0];
let nomError = document.getElementById('nomError');
let prenom = formulaire[1];
let prenomError = document.getElementById('prenomError');

// Ajout des evenements qui declechent les fonctions de validation à chaque input
nom.addEventListener('input', verifierTousLesChamps);
prenom.addEventListener('input', verifierTousLesChamps);
btn.addEventListener('click', sauvegarderVariables);

// Fonction de vérication du nom / prenom
function verifierPatternNomPrenom(champ, error) {
    const motifnom = /^[a-zA-ZÀ-ÿ0-9- ]+$/;
    if (!motifnom.test(champ.value)) {
        champ.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        error.textContent = 'Le ' + champ.name + ' ne respecte pas le format attendu';
        return false;
    }
    champ.style.borderColor = ''; // Bordure par défaut (succès)
    error.textContent = '';
    return true;
}

// Fonction de vérification de la présence des champs
function verifierPresence() {
    if (nom.value === '' || prenom.value === '') {
        return false;
    }
    return true;
}

// Fonction générale de vérification de tous les champs
function verifierTousLesChamps() {
    const nomCorrect = verifierPatternNomPrenom(nom, nomError);
    const prenomCorrect = verifierPatternNomPrenom(prenom, prenomError);
    const presenceCorrect = verifierPresence();

    // Activer ou désactiver le bouton en fonction des validations
    btn.disabled = !(nomCorrect && prenomCorrect && presenceCorrect);
}
