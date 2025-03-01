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
    mdp.style.borderColor = '';
    verifierTousLesChamps();
}

// Fonction de vérication de l'adresse email
function verifierPatternMail(email) {
    const motifEmail = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    if(email.value === '') {
        email.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        emailError.textContent = 'Veuillez saisir votre adresse email.';
        return false;
    }

    if (!motifEmail.test(email.value)) {
        email.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        emailError.textContent = 'Adresse email invalide.';
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
    var erreursMdp = [];

    if(motDePasse.value === '') {
        mdpError.textContent = '';
        return false;
    }

    if (!motifTaille.test(motDePasse.value) || !motifMinuscule.test(motDePasse.value) || !motifMajuscule.test(motDePasse.value) || !motifChiffre.test(motDePasse.value) || !motifSpecial.test(motDePasse.value)) {
        erreursMdp.push('Le mot de passe doit contenir au moins 8 caractères, une minuscule, une majuscule, un chiffre et un caractère spécial.');
        motDePasse.style.borderColor = '';
        mdpError.style.color = 'black';
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
