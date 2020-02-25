<?php

add_action( 'fue_before_variable_replacements', 'fue_register_venue', 10, 4);
function fue_register_venue( $var, $email_data, $fue_email, $queue_item ) { 
    $variables = array(
        'venue_address_line_one'	=> '',
        'venue_address_line_two'	=> '',
        'venue_address_full'	    => '',
        'venue_address_directions'  => '',
        'venue_phone'               => ''
	);

    if ( isset( $email_data['test'] ) && $email_data['test'] ) {
        $product_id = $queue_item->product_id;

        if ( strpos($_SERVER['HTTP_HOST'], 'local') ) {
            $product_id = 17794; // test
        } else if ( strpos($_SERVER['HTTP_HOST'], 'flywheelstaging') ) {
            $product_id = 19381; // test
        } else {
            $product_id = 19381; // test
        }
        
        $venue_nonformatted = true_get_booking_venue($product_id, null);
        $variables['venue_address_full'] = $venue_nonformatted['address'];
        $variables['venue_phone'] = $venue_nonformatted['phone'];

        $variables['venue_address_line_one'] = true_get_booking_venue($product_id, array('street_number', 'street_name'))['address'];
        $variables['venue_address_line_two'] = true_get_booking_venue($product_id, array('city', 'state_short', 'post_code'))['address'];
        $variables['venue_address_directions'] = true_get_booking_venue($product_id, array('address'))['address'];
        // if ( !empty( $email_data['order_id'] ) ) {
        //     $order = wc_get_order( $email_data['order_id'] );
        //     $referral_code = get_user_meta( $order->customer_user, '_affiliate_key', true );

        //     if ( !empty( $referral_code ) ) {
        //         $variables['referral_code'] = $referral_code;
        //     }
        // }
    } else {
        if ( !empty( $queue_item->order_id ) ) {
            $product_id = $queue_item->product_id;

            $venue_nonformatted = true_get_booking_venue($product_id, null);
            $variables['venue_address_full'] = $venue_nonformatted['address'];
            $variables['venue_phone'] = $venue_nonformatted['phone'];
    
            $variables['venue_address_line_one'] = true_get_booking_venue($product_id, array('street_number', 'street_name'))['address'];
            $variables['venue_address_line_two'] = true_get_booking_venue($product_id, array('city', 'state_short', 'post_code'))['address'];
            $variables['venue_address_directions'] = true_get_booking_venue($product_id, array('address'))['address'];
        }
    }

    $var->register( $variables );
}

add_action( 'fue_before_variable_replacements', 'fue_register_organizer', 10, 4);
function fue_register_organizer( $var, $email_data, $fue_email, $queue_item ) { 
    $variables = array(
        'org_name'	    => '',
        'org_phone'	    => '',
        'org_email'	    => ''
    );
    if ( isset( $email_data['test'] ) && $email_data['test'] ) {
        if ( strpos($_SERVER['HTTP_HOST'], 'local') ) {
            $product_id = 17794; // test
        } else if ( strpos($_SERVER['HTTP_HOST'], 'flywheelstaging') ) {
            $product_id = 19381; // test
        } else {
            $product_id = 19381; // test
        }
        $org_nonformatted = true_get_booking_organizer($product_id);
        $variables['org_name'] = $org_nonformatted['name'];
        $variables['org_phone'] = $org_nonformatted['phone'];
        $variables['org_email'] = $org_nonformatted['email'];

    } else {
        $product_id = $queue_item->product_id;
        $org_nonformatted = true_get_booking_organizer($product_id);
        $variables['org_name'] = $org_nonformatted['name'];
        $variables['org_phone'] = $org_nonformatted['phone'];
        $variables['org_email'] = $org_nonformatted['email'];
    }

    $var->register( $variables );
}


add_action( 'fue_before_variable_replacements', 'fue_register_booking_meta', 10, 4);
function fue_register_booking_meta( $var, $email_data, $fue_email, $queue_item ) { 
    $variables = array(
        'booking_id'	=> '',
        'booking_view_booking' => ''
	);

    if ( isset( $email_data['test'] ) && $email_data['test'] ) {
        if ( strpos($_SERVER['HTTP_HOST'], 'local') ) {
            $testbooking_id = '17936'; // test
        } else if ( strpos($_SERVER['HTTP_HOST'], 'flywheelstaging') ) {
            $testbooking_id = '19393'; // test
        } else {
            $testbooking_id = '19393'; // test
        }

        $meta       = maybe_unserialize( $queue_item->meta );
        $booking_id = !empty( $meta['booking_id'] ) ? $meta['booking_id'] : $testbooking_id;

        $booking = new WC_Booking( $booking_id );
        
        // booking data
        if ( !empty( $booking_id ) ) {
            $variables['booking_id'] = $booking_id;
            $variables['booking_view_booking'] = $booking->get_order()->get_view_order_url();
        }
        // if ( !empty( $email_data['order_id'] ) ) {
        //     $order = wc_get_order( $email_data['order_id'] );
        //     $referral_code = get_user_meta( $order->customer_user, '_affiliate_key', true );

        
        // }
    } else {
        if ( !empty( $queue_item->order_id ) ) {
            // booking data
            $meta       = maybe_unserialize( $queue_item->meta );
            $booking_id = !empty( $meta['booking_id'] ) ? $meta['booking_id'] : 0;

            if ( !empty( $booking_id ) ) {
                $variables['booking_id'] = $booking_id;
            }
        }
    }

    $var->register( $variables );
}
