$(document).ready(function() {
    $('.table').each(function() {
        let $table = $(this);

        // Config par défaut
        let options = {
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
            },
            columnDefs: [],
            pageLength: 10,
            ordering: true,
            responsive: true,
            autoWidth: true,
            scrollX: true,
            order: [[0, 'asc']]
        };

        if ($table.hasClass('admin')) {
            options.columnDefs = [
                // Désactivation du tri sur les colonnes images de profil et actions
                { targets: [6, 7], orderable: false }]
        }

        if ($table.hasClass('contact')) {
            options.columnDefs = [
                // Désactivation du tri sur les colonnes images de profil et actions
                { targets: [0, 4], orderable: false }]
            options.order = [[1, 'asc']]
        }

        if ($table.hasClass('groupe')) {
            options.columnDefs = [
                // Désactivation du tri sur les colonnes actions
                { targets: [3], orderable: false }]
        }

        if ($table.hasClass('groupe2')) {
            options.columnDefs = [
                // Désactivation du tri sur les colonnes actions
                { targets: [0], orderable: false }]
            options.order = [[1, 'asc']]
        }

        if ($table.hasClass('agenda')) {
            options.columnDefs = [
                // Désactivation du tri sur les colonnes actions
                { targets: [3], orderable: false }]
        }

        $table.DataTable(options);
    });
});