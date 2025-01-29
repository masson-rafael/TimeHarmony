let formulaire = document.getElementById('form');
let btn = document.getElementById('boutonInscription');

let nom = formulaire[0];
let prenom = formulaire[1];
let email = formulaire[2];
let mdp = formulaire[3];
let mdpConfirme = formulaire[4];

let nomError = document.getElementById('nomError');
let prenomError = document.getElementById('prenomError');
let emailError = document.getElementById('emailError');
let mdpError = document.getElementById('passwordError');
let mdpConfirmeError = document.getElementById('passwordConfirmeError');

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
    // Chargement des valeurs sauvegardées
    nom.value = localStorage.getItem('nom');
    prenom.value = localStorage.getItem('prenom');
    email.value = localStorage.getItem('email');

    // Focus sur le premier champ vide
    if (nom.value === '') {
        nomError.textContent = 'Veuillez saisir votre nom.';
        nom.style.borderColor = 'red';
        mdpVides();
        nom.focus();
    } else if (prenom.value === '') {
        prenomError.textContent = 'Veuillez saisir votre prénom.';
        prenom.style.borderColor = 'red';
        mdpVides();
        prenom.focus();
    } else if (email.value === '') {
        emailError.textContent = 'Veuillez saisir votre adresse email.';
        email.style.borderColor = 'red';
        mdpVides();
        email.focus();
    } else {
        mdpVides();
        mdp.focus();
    }
    btn.disabled = true;
    verifierTousLesChamps();
}

function mdpVides() {
    mdpError.textContent = 'Veuillez saisir votre mot de passe.';
    mdpConfirmeError.textContent = 'Veuillez confirmer votre mot de passe.';
    mdp.style.borderColor = 'red';
    mdpConfirme.style.borderColor = 'red';
}

// Vérification de la validité des champs
function verifierTousLesChamps() {
    const nomCorrect = verifierPresence(nom, nomError);
    const prenomCorrect = verifierPresence(prenom, prenomError);
    const emailCorrect = verifierPatternMail(email);
    const mdpCorrect = verifierPattern(mdp);
    const mdpCorrespondance = verifierCorrespondance();

    // Activation ou désactivation du bouton
    btn.disabled = !(nomCorrect && prenomCorrect && emailCorrect && mdpCorrect && mdpCorrespondance);
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

    if(motDePasse.value === '') {
        mdpError.textContent = 'Veuillez saisir votre mot de passe.';
        return false;
    }

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
    mdpConfirmeError.innerHTML = erreursMdp.join('<br>');

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
        mdpConfirmeError.textContent = 'Les mots de passe ne correspondent pas.';
        return false;
    }
    mdpConfirme.style.borderColor = '';
    mdpConfirmeError.textContent = '';
    return true;
}

// Fonction de vérification de la présence des champs
function verifierPresence(champ, error) {
    if (champ.value === '') {
        champ.style.borderColor = 'red';
        error.textContent = 'Veuillez saisir votre ' + champ.name + '.';
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
