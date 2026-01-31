<?php
namespace Alezux_Members\Modules\Finanzas\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Database_Installer {

	const DB_VERSION = '1.0';
	const OPTION_NAME = 'alezux_finanzas_db_version';

	public static function install() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table_plans = $wpdb->prefix . 'alezux_finanzas_plans';
		$table_subs = $wpdb->prefix . 'alezux_finanzas_subscriptions';
		$table_trans = $wpdb->prefix . 'alezux_finanzas_transactions';

		$sql_plans = "CREATE TABLE $table_plans (
			id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			name VARCHAR(255) NOT NULL,
			course_id BIGINT(20) NOT NULL,
			stripe_product_id VARCHAR(255),
			stripe_price_id VARCHAR(255),
			total_quotas INT(5) DEFAULT 1,
			quota_amount DECIMAL(10,2),
			frequency VARCHAR(50) DEFAULT 'month',
			access_rules LONGTEXT,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP
		) $charset_collate;";

		$sql_subs = "CREATE TABLE $table_subs (
			id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			user_id BIGINT(20) NOT NULL,
			plan_id BIGINT(20) NOT NULL,
			stripe_subscription_id VARCHAR(255),
			stripe_customer_id VARCHAR(255),
			status VARCHAR(50) DEFAULT 'pending',
			quotas_paid INT(5) DEFAULT 0,
			last_payment_date DATETIME,
			next_payment_date DATETIME,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP
		) $charset_collate;";

		$sql_trans = "CREATE TABLE $table_trans (
			id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			user_id BIGINT(20) NOT NULL,
			subscription_id BIGINT(20),
			plan_id BIGINT(20),
			amount DECIMAL(10,2),
			currency VARCHAR(10) DEFAULT 'USD',
			method VARCHAR(50),
			transaction_ref VARCHAR(255),
			status VARCHAR(50),
			data LONGTEXT,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql_plans );
		dbDelta( $sql_subs );
		dbDelta( $sql_trans );

		update_option( self::OPTION_NAME, self::DB_VERSION );
	}

	public static function check_updates() {
		if ( get_option( self::OPTION_NAME ) !== self::DB_VERSION ) {
			self::install();
		}
	}
}
