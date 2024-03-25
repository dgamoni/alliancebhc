<?php

/**
 * Template Name: Provider Updates Archive Final
 *
 * Selectable from a dropdown menu on the edit page screen.
 */
	global $avia_config, $more;

	/*
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */
	 get_header();
	
		
		$showheader = true;
		if(avia_get_option('frontpage') && $blogpage_id = avia_get_option('blogpage'))
		{
			if(get_post_meta($blogpage_id, 'header', true) == 'no') $showheader = false;
		}
		
	 	if($showheader)
	 	{
			echo avia_title(array('title' => avia_which_archive()));
		}
	?>

		<div class="container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>">

			<div class="container template-blog provider-news-archive">

				<main class='content <?php avia_layout_class( 'content' ); ?> units' <?php avia_markup_helper(array('context' => 'content','post_type'=>'post'));?>>

                    <div class="category-term-description">
                        <?php echo term_description(); ?>
                    </div><?php 
                        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
                        $wp_query = new WP_Query(
                            array(
                                'post_type'   => 'provider-updates',
                                'paged'    => $paged,
                                'posts_per_page'    => 5
                            )
                        );

                        $count=0;
                        while ($wp_query->have_posts()) : $wp_query->the_post();
                            $count++;
                            if (!is_sticky()){ ?>
                                <div class="sticky_not archive-item"><?php
                                    the_title( '<h2 class="entry-title"><a href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '" rel="bookmark">', '</a></h2>' ); ?>
                                    <small><?php the_time('F jS, Y') ?></small>
                                    <div class="entry-content"><?php 
                                        the_content();?>
                                    </div>
                                </div><?php }  else { ?>

                                <div class="sticky-box archive-item" id="a-<?php echo $count; ?>"><?php 
                                    the_title( '<h2 class="entry-title"><a href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '" rel="bookmark">', '</a></h2>' ); ?>
                                    <small><?php the_time('F jS, Y') ?></small>
                                    <div class="entry-content"><?php echo substr(get_the_content(),0,180);?>...
                                    <a href="<?php echo the_permalink();?>" style="margin-left:10px;">>> Read more</a></div>
                                </div><?php } 
                        endwhile; ?>

                        <div class="paging">
                            <?php wp_pagenavi(); ?>
                        </div>  

				<!--end content-->
				</main>

                <?php

                ##############################################################################
                # Display the sidebar
                ##############################################################################

                $default_sidebar = true;
                $sidebar_pos = avia_layout_class('main', false);

                $sidebar_smartphone = avia_get_option('smartphones_sidebar') == 'smartphones_sidebar' ? 'smartphones_sidebar_active' : "";
                $sidebar = "";

                if(strpos($sidebar_pos, 'sidebar_left')  !== false) $sidebar = 'left';
                if(strpos($sidebar_pos, 'sidebar_right') !== false) $sidebar = 'right';

                //filter the sidebar position (eg woocommerce single product pages always want the same sidebar pos)
                $sidebar = apply_filters('avf_sidebar_position', $sidebar);

                //if the layout hasnt the sidebar keyword defined we dont need to display one
                if(empty($sidebar)) return;
                if(!empty($avia_config['overload_sidebar'])) $avia_config['currently_viewing'] = $avia_config['overload_sidebar'];


                echo "<aside class='sidebar sidebar_".$sidebar." ".$sidebar_smartphone." ".avia_layout_class( 'sidebar', false )." units' ".avia_markup_helper(array('context' => 'sidebar', 'echo' => false)).">";
                    echo "<div class='inner_sidebar extralight-border'>";

                        dynamic_sidebar('Provider News');

                    echo "</div>";
                echo "</aside>";



				?>

			</div><!--end container-->

		</div><!-- close default .container_wrap element -->




<?php get_footer(); ?>
