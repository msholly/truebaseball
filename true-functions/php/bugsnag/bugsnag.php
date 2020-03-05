<?php

function configure_bugsnag() {
    global $bugsnagWordpress;

    if ( strpos($_SERVER['HTTP_HOST'], 'local') ) {
        $bugsnag_env = 'development';
    } else if ( strpos($_SERVER['HTTP_HOST'], 'flywheelstaging') ) {
        $bugsnag_env = 'staging';
    } else {
        $bugsnag_env = 'production';
    }

    $bugsnagWordpress->setReleaseStage($bugsnag_env);

}

if( class_exists( 'Bugsnag_Wordpress' ) ) {
    configure_bugsnag();
}