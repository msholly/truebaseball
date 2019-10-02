<?php

require_once 'add-address.php';
require_once 'add-city.php';
require_once 'add-state.php';
require_once 'add-zip.php';
require_once 'add-program-type.php';
require_once 'add-event-date.php';

require_once 'add-roles.php';

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