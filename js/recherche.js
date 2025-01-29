/* 
Quand l’utilisateur coche ou décoche un groupe, 
tous les contacts qui sont dans ce groupe sont aussi cochés ou décochés
*/
let groupes = document.querySelectorAll('input[name="groupes[]"]'); // Récupérer tous les groupes

let groupesCoches = new Set(); // Ensemble des groupes cochés

groupes.forEach(groupe => {
    groupe.addEventListener('change', function () {
        const idGroupe = this.value; // ID du groupe
        const isChecked = this.checked; // État coché ou décoché du groupe

        if (isChecked) {
            groupesCoches.add(idGroupe);
        } else {
            groupesCoches.delete(idGroupe);
        }

        // Parcourir les membres du groupe modifié
        if (membres[idGroupe]) {
            const users = membres[idGroupe];

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

                        groupesCoches.forEach(groupeId => {
                            if (membres[groupeId] && membres[groupeId].includes(idUtilisateur)) {
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