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
        const tableObligatoire = document.getElementById('tableObligatoire');
        const tbody = tableObligatoire.querySelector('tbody');

        const row = document.createElement('tr');
        
        // Cellule checkbox
        const checkboxCell = document.createElement('td');
        const checkbox = document.createElement('input');
        checkbox.className = 'form-check-input';
        checkbox.type = 'checkbox';
        checkbox.name = 'contactsObligatoires[]';
        checkbox.value = this.value;
        checkbox.id = `contactCheck${this.value}`;
        checkbox.checked = false;
        checkboxCell.appendChild(checkbox);

        // Cellule nom
        const nameCell = document.createElement('td');
        nameCell.textContent = this.name;

        row.appendChild(checkboxCell);
        row.appendChild(nameCell);
        tbody.appendChild(row);

        this.obligatoryElement = checkbox;
        this.bindObligatoryEvents();
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

    removeFromObligatoryTable() {
        if (this.obligatoryElement) {
            const row = this.obligatoryElement.closest('tr');
            if (row) {
                row.remove();
            }
            this.obligatoryElement = null;
        }
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