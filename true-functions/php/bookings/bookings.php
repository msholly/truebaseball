<?php

/**
 * Will make the Bookings calender default to the month with the first available booking.
 */
add_filter( 'wc_bookings_calendar_default_to_current_date', '__return_false' );

/**
 * Register Shortcode Google Address for Bookings
 */

function true_get_booking_venue_addr($product_id)
{
    $venue = true_get_booking_venue($product_id, null);
    return $venue['address'];
}
add_shortcode('true_booking_venue_addr', 'true_get_booking_venue_addr');


/**
 * Register Shortcode Google Address for Bookings
 */

function true_get_booking_venue($product_id, $part)
{
    $venue = get_field('venue', $product_id);
    $location = $venue['venue_google_map'];
    $phone = $venue['venue_phone'];

    if( $location ) {

        // Loop over segments and construct HTML.
        $address = '';
        $full = array('street_number', 'street_name', 'city', 'state_short', 'post_code');
        $address_partial = $part;
        $address_part = empty( $part ) ? $full : $address_partial;
        foreach( $address_part as $i => $k ) {
            if( isset( $location[ $k ] ) ) {

                if ( $k == 'street_number') {
                    $address .= sprintf( '<span class="addr1"><span class="segment-%s">%s</span> ', $k, $location[ $k ] );
                } elseif ($k == 'street_name') {
                    $address .= sprintf( '<span class="segment-%s">%s</span></span> ', $k, $location[ $k ] );
                } elseif ($k == 'city') {
                    $address .= sprintf( '<span class="segment-%s">%s</span>, ', $k, $location[ $k ] );
                } elseif ($k == 'lat') {
                    $address .= sprintf( '%s,', urlencode($location[ $k ]) );
                    // $address .= sprintf( '<a href="https://www.google.com/maps/dir/?api=1&origin=Google+Pyrmont+NSW&destination=QVB&destination_place_id=ChIJISz8NjyuEmsRFTQ9Iw7Ear8&travelmode=walking class="segment-%s">%s</a>, ', $k, urlencode($location[ $k ] );
                } elseif ($k == 'lng') {
                    $address .= sprintf( '%s', urlencode($location[ $k ]) );
                    // $address .= sprintf( '<a href="https://www.google.com/maps/dir/?api=1&origin=Google+Pyrmont+NSW&destination=QVB&destination_place_id=ChIJISz8NjyuEmsRFTQ9Iw7Ear8&travelmode=walking class="segment-%s">%s</a>, ', $k, urlencode($location[ $k ] );
                } elseif ($k == 'address') {
                    $address .= 'https://www.google.com/maps/dir/?api=1&destination=' . sprintf( '%s', urlencode($location[ $k ]) );
                    // $address .= sprintf( '<a href="https://www.google.com/maps/dir/?api=1&origin=Google+Pyrmont+NSW&destination=QVB&destination_place_id=ChIJISz8NjyuEmsRFTQ9Iw7Ear8&travelmode=walking class="segment-%s">%s</a>, ', $k, urlencode($location[ $k ] );
                } else {
                    $address .= sprintf( '<span class="segment-%s">%s</span> ', $k, $location[ $k ] );
                }               
                
            }
        }

        // Trim trailing comma.
        $address = trim( $address, ', ' );

    }
    return array(
        'address'	=> $address,
        'phone'     => $phone
	);
}

/**
 * Register Shortcode Google Address for Bookings
 */

function true_get_booking_organizer($product_id)
{
    $organizer = get_field('organizer', $product_id);
    $name = $organizer['organizer_name'];
    $phone = $organizer['organizer_phone'];
    $email = $organizer['organizer_email'];

    return array(
        'name'	    => $name,
        'phone'     => $phone,
        'email'     => $email
	);
}