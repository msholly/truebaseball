<?php

/**
 * Register Shortcode BaseballMonkey Product URL from Iconic Woo Custom Fields for Variations
 */

function true_get_baseball_monkey_url($productId)
{
    global $post;
    $productId = $post->ID;

    // start with a null string because shortcodes need to return not echo a value
    $full_baseballmonkey_url = '';

    if (!empty($productId)) {

        if (class_exists('Iconic_CFFV_Fields')) { 
            $full_baseballmonkey_url = Iconic_CFFV_Fields::get_product_fields_data($productId);
            $full_baseballmonkey_url = $full_baseballmonkey_url['baseballmonkey_product_url']['value'];
        }
        
    }

    return $full_baseballmonkey_url;
}
add_shortcode('true_baseballmonkey_url', 'true_get_baseball_monkey_url');


