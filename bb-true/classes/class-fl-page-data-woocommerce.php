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

        // WORKS ON FRONTEND AND BACKEND EDITOR
        if ( is_object( $product ) ) {
            $attributes = '';
            ob_start();
            wc_display_product_attributes( wc_get_product() );
            $html = ob_get_clean();
        }

        // SET STATIC PRODUCT ID IF BB IS TRYING TO SAVE
        if ( empty( $product ) && ! FLPageData::is_archive() && FLBuilderModel::is_builder_active() ) {
            $attributes = 462;

            // SAVE SINCE IT TECHNICALLY WORKS AS CUSTOM STRUCTURE
            // foreach ( $attributes as $attribute ) {
            //     // Get the taxonomy.
            //     $terms = wp_get_post_terms( $product->get_id(), $attribute[ 'name' ], 'all' );
            //     $taxonomy = $terms[ 0 ]->taxonomy;
            //     // Get the taxonomy object.
            //     $taxonomy_object = get_taxonomy( $taxonomy );
            //     // Get the attribute label.
            //     $attribute_label = $taxonomy_object->labels->name;
            //     // Display the label followed by a clickable list of terms.
            //     echo get_the_term_list( $post->ID, $attribute[ 'name' ] , '<div class="attributes">' . $attribute_label . ': ' , ', ', '</div>' );
            // }
        }
        
        if ( $attributes ) {
            return;
        }
        
        return $html;
	}

}

