<?php

function remove_avia_search(){
    remove_filter( 'wp_nav_menu_items', 'avia_append_search_nav', 10, 2 );
}
add_action( 'init' , 'remove_avia_search' );

// Add specific CSS class by filter
add_filter( 'body_class', 'abhc_add_subpage_classes' );
function abhc_add_subpage_classes( $classes ) {
	//die('dead');
	global $post;
	$ancestors = get_post_ancestors( $post );
	//$key will be applied as body class when $value is post id
	$id_classes = array( 
		'consumers_families_descendant' => 74,
		'providers_descendant' => 121,
		'about_alliance_descendant' => 149,
	);

	//make sure $ancestors is an array
	if ( is_array($ancestors) ) {
		//loop through each class name/id association
		foreach ($id_classes as $class_name => $post_id) {
			//if id association is an ancestor
			if ( in_array($post_id, $ancestors) || $post_id == $post->ID ) {
				// add 'class-name' to the $classes array
				$classes[] = $class_name;
			}
		}
	}
	
	// return the $classes array
	return $classes;
}

add_theme_support('avia_template_builder_custom_css');
add_filter( 'the_content', 'add_last_modified_the_content_filter', 90 );
/**
 * Add a icon to the beginning of every post page.
 *
 * @uses is_single()
 */

function add_last_modified_the_content_filter( $content ) {
	global $post;
    if ( is_single() || is_page() && !is_page( 19843 ) && !is_page( 361 ) && !is_page( 20161 ) && !is_page( 24892 ) )
        // WP Curve AB #107553 Add gravity forms
			$content = $content .'<div class="gravity-form"><br/><hr style="margin-top:10px;">'. do_shortcode( '[gravityform id="6" title="false" description="false" ajax=true]' ) . '</div><div class="last_modified" style="font-size:.8em;">Page last modified: ' . get_the_modified_date() . '</div>';
		// WP Curve AB #107553 End
    return $content;
}

if(!function_exists('avia_which_archive'))
{
	/**
	 *  checks which archive we are viewing and returns the archive string
	 */

	function avia_which_archive()
	{
		$output = "";

		if ( is_category() )
		{
			$output = __('','avia_framework')." ".single_cat_title('',false);
		}
		elseif (is_day())
		{
			$output = __('','avia_framework')." ".get_the_time( __('F jS, Y','avia_framework') );
		}
		elseif (is_month())
		{
			$output = __('','avia_framework')." ".get_the_time( __('F, Y','avia_framework') );
		}
		elseif (is_year())
		{
			$output = __('','avia_framework')." ".get_the_time( __('Y','avia_framework') );
		}
		elseif (is_search())
		{
			global $wp_query;
			if(!empty($wp_query->found_posts))
			{
				if($wp_query->found_posts > 1)
				{
					$output =  $wp_query->found_posts ." ". __('search results for:','avia_framework')." ".esc_attr( get_search_query() );
				}
				else
				{
					$output =  $wp_query->found_posts ." ". __('search result for:','avia_framework')." ".esc_attr( get_search_query() );
				}
			}
			else
			{
				if(!empty($_GET['s']))
				{
					$output = __('Search results for:','avia_framework')." ".esc_attr( get_search_query() );
				}
				else
				{
					$output = __('To search the site please enter a valid term','avia_framework');
				}
			}

		}
		elseif (is_author())
		{
			$curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
			$output = __('Author Archive','avia_framework')." ";

			if(isset($curauth->nickname) && isset($curauth->ID))
            {
                $name = apply_filters('avf_author_nickname', $curauth->nickname, $curauth->ID);
		$output .= __('for:','avia_framework') ." ". $name;
            }

		}
		elseif (is_tag())
		{
			$output = __('Tag Archive for:','avia_framework')." ".single_tag_title('',false);
		}
		elseif(is_tax())
		{
			$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
			$output = __('Archive for:','avia_framework')." ".$term->name;
		}
		else
		{
			$output = __('Archives','avia_framework')." ";
		}

		if (isset($_GET['paged']) && !empty($_GET['paged']))
		{
			$output .= " (".__('Page','avia_framework')." ".$_GET['paged'].")";
		}

        	$output = apply_filters('avf_which_archive_output', $output);
        	
		return $output;
	}
}

//set builder mode to debug
/*
add_action('avia_builder_mode', "builder_set_debug");
function builder_set_debug()
{
	return "debug";
}

/*ILI Form - pre-submission review*/
add_action("gform_pre_submission", "pre_submission");


/*ADded by Liam BAiley*/
//add_action('init','wswp_correcting_events');

function wswp_correcting_events() {
    if (is_admin() || $_SERVER['REMOTE_ADDR'] != "81.131.238.88")
        return;
    foreach(EM_Events::get() as $key => $event) {
        
        if (!strstr(get_post_permalink($event->post_id),'/events/')) {
            var_dump($event->post_id);
            echo "<p><p>Event ID= ". $event->event_id . " and post_id=" .  $event->post_id . " and link = ".get_post_permalink($event->post_id) ."</p></p>";
        }
    }
    
}

add_filter('avf_title_args', 'fix_single_post_title', 10, 2);
function fix_single_post_title($args,$id){
	if ( $args['title'] == 'Blog - Latest News' ){
		$category = get_the_category(); 
		$args['title'] = $category[0]->cat_name;
	}
	if(is_singular('event')){ $args['title'] = 'Events';  }
	return $args;
}

/*-----------------------------------------------------------------------------------*/
/* Remove Unwanted Admin Menu Items */
/*-----------------------------------------------------------------------------------*/

function remove_admin_menu_items() {
	$remove_menu_items = array(__('Links'),__('Portfolio'),__('sfi_settings'),__('Dashboard'));
	global $menu;
	end ($menu);
	while (prev($menu)){
		$item = explode(' ',$menu[key($menu)][0]);
		if(in_array($item[0] != NULL?$item[0]:"" , $remove_menu_items)){
		unset($menu[key($menu)]);}
	}
}

/*-----------------------------------------------------------------------------------*/
/* Remove Menu Items for NotificationsEdit Users */
/*-----------------------------------------------------------------------------------*/

function remove_menus()
{
    global $menu;
    global $current_user;
    get_currentuserinfo();
    if($current_user->user_login == 'notificationsedit')
    {
        $restricted = array(__('Media'),
                            __('Links'),
                            __('Pages'),
                            __('Comments'),
                            __('Appearance'),
                            __('Plugins'),
                            __('Users'),
                            __('Tools'),
                            __('Settings'),
                            __('Posts'),
                            __('Profile'),
        );
		remove_menu_page( 'wpfilebase_manage' );
		remove_menu_page( 'admin.php?page=tablepress' );
		remove_menu_page( 'edit.php?post_type=event' );
		remove_menu_page( 'edit.php?post_type=portfolio' );
        end ($menu);
        while (prev($menu)){
            $value = explode(' ',$menu[key($menu)][0]);
            if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
        }// end while
    }// end if
}
add_action('admin_menu', 'remove_menus'); 

/*-----------------------------------------------------------------------------------*/
/* Defer parshing of JS */
/*-----------------------------------------------------------------------------------
if (!(is_user_logged_in() )) {
function defer_parsing_of_js ( $url ) {
if ( FALSE === strpos( $url, '.js' ) ) return $url;
if ( strpos( $url, 'jquery.js' ) ) return $url;
return "$url' defer ";
}
add_filter( 'clean_url', 'defer_parsing_of_js', 11, 1 );
}
*/
/*-----------------------------------------------------------------------------------*/
/* Remove query strings to improve site speed */
/*-----------------------------------------------------------------------------------*

function _remove_script_version( $src ){
    $parts = explode( '?ver', $src );
        return $parts[0];
}
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );
*/
/*-----------------------------------------------------------------------------------*/
/* EWWW folder skipping script */
/*-----------------------------------------------------------------------------------

add_filter( 'ewww_image_optimizer_bypass', 'ewww_skip_theme', 10, 2 );
function ewww_skip_theme ( $bypass, $filename ) {
  $folder_to_skip = '/nas/wp/www/cluster-40160/alliancebhc/wp-content/themes/';  // change this line with the appropriate path to the themes/ folder
  if ( preg_match( "|^$folder_to_skip|", $filename ) ) {
    return true;
  } else {
    return false;
  }
}
*/
/*-----------------------------------------------------------------------------------*/
/* SearchWP Customizations */
/*-----------------------------------------------------------------------------------*/

add_filter( 'avf_ajax_search_function', 'avia_init_searchwp', 10, 4 );

function avia_init_searchwp( $function_name, $search_query, $search_parameters, $defaults ) {
	$function_name = class_exists( 'SearchWP' ) ? 'avia_searchwp_search' : $function_name;
	return $function_name;
}

function avia_searchwp_search( $search_query, $search_parameters, $defaults ) {
	$searchwp = SearchWP::instance();
	$engine_name = 'default'; // you can swap in your Supplemental Search Engine name
	parse_str( $search_query, $output );
	$search_string = isset( $output['s'] ) ? sanitize_text_field( urldecode( $output['s'] ) ) : '';

	// limit the results to 5
	function avia_searchwp_search_num_results() {
		return 5;
	}
	add_filter( 'searchwp_posts_per_page', 'avia_searchwp_search_num_results' );

	$posts = $searchwp->search( $engine_name, $search_string );
	return $posts;
}

add_filter( 'searchwp_missing_integration_notices', '__return_false' );

add_filter( 'author_link', 'my_author_link' );
 
function my_author_link() {
    return home_url();
}

//set builder mode to debug
add_action('avia_builder_mode', "builder_set_debug");
function builder_set_debug()
{
	return "debug";
}
/**
 * Enable debugging in SearchWP
 * AFTER ADDING ENSURE debug.log WAS ADDED TO ~/wp-content/plugins/searchwp
 * If debug.log does not exist, please verify write permissions to the 
 * SearchWP folder and try again
 */
add_filter( 'searchwp_debug', '__return_true' );

function edit_admin_menus() {
    global $menu;
     
    $menu[10][0] = 'Documents & Media'; // Change Posts to Recipes
}
add_action( 'admin_menu', 'edit_admin_menus' );

/**
 * Gravity Wiz // Gravity Forms // Set Number of List Field Rows by Field Value
 *
 * Add/remove list field rows automatically based on the value entered in the specified field. Removes the add/remove
 * that normally buttons next to List field rows.
 *
 * @version	  1.0
 * @author    David Smith <david@gravitywiz.com>
 * @license   GPL-2.0+
 * @link      http://gravitywiz.com/2012/06/03/set-number-of-list-field-rows-by-field-value/
 */
class GWAutoListFieldRows {

	private static $_is_script_output;

	function __construct( $args ) {

		$this->_args = wp_parse_args( $args, array(
			'form_id'       => false,
			'input_html_id' => false,
			'list_field_id' => false
		) );

		extract( $this->_args ); // gives us $form_id, $input_html_id, and $list_field_id

		if( ! $form_id || ! $input_html_id || ! $list_field_id )
			return;

		add_filter( 'gform_pre_render_' . $form_id, array( $this, 'pre_render' ) );

	}

	function pre_render( $form ) {
		?>

		<style type="text/css"> #field_<?php echo $form['id']; ?>_<?php echo $this->_args['list_field_id']; ?> .gfield_list_icons { display: none; } </style>

		<?php

		add_filter( 'gform_register_init_scripts', array( $this, 'register_init_script' ) );

		if( ! self::$_is_script_output )
			$this->output_script();

		return $form;
	}

	function register_init_script( $form ) {

		// remove this function from the filter otherwise it will be called for every other form on the page
		remove_filter( 'gform_register_init_scripts', array( $this, 'register_init_script' ) );

		$args = array(
			'formId'      => $this->_args['form_id'],
			'listFieldId' => $this->_args['list_field_id'],
			'inputHtmlId' => $this->_args['input_html_id']
		);

		$script = "new gwalfr(" . json_encode( $args ) . ");";
		$key = implode( '_', $args );

		GFFormDisplay::add_init_script( $form['id'], 'gwalfr_' . $key , GFFormDisplay::ON_PAGE_RENDER, $script );

	}

	function output_script() {
		?>

		<script type="text/javascript">

			window.gwalfr;

			(function($){

				gwalfr = function( args ) {

					this.formId      = args.formId,
						this.listFieldId = args.listFieldId,
						this.inputHtmlId = args.inputHtmlId;

					this.init = function() {

						var gwalfr = this,
							triggerInput = $( this.inputHtmlId );

						// update rows on page load
						this.updateListItems( triggerInput, this.listFieldId, this.formId );

						// update rows when field value changes
						triggerInput.change(function(){
							gwalfr.updateListItems( $(this), gwalfr.listFieldId, gwalfr.formId );
						});

					}

					this.updateListItems = function( elem, listFieldId, formId ) {

						var listField = $( '#field_' + formId + '_' + listFieldId ),
							count = parseInt( elem.val() );
						rowCount = listField.find( 'table.gfield_list tbody tr' ).length,
							diff = count - rowCount;

						if( diff > 0 ) {
							for( var i = 0; i < diff; i++ ) {
								listField.find( '.add_list_item:last' ).click();
							}
						} else {

							// make sure we never delete all rows
							if( rowCount + diff == 0 )
								diff++;

							for( var i = diff; i < 0; i++ ) {
								listField.find( '.delete_list_item:last' ).click();
							}

						}
					}

					this.init();

				}

			})(jQuery);

		</script>

		<?php
	}
}
// EXAMPLE #1: Number field for the "input_html_id"
new GWAutoListFieldRows( array(
	'form_id' => 7,
	'list_field_id' => 8,
	'input_html_id' => '#input_7_19'
) );

/**
* Move RSVP Tickets form in events template
*/
if (class_exists('Tribe__Tickets__RSVP')) {
    remove_action( 'tribe_events_single_event_after_the_meta', array( Tribe__Tickets__RSVP::get_instance(), 'front_end_tickets_form' ), 5 );
    add_action( 'tribe_events_single_event_before_the_content', array( Tribe__Tickets__RSVP::get_instance(), 'front_end_tickets_form' ), 5 );
};


// Link directly to Media files instead of Attachment pages in search results
function my_search_media_direct_link( $permalink, $post ) {
	if ( is_search() && 'attachment' === get_post_type( $post ) ) {
		$permalink = wp_get_attachment_url( $post->ID );
	}
	return esc_url( $permalink );
}
add_filter( 'the_permalink', 'my_search_media_direct_link', 10, 2 );

add_filter('avf_template_builder_content', 'avf_template_builder_content_title', 10, 1);
function avf_template_builder_content_title($content = "")
{
	$title = avia_title();
	return $title . $content;
}