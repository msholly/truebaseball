<?php
/**
 * Plugin Name: AffiliateWP - Add City field to the affiliate registration form
 * Plugin URI: https://affiliatewp.com
 * Description: Add a custom field (City) to the affiliate registration form
 * Version: 1.0
 */

/*
 * Add the City field to the affiliate registration form
 */
function affwp_add_city_field_to_affiliate_registration_form() {

	$errors = affiliate_wp()->register->get_errors();

	if ( ! array_key_exists( 'empty_city', $errors ) ) {
		$city = sanitize_text_field( $_POST['affwp_city'] );
	}

	?>
	<p>
		<label for="affwp-city">City</label>
		<input id="affwp-city" type="text" name="affwp_city" value="<?php if ( ! empty( $city ) ) {
			echo $city;
		} ?>" title="City" />
	</p>
	<?php
}
add_action( 'affwp_register_fields_before_tos', 'affwp_add_city_field_to_affiliate_registration_form' );

/*
 * Save the City to the affiliate meta after registration
 */
function affwp_save_affiliate_city( $affiliate_id, $status, $args ) {

	$city = sanitize_text_field( $_POST['affwp_city'] );

	if ( ! empty( $city ) ) {
		affwp_add_affiliate_meta( $affiliate_id, 'city', $city );
	}

}
add_action( 'affwp_register_user', 'affwp_save_affiliate_city', 10, 3 );

/*
 * Make City field required during affiliate registration {Remove if it shouldn't be required}
 */
function affwp_add_city_to_required_fields( $required_fields ) {

	$required_fields['affwp_city'] = array(
		'error_id'      => 'empty_city',
		'error_message' => 'Please enter your City',
	);

	return $required_fields;
}
add_filter( 'affwp_register_required_fields', 'affwp_add_city_to_required_fields' );

/*
 * Display the City field in the Profile Settings page in the affiliate dashboard
 */
function affwp_show_city_in_affiliate_dashboard( $affiliate_id, $affiliate_user_id ) {

	$city = affwp_get_affiliate_meta( $affiliate_id, 'city', true );

	?>

	<div class="affwp-wrap affwp-city-wrap">
		<label for="affwp-city"><?php _e( 'Your City', 'affiliate-wp' ); ?></label>
		<input id="affwp-city" type="text" name="city" value="<?php echo esc_attr( $city ); ?>" />
	</div>

	<?php

}
add_action( 'affwp_affiliate_dashboard_before_submit', 'affwp_show_city_in_affiliate_dashboard', 10, 2 );

/*
 * Update the City field from the Profile Settings page in the affiliate dashboard
 */
function affwp_affiliate_dashboard_update_city( $data ) {

	$affiliate_id = absint( $data['affiliate_id'] );

	if ( ! empty( $data['city'] ) ) {

		$city = sanitize_text_field( $data['city'] );

		affwp_update_affiliate_meta( $affiliate_id, 'city', $city );

	} else {

		affwp_delete_affiliate_meta( $affiliate_id, 'city' );

	}

}
add_action( 'affwp_update_affiliate_profile_settings', 'affwp_affiliate_dashboard_update_city' );

/*
 * Display the City field in the edit affiliate page in the admin dashboard
 */
function affwp_admin_edit_affiliate_show_city( $affiliate ) {

	$city = affwp_get_affiliate_meta( $affiliate->affiliate_id, 'city', true );
	?>

	<tr class="form-row form-required">

		<th scope="row">
			<label for="payment_email">City</label>
		</th>

		<td>
			<input class="regular-text" type="text" name="city" id="city" value="<?php echo esc_attr( $city ); ?>" />
			<p class="description">The affiliate's City</p>
		</td>

	</tr>

	<?php
}
add_action( 'affwp_edit_affiliate_end', 'affwp_admin_edit_affiliate_show_city' );

/*
 * Update the affiliate City field from the edit affiliate page in the admin dashboard
 */
function affwp_admin_update_affiliate_city( $affiliate, $updated ) {

	if ( $updated ) {

		$affiliate_id = $affiliate->affiliate_id;

		if ( ! empty( $_POST['city'] ) ) {

			$city = sanitize_text_field( $_POST['city'] );

			affwp_update_affiliate_meta( $affiliate_id, 'city', $city );

		} else {

			affwp_delete_affiliate_meta( $affiliate_id, 'city' );

		}
	}

}
add_action( 'affwp_updated_affiliate', 'affwp_admin_update_affiliate_city', 10, 2 );