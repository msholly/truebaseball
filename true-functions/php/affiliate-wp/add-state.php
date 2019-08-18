<?php
/**
 * Plugin Name: AffiliateWP - Add state field to the affiliate registration form
 * Plugin URI: https://affiliatewp.com
 * Description: Add a custom field (state) to the affiliate registration form
 * Version: 1.0
 */

/*
 * Add the state field to the affiliate registration form
 */
function affwp_add_state_field_to_affiliate_registration_form() {

	$errors = affiliate_wp()->register->get_errors();

	if ( ! array_key_exists( 'empty_state', $errors ) ) {
		$state = sanitize_text_field( $_POST['affwp_state'] );
	}

	?>
	<p>
		<label for="affwp-state">State</label>
		<input id="affwp-state" type="text" name="affwp_state" value="<?php if ( ! empty( $state ) ) {
			echo $state;
		} ?>" title="state" />
	</p>
	<?php
}
add_action( 'affwp_register_fields_before_tos', 'affwp_add_state_field_to_affiliate_registration_form' );

/*
 * Save the state to the affiliate meta after registration
 */
function affwp_save_affiliate_state( $affiliate_id, $status, $args ) {

	$state = sanitize_text_field( $_POST['affwp_state'] );

	if ( ! empty( $state ) ) {
		affwp_add_affiliate_meta( $affiliate_id, 'state', $state );
	}

}
add_action( 'affwp_register_user', 'affwp_save_affiliate_state', 10, 3 );

/*
 * Make state field required during affiliate registration {Remove if it shouldn't be required}
 */
function affwp_add_state_to_required_fields( $required_fields ) {

	$required_fields['affwp_state'] = array(
		'error_id'      => 'empty_state',
		'error_message' => 'Please enter your state',
	);

	return $required_fields;
}
add_filter( 'affwp_register_required_fields', 'affwp_add_state_to_required_fields' );

/*
 * Display the state field in the Profile Settings page in the affiliate dashboard
 */
function affwp_show_state_in_affiliate_dashboard( $affiliate_id, $affiliate_user_id ) {

	$state = affwp_get_affiliate_meta( $affiliate_id, 'state', true );

	?>

	<div class="affwp-wrap affwp-state-wrap">
		<label for="affwp-state"><?php _e( 'Your state', 'affiliate-wp' ); ?></label>
		<input id="affwp-state" type="text" name="state" value="<?php echo esc_attr( $state ); ?>" />
	</div>

	<?php

}
add_action( 'affwp_affiliate_dashboard_before_submit', 'affwp_show_state_in_affiliate_dashboard', 10, 2 );

/*
 * Update the state field from the Profile Settings page in the affiliate dashboard
 */
function affwp_affiliate_dashboard_update_state( $data ) {

	$affiliate_id = absint( $data['affiliate_id'] );

	if ( ! empty( $data['state'] ) ) {

		$state = sanitize_text_field( $data['state'] );

		affwp_update_affiliate_meta( $affiliate_id, 'state', $state );

	} else {

		affwp_delete_affiliate_meta( $affiliate_id, 'state' );

	}

}
add_action( 'affwp_update_affiliate_profile_settings', 'affwp_affiliate_dashboard_update_state' );

/*
 * Display the state field in the edit affiliate page in the admin dashboard
 */
function affwp_admin_edit_affiliate_show_state( $affiliate ) {

	$state = affwp_get_affiliate_meta( $affiliate->affiliate_id, 'state', true );
	?>

	<tr class="form-row form-required">

		<th scope="row">
			<label for="payment_email">State</label>
		</th>

		<td>
			<input class="regular-text" type="text" name="state" id="state" value="<?php echo esc_attr( $state ); ?>" />
			<p class="description">The affiliate's state</p>
		</td>

	</tr>

	<?php
}
add_action( 'affwp_edit_affiliate_end', 'affwp_admin_edit_affiliate_show_state' );

/*
 * Update the affiliate state field from the edit affiliate page in the admin dashboard
 */
function affwp_admin_update_affiliate_state( $affiliate, $updated ) {

	if ( $updated ) {

		$affiliate_id = $affiliate->affiliate_id;

		if ( ! empty( $_POST['state'] ) ) {

			$state = sanitize_text_field( $_POST['state'] );

			affwp_update_affiliate_meta( $affiliate_id, 'state', $state );

		} else {

			affwp_delete_affiliate_meta( $affiliate_id, 'state' );

		}
	}

}
add_action( 'affwp_updated_affiliate', 'affwp_admin_update_affiliate_state', 10, 2 );