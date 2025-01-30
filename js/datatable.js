$(document).ready(function() {
    $('.table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json'
        },
        "pageLength": 10,
        "ordering": true,
        "responsive": true,
        "autoWidth": true,
        "scrollX": true, // Permet d'activer le scroll horizontal
    });
});