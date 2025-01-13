let formulaire = document.getElementById('form');

let mdp = formulaire[3];
let mdpConfirme = formulaire[4];

console.log(mdp);
console.log(mdpConfirme);

mdp.addEventListener('input', verifierCorrespondance);
mdpConfirme.addEventListener('input', verifierCorrespondance);

function verifierCorrespondance() {
    if (mdp.value !== mdpConfirme.value) {
        mdpConfirme.setCustomValidity('Les mots de passe ne correspondent pas');
        mdpConfirme.style.borderColor = 'red'; // Change la couleur du bord en rouge
    } else {
        mdpConfirme.setCustomValidity('');
        mdpConfirme.style.borderColor = ''; // Réinitialise la couleur par défaut
    }
}
