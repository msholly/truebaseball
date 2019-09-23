<?php

// Generate a custom nonce value.
$true_fitting_nonce = wp_create_nonce( 'true_fitting_form_nonce' ); 

// Build the Input
?>

<input type="hidden" name="true_fitting_nonce" value="<?php echo $true_fitting_nonce ?>" />			
