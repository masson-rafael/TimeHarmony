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
        mdpConfirme.setCustomValidity('Les mots de passe ne correspondent pas');
        mdpConfirme.style.borderColor = 'red'; // Change la couleur du bord en rouge
        btn.disabled = true;
    } else {
        mdpConfirme.setCustomValidity('');
        mdpConfirme.style.borderColor = ''; // Réinitialise la couleur par défaut
        btn.disabled = false;
    }
}

function verifierPattern(motDePasse) {
    const motif = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[a-zA-Z\d\W]{8,25}$/;

    if (motDePasse.value.length < 8 || motDePasse.value.length > 25 || !motif.test(motDePasse.value)) {
        motDePasse.setCustomValidity('Le mot de passe doit contenir entre 8 et 25 caractères et au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial');
        motDePasse.style.borderColor = 'red';
        btn.disabled = true;
    } else {
        motDePasse.setCustomValidity('');
        motDePasse.style.borderColor = '';
        btn.disabled = false;
        if(mdp.value !== mdpConfirme.value) {
            btn.disabled = true;
        }
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