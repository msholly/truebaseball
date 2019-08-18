<?php
/**
 * Plugin Name: AffiliateWP - Add Address 2 field to the affiliate registration form
 * Plugin URI: https://affiliatewp.com
 * Description: Add a custom field (Address 2) to the affiliate registration form
 * Version: 1.0
 */

/*
 * Add the Address 2 field to the affiliate registration form
 */
function affwp_add_address_2_field_to_affiliate_registration_form() {

	$errors = affiliate_wp()->register->get_errors();

	if ( ! array_key_exists( 'empty_address_2', $errors ) ) {
		$address_2 = sanitize_text_field( $_POST['affwp_address_2'] );
	}

	?>
	<p>
		<label for="affwp-address-2">Address 2</label>
		<input id="affwp-address-2" type="text" name="affwp_address_2" value="<?php if ( ! empty( $address_2 ) ) {
			echo $address_2;
		} ?>" title="Address 2" />
	</p>
	<?php
}
add_action( 'affwp_register_fields_before_tos', 'affwp_add_address_2_field_to_affiliate_registration_form' );

/*
 * Save the Address 2 to the affiliate meta after registration
 */
function affwp_save_affiliate_address_2( $affiliate_id, $status, $args ) {

	$address_2 = sanitize_text_field( $_POST['affwp_address_2'] );

	if ( ! empty( $address_2 ) ) {
		affwp_add_affiliate_meta( $affiliate_id, 'address_2', $address_2 );
	}

}
add_action( 'affwp_register_user', 'affwp_save_affiliate_address_2', 10, 3 );

/*
 * Make Address 2 field required during affiliate registration {Remove if it shouldn't be required}
 */
function affwp_add_address_2_to_required_fields( $required_fields ) {

	$required_fields['affwp_address_2'] = array(
		'error_id'      => 'empty_address_2',
		'error_message' => 'Please enter your Address 2',
	);

	return $required_fields;
}
add_filter( 'affwp_register_required_fields', 'affwp_add_address_2_to_required_fields' );

/*
 * Display the Address 2 field in the Profile Settings page in the affiliate dashboard
 */
function affwp_show_address_2_in_affiliate_dashboard( $affiliate_id, $affiliate_user_id ) {

	$address_2 = affwp_get_affiliate_meta( $affiliate_id, 'address_2', true );

	?>

	<div class="affwp-wrap affwp-address-2-wrap">
		<label for="affwp-address-2"><?php _e( 'Your Address 2', 'affiliate-wp' ); ?></label>
		<input id="affwp-address-2" type="text" name="address_2" value="<?php echo esc_attr( $address_2 ); ?>" />
	</div>

	<?php

}
add_action( 'affwp_affiliate_dashboard_before_submit', 'affwp_show_address_2_in_affiliate_dashboard', 10, 2 );

/*
 * Update the Address 2 field from the Profile Settings page in the affiliate dashboard
 */
function affwp_affiliate_dashboard_update_address_2( $data ) {

	$affiliate_id = absint( $data['affiliate_id'] );

	if ( ! empty( $data['address_2'] ) ) {

		$address_2 = sanitize_text_field( $data['address_2'] );

		affwp_update_affiliate_meta( $affiliate_id, 'address_2', $address_2 );

	} else {

		affwp_delete_affiliate_meta( $affiliate_id, 'address_2' );

	}

}
add_action( 'affwp_update_affiliate_profile_settings', 'affwp_affiliate_dashboard_update_address_2' );

/*
 * Display the Address 2 field in the edit affiliate page in the admin dashboard
 */
function affwp_admin_edit_affiliate_show_address_2( $affiliate ) {

	$address_2 = affwp_get_affiliate_meta( $affiliate->affiliate_id, 'address_2', true );
	?>

	<tr class="form-row form-required">

		<th scope="row">
			<label for="payment_email">Address 2</label>
		</th>

		<td>
			<input class="regular-text" type="text" name="address_2" id="address_2" value="<?php echo esc_attr( $address_2 ); ?>" />
			<p class="description">The affiliate Address 2</p>
		</td>

	</tr>

	<?php
}
add_action( 'affwp_edit_affiliate_end', 'affwp_admin_edit_affiliate_show_address_2' );

/*
 * Update the affiliate Address 2 field from the edit affiliate page in the admin dashboard
 */
function affwp_admin_update_affiliate_address_2( $affiliate, $updated ) {

	if ( $updated ) {

		$affiliate_id = $affiliate->affiliate_id;

		if ( ! empty( $_POST['address_2'] ) ) {

			$address_2 = sanitize_text_field( $_POST['address_2'] );

			affwp_update_affiliate_meta( $affiliate_id, 'address_2', $address_2 );

		} else {

			affwp_delete_affiliate_meta( $affiliate_id, 'address_2' );

		}
	}

}
add_action( 'affwp_updated_affiliate', 'affwp_admin_update_affiliate_address_2', 10, 2 );