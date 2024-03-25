<?php
global $avia_config, $post_loop_count;

$post_loop_count= 1;
$post_class     = "post-entry-".avia_get_the_id();



// check if we got posts to display:
if (have_posts()) :

    while (have_posts()) : the_post();


$leadership_description = get_field('leadership_description', $post->ID);
?>

        <article class='post-entry post-entry-type-page <?php echo $post_class; ?>' <?php avia_markup_helper(array('context' => 'entry')); ?>>

            <div class="entry-content-wrapper clearfix custom-lead-content ">
                <?php
                echo '<header class="entry-content-header">';
                echo '</header>';

                ?>


                <?php
                echo '<div class="entry-content" '.avia_markup_helper(array('context' => 'entry_content','echo'=>false)).'>';
                ?>

                	<?php echo $leadership_description; ?>

                <?php
                    //the_content(__('Read more','avia_framework').'<span class="more-link-arrow">  &rarr;</span>');

                echo '</div>';



				$args = array(
					'child_of'     => 34611,
					'post_type'    => 'page',
					'post_status'  => 'publish',
				); 
				$pages = get_pages( $args );
				foreach( $pages as $resources_loop=>$post ){
					setup_postdata( $post );
					//var_dump($post);
					$topic_description = get_field('topic_description_small', $post->ID);

                    if ($resources_loop % 3 == 0) {
                        $resources_loop_class = 'first avia-builder-el-first';
                    } else {
                        $resources_loop_class = '';
                    }

					?>
	                    <div class="flex_column av_one_third _av_one_half  flex_column_div av-zero-column-padding <?php echo $resources_loop_class; ?> avia-builder-el-<?php echo $resources_loop; ?>  el_before_av_one_half " style="border-radius:0px; ">
                            <section class="av_textblock_section " itemscope="itemscope" itemtype="https://schema.org/CreativeWork">
                                
	                            <a href="<?php echo get_the_permalink(); ?>" class="lead-child-link">
	                                <div class="avia_textblock  " itemprop="text">                                     

	                                    <header class="entry-content-header">
	                                        <h2 class="post-title entry-title" itemprop="headline">
	                                            <?php echo get_the_title(); ?>
	                                        </h2>
	                                    </header>
	                                    <div class="entry-content" itemprop="text">
	                                    	<p><?php //echo get_the_post_thumbnail(); 
												$thumb_url =  wp_get_attachment_url( get_post_thumbnail_id() );
												$params_news_img = array( 'width' => 340, 'height' => 210 );
												echo '<img src="'. bfi_thumb( $thumb_url, $params_news_img  ) .'" class="">';

	                                    	?></p>
	                                        <p><?php echo $topic_description; ?></p>
	                                        <p><?php //echo subtitle_max_charlength( $topic_description, 140 ); ?></p>
	                                    </div> 

	                                </div>
	                            </a>
                            </section>
                        </div>
					<?php
				}  
				wp_reset_postdata();

                echo '<footer class="entry-footer">';

                echo '</footer>';
                
                //do_action('ava_after_content', get_the_ID(), 'page');
                ?>
            </div>

        </article><!--end post-entry-->


<?php
    $post_loop_count++;
    endwhile;
    else:
?>

    <article class="entry">
        <header class="entry-content-header">
            <h1 class='post-title entry-title'><?php _e('Nothing Found', 'avia_framework'); ?></h1>
        </header>

        <?php get_template_part('includes/error404'); ?>

        <footer class="entry-footer"></footer>
    </article>

<?php

    endif;
?>

<style>
	.additional_resources_wrap {
		/*display: flex;*/
	}
	.lead-title {
		margin-top: 30px;
	}
	.custom-lead-content p {
		padding: 10px 0;
	}
	.embed-container { 
		position: relative; 
		padding-bottom: 56.25%;
		overflow: hidden;
		max-width: 100%;
		height: auto;
	} 

	.embed-container iframe,
	.embed-container object,
	.embed-container embed { 
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
	}
</style>