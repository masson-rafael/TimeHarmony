document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    // Récupérer les données JSON depuis les attributs data
    var evenements = JSON.parse(calendarEl.dataset.evenements);
    var creneaux = JSON.parse(calendarEl.dataset.creneaux);
    var tabIdsNoms = JSON.parse(calendarEl.dataset.tabIdsNoms);

    // Fonction pour formater la date au format "dd-mm-yyyy"
    function formatDateDMY(date) {
        var day = ('0' + date.getDate()).slice(-2);
        var month = ('0' + (date.getMonth() + 1)).slice(-2);
        var year = date.getFullYear();
        return day + '-' + month + '-' + year;
    }

    // Création de l'instance FullCalendar
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next',
            right: ''
        },
        locale: 'fr',
        events: evenements,
        allDaySlot: false,
        eventClick: function (info) {
            // Récupération des dates de l'événement cliqué
            var debut = info.event.start;
            var fin = info.event.end;
            
            // Formater la date de l'événement pour correspondre aux clés de creneaux
            var eventDateKey = formatDateDMY(debut);
            
            // Construire la chaîne du créneau (heure de début et de fin)
            var creneauText = debut.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) + ' - ' +
                               fin.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            
            // Initialiser la variable html
            var html = "";
            
            // Vérifier si des créneaux existent pour cette date
            if (creneaux.hasOwnProperty(eventDateKey)) {
                var disponibilitesParPlage = creneaux[eventDateKey];
                // Parcourir chaque plage horaire de cette date
                for (var plage in disponibilitesParPlage) {
                    if (disponibilitesParPlage.hasOwnProperty(plage)) {
                        var disponibilites = disponibilitesParPlage[plage];
                        var personnes = [];
                        // Parcourir les disponibilités pour cette plage
                        for (var id in disponibilites) {
                            if (disponibilites.hasOwnProperty(id) && parseInt(disponibilites[id], 10) === 1) {
                                // Récupérer le nom complet depuis tabIdsNoms
                                var nomComplet = tabIdsNoms[id];
                                if(nomComplet) {
                                    // Mettre la premiere de nom et prenom en majuscule
                                    let nom = nomComplet.split(' ')[0];
                                    let prenom = nomComplet.split(' ')[1];
                                    nom = nom.charAt(0).toUpperCase() + nom.slice(1);
                                    prenom = prenom.charAt(0).toUpperCase() + prenom.slice(1);
                                    personnes.push(prenom + ' ' + nom);
                                }
                            }
                        }
                        html += `<div class="creneau-item border rounded p-3 mb-1">
                                    <strong>Plage horaire :</strong> ${plage}<br>
                                    <strong>Personnes disponibles :</strong> ${personnes.join(', ')}
                                 </div>
                                 <div class="modal-footer"></div>`;
                    }
                }
            }
            
            if (!html) {
                html = "<p>Aucun créneau disponible dans cet intervalle.</p>";
            }
            
            // Mettre à jour le contenu de la modale
            var modalTitle = "Créneau du " + eventDateKey + " de " +
                debut.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) +
                " à " +
                fin.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            
            document.getElementById('modalTitle').textContent = modalTitle;
            document.getElementById('modalBody').innerHTML = html;
            
            // Afficher la modale Bootstrap
            var myModal = new bootstrap.Modal(document.getElementById('modalResult'));
            myModal.show();
        }
    });

    calendar.render();
});
