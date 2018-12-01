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
	    wp_enqueue_style( 'fl-child-theme', FL_CHILD_THEME_URL . '/dist/styles/main.css' );
    }

    static public function enqueue_js()
    {
	    wp_enqueue_script( 'fl-child-theme', FL_CHILD_THEME_URL . '/dist/scripts/main.js' );
    }

}
