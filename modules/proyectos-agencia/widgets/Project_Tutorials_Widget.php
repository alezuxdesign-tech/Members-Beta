<?php
namespace Alezux_Members\Modules\Proyectos_Agencia\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Alezux_Members\Modules\Proyectos_Agencia\Includes\Project_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Project_Tutorials_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_project_tutorials';
	}

	public function get_title() {
		return 'Tutoriales Netflix Style (Cliente)';
	}

	public function get_icon() {
		return 'eicon-video-camera';
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
			$user_id = get_current_user_id();
			if ( ! $user_id ) return;

			$manager = new Project_Manager();
			$projects = $manager->get_projects_by_user( $user_id );
			if ( ! empty( $projects ) ) {
				$project_id = $projects[0]->id;
			}
		}

		if ( ! $project_id ) {
			echo '<div class="alezux-alert warning">No se encontró un proyecto activo.</div>';
			return;
		}

		$manager = new Project_Manager();
		$tutorials = $manager->get_project_tutorials( $project_id );

		if ( empty( $tutorials ) ) {
			echo '<div class="alezux-alert info">Aún no hay tutoriales disponibles.</div>';
			return;
		}

		?>
		<style>
			.alezux-netflix-container {
				overflow-x: auto;
				white-space: nowrap;
				padding: 20px 0;
				scrollbar-width: thin;
				scrollbar-color: #e50914 #000;
			}
			.alezux-netflix-container::-webkit-scrollbar {
				height: 8px;
			}
			.alezux-netflix-container::-webkit-scrollbar-track {
				background: #000;
			}
			.alezux-netflix-container::-webkit-scrollbar-thumb {
				background: #e50914;
				border-radius: 4px;
			}
			.alezux-video-card {
				display: inline-block;
				width: 300px;
				margin-right: 15px;
				vertical-align: top;
				transition: transform 0.3s ease, z-index 0.3s ease;
				position: relative;
				cursor: pointer;
				border-radius: 4px;
				overflow: hidden;
			}
			.alezux-video-card:hover {
				transform: scale(1.1);
				z-index: 10;
				box-shadow: 0 10px 20px rgba(0,0,0,0.5);
			}
			.alezux-video-thumbnail {
				width: 100%;
				height: 169px;
				background-size: cover;
				background-position: center;
				background-color: #333;
				position: relative;
				display: flex;
				justify-content: center;
				align-items: center;
			}
			.alezux-play-icon {
				font-size: 40px;
				color: rgba(255,255,255,0.8);
				opacity: 0;
				transition: opacity 0.3s;
			}
			.alezux-video-card:hover .alezux-play-icon {
				opacity: 1;
			}
			.alezux-video-info {
				background: #141414;
				padding: 10px;
				white-space: normal;
			}
			.alezux-video-title {
				color: #fff;
				font-size: 14px;
				font-weight: bold;
				margin: 0;
				line-height: 1.2;
			}
			.alezux-video-duration {
				color: #999;
				font-size: 11px;
				margin-top: 5px;
			}

			/* Video Modal */
			.alezux-video-modal {
				position: fixed;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background: rgba(0,0,0,0.9);
				z-index: 10000;
				display: none;
				justify-content: center;
				align-items: center;
			}
			.alezux-video-wrapper {
				width: 80%;
				max-width: 900px;
				aspect-ratio: 16/9;
				background: #000;
				position: relative;
			}
			.alezux-video-close {
				position: absolute;
				top: -40px;
				right: 0;
				color: #fff;
				font-size: 30px;
				cursor: pointer;
			}
		</style>

		<div class="alezux-netflix-container">
			<?php foreach ( $tutorials as $video ) : 
				// Fallback thumbnail if none provided
				$thumbnail = !empty($video['thumbnail']) ? $video['thumbnail'] : 'https://via.placeholder.com/300x169/000000/FFFFFF?text=Tutorial';
			?>
				<div class="alezux-video-card" onclick="playVideo('<?php echo esc_url($video['url']); ?>', '<?php echo esc_attr($video['type']); ?>')">
					<div class="alezux-video-thumbnail" style="background-image: url('<?php echo esc_url($thumbnail); ?>');">
						<div class="alezux-play-icon"><i class="eicon-play"></i></div>
					</div>
					<div class="alezux-video-info">
						<h4 class="alezux-video-title"><?php echo esc_html( $video['title'] ); ?></h4>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<div id="alezux-video-modal" class="alezux-video-modal" onclick="closeVideoModal()">
			<div class="alezux-video-wrapper" onclick="event.stopPropagation()">
				<div class="alezux-video-close" onclick="closeVideoModal()">&times;</div>
				<div id="alezux-video-player-container" style="width:100%; height:100%;">
					<!-- Iframe will be injected here -->
				</div>
			</div>
		</div>

		<script>
		function playVideo(url, type) {
			var playerContainer = document.getElementById('alezux-video-player-container');
			var html = '';
			
			if (type === 'youtube') {
				// Extract ID if full URL, otherwise assume ID
				var videoId = url.match(/(?:youtu\.be\/|youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/);
				if (videoId) {
					html = '<iframe width="100%" height="100%" src="https://www.youtube.com/embed/' + videoId[1] + '?autoplay=1" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
				} else {
                    html = '<iframe width="100%" height="100%" src="' + url + '" frameborder="0" allow="autoplay; fullscreen"></iframe>';
                }
			} else if (type === 'vimeo') {
                // Simplistic Vimeo check
                var videoId = url.match(/vimeo\.com\/(\d+)/);
                if (videoId) {
                     html = '<iframe src="https://player.vimeo.com/video/' + videoId[1] + '?autoplay=1" width="100%" height="100%" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
                } else {
                     html = '<iframe width="100%" height="100%" src="' + url + '" frameborder="0" allow="autoplay; fullscreen"></iframe>';
                }
			} else {
				// MP4 or generic
				html = '<video width="100%" height="100%" controls autoplay><source src="' + url + '" type="video/mp4">Tu navegador no soporta video.</video>';
			}

			playerContainer.innerHTML = html;
			document.getElementById('alezux-video-modal').style.display = 'flex';
		}

		function closeVideoModal() {
			document.getElementById('alezux-video-modal').style.display = 'none';
			document.getElementById('alezux-video-player-container').innerHTML = '';
		}
		</script>
		<?php
	}
}
