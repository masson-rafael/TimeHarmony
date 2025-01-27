let formulaire = document.getElementById('formCreationGroupe');
let btn = document.getElementById('boutonCreationGroupe');

let nom = formulaire[0];
let nomError = document.getElementById('nomError');
let description = formulaire[1];
let descriptionError = document.getElementById('descriptionError');
let contacts = formulaire[2];
let contactsError = document.getElementById('contactsError');

// Ajout des evenements qui declechent les fonctions de validation à chaque input
nom.addEventListener('input', verifierTousLesChampsCreation);
description.addEventListener('input', verifierTousLesChampsCreation);

// Sélectionne toutes les cases à cocher de la table
document.querySelectorAll('table input[type="checkbox"]').forEach(function(checkbox) {
    // Ajoute un événement "click" à chaque case à cocher
    checkbox.addEventListener('click', function() {
        verifierTousLesChampsCreation(); // Appelle la fonction
        console.log(checkbox.value); // Affiche la valeur associée à la case
    });
});


// Préparation des champs au chargement du formulaire
preparationChampsCreation();

// Fonction de préparation des champs
function preparationChampsCreation() {
    btn.disabled = true;
    nom.focus();
    verifierTousLesChampsCreation();
}

// Fonction de vérication de l'adresse nom
function verifierPatternNom(nom) {
    const motifnom = /^[a-zA-ZÀ-ÿ0-9- ]+$/;

    if (nom.value === '') {
        nom.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        nomError.textContent = 'Veuillez saisir un nom.';
        return false;
    }

    if (nom.value.length > 50) {
        nom.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        nomError.textContent = 'Le nom ne doit pas dépasser 50 caractères.';
        return false;
    }

    if (!motifnom.test(nom.value)) {
        nom.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        nomError.textContent = 'Le nom ne correspond pas au format attendu.';
        return false;
    }

    nom.style.borderColor = ''; // Bordure par défaut (succès)
    nomError.textContent = '';
    return true;
}

function verifierPatternDescription(description) {
    description.style.borderColor = 'red'; // Bordure rouge en cas d'erreur

    if(description.value === '') {
        descriptionError.textContent = 'Veuillez saisir une description.';
        return false;
    } if(description.value.length > 200) {
        description.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        descriptionError.textContent = 'La description ne doit pas dépasser 200 caractères.';
        return false;
    } if (description.value.length < 10) {
        description.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        descriptionError.textContent = 'La description doit contenir au moins 10 caractères.';
        return false;
    }

    description.style.borderColor = ''; // Bordure par défaut (succès)
    descriptionError.textContent = '';
    return true;
}

function verifierPatternContacts() {
    // Sélectionne toutes les cases à cocher avec la classe 'form-check-input'
    console.log('contacts appellee');
    const checkboxes = document.querySelectorAll('.form-check-input');
    let checkedCount = 0;

    // Compte le nombre de cases cochées
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            checkedCount++;
        }
    });

    console.log(checkedCount);

    // Si aucune case n'est cochée, affiche un message d'erreur
    if (checkedCount === 0) {
        const checkboxes = document.querySelectorAll('.form-check-input');
        checkboxes.forEach(checkbox => {
            contacts.style.color = 'red'; // Bordure rouge en cas d'erreur
        });
        contactsError.textContent = 'Au moins un contact doit être sélectionné.';
        return false;
    }

    // Si au moins une case est cochée, réinitialise l'erreur
    contacts.style.borderColor = ''; // Bordure par défaut
    contactsError.textContent = '';
    return true;
}


// Fonction de vérification de la présence des champs
function verifierPresenceCreation() {
    if (nom.value === '' || description.value === '' || contacts.value === '') {
        return false;
    }
    return true;
}

// Fonction générale de vérification de tous les champs
function verifierTousLesChampsCreation() {
    const nomCorrect = verifierPatternNom(nom);
    const descriptionCorrect = verifierPatternDescription(description);
    const contactsCorrect = verifierPatternContacts(contacts);
    const presenceCorrect = verifierPresenceCreation();

    // Activer ou désactiver le bouton en fonction des validations
    btn.disabled = !(nomCorrect && descriptionCorrect && presenceCorrect && contactsCorrect);
}