document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    // Récupérer les données JSON depuis les attributs data
    var evenements = JSON.parse(calendarEl.dataset.evenements);
    var creneaux = JSON.parse(calendarEl.dataset.creneaux);
    var dateDebut = JSON.parse(calendarEl.dataset.dateDebut);
    var heureDebut = JSON.parse(calendarEl.dataset.heureDebut);
    var heureFin = JSON.parse(calendarEl.dataset.heureFin);

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
        initialDate: dateDebut,
        slotMinTime: heureDebut,
        slotMaxTime: heureFin,
        contentHeight: 'auto',
        headerToolbar: {
            left: 'prev,next',
            right: ''
        },
        locale: 'fr',
        events: evenements,
        displayEventTime: false,
        allDaySlot: false,           
        eventClick: function (info) {
        // Récupération des dates de l'événement cliqué
            var debut = info.event.start;
            var fin = info.event.end;

            // Formater la date pour obtenir la clé dans 'creneaux'
            var eventDateKey = formatDateDMY(debut);

            // Construire la chaîne du créneau (pour information)
            var creneauText = debut.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) + ' - ' +
                fin.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            var html = "";

            // Vérifier si des créneaux existent pour cette date
            if (creneaux.hasOwnProperty(eventDateKey)) {
                var disponibilitesParPlage = creneaux[eventDateKey];
                // Parcourir chaque plage horaire de cette date
                for (var plage in disponibilitesParPlage) {
                    if (disponibilitesParPlage.hasOwnProperty(plage)) {
                        // Extraction de l'heure de début et de fin de la plage (ex: "10:00 - 11:00")
                        var parts = plage.split(' - ');
                        if (parts.length < 2) continue; // sécurité

                        var slotStartStr = parts[0].trim();
                        var slotEndStr = parts[1].trim();

                        // Créer des objets Date pour le slot (en utilisant la date de l'événement)
                        var slotStartParts = slotStartStr.split(':');
                        var slotEndParts = slotEndStr.split(':');
                        var slotStart = new Date(debut.getFullYear(), debut.getMonth(), debut.getDate(), parseInt(slotStartParts[0], 10), parseInt(slotStartParts[1], 10));
                        var slotEnd = new Date(debut.getFullYear(), debut.getMonth(), debut.getDate(), parseInt(slotEndParts[0], 10), parseInt(slotEndParts[1], 10));

                        // Vérifier que le créneau est dans l'intervalle de l'événement
                        if (slotStart >= debut && slotEnd <= fin) {
                            var disponibilites = disponibilitesParPlage[plage];
                            var personnes = [];
                            var idPersonnes = [];
                            // Parcourir les disponibilités pour cette plage
                            for (var id in disponibilites) {
                                idPersonnes.push(id);
                                if (disponibilites.hasOwnProperty(id) && parseInt(disponibilites[id], 10) === 1) {
                                    var nomComplet = info.event.extendedProps.participants[id];
                                    if (nomComplet) {
                                        personnes.push(nomComplet);
                                    }
                                }
                            }
                            html += `<div class="creneau-item p-3 mb-1">
                                        <strong>Plage horaire :</strong> ${plage}<br>
                                        <strong>Personnes disponibles :</strong> ${personnes.join(', ')} <br>
                                        <a class="px-5 btn btn-primary" href="index.php?controleur=assistant&methode=envoyerMailInvitationCreneau&userIds=${idPersonnes}&startDate=${new Date(debut.getFullYear(), debut.getMonth(), debut.getDate(), parseInt(slotStartParts[0], 10), parseInt(slotStartParts[1], 10))}&endDate=${new Date(debut.getFullYear(), debut.getMonth(), debut.getDate(), parseInt(slotEndParts[0], 10), parseInt(slotEndParts[1], 10))}" role="button">Envoyer un mail d'ajout du créneau</a>
                                    </div>`;
                        }
                    }
                }
            }

            if (!html) {
                html = "<p>Aucun créneau disponible dans cet intervalle.</p>";
            }

            var modalTitle = "Créneau du " + eventDateKey + " de " +
                debut.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) +
                " à " +
                fin.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            document.getElementById('modalTitle').textContent = modalTitle;
            document.getElementById('modalBody').innerHTML = html;

            var myModal = new bootstrap.Modal(document.getElementById('modalResult'));
            myModal.show();
        }
    });

    calendar.render();
});
