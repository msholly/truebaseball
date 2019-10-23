<?php

/**
 * Remove the "Clear log"-button, so a user with admin access can not clear the log
 * and wipe their mischievous behavior from the log.
 */
add_filter('simple_history/user_can_clear_log', function ($user_can_clear_log) {
    $user_can_clear_log = false;
    return $user_can_clear_log;
});


