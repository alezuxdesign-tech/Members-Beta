<?php
namespace Alezux_Members\Modules\Learning_Modules\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Post_Type_Module {

	public function register() {
		add_action( 'init', [ $this, 'register_post_type' ] );
	}

	public function register_post_type() {
		$labels = [
			'name'               => _x( 'Módulos', 'post type general name', 'alezux-members' ),
			'singular_name'      => _x( 'Módulo', 'post type singular name', 'alezux-members' ),
			'menu_name'          => _x( 'Módulos LMS', 'admin menu', 'alezux-members' ),
			'name_admin_bar'     => _x( 'Módulo', 'add new on admin bar', 'alezux-members' ),
			'add_new'            => _x( 'Añadir Nuevo', 'module', 'alezux-members' ),
			'add_new_item'       => __( 'Añadir Nuevo Módulo', 'alezux-members' ),
			'new_item'           => __( 'Nuevo Módulo', 'alezux-members' ),
			'edit_item'          => __( 'Editar Módulo', 'alezux-members' ),
			'view_item'          => __( 'Ver Módulo', 'alezux-members' ),
			'all_items'          => __( 'Todos los Módulos', 'alezux-members' ),
			'search_items'       => __( 'Buscar Módulos', 'alezux-members' ),
			'not_found'          => __( 'No se encontraron módulos.', 'alezux-members' ),
			'not_found_in_trash' => __( 'No se encontraron módulos en la papelera.', 'alezux-members' ),
		];

		$args = [
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => [ 'slug' => 'modulo' ],
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 5,
			'menu_icon'          => 'dashicons-welcome-learn-more', // Icono de educación
			'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
			'show_in_rest'       => true, // Habilitar editor de bloques (Gutenberg) si se desea
		];

		register_post_type( 'alz_module', $args );
	}
}
