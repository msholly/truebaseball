<?php
/**
 * Adds the metabox to Events Calendar Page
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AffiliateWP Affiliate Landing Pages Metabox Class
 *
 * @since 1.0
 */
class AffiliateWP_Affiliate_Landing_Page_CPT_Metabox {

    public function __construct() {

        // Add metabox.
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

    }

    /**
     * Register the meta box for Events Calendar CPT
     *
     * @since  1.0
     * @return void
     */
    public function add_meta_box() {        

        $metabox = new AffiliateWP_Affiliate_Landing_Page_Metabox;          
        if ( true === affwp_alp_is_enabled() ) {
            add_meta_box( 'affwp_alp_settings', __( 'Affiliate Landing Pages', 'affiliatewp-affiliate-landing-pages' ), array( $metabox, 'meta_box' ), array( 'tribe_events' ), 'side', 'default' );
        }
    }
}