<?php

add_action( 'wp_ajax_nopriv_get_tax_info', 'true_get_tax_info' );
add_action( 'wp_ajax_get_tax_info', 'true_get_tax_info' );

function true_get_tax_info() {
    $checkoutData = $_REQUEST['checkoutData'];
    ChromePhp::log($checkoutData);

    try {
        // SET VARIABLES BASED ON SERVER ENV
        if ( strpos($_SERVER['HTTP_HOST'], 'local') ) {
            $taxjar_api = get_option( 'woocommerce_taxjar-integration_settings' )['api_token'];
        } else if ( strpos($_SERVER['HTTP_HOST'], 'flywheelsites') ) {
            $taxjar_api = get_option( 'woocommerce_taxjar-integration_settings' )['api_token'];
        } else {
            $taxjar_api = get_option( 'woocommerce_taxjar-integration_settings' )['api_token'];
        }

        $taxjar = TaxJar\Client::withApiKey($taxjar_api); 

        // Nexus and ship-to information
        $default_locations = get_option( 'woocommerce_default_country' );
        $store_country_state = explode(":", $default_locations);
        $store_zip = get_option( 'woocommerce_store_postcode' );
        $store_city = get_option( 'woocommerce_store_city' );
        $store_addr = get_option( 'woocommerce_store_address' );

        // Cust Information
        $raw_ship_to_country = $checkoutData['country'];
        preg_match('#\((.*?)\)#', $raw_ship_to_country, $ship_to_country);
        // Init variables
        $cart_amount = 0;
        $ship_amount = 0;
        $line_items = $checkoutData['cartProducts'];

        foreach ($line_items as $item) {  
            
            // IF SHIPPING
            if ( $item['productId'] == "2414" ) { // product id of Private 2 Day Ship
                $ship_amount += $item['amount'];
            } else if ( $item['productId'] == "2496" ) {
                $cart_amount += $item['amount'];
            } else {

                // IF DIGITAL GOODS
                if ( $item['productId'] == "2053" || $item['productId'] == "2046" ) {
                    $lineitems[] = array(
                        'id' => $item['productId'],
                        'quantity' => 1,
                        'unit_price' => $item['amount'],
                        'product_tax_code' => '19005',
                        'discount' => 0
                    );
                    $cart_amount += $item['amount'];

                } else {
                    // ALL ITEMS BESIDES SHIPPING AND DIGITAL 
                    $lineitems[] = array(
                        'id' => $item['productId'],
                        'quantity' => 1,
                        'unit_price' => $item['amount'],
                        'discount' => 0
                    );
                    $cart_amount += $item['amount'];
                }
                
            }
        }
        ChromePhp::log($lineitems);

        $tax_objects = [
            'from_country' => $store_country_state[0],
            'from_zip' => $store_zip,
            'from_state' => $store_country_state[1],
            'from_city' => $store_city,
            'from_street' => $store_addr,
            'to_country' => $ship_to_country[1] ?: 'US',
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

        ChromePhp::log($tax_objects);



        $tax = $taxjar->taxForOrder(
            $tax_objects
        );
        ChromePhp::log("RETURNED TAX INFO");
        ChromePhp::log($tax);
        
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            wp_send_json($tax);
            die();
        }
        else {
            // TO DO - BUILD FACETWP URL BASED ON PARAMS
            // wp_redirect( get_permalink( $_REQUEST['post_id'] ) );
            exit();
        }
    
    } catch (TaxJar\Exception $e) {
      // Log error to error.php
      error_log( $e->getMessage(), 0);
    }

}

function true_get_tax () {

    

}
