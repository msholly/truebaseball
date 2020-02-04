<?php
/* Template Name: POS Template */
?>

<?php acf_form_head(); ?>
<?php get_header(); ?>

<div id="oliver-pos">
    <div id="content" role="main" style="height: 100vh">

        <?php /* The loop */ ?>
        <?php while (have_posts()) : the_post(); ?>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div id="oliver-msg" class="alert alert-dismissible fade show nomargin" role="alert">
                            <strong class="status">Holy guacamole!</strong>
                            <span class="msg">You should check in on some of those fields below.</span>
                            <button type="button" class="close" data-hide="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                    <!-- <div class="col-3"> -->
                    <!-- <button id="refreshPage" class="btn btn-dark btn-block btn-lg noradius">Ready</button> -->
                    <!-- <button id="custom_tax_add_button" class="btn btn-success btn-block btn-lg noradius button-secondary">Recalc Tax</button> -->
                    <!-- <button id="custom_fee_remove_button" class="btn btn-danger btn-block btn-lg noradius">Delete Tax</button> -->
                    <!-- <button id="clearAllTags" class="btn btn-block btn-lg noradius color--primary-bg color--white">Clear Tags</button> -->
                    <!-- </div> -->
                </div>

                <?php acf_form(); ?>

                <div class="row">
                    <div class="col-12">
                        <?php $true_pos_nonce = wp_create_nonce('true_pos_form_nonce'); ?>

                        <input type="hidden" id="true_pos_nonce" name="true_pos_nonce" value="<?php echo $true_pos_nonce ?>" />

                        <!-- Image loader -->
                        <div id='loader' style='display: none;'>
                            <img src="<?php bloginfo('stylesheet_directory'); ?>/assets/img/ball-loading.gif" />
                        </div>
                        <!-- Image loader -->
                    </div>
                </div>

                <div class="row ticket-data">
                    <div class="col-6">
                        <h3 id="event-title" class="merge"></h3>
                        <div id="event-date" class="merge"></div>
                    </div>
                    <div class="col-6">
                        <h3>Player Name</h3>
                        <div id="player-name" class="merge"></div>
                    </div>
                </div>

                <div class="row ticket-data">
                    <div class="col-3">
                        <h3>Attendee ID</h3>
                        <div id="ticket-num" class="merge"></div>
                    </div>
                    <div class="col-3">
                        <h3>TICKET TYPE</h3>
                        <div id="ticket-type" class="merge"></div>
                    </div>
                    <div class="col-3">
                        <h3>PURCHASER</h3>
                        <span id="ticket-purchaser" class="merge"></span> for $<span id="ticket-cost" class="merge"></span>
                    </div>
                    <div class="col-3">
                        <h3>SECURITY</h3>
                        <div id="ticket-security" class="merge"></div>
                    </div>
                </div>

                <div class="row ticket-data">
                    <div class="col-12">
                        <div class="alert alert-success" role="alert">
                            <h3 class="nomargin">TICKET <strong>#<span id="ticket-id" class="merge"></span></strong> STATUS from ORDER #<span id="ticket-orderid" class="merge"></span></h3>
                            <div id="ticket-checkin" class="merge"></div>
                        </div>
                    </div>
                </div>

                <div class="row address-data">
                    <div class="col-12">
                        <h3>Ship To Address - Status: <span id="addr-source">From store</span></h3>
                        <div class="original-ship">
                            <div class="merge">
                                <span id="ship-route"></span>
                                <span id="ship-addr2"></span>
                            </div>
                            <div id="ship-rest" class="merge"></div>
                            <button id="showShipToOverride" class="btn btn-outline-info btn-block btn-lg noradius">Ship to new address</button>
                        </div>

                        <div class="auto-complete">

                            <input id="geocomplete-true" type="text" placeholder="Search for an address" autocomplete="new-password">
                            <!-- <input id="find" type="button" value="find" /> -->


                            <div class="row">
                                <div class="col-3">
                                    <label>Street Number </label>
                                    <input id="street_number" name="street_number" type="text" value="" required>
                                </div>
                                <div class="col-6">
                                    <label>Street </label>
                                    <input id="route" name="route" type="text" value="" required>
                                </div>
                                <div class="col-3">
                                    <label>Apt, Suite</label>
                                    <input id="subpremise" name="subpremise" type="text" value="">
                                </div>
                                <div class="col-4">
                                    <label>City</label>
                                    <input id="locality" name="locality" type="text" value="" required>
                                </div>
                                <div class="col-2">
                                    <label>State</label>
                                    <input id="administrative_area_level_1_short" name="administrative_area_level_1_short" type="text" value="" maxlength="2" required>
                                </div>
                                <div class="col-3">
                                    <label>Postal Code</label>
                                    <input id="postal_code" name="postal_code" type="text" value="" maxlength="5" required>
                                </div>
                                <div class="col-3">
                                    <label>Country</label>
                                    <input id="country_short" name="country_short" type="text" value="" maxlength="2" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button id="saveNewShipTo" class="btn btn-dark btn-block btn-lg noradius">Save Address And Calc Taxes</button>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-12 map-wrapper">
                        <h3>Map</h3>
                        <div class="map_canvas"></div>
                    </div>
                </div>

                <div class="fixed-bottom">
                    <table class="table">
                        <tr>
                            <td>
                                <h3 class="nomargin text-center">STEP 1</h3>
                                <button id="customtags_button" class="button button-primary button-large" style="display: block;width: 100%;">Save To POS Order</button>
                            </td>
                            <td>
                                <h3 class="nomargin text-center">STEP 2</h3>
                                <button id="extension_finished" class="button button-secondary button-large" style="display: block;width: 100%;">Complete Tags</button>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php the_content(); ?>

            </div>

        <?php endwhile; ?>

        <script>
            var oliverExtensionTargetOrigin = '<?php echo OLIVER_EXTENSION_TARGET_ORIGIN; ?>';
        </script>
    </div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>