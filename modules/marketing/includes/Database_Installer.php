<?php
namespace Alezux_Members\Modules\Marketing\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Database_Installer {

    public static function check_updates() {
        if ( \get_option( 'alezux_marketing_db_version' ) !== '1.1.0' ) {
            self::install();
        }
    }

    public static function install() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // 1. Tabla de Automatizaciones (Blueprints de Nodos)
        $table_automations = $wpdb->prefix . 'alezux_marketing_automations';
        $sql1 = "CREATE TABLE $table_automations (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            event_trigger varchar(100) NOT NULL,
            blueprint longtext NOT NULL,
            status varchar(20) DEFAULT 'active' NOT NULL,
            total_executions bigint(20) DEFAULT 0 NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id),
            KEY event_trigger (event_trigger)
        ) $charset_collate;";

        // 2. Tabla de Cola de Envío (Asíncrono)
        $table_queue = $wpdb->prefix . 'alezux_marketing_queue';
        $sql2 = "CREATE TABLE $table_queue (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            automation_id bigint(20),
            user_id bigint(20) NOT NULL,
            email_to varchar(255) NOT NULL,
            subject varchar(255) NOT NULL,
            message longtext NOT NULL,
            from_name varchar(100),
            from_email varchar(100),
            status varchar(20) DEFAULT 'pending' NOT NULL,
            scheduled_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            sent_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            KEY status (status),
            KEY scheduled_at (scheduled_at)
        ) $charset_collate;";

        // 3. Tabla de Estadísticas (Aperturas)
        $table_stats = $wpdb->prefix . 'alezux_marketing_stats';
        $sql3 = "CREATE TABLE $table_stats (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            queue_id bigint(20) NOT NULL,
            opened tinyint(1) DEFAULT 0 NOT NULL,
            opened_at datetime DEFAULT NULL,
            user_agent varchar(255),
            ip_address varchar(100),
            PRIMARY KEY (id),
            KEY queue_id (queue_id)
        ) $charset_collate;";

        require_once \ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql1 );
        dbDelta( $sql2 );
        dbDelta( $sql3 );

        \update_option( 'alezux_marketing_db_version', '1.1.0' );
    }
}
