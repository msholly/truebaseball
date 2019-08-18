<?php
/**
 * Plugin Name: AffiliateWP - Add program field to the affiliate registration form
 * Plugin URI: https://affiliatewp.com
 * Description: Add a custom field (program) to the affiliate registration form
 * Version: 1.0
 */

/*
 * Add the program field to the affiliate registration form
 */
function affwp_add_program_field_to_affiliate_registration_form() {

	$errors = affiliate_wp()->register->get_errors();
    // $options = get_option( 'affwp_program' );

	if ( ! array_key_exists( 'empty_program', $errors ) ) {
        $program = sanitize_text_field( $_POST['affwp_program'] );
	}

	?>
	<p>
		<label for="affwp-program">Affiliate Program Type</label>
		        
        <select name='affwp_program'>
            <option value='Web Affiliate'>Web Affiliate</option>
            <option value='Team or Facility'>Team or Facility</option>
        </select>
	</p>
	<?php
}
add_action( 'affwp_register_fields_before_tos', 'affwp_add_program_field_to_affiliate_registration_form' );

/*
 * Save the program to the affiliate meta after registration
 */
function affwp_save_affiliate_program( $affiliate_id, $status, $args ) {

	$program = sanitize_text_field( $_POST['affwp_program'] );

	if ( ! empty( $program ) ) {
		affwp_add_affiliate_meta( $affiliate_id, 'program', $program );
	}

}
add_action( 'affwp_register_user', 'affwp_save_affiliate_program', 10, 3 );

/*
 * Make program field required during affiliate registration {Remove if it shouldn't be required}
 */
function affwp_add_program_to_required_fields( $required_fields ) {

	$required_fields['affwp_program'] = array(
		'error_id'      => 'empty_program',
		'error_message' => 'Please enter your program type',
	);

	return $required_fields;
}
add_filter( 'affwp_register_required_fields', 'affwp_add_program_to_required_fields' );

/*
 * Display the program field in the Profile Settings page in the affiliate dashboard
 */
function affwp_show_program_in_affiliate_dashboard( $affiliate_id, $affiliate_user_id ) {

	$program = affwp_get_affiliate_meta( $affiliate_id, 'program', true );

	?>

	<div class="affwp-wrap affwp-program-wrap">
		<label for="affwp-program"><?php _e( 'Affiliate Program Type', 'affiliate-wp' ); ?></label>
		<input id="affwp-program" type="text" name="program" value="<?php echo esc_attr( $program ); ?>" />
	</div>

	<?php

}
add_action( 'affwp_affiliate_dashboard_before_submit', 'affwp_show_program_in_affiliate_dashboard', 10, 2 );

/*
 * Update the program field from the Profile Settings page in the affiliate dashboard
 */
function affwp_affiliate_dashboard_update_program( $data ) {

	$affiliate_id = absint( $data['affiliate_id'] );

	if ( ! empty( $data['program'] ) ) {

		$program = sanitize_text_field( $data['program'] );

		affwp_update_affiliate_meta( $affiliate_id, 'program', $program );

	} else {

		affwp_delete_affiliate_meta( $affiliate_id, 'program' );

	}

}
add_action( 'affwp_update_affiliate_profile_settings', 'affwp_affiliate_dashboard_update_program' );

/*
 * Display the program field in the edit affiliate page in the admin dashboard
 */
function affwp_admin_edit_affiliate_show_program( $affiliate ) {

	$program = affwp_get_affiliate_meta( $affiliate->affiliate_id, 'program', true );
	?>

	<tr class="form-row form-required">

		<th scope="row">
			<label for="payment_email">Program</label>
		</th>

		<td>
			<input class="regular-text" type="text" name="program" id="program" value="<?php echo esc_attr( $program ); ?>" />
			<p class="description">The affiliate's program</p>
		</td>

	</tr>

	<?php
}
add_action( 'affwp_edit_affiliate_end', 'affwp_admin_edit_affiliate_show_program' );

/*
 * Update the affiliate program field from the edit affiliate page in the admin dashboard
 */
function affwp_admin_update_affiliate_program( $affiliate, $updated ) {

	if ( $updated ) {

		$affiliate_id = $affiliate->affiliate_id;

		if ( ! empty( $_POST['program'] ) ) {

			$program = sanitize_text_field( $_POST['program'] );

			affwp_update_affiliate_meta( $affiliate_id, 'program', $program );

		} else {

			affwp_delete_affiliate_meta( $affiliate_id, 'program' );

		}
	}

}
add_action( 'affwp_updated_affiliate', 'affwp_admin_update_affiliate_program', 10, 2 );