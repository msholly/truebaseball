<?php


function add_affiliate_info_on_oliver_create_order ( $order_id ) {

    $order = new WC_Order( $order_id ); 

    // GET ORDER NOTE
    $customer_note = $order->get_customer_note();

    // IF OLIVER POS
    if (strpos($customer_note, 'POS') !== false) {

        // SET DEFAULTS
        // $items = $order->get_items(); might not need this
        $shipMethod = 'Free Ground Shipping';
        $affiliate_email = 'N/A';
        $affwp_amount = 0;
        $ticketID = 'N/A';

        foreach ( $order->get_items() as $item_id => $item ) {

            $lineItemId = $item->get_product_id();

            // GET Categories
            $term_list = wp_get_post_terms($lineItemId, 'product_cat', array('fields'=>'ids'));
            $cat_id = (int)$term_list[0];

            if ($cat_id == 315) {
                // DETECT SHIPPING 
                // No Affiliate total for shipping
                if ($lineItemId == 2414) { // product id of Private 2 Day Ship
                    $shipMethod = '2 Day Shipping';
                }
                if ($lineItemId == 2496) {
                    $shipMethod = 'Next Day Shipping';
                }   
            }
    
            if ($cat_id == 41) {
                // DETECT BATS 10% 
                // 15% but that's TO DO
                $affwp_amount += number_format(($item->get_total() * .1));
            }
                
            if ($cat_id == 315) {
                // FITTING PRODUCTS 5%
                $affwp_amount += number_format(($item->get_total() * .05));
            }
    
            if ($cat_id == 361) {
                // GEAR PRODUCTS 5%
                $affwp_amount += number_format(($item->get_total() * .05));
            }

        }

        // GET custom post meta, including new Oliver data
        $custom_fields = get_post_custom( $order_id );
        $oliverData = $custom_fields['_order_oliverpos_extension_data'];
        $oliver_data_array=unserialize($oliverData[0]);

        $event_type = $oliver_data_array['wordpress']['data']['customTags']['orderType'];
        $sales_rep_id = $oliver_data_array['wordpress']['data']['customTags']['salesRep'];
        $affiliate_wp_userid = $oliver_data_array['wordpress']['data']['customTags']['affiliateID'];
        $shipTo = $oliver_data_array['wordpress']['data']['customTags']['shipTo'];
        
        // WORKING AUTO CHECK WHEN TICKET IS APPLIED
        $oliverTicketID = $oliver_data_array['wordpress']['data']['ticket']['ticketNumber'];
        $oliverTicketOrderID = $oliver_data_array['wordpress']['data']['ticket']['ticketOrderID'];

        // Get user's full information
        // $user_id = affwp_get_affiliate_user_id( $affwp_ref ); // If getting affiliate ID (not with Oliver)
        if ($affiliate_wp_userid != "N/A") {
            $affiliate_info = get_userdata($affiliate_wp_userid);
            $affiliate_email = $affiliate_info->user_email;
        }

        if ($oliverTicketOrderID) {
            true_woo_ticket_checkin($oliverTicketID);
            $ticketID = $oliverTicketOrderID;
        }

        $sales_rep_info = get_user_by( 'ID', $sales_rep_id ); 
        $sales_rep_email = $sales_rep_info->user_email;

        update_field('order_type', $event_type, $order_id);
        update_field('sales_rep', $sales_rep_info->ID, $order_id);
        update_field('affiliate', $affiliate_info->ID, $order_id);
        update_field('event_ticket', $oliverTicketID, $order_id);
        $note = __($customer_note . ' | TYPE: ' . $event_type . ' | SALESREP: ' . $sales_rep_email . ' | AFFILIATE: ' . $affiliate_email . ' | SHIPPING: ' . $shipMethod. ' | TICKET ORDER ID: ' . $ticketID);

        // update the customer_note on the order, the WP Post Excerpt
        $update_excerpt = array(
            'ID'             => $order_id,
            'post_excerpt'   => $note,
        );
        wp_update_post( $update_excerpt );

        // Add the note
        $order->add_order_note( $note );

        $cust_first_name = $order->get_billing_first_name();
        $cust_last_name = $order->get_billing_last_name();

        if (!$cust_first_name) {
            $cust_first_name = get_user_meta( $order->customer_id, 'first_name', true );
            $cust_last_name = get_user_meta( $order->customer_id, 'last_name', true );
            $order->set_billing_first_name( $cust_first_name );
            $order->set_billing_last_name( $cust_last_name );
        }

        // Set shipping address based on expected from POS UI
        $order->set_shipping_first_name( $cust_first_name );
        $order->set_shipping_last_name( $cust_last_name );
        $order->set_shipping_company( $order->get_billing_company() );
        $order->set_shipping_address_1( $shipTo['shipping_address_1'] );
        $order->set_shipping_address_2( $shipTo['shipping_address_2'] );
        $order->set_shipping_city( $shipTo['shipping_city'] );
        $order->set_shipping_state( $shipTo['shipping_state'] );
        $order->set_shipping_postcode( $shipTo['shipping_postcode'] );
        $order->set_shipping_country( $shipTo['shipping_country'] );

        // Save the data
        $order->save();
        // END ORDER SAVING

        if ( $affiliate_info ) {

            if ( strpos($_SERVER['HTTP_HOST'], 'local') ) {
                $post_url = 'https://true-diamond-science.local/wp-json/affwp/v1/referrals/';
                $ssl_verify = false;
            } else if ( strpos($_SERVER['HTTP_HOST'], 'flywheelstaging') ) {
                $post_url = 'http://staging.true-baseball.flywheelsites.com/wp-json/affwp/v1/referrals/';
                $ssl_verify = false;
            } else {
                $post_url = 'https://truediamondscience.com/wp-json/affwp/v1/referrals/';
                $ssl_verify = true;
            }

            // BEGIN AFFILAITE TRACKING
            $request_url = add_query_arg( 
                array( 
                    'user_id' => $affiliate_info->ID,
                    'amount' => $affwp_amount,
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
                error_log('REST WORKS!', 0);

            } else {
                // maybe display an error message
                ChromePhp::log('REST ERROR');
                error_log('REST ERROR Creating Referral from Oliver POS!', 0);
            }
        }
    }
    // ELSE WEB ORDER
    else {
        // do nothing
    }
    
    
}
add_action( 'woocommerce_order_status_completed', 'add_affiliate_info_on_oliver_create_order', 20 );

// add_action( 'wp_ajax_nopriv_get_ticket_info', 'true_get_ticket_info' );
add_action( 'wp_ajax_get_ticket_info', 'true_get_ticket_info' );

function true_get_ticket_info() {

    if( isset( $_GET['true_pos_nonce'] ) && wp_verify_nonce( $_GET['true_pos_nonce'], 'true_pos_form_nonce') ) {

        $ticketOrderID = sanitize_text_field($_GET['ticketOrderID']);
        $ticketID = sanitize_text_field($_GET['ticketID']);

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
        // ChromePhp::log('true_get_ticket_info');
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
        
    } else {
        ChromePhp::log("Bad Nonce");
        error_log("Bad Oliver Ticket UI Nonce.", 0);
        wp_die( __( 'Invalid nonce specified', 'TRUE Functions' ), __( 'Error', 'TRUE Functions' ), array(
            'response' 	=> 403,
            'back_link' => 'admin.php?page=' . 'TRUE Functions',
        ) );
    }
    
}

function true_woo_ticket_checkin ( $attendee_id ) {
	// bail if event tickets plus is not active
	if ( !class_exists('Tribe__Tickets_Plus__Commerce__WooCommerce__Main') ) return false;

	$tickets_provider = Tribe__Tickets_Plus__Commerce__WooCommerce__Main::get_instance();

	return $tickets_provider->checkin ( $attendee_id );

}

// HIDE ADMIN BAR ON OLIVER POS
function my_theme_hide_admin_bar($bool) {
    if ( is_page_template( 'page-oliver-pos.php' ) ) :
        return false;
    else :
        return $bool;
    endif;
}
add_filter('show_admin_bar', 'my_theme_hide_admin_bar');
