let formulaire = document.getElementById('formContact');
let btn = document.getElementById('boutonEnvoiMessage');

let email = formulaire[0];
let emailError = document.getElementById('emailError');
let message = formulaire[2];
let messageError = document.getElementById('descriptionError');

// Préparation des champs au chargement du formulaire
preparationChamps();

// Ajout des evenements qui declechent les fonctions de validation à chaque input
email.addEventListener('input', verifierTousLesChamps);
message.addEventListener('input', verifierTousLesChamps);
btn.addEventListener('click', sauvegarderVariables);

// Fonction de préparation des champs
function preparationChamps() {
    btn.disabled = true;
    email.value = localStorage.getItem('email');
    if (email.value) {
        message.focus();
    } else {
        email.style.borderColor = 'red';
        emailError.textContent = 'Veuillez saisir votre adresse email.';
        email.focus();
    }
    message.style.borderColor = 'red';
    messageError.textContent = 'Veuillez saisir un sujet de contact.';
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
function verifierPattern(message) {
    const motifTaille = /^[a-zA-Z\d\W]{10,2000}$/;
    message.style.borderColor = 'red';

    if (!motifTaille.test(message.value)) {
        messageError.textContent = 'Le message doit contenir entre 10 et 2000 caractères.';
    } 

    if (motifTaille.test(message.value))
    {
        message.style.borderColor = ''; // Bordure par défaut (pas d'erreur).
        messageError.textContent = '';
        return true;
    }
}

// Fonction de vérification de la présence des champs
function verifierPresence() {
    if (email.value === '' || message.value === '') {
        return false;
    }
    return true;
}

// Fonction générale de vérification de tous les champs
function verifierTousLesChamps() {
    const emailCorrect = verifierPatternMail(email);
    const messageCorrect = verifierPattern(message);
    const presenceCorrect = verifierPresence();

    // Activer ou désactiver le bouton en fonction des validations
    btn.disabled = !(emailCorrect && messageCorrect && presenceCorrect);
}

// Fonction de sauvegarde des variables
function sauvegarderVariables() {
    localStorage.setItem('email', email.value);
}
