<?php
/**
 * Plugin Name: TRUE Custom Modules
 * Plugin URI: http://www.truediamondscience.com
 * Description: Custom modules for the Beaver Builder Plugin.
 * Version: 1.0
 * Author: Mitchell Sholly
 * Author URI: http://architkmedia.com
 */
 
define( 'BB_TRUE_DIR', plugin_dir_path( __FILE__ ) );
define( 'BB_TRUE_URL', plugins_url( '/', __FILE__ ) );
function true_load_module() {
    if ( class_exists( 'FLBuilder' ) ) {
        // Include your custom modules here.
        require_once 'classes/class-fl-page-data-woocommerce.php';
        require_once 'modules/true-technical/true-technical.php';
    }
}
add_action( 'init', 'true_load_module' );

// add_action( 'fl_page_data_add_properties', function() {
//     require_once 'classes/class-fl-page-data-woocommerce.php';
// } );

