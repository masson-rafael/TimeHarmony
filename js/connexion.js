let formulaire = document.getElementById('form');
let btn = document.getElementById('boutonConnexion');

let email = formulaire[0];
let mdp = formulaire[1];

btn.disabled = true;

email.value = localStorage.getItem('email');

mdp.addEventListener('input', verifierPattern);
email.addEventListener('input', verifierPresence);
mdp.addEventListener('input', verifierPresence);

function verifierPattern(motDePasse) {
    const motif = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[a-zA-Z\d\W]{8,25}$/;

    if (motDePasse.value.length < 8 || motDePasse.value.length > 25 || !motif.test(motDePasse.value)) {
        motDePasse.setCustomValidity('Le mot de passe doit contenir entre 8 et 25 caractères et au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial');
        motDePasse.style.borderColor = 'red';
        btn.disabled = true;
    } else {
        btn.disabled = false;
        motDePasse.style.borderColor = '';
    }
}

function verifierPresence() {
    if (email.value === '' || mdp.value === '') {
        motDePasse.style.borderColor = 'red';
        btn.disabled = true;
    } else {
        btn.disabled = false;
        motDePasse.style.borderColor = '';
    }
}