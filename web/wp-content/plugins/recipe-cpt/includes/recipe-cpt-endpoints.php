<?php
namespace hazels_heritage\recipe_endpoints;

/**
 * Disable default REST API endpoints
 * This is a read-only API and makes
 * no use of anything other than the
 * endpoints below
 */
function disable_default_rest_routes() {
	remove_filter( 'rest_api_init', 'create_initial_rest_routes' );
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\disable_default_rest_routes' );

/**
 * Adds access/cache headers to a REST API response object
 *
 * @param $response object REST API response object
 *
 * @return object $response
 */
function add_access_cache_headers( $response ) {

	$response->header( 'Access-Control-Allow-Origin', apply_filters( 'access_control_allow_origin', '*' ) );

	/*
	 * The goal is to ensure that API responses will reach clients in a timely manner,
	 * but we also want to ensure that clients always have the most up-to-date information.
	 * The first constraint can be solved by using the surrogate-control header, and the
	 * second constraint can be solved by using the cache-control header:
	 *
	 * These headers tell the CDN that it is allowed to cache the content for up to one day.
	 * In addition, the headers tell the client that it is allowed to cache the content for 60 seconds,
	 * and that it should go back to its source of truth (the CDN) after 60 seconds.
	 */

	$response->header( 'Cache-Control', 'max-age=' . apply_filters( 'api_max_age', WEEK_IN_SECONDS ) );

	return $response;
}

/**
 * Register recipe REST endpoints
 */
function register_api_hooks() {
	$namespace = 'recipes/v1';

	register_rest_route( $namespace, '/recipes/', array(
		'methods'  => 'GET',
		'callback' => __NAMESPACE__ . '\list_recipes',
		'args'     => array(
			'query_args' => array(
				'default' => array(),
			),
		),
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

function list_recipes( $request ) {

	$args = $request['query_args'];

	$recipe_args = array(
		'post_type'           => 'recipe',
		'post_status'         => 'publish',
		'posts_per_page'      => 25,
		'ignore_sticky_posts' => true,
		'tax_query'           => array(),
	);

	$standard_params = array(
		'order',
		'orderby',
		'author',
		'post_type',
		'ignore_sticky_posts',
		'paged',
		'page',
		'nopaging',
		'posts_per_page',
		's',
	);

	foreach ( $standard_params as $standard_param ) {
		if ( isset( $args[ $standard_param ] ) && ! empty( $args[ $standard_param ] ) ) {
			$recipe_args[ $standard_param ] = $args[ $standard_param ];
		}
	}

	if ( isset( $args['main_ingredient'] ) && ! empty( $args['main_ingredient'] ) ) {
		$recipe_args['tax_query'][] = array(
			'taxonomy' => 'recipe_main_ingredient',
			'field'    => 'term_id',
			'terms'    => ( is_array( $args['main_ingredient'] ) ) ? $args['main_ingredient'] : (int) $args['main_ingredient'],
		);
	}

	if ( isset( $args['recipe_type'] ) && ! empty( $args['recipe_type'] ) ) {
		$recipe_args['tax_query'][] = array(
			'taxonomy' => 'recipe_type',
			'field'    => 'term_id',
			'terms'    => ( is_array( $args['recipe_type'] ) ) ? $args['recipe_type'] : (int) $args['recipe_type'],
		);
	}

	if ( ! empty( $recipe_args['tax_query'] ) && count( $recipe_args['tax_query'] ) > 1 ) {
		$recipe_args['tax_query']['relation'] = 'AND';
	} elseif ( empty( $recipe_args['tax_query'] ) ) {
		unset( $recipe_args['tax_query'] );
	}

	$return = array(
		'total'      => 0,
		'count'      => 0,
		'recipes'    => array(),
		'posts_per_page'   => $recipe_args['posts_per_page'],
		'query_args' => $recipe_args,
	);

	$the_query = new \WP_Query( $recipe_args );

	if ( $the_query->have_posts() ):

		$return['total'] = (int) $the_query->found_posts;
		$return['count'] = (int) $the_query->post_count;
		$i               = 1;

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

			$return['recipes'][ $i ] = array(
				'ID'              => $post_id,
				'title'           => get_the_title( $post_id ),
				'desc'            => $desc,
				'type'            => $type,
				'main_ingredient' => $main_ingredient,
				'thumbnail'       => $thumbnail,
			);

			$i ++;

		endwhile;

		wp_reset_postdata();

	endif;


	$response = new \WP_REST_Response( $return );
	$response = add_access_cache_headers( $response );

	return $response;
}

function list_main_ingredients() {

		$return = array(
			'nice_name' => 'Main Ingredients',
			'slug'      => 'main_ingredient',
			'total'     => 0,
			'values'    => array(),
		);

		$args = array(
			'taxonomy' => 'recipe_main_ingredient',
			'orderby'  => 'name',
			'order'    => 'ASC',
			'number'   => 0,
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

				$return['values'][ $term->term_id ] = $term_details;

			endforeach;

		endif;
	$response = new \WP_REST_Response( $return );
	$response = add_access_cache_headers( $response );

	return $response;
}

function list_recipe_types() {

		$return = array(
			'nice_name' => 'Type',
			'slug'      => 'recipe_type',
			'total'        => 0,
			'values' => array(),
		);

		$args = array(
			'taxonomy' => 'recipe_type',
			'orderby'  => 'name',
			'order'    => 'ASC',
			'number'   => 0,
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

				$return['values'][ $term->term_id ] = $term_details;

			endforeach;

		endif;

	$response = new \WP_REST_Response( $return );
	$response = add_access_cache_headers( $response );

	return $response;
}

function list_main_ingredient_recipes( $request ) {

	$term_id = (int) $request['id'];

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

			$i = 1;

			while ( $the_query->have_posts() ):
				$the_query->the_post();
				$post_id = get_the_ID();

				$desc = get_post_meta( $post_id, 'recipe_desc', true );
				$desc = ( empty( $desc ) ) ? false : $desc;

				$type = get_the_terms( $post_id, 'recipe_type' );
				$type = ( empty( $type ) ) ? false : $type;
				if ( is_array( $type ) && 1 === count( $type ) ) {
					$type = $type[0];
				}

				$main_ingredient = get_the_terms( $post_id, 'recipe_main_ingredient' );
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

				$return['recipes'][ $i ] = array(
					'ID'              => $post_id,
					'title'           => get_the_title( $post_id ),
					'desc'            => $desc,
					'type'            => $type,
					'main_ingredient' => $main_ingredient,
					'thumbnail'       => $thumbnail,
				);

				$i ++;

			endwhile;

			wp_reset_postdata();

		endif;

	$response = new \WP_REST_Response( $return );
	$response = add_access_cache_headers( $response );

	return $response;
}

function list_recipe_type_recipes( $request ) {

	$term_id = (int) $request['id'];

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

			$i = 1;

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

				$return['recipes'][ $i ] = array(
					'ID'              => $post_id,
					'title'           => get_the_title( $post_id ),
					'desc'            => $desc,
					'type'            => $type,
					'main_ingredient' => $main_ingredient,
					'thumbnail'       => $thumbnail,
				);

				$i ++;

			endwhile;

			wp_reset_postdata();

		endif;

	$response = new \WP_REST_Response( $return );
	$response = add_access_cache_headers( $response );

	return $response;
}

function recipe_details( $request ) {
	$post_id = (int) $request['id'];

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

	$response = new \WP_REST_Response( $return );
	$response = add_access_cache_headers( $response );

	return $response;
}
