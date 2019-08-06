<?php 
/* Template Name: POS Template */ 
?>

<?php acf_form_head(); ?>
<?php get_header(); ?>

	<div id="primary">
		<div id="content" role="main">

			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
                
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">
                            <button id="clearAllTags" class="noradius color--primary-bg color--white">Clear All</button>
                        </div>
                    </div>
                
				
                
                

                    <?php acf_form(); ?>

                    <button href="#" id="send-acf-to-oliver" class="button button-primary button-large">SEND TO OLIVER</button>

                    <?php the_content(); ?>
				
				</div>

			<?php endwhile; ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>
