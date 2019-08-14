<?php 
/* Template Name: POS Template */ 
?>

<?php acf_form_head(); ?>
<?php get_header(); ?>

	<div id="oliver-pos">
		<div id="content" role="main" style="height: 100vh">

			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
                
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <button id="clearAllTags" class="noradius color--primary-bg color--white">Clear All</button>
                            <button id="refreshPage" class="noradius color--primary-bg fright" style="background-color: transparent !important">Refresh</button>
                        </div>
                    </div>

                    <?php acf_form(); ?>

                    <div class="row">
                        <div class="col-12">
                            <!-- Image loader -->
                            <div id='loader' style='display: none;'>
                                <img src="<?php bloginfo('stylesheet_directory');?>/assets/img/ball-loading.gif" />
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
                            <h3>TICKET #</h3>
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

                    <!-- <div class="row">
                        <div class="col-4">
                            <h3>Player</h3>
                            <div id="player-name"></div>
                        </div>
                    </div> -->

                    <!-- <button href="#" id="send-acf-to-oliver" class="button button-primary button-large">SEND TO OLIVER</button> -->

                    <div class="fixed-bottom">
                        <table class="table">
                            <tr>
                                <td>
                                    <h3 class="nomargin text-center">STEP 1</h3>
                                    <button id="customtags_button" class="button button-primary button-large" style="display: block;width: 100%;">Save to POS Order</button>
                                </td>
                                <td>
                                    <h3 class="nomargin text-center">STEP 2</h3>
                                    <button id="extension_finished" class="button button-secondary button-large" style="display: block;width: 100%;">Extension Finished</button>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
    

                    <?php the_content(); ?>
				
				</div>

			<?php endwhile; ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>