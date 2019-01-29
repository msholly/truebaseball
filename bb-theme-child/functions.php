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

add_action('admin_head', 'my_custom_fonts');

function my_custom_fonts() {
  echo '<style>
    body.events-cal .ui-autocomplete {
        font-size: 11px !important;
    }
  </style>';
}


// THE WEIRD BUG LOADING ADMIN COMMON ON THE FRONT END
function project_dequeue_unnecessary_styles() {
  wp_dequeue_style( 'common' );
  wp_deregister_style( 'common' );
}
if ( ! is_admin() ) add_action( 'wp_enqueue_scripts', 'project_dequeue_unnecessary_styles' );


// Add a Beaver Builder Template to the bottom of the Beaver Builder Theme vertical menu
add_filter( 'wp_nav_menu_items', 'your_custom_menu_item', 10, 2 );
function your_custom_menu_item ( $items, $args ) {
    // if the menu is in fact our mobile main menu
    if ($args->menu == 'mobile-main-menu') {
        // get the content of our Beaver Builder Template
        // $bb_content = do_shortcode('[fl_builder_insert_layout slug="vertical-menu-content-bottom"]');
        $logo = '<a href="' . get_home_url() . '" class="sidenav-logo">' . '<svg id="true_diamond_science_logo" data-name="TRUE Diamond Science" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 275.21 47.32"><title>TRUE Diamond Science</title><path d="M135.16,3.76H77.44l-2.37,5.6h45.85c-.29.86-.86,2.09-1.61,2.38H74L68.05,26H84.37L88,17.34h26.29L116.08,26H132.5l-1.71-8.63a5.71,5.71,0,0,0,5.13-3.61l2.37-5.32h0a3.52,3.52,0,0,0,.1-3.22c-.57-1-1.62-1.33-3.23-1.43" style="fill:#FFFFFF"/><path d="M192.4,3.86,186,19a3.5,3.5,0,0,1-1,1.42H156.71a.54.54,0,0,1-.1-.38l6.84-16.13H147.5L140,21.42a3.54,3.54,0,0,0-.1,3.23c.57.85,1.62,1.23,3.33,1.23h51.26A6.26,6.26,0,0,0,200.66,22l7.69-18.13Z" style="fill:#FFFFFF"/><polygon points="270.81 9.27 273.18 3.76 215.09 3.76 206.16 24.84 205.88 25.69 205.78 25.88 263.78 25.88 266.35 19.8 223.82 19.8 224.86 17.24 267.39 17.24 269.67 11.64 227.24 11.64 228.28 9.27 270.81 9.27" style="fill:#FFFFFF"/><polygon points="71.08 3.76 5.39 3.76 3.02 9.27 29.13 9.27 21.91 25.88 38.14 25.88 45.26 9.27 68.71 9.27 71.08 3.76" style="fill:#FFFFFF"/><path d="M120,32.5h3.79c2.89,0,3.93,1.3,3,3.46L124,42.24c-1.09,2.48-3.32,3.67-6.33,3.67h-3.57Zm-2.53,11.6h1.18a2.6,2.6,0,0,0,2.72-1.68L124.2,36c.47-1.05.26-1.66-1.25-1.66h-1.18Z" style="fill:#FFFFFF"/><path d="M130.77,32.5h2.56l-5.88,13.41h-2.56Z" style="fill:#FFFFFF"/><path d="M139.16,32.5h2.13L139.4,45.91h-2.56l.51-2.88h-3.49l-2,2.88h-2.56Zm-.61,3.86h-.06l-3.38,4.86h2.56Z" style="fill:#FFFFFF"/><path d="M146.54,32.5H149l.18,7.1h0l6.38-7.1h2.48l-5.88,13.41h-2.56l3.58-8.16h0l-5.07,5.77h-1.28l0-5.77h0l-3.58,8.16h-2.56Z" style="fill:#FFFFFF"/><path d="M159,35.66c1-2.18,3.65-3.28,5.74-3.28s3.81,1.1,2.85,3.28l-3.1,7.08c-1,2.19-3.65,3.28-5.74,3.28s-3.81-1.09-2.85-3.28Zm-.54,7.08c-.41.93.21,1.36,1.14,1.36a2.48,2.48,0,0,0,2.33-1.36l3.1-7.08c.41-.92-.2-1.36-1.13-1.36a2.48,2.48,0,0,0-2.33,1.36Z" style="fill:#FFFFFF"/><path d="M171.1,32.5h2.46l.32,8.08h.05l3.55-8.08H180l-5.88,13.41h-2.41l-.38-8.06h-.05l-3.54,8.06h-2.56Z" style="fill:#FFFFFF"/><path d="M182.35,32.5h3.79c2.89,0,3.93,1.3,3,3.46l-2.75,6.28c-1.09,2.48-3.32,3.67-6.34,3.67h-3.56Zm-2.53,11.6H181a2.59,2.59,0,0,0,2.72-1.68L186.56,36c.46-1.05.25-1.66-1.26-1.66h-1.18Z" style="fill:#FFFFFF"/><path d="M204.31,36.36h-2.56l.19-.43c.39-.89.21-1.63-1.1-1.63A2.59,2.59,0,0,0,198.35,36c-.45,1-.49,1.39.67,1.79l1.7.58c2,.64,1.94,1.72,1.08,3.67A6.19,6.19,0,0,1,195.65,46c-2.48,0-3.76-1.37-3-3.18l.31-.72h2.56l-.26.6c-.31.7-.1,1.38,1.21,1.38,1.73,0,2.19-.77,2.73-2,.63-1.43.5-1.65-.59-2l-1.58-.57c-1.92-.67-2-1.77-1.28-3.44a6.39,6.39,0,0,1,6-3.68c2.56,0,3.59,1.55,2.94,3Z" style="fill:#FFFFFF"/><path d="M211.48,42.91c-.73,1.66-3.15,3.11-5.43,3.11-2,0-3.91-.71-2.83-3.16l3.24-7.39A6.06,6.06,0,0,1,212,32.38c2.36,0,3.53,1.34,2.72,3.21l-.33.75H211.8l.29-.64c.33-.75,0-1.4-1-1.4a2.25,2.25,0,0,0-2.27,1.57l-3,6.85c-.34.78-.2,1.38.95,1.38a2.34,2.34,0,0,0,2.2-1.36l.29-.66h2.56Z" style="fill:#FFFFFF"/><path d="M218.21,32.5h2.56l-5.88,13.41h-2.56Z" style="fill:#FFFFFF"/><path d="M223.26,32.5h7.63l-.79,1.8H225l-1.73,3.94h4.42l-.8,1.81H222.5L220.78,44h5.07L225,45.91h-7.64Z" style="fill:#FFFFFF"/><path d="M233,32.5h2.46l.32,8.08h.05l3.55-8.08h2.56L236,45.91H233.6l-.39-8.06h0l-3.54,8.06h-2.56Z" style="fill:#FFFFFF"/><path d="M247.77,42.91C247,44.57,244.62,46,242.34,46c-2,0-3.91-.71-2.84-3.16l3.24-7.39a6.07,6.07,0,0,1,5.53-3.09c2.36,0,3.53,1.34,2.71,3.21l-.33.75h-2.56l.28-.64c.33-.75,0-1.4-1-1.4a2.26,2.26,0,0,0-2.27,1.57l-3,6.85c-.34.78-.2,1.38,1,1.38a2.34,2.34,0,0,0,2.2-1.36l.29-.66h2.56Z" style="fill:#FFFFFF"/><path d="M254.42,32.5h7.64l-.8,1.8h-5.07l-1.73,3.94h4.42l-.79,1.81h-4.42L251.94,44H257l-.84,1.92h-7.63Z" style="fill:#FFFFFF"/></svg>' . '</a>';
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