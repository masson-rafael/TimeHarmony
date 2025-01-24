let formulaire = document.getElementById('form');
let btn = document.getElementById('boutonInscription');

let nom = formulaire[0];
let prenom = formulaire[1];
let email = formulaire[2];
let mdp = formulaire[3];
let mdpConfirme = formulaire[4];

nom.value = localStorage.getItem('nom');
prenom.value = localStorage.getItem('prenom');
email.value = localStorage.getItem('email');

mdp.addEventListener('input', verifierCorrespondance);
mdpConfirme.addEventListener('input', verifierCorrespondance);
mdp.addEventListener('input', () => verifierPattern(mdp));
mdpConfirme.addEventListener('input', () => verifierPattern(mdpConfirme));

nom.addEventListener('input', verifierPresence);
prenom.addEventListener('input', verifierPresence);
email.addEventListener('input', verifierPresence);

btn.addEventListener('click', sauvegarderVariables);

btn.disabled = true;

function verifierCorrespondance() {
    if (mdp.value !== mdpConfirme.value) {
        // mdpConfirme.setCustomValidity('Les mots de passe ne correspondent pas');
        mdpConfirme.style.borderColor = 'red'; // Change la couleur du bord en rouge
        btn.disabled = true;
        // mdpConfirme.reportValidity();
    } else {
        // mdpConfirme.setCustomValidity('');
        mdpConfirme.style.borderColor = ''; // Réinitialise la couleur par défaut
        btn.disabled = false;
    }
}

function verifierPattern(motDePasse) {
    const motifTaille = /^[a-zA-Z\d\W]{8,25}$/; // Vérifie que la longueur est entre 8 et 25 caractères.
    const motifMinuscule = /[a-z]/; // Vérifie la présence d'au moins une lettre minuscule.
    const motifMajuscule = /[A-Z]/; // Vérifie la présence d'au moins une lettre majuscule.
    const motifChiffre = /\d/; // Vérifie la présence d'au moins un chiffre.
    const motifSpecial = /\W/; // Vérifie la présence d'au moins un caractère spécial (non alphanumérique).
    
    btn.disabled = true; // Le bouton est désactivé par défaut.
    motDePasse.style.borderColor = 'red'; // Bordure rouge par défaut.
    
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
        btn.disabled = false; // Active le bouton.
        console.log("Mot de passe valide.");
    }
}

function verifierPresence() {
    if (nom.value === '' || prenom.value === '' || email.value === '') {
        btn.disabled = true;
    } else {
        btn.disabled = false;
    }
}

function sauvegarderVariables() {
    localStorage.setItem('nom', nom.value);
    localStorage.setItem('prenom', prenom.value);
    localStorage.setItem('email', email.value);
}