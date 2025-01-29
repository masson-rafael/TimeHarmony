let form = document.getElementById('formModificationGroupe');
let bouton = document.getElementById('boutonModificationGroupe');

let nomGroupe = form[0];
let nomGroupeError = document.getElementById('nomGroupeError');
let descriptionGroupe = form[1];
let descriptionGroupeError = document.getElementById('descriptionGroupeError');
let contactsGroupe = form[2];
let contactsGroupeError = document.getElementById('contactsGroupeError');

// Préparation des champs au chargement du form
verifierTousLesChamps();
nomGroupe.focus();

// Ajout des evenements qui declechent les fonctions de validation à chaque input
nomGroupe.addEventListener('input', verifierTousLesChamps);
descriptionGroupe.addEventListener('input', verifierTousLesChamps);

// Sélectionne toutes les cases à cocher de la table
document.querySelectorAll('table input[type="checkbox"]').forEach(function(checkbox) {
    // Ajoute un événement "click" à chaque case à cocher
    checkbox.addEventListener('click', function() {
        verifierTousLesChamps(); // Appelle la fonction
        console.log(checkbox.value); // Affiche la valeur associée à la case
    });
});

// Fonction de vérication de l'adresse nomGroupe
function verifierPatternNomGroupe(nomGroupe) {
    const motifnomGroupe = /^[a-zA-ZÀ-ÿ0-9- ]+$/;

    if (nomGroupe.value === '') {
        nomGroupe.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        nomGroupeError.textContent = 'Veuillez saisir un nom pour le groupe.';
        return false;
    }

    if (!motifnomGroupe.test(nomGroupe.value)) {
        nomGroupe.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        nomGroupeError.textContent = 'Le nom du groupe ne correspond pas au format attendu.';
        return false;
    }

    nomGroupe.style.borderColor = ''; // Bordure par défaut (succès)
    nomGroupeError.textContent = '';
    return true;
}

function verifierPatternDescriptionGroupe(descriptionGroupe) {
    descriptionGroupe.style.borderColor = 'red'; // Bordure rouge en cas d'erreur

    if(descriptionGroupe.value === '') {
        descriptionGroupeError.textContent = 'Veuillez saisir une description du groupe.';
        return false;
    } if(descriptionGroupe.value.length > 100) {
        descriptionGroupe.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        descriptionGroupeError.textContent = 'La description du groupe ne doit pas dépasser 100 caractères.';
        return false;
    } if (descriptionGroupe.value.length < 10) {
        descriptionGroupe.style.borderColor = 'red'; // Bordure rouge en cas d'erreur
        descriptionGroupeError.textContent = 'La description du groupe doit contenir au moins 10 caractères.';
        return false;
    }

    descriptionGroupe.style.borderColor = ''; // Bordure par défaut (succès)
    descriptionGroupeError.textContent = '';
    return true;
}

function verifierPatternContactsGroupe() {
    // Sélectionne toutes les cases à cocher avec la classe 'form-check-input'
    const checkboxes = document.querySelectorAll('.form-check-input');
    let checkedCount = 0;

    // Compte le nomGroupebre de cases cochées
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            checkedCount++;
        }
    });

    // Si aucune case n'est cochée, affiche un message d'erreur
    if (checkedCount === 0) {
        const checkboxes = document.querySelectorAll('.form-check-input');
        checkboxes.forEach(checkbox => {
            checkbox.style.color = 'red'; // Bordure rouge en cas d'erreur
        });
        contactsGroupeError.textContent = 'Au moins un contact doit être sélectionné.';
        return false;
    }

    // Si au moins une case est cochée, réinitialise l'erreur
    contactsGroupe.style.borderColor = ''; // Bordure par défaut
    contactsGroupeError.textContent = '';
    return true;
}


// Fonction de vérification de la présence des champs
function verifierPresence() {
    if (nomGroupe.value === '' || descriptionGroupe.value === '' || contactsGroupe.value === '') {
        return false;
    }
    return true;
}

// Fonction générale de vérification de tous les champs
function verifierTousLesChamps() {
    const nomGroupeCorrect = verifierPatternNomGroupe(nomGroupe);
    const descriptionGroupeCorrect = verifierPatternDescriptionGroupe(descriptionGroupe);
    const contactsGroupeCorrect = verifierPatternContactsGroupe(contactsGroupe);
    const presenceCorrect = verifierPresence();

    // Activer ou désactiver le bouton en fonction des validations
    bouton.disabled = !(nomGroupeCorrect && descriptionGroupeCorrect && presenceCorrect && contactsGroupeCorrect);
}