<?php
/**
 * Plugin Name:       		Oliver Extension Demo Plugin - A WooCommerce Point of Sale (POS)
 * Description:       		Oliver POS with True Diamond extension.
 * Version:           		1.0.0
 * Author:            		Oliver POS
 * Author URI:        		https://oliverpos.com/
 * License:           		GPL-2.0+
 * License URI:       		http://www.gnu.org/licenses/gpl-2.0.txt
 * WC requires at least:	3.5.1
 * WC tested up to:			3.6.5
 */

// Define plugin file.
if ( ! defined( 'OLIVER_EXTENSION_DEMO_PLUGIN' ) ) {
  define( 'OLIVER_EXTENSION_DEMO_PLUGIN', __FILE__ );
}

// plugin activation process
register_activation_hook( OLIVER_EXTENSION_DEMO_PLUGIN, function(){
	$post_id = wp_insert_post(array(
	  'post_title'    => 'optd',
	  'post_content'  => 'Content of your page',
	  'post_status'   => 'publish',
	  'post_author'   => get_current_user_id(),
	  'post_type' 	  => 'page'
   	));

   	if (!$post_id) {
        wp_die('Error creating template page');
    } else {
    	// for add template to page
        update_post_meta($post_id, '_wp_page_template', 'tp-file.php');
    }

	// developer can perform more events
});


// plugin activation process
register_deactivation_hook( OLIVER_EXTENSION_DEMO_PLUGIN, function(){
	wp_delete_post(get_page_by_title( 'optd' )->ID, true);
	// deactivation event
});

// load html/form page
if (isset($_GET['oliver-extension-true-diamond'])) {
	require_once 'oliver-extension-true-diamond.php';
	exit;
}

if (isset($_GET['oliver-extension-custom-fee'])) {
	require_once 'oliver-extension-custom-fee-backup.php';
	exit;
}

if (isset($_GET['oliver-extension-tax'])) {
	require_once 'oliver-extension-tax-backup.php';
	exit;
}

if (isset($_GET['oliver-extension-true-diamond-mockup'])) {
	require_once 'oliver-extension-true-diamond-mockup.php';
	exit;
}

if (isset($_GET['oliver-extension-true-diamond-inherit'])) {
	require_once 'oliver-extension-true-diamond-with-inherited-design.php';
	exit;
}

// ======================= Action & Filters ============================

// this action enquue jquery to plugin
add_action( 'admin_enqueue_scripts', function(){
	wp_enqueue_script('jquery');
});


// for create a custom template
add_filter( 'page_template', function($template){
	// here we are register our page in wordpress
	// tp-file.php is used for custom page template that means we can remove header and footer of wordpress
	if( 'tp-file.php' == basename( $template ) )
        $template = WP_PLUGIN_DIR . '/oliver-extension-demo-plugin/tp-file.php';
    return $template;
});

// this action enquue jquery to plugin
add_action( 'tds_neworder', function($data){
	// we are saving the data into wordpress option table in column oliver_tds_neworder
	update_option("oliver_tds_neworder", $data);

	/* 
	 * we get data by get_option("oliver_tds_neworder");
	 * But due to wordpress ethics data array saved in serialize form so we need to unserialze the data
	 * i am using this tool for unserialize aaray online https://www.unserialize.com/
	 */
});

// ======================= Action & Filters ============================