let formulaire = document.getElementById('form');
let btn = document.getElementById('boutonInscription');

let nom = formulaire[0];
let prenom = formulaire[1];
let email = formulaire[2];
let mdp = formulaire[3];
let mdpConfirme = formulaire[4];

// Préparation des champs au chargement du formulaire
preparationChamps();

// Ajout des écouteurs d'événements pour tous les champs
nom.addEventListener('input', verifierTousLesChamps);
prenom.addEventListener('input', verifierTousLesChamps);
email.addEventListener('input', verifierTousLesChamps);
mdp.addEventListener('input', verifierTousLesChamps);
mdpConfirme.addEventListener('input', verifierTousLesChamps);
btn.addEventListener('click', sauvegarderVariables);

function preparationChamps() {
    nom.style.borderColor = 'red';
    prenom.style.borderColor = 'red';
    email.style.borderColor = 'red';
    mdp.style.borderColor = 'red';
    mdpConfirme.style.borderColor = 'red';

    // Chargement des valeurs sauvegardées
    nom.value = localStorage.getItem('nom');
    prenom.value = localStorage.getItem('prenom');
    email.value = localStorage.getItem('email');

    // Focus sur le premier champ vide
    if (nom.value === '') {
        nom.focus();
    } else if (prenom.value === '') {
        prenom.focus();
    } else if (email.value === '') {
        email.focus();
    } else {
        mdp.focus();
    }

    btn.disabled = true;
    verifierTousLesChamps();
}

// Vérification de la validité des champs
function verifierTousLesChamps() {
    const nomCorrect = verifierPresence(nom);
    const prenomCorrect = verifierPresence(prenom);
    const emailCorrect = verifierPatternMail(email);
    const mdpCorrect = verifierPattern(mdp);
    const mdpCorrespondance = verifierCorrespondance();

    // Activation ou désactivation du bouton
    btn.disabled = !(nomCorrect && prenomCorrect && emailCorrect && mdpCorrect && mdpCorrespondance);
}

// Fonction de vérication de l'adresse email
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

// Fonction de vérification du mot de passe
function verifierPattern(motDePasse) {
    const motifTaille = /^[a-zA-Z\d\W]{8,25}$/;
    const motifMinuscule = /[a-z]/;
    const motifMajuscule = /[A-Z]/;
    const motifChiffre = /\d/;
    const motifSpecial = /\W/;
    motDePasse.style.borderColor = 'red';

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

// Fonction de vérification de la correspondance des mots de passe
function verifierCorrespondance() {
    if (mdp.value !== mdpConfirme.value || mdp.value === '') {
        mdpConfirme.style.borderColor = 'red';
        console.log("Les mots de passe ne correspondent pas.");
        return false;
    }
    mdpConfirme.style.borderColor = '';
    return true;
}

// Fonction de vérification de la présence des champs
function verifierPresence(champ) {
    if (champ.value === '') {
        champ.style.borderColor = 'red';
        return false;
    }
    champ.style.borderColor = '';
    return true;
}

// Sauvegarde des valeurs des champs
function sauvegarderVariables() {
    localStorage.setItem('nom', nom.value);
    localStorage.setItem('prenom', prenom.value);
    localStorage.setItem('email', email.value);
}
