<?php namespace WSUWP\Plugin\Exhibits;

class Content_Migrations {

	private static $migrations = array(
		array(
			'site_url'  => 'https://museum.wsu.edu/events',
			'migration' => 'migration-museum-events',
		),
		array(
			'site_url'  => 'https://stage.web.wsu.edu/museum-wds',
			'migration' => 'migration-museum-events',
		),
	);


	public static function init() {

		$site_url = site_url();

		$migration_key = array_search( $site_url, array_column( self::$migrations, 'site_url' ), true );

		if ( $migration_key ) {
			require_once __DIR__ . '/' . self::$migrations[ $migration_key ]['migration'] . '.php';
		}

	}

}

Content_Migrations::init();
