<?php
// Cargar WordPress
require_once dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/wp-load.php';

// Validar seguridad básica (solo admins)
if ( ! current_user_can( 'manage_options' ) ) {
    die('Acceso denegado');
}

echo "<h2>DEBUG: Probando Enrollment Manager</h2>";

$user_id = 13; // El ID del usuario que tiene problemas
$email = 'usuario_test_13@example.com'; // Email dummy
$plan_id = 1; // El Plan ID que estamos probando

// Verificar si existe usuario
$user = get_user_by('id', $user_id);
if (!$user) {
    die("Error: Usuario ID $user_id no existe.");
}
echo "Usuario encontrado: " . $user->user_email . "<br>";

// Invocar manualmente
if ( ! class_exists( 'Alezux_Members\Modules\Finanzas\Includes\Enrollment_Manager' ) ) {
    die("Error: Clase Enrollment_Manager no cargada.");
}

echo "Intentando matricular User $user_id en Plan $plan_id...<br>";

// Simulamos los datos de Stripe
$result = \Alezux_Members\Modules\Finanzas\Includes\Enrollment_Manager::enroll_user(
    $user->user_email,
    $plan_id,
    'sub_test_debug_' . rand(1000,9999), // Fake Stripe Sub ID
    10.00,
    'ref_test_debug_' . rand(1000,9999)
);

if ( $result ) {
    echo "<h3>¡ÉXITO! Resultado: $result</h3>";
    echo "Revisa la tabla wp_alezux_finanzas_subscriptions ahora.";
} else {
    echo "<h3>FALLO: La función devolvió false.</h3>";
    global $wpdb;
    echo "Last DB Error: " . $wpdb->last_error;
}
