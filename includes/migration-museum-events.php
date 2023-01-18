<?php namespace WSUWP\Plugin\Exhibits;

class Migration_Museum_Events {

	private static $slug = 'museum-exhibit';

	private static function get_existing_posts() {

		$args = array(
			'post_type'      => array( self::$slug ),
			'posts_per_page' => -1,
			'post__not_in'   => self::get_exclude_ids(),
		);

		$result = new \WP_Query( $args );

		return $result->posts;

	}


	private static function get_exclude_ids() {

		$exclude_ids = array();

		$args = array(
			'post_type'      => Post_Type_Museum_Exhibit::get( 'post_type' ),
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => '_wsuwp_original_post_id',
					'value'   => '',
					'compare' => '!=',
				),
			),
		);

		$query = new \WP_Query( $args );

		if ( $query->have_posts() ) {
			$posts = $query->posts;
			foreach ( $posts as $post ) {
				$id            = $post->ID;
				$exclude_ids[] = get_post_meta( $id, '_wsuwp_original_post_id', true );
			}
		}

		return $exclude_ids;

	}


	private static function append_hero_content( $content, $post ) {

		$artist    = get_post_meta( $post->ID, '_exhibit_artist_text', true );
		$slide_ids = array(
			get_post_thumbnail_id( $post->ID ),
			get_post_meta( $post->ID, 'museum-exhibit_slide-two_thumbnail_id', true ),
			get_post_meta( $post->ID, 'museum-exhibit_slide-three_thumbnail_id', true ),
		);

		$content .= '<!-- wp:wsuwp/hero {"title":"' . $post->post_title . '","headingTag":"h1",';
		$content .= '"caption":"' . $artist . '",';
		$content .= '"sliderImages":[';

		foreach ( $slide_ids as $key => $slide_id ) {
			$image_url = wp_get_attachment_image_url( $slide_id, 'full' );
			$thumb     = wp_get_attachment_image_url( $slide_id, 'thumbnail' );

			$content .= '{"id":' . $slide_id . ',';
			$content .= '"url":"' . $image_url . '",';
			$content .= '"thumbnail":"' . $thumb . '",';
			$content .= '"focalPoint":{"x":0.5,"y":0.25}}';

			if ( array_key_last( $slide_ids ) !== $key ) {
				$content .= ',';
			}
		}

		$content .= '],';
		$content .= '"sliderDelay":5000,"backgroundType":"slider"} /-->';

		return $content;

	}


	private static function append_gallery_content( $content, $post ) {

		$gallery_content     = get_post_meta( $post->ID, '_exhibit_gallery_content', true );
		$has_gallery_content = ! empty( trim( wp_strip_all_tags( $gallery_content ) ) );

		if ( $has_gallery_content ) {
			$content .= '<!-- wp:spacer {"height":50} --><div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div><!-- /wp:spacer -->';

			if ( false !== strpos( $gallery_content, '[gallery ' ) ) {
				preg_match( '/ids=\"(.*)\"/', $gallery_content, $matches );
				$ids = explode( ',', $matches[1] );

				$content .= '<!-- wp:gallery {"ids":[' . implode( ',', $ids ) . '],"columns":3,"linkTo":"file"} -->';
				$content .= '<figure class="wp-block-gallery columns-3 is-cropped"><ul class="blocks-gallery-grid">';

				foreach ( $ids as $id ) {
					$image_url = wp_get_attachment_image_url( $id, 'large' );

					if ( ! $image_url ) {
						continue;
					}

					$content .= '<li class="blocks-gallery-item"><figure>';
					$content .= '<a href="' . $image_url . '">';
					$content .= '<img src="' . $image_url . '"';
					$content .= ' alt=""';
					$content .= ' data-id="' . $id . '"';
					$content .= ' class="wp-image-' . $id . '"/>';
					$content .= '</a></figure></li>';
				}

				$content .= '</ul></figure>';
				$content .= '<!-- /wp:gallery -->';
			}
		}

		return $content;

	}


	private static function get_post_content( $post ) {

		$content = '';

		$post_content    = $post->post_content;
		$sidebar_content = get_post_meta( $post->ID, '_exhibit_sidebar_content', true );

		$has_sidebar_content = ! empty( trim( wp_strip_all_tags( $sidebar_content ) ) );

		$content = self::append_hero_content( $content, $post );

		if ( $has_sidebar_content ) {
			$content .= '<!-- wp:wsuwp/row {"layout":"sidebar-right"} -->';
			$content .= '<!-- wp:wsuwp/column -->';
		}

		$content .= '<!-- wp:freeform -->';
		$content .= apply_filters( 'the_content', str_replace( '<!--more-->', '', $post_content ) );
		$content .= '<!-- /wp:freeform -->';

		if ( $has_sidebar_content ) {
			$content .= '<!-- /wp:wsuwp/column -->';
			$content .= '<!-- wp:wsuwp/column -->';

			$content .= '<!-- wp:freeform -->';
			$content .= apply_filters( 'the_content', str_replace( '<!--more-->', '', $sidebar_content ) );
			$content .= '<!-- /wp:freeform -->';

			$content .= '<!-- /wp:wsuwp/column -->';
			$content .= '<!-- /wp:wsuwp/row -->';
		}

		$content = self::append_gallery_content( $content, $post );

		return $content;

	}


	private static function migrate_post( $post ) {

		$post_data = array(
			'ID'            => 0,
			'post_author'   => $post->post_author,
			'post_date'     => $post->post_date,
			'post_modified' => $post->post_modified,
			'post_title'    => $post->post_title,
			'post_name'     => $post->post_name,
			'post_excerpt'  => $post->post_excerpt,
			'post_status'   => $post->post_status,
			'post_type'     => Post_Type_Museum_Exhibit::get( 'post_type' ),
			'post_content'  => self::get_post_content( $post ),
			'meta_input'    => array(
				'_wsuwp_original_post_id' => $post->ID,
			),
		);

		$post_id = wp_insert_post( $post_data );

	}


	private static function migrate_posts( $posts ) {

		foreach ( $posts as $post ) {
			self::migrate_post( $post );
		}

	}


	public static function init() {

		$has_run = get_option( 'wsuwp_exhibits_imported', false );

		if ( false === $has_run ) {
			$posts = self::get_existing_posts();
			self::migrate_posts( $posts );

			add_option( 'wsuwp_exhibits_imported', true );
		}

	}

}

Migration_Museum_Events::init();
