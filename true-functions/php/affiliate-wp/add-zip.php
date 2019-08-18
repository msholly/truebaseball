<?php
/**
 * Plugin Name: AffiliateWP - Add zip field to the affiliate registration form
 * Plugin URI: https://affiliatewp.com
 * Description: Add a custom field (zip) to the affiliate registration form
 * Version: 1.0
 */

/*
 * Add the zip field to the affiliate registration form
 */
function affwp_add_zip_field_to_affiliate_registration_form() {

	$errors = affiliate_wp()->register->get_errors();

	if ( ! array_key_exists( 'empty_zip', $errors ) ) {
		$zip = sanitize_text_field( $_POST['affwp_zip'] );
	}

	?>
	<p>
		<label for="affwp-zip">Zip</label>
		<input id="affwp-zip" type="text" name="affwp_zip" value="<?php if ( ! empty( $zip ) ) {
			echo $zip;
		} ?>" title="zip" />
	</p>
	<?php
}
add_action( 'affwp_register_fields_before_tos', 'affwp_add_zip_field_to_affiliate_registration_form' );

/*
 * Save the zip to the affiliate meta after registration
 */
function affwp_save_affiliate_zip( $affiliate_id, $status, $args ) {

	$zip = sanitize_text_field( $_POST['affwp_zip'] );

	if ( ! empty( $zip ) ) {
		affwp_add_affiliate_meta( $affiliate_id, 'zip', $zip );
	}

}
add_action( 'affwp_register_user', 'affwp_save_affiliate_zip', 10, 3 );

/*
 * Make zip field required during affiliate registration {Remove if it shouldn't be required}
 */
function affwp_add_zip_to_required_fields( $required_fields ) {

	$required_fields['affwp_zip'] = array(
		'error_id'      => 'empty_zip',
		'error_message' => 'Please enter your zip',
	);

	return $required_fields;
}
add_filter( 'affwp_register_required_fields', 'affwp_add_zip_to_required_fields' );

/*
 * Display the zip field in the Profile Settings page in the affiliate dashboard
 */
function affwp_show_zip_in_affiliate_dashboard( $affiliate_id, $affiliate_user_id ) {

	$zip = affwp_get_affiliate_meta( $affiliate_id, 'zip', true );

	?>

	<div class="affwp-wrap affwp-zip-wrap">
		<label for="affwp-zip"><?php _e( 'Your zip', 'affiliate-wp' ); ?></label>
		<input id="affwp-zip" type="text" name="zip" value="<?php echo esc_attr( $zip ); ?>" />
	</div>

	<?php

}
add_action( 'affwp_affiliate_dashboard_before_submit', 'affwp_show_zip_in_affiliate_dashboard', 10, 2 );

/*
 * Update the zip field from the Profile Settings page in the affiliate dashboard
 */
function affwp_affiliate_dashboard_update_zip( $data ) {

	$affiliate_id = absint( $data['affiliate_id'] );

	if ( ! empty( $data['zip'] ) ) {

		$zip = sanitize_text_field( $data['zip'] );

		affwp_update_affiliate_meta( $affiliate_id, 'zip', $zip );

	} else {

		affwp_delete_affiliate_meta( $affiliate_id, 'zip' );

	}

}
add_action( 'affwp_update_affiliate_profile_settings', 'affwp_affiliate_dashboard_update_zip' );

/*
 * Display the zip field in the edit affiliate page in the admin dashboard
 */
function affwp_admin_edit_affiliate_show_zip( $affiliate ) {

	$zip = affwp_get_affiliate_meta( $affiliate->affiliate_id, 'zip', true );
	?>

	<tr class="form-row form-required">

		<th scope="row">
			<label for="payment_email">Zip</label>
		</th>

		<td>
			<input class="regular-text" type="text" name="zip" id="zip" value="<?php echo esc_attr( $zip ); ?>" />
			<p class="description">The affiliate's zip</p>
		</td>

	</tr>

	<?php
}
add_action( 'affwp_edit_affiliate_end', 'affwp_admin_edit_affiliate_show_zip' );

/*
 * Update the affiliate zip field from the edit affiliate page in the admin dashboard
 */
function affwp_admin_update_affiliate_zip( $affiliate, $updated ) {

	if ( $updated ) {

		$affiliate_id = $affiliate->affiliate_id;

		if ( ! empty( $_POST['zip'] ) ) {

			$zip = sanitize_text_field( $_POST['zip'] );

			affwp_update_affiliate_meta( $affiliate_id, 'zip', $zip );

		} else {

			affwp_delete_affiliate_meta( $affiliate_id, 'zip' );

		}
	}

}
add_action( 'affwp_updated_affiliate', 'affwp_admin_update_affiliate_zip', 10, 2 );