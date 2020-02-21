<?php

/**
 * Register Shortcode JustBats Product URL from Iconic Woo Custom Fields for Variations
 */

function true_get_booking_venue_addr()
{
    $location = get_field('venue_google_map');
    if( $location ) {

        // Loop over segments and construct HTML.
        $address = '';
        foreach( array('street_number', 'street_name', 'city', 'state', 'post_code') as $i => $k ) {
            if( isset( $location[ $k ] ) ) {

                if ( $k == 'street_number') {
                    $address .= sprintf( '<span class="addr1"><span class="segment-%s">%s</span> ', $k, $location[ $k ] );
                } elseif ($k == 'street_name') {
                    $address .= sprintf( '<span class="segment-%s">%s</span></span> ', $k, $location[ $k ] );
                } elseif ($k == 'city') {
                    $address .= sprintf( '<span class="segment-%s">%s</span>, ', $k, $location[ $k ] );
                } else {
                    $address .= sprintf( '<span class="segment-%s">%s</span> ', $k, $location[ $k ] );
                }
                
            }
        }

        // Trim trailing comma.
        $address = trim( $address, ', ' );

    }

    return $address;
}
add_shortcode('true_booking_venue_addr', 'true_get_booking_venue_addr');