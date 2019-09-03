<?php

require_once 'oliver-pos.php';

// Add a Woo Product Category on ticket save
add_action( 'event_tickets_after_save_ticket', 'tribe_events_add_product_category_to_tickets', 10, 4 );

function tribe_events_add_product_category_to_tickets( $event_id, $ticket, $raw_data, $classname ) {

    if ( ! empty( $ticket ) && isset( $ticket->ID ) ) {
        wp_add_object_terms( $ticket->ID, 'tickets', 'product_cat' );
    }

}


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

/**
 * Register new endpoint to use inside My Account page.
 *
 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
 */  
function true_add_events_endpoint() {
    add_rewrite_endpoint( 'events', EP_ROOT | EP_PAGES );
}
  
add_action( 'init', 'true_add_events_endpoint' );
  
  
/**
 * Add new query var.
 *
 * @param array $vars
 * @return array
 */  
function true_events_query_vars( $vars ) {
    $vars[] = 'events';
    return $vars;
}
  
add_filter( 'query_vars', 'true_events_query_vars', 0 );
  
  
/**
 * Custom help to add new items into an array after a selected item.
 *
 * @param array $items
 * @param array $new_items
 * @param string $after
 * @return array
 */
function true_insert_after_helper( $items, $new_items, $after ) {
	// Search for the item position and +1 since is after the selected item key.
	$position = array_search( $after, array_keys( $items ) ) + 1;

	// Insert the new item.
	$array = array_slice( $items, 0, $position, true );
	$array += $new_items;
	$array += array_slice( $items, $position, count( $items ) - $position, true );

    return $array;
}

/**
 * Insert the new endpoint into the My Account menu.
 *
 * @param array $items
 * @return array
 */
  
function true_add_events_link_my_account( $items ) {

    $new_items = array();
	$new_items['events'] = __( 'Events', 'woocommerce' );

	// Add the new item after `orders`.
	return true_insert_after_helper( $items, $new_items, 'orders' );
    
}
  
add_filter( 'woocommerce_account_menu_items', 'true_add_events_link_my_account' );
  
  
// ------------------
// 4. Add content to the new endpoint
  
function true_events_content() {
    $myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
    if ( $myaccount_page_id ) {
    $myaccount_orders_page_url = get_permalink( $myaccount_page_id ) . 'orders';
    }
echo '<h3>Your upcoming TRUE Fitting Events</h3><p>Note: You may have multiple tickets per event, and those will be available in the <a href="' . esc_url( $myaccount_orders_page_url ) . '">ORDERS</a> tab.</p>';
echo do_shortcode( ' [tribe-user-event-confirmations] ' );
}
  
add_action( 'woocommerce_account_events_endpoint', 'true_events_content' );
// Note: add_action must follow 'woocommerce_account_{your-endpoint-slug}_endpoint' format

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

        // Add shipping to notes
        $shipMethod = $order->get_shipping_method();

        if (!$shipMethod) {
            $shipMethod = 'N/A';
        }
        // GET AFWP COOKIE ID
        $affwp_ref = $_COOKIE['affwp_ref'];

        // Set Defaults
        $affiliate_login_name = 'N/A';
        $parent_mlm_login_name = 'N/A';
        $web_order_type = 'Online USD';
        $web_order_slug = 'ecomm';

        if( class_exists( 'Affiliate_WP' ) ) {
            if ($affwp_ref) {
                $user_id = affwp_get_affiliate_user_id( $affwp_ref );
                $affiliate_info = get_userdata($user_id);
                $affiliate_login_name = $affiliate_info->user_login;
                
                // MLM PARENT
                $parent_mlm_id = affwp_mlm_get_parent_affiliate($affwp_ref);
                $parent_mlm_info = get_userdata($parent_mlm_id);
                $parent_mlm_login_name = $parent_mlm_info->user_login;

                $web_order_type = 'Web Affiliate Program';
                $web_order_slug = 'web';
                update_field('affiliate', $user_id, $order_id);
                update_field('sales_rep', $parent_mlm_info->ID, $order_id);
            } 
        }
        
        // The text for the note
        // $note = __('TYPE: ' . $web_order_type . ' | SALESREP: ' . $parent_mlm_login_name . ' | AFFILIATE: ' . $affiliate_login_name . ' | SHIPPING: ' . $shipMethod );
        $note = __('TYPE: ' . $web_order_type . ' | SALESREP: ' . $parent_mlm_login_name . ' | AFFILIATE: ' . $affiliate_login_name );

        update_field('order_type', $web_order_slug, $order_id);
        
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


// define the woocommerce_cart_item_thumbnail callback 
function filter_woocommerce_cart_item_thumbnail( $product_get_image, $cart_item, $cart_item_key ) { 
    // make filter magic happen here... 
    
    if (strpos($product_get_image, 'placeholder') !== false) {
        return false;
    }
    return $product_get_image; 
}; 
         
// add the filter 
add_filter( 'woocommerce_cart_item_thumbnail', 'filter_woocommerce_cart_item_thumbnail', 10, 3 ); 


/**
 * @snippet       Hide one shipping option in one zone when Free Shipping is available
 * @how-to        Watch tutorial @ https://businessbloomer.com/?p=19055
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 3.6.3
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */
function true_unset_shipping_when_free_is_available_in_zone( $rates, $package ) {
    // Only unset rates if free_shipping is available
    if ( isset( $rates['free_shipping:3'] ) ) {
        unset( $rates['flat_rate:1'] );
    }     
        
    return $rates;
  
}

add_filter( 'woocommerce_package_rates', 'true_unset_shipping_when_free_is_available_in_zone', 10, 2 );
