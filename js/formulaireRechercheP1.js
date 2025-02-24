class Contact {
    constructor(element) {
        this.element = element;
        this.value = element.value;
        this.obligatoryElement = null;
        this.name = element.parentNode.parentNode.childNodes[3].textContent;
        this.bindEvents();
    }

    bindEvents() {
        this.element.addEventListener('change', () => {
            if (this.element.checked) {
                this.addToObligatoryTable();
            } else {
                this.removeFromObligatoryTable();
                this.checkIfLastInGroup();
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

    bindObligatoryEvents() {
        this.obligatoryElement.addEventListener('change', () => {
            if (!this.obligatoryElement.checked) {
                this.removeFromObligatoryTable();
                this.element.checked = false;
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

    constructor() {
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

                console.log(membres2);
                console.log(membres2[groupId]);
                if (membres2[groupId]) {
                    this.updateGroupMembers(groupId, isChecked);
                }
            });
        });
    }

    updateGroupMembers(groupId, isChecked) {
        const users = membres2[groupId];
        users.forEach(userId => {
            const contact = this.contacts.get(userId.toString());
            if (contact) {
                contact.element.checked = isChecked;
                if (isChecked) {
                    contact.addToObligatoryTable();
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
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    const manager = new GroupManager();
});