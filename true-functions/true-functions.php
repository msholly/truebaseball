<?php
/**
 * Plugin Name: TRUE Functions
 * Plugin URI: https://architkmedia.com
 * Description: Additional Functions for TRUE
 * Version: 1.0.0
 * Author: Mitchell Sholly
 * Author URI: http://mitchellsholly.com
 * License: GPL2
 */

// Debugging
// include 'php/ChromePhp.php';
// ChromePhp::log("TEST");

// DOTENV
require __DIR__ . '/vendor/autoload.php';
// $dotenv = Dotenv\Dotenv::create(__DIR__);
// $dotenv->load();

// GET FROM DB
/**
 * Retrieves a row from the database based on a given row ID.
 *
 * Corresponds to the value of $primary_key.
 *
 * @param  int                    $row_id Row ID.
 * @return array|null|object|void
 */
function get_awf_api( $row_id ) {
    global $wpdb;
    return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}affiliate_wp_rest_consumers WHERE user_id = %s LIMIT 1;", $row_id ) );
}

function true_get_awp_api_auth () {
    $awp_db = get_awf_api(1);

    $public_key = $awp_db->public_key;
    $token = $awp_db->token;

    return "Basic " . base64_encode( "{$public_key}:{$token}" );
}

// INIT TAXJAR 
require_once 'php/taxjar/taxjar.php';

// ADDITIONAL AWP FIELDS
require_once 'php/affiliate-wp/index.php';

// WOOCOMMERCE EXTENSIONS
require_once 'php/woocommerce/index.php';

// For ACF Debuggin
add_filter( 'acf/settings/remove_wp_meta_box', '__return_false' );

function true_woocommerce_after_checkout_form () {
    // OBJECT TESTING ONLY
    // $awp_db = get_awf_api(1);
    // $affiliateId = 8;
    // $parent_mlm = affwp_mlm_get_parent_affiliate($affiliateId);
    // ChromePhp::log($parent_mlm);

    // $order_id = 2484;
    // $order = new WC_Order( $order_id ); 
    // $shipMethod = $order->get_shipping_method();
    // ChromePhp::log($shipMethod);

    // $public_key = $awp_db->public_key;
    // $token = $awp_db->token;
    // ChromePhp::log($public_key);
    // $order_id = 2693;
    // $custom_fields = get_post_custom( $order_id );
    // $oliverData = $custom_fields['_order_oliverpos_extension_data'];
    //     $oliver_data_array=unserialize($oliverData[0]);
    //     ChromePhp::log($oliver_data_array['wordpress']);
    //     $event_type = $oliver_data_array['wordpress']['data']['customTags']['orderType'];
    //     ChromePhp::log($event_type);

    //     $sales_rep_id = $oliver_data_array['wordpress']['data']['customTags']['salesRep'];
    //     ChromePhp::log($sales_rep_id);

    //     $affiliate_wp_userid = $oliver_data_array['wordpress']['data']['customTags']['affiliateID'];
    //     ChromePhp::log($affiliate_wp_userid);


    //     // WORKING AUTO CHECK WHEN TICKET IS APPLIED
    //     $oliverTicketID = $oliver_data_array['wordpress']['data']['ticket']['ticketNumber'];
    //     ChromePhp::log($oliverTicketID);

    //     true_woo_ticket_checkin(2275);


}
// add_action( 'woocommerce_checkout_before_customer_details', 'true_woocommerce_after_checkout_form' );
add_action( 'cfw_checkout_before_form', 'true_woocommerce_after_checkout_form' );




?>