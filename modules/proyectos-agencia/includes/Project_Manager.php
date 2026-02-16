<?php
namespace Alezux_Members\Modules\Proyectos_Agencia\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Project_Manager {

	private $table_projects;
	private $table_meta;

	public function __construct() {
		global $wpdb;
		$this->table_projects = $wpdb->prefix . 'alezux_projects';
		$this->table_meta     = $wpdb->prefix . 'alezux_project_meta';
	}

	/**
	 * Crea las tablas necesarias en la base de datos.
	 */
	public function create_tables() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$sql_projects = "CREATE TABLE {$this->table_projects} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			name varchar(255) NOT NULL,
			customer_id bigint(20) NOT NULL,
			status varchar(50) DEFAULT 'pending' NOT NULL,
			current_step varchar(50) DEFAULT 'briefing' NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id),
			KEY customer_id (customer_id),
			KEY status (status)
		) $charset_collate;";

		$sql_meta = "CREATE TABLE {$this->table_meta} (
			meta_id bigint(20) NOT NULL AUTO_INCREMENT,
			project_id bigint(20) NOT NULL,
			meta_key varchar(255) NOT NULL,
			meta_value longtext,
			PRIMARY KEY  (meta_id),
			KEY project_id (project_id),
			KEY meta_key (meta_key)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql_projects . $sql_meta );
	}

	/**
	 * Crea un nuevo proyecto.
	 */
	public function create_project( $name, $customer_id ) {
		global $wpdb;

		// Convertir a int el ID
		$customer_id = absint( $customer_id );
		
		$result = $wpdb->insert(
			$this->table_projects,
			[
				'name'        => sanitize_text_field( $name ),
				'customer_id' => $customer_id,
				'status'      => 'pending',
				'current_step'=> 'briefing'
			],
			[ '%s', '%d', '%s', '%s' ]
		);

		if ( $result ) {
			return $wpdb->insert_id;
		}

		return false;
	}

	/**
	 * Obtiene un proyecto por ID.
	 */
	public function get_project( $project_id ) {
		global $wpdb;
		// Prepare statement para evitar SQL Injection
		$query = $wpdb->prepare( "SELECT * FROM {$this->table_projects} WHERE id = %d", $project_id );
		return $wpdb->get_row( $query );
	}

	/**
	 * Obtiene proyectos de un usuario.
	 */
	public function get_projects_by_user( $user_id ) {
		global $wpdb;
		$user_id = absint( $user_id );
		$query = $wpdb->prepare( "SELECT * FROM {$this->table_projects} WHERE customer_id = %d ORDER BY created_at DESC", $user_id );
		return $wpdb->get_results( $query );
	}

	/**
	 * Obtiene TODOS los proyectos (Para Admin).
	 */
	public function get_all_projects() {
		global $wpdb;
		return $wpdb->get_results( "SELECT * FROM {$this->table_projects} ORDER BY created_at DESC" );
	}

	/**
	 * Actualiza el estado y paso de un proyecto.
	 */
	public function update_status( $project_id, $status, $step = null ) {
		global $wpdb;
		
		$data = [ 'status' => $status ];
		$format = [ '%s' ];

		if ( $step ) {
			$data['current_step'] = $step;
			$format[] = '%s';
		}

		$data['updated_at'] = current_time( 'mysql' );
		$format[] = '%s';

		return $wpdb->update(
			$this->table_projects,
			$data,
			[ 'id' => $project_id ],
			$format,
			[ '%d' ]
		);
	}

	/**
	 * Guarda metadatos del proyecto.
	 */
	public function update_project_meta( $project_id, $key, $value ) {
		global $wpdb;

		// Verificar si ya existe
		$existing = $wpdb->get_var( $wpdb->prepare(
			"SELECT meta_id FROM {$this->table_meta} WHERE project_id = %d AND meta_key = %s",
			$project_id, $key
		));

		if ( $existing ) {
			$wpdb->update(
				$this->table_meta,
				[ 'meta_value' => $value ],
				[ 'meta_id' => $existing ],
				[ '%s' ],
				[ '%d' ]
			);
			return $existing;
		} else {
			$wpdb->insert(
				$this->table_meta,
				[
					'project_id' => $project_id,
					'meta_key'   => $key,
					'meta_value' => $value
				],
				[ '%d', '%s', '%s' ]
			);
			return $wpdb->insert_id;
		}
	}

	/**
	 * Obtiene metadatos de un proyecto.
	 */
	public function get_project_meta( $project_id, $key ) {
		global $wpdb;
		$val = $wpdb->get_var( $wpdb->prepare(
			"SELECT meta_value FROM {$this->table_meta} WHERE project_id = %d AND meta_key = %s",
			$project_id, $key
		));
		return $val;
	}
}
