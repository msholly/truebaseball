<?php

// add_action('wp_ajax_nopriv_get_tax_info', 'true_get_tax_info');
add_action('wp_ajax_get_tax_info', 'true_get_tax_info');

function true_get_tax_info()
{

    if (isset($_GET['true_pos_nonce']) && wp_verify_nonce($_GET['true_pos_nonce'], 'true_pos_form_nonce')) {

        $checkoutData = $_REQUEST['checkoutData'];

        try {
            $taxjar_api = get_option('woocommerce_taxjar-integration_settings')['api_token'];
            $taxjar = TaxJar\Client::withApiKey($taxjar_api);

            // Nexus and ship-to information
            $default_locations = get_option('woocommerce_default_country');
            $store_country_state = explode(':', $default_locations);
            $store_zip = get_option('woocommerce_store_postcode');
            $store_city = get_option('woocommerce_store_city');
            $store_addr = get_option('woocommerce_store_address');

            // Cust Information
            // $raw_ship_to_country = $checkoutData['country'];
            // preg_match('#\((.*?)\)#', $raw_ship_to_country, $ship_to_country);
            // Init variables
            $cart_amount = 0;
            $ship_amount = 0;
            $line_items = $checkoutData['cartProducts'];
            // ChromePhp::log($line_items);

            foreach ($line_items as $item) {

                // IF SHIPPING
                if ($item['productId'] == '2414') { // product id of Private 2 Day Ship
                    // NO TAXES FOR SHIPPING
                    // $ship_amount += $item['amount'];
                } else if ($item['productId'] == '2496') {
                    // NO TAXES FOR SHIPPING
                    // $ship_amount += $item['amount'];
                } else {

                    // IF DIGITAL GOODS
                    if ($item['productId'] == '2053' || $item['productId'] == '2046') {

                        $lineitems[] = array(
                            'id' => $item['productId'],
                            'quantity' => intval($item['quantity']),
                            'unit_price' => $item['amount'] / $item['quantity'],
                            'discount' => intval($item['discountAmount']),
                            'product_tax_code' => '19005',
                        );
                        $cart_amount += $item['amount'];
                    } else {
                        // ALL ITEMS BESIDES SHIPPING AND DIGITAL 
                        $lineitems[] = array(
                            'id' => $item['productId'],
                            'quantity' => intval($item['quantity']),
                            'unit_price' => $item['amount'] / $item['quantity'],
                            'discount' => intval($item['discountAmount'])
                        );
                        $cart_amount += $item['amount'];
                    }
                }
            }
            // ChromePhp::log($lineitems);

            $tax_objects = [
                'from_country' => $store_country_state[0],
                'from_zip' => $store_zip,
                'from_state' => $store_country_state[1],
                'from_city' => $store_city,
                'from_street' => $store_addr,
                'to_country' => 'US',
                'to_zip' => $checkoutData['zip'],
                'to_state' => $checkoutData['state'],
                'to_city' => $checkoutData['city'],
                'to_street' => $checkoutData['addressLine1'],
                'amount' => $cart_amount,
                'shipping' => $ship_amount,
                // 'nexus_addresses' => [
                //     [
                //         'id' => 'Main Location',
                //         'country' => $store_country_state[0],
                //         'zip' => $store_zip,
                //         'state' => $store_country_state[1],
                //         'city' => $store_city,
                //         'street' => $store_addr,
                //     ]
                // ],
                'line_items' => $lineitems
            ];

            // ChromePhp::log($tax_objects);
            $tax = $taxjar->taxForOrder(
                $tax_objects
            );
            // ChromePhp::log('RETURNED TAX INFO');
            // ChromePhp::log($tax);

            if (defined('DOING_AJAX') && DOING_AJAX) {
                wp_send_json($tax);
                die();
            } else {
                exit();
            }
        } catch (TaxJar\Exception $e) {
            // Log error to error.php
            error_log($e->getMessage(), 0);
        }
    } else {
        ChromePhp::log("Bad Nonce");
        error_log("Bad Oliver Tax Nonce.", 0);
        wp_die(__('Invalid nonce specified', 'TRUE Functions'), __('Error', 'TRUE Functions'), array(
            'response'     => 403,
            'back_link' => 'admin.php?page=' . 'TRUE Functions',
        ));
    }
}
