let formulaire = document.getElementById('formCreationGroupe');
let btn = document.getElementById('boutonCreationGroupe');

let nom = formulaire[0];
let nomError = document.getElementById('nomError');
let description = formulaire[1];
let descriptionError = document.getElementById('descriptionError');
let contacts = formulaire[2];
let contactsError = document.getElementById('contactsError');

// Préparation des champs au chargement du formulaire
preparationChamps();

// Ajout des evenements qui declechent les fonctions de validation à chaque input
nom.addEventListener('input', verifierTousLesChamps);
description.addEventListener('input', verifierTousLesChamps);
contacts.addEventListener('change', verifierTousLesChamps);

// Fonction de préparation des champs
function preparationChamps() {
    btn.disabled = true;
    nom.focus();
}

// Fonction de vérication de l'adresse nom
function verifierPatternNom(nom) {
    const motifnom = /^[a-zA-ZÀ-ÿ0-9- ]+$/;

    if (nom.value === '') {
        nom.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        nomError.textContent = 'Veuillez saisir un nom.';
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
    } if(description.value.length > 100) {
        description.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        descriptionError.textContent = 'La description ne doit pas dépasser 100 caractères.';
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
    const checkboxes = document.querySelectorAll('.form-check-input');
    let checkedCount = 0;

    // Compte le nombre de cases cochées
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            checkedCount++;
        }
    });

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
function verifierPresence() {
    if (nom.value === '' || description.value === '' || contacts.value === '') {
        return false;
    }
    return true;
}

// Fonction générale de vérification de tous les champs
function verifierTousLesChamps() {
    const nomCorrect = verifierPatternNom(nom);
    const descriptionCorrect = verifierPatternDescription(description);
    const contactsCorrect = verifierPatternContacts(contacts);
    const presenceCorrect = verifierPresence();

    // Activer ou désactiver le bouton en fonction des validations
    btn.disabled = !(nomCorrect && descriptionCorrect && presenceCorrect && contactsCorrect);
}