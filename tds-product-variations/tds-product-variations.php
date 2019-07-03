<?php
/**
 * Plugin Name: True Diamond Science - Product Variations
 * Description: Integrate FacetWP, Beaver Builder and Product Variations
 * Version:     1.0.1
 * Author:      Felipe Elia | Codeable
 * Author URI:  https://codeable.io/developers/felipe-elia?ref=qGTOJ
 * Text Domain: tds-product-variations
 * Domain Path: /languages
 *
 * @package TDS_Product_Variations
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class
 */
class TDS_Product_Variations {
	/**
	 * The only class instance.
	 *
	 * @var TDS_Product_Variations
	 */
	protected static $instance = null;

	/**
	 * Stores the Post IDs got to bypass WooCommerce integration.
	 *
	 * @var array|null
	 */
	private $_post_ids;

	/**
	 * Constructor. Executed only once (singleton).
	 */
	private function __construct() {
		add_filter( 'fl_builder_loop_query_args', [ $this, 'facet_add_post_type' ] );
	}

	/**
	 * Add `product_variation` as a possible post_type when searching for products.
	 *
	 * @param array $args Query arguments.
	 * @return array
	 */
	public function facet_add_post_type( $args ) {
		if ( ! empty( $args['facetwp'] ) && 'product' === $args['post_type'] ) {
			$args['post_type'] = [ 'product_variation', 'product' ];
			add_filter( 'facetwp_filtered_post_ids', [ $this, 'filtered_post_ids_backup' ], 1, 3 );
			add_filter( 'facetwp_filtered_post_ids', [ $this, 'filtered_post_ids_restore' ], 11, 3 );
		} else {
			remove_filter( 'facetwp_filtered_post_ids', 'tds_product_variations_facetwp_post_id' );
		}
		return $args;
	}


	/**
	 * Add the product variation IDs to the results and stores it.
	 *
	 * @param array            $post_ids         Post IDs found so far.
	 * @param FacetWP_Renderer $facetwp_renderer The FacetWP renderer object.
	 * @return array
	 */
	function filtered_post_ids_backup( $post_ids, $facetwp_renderer ) {
		if ( ! empty( $facetwp_renderer->facets ) ) {
			$woo_integration = new FacetWP_Integration_WooCommerce();
			$this->_post_ids = $woo_integration->process_variations( $post_ids );
			return $tds_prod_variations_post_ids;
		}
		return $tds_prod_variations_post_ids;
	}

	/**
	 * Restore the IDs got in `filtered_post_ids_backup()`.
	 *
	 * @param array            $post_ids         Post IDs found so far.
	 * @param FacetWP_Renderer $facetwp_renderer The FacetWP renderer object.
	 * @return array
	 */
	function filtered_post_ids_restore( $post_ids, $facetwp_renderer ) {
		$post_ids = (array) $this->_post_ids;

		if ( class_exists( 'Iconic_WSSV_Product' ) ) {
			foreach ( $post_ids as $index => $post_id ) {
				$visibility_terms = Iconic_WSSV_Product::get_visibility_term_slugs( $post_id );

				if ( in_array( 'exclude-from-filtered', $visibility_terms ) ) {
					unset( $post_ids[ $index ] );
				}
			}
		}

		return $post_ids;
	}


	/**
	 * SINGLETON. Return the only class instance.
	 *
	 * @return Class Name
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}

add_action( 'init', [ 'TDS_Product_Variations', 'get_instance' ] );
