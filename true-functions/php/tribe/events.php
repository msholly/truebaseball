<?php

// DISABLE AUTOMATIC SKU GENERATION
add_filter( 'event_tickets_woo_should_default_ticket_sku', false );


// Add a Woo Product Category on ticket save
add_action( 'event_tickets_after_save_ticket', 'tribe_events_add_product_category_to_tickets', 10, 4 );

function tribe_events_add_product_category_to_tickets( $event_id, $ticket, $raw_data, $classname ) {

    if ( ! empty( $ticket ) && isset( $ticket->ID ) ) {
        wp_add_object_terms( $ticket->ID, 'tickets', 'product_cat' );
    }

}