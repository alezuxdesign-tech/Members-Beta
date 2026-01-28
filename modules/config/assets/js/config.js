jQuery(document).ready(function ($) {
    // Escuchar clic en el encabezado del widget de configuración
    $(document).on('click', '.alezux-config-header', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $wrapper = $(this).closest('.alezux-config-card');

        // Toggle state
        $wrapper.toggleClass('active');

        // Close other open widgets if any
        $('.alezux-config-card').not($wrapper).removeClass('active');
    });

    // Close on click outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.alezux-config-card').length) {
            $('.alezux-config-card').removeClass('active');
        }
    });
});
