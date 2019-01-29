<?php
function get_product_attributes() {
    global $product;
    wc_display_product_attributes( $product );
}

echo get_product_attributes();
