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

function project_dequeue_unnecessary_styles() {
  wp_dequeue_style( 'common-css' );
  wp_deregister_style( 'common-css' );
}
if ( ! is_admin() ) add_action( 'wp_enqueue_scripts', 'project_dequeue_unnecessary_styles' );
