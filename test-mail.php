<?php
/**
 * Script de prueba de correo para Alezux Members Beta
 * Úsalo para verificar si el servidor permite enviar correos.
 */

// Cargar WordPress
define( 'WP_USE_THEMES', false );
require_once( __DIR__ . '/../../../wp-load.php' ); // Ajusta la ruta si es necesario

if ( ! current_user_can( 'administrator' ) ) {
    die( 'Acceso denegado.' );
}

$to = get_option( 'admin_email' );
$subject = 'Prueba de Sistema - Alezux Members';
$message = 'Este es un correo de prueba para verificar la configuración del servidor.';

echo "<h1>Probando envío de correo a: $to</h1>";

$sent = wp_mail( $to, $subject, $message );

if ( $sent ) {
    echo "<p style='color: green;'>¡Éxito! El correo ha sido aceptado para su entrega.</p>";
} else {
    echo "<p style='color: red;'>Fallo: El correo no pudo ser enviado.</p>";
    echo "<p>Revisa el archivo <b>alezux-mail-errors.log</b> en la carpeta del plugin para ver el error detallado.</p>";
}

// Probar a través del Engine del Plugin
echo "<h2>Probando a través del Motor del Plugin</h2>";
if ( class_exists( '\Alezux_Members\Modules\Marketing\Marketing' ) ) {
    $engine = \Alezux_Members\Modules\Marketing\Marketing::get_instance()->get_engine();
    $sent_engine = $engine->send_email( 'student_welcome', $to, [
        'user' => wp_get_current_user(),
        'course_title' => 'Curso de Prueba'
    ], true );

    if ( $sent_engine ) {
        echo "<p style='color: green;'>¡Éxito! El motor del plugin envió el correo correctamente.</p>";
    } else {
        echo "<p style='color: red;'>Fallo: El motor del plugin devolvió error.</p>";
    }
} else {
    echo "<p>El plugin no parece estar activo o el namespace es incorrecto.</p>";
}
