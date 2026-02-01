<?php
// Cargar WordPress
require_once('wp-load.php');

global $wpdb;
$table_name = $wpdb->prefix . 'alezux_finanzas_plans';
$plan = $wpdb->get_row("SELECT * FROM $table_name ORDER BY id DESC LIMIT 1");

if ($plan) {
    echo "Plan Encontrado en Base de Datos Local:\n";
    echo "ID: " . $plan->id . "\n";
    echo "Nombre: " . $plan->name . "\n";
    echo "Stripe Product ID: " . $plan->stripe_product_id . "\n";
    echo "Stripe Price ID: " . $plan->stripe_price_id . "\n";
    echo "Total Cuotas (Regla Local): " . $plan->total_quotas . "\n";
    echo "Monto por Cuota: " . $plan->quota_amount . "\n";
    echo "Reglas de Acceso: " . $plan->access_rules . "\n";
} else {
    echo "No se encontraron planes en la tabla $table_name.";
}
