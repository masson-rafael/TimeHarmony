$(document).ready(function() {
    $('.table').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.2.1/i18n/fr-FR.json'
        },
        "pageLength": 10,
        "ordering": true,
        "responsive": true,
        "autoWidth": true,
        "scrollX": true, // Permet d'activer le scroll horizontal
    });
});