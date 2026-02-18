<?php
namespace Alezux_Members\Modules\Proyectos_Agencia\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Alezux_Members\Modules\Proyectos_Agencia\Includes\Project_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Project_Deliverables_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_project_deliverables';
	}

	public function get_title() {
		return 'Carpetas Entregables (Cliente)';
	}

	public function get_icon() {
		return 'eicon-folder';
	}

	public function get_categories() {
		return [ 'alezux-members' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => 'Configuración',
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'project_id_source',
			[
				'label' => 'Origen del Proyecto',
				'type' => Controls_Manager::SELECT,
				'default' => 'current_user',
				'options' => [
					'current_user' => 'Asignado al Usuario Actual',
					'manual'       => 'Manual (ID)',
				],
			]
		);

		$this->add_control(
			'manual_project_id',
			[
				'label' => 'ID del Proyecto',
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'project_id_source' => 'manual',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		
		$project_id = 0;

		if ( 'manual' === $settings['project_id_source'] ) {
			$project_id = absint( $settings['manual_project_id'] );
		} else {
			// Buscar proyecto del usuario actual
			$user_id = get_current_user_id();
			if ( ! $user_id ) return;

			$manager = new Project_Manager();
			$projects = $manager->get_projects_by_user( $user_id );
			if ( ! empty( $projects ) ) {
				$project_id = $projects[0]->id; // Tomar el más reciente
			}
		}

		if ( ! $project_id ) {
			echo '<div class="alezux-alert warning">No se encontró un proyecto activo.</div>';
			return;
		}

		$manager = new Project_Manager();
		$deliverables = $manager->get_project_deliverables( $project_id );

		if ( empty( $deliverables ) ) {
			echo '<div class="alezux-alert info">Aún no hay entregables disponibles.</div>';
			return;
		}

		// Styles for Glassmorphism Folders
		?>
		<style>
			.alezux-folders-grid {
				display: grid;
				grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
				gap: 20px;
				padding: 20px;
			}
			.alezux-folder-card {
				background: rgba(255, 255, 255, 0.05);
				backdrop-filter: blur(10px);
				-webkit-backdrop-filter: blur(10px);
				border: 1px solid rgba(255, 255, 255, 0.1);
				border-radius: 16px;
				padding: 20px;
				text-align: center;
				transition: all 0.3s ease;
				cursor: pointer;
				position: relative;
				overflow: hidden;
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
				min-height: 180px;
			}
			.alezux-folder-card:hover {
				transform: translateY(-5px);
				background: rgba(255, 255, 255, 0.1);
				box-shadow: 0 10px 30px rgba(0,0,0,0.2);
				border-color: rgba(255, 255, 255, 0.2);
			}
			.alezux-folder-icon {
				font-size: 60px;
				color: #4facfe; /* Default Blue */
				margin-bottom: 15px;
				transition: all 0.3s ease;
			}
			.alezux-folder-card:hover .alezux-folder-icon {
				transform: scale(1.1);
				color: #00f2fe;
			}
			.alezux-folder-title {
				font-family: 'Inter', sans-serif;
				font-weight: 600;
				color: #fff;
				font-size: 16px;
				margin: 0;
			}
			.alezux-folder-count {
				font-size: 12px;
				color: rgba(255, 255, 255, 0.5);
				margin-top: 5px;
			}
			
			/* Modal Styles */
			.alezux-folder-modal {
				position: fixed;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background: rgba(0,0,0,0.8);
				backdrop-filter: blur(5px);
				z-index: 9999;
				display: none;
				justify-content: center;
				align-items: center;
			}
			.alezux-folder-modal-content {
				background: #1a202c;
				width: 90%;
				max-width: 600px;
				border-radius: 12px;
				padding: 30px;
				position: relative;
				border: 1px solid rgba(255,255,255,0.1);
				box-shadow: 0 20px 50px rgba(0,0,0,0.5);
			}
			.alezux-modal-close {
				position: absolute;
				top: 15px;
				right: 15px;
				color: #fff;
				cursor: pointer;
				font-size: 20px;
			}
			.alezux-file-list {
				list-style: none;
				padding: 0;
				margin-top: 20px;
			}
			.alezux-file-item {
				display: flex;
				align-items: center;
				padding: 12px;
				border-bottom: 1px solid rgba(255,255,255,0.05);
				transition: background 0.2s;
			}
			.alezux-file-item:hover {
				background: rgba(255,255,255,0.02);
			}
			.alezux-file-icon {
				margin-right: 15px;
				color: #a0aec0;
				font-size: 20px;
			}
			.alezux-file-name {
				color: #e2e8f0;
				flex-grow: 1;
				text-decoration: none;
				font-size: 14px;
			}
			.alezux-file-download {
				color: #4facfe;
				font-size: 18px;
			}
		</style>

		<div class="alezux-folders-grid">
			<?php foreach ( $deliverables as $index => $folder ) : 
				$files_count = isset($folder['files']) ? count($folder['files']) : 0;
			?>
				<div class="alezux-folder-card" onclick="openFolderModal(<?php echo $index; ?>)">
					<div class="alezux-folder-icon">
						<i class="eicon-folder"></i>
					</div>
					<h3 class="alezux-folder-title"><?php echo esc_html( $folder['name'] ); ?></h3>
					<div class="alezux-folder-count"><?php echo $files_count; ?> Archivos</div>

					<!-- Hidden Content for Modal -->
					<div id="folder-content-<?php echo $index; ?>" style="display:none;">
						<ul class="alezux-file-list">
							<?php if ( ! empty( $folder['files'] ) ) : ?>
								<?php foreach ( $folder['files'] as $file ) : ?>
									<li>
										<a href="<?php echo esc_url( $file['url'] ); ?>" target="_blank" class="alezux-file-item">
											<span class="alezux-file-icon"><i class="eicon-file-download"></i></span>
											<span class="alezux-file-name"><?php echo esc_html( $file['name'] ); ?></span>
											<span class="alezux-file-download"><i class="eicon-download-bold"></i></span>
										</a>
									</li>
								<?php endforeach; ?>
							<?php else: ?>
								<li style="color:#718096; padding:10px;">Carpeta vacía</li>
							<?php endif; ?>
						</ul>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<!-- Generic Modal Container -->
		<div id="alezux-folder-modal" class="alezux-folder-modal" onclick="closeFolderModal(event)">
			<div class="alezux-folder-modal-content" onclick="event.stopPropagation()">
				<div class="alezux-modal-close" onclick="closeFolderModal(event)"><i class="eicon-close"></i></div>
				<h3 id="modal-folder-title" style="color:#fff; margin-top:0;">Carpeta</h3>
				<div id="modal-folder-body"></div>
			</div>
		</div>

		<script>
		function openFolderModal(index) {
			var title = jQuery('.alezux-folder-card').eq(index).find('.alezux-folder-title').text();
			var content = jQuery('#folder-content-' + index).html();
			
			jQuery('#modal-folder-title').text(title);
			jQuery('#modal-folder-body').html(content);
			jQuery('#alezux-folder-modal').fadeIn(200).css('display', 'flex');
		}

		function closeFolderModal(e) {
			jQuery('#alezux-folder-modal').fadeOut(200);
		}
		</script>
		<?php
	}
}
