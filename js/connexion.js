let formulaire = document.getElementById('formConnexion');
let btn = document.getElementById('boutonConnexion');

let email = formulaire[0];
let mdp = formulaire[1];

btn.disabled = true;

email.value = localStorage.getItem('email');

email.addEventListener('input', verifierTousLesChamps);
mdp.addEventListener('input', verifierTousLesChamps);

btn.addEventListener('click', sauvegarderVariables);

function verifierPatternMail(email) {
    const motifEmail = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!motifEmail.test(email.value)) {
        email.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        console.log("Adresse email invalide.");
        return false;
    }
    email.style.borderColor = ''; // Bordure par défaut (succès)
    return true;
}

function verifierPattern(motDePasse) {
    const motifTaille = /^[a-zA-Z\d\W]{8,25}$/;
    const motifMinuscule = /[a-z]/;
    const motifMajuscule = /[A-Z]/;
    const motifChiffre = /\d/;
    const motifSpecial = /\W/;

    if (!motifTaille.test(motDePasse.value)) {
        console.log("Le mot de passe doit contenir entre 8 et 25 caractères.");
    } if (!motifMinuscule.test(motDePasse.value)) {
        console.log("Le mot de passe doit contenir au moins une lettre minuscule.");
    } if (!motifMajuscule.test(motDePasse.value)) {
        console.log("Le mot de passe doit contenir au moins une lettre majuscule.");
    } if (!motifChiffre.test(motDePasse.value)) {
        console.log("Le mot de passe doit contenir au moins un chiffre.");
    } if (!motifSpecial.test(motDePasse.value)) {
        console.log("Le mot de passe doit contenir au moins un caractère spécial.");
    } 

    if (motifTaille.test(motDePasse.value) && motifMinuscule.test(motDePasse.value) && motifMajuscule.test(motDePasse.value) && motifChiffre.test(motDePasse.value) && motifSpecial.test(motDePasse.value))
    {
        motDePasse.style.borderColor = ''; // Bordure par défaut (pas d'erreur).
        return true;
    }
}

function verifierPresence() {
    if (email.value === '' || mdp.value === '') {
        return false;
    }
    return true;
}

function verifierTousLesChamps() {
    const emailCorrect = verifierPatternMail(email);
    const mdpCorrect = verifierPattern(mdp);
    const presenceCorrect = verifierPresence();

    // Activer ou désactiver le bouton en fonction des validations
    btn.disabled = !(emailCorrect && mdpCorrect && presenceCorrect);
}

function sauvegarderVariables() {
    localStorage.setItem('email', email.value);
}
