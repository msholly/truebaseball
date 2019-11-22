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

            // get variant info
            $product = wc_get_product($productId);
            $bat_variant = $product->get_variation_attributes();
            $bat_grip = $bat_variant['attribute_pa_grip-size'];
            $bat_length = $bat_variant['attribute_pa_length-in'];

            // Replace to match Justbats
            if ($bat_grip == 'youth-standard') {
                $bat_grip = 'Small%20Grip';
            }
            if ($bat_grip == 'youth-oversized') {
                $bat_grip = 'Med%20Grip';
            }

            // slug to back decimal
            $bat_length = str_replace('-', '.', $bat_length);

            // FINAL target
            // https://www.justbats.com/product/true-t1--10-usa-baseball-bat--yb-t1-20-10/32218/?attr=28.5%22|Small%20Grip
            $full_justbats_url = $base_justbats_url . $slug . '/?attr=' . $bat_length . '%22--' . $bat_grip;

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
