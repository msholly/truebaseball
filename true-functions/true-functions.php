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

// Composer
require __DIR__ . '/vendor/autoload.php';

// INIT TAXJAR 
require_once 'php/taxjar/taxjar.php';

// ADDITIONAL AWP FIELDS
require_once 'php/affiliate-wp/affiliate-wp.php';

// WOOCOMMERCE EXTENSIONS
require_once 'php/woocommerce/woocommerce.php';

// TRIBE EVENT EXTENSIONS
require_once 'php/tribe/events.php';

// Simple History Additional Functions
require_once 'php/simple-history/simple-history.php';

// Fitting Algo Integrations
require_once 'php/fitting/justBats.php';
require_once 'php/fitting/baseballMonkey.php';

// Bookings Extensions
require_once 'php/bookings/bookings.php';
require_once 'php/bookings/follow-up-emails.php';

// For ACF Debuggin - Disable for Performance
add_filter( 'acf/settings/remove_wp_meta_box', '__return_false' );

// Fix extra spaces in compact event list
if (is_single()) {
    remove_filter('the_content', 'wpautop');
}

// function true_woocommerce_after_checkout_form()
// {

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


// }
// add_action( 'woocommerce_checkout_before_customer_details', 'true_woocommerce_after_checkout_form' );
// add_action('cfw_checkout_before_form', 'true_woocommerce_after_checkout_form');
