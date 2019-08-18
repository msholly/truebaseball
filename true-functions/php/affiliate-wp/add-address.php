<?php
/**
 * Plugin Name: AffiliateWP - Add Address field to the affiliate registration form
 * Plugin URI: https://affiliatewp.com
 * Description: Add a custom field (Address) to the affiliate registration form
 * Version: 1.0
 */

/*
 * Add the Address field to the affiliate registration form
 */
function affwp_add_address_1_field_to_affiliate_registration_form() {

	$errors = affiliate_wp()->register->get_errors();

	if ( ! array_key_exists( 'empty_address_1', $errors ) ) {
		$address_1 = sanitize_text_field( $_POST['affwp_address_1'] );
	}

    ?>
    <h4>Additional Information</h4>
	<p>
		<label for="affwp-address-1">Address</label>
		<input id="affwp-address-1" type="text" name="affwp_address_1" value="<?php if ( ! empty( $address_1 ) ) {
			echo $address_1;
		} ?>" title="Address" />
	</p>
	<?php
}
add_action( 'affwp_register_fields_before_tos', 'affwp_add_address_1_field_to_affiliate_registration_form' );

/*
 * Save the Address to the affiliate meta after registration
 */
function affwp_save_affiliate_address_1( $affiliate_id, $status, $args ) {

	$address_1 = sanitize_text_field( $_POST['affwp_address_1'] );

	if ( ! empty( $address_1 ) ) {
		affwp_add_affiliate_meta( $affiliate_id, 'address_1', $address_1 );
	}

}
add_action( 'affwp_register_user', 'affwp_save_affiliate_address_1', 10, 3 );

/*
 * Make Address field required during affiliate registration {Remove if it shouldn't be required}
 */
function affwp_add_address_1_to_required_fields( $required_fields ) {

	$required_fields['affwp_address_1'] = array(
		'error_id'      => 'empty_address_1',
		'error_message' => 'Please enter your Address',
	);

	return $required_fields;
}
add_filter( 'affwp_register_required_fields', 'affwp_add_address_1_to_required_fields' );

/*
 * Display the Address field in the Profile Settings page in the affiliate dashboard
 */
function affwp_show_address_1_in_affiliate_dashboard( $affiliate_id, $affiliate_user_id ) {

	$address_1 = affwp_get_affiliate_meta( $affiliate_id, 'address_1', true );

	?>

	<div class="affwp-wrap affwp-address-1-wrap">
		<label for="affwp-address-1"><?php _e( 'Your Address', 'affiliate-wp' ); ?></label>
		<input id="affwp-address-1" type="text" name="address_1" value="<?php echo esc_attr( $address_1 ); ?>" />
	</div>

	<?php

}
add_action( 'affwp_affiliate_dashboard_before_submit', 'affwp_show_address_1_in_affiliate_dashboard', 10, 2 );

/*
 * Update the Address field from the Profile Settings page in the affiliate dashboard
 */
function affwp_affiliate_dashboard_update_address_1( $data ) {

	$affiliate_id = absint( $data['affiliate_id'] );

	if ( ! empty( $data['address_1'] ) ) {

		$address_1 = sanitize_text_field( $data['address_1'] );

		affwp_update_affiliate_meta( $affiliate_id, 'address_1', $address_1 );

	} else {

		affwp_delete_affiliate_meta( $affiliate_id, 'address_1' );

	}

}
add_action( 'affwp_update_affiliate_profile_settings', 'affwp_affiliate_dashboard_update_address_1' );

/*
 * Display the Address field in the edit affiliate page in the admin dashboard
 */
function affwp_admin_edit_affiliate_show_address_1( $affiliate ) {

	$address_1 = affwp_get_affiliate_meta( $affiliate->affiliate_id, 'address_1', true );
	?>

    <tr class="form-row">
        <th scope="row" id="direct-link-tracking">
            TRUE Affiliate Information
        </th>
        <td><hr></td>
    </tr>

	<tr class="form-row form-required">

		<th scope="row">
			<label for="payment_email">Address</label>
		</th>

		<td>
			<input class="regular-text" type="text" name="address_1" id="address_1" value="<?php echo esc_attr( $address_1 ); ?>" />
			<p class="description">The affiliate Address</p>
		</td>

	</tr>

	<?php
}
add_action( 'affwp_edit_affiliate_end', 'affwp_admin_edit_affiliate_show_address_1' );

/*
 * Update the affiliate Address field from the edit affiliate page in the admin dashboard
 */
function affwp_admin_update_affiliate_address_1( $affiliate, $updated ) {

	if ( $updated ) {

		$affiliate_id = $affiliate->affiliate_id;

		if ( ! empty( $_POST['address_1'] ) ) {

			$address_1 = sanitize_text_field( $_POST['address_1'] );

			affwp_update_affiliate_meta( $affiliate_id, 'address_1', $address_1 );

		} else {

			affwp_delete_affiliate_meta( $affiliate_id, 'address_1' );

		}
	}

}
add_action( 'affwp_updated_affiliate', 'affwp_admin_update_affiliate_address_1', 10, 2 );