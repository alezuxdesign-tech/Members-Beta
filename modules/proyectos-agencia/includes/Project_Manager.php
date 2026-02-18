<?php
namespace Alezux_Members\Modules\Proyectos_Agencia\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Project_Manager {

	public function get_project( $project_id ) {
		return null;
	}

	public function get_all_project_meta( $project_id ) {
		return [];
	}

	public function get_project_deliverables( $project_id ) {
		return [];
	}

	public function get_project_tutorials( $project_id ) {
		return [];
	}

	public function update_project_deliverables( $project_id, $data ) {
		return false;
	}

	public function update_project_tutorials( $project_id, $data ) {
		return false;
	}
}
