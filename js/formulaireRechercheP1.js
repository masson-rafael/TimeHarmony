let grps = document.querySelectorAll('input[name="grps[]"]'); // Récupérer tous les grps
let mmbrs = document.querySelectorAll('input[name="contacts[]"]'); // Récupérer tous les mmbrs
let tableObligatoire = document.getElementById('tableObligatoire'); // Récupérer la table des contacts obligatoires
let btn = document.getElementById('boutonFinPage1');

btn.disabled = true;

mmbrs.forEach(membre => {
    membre.addEventListener('change', () => {
        if (membre.checked) {
            btn.disabled = false;
            ajouterContactEnfant(membre);
        } else {
            btn.disabled = true;
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
    checkbox.name = 'contacts[]';
    checkbox.value = contact.value; // ID du contact
    checkbox.id = `contactCheck${contact.value}`; // ID unique basé sur la valeur du contact
    celluleCheckbox.appendChild(checkbox); // Ajouter la checkbox à la cellule

    // Créer la deuxième cellule avec le nom du contact
    const celluleNom = document.createElement('td');
    celluleNom.textContent = membres2[contact.value]; // Texte basé sur le nom du contact

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
                    } else {
                        // Vérifier si l'utilisateur appartient à un autre groupe coché
                        let estDansUnAutreGroupe = false;

                        grpsCoches.forEach(groupeId => {
                            if (membres2[groupeId] && membres2[groupeId].includes(idUtilisateur)) {
                                estDansUnAutreGroupe = true;
                            }
                        });

                        if (!estDansUnAutreGroupe) {
                            userCheckbox.checked = false;
                            userCheckbox.disabled = false;
                        }
                    }
                }
            });
        }
    });
});