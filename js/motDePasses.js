let formulaire = document.getElementById('form');
let btn = document.getElementById('boutonInscription');

let nom = formulaire[0];
let prenom = formulaire[1];
let email = formulaire[2];
let mdp = formulaire[3];
let mdpConfirme = formulaire[4];

// Chargement des valeurs sauvegardées
nom.value = localStorage.getItem('nom');
prenom.value = localStorage.getItem('prenom');
email.value = localStorage.getItem('email');

btn.disabled = true;

// Ajout des écouteurs d'événements pour tous les champs
nom.addEventListener('input', verifierTousLesChamps);
prenom.addEventListener('input', verifierTousLesChamps);
email.addEventListener('input', verifierTousLesChamps);
mdp.addEventListener('input', verifierTousLesChamps);
mdpConfirme.addEventListener('input', verifierTousLesChamps);

btn.addEventListener('click', sauvegarderVariables);

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

function verifierPatternMail(champEmail) {
    const motifEmail = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!motifEmail.test(champEmail.value)) {
        champEmail.style.borderColor = 'red';
        return false;
    }
    champEmail.style.borderColor = '';
    return true;
}

function verifierPattern(champMotDePasse) {
    const motifTaille = /^[a-zA-Z\d\W]{8,25}$/;
    const motifMinuscule = /[a-z]/;
    const motifMajuscule = /[A-Z]/;
    const motifChiffre = /\d/;
    const motifSpecial = /\W/;

    const valide =
        motifTaille.test(champMotDePasse.value) &&
        motifMinuscule.test(champMotDePasse.value) &&
        motifMajuscule.test(champMotDePasse.value) &&
        motifChiffre.test(champMotDePasse.value) &&
        motifSpecial.test(champMotDePasse.value);

    champMotDePasse.style.borderColor = valide ? '' : 'red';
    return valide;
}

function verifierCorrespondance() {
    if (mdp.value !== mdpConfirme.value || mdp.value === '') {
        mdpConfirme.style.borderColor = 'red';
        return false;
    }
    mdpConfirme.style.borderColor = '';
    return true;
}

function verifierPresence(champ) {
    if (champ.value === '') {
        champ.style.borderColor = 'red';
        return false;
    }
    champ.style.borderColor = '';
    return true;
}

function sauvegarderVariables() {
    localStorage.setItem('nom', nom.value);
    localStorage.setItem('prenom', prenom.value);
    localStorage.setItem('email', email.value);
}
