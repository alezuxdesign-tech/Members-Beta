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
		return [ 'alezux-members' ];
	}

	public function get_style_depends() {
		return [ 'alezux-notifications-css' ];
	}

	public function get_script_depends() {
		return [ 'alezux-notifications-js' ];
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

		$this->add_control(
			'notification_icon',
			[
				'label' => __( 'Icon', 'alezux-members' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-bell',
					'library' => 'fa-solid',
				],
			]
		);

		$this->end_controls_section();

		// Labels Section
		$this->start_controls_section(
			'section_labels',
			[
				'label' => __( 'Labels', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'label_mark_all_read',
			[
				'label' => __( 'Mark all read', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Mark all as read', 'alezux-members' ),
			]
		);

		$this->add_control(
			'label_inbox',
			[
				'label' => __( 'Inbox Tab', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Inbox', 'alezux-members' ),
			]
		);

		$this->add_control(
			'label_general',
			[
				'label' => __( 'General Tab', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'General', 'alezux-members' ),
			]
		);

		$this->add_control(
			'label_archived',
			[
				'label' => __( 'Archived Tab', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Archived', 'alezux-members' ),
			]
		);

		$this->add_control(
			'label_loading',
			[
				'label' => __( 'Loading Text', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Loading...', 'alezux-members' ),
			]
		);

		$this->add_control(
			'label_no_notifications',
			[
				'label' => __( 'No Notifications Text', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'No notifications', 'alezux-members' ),
			]
		);

		$this->end_controls_section();

		// Style Section
		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => __( 'Icon', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => __( 'Color', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-bell-icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .alezux-bell-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bg_color',
			[
				'label'     => __( 'Background Color', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-bell-icon' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => __( 'Size', 'alezux-members' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .alezux-bell-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .alezux-bell-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .alezux-bell-icon img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label'      => __( 'Padding', 'alezux-members' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .alezux-bell-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_border_radius',
			[
				'label'      => __( 'Border Radius', 'alezux-members' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .alezux-bell-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'badge_bg_color',
			[
				'label'     => __( 'Badge Background Color', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-notification-badge' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				],
				'condition' => [
					'notification_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'badge_text_color',
			[
				'label'     => __( 'Badge Text Color', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-notification-badge' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		// Dropdown Style Section
		$this->start_controls_section(
			'section_style_dropdown',
			[
				'label' => __( 'Dropdown / Panel', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'dropdown_bg_color',
			[
				'label'     => __( 'Background Color', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-notifications-dropdown' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'accordion_accent_color',
			[
				'label'     => __( 'Accent Color', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-tab.active' => 'color: {{VALUE}}; border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .alezux-tab.active .badge-count' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .alezux-notification-item.unread::after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="alezux-notifications-widget">
			<div class="alezux-bell-icon">
				<?php \Elementor\Icons_Manager::render_icon( $settings['notification_icon'], [ 'aria-hidden' => 'true' ] ); ?>
				<span class="alezux-notification-badge" style="display:none;">0</span>
			</div>
			
			<div class="alezux-notifications-dropdown">
				<div class="alezux-dropdown-header">
					<h3><?php echo esc_html( $settings['title'] ); ?></h3>
					<span class="alezux-mark-all-read"><?php echo esc_html( $settings['label_mark_all_read'] ); ?></span>
				</div>
				
				<div class="alezux-tabs">
					<div class="alezux-tab active" data-target="inbox"><?php echo esc_html( $settings['label_inbox'] ); ?> <span class="badge-count">0</span></div>
					<div class="alezux-tab" data-target="general"><?php echo esc_html( $settings['label_general'] ); ?></div>
					<div class="alezux-tab" data-target="archived"><?php echo esc_html( $settings['label_archived'] ); ?></div>
					<div class="alezux-settings-icon"><i class="eicon-gear"></i></div>
				</div>

				<div class="alezux-notifications-list" id="alezux-notif-list-inbox">
					<!-- Notifications loaded via AJAX -->
					<div class="alezux-loading"><?php echo esc_html( $settings['label_loading'] ); ?></div>
				</div>
				<div class="alezux-notifications-list" id="alezux-notif-list-general" style="display:none;"></div>
				<div class="alezux-notifications-list" id="alezux-notif-list-archived" style="display:none;"></div>
			</div>
		</div>
		<?php
	}
}
