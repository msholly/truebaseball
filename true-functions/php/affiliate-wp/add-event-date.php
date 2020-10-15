<?php
/**
 * Plugin Name: AffiliateWP - Add event field to the affiliate registration form
 * Plugin URI: https://affiliatewp.com
 * Description: Add a custom field (event) to the affiliate registration form
 * Version: 1.0
 */

/*
 * Add the event field to the affiliate registration form
 */
function affwp_add_event_field_to_affiliate_registration_form() {

	$errors = affiliate_wp()->register->get_errors();

	if ( ! array_key_exists( 'empty_event', $errors ) ) {
		$event = sanitize_text_field( $_POST['affwp_event'] );
	}

	?>
	<p>
		<label for="affwp-event">Requested Event Date</label>
		<input id="affwp-event" type="text" name="affwp_event" value="<?php if ( ! empty( $event ) ) {
			echo $event;
		} ?>" title="event" />
	</p>
	<?php
}
// add_action( 'affwp_register_fields_before_tos', 'affwp_add_event_field_to_affiliate_registration_form' );

/*
 * Save the event to the affiliate meta after registration
 */
function affwp_save_affiliate_event( $affiliate_id, $status, $args ) {

	$event = sanitize_text_field( $_POST['affwp_event'] );

	if ( ! empty( $event ) ) {
		affwp_add_affiliate_meta( $affiliate_id, 'event', $event );
	}

}
// add_action( 'affwp_register_user', 'affwp_save_affiliate_event', 10, 3 );

/*
 * Make event field required during affiliate registration {Remove if it shouldn't be required}
 */
function affwp_add_event_to_required_fields( $required_fields ) {

	$required_fields['affwp_event'] = array(
		'error_id'      => 'empty_event',
		'error_message' => 'Please enter your event',
	);

	return $required_fields;
}
// add_filter( 'affwp_register_required_fields', 'affwp_add_event_to_required_fields' );

/*
 * Display the event field in the Profile Settings page in the affiliate dashboard
 */
function affwp_show_event_in_affiliate_dashboard( $affiliate_id, $affiliate_user_id ) {

	$event = affwp_get_affiliate_meta( $affiliate_id, 'event', true );

	?>

	<div class="affwp-wrap affwp-event-wrap">
		<label for="affwp-event"><?php _e( 'Your event', 'affiliate-wp' ); ?></label>
		<input id="affwp-event" type="text" name="event" value="<?php echo esc_attr( $event ); ?>" />
	</div>

	<?php

}
// add_action( 'affwp_affiliate_dashboard_before_submit', 'affwp_show_event_in_affiliate_dashboard', 10, 2 );

/*
 * Update the event field from the Profile Settings page in the affiliate dashboard
 */
function affwp_affiliate_dashboard_update_event( $data ) {

	$affiliate_id = absint( $data['affiliate_id'] );

	if ( ! empty( $data['event'] ) ) {

		$event = sanitize_text_field( $data['event'] );

		affwp_update_affiliate_meta( $affiliate_id, 'event', $event );

	} else {

		affwp_delete_affiliate_meta( $affiliate_id, 'event' );

	}

}
// add_action( 'affwp_update_affiliate_profile_settings', 'affwp_affiliate_dashboard_update_event' );

/*
 * Display the event field in the edit affiliate page in the admin dashboard
 */
function affwp_admin_edit_affiliate_show_event( $affiliate ) {

	$event = affwp_get_affiliate_meta( $affiliate->affiliate_id, 'event', true );
	?>

	<tr class="form-row form-required">

		<th scope="row">
			<label for="payment_email">Requested Event Date</label>
		</th>

		<td>
			<input class="regular-text" type="text" name="event" id="event" value="<?php echo esc_attr( $event ); ?>" />
			<p class="description">The affiliate's requested event date</p>
		</td>

	</tr>

	<?php
}
add_action( 'affwp_edit_affiliate_end', 'affwp_admin_edit_affiliate_show_event' );

/*
 * Update the affiliate event field from the edit affiliate page in the admin dashboard
 */
function affwp_admin_update_affiliate_event( $affiliate, $updated ) {

	if ( $updated ) {

		$affiliate_id = $affiliate->affiliate_id;

		if ( ! empty( $_POST['event'] ) ) {

			$event = sanitize_text_field( $_POST['event'] );

			affwp_update_affiliate_meta( $affiliate_id, 'event', $event );

		} else {

			affwp_delete_affiliate_meta( $affiliate_id, 'event' );

		}
	}

}
add_action( 'affwp_updated_affiliate', 'affwp_admin_update_affiliate_event', 10, 2 );