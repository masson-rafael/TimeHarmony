const tableObligatoire = document.getElementById('tableObligatoire');

class Contact {
    constructor(element) {
        this.element = element;
        this.value = element.value;
        this.obligatoryElement = null;
        this.name = element.parentNode.parentNode.childNodes[3].textContent;
        this.bindEvents();
    }

// Modification de la méthode bindEvents dans la classe Contact
bindEvents() {
    this.element.addEventListener('change', () => {
        if (this.element.checked) {
            this.addToObligatoryTable();
            // Nouvelle méthode pour cocher les groupes contenant ce contact
            this.checkRelatedGroups();
            // Vérifie si tous les contacts des groupes associés sont cochés
            GroupManager.checkIfAllContactsSelected();
        } else {
            this.removeFromObligatoryTable();
            this.uncheckRelatedGroups();
            this.checkIfLastInGroup();
        }
    });
}

// Nouvelle méthode pour cocher tous les groupes auxquels ce contact appartient
checkRelatedGroups() {
    Object.keys(membres2).forEach(groupId => {
        const membersInGroup = membres2[groupId];
        if (membersInGroup.includes(this.value)) {
            // Vérifier si tous les autres membres du groupe sont déjà cochés
            let allOtherMembersSelected = true;
            for (const memberId of membersInGroup) {
                if (memberId !== this.value) {
                    const contactElement = document.querySelector(`input[name="contacts[]"][value="${memberId}"]`);
                    if (contactElement && !contactElement.checked) {
                        allOtherMembersSelected = false;
                        break;
                    }
                }
            }
            
            // Si tous les autres membres sont déjà cochés, cocher ce groupe
            if (allOtherMembersSelected) {
                const groupCheckbox = document.querySelector(`input[name="groupes[]"][value="${groupId}"]`);
                if (groupCheckbox && !groupCheckbox.checked) {
                    groupCheckbox.checked = true;
                    GroupManager.checkedGroups.add(groupId);
                }
            }
        }
    });
}

    // Nouvelle méthode pour décocher tous les groupes auxquels ce contact appartient
    uncheckRelatedGroups() {
        Object.keys(membres2).forEach(groupId => {
            const membersInGroup = membres2[groupId];
            console.log(membersInGroup);
            console.log(this.value);
            console.log(membersInGroup.includes(parseInt(this.value)));
            if (membersInGroup.includes(parseInt(this.value))) {
                const groupCheckbox = document.querySelector(`input[name="groupes[]"][value="${groupId}"]`);
                if (groupCheckbox && groupCheckbox.checked) {
                    groupCheckbox.checked = false;
                    GroupManager.checkedGroups.delete(groupId);
                }
            } else {
                console.log('Not in group', groupId);
            }
        });
    }

    addToObligatoryTable() {
        // Vérifier si l'élément existe déjà dans la table obligatoire
        if (this.checkIfAlreadyInTable()) {
            // Si l'élément existe déjà, ne pas l'ajouter à nouveau
            return;
        }
    
        console.log(tableObligatoire);
        // If the table doesn't exist, create it and add it to the DOM
        if (!tableObligatoire) {
            // Find a suitable container to append the table to
            const container = document.querySelector('.container') || document.body;
            
            // Create the table HTML structure
            const tableHTML = `
                <table id="tableObligatoire" class="table table-bordered table-hover">
                    <thead>
                        <tr class="table-primary">
                            <th class="bg-primary text-secondary" scope="col">Spécifiez le type de présence</th>
                            <th class="bg-primary text-secondary" scope="col">Nom des contacts présents</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            `;
            
            // Create a div to hold the table
            const tableDiv = document.createElement('div');
            tableDiv.innerHTML = tableHTML;
            
            // Append the table to the container
            container.appendChild(tableDiv.firstChild);
            
            console.log('Created obligatory table');
        }

        const tbody = tableObligatoire.querySelector('tbody');
    
        const row = document.createElement('tr');
        
        // Cellule checkbox remplacée par boutons radio selon le commentaire
        const checkboxCell = document.createElement('td');
        checkboxCell.className = 'bg-secondary';
        
        // Premier bouton radio (Présent)
        const checkBox1 = document.createElement('input');
        checkBox1.className = 'btn-check';
        checkBox1.type = 'radio';
        checkBox1.name = `contactStatus${this.value}`;  // Nom unique par contact
        checkBox1.value = 'present';
        checkBox1.id = `contactCheckP${this.value}`;
        checkBox1.checked = true;
        checkBox1.autocomplete = 'off';
        
        const label1 = document.createElement('label');
        label1.className = 'btn';
        label1.htmlFor = `contactCheckP${this.value}`;
        label1.textContent = 'Présent';
        
        checkboxCell.appendChild(checkBox1);
        checkboxCell.appendChild(label1);
    
        // Deuxième bouton radio (Obligatoire)
        const checkBox2 = document.createElement('input');
        checkBox2.className = 'btn-check';
        checkBox2.type = 'radio';
        checkBox2.name = `contactStatus${this.value}`;  // Même nom pour grouper les radios
        checkBox2.value = 'obligatoire';
        checkBox2.id = `contactCheckO${this.value}`;
        checkBox2.checked = false;
        checkBox2.autocomplete = 'off';
        
        const label2 = document.createElement('label');
        label2.className = 'btn';
        label2.htmlFor = `contactCheckO${this.value}`;
        label2.textContent = 'Obligatoire';
        
        checkboxCell.appendChild(checkBox2);
        checkboxCell.appendChild(label2);
        
        // Input caché pour conserver la valeur pour le formulaire
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'contactsObligatoires[]';
        hiddenInput.value = this.value;
        checkboxCell.appendChild(hiddenInput);
    
        // Cellule nom
        const nameCell = document.createElement('td');
        nameCell.className = 'bg-secondary';
        nameCell.textContent = this.name;
    
        row.appendChild(checkboxCell);
        row.appendChild(nameCell);
        tbody.appendChild(row);
    
        // Mise à jour de la référence à l'élément obligatoire
        this.obligatoryElement = hiddenInput;
        
        // Ajout des événements pour les boutons radio
        this.bindRadioEvents(checkBox1, checkBox2);
    }
    
    // Nouvelle méthode pour vérifier si un contact est déjà dans la table
    checkIfAlreadyInTable() {
        //const tableObligatoire = document.getElementById('tableObligatoire');
        if (!tableObligatoire) {
            console.log("Pas de table");
            return false;
        };
        
        const tbody = tableObligatoire.querySelector('tbody');
        if (!tbody) {
            console.log("Pas de tbody");
            return false;
        }
        
        // Rechercher un élément caché avec la même valeur
        const existingInput = tbody.querySelector(`input[type="hidden"][name="contactsObligatoires[]"][value="${this.value}"]`);
        
        // Rechercher les boutons radio associés
        const existingRadioP = tbody.querySelector(`#contactCheckP${this.value}`);
        const existingRadioO = tbody.querySelector(`#contactCheckO${this.value}`);
        
        // Si l'un des éléments existe, le contact est déjà dans la table
        const alreadyExists = existingInput || existingRadioP || existingRadioO;
        
        // Si le contact existe déjà, mettre à jour la référence à l'élément obligatoire
        if (alreadyExists && existingInput) {
            this.obligatoryElement = existingInput;
        }
        
        return alreadyExists;
    }
    
    // Nouvelle méthode pour gérer les événements des boutons radio
    bindRadioEvents(presentRadio, obligatoireRadio) {
        // Les deux boutons pour ce contact
        presentRadio.addEventListener('change', () => {
            if (presentRadio.checked) {
                // L'utilisateur a sélectionné "Présent"
                // On garde le contact dans la table mais on le marque comme non obligatoire
                obligatoireRadio.checked = false;
            }
        });
        
        obligatoireRadio.addEventListener('change', () => {
            if (obligatoireRadio.checked) {
                // L'utilisateur a sélectionné "Obligatoire"
                // On garde le contact dans la table et on le marque comme obligatoire
                presentRadio.checked = false;
            }
        });
        
        // Remplacer la méthode bindObligatoryEvents par cette logique
        const row = presentRadio.closest('tr');
        row.addEventListener('dblclick', () => {
            // Double-clic sur la ligne supprime le contact de la table
            this.removeFromObligatoryTable();
            this.element.checked = false;
            this.uncheckRelatedGroups();
            GroupManager.updateGroupSelectionState();
        });
    }
    
    // Modifier la méthode removeFromObligatoryTable pour s'adapter au nouveau système
    removeFromObligatoryTable() {
        if (this.obligatoryElement) {
            const row = this.obligatoryElement.closest('tr');
            if (row) {
                row.remove();
            }
            this.obligatoryElement = null;
            console.log('Removed from table');
        }
        
        // Check if the table exists
        //let tableObligatoire = document.getElementById('tableObligatoire');
        if (tableObligatoire) {
            // Check if the table is empty
            if (tableObligatoire.querySelector('tbody').childElementCount === 0) {
                tableObligatoire.remove();
                console.log('Removed obligatory table');
            }
        }
    }

// Modification de la méthode bindObligatoryEvents pour utiliser aussi checkRelatedGroups
bindObligatoryEvents() {
    this.obligatoryElement.addEventListener('change', () => {
        if (!this.obligatoryElement.checked) {
            this.removeFromObligatoryTable();
            this.element.checked = false;
            this.uncheckRelatedGroups();
            // Mettre à jour l'état des groupes
            GroupManager.updateGroupSelectionState();
        } else {
            // Si on coche dans la table des obligatoires, vérifier les groupes associés
            this.element.checked = true;
            this.checkRelatedGroups();
        }
    });
}

    checkIfLastInGroup() {
        Object.keys(membres2).forEach(groupId => {
            const membersInGroup = membres2[groupId];
            if (membersInGroup.includes(this.value)) {
                let allOthersRemoved = true;
                membersInGroup.forEach(memberId => {
                    if (memberId !== this.value) {
                        const obligatoryCheck = document.querySelector(`#contactCheck${memberId}`);
                        if (obligatoryCheck) {
                            allOthersRemoved = false;
                        }
                    }
                });
                
                if (allOthersRemoved) {
                    const groupCheckbox = document.querySelector(`input[name="groupes[]"][value="${groupId}"]`);
                    if (groupCheckbox) {
                        groupCheckbox.checked = false;
                        GroupManager.checkedGroups.delete(groupId);
                    }
                }
            }
        });
    }
}

class GroupManager {
    static checkedGroups = new Set();
    static instance = null;

    constructor() {
        GroupManager.instance = this;
        this.groups = document.querySelectorAll('input[name="groupes[]"]');
        this.contacts = new Map();
        this.initializeContacts();
        this.bindGroupEvents();
    }

    initializeContacts() {
        const contactElements = document.querySelectorAll('input[name="contacts[]"]');
        contactElements.forEach(element => {
            this.contacts.set(element.value, new Contact(element));
        });
    }

    bindGroupEvents() {
        this.groups.forEach(group => {
            group.addEventListener('change', () => {
                const groupId = group.value;
                const isChecked = group.checked;

                if (isChecked) {
                    GroupManager.checkedGroups.add(groupId);
                } else {
                    GroupManager.checkedGroups.delete(groupId);
                }

                if (membres2[groupId]) {
                    this.updateGroupMembers(groupId, isChecked);
                }
            });
        });
    }

// Modification de la méthode updateGroupMembers dans GroupManager
updateGroupMembers(groupId, isChecked) {
    const users = membres2[groupId];
    users.forEach(userId => {
        const contact = this.contacts.get(userId.toString());
        if (contact) {
            contact.element.checked = isChecked;
            if (isChecked) {
                contact.addToObligatoryTable();
                // Ajouter ceci pour cocher les autres groupes contenant ces contacts
                contact.checkRelatedGroups();
            } else {
                let keepChecked = false;
                GroupManager.checkedGroups.forEach(checkedGroupId => {
                    if (membres2[checkedGroupId] && membres2[checkedGroupId].includes(userId)) {
                        keepChecked = true;
                    }
                });
                
                if (!keepChecked) {
                    contact.element.checked = false;
                    contact.removeFromObligatoryTable();
                }
            }
        }
    });
}

    // Nouvelle méthode pour vérifier si tous les contacts d'un groupe sont cochés
    static checkIfAllContactsSelected() {
        if (!GroupManager.instance) return;
        
        Object.keys(membres2).forEach(groupId => {
            const groupCheckbox = document.querySelector(`input[name="groupes[]"][value="${groupId}"]`);
            if (groupCheckbox && !groupCheckbox.checked) {
                const membersInGroup = membres2[groupId];
                let allMembersSelected = true;
                
                // Vérifier si tous les membres du groupe sont cochés
                for (const memberId of membersInGroup) {
                    const contactElement = document.querySelector(`input[name="contacts[]"][value="${memberId}"]`);
                    if (contactElement && !contactElement.checked) {
                        allMembersSelected = false;
                        break;
                    }
                }
                
                // Si tous les membres sont cochés, cocher le groupe
                if (allMembersSelected && membersInGroup.length > 0) {
                    groupCheckbox.checked = true;
                    GroupManager.checkedGroups.add(groupId);
                }
            }
        });
    }

    // Méthode pour mettre à jour l'état de sélection des groupes
    static updateGroupSelectionState() {
        if (!GroupManager.instance) return;
        
        Object.keys(membres2).forEach(groupId => {
            const groupCheckbox = document.querySelector(`input[name="groupes[]"][value="${groupId}"]`);
            if (groupCheckbox) {
                const membersInGroup = membres2[groupId];
                let allMembersSelected = true;
                
                // Vérifier si tous les membres du groupe sont cochés
                for (const memberId of membersInGroup) {
                    const contactElement = document.querySelector(`input[name="contacts[]"][value="${memberId}"]`);
                    if (contactElement && !contactElement.checked) {
                        allMembersSelected = false;
                        break;
                    }
                }
                
                // Mettre à jour l'état du groupe
                groupCheckbox.checked = allMembersSelected && membersInGroup.length > 0;
                if (allMembersSelected && membersInGroup.length > 0) {
                    GroupManager.checkedGroups.add(groupId);
                } else {
                    GroupManager.checkedGroups.delete(groupId);
                }
            }
        });
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    const manager = new GroupManager();
    // Effectuer une vérification initiale
    GroupManager.checkIfAllContactsSelected();
});