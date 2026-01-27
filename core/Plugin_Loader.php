<?php
namespace Alezux_Members\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Plugin_Loader
 * Maneja la carga dinámica de módulos.
 */
class Plugin_Loader {

	private $modules = [];

	public function run() {
		$this->load_modules();
		$this->init_modules();
	}

	/**
	 * Escanea el directorio de módulos y carga los archivos principales.
	 */
	private function load_modules() {
		if ( ! is_dir( ALEZUX_MEMBERS_MODULES_PATH ) ) {
			return;
		}

		$dirs = glob( ALEZUX_MEMBERS_MODULES_PATH . '*', GLOB_ONLYDIR );

		foreach ( $dirs as $dir ) {
			$dirname = basename( $dir );
			// Convierte 'nombre-modulo' a 'Nombre_Modulo' para el nombre de la clase
			$class_name_suffix = str_replace( '-', '_', ucwords( $dirname, '-' ) ); 
			$file_path = $dir . '/' . $class_name_suffix . '.php';

			if ( file_exists( $file_path ) ) {
				require_once $file_path;
				
				// Asumimos que el namespace del módulo sigue la convención Alezux_Members\Modules\Nombre_Modulo
				$full_class_name = "\\Alezux_Members\\Modules\\{$class_name_suffix}\\{$class_name_suffix}";

				if ( class_exists( $full_class_name ) ) {
					$this->modules[ $dirname ] = new $full_class_name();
				}
			}
		}
	}

	/**
	 * Inicializa todos los módulos cargados.
	 */
	private function init_modules() {
		foreach ( $this->modules as $module ) {
			if ( method_exists( $module, 'init' ) ) {
				$module->init();
			}
		}
	}
}
