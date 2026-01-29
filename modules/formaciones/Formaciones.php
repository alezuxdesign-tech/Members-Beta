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


		// LOGGING FOR DEBUGGING
		error_log( "Alezux Toggle: Post ID: $post_id, User ID: $user_id" );

		// Check if already completed
		$is_completed = learndash_is_target_complete( $post_id, $user_id );
		error_log( "Alezux Toggle: Is Completed? " . ( $is_completed ? 'YES' : 'NO' ) );

		if ( $is_completed ) {
			// Unmark complete (Requires removing user activity)
            error_log( "Alezux Toggle: Attempting to UNMARK" );
            
			// LearnDash doesn't have a simple "unmark" function for topics generally accessible, 
			// we have to delete the activity record.
			
			// This is a bit of a workaround regarding LearnDash internal APIs.
			// We look for the activity record for this user and post.
			
			global $wpdb;
			// Assuming 'sfwd-topic' or 'sfwd-lessons'
			// However `learndash_is_target_complete` checks activity. 
			
			// Try to find the activity ID to delete it via LD API if possible, or raw DB if needed (careful).
			// Safe way: Update user meta course progress if needed, but activity table is key.
			
			// For simplicity and safety, we will attempt to use 'learndash_update_user_activity' with status 'not_completed' if it supports it, 
			// OR direct deletion if we are sure.
			
			// Actually, LearnDash stores completion in `wp_learndash_user_activity`.
			
			/*
			 * @todo Refine unmark logic if standard API is missing.
			 * For now, let's try to fetch activity and delete.
			 */
             
            // Try using the generic activity management. 
            // If we cannot "uncomplete" easily, that's a limitation we might face.
            // But let's look at `learndash_delete_user_activity`.
            
            // Using a lower level query to be sure we hit the right record.
            $activity_type = 'topic'; // Default guess
            $post_type = get_post_type($post_id);
            if('sfwd-lessons' === $post_type) $activity_type = 'lesson';
            if('sfwd-topic' === $post_type) $activity_type = 'topic';

            // Delete activity
            // Since there is no public API to "uncomplete", we might need to rely on clearing the completion timestamp in user meta 
            // OR deleting the row from wp_learndash_user_activity.
            
            // Let's use a widely accepted method for custom uncompletion:
            // 1. Get course ID
            $course_id = learndash_get_course_id( $post_id );
            
            // 2. Delete from Activity Table
            // Ensure we use the correct post type. If it's a topic, we must explicitly say 'topic'.
            $post_type_raw = get_post_type($post_id);
            if ( 'sfwd-topic' === $post_type_raw ) {
                $activity_type = 'topic';
            } elseif ( 'sfwd-lessons' === $post_type_raw ) {
                $activity_type = 'lesson';
            } else {
                 // Fallback or specific handling for other types if needed
                 $activity_type = 'topic'; 
            }

            $wpdb->delete(
                $wpdb->prefix . 'learndash_user_activity',
                [
                    'user_id' => $user_id,
                    'post_id' => $post_id,
                    'activity_type' => $activity_type
                ]
            );

            // 3. Update Course Progress Meta (Legacy & New)
            // LearnDash caches progress, so we might need to trigger a recalculation or manually update user meta.
            // 'course_completed_' meta
             $course_progress = get_user_meta( $user_id, '_sfwd_course_progress', true );
             if(isset($course_progress[$course_id]) && isset($course_progress[$course_id]['topics'][$post_id])) {
                 unset($course_progress[$course_id]['topics'][$post_id]);
                 // Also handling lessons if it was a lesson
                 if ( isset($course_progress[$course_id]['lessons'][$post_id]) ) {
                     unset($course_progress[$course_id]['lessons'][$post_id]); // Mark lesson incomplete
                 }
                 update_user_meta( $user_id, '_sfwd_course_progress', $course_progress );
             }

			wp_send_json_success( [ 'status' => 'incomplete' ] );

		} else {
			// Mark complete
			learndash_process_mark_complete( $user_id, $post_id );
			wp_send_json_success( [ 'status' => 'completed' ] );
		}
	}
}
