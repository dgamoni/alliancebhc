<?php
global $avia_config, $post_loop_count;

$post_loop_count= 1;
$post_class     = "post-entry-".avia_get_the_id();



// check if we got posts to display:
if (have_posts()) :

    while (have_posts()) : the_post();


$topic_description = get_field('topic_description', $post->ID);
$featured_video_url = get_field('featured_video_url', $post->ID);
$video = get_field('featured_video_url', false, false);
$v = parse_video_uri( $video ); 
$vid = $v['id'];
$type = $v['type'];
?>

        <article class='post-entry post-entry-type-page <?php echo $post_class; ?>' <?php avia_markup_helper(array('context' => 'entry')); ?>>

            <div class="entry-content-wrapper clearfix custom-lead-content ">
                <?php
                //echo '<header class="entry-content-header">';
                //echo '</header>';

                ?>


                <?php
                echo '<div class="entry-content contains-featured-image" '.avia_markup_helper(array('context' => 'entry_content','echo'=>false)).'>';
                ?>
                    <?php //if ( $topic_title ): ?>
                        <!-- <h2 class="lead-title topic_title"> -->
                            <?php //echo get_the_title(); ?>
                       <!--  </h2> -->
                    <?php //endif; ?>

                    <div class="flex_column av_one_half  flex_column_div av-zero-column-padding first avia-builder-el-first avia-builder-el-0  el_before_av_one_half " style="border-radius:0px; ">
                        <section class="av_textblock_section " itemscope="itemscope" itemtype="https://schema.org/CreativeWork">
                            <div class="avia_textblock  " itemprop="text">                                     

                                <div class="entry-content" itemprop="text">
				                    <?php if ( $topic_description ): ?>
				                        <div class="topic_description">
				                            <!-- <p> -->
				                            	<?php $topic_description =  apply_filters('the_content', $topic_description); echo $topic_description ?>
				                            <!-- </p> -->
				                        </div>
				                    <?php endif; ?>
                                </div> 

                            </div>
                        </section>
                    </div>

                    <div class="flex_column av_one_half  flex_column_div av-zero-column-padding avia-builder-el-1  el_before_av_one_half " style="border-radius:0px; ">
                        <section class="av_textblock_section " itemscope="itemscope" itemtype="https://schema.org/CreativeWork">
                            <div class="avia_textblock  " itemprop="text">                                     

                                <div class="entry-content" itemprop="text">
				                    <?php if ( $featured_video_url ): ?>
				                        <div class="featured_video_url">
				                            <!-- <p> -->
				                            	<?php 
				                            	//echo $featured_video_url;

				                            	$iframe = $featured_video_url;
				                            	// use preg_match to find iframe src
												preg_match('/src="(.+?)"/', $iframe, $matches);
												$src = $matches[1];


												// add extra params to iframe src
												$params = array(
												    'controls'    => 0,
												    'hd'        => 1,
												    'autohide'    => 1
												);

												$new_src = add_query_arg($params, $src);
												$new_src .= '?iframe=true';

												$iframe = str_replace($src, $new_src, $iframe);


												// add extra attributes to iframe html
												$attributes = 'title="Wistia video player" allowtransparency="true" frameborder="0" scrolling="no" class="wistia_embed popover=true" name="wistia_embed" allowfullscreen mozallowfullscreen webkitallowfullscreen oallowfullscreen msallowfullscreen';

												$iframe = str_replace('></iframe>', ' ' . $attributes . '></iframe>', $iframe);


												
												//echo $iframe;
				                            	?>
<?php if ($type =='wistia'): ?>
	<script src="//fast.wistia.com/embed/medias/<?php echo $vid; ?>.jsonp" async></script>
	<script src="//fast.wistia.com/assets/external/E-v1.js" async></script>
	<span class="wistia_embed wistia_async_<?php echo $vid; ?> popover=true popoverAnimateThumbnail=true" style="display:inline-block;height:300px;width:100%">&nbsp;</span>
<?php else: ?>
	<?php //echo '<a href="#" rel="lightbox">'.$featured_video_url.'</a>'; ?>
	<?php echo $featured_video_url; ?>
<?php endif; ?>
				                                <?php //echo $type; ?>

				                            <!-- </p> -->
				                        </div>
				                     <?php else : ?>
				                     	<?php the_post_thumbnail(); ?>
				                    <?php endif; ?>                                	
                                </div> 

                            </div>
                        </section>
                    </div>


                    <!--  avia content -->
                    <div class="lead-child-avia-content">
                   		<?php the_content(__('Read more','avia_framework').'<span class="more-link-arrow">  &rarr;</span>'); ?>
                   		<?php //echo get_the_content( ); ?>

                   		<!-- <a href="//fast.wistia.net/embed/playlists/egdwclamrv?media_0_0%5BautoPlay%5D=false&media_0_0%5BcontrolsVisibleOnLoad%5D=false&popover=true&theme=bento&version=v1&videoFoam=false&videoOptions%5BautoPlay%5D=true&videoOptions%5BinlineOptionsOnly%5D=true&videoOptions%5BplayerColor%5D=54bbff&videoOptions%5Bversion%5D=v1&videoOptions%5BvideoHeight%5D=450&videoOptions%5BvideoWidth%5D=800&videoOptions%5BvolumeControl%5D=true" class="wistia-popover[height=450,playerColor=54bbff,width=1107]"><img src="http://embed.wistia.com/deliveries/70cb3ed35fa2a293943ee15c88ec6f6d98fede27.jpg?image_play_button=true&image_play_button_color=54bbffe0&image_crop_resized=150x84" alt="" /></a>
<script charset="ISO-8859-1" src="//fast.wistia.com/assets/external/popover-v1.js"></script> -->



                   	</div>

                   	<div class="clearfix"></div>

                    <!-- Additional Resources -->
                    
	                    <?php if( have_rows('detail_information') ): ?>
	                    
	                    <div class="additional_resources_wrap">

	                        <?php while( have_rows('detail_information') ): the_row(); 
	                            
	                            // vars
	                            //$additional_resources_title = get_sub_field('additional_resources_title');
	                            //$additional_resources_description = get_sub_field('additional_resources_description');
	                            $additional_resources = get_sub_field('additional_resources');
	                            //arsort($additional_resources);
	                            //echo "<pre>", var_dump($additional_resources), "</pre>";
	                            ?>
	                                <?php //if ( $additional_resources_title ): ?>
	           
	                                <?php //endif; ?>


	                                <!-- repeater -->
	                                <div class="additional_resources_wrap">
		                                <?php //if ( have_rows( 'additional_resources' ) ) : ?>  
		                                <?php if ( $additional_resources ) : ?>  

			                                <!-- <h2 class="lead-title additional_resources_title">
		                                        Additional Resources
		                                    </h2> -->

		                                <?php $resources_loop = 0; ?>  

		                                    <?php //while ( have_rows( 'additional_resources' ) ) : the_row(); ?>
		                                    <?php foreach($additional_resources as $entry) : ?>

		                                        <?php 
		                                        // $resource_title = get_sub_field( 'resource_title' );
		                                        // $resource_link = get_sub_field( 'resource_link' );
		                                        // $resource_description = get_sub_field( 'resource_description' );
		                                        $resource_title = $entry['resource_title'];
		                                        $resource_image = $entry['resource_image'];
		                                        $resource_description = $entry['resource_description'];
		                                        $resource_link_text = $entry['resource_link_text'];
		                                        $resource_link = $entry['resource_link'];
		                                        
		                                        
		                                        if ($resources_loop % 3 == 0) {
		                                            $resources_loop_class = 'first avia-builder-el-first';
		                                        } else {
		                                            $resources_loop_class = '';
		                                        }
		                                         ?>

		                                          <div class="flex_column av_one_third  flex_column_div av-zero-column-padding <?php echo $resources_loop_class; ?> avia-builder-el-<?php echo $resources_loop; ?>  el_before_av_one_half " style="border-radius:0px; ">
		                                            <section class="av_textblock_section " itemscope="itemscope" itemtype="https://schema.org/CreativeWork">
		                                                <div class="avia_textblock  " itemprop="text">                                     
		                                                	<?php /*
		                                                    <header class="entry-content-header">
		                                                        <h2 class="post-title entry-title" itemprop="headline">
		                                                            <a href="<?php echo $resource_link; ?>" ><?php echo $resource_title; ?></a>
		                                                        </h2>
		                                                    </header>
		                                                    */ ?>

		                                                    <div class="entry-content" itemprop="text">
		                                                    	<div class="resource_image_wrap">
			                                                    	<?php 
			                                                    	if ($resource_image) {
			                                                    		$size = 'medium';
			                                                    		//echo wp_get_attachment_image( $resource_image, $size );

			                                                    		$thumb_url =  wp_get_attachment_url( $resource_image );

			                                                    		/* Support Ticket - 107178 - Change Image Dimentions */ 
																		$params_news_img = array( 'width' => 360, 'height' => 232 );

																		echo '<a target="_blank" href="' . $resource_link . '"><img src="'. bfi_thumb( $thumb_url, $params_news_img  ) .'" class="bfi_700"></a>';

			                                                    	} ?>
		                                                    	</div>
																<h2 class="post-title entry-title" itemprop="headline">
		                                                            <a target="_blank" href="<?php echo $resource_link; ?>" ><?php echo $resource_title; ?></a>
		                                                        </h2>
		                                                        <p><?php echo $resource_description; ?></p>
		                                                        <p> <a href="<?php echo $resource_link; ?>" class="more-link" target="_blank"><?php echo $resource_link_text; ?><span class="more-link-arrow"></span></a></p>
		                                                    </div> 

		                                                </div>
		                                            </section>
		                                        </div>

		                                        <?php $resources_loop++; ?>

		                                    <?php //endwhile; ?> 
		                                    <?php endforeach; ?>

		                                <?php endif; ?>
		                            </div>  
	                                <!-- end repeater -->
	                        
	                        <?php endwhile; ?>

	                    	</div>  

	                    <?php endif; ?>
	                
                    <!-- end Additional Resources -->


                <?php
                    //the_content(__('Read more','avia_framework').'<span class="more-link-arrow">  &rarr;</span>');

                echo '</div>';

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