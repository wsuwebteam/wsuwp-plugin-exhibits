<?php namespace WSUWP\Plugin\Exhibits;

class Plugin {


	private static $version = '1.0.5';


	public static function get( $property ) {

		switch ( $property ) {

			case 'version':
				return self::$version;

			case 'dir':
				return plugin_dir_path( dirname( __FILE__ ) );

			case 'url':
				return plugin_dir_url( dirname( __FILE__ ) );

			case 'template_dir':
				return plugin_dir_path( dirname( __FILE__ ) ) . '/templates';

			default:
				return '';

		}

	}

	public static function init() {

		require_once __DIR__ . '/post-type-museum-exhibit.php';
		require_once __DIR__ . '/content-migrations.php';

	}


	public static function require_class( $class_name ) {

		require_once self::get( 'dir' ) . '/classes/class-' . $class_name . '.php';

	}
}

Plugin::init();
