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


add_role(
    'affiliate',
    __( 'Affiliate' ),
    array(
        'read'         => true,  // true allows this capability
    )
);

add_role(
    'sales_rep',
    __( 'Sales Rep' ),
    array(
        'read'         => true,  // true allows this capability
    )
);

function pw_affwp_set_role_on_registration( $affiliate_id = 0 ) {
	$user_id = affwp_get_affiliate_user_id( $affiliate_id );
	$user = new WP_User( $user_id );
	$user->add_role( 'affiliate' );
}
add_action( 'affwp_insert_affiliate', 'pw_affwp_set_role_on_registration' );


// For ACF Debuggin
add_filter( 'acf/settings/remove_wp_meta_box', '__return_false' );


/*
 * Legacy API
 * Add a referanse field to the Order API response.
*/ 
function prefix_wc_api_order_response( $order ) {
    // Get the value
    $transaction_id = get_post_meta($order['id'], '_transaction_id', true);

	$true_meta_event = ( $value = get_field('order_type', $order['id']) ) ? $value : '';
	$true_meta_salesrep = ( $value = get_field('sales_rep', $order['id']) ) ? $value : '';
	$true_meta_affiliate = ( $value = get_field('affiliate', $order['id']) ) ? $value : '';

	$order['true_meta_event'] = $true_meta_event;
	$order['true_meta_salesrep'] = $true_meta_salesrep;
	$order['true_meta_affiliate'] = $true_meta_affiliate;
	$order['transaction_id'] = $transaction_id;

	return $order;
}
add_filter( 'woocommerce_api_order_response', 'prefix_wc_api_order_response', 10, 1 );


function add_affiliate_info_on_create_order ( $order_id ) {

    $order = new WC_Order( $order_id ); 

    // GET ORDER NOTE
    $customer_note = $order->get_customer_note();

    // IF OLIVER POS
    if (strpos($customer_note, 'POS') !== false) {
        // 
    }
    // ELSE WEB ORDER
    else {

        // GET AFWP COOKIE ID
        $affwp_ref = $_COOKIE['affwp_ref'];

        $user_id = affwp_get_affiliate_user_id( $affwp_ref );
        $affiliate_info = get_userdata($user_id);
        $affiliate_login_name = $affiliate_info->user_login;
        
        // The text for the note
        $note = __('TYPE: Web | SALESREP: none | AFFILIATE: ' . $affiliate_login_name );

        update_field('order_type', 'web', $order_id);
        update_field('affiliate', $user_id, $order_id);

        // update the customer_note on the order, the WP Post Excerpt
        $update_excerpt = array(
            'ID'             => $order_id,
            'post_excerpt'   => $note,
        );
        wp_update_post( $update_excerpt );

        // Add the note
        $order->add_order_note( $note );

        // Save the data
        $order->save();
    }
    
    
}
add_action( 'woocommerce_new_order', 'add_affiliate_info_on_create_order', 20 );


function add_affiliate_info_on_oliver_create_order ( $order_id ) {

    $order = new WC_Order( $order_id ); 

    // GET ORDER NOTE
    $customer_note = $order->get_customer_note();

    // IF OLIVER POS
    if (strpos($customer_note, 'POS') !== false) {

        // GET custom post meta, including new Oliver data
        $custom_fields = get_post_custom( $order_id );
        $event_type = $custom_fields['_order_oliverpos_tds_type'][0];
        $sales_rep_email = $custom_fields['_order_oliverpos_tds_salesrep_email'][0];
        $affiliate_wp_userid = $custom_fields['_order_oliverpos_tds_affiliate_email'][0];
        
        // Get user's full information
        // $user_id = affwp_get_affiliate_user_id( $affwp_ref ); // If getting affiliate ID (not with Oliver)
        $affiliate_info = get_userdata($affiliate_wp_userid);
        $affiliate_login_name = $affiliate_info->user_login;

        $sales_rep_info = get_user_by( 'email', $sales_rep_email ); 
        $sales_rep_login_name = $sales_rep_info->user_login;

        update_field('order_type', $event_type, $order_id);
        update_field('sales_rep', $sales_rep_info->ID, $order_id);
        update_field('affiliate', $affiliate_info->ID, $order_id);
        $note = __($customer_note . ' | TYPE: ' . $event_type . ' | SALESREP: ' . $sales_rep_login_name . ' | AFFILIATE: ' . $affiliate_login_name );

        // update the customer_note on the order, the WP Post Excerpt
        $update_excerpt = array(
            'ID'             => $order_id,
            'post_excerpt'   => $note,
        );
        wp_update_post( $update_excerpt );

        // Add the note
        $order->add_order_note( $note );

        // Save the data
        $order->save();
    }
    // ELSE WEB ORDER
    else {
        // do nothing
    }
    
    
}
add_action( 'woocommerce_order_status_completed', 'add_affiliate_info_on_oliver_create_order', 20 );

// function true_woocommerce_after_checkout_form () {
    // TESTING OBJECTS ONLY 

	// if(!isset($_COOKIE[$affwp_ref])) {
    //     echo "The cookie: '" . $_COOKIE[$affwp_ref] . "' is not set.";
    //     } else {
    //     echo "The cookie '" . $affwp_ref . "' is set.";
    //     echo "Value of cookie: " . $_COOKIE[$affwp_ref];
    //     }


    // $cookieValue = $_COOKIE['affwp_ref'];
    // echo "The cookie: '" . $cookieValue . "' is set.";

    // $affwp_ref = $_COOKIE['affwp_ref'];

    // $user_id = affwp_get_affiliate_user_id( $affwp_ref );
    // $affiliate_info = get_userdata($user_id);
    // $affiliate_login_name = $affiliate_info->user_login;
    // // ChromePhp::log($user_id);
    // echo "The cookie: '" . $affiliate_login_name . "' is set.";

    // ChromePhp::log($league);
    // $order = new WC_Order( $order_id ); 

    // $args = array(
    //     'created_via' => 'checkout',
    // );
    // $orders = wc_get_orders( $args );

    // $order_data = $order->get_meta();
    
    // // Oliver Custom Meta testing
    // $order_id = 2326;

    // $custom_fields = get_post_custom( $order_id );
    // $event_type = $custom_fields['_order_oliverpos_tds_type'][0];
    // $sales_rep_email = $custom_fields['_order_oliverpos_tds_salesrep_email'][0];
    // $affiliate_email = $custom_fields['_order_oliverpos_tds_affiliate_email'][0];
    // ChromePhp::log($event_type);
    // ChromePhp::log($sales_rep_email);
    // ChromePhp::log($affiliate_email);



// }
// add_action( 'woocommerce_checkout_before_customer_details', 'true_woocommerce_after_checkout_form' );
// add_action( 'cfw_checkout_before_form', 'true_woocommerce_after_checkout_form' );


?>