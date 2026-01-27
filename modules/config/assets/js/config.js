jQuery(document).ready(function ($) {
    // Escuchar clic en el encabezado del widget de configuración
    $(document).on('click', '.alezux-config-header', function () {
        var $wrapper = $(this).closest('.alezux-config-card');
        var $menu = $wrapper.find('.alezux-config-menu');
        var $toggleIcon = $(this).find('.alezux-config-toggle-icon');

        // Toggle de la clase para estilos (rotación de icono, etc)
        $wrapper.toggleClass('is-open');

        // Animación slideToggle para el menú
        $menu.slideToggle(300);
    });
});
