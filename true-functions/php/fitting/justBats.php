<?php

/**
 * Register Shortcode JustBats Product URL from Iconic Woo Custom Fields for Variations
 */

function true_get_just_bats_url($productId)
{
    global $post;
    $productId = $post->ID;

    $base_justbats_url = 'https://www.justbats.com/product/';

    // start with a null string because shortcodes need to return not echo a value
    $full_justbats_url = '';

    if (!empty($productId)) {

        if (class_exists('Iconic_CFFV_Fields')) { 
            $slug = Iconic_CFFV_Fields::get_product_fields_data($productId);
            $slug = $slug['justbats_product_url']['value'];
    
            if (startsWith($slug, '/')) {
                // Remove Starting slash just in case
                $slug = substr($slug, 1);
            }
            $full_justbats_url = $base_justbats_url . $slug;
        }
        
    }

    return $full_justbats_url;
}
add_shortcode('true_just_bats_url', 'true_get_just_bats_url');

// startsWith Helper
function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}
