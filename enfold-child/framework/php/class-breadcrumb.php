<?php

if(!class_exists('avia_breadcrumb'))
{
	class avia_breadcrumb
	{
		var $options;

	function avia_breadcrumb($options = ""){

		$this->options = array( 	//change this array if you want another output scheme
		'before' => '<span class="arrow"> ',
		'after' => ' </span>',
		'delimiter' => '&raquo;'
		);

		if(is_array($options))
		{
			$this->options = array_merge($this->options, $options);
		}


		$markup = $this->options['before'].$this->options['delimiter'].$this->options['after'];

		global $post;
		echo '<p class="breadcrumb"><span class="breadcrumb_info">'.__('You are heressss:','avia_framework').'</span> <a href="'.get_bloginfo('url').'">';
			bloginfo('name');
			echo "</a>";
			if(!is_front_page()){echo $markup;}

			$output = $this->simple_breadcrumb_case($post);

			echo "<span class='current_crumb'>";
			if ( is_page() || is_single()) {
			the_title();
			}else{
			echo $output;
			}
			echo " </span></p>";
		}

		function simple_breadcrumb_case($der_post)
		{
			global $post;

			$markup = $this->options['before'].$this->options['delimiter'].$this->options['after'];
			if (is_page()){
				 if($der_post->post_parent) {
					 $my_query = get_post($der_post->post_parent);
					 $this->simple_breadcrumb_case($my_query);
					 $link = '<a href="';
					 $link .= get_permalink($my_query->ID);
					 $link .= '">';
					 $link .= ''. get_the_title($my_query->ID) . '</a>'. $markup;
					 echo $link;
				  }
			return;
			}

		if(is_single())
		{
			$category = get_the_category();
			if (is_attachment()){

				$my_query = get_post($der_post->post_parent);
				$category = get_the_category($my_query->ID);

				if(isset($category[0]))
				{
					$ID = $category[0]->cat_ID;
					$parents = get_category_parents($ID, TRUE, $markup, FALSE );
					if(!is_object($parents)) echo $parents;
					previous_post_link("%link $markup");
				}

			}else{

				$postType = get_post_type();

				if($postType == 'post')
				{
					$ID = $category[0]->cat_ID;
					echo get_category_parents($ID, TRUE, $markup, FALSE );
				}
				else if($postType == 'portfolio')
				{
					$terms = get_the_term_list( $post->ID, 'portfolio_entries', '', '$$$', '' );
					$terms = explode('$$$',$terms);
					echo $terms[0].$markup;

				}
			}
		return;
		}

		if(is_tax()){
			  $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
			  return $term->name;

		}


		if(is_category()){
			$category = get_the_category();
			$i = $category[0]->cat_ID;
			$parent = $category[0]-> category_parent;

			if($parent > 0 && $category[0]->cat_name == single_cat_title("", false)){
				echo get_category_parents($parent, TRUE, $markup, FALSE);
			}
		return single_cat_title('',FALSE);
		}


		if(is_author()){
			$curauth = get_userdatabylogin(get_query_var('author_name'));
			return "Author: ".$curauth->nickname;
		}
		if(is_tag()){ return "Tag: ".single_tag_title('',FALSE); }

		if(is_404()){ return __("404 - Page not Found",'avia_framework'); }

		if(is_search()){ return __("Search",'avia_framework');}

		if(is_year()){ return get_the_time('Y'); }

		if(is_month()){
		$k_year = get_the_time('Y');
		echo "<a href='".get_year_link($k_year)."'>".$k_year."</a>".$markup;
		return get_the_time('F'); }

		if(is_day() || is_time()){
		$k_year = get_the_time('Y');
		$k_month = get_the_time('m');
		$k_month_display = get_the_time('F');
		echo "<a href='".get_year_link($k_year)."'>".$k_year."</a>".$markup;
		echo "<a href='".get_month_link($k_year, $k_month)."'>".$k_month_display."</a>".$markup;

		return get_the_time('jS (l)'); }

		}

	}
}




/*-----------------------------------------------------------------------------------*/
/* avia_breadcrumbs() - Custom breadcrumb generator function  */
/*
/* Params:
/*
/* Arguments Array:
/*
/* 'separator' 			- The character to display between the breadcrumbs.
/* 'before' 			- HTML to display before the breadcrumbs.
/* 'after' 				- HTML to display after the breadcrumbs.
/* 'front_page' 		- Include the front page at the beginning of the breadcrumbs.
/* 'show_home' 			- If $show_home is set and we're not on the front page of the site, link to the home page.
/* 'echo' 				- Specify whether or not to echo the breadcrumbs. Alternative is "return".
/* 'show_posts_page'	- If a static front page is set and there is a posts page, toggle whether or not to display that page's tree.
/*
/*-----------------------------------------------------------------------------------*/



/**
 * The code below is an inspired/modified version by woothemes breadcrumb function which in turn is inspired by Justin Tadlock's Hybrid Core :)
 */


function avia_breadcrumbs( $args = array() ) {
	global $wp_query, $wp_rewrite;

	/* Create an empty variable for the breadcrumb. */
	$breadcrumb = '';

	/* Create an empty array for the trail. */
	$trail = array();
	$path = '';

	/* Set up the default arguments for the breadcrumb. */
	$defaults = array(
		'separator' => '&raquo;',
		'before' => '<span class="breadcrumb-title">' . __( 'You are here:', 'avia_framework' ) . '</span>',
		'after' => false,
		'front_page' => true,
		'show_home' => __( 'Home', 'avia_framework' ),
		'echo' => false,
		'show_categories' => true,
		'show_posts_page' => true,
		'truncate' => 70,
		'richsnippet' => false
	);


	/* Allow singular post views to have a taxonomy's terms prefixing the trail. */
	if ( is_singular() )
		$defaults["singular_{$wp_query->post->post_type}_taxonomy"] = false;

	/* Apply filters to the arguments. */
	$args = apply_filters( 'avia_breadcrumbs_args', $args );

	/* Parse the arguments and extract them for easy variable naming. */
	extract( wp_parse_args( $args, $defaults ) );

	/* If $show_home is set and we're not on the front page of the site, link to the home page. */
	if ( !is_front_page() && $show_home )
		$trail[] = '<a href="' . home_url() . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" rel="home" class="trail-begin">' . $show_home . '</a>';

	/* If viewing the front page of the site. */
	if ( is_front_page() ) {
		if ( !$front_page )
			$trail = false;
		elseif ( $show_home )
			$trail['trail_end'] = "{$show_home}";
	}

	/* If viewing the "home"/posts page. */
	elseif ( is_home() ) {
		$home_page = get_page( $wp_query->get_queried_object_id() );
		$trail = array_merge( $trail, avia_breadcrumbs_get_parents( $home_page->post_parent, '' ) );
		$trail['trail_end'] = get_the_title( $home_page->ID );
	}

	/* If viewing a singular post (page, attachment, etc.). */
	elseif ( is_singular() ) {

		/* Get singular post variables needed. */
		$post = $wp_query->get_queried_object();
		$post_id = absint( $wp_query->get_queried_object_id() );
		$post_type = $post->post_type;
		$parent = $post->post_parent;


		/* If a custom post type, check if there are any pages in its hierarchy based on the slug. */
		if ( 'page' !== $post_type && 'post' !== $post_type ) {

			$post_type_object = get_post_type_object( $post_type );

			/* If $front has been set, add it to the $path. */
			if ( 'post' == $post_type || 'attachment' == $post_type || ( $post_type_object->rewrite['with_front'] && $wp_rewrite->front ) )
				$path .= trailingslashit( $wp_rewrite->front );

			/* If there's a slug, add it to the $path. */
			if ( !empty( $post_type_object->rewrite['slug'] ) )
				$path .= $post_type_object->rewrite['slug'];

			/* If there's a path, check for parents. */
			if ( !empty( $path ) )
				$trail = array_merge( $trail, avia_breadcrumbs_get_parents( '', $path ) );

			/* If there's an archive page, add it to the trail. */
			if ( !empty( $post_type_object->has_archive ) && function_exists( 'get_post_type_archive_link' ) )
				$trail[] = '<a href="' . get_post_type_archive_link( $post_type ) . '" title="' . esc_attr( $post_type_object->labels->name ) . '">' . $post_type_object->labels->name . '</a>';
		}

		/* try to build a generic taxonomy trail no matter the post type and taxonomy and terms
		$currentTax = "";
		foreach(get_taxonomies() as $tax)
		{
			$terms = get_the_term_list( $post_id, $tax, '', '$$$', '' );
			echo"<pre>";
			print_r($tax.$terms);
			echo"</pre>";
		}
		*/

		/* If the post type path returns nothing and there is a parent, get its parents. */
		if ( empty( $path ) && 0 !== $parent || 'attachment' == $post_type )
			$trail = array_merge( $trail, avia_breadcrumbs_get_parents( $parent, '' ) );

		/* Toggle the display of the posts page on single blog posts. */
		if ( 'post' == $post_type && $show_posts_page == true && 'page' == get_option( 'show_on_front' ) ) {
			$posts_page = get_option( 'page_for_posts' );
			if ( $posts_page != '' && is_numeric( $posts_page ) ) {
				$trail = array_merge( $trail, avia_breadcrumbs_get_parents( $posts_page, '' ) );
			}
		}

        if('post' == $post_type && $show_categories)
        {
                $category = get_the_category();
				
                foreach($category as $cat)
                {
                    if(!empty($cat->parent))
                    {
                        $parents = get_category_parents($cat->cat_ID, TRUE, '$$$', FALSE );
                        $parents = explode("$$$", $parents);
                        foreach ($parents as $parent_item)
                        {
                            if($parent_item) $trail[] = $parent_item;
                        }
                        break;
                    }
                }
                
                if(isset($category[0]) && empty($parents))
                {
                	$trail[] = '<a href="'.get_category_link($category[0]->term_id ).'">'.$category[0]->cat_name.'</a>';
                }
                
        }

		if($post_type == 'portfolio')
		{
			$parents = get_the_term_list( $post_id, 'portfolio_entries', '', '$$$', '' );
			$parents = explode('$$$',$parents);
			foreach ($parents as $parent_item)
			{
				if($parent_item) $trail[] = $parent_item;
			}
		}

		/* Display terms for specific post type taxonomy if requested. */
		if ( isset( $args["singular_{$post_type}_taxonomy"] ) && $terms = get_the_term_list( $post_id, $args["singular_{$post_type}_taxonomy"], '', ', ', '' ) )
			$trail[] = $terms;

		/* End with the post title. */
		$post_title = get_the_title( $post_id ); // Force the post_id to make sure we get the correct page title.
		if ( !empty( $post_title ) )
			$trail['trail_end'] = $post_title;

	}

	/* If we're viewing any type of archive. */
	elseif ( is_archive() ) {

		/* If viewing a taxonomy term archive. */
		if ( is_tax() || is_category() || is_tag() ) {

			/* Get some taxonomy and term variables. */
			$term = $wp_query->get_queried_object();
			$taxonomy = get_taxonomy( $term->taxonomy );

			/* Get the path to the term archive. Use this to determine if a page is present with it. */
			if ( is_category() )
				$path = get_option( 'category_base' );
			elseif ( is_tag() )
				$path = get_option( 'tag_base' );
			else {
				if ( $taxonomy->rewrite['with_front'] && $wp_rewrite->front )
					$path = trailingslashit( $wp_rewrite->front );
				$path .= $taxonomy->rewrite['slug'];
			}

			/* Get parent pages by path if they exist. */
			if ( $path )
				$trail = array_merge( $trail, avia_breadcrumbs_get_parents( '', $path ) );

			/* If the taxonomy is hierarchical, list its parent terms. */
			if ( is_taxonomy_hierarchical( $term->taxonomy ) && $term->parent )
				$trail = array_merge( $trail, avia_breadcrumbs_get_term_parents( $term->parent, $term->taxonomy ) );

			/* Add the term name to the trail end. */
			$trail['trail_end'] = $term->name;
		}

		/* If viewing a post type archive. */
		elseif ( function_exists( 'is_post_type_archive' ) && is_post_type_archive() ) {

			/* Get the post type object. */
			$post_type_object = get_post_type_object( get_query_var( 'post_type' ) );

			/* If $front has been set, add it to the $path. */
			if ( $post_type_object->rewrite['with_front'] && $wp_rewrite->front )
				$path .= trailingslashit( $wp_rewrite->front );

			/* If there's a slug, add it to the $path. */
			if ( !empty( $post_type_object->rewrite['archive'] ) )
				$path .= $post_type_object->rewrite['archive'];

			/* If there's a path, check for parents. */
			if ( !empty( $path ) )
				$trail = array_merge( $trail, avia_breadcrumbs_get_parents( '', $path ) );

			/* Add the post type [plural] name to the trail end. */
			$trail['trail_end'] = $post_type_object->labels->name;
		}

		/* If viewing an author archive. */
		elseif ( is_author() ) {

			/* If $front has been set, add it to $path. */
			if ( !empty( $wp_rewrite->front ) )
				$path .= trailingslashit( $wp_rewrite->front );

			/* If an $author_base exists, add it to $path. */
			if ( !empty( $wp_rewrite->author_base ) )
				$path .= $wp_rewrite->author_base;

			/* If $path exists, check for parent pages. */
			if ( !empty( $path ) )
				$trail = array_merge( $trail, avia_breadcrumbs_get_parents( '', $path ) );

			/* Add the author's display name to the trail end. */
			$trail['trail_end'] =  apply_filters('avf_author_name', get_the_author_meta('display_name', get_query_var('author')), get_query_var('author'));
		}

		/* If viewing a time-based archive. */
		elseif ( is_time() ) {

			if ( get_query_var( 'minute' ) && get_query_var( 'hour' ) )
				$trail['trail_end'] = get_the_time( __( 'g:i a', 'avia_framework' ) );

			elseif ( get_query_var( 'minute' ) )
				$trail['trail_end'] = sprintf( __( 'Minute %1$s', 'avia_framework' ), get_the_time( __( 'i', 'avia_framework' ) ) );

			elseif ( get_query_var( 'hour' ) )
				$trail['trail_end'] = get_the_time( __( 'g a', 'avia_framework' ) );
		}

		/* If viewing a date-based archive. */
		elseif ( is_date() ) {

			/* If $front has been set, check for parent pages. */
			if ( $wp_rewrite->front )
				$trail = array_merge( $trail, avia_breadcrumbs_get_parents( '', $wp_rewrite->front ) );

			if ( is_day() ) {
				$trail[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( esc_attr__( 'Y', 'avia_framework' ) ) . '">' . get_the_time( __( 'Y', 'avia_framework' ) ) . '</a>';
				$trail[] = '<a href="' . get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) . '" title="' . get_the_time( esc_attr__( 'F', 'avia_framework' ) ) . '">' . get_the_time( __( 'F', 'avia_framework' ) ) . '</a>';
				$trail['trail_end'] = get_the_time( __( 'j', 'avia_framework' ) );
			}

			elseif ( get_query_var( 'w' ) ) {
				$trail[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( esc_attr__( 'Y', 'avia_framework' ) ) . '">' . get_the_time( __( 'Y', 'avia_framework' ) ) . '</a>';
				$trail['trail_end'] = sprintf( __( 'Week %1$s', 'avia_framework' ), get_the_time( esc_attr__( 'W', 'avia_framework' ) ) );
			}

			elseif ( is_month() ) {
				$trail[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( esc_attr__( 'Y', 'avia_framework' ) ) . '">' . get_the_time( __( 'Y', 'avia_framework' ) ) . '</a>';
				$trail['trail_end'] = get_the_time( __( 'F', 'avia_framework' ) );
			}

			elseif ( is_year() ) {
				$trail['trail_end'] = get_the_time( __( 'Y', 'avia_framework' ) );
			}
		}
	}

	/* If viewing search results. */
	elseif ( is_search() )
		$trail['trail_end'] = sprintf( __( 'Search results for &quot;%1$s&quot;', 'avia_framework' ), esc_attr( get_search_query() ) );

	/* If viewing a 404 error page. */
	elseif ( is_404() )
		$trail['trail_end'] = __( '404 Not Found', 'avia_framework' );

	/* Allow child themes/plugins to filter the trail array. */
	$trail = apply_filters( 'avia_breadcrumbs_trail', $trail, $args );

	/* Connect the breadcrumb trail if there are items in the trail. */
	if ( is_array( $trail ) ) {

		$el_tag = "span";
		$vocabulary = "";

		//google rich snippets
		if($richsnippet === true)
		{
			$vocabulary = 'xmlns:v="http://rdf.data-vocabulary.org/#"';
		}

		/* Open the breadcrumb trail containers. */
		$breadcrumb = '<div class="breadcrumb breadcrumbs avia-breadcrumbs"><div class="breadcrumb-trail" '.$vocabulary.'>';

		/* If $before was set, wrap it in a container. */
		if ( !empty( $before ) )
			$breadcrumb .= '<'.$el_tag.' class="trail-before">' . $before . '</'.$el_tag.'> ';

		/* Wrap the $trail['trail_end'] value in a container. */
		if ( !empty( $trail['trail_end'] ) )
		{
			if(!is_search())
			{
				$trail['trail_end'] =  avia_backend_truncate($trail['trail_end'], $truncate, " ", $pad="...", false, '<strong><em><span>', true);
			}
			$trail['trail_end'] = '<'.$el_tag.' class="trail-end">' . $trail['trail_end'] . '</'.$el_tag.'>';
		}

		if($richsnippet === true)
		{
			foreach($trail as $key => &$link)
			{
				if("trail_end" == $key) continue;

				$link = preg_replace('!rel=".+?"|rel=\'.+?\'|!',"", $link);
				$link = str_replace('<a ', '<a rel="v:url" property="v:title" ', $link);
				$link = '<span typeof="v:Breadcrumb">'.$link.'</span>';
			}
		}


		/* Format the separator. */
		if ( !empty( $separator ) )
			$separator = '<span class="sep">' . $separator . '</span>';

		/* Join the individual trail items into a single string. */
		$breadcrumb .= join( " {$separator} ", $trail );

		/* If $after was set, wrap it in a container. */
		if ( !empty( $after ) )
			$breadcrumb .= ' <span class="trail-after">' . $after . '</span>';

		/* Close the breadcrumb trail containers. */
		$breadcrumb .= '</div></div>';
	}

	/* Allow developers to filter the breadcrumb trail HTML. */
	$breadcrumb = apply_filters( 'avia_breadcrumbs', $breadcrumb );

	/* Output the breadcrumb. */
	if ( $echo )
		echo $breadcrumb;
	else
		return $breadcrumb;

} // End avia_breadcrumbs()

/*-----------------------------------------------------------------------------------*/
/* avia_breadcrumbs_get_parents() - Retrieve the parents of the current page/post */
/*-----------------------------------------------------------------------------------*/
/**
 * Gets parent pages of any post type or taxonomy by the ID or Path.  The goal of this function is to create
 * a clear path back to home given what would normally be a "ghost" directory.  If any page matches the given
 * path, it'll be added.  But, it's also just a way to check for a hierarchy with hierarchical post types.
 *
 * @since 3.7.0
 * @param int $post_id ID of the post whose parents we want.
 * @param string $path Path of a potential parent page.
 * @return array $trail Array of parent page links.
 */
function avia_breadcrumbs_get_parents( $post_id = '', $path = '' ) {

	/* Set up an empty trail array. */
	$trail = array();

	/* If neither a post ID nor path set, return an empty array. */
	if ( empty( $post_id ) && empty( $path ) )
		return $trail;

	/* If the post ID is empty, use the path to get the ID. */
	if ( empty( $post_id ) ) {

		/* Get parent post by the path. */
		$parent_page = get_page_by_path( $path );

		/* ********************************************************************
		Modification: The above line won't get the parent page if
		the post type slug or parent page path is not the full path as required
		by get_page_by_path. By using get_page_with_title, the full parent
		trail can be obtained. This may still be buggy for page names that use
		characters or long concatenated names.
		Author: Byron Rode
		Date: 06 June 2011
		******************************************************************* */

		if( empty( $parent_page ) )
		        // search on page name (single word)
			$parent_page = get_page_by_title ( $path );

		if( empty( $parent_page ) )
			// search on page title (multiple words)
			$parent_page = get_page_by_title ( str_replace( array('-', '_'), ' ', $path ) );

		/* End Modification */

		/* If a parent post is found, set the $post_id variable to it. */
		if ( !empty( $parent_page ) )
			$post_id = $parent_page->ID;
	}

	/* If a post ID and path is set, search for a post by the given path. */
	if ( $post_id == 0 && !empty( $path ) ) {

		/* Separate post names into separate paths by '/'. */
		$path = trim( $path, '/' );
		preg_match_all( "/\/.*?\z/", $path, $matches );

		/* If matches are found for the path. */
		if ( isset( $matches ) ) {

			/* Reverse the array of matches to search for posts in the proper order. */
			$matches = array_reverse( $matches );

			/* Loop through each of the path matches. */
			foreach ( $matches as $match ) {

				/* If a match is found. */
				if ( isset( $match[0] ) ) {

					/* Get the parent post by the given path. */
					$path = str_replace( $match[0], '', $path );
					$parent_page = get_page_by_path( trim( $path, '/' ) );

					/* If a parent post is found, set the $post_id and break out of the loop. */
					if ( !empty( $parent_page ) && $parent_page->ID > 0 ) {
						$post_id = $parent_page->ID;
						break;
					}
				}
			}
		}
	}

	/* While there's a post ID, add the post link to the $parents array. */
	while ( $post_id ) {

		/* Get the post by ID. */
		$page = get_page( $post_id );

		/* Add the formatted post link to the array of parents. */
		$parents[]  = '<a href="' . get_permalink( $post_id ) . '" title="' . esc_attr( get_the_title( $post_id ) ) . '">' . get_the_title( $post_id ) . '</a>';

		/* Set the parent post's parent to the post ID. */
		if(is_object($page))
		{
			$post_id = $page->post_parent;
		}
		else
		{
			$post_id = "";
		}
	}

	/* If we have parent posts, reverse the array to put them in the proper order for the trail. */
	if ( isset( $parents ) )
		$trail = array_reverse( $parents );

	/* Return the trail of parent posts. */
	return $trail;

} // End avia_breadcrumbs_get_parents()

/*-----------------------------------------------------------------------------------*/
/* avia_breadcrumbs_get_term_parents() - Retrieve the parents of the current term */
/*-----------------------------------------------------------------------------------*/
/**
 * Searches for term parents of hierarchical taxonomies.  This function is similar to the WordPress
 * function get_category_parents() but handles any type of taxonomy.
 *
 * @since 3.7.0
 * @param int $parent_id The ID of the first parent.
 * @param object|string $taxonomy The taxonomy of the term whose parents we want.
 * @return array $trail Array of links to parent terms.
 */
function avia_breadcrumbs_get_term_parents( $parent_id = '', $taxonomy = '' ) {

	/* Set up some default arrays. */
	$trail = array();
	$parents = array();

	/* If no term parent ID or taxonomy is given, return an empty array. */
	if ( empty( $parent_id ) || empty( $taxonomy ) )
		return $trail;

	/* While there is a parent ID, add the parent term link to the $parents array. */
	while ( $parent_id ) {

		/* Get the parent term. */
		$parent = get_term( $parent_id, $taxonomy );

		/* Add the formatted term link to the array of parent terms. */
		$parents[] = '<a href="' . get_term_link( $parent, $taxonomy ) . '" title="' . esc_attr( $parent->name ) . '">' . $parent->name . '</a>';

		/* Set the parent term's parent as the parent ID. */
		$parent_id = $parent->parent;
	}

	/* If we have parent terms, reverse the array to put them in the proper order for the trail. */
	if ( !empty( $parents ) )
		$trail = array_reverse( $parents );

	/* Return the trail of parent terms. */
	return $trail;

} // End avia_breadcrumbs_get_term_parents()
