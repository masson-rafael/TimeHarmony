let formulaire = document.getElementById('form');
let btn = document.getElementById('boutonInscription');

let mdp = formulaire[3];
let mdpConfirme = formulaire[4];

console.log(mdp);
console.log(mdpConfirme);

mdp.addEventListener('input', verifierCorrespondance);
mdpConfirme.addEventListener('input', verifierCorrespondance);
mdp.addEventListener('input', () => verifierPattern(mdp));
mdpConfirme.addEventListener('input', () => verifierPattern(mdpConfirme));
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