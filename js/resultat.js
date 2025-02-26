document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    // Récupérer les données JSON depuis les attributs data
    var evenements = JSON.parse(calendarEl.dataset.evenements);
    var creneaux = JSON.parse(calendarEl.dataset.creneaux); // creneauxCommuns stocké dans data-creneaux
    var dateDebut = calendarEl.dataset.dateDebut;


    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        initialDate: dateDebut,
        headerToolbar: {
            left: 'prev,next',
            // center: 'title',
            right: ''
        },
        locale: 'fr',
        events: evenements,
        allDaySlot: false,
        
        eventContent: function() {
            return { html: '' }; // Enlever le texte
        },
        eventClick: function(info) {
            // Récupération des dates de l'événement cliqué
            var eventStart = info.event.start;
            var eventEnd = info.event.end;

            // Formater la date de l'événement au format dd-mm-yyyy pour correspondre aux clés de creneaux
            function formatDate(d) {
                var day = ('0' + d.getDate()).slice(-2);
                var month = ('0' + (d.getMonth() + 1)).slice(-2);
                var year = d.getFullYear();
                return day + '-' + month + '-' + year;
            }
            var eventDateKey = formatDate(eventStart);

            // Récupérer les créneaux pour le jour de l'événement
            var dayCreneaux = creneaux[eventDateKey] || {};
            var html = "";

            // Parcourir chaque créneau de la journée
            for (var slot in dayCreneaux) {
                if (dayCreneaux.hasOwnProperty(slot)) {
                    // Le slot est au format "HH:MM - HH:MM"
                    var times = slot.split(' - ');
                    var slotStartStr = times[0];
                    var slotEndStr = times[1];

                    // Création d'objets Date pour le début et la fin du créneau sur la même date que l'événement
                    var slotStartParts = slotStartStr.split(':');
                    var slotEndParts = slotEndStr.split(':');
                    var slotStart = new Date(eventStart.getFullYear(), eventStart.getMonth(), eventStart.getDate(), parseInt(slotStartParts[0], 10), parseInt(slotStartParts[1], 10));
                    var slotEnd = new Date(eventStart.getFullYear(), eventStart.getMonth(), eventStart.getDate(), parseInt(slotEndParts[0], 10), parseInt(slotEndParts[1], 10));

                    // Vérifier que le créneau est dans l'intervalle de l'événement
                    if (slotStart >= eventStart && slotEnd <= eventEnd) {
                        // Récupérer les disponibilités pour ce créneau
                        var disponibilites = dayCreneaux[slot];
                        var personnes = [];
                        for (var person in disponibilites) {
                            if (disponibilites.hasOwnProperty(person) && parseInt(disponibilites[person], 10) === 1) {
                                // Mettre la première lettre en majuscule
                                personnes.push(person.charAt(0).toUpperCase() + person.slice(1));
                            }
                        }
                        html += `<div class="creneau-item border rounded p-3 mb-1">
                                    <strong>Plage horaire :</strong> ${slot}<br>
                                    <strong>Personnes disponibles :</strong> ${personnes.join(', ')}
                                 </div>`;
                            }
                }
            }

            if (!html) {
                html = "<p>Aucun créneau disponible dans cet intervalle.</p>";
            }

            // Mettre à jour le contenu de la modale
            var modalTitle = "Créneau du " + eventDateKey + " de " +
                eventStart.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) +
                " à " +
                eventEnd.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            document.getElementById('modalTitle').textContent = modalTitle;
            document.getElementById('modalBody').innerHTML = html;

            // Afficher la modale Bootstrap
            var myModal = new bootstrap.Modal(document.getElementById('eventModal'));
            myModal.show();
        }
    });

    calendar.render();
});
