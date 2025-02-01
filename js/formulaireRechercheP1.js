let grps = document.querySelectorAll('input[name="groupes[]"]'); // Récupérer tous les grps
let mmbrs = document.querySelectorAll('input[name="contacts[]"]'); // Récupérer tous les mmbrs
let mmbrsObligatoires;
let tableObligatoire = document.getElementById('tableObligatoire'); // Récupérer la table des contacts obligatoires

let nombreContactsChecked = 0;

mmbrs.forEach(membre => {
    membre.addEventListener('change', () => {
        if (membre.checked) {
            nombreContactsChecked++;
            ajouterContactEnfant(membre);
        } else {
            nombreContactsChecked--;
            retirerContactEnfant(membre, tableObligatoire);
        }
    });
});

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
    checkbox.id = `contactCheck${contact.value}`; // ID unique basé sur la valeur du contact
    checkbox.checked = true;
    celluleCheckbox.appendChild(checkbox); // Ajouter la checkbox à la cellule

    // Créer la deuxième cellule avec le nom du contact
    const celluleNom = document.createElement('td');
    celluleNom.textContent = contact.parentNode.parentNode.childNodes[3].textContent; // Texte basé sur le nom du contact

    // Ajouter les cellules à la ligne
    ligne.appendChild(celluleCheckbox);
    ligne.appendChild(celluleNom);

    // Ajouter la ligne au tableau
    const tbody = tableObligatoire.querySelector('tbody'); // Cibler le <tbody> de la table
    if (tbody) {
        tbody.appendChild(ligne);
    } else {
        console.error("La table n'a pas de <tbody>.");
    }

    mmbrsObligatoires = document.querySelectorAll('input[name="contactsObligatoires[]"]'); // Récupérer tous les mmbrs obligatoires
    mettreAJourMembresObligatoires();
}

function retirerContactEnfant(contact, table) {
    const idContact = contact.value;
    const ligne = table.querySelector(`#contactCheck${idContact}`).parentNode.parentNode;

    if (ligne) {
        ligne.remove();
    } else {
        console.error("La ligne n'a pas été trouvée.");
    }
}

function decocherCase(contact) {
    contact.checked = false;
}

let grpsCoches = new Set(); // Ensemble des grps cochés

grps.forEach(groupe => {
    groupe.addEventListener('change', function () {
        const idGroupe = this.value; // ID du groupe
        const isChecked = this.checked; // État coché ou décoché du groupe

        if (isChecked) {
            grpsCoches.add(idGroupe);
        } else {
            grpsCoches.delete(idGroupe);
        }

        // Parcourir les mmbrs du groupe modifié
        if (membres2[idGroupe]) {
            const users = membres2[idGroupe];

            users.forEach(idUtilisateur => {
                const userCheckbox = document.querySelector(`input[name="contacts[]"][value="${idUtilisateur}"]`);

                // Si l'utilisateur est dans la liste de contact affichée
                if (userCheckbox) {
                    if (isChecked) {
                        userCheckbox.checked = true;
                        userCheckbox.disabled = true;
                        ajouterContactEnfant(userCheckbox);
                    } else {
                        // Vérifier si l'utilisateur appartient à un autre groupe coché
                        let estDansUnAutreGroupe = false;

                        grpsCoches.forEach(groupeId => {
                            if (membres2[groupeId] && membres2[groupeId].includes(idUtilisateur)) {
                                estDansUnAutreGroupe = true;
                                retirerContactEnfant(userCheckbox, tableObligatoire);
                            }
                        });

                        if (!estDansUnAutreGroupe) {
                            userCheckbox.checked = false;
                            userCheckbox.disabled = false;
                            retirerContactEnfant(userCheckbox, tableObligatoire);

                        }
                    }
                }
            });
        }
    });
});

function mettreAJourMembresObligatoires() {
    mmbrsObligatoires.forEach(membre => {
        membre.addEventListener('change', () => {
            console.log("Membre changé");
            if (!membre.checked) {
                retirerContactEnfant(membre, tableObligatoire);
                decocherCase(document.querySelector(`input[name="contacts[]"][value="${membre.value}"]`));
            }
        });
    });
}