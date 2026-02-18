<?php
namespace Alezux_Members\Modules\Proyectos_Agencia\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Project_Manager {

	private $table_projects;
	private $table_meta;
	private $table_messages;

	public function __construct() {
		global $wpdb;
		$this->table_projects = $wpdb->prefix . 'alezux_projects';
		$this->table_meta     = $wpdb->prefix . 'alezux_project_meta';
		$this->table_messages = $wpdb->prefix . 'alezux_project_messages';
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

		$sql_messages = "CREATE TABLE {$this->table_messages} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			project_id bigint(20) NOT NULL,
			sender_id bigint(20) NOT NULL,
			content longtext NOT NULL,
			type varchar(50) DEFAULT 'text' NOT NULL,
			is_read tinyint(1) DEFAULT 0 NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id),
			KEY project_id (project_id),
			KEY sender_id (sender_id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql_projects . $sql_meta . $sql_messages );
	}

	// ... (rest of the file until get_project_meta)

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

	/**
	 * Obtiene TODOS los metadatos de un proyecto.
	 */
	public function get_all_project_meta( $project_id ) {
		global $wpdb;
		$results = $wpdb->get_results( $wpdb->prepare(
			"SELECT meta_key, meta_value FROM {$this->table_meta} WHERE project_id = %d",
			$project_id
		));

		$meta = [];
		if ( $results ) {
			foreach ( $results as $row ) {
				$meta[ $row->meta_key ] = $row->meta_value;
			}
		}
		return $meta;
	}

	/**
	 * Obtiene los mensajes de un proyecto.
	 */
	public function get_project_messages( $project_id ) {
		global $wpdb;
		$project_id = absint( $project_id );
		
		$query = $wpdb->prepare( 
			"SELECT * FROM {$this->table_messages} WHERE project_id = %d ORDER BY created_at ASC", 
			$project_id 
		);
		
		return $wpdb->get_results( $query );
	}

	/**
	 * Añade un mensaje al proyecto.
	 */
	public function add_project_message( $project_id, $sender_id, $content, $type = 'text' ) {
		global $wpdb;

		$result = $wpdb->insert(
			$this->table_messages,
			[
				'project_id' => absint( $project_id ),
				'sender_id'  => absint( $sender_id ),
				'content'    => wp_kses_post( $content ), // Permitir HTML básico
				'type'       => sanitize_text_field( $type ) // 'text', 'file', 'system'
			],
			[ '%d', '%d', '%s', '%s' ]
		);

		return $result ? $wpdb->insert_id : false;
	}

	/**
	 * Marca los mensajes como leídos (de otros usuarios).
	 */
	public function mark_messages_read( $project_id, $exclude_sender_id ) {
		global $wpdb;
		$project_id = absint( $project_id );
		$exclude_sender_id = absint( $exclude_sender_id );

		return $wpdb->query( $wpdb->prepare(
			"UPDATE {$this->table_messages} SET is_read = 1 WHERE project_id = %d AND sender_id != %d AND is_read = 0",
			$project_id, $exclude_sender_id
		));
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
			$project_id = $wpdb->insert_id;
			do_action( 'alezux_project_created', $project_id );
			return $project_id;
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
		
		// Obtener estado anterior para hook
		$old_project = $this->get_project( $project_id );
		$old_status  = $old_project ? $old_project->status : '';
		$old_step    = $old_project ? $old_project->current_step : '';

		$data = [ 'status' => $status ];
		$format = [ '%s' ];

		if ( $step ) {
			$data['current_step'] = $step;
			$format[] = '%s';
		}

		$data['updated_at'] = current_time( 'mysql' );
		$format[] = '%s';

		$result = $wpdb->update(
			$this->table_projects,
			$data,
			[ 'id' => $project_id ],
			$format,
			[ '%d' ]
		);

		if ( $result !== false ) {
			do_action( 'alezux_project_status_updated', $project_id, $status, $old_status, $step, $old_step );
		}

		return $result;
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
	 * Obtiene la estructura de entregables (Carpetas/Archivos).
	 */
	public function get_project_deliverables( $project_id ) {
		$data = $this->get_project_meta( $project_id, 'deliverables_structure' );
		return $data ? json_decode( $data, true ) : [];
	}

	/**
	 * Actualiza la estructura de entregables.
	 */
	public function update_project_deliverables( $project_id, $structure ) {
		return $this->update_project_meta( $project_id, 'deliverables_structure', json_encode( $structure ) );
	}

	/**
	 * Obtiene la lista de tutoriales (Videos).
	 */
	public function get_project_tutorials( $project_id ) {
		$data = $this->get_project_meta( $project_id, 'tutorials_list' );
		return $data ? json_decode( $data, true ) : [];
	}

	/**
	 * Actualiza la lista de tutoriales.
	 */
	public function update_project_tutorials( $project_id, $tutorials ) {
		return $this->update_project_meta( $project_id, 'tutorials_list', json_encode( $tutorials ) );
	}

}
