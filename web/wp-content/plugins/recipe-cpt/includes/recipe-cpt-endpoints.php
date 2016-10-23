<?php
namespace hazels_heritage\recipe_endpoints;

/**
 * Register recipe REST endpoints
 */
function register_api_hooks() {
	$namespace = 'recipes/v1';

	register_rest_route( $namespace, '/recipes/', array(
		'methods'  => 'GET',
		'callback' => __NAMESPACE__ . '\list_recipes',
	) );

	register_rest_route( $namespace, '/recipes/(?P<id>\d+)', array(
		'methods'  => 'GET',
		'callback' => __NAMESPACE__ . '\recipe_details',
		'args'     => array(
			'id' => array(
				'validate_callback' => function ( $param, $request, $key ) {
					$args = array(
						'post_type'           => 'recipe',
						'posts_per_page'      => 1,
						'post_status'         => 'publish',
						'post__in'            => array( (int) $param ),
						'ignore_sticky_posts' => true,
					);

					$query = new \WP_Query( $args );

					return $query->have_posts();
				}
			),
		),
	) );

	register_rest_route( $namespace, '/main-ingredients/', array(
		'methods'  => 'GET',
		'callback' => __NAMESPACE__ . '\list_main_ingredients',
	) );

	register_rest_route( $namespace, '/main-ingredients/(?P<id>\d+)', array(
		'methods'  => 'GET',
		'callback' => __NAMESPACE__ . '\list_main_ingredient_recipes',
		'args'     => array(
			'id' => array(
				'validate_callback' => function ( $param, $request, $key ) {
					$args = array(
						'post_type'           => 'recipe',
						'tax_query'           => array(
							array(
								'taxonomy' => 'recipe_main_ingredient',
								'terms'    => (int) $param,
							),
						),
						'posts_per_page'      => 1,
						'post_status'         => 'publish',
						'ignore_sticky_posts' => true,
					);

					$query = new \WP_Query( $args );

					return $query->have_posts();
				}
			),
		),
	) );
	
	register_rest_route( $namespace, '/recipe-types/', array(
		'methods'  => 'GET',
		'callback' => __NAMESPACE__ . '\list_recipe_types',
	) );

	register_rest_route( $namespace, '/recipe-types/(?P<id>\d+)', array(
		'methods'  => 'GET',
		'callback' => __NAMESPACE__ . '\list_recipe_type_recipes',
		'args'     => array(
			'id' => array(
				'validate_callback' => function ( $param, $request, $key ) {
					$args = array(
						'post_type'           => 'recipe',
						'tax_query'           => array(
							array(
								'taxonomy' => 'recipe_type',
								'terms'    => (int) $param,
							),
						),
						'posts_per_page'      => 1,
						'post_status'         => 'publish',
						'ignore_sticky_posts' => true,
					);

					$query = new \WP_Query( $args );

					return $query->have_posts();
				}
			),
		),
	) );

}

add_action( 'rest_api_init', __NAMESPACE__ . '\register_api_hooks' );

function list_recipes() {

	if ( 0 || false === ( $return = get_transient( 'recipes_list' ) ) || IS_LOCAL ) {

		$args = array(
			'post_type'      => 'recipe',
			'post_status'    => 'publish',
			'posts_per_page' => 25,
		);
		
		if( isset( $_GET['per_page'] ) ){
			$per_page = (int) $_GET['per_page'];
			if( $per_page ){
				$args['posts_per_page'] = $per_page;
			}
		}

		if( isset( $_GET['paged'] ) ){
			$paged = (int) $_GET['paged'];
			if( $paged ){
				$args['paged'] = $paged;
			}
		}

		$return = array(
			'total'   => 0,
			'count'   => 0,
			'recipes' => array(),
			'per_page' => $args['posts_per_page'],
			'paged' => $args['paged'],
		);

		$the_query = new \WP_Query( $args );

		if ( $the_query->have_posts() ):

			$return['total'] = (int) $the_query->found_posts;
			$return['count'] = (int) $the_query->post_count;

			while ( $the_query->have_posts() ):
				$the_query->the_post();
				$post_id = get_the_ID();

				$desc = get_post_meta( $post_id, 'recipe_desc', true );
				$desc = ( empty( $desc ) ) ? false : $desc;

				$type = wp_get_post_terms( $post_id, 'recipe_type' );
				$type = ( empty( $type ) ) ? false : $type;
				if ( is_array( $type ) && 1 === count( $type ) ) {
					$type = $type[0];
				}

				$main_ingredient = wp_get_post_terms( $post_id, 'recipe_main_ingredient' );
				$main_ingredient = ( empty( $main_ingredient ) ) ? false : $main_ingredient;
				if ( is_array( $main_ingredient ) && 1 === count( $main_ingredient ) ) {
					$main_ingredient = $main_ingredient[0];
				}

				$thumbnail = get_post_thumbnail_id( $post_id );
				if ( empty( $thumbnail ) ) {
					$thumbnail = false;
				} else {
					$thumbnail = wp_get_attachment_image_src( $thumbnail, 'thumbnail' );
					if ( is_array( $thumbnail ) ) {
						$thumbnail = array(
							'src'    => $thumbnail[0],
							'width'  => $thumbnail[1],
							'height' => $thumbnail[2],
						);
					}
				}

				$return['recipes'][ $post_id ] = array(
					'ID'              => $post_id,
					'title'           => get_the_title( $post_id ),
					'desc'            => $desc,
					'type'            => $type,
					'main_ingredient' => $main_ingredient,
					'thumbnail'       => $thumbnail,
				);

			endwhile;

			wp_reset_postdata();

			// cache for 10 minutes
			set_transient( 'recipes_list', $return, apply_filters( 'posts_ttl', 60 * 10 ) );

		endif;

	}

	$response = new \WP_REST_Response( $return );

	$response->header( 'Access-Control-Allow-Origin', apply_filters( 'access_control_allow_origin', '*' ) );

	return $response;
}

function list_main_ingredients() {

	if ( 0 || false === ( $return = get_transient( 'main_ingredients_list' ) ) || IS_LOCAL ) {

		$return = array(
			'total'            => 0,
			'main_ingredients' => array(),
		);

		$args = array(
			'taxonomy' => 'recipe_main_ingredient',
			'orderby'  => 'name',
			'order'    => 'ASC',
			'number'   => 25,
		);

		$the_terms = get_terms( $args );

		if ( ! is_wp_error( $the_terms ) && ! empty( $the_terms ) ):
			$return['total'] = count( $the_terms );
			foreach ( $the_terms as $term ):

				$term_details = array(
					'ID'           => $term->term_id,
					'name'         => $term->name,
					'slug'         => $term->slug,
					'has_children' => ( 0 === $term->parent ) ? false : true,
				);

				$return['main_ingredients'][$term->term_id] = $term_details;

			endforeach;

			// cache for 10 minutes
			set_transient( 'main_ingredients_list', $return, apply_filters( 'posts_ttl', 60 * 10 ) );

		endif;
	}

	$response = new \WP_REST_Response( $return );

	$response->header( 'Access-Control-Allow-Origin', apply_filters( 'access_control_allow_origin', '*' ) );

	return $response;
}

function list_recipe_types() {

	if ( 0 || false === ( $return = get_transient( 'recipe_types_list' ) ) || IS_LOCAL ) {

		$return = array(
			'total'            => 0,
			'recipe_types' => array(),
		);

		$args = array(
			'taxonomy' => 'recipe_type',
			'orderby'  => 'name',
			'order'    => 'ASC',
			'number'   => 25,
		);

		$the_terms = get_terms( $args );

		if ( ! is_wp_error( $the_terms ) && ! empty( $the_terms ) ):
			$return['total'] = count( $the_terms );
			foreach ( $the_terms as $term ):

				$term_details = array(
					'ID'           => $term->term_id,
					'name'         => $term->name,
					'slug'         => $term->slug,
					'has_children' => ( 0 === $term->parent ) ? false : true,
				);

				$return['recipe_types'][$term->term_id] = $term_details;

			endforeach;

			// cache for 10 minutes
			set_transient( 'recipe_types_list', $return, apply_filters( 'posts_ttl', 60 * 10 ) );

		endif;
	}

	$response = new \WP_REST_Response( $return );

	$response->header( 'Access-Control-Allow-Origin', apply_filters( 'access_control_allow_origin', '*' ) );

	return $response;
}

function list_main_ingredient_recipes( $request ) {

	$term_id = (int) $request['id'];

	if ( 0 || false === ( $return = get_transient( 'main_ingredient_' . $term_id . '_recipes_list' ) ) || IS_LOCAL ) {

		$term_name = get_term_field( 'name', $term_id, 'recipe_main_ingredient' );
		if ( null === $term_name || is_wp_error( $term_name ) ) {
			$term_name = false;
		}

		$return = array(
			'total'   => 0,
			'name'    => $term_name,
			'recipes' => array(),
		);

		$args = array(
			'post_type'           => 'recipe',
			'post_status'         => 'publish',
			'posts_per_page'      => 25,
			'nopaging'            => true,
			'tax_query'           => array(
				array(
					'taxonomy' => 'recipe_main_ingredient',
					'terms'    => $term_id,
				),
			),
			'ignore_sticky_posts' => true,
		);

		$the_query = new \WP_Query( $args );

		if ( $the_query->have_posts() ):

			$return['total'] = (int) $the_query->post_count;

			while ( $the_query->have_posts() ):
				$the_query->the_post();
				$post_id = get_the_ID();

				$desc = get_post_meta( $post_id, 'recipe_desc', true );
				$desc = ( empty( $desc ) ) ? false : $desc;

				$type = wp_get_post_terms( $post_id, 'recipe_type' );
				$type = ( empty( $type ) ) ? false : $type;
				if ( is_array( $type ) && 1 === count( $type ) ) {
					$type = $type[0];
				}

				$main_ingredient = wp_get_post_terms( $post_id, 'recipe_main_ingredient' );
				$main_ingredient = ( empty( $main_ingredient ) ) ? false : $main_ingredient;
				if ( is_array( $main_ingredient ) && 1 === count( $main_ingredient ) ) {
					$main_ingredient = $main_ingredient[0];
				}

				$thumbnail = get_post_thumbnail_id( $post_id );
				if ( empty( $thumbnail ) ) {
					$thumbnail = false;
				} else {
					$thumbnail = wp_get_attachment_image_src( $thumbnail, 'thumbnail' );
					if ( is_array( $thumbnail ) ) {
						$thumbnail = array(
							'src'    => $thumbnail[0],
							'width'  => $thumbnail[1],
							'height' => $thumbnail[2],
						);
					}
				}

				$return['recipes'][ $post_id ] = array(
					'ID'              => $post_id,
					'title'           => get_the_title( $post_id ),
					'desc'            => $desc,
					'type'            => $type,
					'main_ingredient' => $main_ingredient,
					'thumbnail'       => $thumbnail,
				);

			endwhile;

			wp_reset_postdata();

			// cache for 10 minutes
			set_transient( 'main_ingredient_' . $term_id . '_recipes_list', $return, apply_filters( 'posts_ttl', 60 * 10 ) );

		endif;

	}

	$response = new \WP_REST_Response( $return );

	$response->header( 'Access-Control-Allow-Origin', apply_filters( 'access_control_allow_origin', '*' ) );

	return $response;
}

function list_recipe_type_recipes( $request ) {

	$term_id = (int) $request['id'];

	if ( 0 || false === ( $return = get_transient( 'recipe_type_' . $term_id . '_recipes_list' ) ) || IS_LOCAL ) {

		$term_name = get_term_field( 'name', $term_id, 'recipe_main_ingredient' );
		if ( null === $term_name || is_wp_error( $term_name ) ) {
			$term_name = false;
		}

		$return = array(
			'total'   => 0,
			'name'    => $term_name,
			'recipes' => array(),
		);

		$args = array(
			'post_type'           => 'recipe',
			'post_status'         => 'publish',
			'posts_per_page'      => 25,
			'nopaging'            => true,
			'tax_query'           => array(
				array(
					'taxonomy' => 'recipe_type',
					'terms'    => $term_id,
				),
			),
			'ignore_sticky_posts' => true,
		);

		$the_query = new \WP_Query( $args );

		if ( $the_query->have_posts() ):

			$return['total'] = (int) $the_query->post_count;

			while ( $the_query->have_posts() ):
				$the_query->the_post();
				$post_id = get_the_ID();

				$desc = get_post_meta( $post_id, 'recipe_desc', true );
				$desc = ( empty( $desc ) ) ? false : $desc;

				$type = wp_get_post_terms( $post_id, 'recipe_type' );
				$type = ( empty( $type ) ) ? false : $type;
				if ( is_array( $type ) && 1 === count( $type ) ) {
					$type = $type[0];
				}

				$main_ingredient = wp_get_post_terms( $post_id, 'recipe_main_ingredient' );
				$main_ingredient = ( empty( $main_ingredient ) ) ? false : $main_ingredient;
				if ( is_array( $main_ingredient ) && 1 === count( $main_ingredient ) ) {
					$main_ingredient = $main_ingredient[0];
				}

				$thumbnail = get_post_thumbnail_id( $post_id );
				if ( empty( $thumbnail ) ) {
					$thumbnail = false;
				} else {
					$thumbnail = wp_get_attachment_image_src( $thumbnail, 'thumbnail' );
					if ( is_array( $thumbnail ) ) {
						$thumbnail = array(
							'src'    => $thumbnail[0],
							'width'  => $thumbnail[1],
							'height' => $thumbnail[2],
						);
					}
				}

				$return['recipes'][ $post_id ] = array(
					'ID'              => $post_id,
					'title'           => get_the_title( $post_id ),
					'desc'            => $desc,
					'type'            => $type,
					'main_ingredient' => $main_ingredient,
					'thumbnail'       => $thumbnail,
				);

			endwhile;

			wp_reset_postdata();

			// cache for 10 minutes
			set_transient( 'recipe_type_' . $term_id . '_recipes_list', $return, apply_filters( 'posts_ttl', 60 * 10 ) );

		endif;

	}

	$response = new \WP_REST_Response( $return );

	$response->header( 'Access-Control-Allow-Origin', apply_filters( 'access_control_allow_origin', '*' ) );

	return $response;
}

function recipe_details( $request ) {
	$post_id = (int) $request['id'];

	if ( 0 || false === ( $return = get_transient( 'recipe_' . $post_id . '_details' ) ) || IS_LOCAL ) {

		$desc = get_post_meta( $post_id, 'recipe_desc', true );
		$desc = ( empty( $desc ) ) ? false : $desc;

		$type = wp_get_post_terms( $post_id, 'recipe_type' );
		$type = ( empty( $type ) ) ? false : $type;
		if ( is_array( $type ) && 1 === count( $type ) ) {
			$type = $type[0];
		}

		$main_ingredient = wp_get_post_terms( $post_id, 'recipe_main_ingredient' );
		$main_ingredient = ( empty( $main_ingredient ) ) ? false : $main_ingredient;
		if ( is_array( $main_ingredient ) && 1 === count( $main_ingredient ) ) {
			$main_ingredient = $main_ingredient[0];
		}

		$thumbnail = get_post_thumbnail_id( $post_id );
		if ( empty( $thumbnail ) ) {
			$thumbnail = false;
		} else {
			$thumbnail = wp_get_attachment_image_src( $thumbnail, 'thumbnail' );
			if ( is_array( $thumbnail ) ) {
				$thumbnail = array(
					'src'    => $thumbnail[0],
					'width'  => $thumbnail[1],
					'height' => $thumbnail[2],
				);
			}
		}

		$return = array(
			'ID'              => $post_id,
			'title'           => get_the_title( $post_id ),
			'desc'            => $desc,
			'type'            => $type,
			'main_ingredient' => $main_ingredient,
			'thumbnail'       => $thumbnail,
		);

		$meta_fields = array(
			'desc',
			'prep_duration',
			'prep_instructions',
			'cooking_duration',
			'cooking_instructions',
			'source_url',
			'ingredients',
		);

		foreach ( $meta_fields as $meta_field ) {
			$value = get_post_meta( $post_id, 'recipe_' . $meta_field, true );
			
			$return[ $meta_field ] = ( empty( $value ) ) ? false : $value;
		}

		// cache for 10 minutes
		set_transient( 'recipe_' . $post_id . '_details', $return, apply_filters( 'posts_ttl', 60 * 10 ) );
	}

	$response = new \WP_REST_Response( $return );

	$response->header( 'Access-Control-Allow-Origin', apply_filters( 'access_control_allow_origin', '*' ) );

	return $response;
}