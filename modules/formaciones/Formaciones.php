<?php
namespace Alezux_Members\Modules\Formaciones;

use Alezux_Members\Core\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Formaciones
 * Módulo para gestionar formaciones.
 */
class Formaciones extends Module_Base {

	public function init() {
		// Cargar clases internas
		require_once __DIR__ . '/includes/Course_Meta_Fields.php';
		// Instanciar Meta Fields para que corran los hooks del backend
		new \Alezux_Members\Modules\Formaciones\Includes\Course_Meta_Fields();

		// Encolar assets de administración
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );

		// Encolar assets del frontend
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_frontend_assets' ] );

		// Registrar Widget de Elementor
		add_action( 'elementor/widgets/register', [ $this, 'register_elementor_widgets' ] );

		// AJAX para completar/descompletar topic
		add_action( 'wp_ajax_alezux_toggle_topic_complete', [ $this, 'handle_topic_completion' ] );
	}

	public function enqueue_frontend_assets() {
		wp_enqueue_style(
			'alezux-formaciones-front',
			$this->get_asset_url( 'assets/css/formaciones.css' ),
			[],
			'1.0.5' . time() // Version bumped for full customization controls
		);

		wp_enqueue_script(
			'alezux-formaciones-js',
			$this->get_asset_url( 'assets/js/formaciones.js' ),
			[ 'jquery' ],
			'1.0.0',
			true
		);

		wp_localize_script( 'alezux-formaciones-js', 'alezux_vars', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		] );
	}

	public function register_elementor_widgets( $widgets_manager ) {
		require_once __DIR__ . '/includes/Elementor_Widget_Formaciones_Grid.php';
		require_once __DIR__ . '/includes/Elementor_Widget_Topics.php';
		require_once __DIR__ . '/includes/Elementor_Widget_Btn_Complete_Topic.php';
		
		$widgets_manager->register( new \Alezux_Members\Modules\Formaciones\Includes\Elementor_Widget_Formaciones_Grid() );
		$widgets_manager->register( new \Alezux_Members\Modules\Formaciones\Includes\Elementor_Widget_Topics() );
		$widgets_manager->register( new \Alezux_Members\Modules\Formaciones\Includes\Elementor_Widget_Btn_Complete_Topic() );
	}

	public function enqueue_admin_assets( $hook ) {
		global $post_type;
		
		// Solo cargar en la edición de cursos de LearnDash
		if ( 'sfwd-courses' !== $post_type ) {
			return;
		}

		wp_enqueue_media(); // Necesario para el uploader de imágenes

		wp_enqueue_style(
			'alezux-formaciones-admin',
			$this->get_asset_url( 'assets/css/admin-formaciones.css' ),
			[],
			'1.0.0'
		);

		wp_enqueue_script(
			'alezux-formaciones-admin',
			$this->get_asset_url( 'assets/js/admin-formaciones.js' ),
			[ 'jquery' ],
			'1.0.0',
			true
		);
	}
	public function handle_topic_completion() {
		// Validar nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'alezux_toggle_complete_' . $_POST['post_id'] ) ) {
			wp_send_json_error( [ 'message' => 'Invalid nonce' ] );
		}

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( [ 'message' => 'User not logged in' ] );
		}

		$post_id = intval( $_POST['post_id'] );
		$user_id = get_current_user_id();

		if ( ! $post_id ) {
			wp_send_json_error( [ 'message' => 'Invalid Post ID' ] );
		}

		// Check if already completed
		$is_completed = learndash_is_target_complete( $post_id, $user_id );

		if ( $is_completed ) {
			// Unmark complete logic
            
			// LearnDash does not have a public API to "unmark" a topic/lesson simply.
			// We must remove the record from wp_learndash_user_activity table.
			
			global $wpdb;
            
            // Determine activity type
            $activity_type = 'topic'; // Default
            $post_type = get_post_type($post_id);
            if('sfwd-lessons' === $post_type) $activity_type = 'lesson';
            if('sfwd-topic' === $post_type) $activity_type = 'topic';

            // 1. Delete from Activity Table
            $wpdb->delete(
                $wpdb->prefix . 'learndash_user_activity',
                [
                    'user_id' => $user_id,
                    'post_id' => $post_id,
                    'activity_type' => $activity_type
                ]
            );

            // 2. Clear User Meta Cache for Course Progress
            // This forces LearnDash to rebuild the progress user meta on next load
            $course_id = learndash_get_course_id( $post_id );
            if ( $course_id ) {
                 $course_progress = get_user_meta( $user_id, '_sfwd_course_progress', true );
                 
                 // Remove from progress array if exists
                 if ( isset( $course_progress[$course_id] ) ) {
                      if ( 'topic' === $activity_type && isset( $course_progress[$course_id]['topics'][$post_id] ) ) {
                          unset( $course_progress[$course_id]['topics'][$post_id] );
                      } elseif ( 'lesson' === $activity_type && isset( $course_progress[$course_id]['lessons'][$post_id] ) ) {
                          unset( $course_progress[$course_id]['lessons'][$post_id] );
                      }
                      
                      // Update the meta
                      update_user_meta( $user_id, '_sfwd_course_progress', $course_progress );
                 }
            }

			wp_send_json_success( [ 'status' => 'incomplete' ] );

		} else {
			// Mark complete
            // We pass false for 'unmark' (default)
			learndash_process_mark_complete( $user_id, $post_id );
			wp_send_json_success( [ 'status' => 'completed' ] );
		}
	}
}
