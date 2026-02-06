<?php
namespace Alezux_Members\Modules\Logros\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Recent_Logros_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_recent_logros';
	}

	public function get_title() {
		return esc_html__( 'Últimos Logros', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	public function get_categories() {
		return [ 'alezux-lms' ];
	}

	public function get_style_depends() {
		return [ 'alezux-recent-logros-css' ];
	}

	public function get_script_depends() {
		return [ 'alezux-recent-logros-js' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Contenido', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'limit',
			[
				'label' => esc_html__( 'Límite de Logros', 'alezux-members' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 20,
				'step' => 1,
				'default' => 5,
			]
		);

		$this->add_control(
			'logros_page_url',
			[
				'label' => esc_html__( 'URL Página de Logros', 'alezux-members' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://tu-sitio.com/logros', 'alezux-members' ),
				'default' => [
					'url' => '#',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$limit = $settings['limit'];
		$logros_url = ! empty( $settings['logros_page_url']['url'] ) ? $settings['logros_page_url']['url'] : '#';

		global $wpdb;
		$table_name = $wpdb->prefix . 'alezux_achievements';

		// Consulta segura a la base de datos
		$results = $wpdb->get_results( $wpdb->prepare( 
			"SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d", 
			$limit 
		) );

		if ( empty( $results ) ) {
			echo '<div class="alezux-no-logros">No hay logros recientes.</div>';
			return;
		}

		echo '<div class="alezux-recent-logros-widget">';

		foreach ( $results as $row ) {
			$student_id = $row->student_id;
			$user_info = get_userdata( $student_id );
			
			if ( ! $user_info ) {
				continue;
			}

			$student_name = $user_info->display_name;
			$avatar_url = get_avatar_url( $student_id, ['size' => 96] );
			
			// Calcular tiempo transcurrido
			$time_ago = human_time_diff( strtotime( $row->created_at ), current_time( 'timestamp' ) ) . ' atrás';
			
            // Formatear mensaje corto (si es necesario un truncado extra visual, aunque CSS lo hace)
            // Aquí usamos mb_substr para asegurar compatibilidad con caracteres especiales
            $message_preview = mb_strimwidth( strip_tags( $row->message ), 0, 100, '...' );

			?>
			<a href="<?php echo esc_url( $logros_url ); ?>" class="aleuz-logro-item">
				<div class="alezux-logro-avatar">
					<img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $student_name ); ?>">
				</div>
				<div class="alezux-logro-content">
					<div class="alezux-logro-header">
						<span class="alezux-logro-name"><?php echo esc_html( $student_name ); ?></span>
						<span class="alezux-logro-time"><?php echo esc_html( $time_ago ); ?></span>
					</div>
					<p class="alezux-logro-message"><?php echo esc_html( $message_preview ); ?></p>
				</div>
			</a>
			<?php
		}

		echo '</div>';
	}
}
