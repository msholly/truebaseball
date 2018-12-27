<?php
/*
Plugin Name: AffiliateWP - Affiliate Landing Page Modification
Description: Modification in the Affiliate Landing Page add-on so that it is available in Event Calendar posts as well.
Version:     1.0.0
Author:      Wooninjas
Author URI:  http://www.wooninjas.com
License:     GPL2
Text Domain: awpalp
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Check required plugins
 */
function awpalp_required_plugins() {

    if ( ! is_plugin_active( 'affiliate-wp/affiliate-wp.php' ) ) {
    	deactivate_plugins ( plugin_basename ( __FILE__ ), true );
        $class = "notice is-dismissible error";
        $message = __( "AffiliateWP - Affiliate Landing Page Modification add-on requires Affiliate WP plugin to be activated.", "awpalp" );
        printf ( "<div id='message' class='%s'> <p>%s</p></div>", $class, $message );       
    }
    elseif ( ! is_plugin_active( 'affiliatewp-affiliate-landing-pages/affiliatewp-affiliate-landing-pages.php' ) ) {
        deactivate_plugins ( plugin_basename ( __FILE__ ), true );
        $class = "notice is-dismissible error";
        $message = __( "AffiliateWP - Affiliate Landing Page Modification add-on requires Affiliate Landing Page plugin to be activated.", "awpalp" );
        printf ( "<div id='message' class='%s'> <p>%s</p></div>", $class, $message );
    }

}
add_action( "admin_notices", "awpalp_required_plugins" );


/**
 * Check required plugins
 */
function list_landing_pages( $affiliate_id = 0 ) {
    
            // Remove the other action that list event-ticket as well as URLs.
            $affiliatewp_affiliate_landing_pages = affiliatewp_affiliate_landing_pages();
            remove_action( 'affwp_affiliate_dashboard_urls_top', array( $affiliatewp_affiliate_landing_pages, 'list_landing_pages' ) );
        
            $affiliate_user_name = affwp_get_affiliate_username( $affiliate_id );
            $landing_page_ids    = affwp_alp_get_landing_page_ids( $affiliate_user_name );

            $text = count( $landing_page_ids ) === 1 ? __( 'Your landing page:', 'affiliatewp-affiliate-landing-pages' ) : __( 'Your landing pages:', 'affiliatewp-affiliate-landing-pages' );
        ?>
            <?php if ( ! empty( $landing_page_ids ) ) : ?>
            <p><?php echo $text; ?></p>
            <p>
                <?php foreach ( $landing_page_ids as $id ) : 
                        if( get_post_type( $id ) == 'tribe_events' ) :
                ?>
                    <?php echo get_permalink( $id ); ?><br>
                <?php endif; 
                    endforeach; ?>
            </p>
            <?php endif; ?>

    <?php
}
add_action( 'affwp_affiliate_dashboard_urls_top', 'list_landing_pages', 9, 1 );

/**
 * Core plugin class
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cpt-metabox.php';

add_action( "plugins_loaded", "run_awpalp", 101 );


/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_awpalp() {

    if ( class_exists( 'AffiliateWP_Affiliate_Landing_Page_Metabox' ) ) {
       new AffiliateWP_Affiliate_Landing_Page_CPT_Metabox();
    } else {
        add_action( 'admin_notices', 'awpalp_required_plugins' );
    }
}