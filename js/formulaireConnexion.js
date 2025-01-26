let formulaire = document.getElementById('formConnexion');
let btn = document.getElementById('boutonConnexion');

let email = formulaire[0];
let emailError = document.getElementById('emailError');
let mdp = formulaire[1];
let mdpError = document.getElementById('passwordError');

// Préparation des champs au chargement du formulaire
preparationChamps();

// Ajout des evenements qui declechent les fonctions de validation à chaque input
email.addEventListener('input', verifierTousLesChamps);
mdp.addEventListener('input', verifierTousLesChamps);
btn.addEventListener('click', sauvegarderVariables);

// Fonction de préparation des champs
function preparationChamps() {
    btn.disabled = true;
    email.value = localStorage.getItem('email');
    if (email.value) {
        mdp.focus();
    } else {
        email.style.borderColor = 'red';
        emailError.textContent = 'Veuillez saisir votre adresse email.';
        email.focus();
    }
    mdp.style.borderColor = 'red';
    mdpError.textContent = 'Veuillez saisir votre mot de passe.';
}

// Fonction de vérication de l'adresse email
function verifierPatternMail(email) {
    const motifEmail = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!motifEmail.test(email.value)) {
        email.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        emailError.textContent = 'Adresse email invalide.';
        console.log("Adresse email invalide.");
        return false;
    }
    email.style.borderColor = ''; // Bordure par défaut (succès)
    emailError.textContent = '';
    return true;
}

// Fonction de vérification du mot de passe
function verifierPattern(motDePasse) {
    const motifTaille = /^[a-zA-Z\d\W]{8,25}$/;
    const motifMinuscule = /[a-z]/;
    const motifMajuscule = /[A-Z]/;
    const motifChiffre = /\d/;
    const motifSpecial = /\W/;
    motDePasse.style.borderColor = 'red';
    var erreursMdp = [];

    if (!motifTaille.test(motDePasse.value)) {
        erreursMdp.push('Le mot de passe doit contenir entre 8 et 25 caractères.');
    } if (!motifMinuscule.test(motDePasse.value)) {
        erreursMdp.push('Le mot de passe doit contenir au moins une lettre minuscule.');
    } if (!motifMajuscule.test(motDePasse.value)) {
        erreursMdp.push('Le mot de passe doit contenir au moins une lettre majuscule.');
    } if (!motifChiffre.test(motDePasse.value)) {
        erreursMdp.push('Le mot de passe doit contenir au moins un chiffre.');
    } if (!motifSpecial.test(motDePasse.value)) {
        erreursMdp.push('Le mot de passe doit contenir au moins un caractère spécial.');
    } 
    mdpError.innerHTML = erreursMdp.join('<br>');

    if (motifTaille.test(motDePasse.value) && motifMinuscule.test(motDePasse.value) && motifMajuscule.test(motDePasse.value) && motifChiffre.test(motDePasse.value) && motifSpecial.test(motDePasse.value))
    {
        motDePasse.style.borderColor = ''; // Bordure par défaut (pas d'erreur).
        mdpError.textContent = '';
        return true;
    }
}

// Fonction de vérification de la présence des champs
function verifierPresence() {
    if (email.value === '' || mdp.value === '') {
        return false;
    }
    return true;
}

// Fonction générale de vérification de tous les champs
function verifierTousLesChamps() {
    const emailCorrect = verifierPatternMail(email);
    const mdpCorrect = verifierPattern(mdp);
    const presenceCorrect = verifierPresence();

    // Activer ou désactiver le bouton en fonction des validations
    btn.disabled = !(emailCorrect && mdpCorrect && presenceCorrect);
}

// Fonction de sauvegarde des variables
function sauvegarderVariables() {
    localStorage.setItem('email', email.value);
}
