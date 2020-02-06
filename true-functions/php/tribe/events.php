<?php

// DISABLE AUTOMATIC SKU GENERATION
add_filter('event_tickets_woo_should_default_ticket_sku', false);


// Add a Woo Product Category on ticket save
add_action('event_tickets_after_save_ticket', 'tribe_events_add_product_category_to_tickets', 10, 4);

function tribe_events_add_product_category_to_tickets($event_id, $ticket, $raw_data, $classname)
{

    if (!empty($ticket) && isset($ticket->ID)) {
        wp_add_object_terms($ticket->ID, 'tickets', 'product_cat');
        // And set thumbnail for MC Abandoned Cart Automation
        set_post_thumbnail( $ticket->ID, 2964 );
        // Set custom thank you page ID
        update_post_meta( $ticket->ID, '_custom_thank_you_page', 8971);
        update_post_meta( $ticket->ID, '_custom_thank_you_page_priority', 10);
    }
}

// Register Manage Categories capabilities for User Roles Plugin
add_filter('tribe_events_register_event_cat_type_args', function ($args) {
    $args['capabilities'] = [
        'manage_terms' => 'edit_tribe_events',
        'edit_terms'   => 'edit_tribe_events',
        'delete_terms' => 'edit_tribe_events',
        'assign_terms' => 'edit_tribe_events',
    ];
    return $args;
});

// Set time increment to 20
add_filter('tribe_events_meta_box_timepicker_step', 'tribe_twenty_minute_timepicker');

function tribe_twenty_minute_timepicker()
{
    return 10;
}

// Programmatically set Event Information
// add_action('save_post', 'true_event_automation', 100, 2);

// function true_event_automation($post_id)
// {

//     $this_post = get_post($post_id);

//     if ($this_post->post_status == 'publish' && $this_post->post_type == 'tribe_events') {

//         $new_event_title = '';
//         $new_event_content = '';

//         // EVENT INFORMATION
//         $event_meta = tribe_get_event($post_id);
//         $event_start = tribe_get_start_time($post_id, 'g:ia');
//         $event_end = tribe_get_end_time($post_id, 'g:ia');
//         $venue_name = $event_meta->venues[0]->post_title;
//         $event_tags = wp_get_post_tags($post_id, array('fields' => 'slugs'));

//         // URL SLUG
//         $new_slug = sanitize_title($venue_name . '-' . tribe_get_start_date($post_id, false, 'M j') . '-' . $event_start . '-' . $event_end);
//         $new_slug = wp_unique_post_slug($new_slug, $post_id, $this_post->post_status, $this_post->post_type, $this_post->post_parent);

//         // FOR CHILD EVENTS
//         if (in_array("child", $event_tags)) {
//             $new_event_title = $venue_name . " | TRUE Bat Hit+Fit Challenge | " . $event_start . " - " . $event_end;
//             $new_event_content = ''; // No content for child events
//         } else {
//             // error_log("Couldn't find CHILD tag", 0);
//         }
//         if (in_array("parent", $event_tags)) {
//             // For parent cal shortcode
//             $primary_event_cat = get_primary_taxonomy_term($post_id, 'tribe_events_cat');

//             $new_event_title = $venue_name . " | TRUE Bat Hit+Fit Challenge";

//             $cat_slug = $primary_event_cat['slug'];
//             $new_event_content = "[ecs-list-events cat='" . $cat_slug . "' exclude_tag='parent' tag_cat_operator='AND' hide_soldout='true' design='compact' fgthumb='white' bgthumb='#00aff0' contentorder='date_thumb, thumbnail, title, date, venue, time, excerpt, button' button='Register Now' limit='-1']";
//         } else {
//             // error_log("Couldn't find PARENT tag", 0);
//         }

//         // Check for proper tags
//         if (in_array("parent", $event_tags)) {

//             if (in_array("child", $event_tags)) {
//                 error_log(json_encode($event_tags, 0));
//                 // if there's a child one too
//                 add_flash_notice(__("CHILD and PARENT tags can't be used together"), "error", true);
//             }

//             // if (!in_array("A", $event_tags) || !in_array("B", $event_tags)) {
//             //     // if a parent tag has no Funnel Type
//             //     add_flash_notice(__("A or B type of event is required."), "error", true);
//             // }

//             if ($primary_event_cat['slug'] == "true-fitting") {
//                 // If Categories are set incorrectly
//                 error_log(json_encode($primary_event_cat, 0));
//                 add_flash_notice(__("A Primary Event Category is required. This category should have a date and only for this series of events."), "error", true);
//             }
//         }

//         if (in_array("child", $event_tags)) {
//             if (in_array("a", $event_tags)) {
//                 // if a parent tag has no Funnel Type
//                 add_flash_notice(__("A type of event should not be used on child events."), "error", true);
//             }
//             if (in_array("b", $event_tags)) {
//                 // if a parent tag has no Funnel Type
//                 add_flash_notice(__("B type of event should not be used on child events."), "error", true);
//             }
//         }

//         if (!in_array("facility", $event_tags) && !in_array("league", $event_tags)) {
//             add_flash_notice(__("LEAGUE or FACILITY tags are required for all events."), "error", true);
//         }

//         // error_log(json_encode($this_post->post_date, 0));

//         // Unhook this function so it doesn't loop infinitely
//         remove_action('save_post', 'true_event_automation', 100);

//         // Update the post, which calls save_post again
//         // Only update new events past Nov 6 to avoid updating old event URLs
//         if (strtotime($this_post->post_date) < mktime(0, 0, 0, 11, 6, 2019)) {
//             $event_post = array(
//                 'ID'           => $post_id,
//                 'post_title'   => $new_event_title,
//                 'post_content' => $new_event_content
//             );
//         } else {
//             $event_post = array(
//                 'ID'           => $post_id,
//                 'post_title'   => $new_event_title,
//                 'post_content' => $new_event_content,
//                 'post_name'    => $new_slug
//             );
//         }

//         // TEMP LOGGING IN CASE SOMETHING GOES SOUTH
//         error_log("true_event_automation post id = " . $post_id , 0);

//         wp_update_post($event_post, true);
//         if (is_wp_error($post_id)) {
//             $errors = $post_id->get_error_messages();
//             foreach ($errors as $error) {
//                 echo $error;
//             }
//         }

//         // re-hook this function
//         add_action('save_post', 'true_event_automation', 100);
//     }
// }

/**
 * Returns the primary term for the chosen taxonomy set by Yoast SEO
 * or the first term selected.
 *
 * @link https://www.tannerrecord.com/how-to-get-yoasts-primary-category/
 * @param integer $post The post id.
 * @param string  $taxonomy The taxonomy to query. Defaults to category.
 * @return array The term with keys of 'title', 'slug', and 'url'.
 */
function get_primary_taxonomy_term($post = 0, $taxonomy = 'category')
{
    if (!$post) {
        $post = get_the_ID();
    }
    $terms        = get_the_terms($post, $taxonomy);
    $primary_term = array();
    if ($terms) {
        $term_display = '';
        $term_slug    = '';
        $term_link    = '';
        if (class_exists('WPSEO_Primary_Term')) {
            $wpseo_primary_term = new WPSEO_Primary_Term($taxonomy, $post);
            $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
            $term               = get_term($wpseo_primary_term);
            if (is_wp_error($term)) {
                $term_display = $terms[0]->name;
                $term_slug    = $terms[0]->slug;
                $term_link    = get_term_link($terms[0]->term_id);
            } else {
                $term_display = $term->name;
                $term_slug    = $term->slug;
                $term_link    = get_term_link($term->term_id);
            }
        } else {
            $term_display = $terms[0]->name;
            $term_slug    = $terms[0]->slug;
            $term_link    = get_term_link($terms[0]->term_id);
        }
        $primary_term['url']   = $term_link;
        $primary_term['slug']  = $term_slug;
        $primary_term['title'] = $term_display;
    }
    return $primary_term;
}


/**
 * Add a flash notice to {prefix}options table until a full page refresh is done
 *
 * @param string $notice our notice message
 * @param string $type This can be "info", "warning", "error" or "success", "warning" as default
 * @param boolean $dismissible set this to TRUE to add is-dismissible functionality to your notice
 * @return void
 */

function add_flash_notice($notice = "", $type = "warning", $dismissible = true)
{
    // Here we return the notices saved on our option, if there are not notices, then an empty array is returned
    $notices = get_option("my_flash_notices", array());

    $dismissible_text = ($dismissible) ? "is-dismissible" : "";

    // We add our new notice.
    array_push($notices, array(
        "notice" => $notice,
        "type" => $type,
        "dismissible" => $dismissible_text
    ));

    // Then we update the option with our notices array
    update_option("my_flash_notices", $notices);
}

/**
 * Function executed when the 'admin_notices' action is called, here we check if there are notices on
 * our database and display them, after that, we remove the option to prevent notices being displayed forever.
 * @return void
 */

function display_flash_notices()
{
    $notices = get_option("my_flash_notices", array());

    // Iterate through our notices to be displayed and print them.
    foreach ($notices as $notice) {
        printf(
            '<div class="notice notice-%1$s %2$s"><p>%3$s</p></div>',
            $notice['type'],
            $notice['dismissible'],
            $notice['notice']
        );
    }

    // Now we reset our options to prevent notices being displayed forever.
    if (!empty($notices)) {
        delete_option("my_flash_notices", array());
    }
}

// We add our display_flash_notices function to the admin_notices
// add_action('admin_notices', 'display_flash_notices', 12);


function true_admin_event_js_enqueue($hook_suffix)
{
    $cpt = 'tribe_events';

    if (in_array($hook_suffix, array('post.php', 'post-new.php'))) {
        $screen = get_current_screen();

        if (is_object($screen) && $cpt == $screen->post_type) {

            $jsString = plugin_dir_path( __FILE__ ) . 'js/true-admin-event.js';
            // Register, enqueue scripts and styles here
            wp_enqueue_script( 'true_admin_event', plugins_url( 'js/true-admin-event.js', __FILE__ ), array('jquery'), filemtime($jsString), true );

        }
    }
}

add_action('admin_enqueue_scripts', 'true_admin_event_js_enqueue');

/**
 * Example for adding event data to WooCommerce checkout for Events Calendar tickets.
 * @link http://theeventscalendar.com/support/forums/topic/event-title-and-date-in-cart/
 */
add_filter( 'woocommerce_cart_item_name', 'woocommerce_cart_item_name_event_title', 10, 3 );

function woocommerce_cart_item_name_event_title( $title, $values, $cart_item_key ) {
	$ticket_meta = get_post_meta( $values['product_id'] );
	$event_id = absint( $ticket_meta['_tribe_wooticket_for_event'][0] );

	if ( $event_id ) {
		$title = sprintf( '%s at <a href="%s" target="_blank"> <strong> %s</strong></a>', $title, get_permalink( $event_id ), get_the_title( $event_id ) );
	}

	return $title;
}
