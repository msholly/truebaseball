<?php

// Defines
define('FL_CHILD_THEME_DIR', get_stylesheet_directory());
define('FL_CHILD_THEME_URL', get_stylesheet_directory_uri());

// Classes
require_once 'classes/class-fl-child-theme.php';

// Actions
add_action('wp_enqueue_scripts', 'FLChildTheme::enqueue_scripts', 1000);
add_action( 'wp_enqueue_scripts', 'FLChildTheme::enqueue_js' );

/**
 * Adding search icon at right side of the menu module
 *
 * @return void
 */
add_action('fl_builder_after_render_module', 'frame_add_search_icon_themer_header', 10);
function frame_add_search_icon_themer_header($module)
{
    global $woocommerce;
    //* Checking that you are using BB Theme
    if (! class_exists('FLTheme')) {
        return;
    }

    $id = '';

    if ($module->settings->class === "add-search-icon") {
        $id = $module->node;
    }
    if ($module->settings->type == "menu" && $module->node == $id) {
        //* Displaying the search icon
        FLTheme::nav_search();
    }
    return;
}


// FIX AFFILIATE SEARCH ON ADMIN 
add_action('admin_head', 'my_custom_fonts');

function my_custom_fonts() {
  echo '<style>
    body.events-cal .ui-autocomplete {
        font-size: 11px !important;
    }
    .acf-max-width img{
      max-width: 300px;
      max-height: 650px;
    }
  </style>';
}


// THE WEIRD BUG LOADING ADMIN COMMON ON THE FRONT END
// function project_dequeue_unnecessary_styles() {
//   wp_dequeue_style( 'common' );
//   wp_deregister_style( 'common' );
// }
// if ( ! is_admin() ) add_action( 'wp_enqueue_scripts', 'project_dequeue_unnecessary_styles' );


// Add a Beaver Builder Template to the bottom of the Beaver Builder Theme vertical menu
add_filter( 'wp_nav_menu_items', 'your_custom_menu_item', 10, 2 );
function your_custom_menu_item ( $items, $args ) {
    // if the menu is in fact our mobile main menu
    if ($args->menu == 'mobile-main-menu') {
        // get the content of our Beaver Builder Template
        // $bb_content = do_shortcode('[fl_builder_insert_layout slug="vertical-menu-content-bottom"]');
        $logo = '<a href="' . get_home_url() . '" class="sidenav-logo">' . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 571.33 91.59">
            <title>TRUE Diamond Science Logo</title>
            <g id="true-diamond-science-logo-white">
                <g id="true-diamond-science-logo-white-2">
                    <path
                        d="M279.58.07H157.43l-5,11.75h97c-.53,1.73-1.89,4.46-3.33,4.93h-96l-12.64,30H172l7.64-18.28h55.67l3.87,18.28H274l-3.65-18.36c5.21-.48,8.84-3.06,10.8-7.66l5.05-11.18v0c1.18-2.85,1.22-5.14.12-6.81S283,.11,279.58.07"
                        style="fill:#fff" />
                    <path
                        d="M400.46.12,387,31.94A7.78,7.78,0,0,1,384.73,35H324.85a1.7,1.7,0,0,1-.27-.81L339.12,0H305.41L289.59,37.21c-1.23,2.88-1.28,5.2-.17,6.88s3.51,2.69,7.09,2.69H404.85c6.31-.06,10.71-2.84,13.08-8.28L434.17.12Z"
                        style="fill:#fff" />
                    <polygon
                        points="566.35 11.74 571.33 0.07 448.55 0.07 429.7 44.55 428.98 46.34 428.79 46.78 551.49 46.69 556.89 34.02 466.98 34.02 469.32 28.5 559.1 28.5 564.08 16.75 474.29 16.75 476.4 11.74 566.35 11.74"
                        style="fill:#fff" />
                    <polygon
                        points="144.01 0 4.99 0 0 11.67 55.26 11.67 40.05 46.85 74.22 46.85 89.42 11.67 139.03 11.67 144.01 0"
                        style="fill:#fff" />
                    <path
                        d="M381.06,81.08H363c-.18-.17.32-1.4.63-2h20.58L386.1,75H360.18a3.14,3.14,0,0,0-2.1.93,7.15,7.15,0,0,0-1.67,2.38v0l-1.82,4.19c-.73,1.61-.6,2.52.39,2.69l19.22,0-.9,2H352.92l-1.76,4.11h20.23v0h5.4c1.16,0,2.48-1.19,3.3-3l1.67-3.66C382.55,82.92,382.2,81.08,381.06,81.08Z"
                        style="fill:#fff" />
                    <path
                        d="M428,85.6h20.3l3.44-7a4.15,4.15,0,0,0,.61-2.4c-.1-.63-.52-.94-1.28-.94H428c-1.35,0-2.52,1-3.48,2.89l-1.22,2.47h0l-3.77,7.61a4.28,4.28,0,0,0-.61,2.41c.1.62.52.93,1.29.93h25.18l2.11-4.1H427.18a.89.89,0,0,1,0-.28Zm3.29-6.22H444a1,1,0,0,1,0,.29l-.9,1.83H430l.51-1A4.29,4.29,0,0,1,431.28,79.38Z"
                        style="fill:#fff" />
                    <path
                        d="M540.32,76.22c-.11-.63-.53-.94-1.29-.94H515.94c-1.35,0-2.51,1-3.47,2.89l-1.23,2.47h0l-3.77,7.61a4.28,4.28,0,0,0-.61,2.41c.1.62.52.93,1.29.93h25.19l2.1-4.1H515.15a.89.89,0,0,1,0-.28l.8-1.61h20.3l3.45-7A4.32,4.32,0,0,0,540.32,76.22Zm-21.07,3.16H532a1,1,0,0,1,0,.29l-.9,1.83H518l.52-1A4,4,0,0,1,519.25,79.38Z"
                        style="fill:#fff" />
                    <path
                        d="M215.82,78.12a4.32,4.32,0,0,0,.61-2.4c-.11-.63-.53-.94-1.29-.94H190l-2,4.1h20.15a.72.72,0,0,1,0,.29l-.79,1.6H187.45l-3.88,7a4.25,4.25,0,0,0-.61,2.4c.11.63.53.94,1.29.94h23.08c1.35,0,2.52-1,3.48-2.89L212,85.73h0ZM204,87H191.26a.68.68,0,0,1,0-.28l.9-1.84h13.1l-.52,1A3.91,3.91,0,0,1,204,87Z"
                        style="fill:#fff" />
                    <path
                        d="M390.29,87l1.37-2.76,2-4a4.14,4.14,0,0,1,.73-1.07h15.16l2.1-4.1H391.07c-1.35,0-2.51,1-3.47,2.88l-1.23,2.47h0l-3.77,7.62a4.23,4.23,0,0,0-.61,2.4c.1.63.52.93,1.29.93h20.64l1.81-4.1H390.28A.89.89,0,0,1,390.29,87Z"
                        style="fill:#fff" />
                    <path
                        d="M489.1,87.21l1.36-2.76,2-4a4,4,0,0,1,.73-1.08h15.16l2.1-4.1H489.87c-1.34,0-2.51,1-3.47,2.89l-1.23,2.47h0l-3.77,7.61a4.36,4.36,0,0,0-.61,2.41q.15.93,1.29.93h20.65l1.81-4.1H489.09A.67.67,0,0,1,489.1,87.21Z"
                        style="fill:#fff" />
                    <path
                        d="M315.59,76c-.1-.63-.52-.93-1.29-.93H296.36L297,73.9h-7.28L280.9,91.41h7.47l6-12.2h12.94a.9.9,0,0,1,0,.28l-5.84,11.92h7.18l6.36-13A4.23,4.23,0,0,0,315.59,76Z"
                        style="fill:#fff" />
                    <path
                        d="M280.92,85.52l2.36-4.8.54-1.08h0l.58-1.2A4.23,4.23,0,0,0,285,76c-.1-.63-.52-.93-1.29-.93H260.63c-1.35,0-2.52,1-3.48,2.88l-1.22,2.47h0l-3.77,7.61a4.2,4.2,0,0,0-.61,2.41c.1.63.52.93,1.28.93h23.09c1.35,0,2.52-1,3.48-2.88l1.49-3Zm-8.32,1.79H259.84a.89.89,0,0,1,0-.28l1.43-2.88,1.92-3.87a4.14,4.14,0,0,1,.74-1.07H276.7a.89.89,0,0,1,0,.28l-.91,1.87-2.44,4.88A4.4,4.4,0,0,1,272.6,87.31Z"
                        style="fill:#fff" />
                    <path
                        d="M483.53,76c-.11-.63-.53-.93-1.29-.93H464.3l.59-1.21h-7.28l-8.78,17.51h7.48l6-12.2h12.93a.67.67,0,0,1,0,.28l-5.84,11.92h7.19l6.36-13A4.32,4.32,0,0,0,483.53,76Z"
                        style="fill:#fff" />
                    <path
                        d="M253.73,78.53a5.07,5.07,0,0,0,.84-2.37c-.1-.61-.5-.92-1.22-.93l-1.94,0-23.68.36.41-.83h-7.36l-8.09,16.29h7.37l3.15-6.37,2.34-4.7,6.55-.1L227,91.08h7.11l5.23-11.28,6.65-.1-5,11.38h7.1l5.58-12.53Z"
                        style="fill:#fff" />
                    <path
                        d="M179.93,69.43l.11-.22h-7.37l-3,6.1-19.31,0c-1.15.17-2.14,1.07-2.94,2.67l-5.15,10.17v0a4.18,4.18,0,0,0-.59,2.37c.1.61.49.92,1.22.93h26l1-2.27ZM153.88,80.35h13.27l-2.94,6H150.5A55.34,55.34,0,0,1,153.88,80.35Z"
                        style="fill:#fff" />
                    <path
                        d="M351,69.21h-7.37l-3,6.1-19.31,0c-1.15.17-2.14,1.07-2.94,2.67l-5.15,10.17v0a4.18,4.18,0,0,0-.59,2.37c.1.61.49.92,1.22.93h26l1-2.27,10-19.76ZM324.88,80.35h13.27l-2.95,6H321.5A55.34,55.34,0,0,1,324.88,80.35Z"
                        style="fill:#fff" />
                    <polygon points="179.78 74.78 171.54 91.5 178.66 91.5 187.15 74.78 179.78 74.78" style="fill:#fff" />
                    <polygon points="415.42 74.78 407.18 91.5 414.3 91.5 422.79 74.78 415.42 74.78" style="fill:#fff" />
                </g>
            </g>
        </svg>' . '</a>';
        $searchform = '<li>' . get_search_form( false ) . '</li>';
    	// append the content to the beginning of our menu
        $items = $logo .= $searchform .= $items;
    }
    return $items;
}


// ADD CATEOGRY TO BODY
add_filter( 'body_class', 'wc_product_cats_css_body_class' );
 
function wc_product_cats_css_body_class( $classes ){
  if( is_singular( 'product' ) ){
    $custom_terms = get_the_terms(0, 'product_cat');
    if ($custom_terms) {
      foreach ($custom_terms as $custom_term) {
        $classes[] = 'product-cat-' . $custom_term->slug;
      }
    }
  }
  return $classes;
}

/**
 * Attributes shortcode callback.
 */
function woo_attributes_shortcode( $atts ) {

  global $product;

  if( ! is_object( $product ) || ! $product->has_attributes() ){
      return;
  }

  // parse the shortcode attributes
  $args = shortcode_atts( array(
      'attributes' => array_keys( $product->get_attributes() ), // by default show all attributes
  ), $atts );

  // is pass an attributes param, turn into array
  if( is_string( $args['attributes'] ) ){
      $args['attributes'] = array_map( 'trim', explode( '|' , $args['attributes'] ) );
  }

  // start with a null string because shortcodes need to return not echo a value
  $html = '';

  if( ! empty( $args['attributes'] ) ){

      foreach ( $args['attributes'] as $attribute ) {

          // get the WC-standard attribute taxonomy name
          $taxonomy = strpos( $attribute, 'pa_' ) === false ? wc_attribute_taxonomy_name( $attribute ) : $attribute;

          if( taxonomy_is_product_attribute( $taxonomy ) ){

              // Get the attribute label.
              $attribute_label = wc_attribute_label( $taxonomy );

              // Build the html string with the label followed by a clickable list of terms.
              // heads up that in WC2.7 $product->id needs to be $product->get_id()
              $html .= get_the_term_list( $product->get_id(), $taxonomy, $attribute_label . ': ' , ', ' );                                
          }

      }

      // if we have anything to display, wrap it in a <ul> for proper markup
      // OR: delete these lines if you only wish to return the <li> elements
      // if( $html ){
      //     $html = '<ul class="product-attributes">' . $html . '</ul>';
      // }

  }

  return $html;
}
add_shortcode( 'display_attributes', 'woo_attributes_shortcode' );



/**
 * Disable the emoji's
 */
function disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
    add_filter( 'wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2 );
 }
 add_action( 'init', 'disable_emojis' );
 
 /**
  * Filter function used to remove the tinymce emoji plugin.
  * 
  * @param array $plugins 
  * @return array Difference betwen the two arrays
  */
 function disable_emojis_tinymce( $plugins ) {
    if ( is_array( $plugins ) ) {
        return array_diff( $plugins, array( 'wpemoji' ) );
    } else {
        return array();
    }
 }
 
 /**
  * Remove emoji CDN hostname from DNS prefetching hints.
  *
  * @param array $urls URLs to print for resource hints.
  * @param string $relation_type The relation type the URLs are printed for.
  * @return array Difference betwen the two arrays.
  */
function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
    if ( 'dns-prefetch' == $relation_type ) {
        /** This filter is documented in wp-includes/formatting.php */
        $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );

        $urls = array_diff( $urls, array( $emoji_svg_url ) );
    }

    return $urls;
 }


 /**
 * Add product_brand to taxonomies list for variations.
 * 
 * @param array $taxonomies
 *
 * @return array
 */
function iconic_add_brands_to_variations( $taxonomies ) {
	$taxonomies[] = 'length-in';

	return $taxonomies;
}

add_filter( 'iconic_wssv_variation_taxonomies', 'iconic_add_brands_to_variations', 10, 1 );

/**
 * Increase page list for variations on product admin.
 * 
 * 
 *
 * @return number
 */
add_filter( 'woocommerce_admin_meta_boxes_variations_per_page', 'true_increase_variations_per_page' );

function true_increase_variations_per_page() {
	return 50;
}


/**
 * Event Tickets Plus: WooCommerce: Force all tickets to be "Sold Individually".
 *
 * By default, this limits the purchase quantity to 1, but this is quantity is
 * filterable with `woocommerce_add_to_cart_sold_individually_quantity`.
 *
 * @see Tribe__Tickets_Plus__Commerce__WooCommerce__Main::get_event_for_ticket()
 * @see WC_Product::is_sold_individually()
 * @see WC_Cart::add_to_cart()
 *
 * @link https://gist.github.com/cliffordp/9a457b724e38b3036f8d48adc90930ed
 */
// function cliff_et_force_woo_tix_sold_individually( $sold_individually, $wc_product_instance ) {
//   if (! class_exists( 'Tribe__Tickets_Plus__Commerce__WooCommerce__Main' ) ) {
//       return $sold_individually;
//   }
//   $wootix = new Tribe__Tickets_Plus__Commerce__WooCommerce__Main;
//   if ( empty( $wc_product_instance->get_id() ) ) {
//       return false;
//   }
//   if ( $wootix->get_event_for_ticket( $wc_product_instance->get_id() ) ) {
//       return true;
//   } else {
//       return $sold_individually;
//   }
// }
// add_filter( 'woocommerce_is_sold_individually', 'cliff_et_force_woo_tix_sold_individually', 10, 2 );


// Disable autoptimize on pages with the word "oliver-pos" in the URL
function true_ao_noptimize() {
    if (strpos($_SERVER['REQUEST_URI'],'oliver-pos')!==false) {
        return true;
    } else {
        return false;
    }
}
add_filter('autoptimize_filter_noptimize','true_ao_noptimize',10,0);

// Adds ORDER ID: and TICKET ID to results, but not search
function true_acf_extend_search_result( $title, $post, $field, $post_id ) {
    // add post type to each result
    $pre = 'Order ID: ';
    $title = $pre .= $title .= ' | Ticket ID: ' . $post->ID;
    // ChromePhp::log($post);
    return $title;
}
add_filter( 'acf/fields/post_object/result', 'true_acf_extend_search_result', 10, 4);


// function true_acf_extend_search( $args, $field, $post_id ) {
	
    // // only show children of the current post being edited
    // $args['ID'] = $post_id;
	
	
	// return
    // return $args;
    
// }
// filter for every field
// add_filter('acf/fields/post_object/query', 'true_acf_extend_search', 10, 3);