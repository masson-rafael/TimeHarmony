// === VARIABLES GLOBALES ===
let grps = document.querySelectorAll('input[name="groupes[]"]'); // Récupérer tous les groupes
let mmbrs = document.querySelectorAll('input[name="contacts[]"]'); // Récupérer tous les membres
let mmbrsObligatoires; // Liste des membres obligatoires (à jour dynamiquement)
let tableObligatoire = document.getElementById('tableObligatoire'); // Table des contacts obligatoires

let nombreContactsChecked = 0; // Compteur des contacts cochés
let grpsCoches = new Set(); // Ensemble des groupes cochés

// === GESTION DES MEMBRES ===

// Écouteur pour chaque membre
mmbrs.forEach(membre => {
    membre.addEventListener('change', () => {
        if (membre.checked) {
            nombreContactsChecked++;
            ajouterContactEnfant(membre);
        } else {
            nombreContactsChecked--;
            retirerContactEnfant(membre, tableObligatoire);

            // Vérifier si le membre retiré était le dernier du groupe
            console.log(idUtilisateur);
            verifierSiDernierDuGroupe(idUtilisateur);
        }
    });
});

/**
 * Ajouter un membre dans la table des contacts obligatoires
 * @param {HTMLElement} contact - L'élément input du membre à ajouter
 */
function ajouterContactEnfant(contact) {
    // Créer la ligne <tr>
    const ligne = document.createElement('tr');

    // Créer la première cellule avec l'input checkbox
    const celluleCheckbox = document.createElement('td');
    const checkbox = document.createElement('input');
    checkbox.className = 'form-check-input';
    checkbox.type = 'checkbox';
    checkbox.name = 'contactsObligatoires[]';
    checkbox.value = contact.value; // ID du contact
    checkbox.id = `contactCheck${contact.value}`;
    checkbox.checked = true;
    celluleCheckbox.appendChild(checkbox);

    // Créer la deuxième cellule avec le nom du contact
    const celluleNom = document.createElement('td');
    celluleNom.textContent = contact.parentNode.parentNode.childNodes[3].textContent; // Texte du nom

    // Ajouter les cellules à la ligne
    ligne.appendChild(celluleCheckbox);
    ligne.appendChild(celluleNom);

    // Ajouter la ligne au tableau
    const tbody = tableObligatoire.querySelector('tbody');
    if (tbody) {
        tbody.appendChild(ligne);
    } else {
        console.error("La table n'a pas de <tbody>.");
    }

    // Mettre à jour la liste des membres obligatoires
    mmbrsObligatoires = document.querySelectorAll('input[name="contactsObligatoires[]"]');
    mettreAJourMembresObligatoires();
}

/**
 * Retirer un membre de la table des contacts obligatoires
 * @param {HTMLElement} contact - L'élément input du membre à retirer
 * @param {HTMLElement} table - La table où le membre doit être retiré
 */
function retirerContactEnfant(contact, table) {
    const idContact = contact.value;
    const ligne = table.querySelector(`#contactCheck${idContact}`).parentNode.parentNode;

    if (ligne) {
        ligne.remove();
    } else {
        console.error("La ligne n'a pas été trouvée.");
    }
}

/**
 * Décocher une case
 * @param {HTMLElement} contact - L'élément input à décocher
 */
function decocherCase(contact) {
    contact.checked = false;
}

// === GESTION DES GROUPES (MODIFIÉ) ===
grps.forEach(groupe => {
    groupe.addEventListener('change', function () {
        const idGroupe = this.value; // ID du groupe
        const isChecked = this.checked; // État coché ou décoché du groupe

        if (isChecked) {
            grpsCoches.add(idGroupe); // Ajouter le groupe à l'ensemble des groupes cochés
        } else {
            grpsCoches.delete(idGroupe); // Retirer le groupe de l'ensemble des groupes cochés
        }

        // Parcourir les membres du groupe modifié
        if (membres2[idGroupe]) {
            const users = membres2[idGroupe];

            users.forEach(idUtilisateur => {
                const userCheckbox = document.querySelector(`input[name="contacts[]"][value="${idUtilisateur}"]`);

                if (userCheckbox) {
                    if (isChecked) {
                        // Groupe activé : cocher et désactiver les cases
                        userCheckbox.checked = true;
                        ajouterContactEnfant(userCheckbox);
                    } else {
                        // Groupe désactivé : vérifier si les cases doivent être réactivées
                        let estDansUnAutreGroupe = false;

                        grpsCoches.forEach(groupeId => {
                            if (membres2[groupeId] && membres2[groupeId].includes(idUtilisateur)) {
                                estDansUnAutreGroupe = true;
                            }
                        });

                        if (!estDansUnAutreGroupe) {
                            // Réactiver la checkbox et décocher
                            userCheckbox.checked = false;
                            retirerContactEnfant(userCheckbox, tableObligatoire);
                        }
                    }
                }
            });

            // Nouvelle fonctionnalité : vérifier si le groupe est vidé de la table obligation
            verifierEtReactiverMembresDuGroupe(idGroupe);
        }
    });
});

/**
 * Vérifie si un groupe est vidé de la table obligatoire, et rend les membres cliquables
 * @param {string} idGroupe - L'ID du groupe à vérifier
 */
function verifierEtReactiverMembresDuGroupe(idGroupe) {
    if (membres2[idGroupe]) {
        const users = membres2[idGroupe];
        let tousRetires = true;

        users.forEach(idUtilisateur => {
            const obligatoireCheckbox = document.querySelector(`#contactCheck${idUtilisateur}`);
            if (obligatoireCheckbox) {
                tousRetires = false; // Il reste un membre dans la table
            }
        });

        if (tousRetires) {
            // Rendre toutes les cases des membres cliquables
            users.forEach(idUtilisateur => {
                const userCheckbox = document.querySelector(`input[name="contacts[]"][value="${idUtilisateur}"]`);
                if (userCheckbox) {
                    userCheckbox.disabled = false; // Réactiver la case
                }
            });
        }
    }
}

/**
 * Vérifie si un contact est le dernier dans la table obligatoire pour un groupe donné,
 * en excluant l'ID spécifié.
 * Si oui, décoche le groupe dans la liste principale.
 * @param {string} idContact - L'ID du contact retiré
 */
function verifierSiDernierDuGroupe(idContact) {
    // Parcourir les groupes pour lesquels ce contact pourrait appartenir
    Object.keys(membres2).forEach(idGroupe => {
        const membresDuGroupe = membres2[idGroupe]; // Liste des membres du groupe
        let tousRetires = true;

        // Vérifier si un membre du groupe (autre que le contact retiré) est toujours dans la table des obligatoires
        membresDuGroupe.forEach(idMembre => {
            if (idMembre !== idContact) { // Exclure le contact actuellement traité
                const obligatoireCheckbox = document.querySelector(`#contactCheck${idMembre}`);
                if (obligatoireCheckbox) {
                    tousRetires = false; // Il reste au moins un autre membre dans la table
                }
            }
        });

        // Si tous les membres du groupe (autre que le contact traité) sont retirés, décocher le groupe
        if (tousRetires) {
            const groupeCheckbox = document.querySelector(`input[name="groupes[]"][value="${idGroupe}"]`);
            if (groupeCheckbox) {
                groupeCheckbox.checked = false; // Décocher la case du groupe
                grpsCoches.delete(idGroupe); // Retirer le groupe de l'ensemble des groupes cochés
            }
        }
    });
}

// === GESTION DES MEMBRES OBLIGATOIRES ===

/**
 * Mettre à jour les membres obligatoires lorsqu'ils sont cochés ou décochés
 */
function mettreAJourMembresObligatoires() {
    mmbrsObligatoires.forEach(membre => {
        membre.addEventListener('change', () => {
            if (!membre.checked) {
                retirerContactEnfant(membre, tableObligatoire);
                decocherCase(document.querySelector(`input[name="contacts[]"][value="${membre.value}"]`));
            }
        });
    });
}