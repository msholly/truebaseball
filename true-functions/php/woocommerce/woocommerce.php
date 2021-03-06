<?php

require_once 'oliver-pos.php';

/*
 * Legacy API
 * Add a referanse field to the Order API response.
*/
function prefix_wc_api_order_response($order)
{
    // Get the value
    $transaction_id = get_post_meta($order['id'], '_transaction_id', true);

    $true_meta_event = ($value = get_field('order_type', $order['id'])) ? $value : '';
    $true_meta_salesrep = ($value = get_field('sales_rep', $order['id'])) ? $value : '';
    $true_meta_affiliate = ($value = get_field('affiliate', $order['id'])) ? $value : '';

    $order['true_meta_event'] = $true_meta_event;
    $order['true_meta_salesrep'] = $true_meta_salesrep;
    $order['true_meta_affiliate'] = $true_meta_affiliate;
    $order['transaction_id'] = $transaction_id;

    $order['true_meta_attendee'] = tribe_tickets_get_attendees($order['id'], $context = null);

    return $order;
}
add_filter('woocommerce_api_order_response', 'prefix_wc_api_order_response', 10, 1);

/**
 * Register new endpoint to use inside My Account page.
 *
 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
 */
function true_add_events_endpoint()
{
    add_rewrite_endpoint('events', EP_ROOT | EP_PAGES);
}

add_action('init', 'true_add_events_endpoint');


/**
 * Add new query var.
 *
 * @param array $vars
 * @return array
 */
function true_events_query_vars($vars)
{
    $vars[] = 'events';
    return $vars;
}

add_filter('query_vars', 'true_events_query_vars', 0);


/**
 * Custom help to add new items into an array after a selected item.
 *
 * @param array $items
 * @param array $new_items
 * @param string $after
 * @return array
 */
function true_insert_after_helper($items, $new_items, $after)
{
    // Search for the item position and +1 since is after the selected item key.
    $position = array_search($after, array_keys($items)) + 1;

    // Insert the new item.
    $array = array_slice($items, 0, $position, true);
    $array += $new_items;
    $array += array_slice($items, $position, count($items) - $position, true);

    return $array;
}

/**
 * Insert the new endpoint into the My Account menu.
 *
 * @param array $items
 * @return array
 */

function true_add_events_link_my_account($items)
{

    $new_items = array();
    $new_items['events'] = __('Events', 'woocommerce');

    // Add the new item after `orders`.
    return true_insert_after_helper($items, $new_items, 'orders');
}

add_filter('woocommerce_account_menu_items', 'true_add_events_link_my_account');


// ------------------
// 4. Add content to the new endpoint

function true_events_content()
{
    $myaccount_page_id = get_option('woocommerce_myaccount_page_id');
    if ($myaccount_page_id) {
        $myaccount_orders_page_url = get_permalink($myaccount_page_id) . 'orders';
    }
    echo '<h3>Your upcoming TRUE Fitting Events</h3><p>Note: You may have multiple tickets per event, and those will be available in the <a href="' . esc_url($myaccount_orders_page_url) . '">ORDERS</a> tab.</p>';
    echo do_shortcode(' [tribe-user-event-confirmations] ');
}

add_action('woocommerce_account_events_endpoint', 'true_events_content');
// Note: add_action must follow 'woocommerce_account_{your-endpoint-slug}_endpoint' format

function add_affiliate_info_on_create_order($order_id)
{

    $order = new WC_Order($order_id);

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
        $affiliate_email = 'N/A';
        $parent_mlm_email = 'N/A';
        $event_type = 'ecomm';
        $customer_note = 'Web Checkout';
        $oliverTicketOrderID = 'N/A';

        if (class_exists('Affiliate_WP')) {
            if ($affwp_ref) {
                $user_id = affwp_get_affiliate_user_id($affwp_ref);
                $affiliate_info = get_userdata($user_id);
                // $affiliate_login_name = $affiliate_info->user_login;
                $affiliate_email = $affiliate_info->user_email;

                // MLM PARENT
                $parent_mlm_affid = affwp_mlm_get_parent_affiliate($affwp_ref);
                $parent_mlm_wpid = affwp_get_affiliate_user_id($parent_mlm_affid);
                $parent_mlm_info = get_userdata($parent_mlm_wpid);
                $parent_mlm_email = $parent_mlm_info->user_email;

                $event_type = 'web';

                // IF TRUE SALES REP AFFILIATE ORDER 
                list($email_prefix, $email_domain) = explode('@', $affiliate_email);

                if ($email_domain == 'truediamondscience.com') {
                    // Are internal Sales Reps
                    $parent_mlm_email = $affiliate_email;
                    $affiliate_email = 'N/A';
                }
                update_field('affiliate', $user_id, $order_id);
                update_field('sales_rep', $parent_mlm_info->ID, $order_id);
            }
        }

        // The text for the note
        // $note = __('TYPE: ' . $web_order_type . ' | SALESREP: ' . $parent_mlm_login_name . ' | AFFILIATE: ' . $affiliate_login_name . ' | SHIPPING: ' . $shipMethod );
        // $note = __('Web Checkout | TYPE: ' . $web_order_slug . ' | SALESREP: ' . $parent_mlm_login_name . ' | AFFILIATE: ' . $affiliate_login_name . ' | SHIPPING: ' . $shipMethod );
        $note = __($customer_note . ' | TYPE: ' . $event_type . ' | SALESREP: ' . $parent_mlm_email . ' | AFFILIATE: ' . $affiliate_email . ' | SHIPPING: ' . $shipMethod . ' | TICKET ORDER ID: ' . $oliverTicketOrderID);

        update_field('order_type', $event_type, $order_id);

        // update the customer_note on the order, the WP Post Excerpt
        $update_excerpt = array(
            'ID'             => $order_id,
            'post_excerpt'   => $note,
        );
        wp_update_post($update_excerpt);

        // Add the note
        $order->add_order_note($note);

        // Save the data
        $order->save();
    }
}
add_action('woocommerce_checkout_order_processed', 'add_affiliate_info_on_create_order', 20);


// define the woocommerce_cart_item_thumbnail callback 
function filter_woocommerce_cart_item_thumbnail($product_get_image, $cart_item, $cart_item_key)
{
    // make filter magic happen here... 

    if (strpos($product_get_image, 'placeholder') !== false) {
        return false;
    }
    return $product_get_image;
};

// add the filter 
add_filter('woocommerce_cart_item_thumbnail', 'filter_woocommerce_cart_item_thumbnail', 10, 3);


/**
 * @snippet       Hide one shipping option in one zone when Free Shipping is available
 */
function true_unset_shipping_when_free_is_available_in_zone($rates)
{
    // Only unset rates if free_shipping is available
    if (isset($rates['free_shipping:3'])) {
        unset($rates['flat_rate:1']);
    }

    return $rates;
}

add_filter('woocommerce_package_rates', 'true_unset_shipping_when_free_is_available_in_zone', 10, 2);
add_filter('cfw_get_shipping_checkout_fields', 'true_unset_shipping_when_free_is_available_in_zone', 10, 2);

/**
 * Hides the product's weight and dimension in the single product page.
 */
add_filter('wc_product_enable_dimensions_display', '__return_false');


/**
 *  Add a custom email to the list of emails WooCommerce should load
 *
 * @since 0.1
 * @param array $email_classes available email classes
 * @return array filtered available email classes
 */
function add_event_order_woocommerce_email($email_classes)
{

    // include our custom email class
    require_once('class-wc-event-organizer-order-email.php');

    // add the email class to the list of email classes that WooCommerce loads
    $email_classes['WC_Event_Organizer_Order_Email'] = new WC_Event_Organizer_Order_Email();

    return $email_classes;
}
add_filter('woocommerce_email_classes', 'add_event_order_woocommerce_email');


/**
 * @snippet       Enable Payment Gateway for a Specific User Role | WooCommerce
 * @how-to        Watch tutorial @ https://businessbloomer.com/?p=19055
 * @sourcecode    https://businessbloomer.com/?p=273
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 3.5.4
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */

add_filter('woocommerce_available_payment_gateways', 'true_paypal_enable_manager');

function true_paypal_enable_manager($available_gateways)
{
    // global $woocommerce;
    if (isset($available_gateways['cheque']) && !current_user_can('administrator')) {
        unset($available_gateways['cheque']);
    }
    // if (isset($available_gateways['ppec_paypal']) && !current_user_can('administrator')) {
    //     unset($available_gateways['ppec_paypal']);
    // }
    return $available_gateways;
}

// Add custom mailchimp integration to CheckoutWC
function true_add_mailchimp_to_checkoutwc()
{
    ?>
    <p><label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
            <input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="mc4wp-subscribe" value="1" />
            Subscribe to the TRUE Newsletter.
        </label>
    </p>
<?php
}
add_action('cfw_checkout_before_payment_method_tab_nav', 'true_add_mailchimp_to_checkoutwc');

// Deferr Transactional emails to speed checkout
add_filter( 'woocommerce_defer_transactional_emails', '__return_true' );


// Rename Phone to Mobile
add_filter( 'cfw_get_shipping_checkout_fields', 'change_shipping_apt_field_label', 100, 1 );

function change_shipping_apt_field_label( $fields ) {
    $fields[ 'shipping_phone' ][ 'label' ] = 'Mobile Number';
    $fields[ 'shipping_phone' ][ 'placeholder' ] = 'Mobile Number';

	return $fields;
}
