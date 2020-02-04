<?php

/**
 * Helper class for child theme functions.
 *
 * @class FLChildTheme
 */
final class FLChildTheme {

    /**
	 * Enqueues scripts and styles.
	 *
     * @return void
     */
    static public function enqueue_scripts()
    {
	    wp_enqueue_style( 'fl-child-theme', FL_CHILD_THEME_URL . '/style.min.css', array(), filemtime( get_stylesheet_directory() . '/style.min.css' ) );
    }

    static public function enqueue_js()
    {

      // TO DO, CONDITIONAL LOADING BASED ON ENVIRONMENT
      wp_register_script( "fl-child-theme", FL_CHILD_THEME_URL. '/assets/js/custom.js', array('jquery'), filemtime( get_stylesheet_directory() . '/assets/js/custom.js' ), true );
      // wp_register_script( "fl-child-theme", FL_CHILD_THEME_URL. '/assets/js/custom.min.js', array('jquery'), '1.0.0', true );

      wp_localize_script( 'fl-child-theme', 'truefunction', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'siteurl' => get_option('siteurl')
      ));

      // wp_enqueue_script( 'fl-child-theme-vendor', FL_CHILD_THEME_URL . '/assets/js/vendor.min.js' );
      wp_enqueue_script( 'fl-child-theme-vendor', FL_CHILD_THEME_URL . '/assets/js/vendor.js' );

	    // wp_enqueue_script( 'fl-child-theme', FL_CHILD_THEME_URL . '/assets/js/custom.min.js' );
      // wp_enqueue_script( 'fl-child-theme', FL_CHILD_THEME_URL . '/assets/js/custom.js', array(), '1.0.0', true );
      wp_enqueue_script( 'fl-child-theme' );

      // Oliver Scripts
      if (is_page_template('page-oliver-pos.php')) {
        wp_enqueue_script( 'google-geocode', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAi9OGYa4-u59XxGQCR8xIT2GJDWs9AzMc&libraries=places', array('jquery'), '1.0', true );
        wp_enqueue_script( 'jquery-geocomplete', FL_CHILD_THEME_URL . '/assets/js/map/jquery.geocomplete.min.js', array('jquery'), filemtime( get_stylesheet_directory() . '/assets/js/map/jquery.geocomplete.min.js' ), true );
        
        // TO-DO Rename map directory
        wp_enqueue_script( 'oliver-pos', FL_CHILD_THEME_URL . '/assets/js/map/oliver-pos.js', array('jquery'), filemtime( get_stylesheet_directory() . '/assets/js/map/oliver-pos.js' ), true );

      }
      

    }

}
