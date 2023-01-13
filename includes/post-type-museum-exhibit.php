<?php namespace WSUWP\Plugin\Exhibits;

class Post_Type_Museum_Exhibit {

	private static $slug = 'wsuwp_museum_exhibit';

	private static $attributes = array(
		'labels'       => array(
			'name'               => 'Exhibits',
			'singular_name'      => 'Exhibit',
			'all_items'          => 'All Exhibits',
			'view_item'          => 'View Exhibit',
			'add_new_item'       => 'Add New Exhibit',
			'add_new'            => 'Add New',
			'edit_item'          => 'Edit Exhibit',
			'update_item'        => 'Update Exhibit',
			'search_items'       => 'Search Exhibits',
			'not_found'          => 'Not found',
			'not_found_in_trash' => 'Not found in Trash',
		),
		'description'  => 'WSU Museum Exhibits.',
		'public'       => true,
		'hierarchical' => false,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-images-alt',
		'supports'     => array(
			'title',
			'editor',
			'thumbnail',
			'revisions',
			'custom-fields',
		),
		'taxonomies'   => array(
			'category',
			'post_tag',
		),
		'rewrite'      => array(
			'slug'       => 'exhibit',
			'with_front' => true,
		),
	);


	public static function get( $name ) {

		switch ( $name ) {

			case 'post_type':
				return self::$slug;
			default:
				return '';

		}

	}


	public static function register_post_type() {

		register_post_type( self::$slug, self::$attributes );

		register_post_meta(
			self::$slug,
			'_wsuwp_original_post_id',
			array(
				'type'          => 'string',
				'show_in_rest'  => true,
				'single'        => true,
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

	}


	public static function init() {

		add_action( 'init', array( __CLASS__, 'register_post_type' ), 11 );

	}

}

Post_Type_Museum_Exhibit::init();
