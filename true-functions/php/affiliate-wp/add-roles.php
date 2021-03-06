<?php

add_role(
    'affiliate',
    __('Affiliate'),
    array(
        'read'         => true,  // true allows this capability
    )
);

add_role(
    'sales_rep',
    __('Sales Rep'),
    array(
        'read'         => true,  // true allows this capability
    )
);

function pw_affwp_set_role_on_registration($affiliate_id = 0)
{
    $user_id = affwp_get_affiliate_user_id($affiliate_id);
    $user = new WP_User($user_id);
    $user->add_role('affiliate');
}
add_action('affwp_insert_affiliate', 'pw_affwp_set_role_on_registration');

// Add role to admin body html
add_filter('admin_body_class', function ($classes) {
    $user = wp_get_current_user();
    foreach ($user->roles as $user_role) {
        $classes .= ' ' . 'role-' . $user_role . ' ';
    }
    return $classes;
});
