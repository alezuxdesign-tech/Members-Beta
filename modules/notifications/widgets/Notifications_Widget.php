<?php
namespace Alezux_Members\Modules\Notifications\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Notifications_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_notifications';
	}

	public function get_title() {
		return __( 'Alezux Notifications', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-bell';
	}

	public function get_categories() {
		return [ 'alezux_members' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Notifications', 'alezux-members' ),
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="alezux-notifications-widget">
			<div class="alezux-bell-icon">
				<i class="eicon-bell" aria-hidden="true"></i>
				<span class="alezux-notification-badge" style="display:none;">0</span>
			</div>
			
			<div class="alezux-notifications-dropdown">
				<div class="alezux-dropdown-header">
					<h3><?php echo esc_html( $settings['title'] ); ?></h3>
					<span class="alezux-mark-all-read"><?php esc_html_e( 'Mark all as read', 'alezux-members' ); ?></span>
				</div>
				
				<div class="alezux-tabs">
					<div class="alezux-tab active" data-target="inbox">Inbox <span class="badge-count">0</span></div>
					<div class="alezux-tab" data-target="general">General</div>
					<div class="alezux-tab" data-target="archived">Archived</div>
					<div class="alezux-settings-icon"><i class="eicon-gear"></i></div>
				</div>

				<div class="alezux-notifications-list" id="alezux-notif-list-inbox">
					<!-- Notifications loaded via AJAX -->
					<div class="alezux-loading">Loading...</div>
				</div>
				<div class="alezux-notifications-list" id="alezux-notif-list-general" style="display:none;"></div>
				<div class="alezux-notifications-list" id="alezux-notif-list-archived" style="display:none;"></div>
			</div>
		</div>
		<?php
	}
}
