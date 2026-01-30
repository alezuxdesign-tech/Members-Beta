<?php
namespace Alezux_Members\Modules\Estudiantes\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Estudiantes_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_estudiantes_table';
	}

	public function get_title() {
		return esc_html__( 'Tabla de Estudiantes', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-table';
	}

	public function get_categories() {
		return [ 'alezux-admin' ];
	}

	public function get_style_depends() {
		return [ 'alezux-estudiantes-css' ];
	}

	protected function _register_controls() {
		// --- Sección de Estilo de la Tabla ---
		$this->start_controls_section(
			'section_style_table',
			[
				'label' => esc_html__( 'Tabla', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'table_header_bg',
			[
				'label'     => esc_html__( 'Fondo Cabecera', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-estudiantes-table th' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'table_header_color',
			[
				'label'     => esc_html__( 'Color Texto Cabecera', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-estudiantes-table th' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'table_header_typography',
				'label'    => esc_html__( 'Tipografía Cabecera', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-estudiantes-table th',
			]
		);

		$this->add_control(
			'table_row_even_bg',
			[
				'label'     => esc_html__( 'Fondo Filas Pares', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-estudiantes-table tr:nth-child(even) td' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'table_row_odd_bg',
			[
				'label'     => esc_html__( 'Fondo Filas Impares', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-estudiantes-table tr:nth-child(odd) td' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// --- Sección Botón Gestionar ---
		$this->start_controls_section(
			'section_style_btn',
			[
				'label' => esc_html__( 'Botón Gestionar', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'btn_bg_color',
			[
				'label'     => esc_html__( 'Color Fondo', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .btn-gestionar' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_text_color',
			[
				'label'     => esc_html__( 'Color Texto', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .btn-gestionar' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'btn_border',
				'selector' => '{{WRAPPER}} .btn-gestionar',
			]
		);

		$this->add_control(
			'btn_border_radius',
			[
				'label'      => esc_html__( 'Radio de Borde', 'alezux-members' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .btn-gestionar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		// Obtener estudiantes
		$args = [
			'role__in' => [ 'subscriber', 'student' ],
			'number'   => 50, // Límite inicial para rendimiento
		];

		$user_query = new \WP_User_Query( $args );
		$students = $user_query->get_results();

		// Fallback si no hay roles específicos
		if ( empty( $students ) ) {
			$args = [ 'number' => 50 ]; 
			$user_query = new \WP_User_Query( $args );
			$students = $user_query->get_results();
		}

		?>
		<div class="alezux-estudiantes-table-wrapper">
			<table class="alezux-estudiantes-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'FOTO', 'alezux-members' ); ?></th>
						<th><?php esc_html_e( 'NOMBRE', 'alezux-members' ); ?></th>
						<th><?php esc_html_e( 'CORREO', 'alezux-members' ); ?></th>
						<th><?php esc_html_e( 'ESTADO', 'alezux-members' ); ?></th>
						<th><?php esc_html_e( 'FUNCIONES', 'alezux-members' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( ! empty( $students ) ) : ?>
						<?php foreach ( $students as $student ) : 
							$avatar_url = get_avatar_url( $student->ID );
							$name = $student->display_name;
							$email = $student->user_email;
							// Estado simulado 'OK' o 'Activo' por ahora
							// Se podría integrar lógica real de actividad si existiera
							$status = 'Active'; 
							$status_label = 'OK';
							$status_class = 'status-active';
						?>
						<tr>
							<td class="col-foto">
								<img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $name ); ?>">
							</td>
							<td class="col-nombre">
								<?php echo esc_html( $name ); ?>
								<div style="font-size: 12px; color: #999;"><?php echo '@' . esc_html( $student->user_nicename ); ?></div>
							</td>
							<td class="col-correo">
								<?php echo esc_html( $email ); ?>
							</td>
							<td class="col-estado">
								<span class="<?php echo esc_attr( $status_class ); ?>">
									<i class="fa fa-circle" style="font-size: 8px; margin-right: 4px;"></i>
									<?php echo esc_html( $status_label ); ?>
								</span>
							</td>
							<td class="col-funciones">
								<button class="btn-gestionar" data-student-id="<?php echo esc_attr( $student->ID ); ?>">
									<i class="fa fa-cog"></i> <?php esc_html_e( 'Gestionar', 'alezux-members' ); ?>
								</button>
							</td>
						</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="5" style="text-align:center; padding: 20px;">
								<?php esc_html_e( 'No se encontraron estudiantes.', 'alezux-members' ); ?>
							</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		
		<!-- Script simple para el botón (placeholder para futuro popup) -->
		<script>
		jQuery(document).ready(function($) {
			$('.btn-gestionar').on('click', function(e) {
				e.preventDefault();
				var studentId = $(this).data('student-id');
				// Aquí irá la lógica del popup
				alert('Gestionar estudiante ID: ' + studentId + '. (Funcionalidad de Popup pendiente)');
			});
		});
		</script>
		<?php
	}
}
