<?php
namespace Alezux_Members\Modules\Proyectos_Agencia\Includes;

use Alezux_Members\Modules\Marketing\Marketing;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Proyectos_Agencia_Emails {

	public function init() {
		// 1. Registrar tipos de correo en el sistema de Marketing
		add_filter( 'alezux_marketing_email_types', [ $this, 'register_email_types' ] );

		// 2. Proveer plantillas por defecto
		add_filter( 'alezux_marketing_default_template', [ $this, 'provide_default_templates' ], 10, 2 );

		// 3. Escuchar eventos del proyecto
		add_action( 'alezux_project_created', [ $this, 'send_project_created_email' ] );
		add_action( 'alezux_project_status_updated', [ $this, 'handle_status_update' ], 10, 3 );
	}

	public function provide_default_templates( $defaults, $type ) {
		// Estilos básicos (CSS inline simplificado para email)
		$common_css = 'font-family: sans-serif; background: #f4f4f4; padding: 20px;';
		$container_css = 'max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px;';
		$btn_css = 'display: inline-block; padding: 12px 24px; background: #6c5ce7; color: #fff; text-decoration: none; border-radius: 4px; font-weight: bold;';

		$header = '<div style="' . $common_css . '"><div style="' . $container_css . '"><div style="text-align:center; margin-bottom:30px;"><img src="{{logo_url}}" style="max-width:150px;"></div>';
		$footer = '<div style="margin-top:30px; font-size:12px; color:#888; text-align:center;"><p>&copy; ' . date('Y') . ' {{site_name}}</p></div></div></div>';

		switch ( $type ) {
			case 'project_created':
				return [
					'subject' => '¡Bienvenido a tu Nuevo Proyecto: {{project_name}}!',
					'content' => $header . '
						<h2>Hola {{user.name}},</h2>
						<p>Estamos emocionados de comenzar a trabajar en tu proyecto <strong>{{project_name}}</strong>.</p>
						<p>Para comenzar, necesitamos que completes un breve formulario de Briefing para entender mejor tus necesidades.</p>
						<p style="text-align: center; margin: 30px 0;">
							<a href="{{login_url}}" style="' . $btn_css . '">Ir a mi Panel de Cliente</a>
						</p>
					' . $footer
				];

			case 'briefing_received':
				return [
					'subject' => '[Admin] Nuevo Briefing: {{project_name}}',
					'content' => $header . '
						<h2>Briefing Recibido</h2>
						<p>El cliente <strong>{{customer_name}}</strong> ha completado el briefing para el proyecto <strong>{{project_name}}</strong>.</p>
						<p>Revisa los detalles en el panel de administración.</p>
						<p style="text-align: center; margin: 30px 0;">
							<a href="{{admin_url}}" style="' . $btn_css . '">Ver Proyecto</a>
						</p>
					' . $footer
				];

			case 'design_ready':
				return [
					'subject' => '🎨 Tu Diseño está listo para revisión: {{project_name}}',
					'content' => $header . '
						<h2>¡Tenemos una propuesta para ti!</h2>
						<p>Hola {{user.name}}, el diseño para tu proyecto <strong>{{project_name}}</strong> está listo.</p>
						<p>Por favor, ingresa a la plataforma para revisar la propuesta y darnos tu feedback o aprobación.</p>
						<p style="text-align: center; margin: 30px 0;">
							<a href="{{login_url}}" style="' . $btn_css . '">Revisar Diseño</a>
						</p>
					' . $footer
				];

			case 'project_completed':
				return [
					'subject' => '🚀 ¡Proyecto Completado!: {{project_name}}',
					'content' => $header . '
						<h2>¡Felicidades, {{user.name}}!</h2>
						<p>Nos complace informarte que tu proyecto <strong>{{project_name}}</strong> ha sido finalizado exitosamente.</p>
						<p>Gracias por confiar en nosotros.</p>
						<p style="text-align: center; margin: 30px 0;">
							<a href="{{login_url}}" style="' . $btn_css . '">Ver Proyecto Finalizado</a>
						</p>
					' . $footer
				];
		}

		return $defaults;
	}

	public function register_email_types( $types ) {
		$new_types = [
			'project_created' => [
				'title'       => 'Proyectos - Nuevo Proyecto',
				'description' => 'Se envía al cliente cuando se crea un nuevo proyecto para él.',
				'variables'   => [ '{{user.name}}', '{{project_name}}', '{{login_url}}', '{{site_name}}', '{{logo_url}}' ]
			],
			'briefing_received' => [
				'title'       => 'Proyectos - Briefing Recibido',
				'description' => 'Notificación al administrador (o equipo) cuando el cliente envía el briefing.',
				'variables'   => [ '{{project_name}}', '{{customer_name}}', '{{admin_url}}', '{{site_name}}', '{{logo_url}}' ]
			],
			'design_ready' => [
				'title'       => 'Proyectos - Diseño Listo',
				'description' => 'Invita al cliente a revisar la propuesta de diseño.',
				'variables'   => [ '{{user.name}}', '{{project_name}}', '{{design_url}}', '{{login_url}}', '{{site_name}}', '{{logo_url}}' ]
			],
			'design_approved' => [
				'title'       => 'Proyectos - Diseño Aprobado',
				'description' => 'Notificación al administrador cuando el cliente aprueba el diseño.',
				'variables'   => [ '{{project_name}}', '{{customer_name}}', '{{admin_url}}', '{{site_name}}', '{{logo_url}}' ]
			],
			'project_completed' => [
				'title'       => 'Proyectos - Proyecto Finalizado',
				'description' => 'Informar al cliente que su proyecto ha sido completado y entregado.',
				'variables'   => [ '{{user.name}}', '{{project_name}}', '{{login_url}}', '{{site_name}}', '{{logo_url}}' ]
			],
		];

		return array_merge( $types, $new_types );
	}

	public function send_project_created_email( $project_id ) {
		error_log( "Proyectos Email Debug: Project Created ID: $project_id" );
		$this->send_email_to_customer( $project_id, 'project_created' );
	}

	public function handle_status_update( $project_id, $new_status, $old_status, $new_step = null, $old_step = null ) {
		error_log( "Proyectos Email Debug: Status Update ID: $project_id | Status: $old_status -> $new_status | Step: $old_step -> $new_step" );
		
		if ( $new_status === $old_status && $new_step === $old_step ) return;

		// Detectar cambios de estado lógicos (ya sea por status o por step)
		
		// 1. Briefing Completado
		if ( $new_status === 'briefing_completed' && $old_status !== 'briefing_completed' ) {
			$this->send_email_to_admin( $project_id, 'briefing_received' );
		}

		// 2. Diseño Listo para Revisión
		// Si entra en design_review por status O por step
		$entered_design_review = ( $new_status === 'design_review' && $old_status !== 'design_review' ) || 
								 ( $new_step === 'design_review' && $old_step !== 'design_review' );
		
		if ( $entered_design_review ) {
			$this->send_email_to_customer( $project_id, 'design_ready' );
		}

		// 3. Diseño Aprobado (En Progreso)
		// Si entra en in_progress (o approved) desde design_review
		$target_statuses = [ 'in_progress', 'approved' ];
		$entered_progress = ( in_array( $new_status, $target_statuses ) && ! in_array( $old_status, $target_statuses ) ) || 
							( $new_step === 'in_progress' && $old_step !== 'in_progress' );

		if ( $entered_progress ) {
			// Verificar si veníamos de revisión (para no enviar en otros casos raros)
			// Aunque si pasa directo a progreso también es válido notificar al admin que "se aprobó" implícitamente?
			// El usuario dijo "cuando el cliente aprueba". 
			// Asumimos que si avanza a progreso, notificamos al admin.
			$this->send_email_to_admin( $project_id, 'design_approved' );
		}

		// 4. Proyecto Completado
		$entered_completed = ( $new_status === 'completed' && $old_status !== 'completed' ) || 
							 ( $new_step === 'completed' && $old_step !== 'completed' );

		if ( $entered_completed ) {
			$this->send_email_to_customer( $project_id, 'project_completed' );
		}
	}

	private function send_email_to_customer( $project_id, $type ) {
		$manager = new Project_Manager();
		$project = $manager->get_project( $project_id );
		if ( ! $project ) return;

		$user = get_userdata( $project->customer_id );
		if ( ! $user ) {
			error_log( "Proyectos Email Debug: Error - Customer not found for project $project_id" );
			return;
		}

		// Preparar datos
		$data = [
			'user' => $user,
			'project_name' => $project->name,
			'design_url'   => $manager->get_project_meta( $project->id, 'design_proposal_url' )
		];

		// Usar el Engine de Marketing
		if ( class_exists( '\Alezux_Members\Modules\Marketing\Marketing' ) ) {
			$engine = Marketing::get_instance()->get_engine();
			$sent = $engine->send_email( $type, $user->user_email, $data );
			error_log( "Proyectos Email Debug: Sending $type to customer " . $user->user_email . " | Result: " . ( $sent ? 'Success' : 'Failed' ) );
		}
	}

	private function send_email_to_admin( $project_id, $type ) {
		$manager = new Project_Manager();
		$project = $manager->get_project( $project_id );
		if ( ! $project ) return;

		$customer = get_userdata( $project->customer_id );
		$admin_email = get_option( 'admin_email' );

		// Preparar datos
		$data = [
			'project_name'  => $project->name,
			'customer_name' => $customer ? $customer->display_name : 'Cliente #' . $project->customer_id,
			'admin_url'     => admin_url( 'admin.php?page=alezux_projects' ) // O URL directa si existiera
		];

		if ( class_exists( '\Alezux_Members\Modules\Marketing\Marketing' ) ) {
			$engine = Marketing::get_instance()->get_engine();
			$sent = $engine->send_email( $type, $admin_email, $data );
			error_log( "Proyectos Email Debug: Sending $type to admin $admin_email | Result: " . ( $sent ? 'Success' : 'Failed' ) );
		}
	}
}
