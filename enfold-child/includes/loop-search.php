<?php
global $avia_config;


// check if we got posts to display:
if (have_posts()) :
	$first = true;

	$counterclass = "";
	$post_loop_count = 1;
	$page = (get_query_var('paged')) ? get_query_var('paged') : 1;
	if($page > 1) $post_loop_count = ((int) ($page - 1) * (int) get_query_var('posts_per_page')) +1;
	$blog_style = avia_get_option('blog_style','multi-big');

	while ( have_posts() ) : the_post();


	$the_id 		= get_the_ID();
	$parity			= $post_loop_count % 2 ? 'odd' : 'even';
	$last           = count($wp_query->posts) == $post_loop_count ? " post-entry-last " : "";
	$post_class 	= "post-entry-".$the_id." post-loop-".$post_loop_count." post-parity-".$parity.$last." ".$blog_style;
	$post_format 	= get_post_format() ? get_post_format() : 'standard';

	?>

	<article <?php post_class('post-entry post-entry-type-'.$post_format . " " . $post_class . " "); avia_markup_helper(array('context' => 'entry')); ?>>
        <div class="entry-content-wrapper clearfix <?php echo $post_format; ?>-content">

            <header class="entry-content-header">
                <?php
                echo "<span class='search-result-counter {$counterclass}'>{$post_loop_count}</span>";
                //echo the post title
                $markup = avia_markup_helper(array('context' => 'entry_title','echo'=>false));
				$post_type = get_post_type_object( get_post_type($post) );
                echo "<h2 class='post-title entry-title'><a title='".the_title_attribute('echo=0')."' href='".get_permalink()."' $markup>".get_the_title()."</a>";
				echo "    <span style='font-size:9px;color:#111111;margin:2px;padding:3px 8px; background:#F2F2F2;'>";
			echo $post_type->labels->singular_name ; 
			echo "</span></h2>";
                ?>
                <span class='post-meta-infos'>
                    <time class='date-container minor-meta updated' <?php avia_markup_helper(array('context' => 'entry_time')); ?>>
                        <?php echo 'Last update ';
						the_modified_date('Y-m-d'); ?>						
                    </time>
                    <?php
					/*
                    if(get_post_type() !== "page")
                  
                    $taxonomies  = get_object_taxonomies(get_post_type($the_id));
                    $cats = '';
					$excluded_taxonomies = array_merge( get_taxonomies( array( 'public' => false ) ), array('post_tag','post_format') );
					$excluded_taxonomies = apply_filters('avf_exclude_taxonomies', $excluded_taxonomies, get_post_type($the_id), $the_id);
					
                    if(!empty($taxonomies))
                    {
                        foreach($taxonomies as $taxonomy)
                        {
                            if(!in_array($taxonomy, $excluded_taxonomies))
                            {
                                $cats .= get_the_term_list($the_id, $taxonomy, '', ', ','').' ';
                            }
                        }
                    }

                    if(!empty($cats))
                    {
                        echo "<span class='text-sep'>/</span>";
                       // echo '<span class="blog-categories minor-meta">'.__('in','avia_framework')." ";
                        echo $cats;
                       // echo '</span>';
                    }
*/
                    ?>

                </span>
            </header>

            <?php
                echo '<div class="entry-content" '.avia_markup_helper(array('context' => 'entry_content','echo'=>false)).'>';
                $excerpt = trim(get_the_excerpt());
                if(!empty($excerpt))
                {
                    the_excerpt();
                }
                else
                {
                    $excerpt = strip_shortcodes( get_the_content() );
                    $excerpt = apply_filters('the_excerpt', $excerpt);
                    $excerpt = str_replace(']]>', ']]&gt;', $excerpt);
                    echo $excerpt;
                }
                echo '</div>';
            ?>
        </div>

        <footer class="entry-footer"></footer>
        
        <?php do_action('ava_after_content', $the_id, 'loop-search'); ?>
	</article><!--end post-entry-->

	<?php


		$first = false;
		$post_loop_count++;
		if($post_loop_count >= 100) $counterclass = "nowidth";
	endwhile;
	else:


?>

	<article class="entry entry-content-wrapper clearfix" id='search-fail'>
            <p class="entry-content" <?php avia_markup_helper(array('context' => 'entry_content')); ?>>
                <strong><?php _e('Nothing Found', 'avia_framework'); ?></strong><br/>
               <?php _e('Sorry, no posts matched your criteria.', 'avia_framework'); ?>
            </p>

            <div class='hr_invisible'></div>

            <section class="search_not_found">
                <ul>
                    <li><?php _e('Check your spelling.', 'avia_framework'); ?></li>
                    <li><?php _e('Try a similar keyword, for example: IDD instead of I/DD.', 'avia_framework'); ?></li>
                    <li><?php _e('Try using more than one keyword.', 'avia_framework'); ?></li>
                </ul>

                <div class='hr_invisible'></div>
              <h3 class=''><?php // _e('Feel like browsing some posts instead?', 'avia_framework'); ?></h3>

        <?php
      //  the_widget('avia_combo_widget', 'error404widget', array('widget_id'=>'arbitrary-instance-'.$id,
      //          'before_widget' => '<div class="widget avia_combo_widget">',
       //         'after_widget' => '</div>',
      //          'before_title' => '<h3 class="widgettitle">',
      //          'after_title' => '</h3>'
      //      ));
        echo '</section>';
	echo "</article>";

	endif;
	echo avia_pagination('', 'nav');
?>
