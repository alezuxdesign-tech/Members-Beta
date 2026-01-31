jQuery(document).ready(function ($) {
    var timer;

    $('.alezux-estudiantes-search input').on('keyup', function () {
        var $input = $(this);
        var $wrapper = $input.closest('.alezux-estudiantes-wrapper');
        var $tbody = $wrapper.find('.alezux-estudiantes-table tbody');
        var query = $input.val();

        clearTimeout(timer);

        timer = setTimeout(function () {
            // Lógica de búsqueda original si la hubiera
        }, 500);
    });
});
