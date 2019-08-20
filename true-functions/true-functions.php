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

require_once 'php/affiliate-wp/add-address.php';
require_once 'php/affiliate-wp/add-city.php';
require_once 'php/affiliate-wp/add-state.php';
require_once 'php/affiliate-wp/add-zip.php';
require_once 'php/affiliate-wp/add-program-type.php';
require_once 'php/affiliate-wp/add-event-date.php';

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

// HIDE ADMIN BAR ON OLIVER POS
function my_theme_hide_admin_bar($bool) {
    if ( is_page_template( 'page-oliver-pos.php' ) ) :
        return false;
    else :
        return $bool;
    endif;
}
add_filter('show_admin_bar', 'my_theme_hide_admin_bar');

  
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

        // GET AFWP COOKIE ID
        $affwp_ref = $_COOKIE['affwp_ref'];

        $affiliate_login_name = 'N/A';

        if( class_exists( 'Affiliate_WP' ) ) {
            if ($affwp_ref) {
                $user_id = affwp_get_affiliate_user_id( $affwp_ref );
                $affiliate_info = get_userdata($user_id);
                $affiliate_login_name = $affiliate_info->user_login;
            }
        }
        
            
        // The text for the note
        $note = __('TYPE: Web | SALESREP: none | AFFILIATE: ' . $affiliate_login_name . ' | SHIPPING: ' . $shipMethod );

        update_field('order_type', 'web', $order_id);

        if ($user_id) {
            update_field('affiliate', $user_id, $order_id);
        }
        
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

        // Add shipping to notes
        $items = $order->get_items(); 

        foreach ( $order->get_items() as $item_id => $item ) {

            $lineItemId = $item->get_product_id();
            // ChromePhp::log($item->get_product_id());
            if ( $lineItemId == 2414 ) { // product id of Private 2 Day Ship
                $shipMethod = "2 Day Shipping";
            } else if ( $lineItemId == 2496 ) {
                $shipMethod = "Next Day Shipping";
            } else {
                $shipMethod = "Flat Rate";
            }

        }

        // GET custom post meta, including new Oliver data
        $custom_fields = get_post_custom( $order_id );
        $event_type = $custom_fields['_order_oliverpos_tds_type'][0];
        $sales_rep_email = $custom_fields['_order_oliverpos_tds_salesrep_email'][0];
        $affiliate_wp_userid = $custom_fields['_order_oliverpos_tds_affiliate_email'][0];

        // WORKING AUTO CHECK WHEN TICKET IS APPLIED
        // $oliverTicketID = $custom_fields['_order_oliverpos_tds_ticket'][0];
        // true_woo_ticket_checkin($oliverTicketID);

        // Get user's full information
        // $user_id = affwp_get_affiliate_user_id( $affwp_ref ); // If getting affiliate ID (not with Oliver)
        $affiliate_info = get_userdata($affiliate_wp_userid);
        $affiliate_login_name = $affiliate_info->user_login;

        $sales_rep_info = get_user_by( 'email', $sales_rep_email ); 
        $sales_rep_login_name = $sales_rep_info->user_login;

        update_field('order_type', $event_type, $order_id);
        update_field('sales_rep', $sales_rep_info->ID, $order_id);
        update_field('affiliate', $affiliate_info->ID, $order_id);
        $note = __($customer_note . ' | TYPE: ' . $event_type . ' | SALESREP: ' . $sales_rep_login_name . ' | AFFILIATE: ' . $affiliate_login_name . ' | SHIPPING: ' . $shipMethod);

        // update the customer_note on the order, the WP Post Excerpt
        $update_excerpt = array(
            'ID'             => $order_id,
            'post_excerpt'   => $note,
        );
        wp_update_post( $update_excerpt );

        // Add the note
        $order->add_order_note( $note );

        if ( !$order->has_shipping_address() ) {
            $order->set_shipping_first_name( $order->get_billing_first_name() );
            $order->set_shipping_last_name( $order->get_billing_last_name() );
            $order->set_shipping_company( $order->get_billing_company() );
            $order->set_shipping_address_1( $order->get_billing_address_1() );
            $order->set_shipping_address_2( $order->get_billing_address_2() );
            $order->set_shipping_city( $order->get_billing_city() );
            $order->set_shipping_state( $order->get_billing_state() );
            $order->set_shipping_postcode( $order->get_billing_postcode() );
            $order->set_shipping_country( $order->get_billing_country() );
        }

        // Save the data
        $order->save();
        // END ORDER SAVING


        $affiliate_payout = number_format(($order->get_subtotal() * .2));
        if ( strpos($_SERVER['HTTP_HOST'], 'local') ) {
            $post_url = 'https://true-diamond-science.local/wp-json/affwp/v1/referrals/';
            $ssl_verify = false;
		} else if ( strpos($_SERVER['HTTP_HOST'], 'flywheelstaging') ) {
            $post_url = 'http://staging.true-baseball.flywheelsites.com/wp-json/affwp/v1/referrals/';
            $ssl_verify = false;
		} else {
            $post_url = 'https://stage.truediamondscience.com/wp-json/affwp/v1/referrals/';
            $ssl_verify = true;
        }
        
        if ( $affiliate_info ) {
            // BEGIN AFFILAITE TRACKING
            $request_url = add_query_arg( 
                array( 
                    'user_id' => $affiliate_info->ID,
                    'amount' => $affiliate_payout,
                    'description' => rawurlencode($note),
                    'reference' => $order_id,
                    'context' => 'woocommerce',
                    'status' => 'unpaid'
                ), 
                $post_url 
            );
        
            $auth = true_get_awp_api_auth();
            // Send the request, storing the return in $response.<br>
            $response = wp_remote_post( $request_url, 
                array(
                    'headers' => array('Authorization' => $auth),
                    'sslverify' => $ssl_verify
                ) 
            );
        
            // Check for the requisite response code. If 201, retrieve the response body and continue.
            if ( 201 === wp_remote_retrieve_response_code( $response ) ) {
                $body = wp_remote_retrieve_body( $response );
                ChromePhp::log($body);
                error_log("REST WORKS!", 0);

            } else {
                // maybe display an error message
                ChromePhp::log("REST ERROR");
                error_log("REST ERROR Creating Referral from Oliver POS!", 0);
            }
        }
    }
    // ELSE WEB ORDER
    else {
        // do nothing
    }
    
    
}
add_action( 'woocommerce_order_status_completed', 'add_affiliate_info_on_oliver_create_order', 20 );

add_action( 'wp_ajax_nopriv_get_ticket_info', 'true_get_ticket_info' );
add_action( 'wp_ajax_get_ticket_info', 'true_get_ticket_info' );

function true_get_ticket_info() {
    $ticketOrderID = sanitize_text_field($_REQUEST['ticketOrderID']);
    $ticketID = sanitize_text_field($_REQUEST['ticketID']);

    // Woo Order Info
    $order = new WC_Order( $ticketOrderID ); 

    $ticketTotal = $order->get_total();
    $ticketOrderStatus = $order->get_status();

    // Tribe Ticket Information
    // $woo_tickets = TribeWooTickets::get_instance();
    $attendee_metadata = tribe_get_event_meta ( $ticketID );
    $attendee_info = tribe_tickets_get_attendees( $ticketID );
    $ticket_event = tribe_events_get_ticket_event( $ticketID );

    $event_ids = tribe_tickets_get_event_ids($ticketOrderID);
    $event_meta = tribe_events_get_event($event_ids[0]);
    $event_date = tribe_get_start_date($event_ids[0]);
    // foreach ( $ticket_ids as $ticket_id ) {
    //     $ticketTitle = get_the_title( $ticket_id );
    // }

    // ChromePhp::log($ticketID);
    // ChromePhp::log("true_get_ticket_info");
    $data = (object) [
        'ticketOrderID' => $ticketOrderID,
        'ticketTotal' => $ticketTotal,
        'ticketOrderStatus' => $ticketOrderStatus,
        'attendee_metadata' => $attendee_metadata,
        'attendee_info' => $attendee_info,
        'ticket_event' => $ticket_event,
        'event_meta' => $event_meta,
        'event_date' => $event_date
    ];

    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        wp_send_json($data);
		die();
	}
	else {
        // TO DO - BUILD FACETWP URL BASED ON PARAMS
		wp_redirect( get_permalink( $_REQUEST['post_id'] ) );
		exit();
    }


}


function true_woocommerce_after_checkout_form () {
    // ChromePhp::log("RUNNING");
    // $ch = curl_init();

    // curl_setopt($ch, CURLOPT_URL,            'https://calcconnect.vertexsmb.com/vertex-ws/services/CalculateTax70' );
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
    // curl_setopt($ch, CURLOPT_POST,           1 );
    // curl_setopt($ch, CURLOPT_POSTFIELDS,     '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">   <soapenv:Body xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">      <VertexEnvelope xmlns="urn:vertexinc:o-series:tps:7:0">         <Login>            <TrustedId>1347340878853852</TrustedId>         </Login>         <QuotationRequest transactionType="SALE" documentNumber="554739" postingDate="2019-07-29" documentDate="2019-07-29">            <Seller>               <Company>truesports</Company>              <PhysicalOrigin>                  <StreetAddress1>121 N SHIRK RD</StreetAddress1>                  <City>NEW HOLLAND</City>                  <MainDivision>PA</MainDivision>                  <PostalCode>17557-9714</PostalCode>                  <Country>USA</Country>               </PhysicalOrigin>            </Seller>            <Customer>               <CustomerCode>525098</CustomerCode>               <Destination>                  <MainDivision>pa</MainDivision>                  <PostalCode>19142</PostalCode>                  <Country>USA</Country>               </Destination>            </Customer>            <LineItem lineItemNumber="1">               <Product productClass="">bike</Product>               <Freight>10</Freight>               <Quantity unitOfMeasure="EA">1</Quantity>               <ExtendedPrice>1000</ExtendedPrice>            </LineItem>         </QuotationRequest>      </VertexEnvelope>   </soapenv:Body></soapenv:Envelope>' ); 
    // curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 

    // $result=curl_exec ($ch);
    // ChromePhp::log($result);

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

    // $args = array(
    //     'created_via' => 'checkout',
    // );
    // $orders = wc_get_orders( $args );

    // $order_data = $order->get_meta();
    ///////////////////
    // // Oliver Custom Meta testing
    // $order_id = 2317;
    // $affiliate_info = get_userdata(14);
    // $note = 'POS Checkout | league | shollycreativeinc | ';

    // $order = new WC_Order( $order_id ); 
    /////////////////

    // $line_items = $order->get_items();
    // $event_type = $custom_fields['_order_oliverpos_tds_type'][0];
    // $sales_rep_email = $custom_fields['_order_oliverpos_tds_salesrep_email'][0];
    // $affiliate_email = $custom_fields['_order_oliverpos_tds_affiliate_email'][0];
    // $affiliate_payout = number_format(($order->get_subtotal() * .2));
    // ChromePhp::log($affiliate_payout);

    // TO DO FOR EACH PRODUCT PAYOUT AMOUNT
    // foreach ($order->get_items() as $item_id => $item_data) {

    //     // Get an instance of corresponding the WC_Product object
    //     $product = $item_data->get_product();
    //     // $product_cats = get_the_terms( $product, 'product_cat' );

    //     $product_name = $product->get_name(); // Get the product name
    
    //     $item_quantity = $item_data->get_quantity(); // Get the item quantity
    
    //     $item_price = $product->get_price(); // Get the item line price
    
    //     // Displaying this data (to check)
    //     echo 'Product name: '.$product_name.' | Quantity: '.$item_quantity.' | Item total: '. number_format( $item_price, 2 );
    // }
    
    // ChromePhp::log($sales_rep_email);
    // ChromePhp::log($affiliate_email);

    // ChromePhp::log("MAKE REFERRAL");
    ///////////////////////////
    // if ( strpos($_SERVER['HTTP_HOST'], 'local') ) {
    //     $post_url = 'https://true-diamond-science.local/wp-json/affwp/v1/referrals/';
    //     $ssl_verify = false;
    // } else if ( strpos($_SERVER['HTTP_HOST'], 'flywheelstaging') ) {
    //     $post_url = 'http://staging.true-baseball.flywheelsites.com/wp-json/affwp/v1/referrals/';
    //     $ssl_verify = false;
    // } else {
    //     $post_url = 'https://stage.truediamondscience.com/wp-json/affwp/v1/referrals/';
    //     $ssl_verify = true;
    // }

    // $request_url = add_query_arg( 
    //     array( 
    //         'user_id' => $affiliate_info->ID,
    //         'amount' => $affiliate_payout,
    //         'description' => rawurlencode($note),
    //         'reference' => $order_id,
    //         'context' => 'pos',
    //         'status' => 'unpaid'
    //     ), 
    //     $post_url
    // );

    // $auth = get_awp_api_auth();
    // // Send the request, storing the return in $response.
    // $response = wp_remote_post( $request_url, 
    //     array(
    //         'headers' => array('Authorization' => $auth),
    //         'sslverify' => $ssl_verify
    //     ) 
    // );

    // // Check for the requisite response code. If 201, retrieve the response body and continue.
    // if ( 201 === wp_remote_retrieve_response_code( $response ) ) {
    //     $body = wp_remote_retrieve_body( $response );
    //     ChromePhp::log($body);
    // } else {
    //     // maybe display an error message
        // ChromePhp::log("REST ERROR");
    //     $error_message = $response->get_error_message();
    //     ChromePhp::log($error_message);
    // }
    // $playerHeight = 36;
    // $playerWeight = 38;
    // truefit_tball_length($playerHeight,$playerWeight);



    // $order_id = 2475;
    // $order = new WC_Order( $order_id ); 
    // $items = $order->get_items(); 

    // // $order->get_items();
    // foreach ( $order->get_items() as $item_id => $item ) {
    //     $lineItemId = $item->get_product_id();

    //     if ( $lineItemId == 2414 ) {
    //         $set2DayShipMethod = true;
    //     }
        



    //     // Here you get your data
    //     // $custom_field = wc_get_order_item_meta( $item_id, '_tmcartepo_data', true ); 
    
    //     // To test data output (uncomment the line below)
    //     // print_r($custom_field);
    
    //     // If it is an array of values
    //     // if( is_array( $custom_field ) ){
    //     //     echo implode( '<br>', $custom_field ); // one value displayed by line 
    //     // } 
    //     // just one value (a string)
    //     // else {
    //     //     echo $custom_field;
    //     // }
    // }

    // if ($set2DayShipMethod) {
    //     ChromePhp::log($lineItemId);
    //     // $item = new WC_Order_Item_Shipping();
    //     // // $new_ship_price = 45; // Don't set price, becuase we don't want to affect overall cart totals

    //     // $item->set_method_title( "2 Day Shipping" );
    //     // $item->set_method_id( "flat_rate:5" ); // set an existing Shipping method rate ID
    //     // // $item->set_total( $new_ship_price ); // (optional)

    //     // $order->add_item( $item );
    // }
    $order_id = 2689;
    $custom_fields = get_post_custom( $order_id );
    $oliverData = $custom_fields['_order_oliverpos_extension_data'];
    ChromePhp::log($oliverData);


}
// add_action( 'woocommerce_checkout_before_customer_details', 'true_woocommerce_after_checkout_form' );
add_action( 'cfw_checkout_before_form', 'true_woocommerce_after_checkout_form' );


function true_woo_ticket_checkin ( $attendee_id ) {

	// bail if event tickets plus is not active
	if ( !class_exists('Tribe__Tickets_Plus__Commerce__WooCommerce__Main') ) return false;

	$tickets_provider = Tribe__Tickets_Plus__Commerce__WooCommerce__Main::get_instance();

	return $tickets_provider->checkin ( $attendee_id );

}


?>