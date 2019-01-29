<?php

/**
 * Handles logic for page data WooCommerce properties.
 *
 * @since 1.0
 */
final class TRUEFLPageDataWooCommerce {
   
	/**
	 * @since 1.0
	 * @return string
	 */
	static public function get_product_attributes() {
        global $product;
        wc_display_product_attributes( $product );
	}

}

