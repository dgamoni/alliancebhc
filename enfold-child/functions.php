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
	global $post;
	$title = '';
	$alliancebhc_slider_in_header = get_field('alliancebhc_slider_in_header', $post->ID );
	if(!$alliancebhc_slider_in_header):
		$title = avia_title();
	endif;
	return $title . $content;
}

// dg

// add body class for options title_bar_breadcrumb
add_filter('body_class','my_class_names');
function my_class_names( $classes ) {
	if( is_page() ) {
		global $avia_config;
		$header_settings = avia_header_setting();
		if($header_settings['header_title_bar'] == 'title_bar_breadcrumb') {
			$classes[] = 'title_bar_breadcrumb';
		} else if ($header_settings['header_title_bar']=='breadcrumbs_only'){
			$classes[] = 'breadcrumbs_only';
		} else if ($header_settings['header_title_bar']=='title_bar'){
			$classes[] = 'title_bar';
		}
	}
	return $classes;
}

// set_home init
//add_action( 'init', 'init_set_home_for_user' );
// function init_set_home_for_user() {
// 	global $current_user;

// 	$set_home = get_user_meta( $current_user->ID, 'set_home');
// 	if($set_home){
// 		//add_user_meta($current_user->ID, 'set_home', false );
// 	}

// 	// if(is_user_logged_in()){
// 	// 	add_user_meta($current_user->ID, 'set_home', true );
// 	// }
// }

//init redirect
add_action('template_redirect', 'redirect_to_home_');
function redirect_to_home_() {
	 if(is_user_logged_in() && !is_admin()) {
	 	global $current_user;
	 	//add_user_meta($current_user->ID, 'set_home', true );//test
	 	//delete_user_meta($current_user->ID, 'set_home');//test
		$set_home = get_user_meta( $current_user->ID, 'set_home');
		if(($set_home[0]==1) && is_front_page()) {
			$location = get_site_url() . "/providers";
			wp_redirect( $location, 301 );
			//wp_redirect(home_url());
	    	exit();
		 }
	} else if( isset($_COOKIE['set_home']) && !is_admin()) {
		$set_home = $_COOKIE['set_home'];
		if(($set_home == 'I dont want this as my homepage') && is_front_page()) {
			$location = get_site_url() . "/providers";
			wp_redirect( $location, 301 );
			//wp_redirect(home_url());
	    	exit();
		}
	}
}

function check_set_home(){
	if(is_user_logged_in() && !is_admin()) {
		global $current_user;
		$set_home = get_user_meta( $current_user->ID, 'set_home');
		if($set_home[0]==1){
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function check_set_home_cook(){
	if( !is_user_logged_in() && !is_admin() && isset($_COOKIE['set_home']) ) {

		$set_home = $_COOKIE['set_home'];
		
		if($set_home == 'I dont want this as my homepage'){
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

// set home click js
add_action('wp_footer', 'add_custom_css');
function add_custom_css() {
	global $current_user;
	?>
	<script>
		jQuery(document).ready(function($) {

// $('#play-button').click(function()
// {
//     wistiaApi.play();
// });

// $('.open-popup-link').magnificPopup({
//   type: 'iframe',
//   iframe: {
//     patterns: {
//       wistia: {
//         index: 'wistia.com',
//         id: function(url) {        
//             var m = url.match(/^.+wistia.com\/(medias)\/([^_]+)[^#]*(#medias=([^_&]+))?/);
//             if (m !== null) {
//                 if(m[4] !== undefined) {
//                     return m[4];
//                 }
//                 return m[2];
//             }
//             return null;
//         },
//         src: '//fast.wistia.net/embed/iframe/%id%?autoPlay=1' // https://wistia.com/doc/embed-options#options_list
//       }
//     }
//   }
// });

// $('.open-popup-link').magnificPopup({
//   type:'inline',
//   closeBtnInside: true,
//   fixedContentPos: true,
//   mainClass: 'mfp-no-margins mfp-with-zoom',
//   midClick: true, // allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source.
  
//   // Delay in milliseconds before popup is removed
//   removalDelay: 300,

//   // Class that is added to popup wrapper and background
//   // make it unique to apply your CSS animations just to this exact popup
//   mainClass: 'mfp-fade'
// });

			$('.wp-embedded-content').addClass('popover=true');

			//$('body').avia_activate_lightbox();

			$('.customtitle_container.provider-news .sep').last().css('display', 'none');
			$('.customtitle_container.general-news-announcements .sep').last().css('display', 'none');
			
			<?php if(check_set_home()){	echo "$('#providers').val('I dont want this as my homepage')";} else if(check_set_home_cook()){	echo "$('#providers').val('I dont want this as my homepage')";} ?>

			$('#providers').on('click', function() {
				var set_home = $(this).val();
				console.log('click'+set_home);
				console.log('user: '+<?php echo $current_user->ID;?>);
		        $.ajax({
		            type    : "POST",
		            url     : "<?php echo admin_url('admin-ajax.php'); ?>",
		            dataType: "json",
		            data    : "action=alliancebhc_sethomepage_cook&set_home=" + set_home+"&user=<?php echo $current_user->ID; ?>",
		            success : function (a) {
		                console.log(a);
		                if(!a.error) {
		                	$('#providers').val(a.set_home);
		                }else {
		                	$('#providers').after('<p class="error">'+a.error+'</p>');
		                }
		                
		            }
		        });//end ajax
			});

			if ($('.on_image').length > 0) {
				var on_image_text = $('.on_image .avia_textblock').html(); 
				console.log(on_image_text);
				$('.on_image').hide();
				$('#background-main .container').addClass('background_on_image').html(on_image_text);
			 }
			 if( $('#background-main'.length > 0) ) {
			 	$('body').addClass('slider_background_enable');
			 }
		});
	</script>
	<style>

	#top .title_container span {
	    font-size: 1.1em;
	}
.resource_image_wrap {

}
.resource_image_wrap img {
	
}
#top.page-template-template-thought-leadership-php #main .sidebar{
	padding-top: 50px;
}

#top.parent-slug-thought-leadership #main .sidebar {
	/*padding-top: 30px;	*/
	padding-top: 25px;	
}
.parent-slug-thought-leadership.slider_background_enable .container .av-content-small.units {
	/*padding-top: 0;*/
}


.template-page .entry-content-wrapper h2.additional_resources_title {
	text-align: center;
	padding-bottom: 15px;
}
.main_color a.lead-child-link {
	text-decoration: none;
	color: black;
}

a.lead-child-link:hover {

}
.lead-child-avia-content .gravity-form {
	display: none;
}
.customtitle_container.provider-news .trail-end {
	display: none;
}
.customtitle_container.general-news-announcements .trail-end {
	display: none;
}
		div .padding_main_content .av_three_fifth {
			width: 63%;
		}
		.slider_background_enable div .padding_main_content .av_three_fifth {
			padding-top: 0 !important;
		}
		div .padding_main_content .av_two_fifth {
			margin-left: 0;
		}

		.title_wrap {
			position: absolute;
		    bottom: 0px;
		    padding: 25px 25px 0px 25px;
		    width: 63%;
		    background-color: white;
		}
		.title_wrap h1 {
			padding-bottom: 0;
		    margin-bottom: 0;
		}
		.customtitle_container.title_container {
			height: 26px !important;
		}
		#top .title_container .container {
		    padding-top: 0px;
		    padding-bottom: 0px;
		    min-height: 15px;
		}
		.slider_background_enable {

		}
		#top.slider_background_enable #main .avia-section .template-page {
		    padding-top: 0;
		}
		.fixmargin {
			padding-top: 50px;
		}
		.slider_background_enable div .padding_main_content .av_two_fifth {
		    margin-top: 50px;
		}

		.breadcrumbs_only .title_container {
		  display: none;
		}
		.breadcrumbs_only .container_wrap .title_container {
		  display: block;
		}

		.title_bar .title_container {
		  display: none;
		}
		.title_bar .container_wrap .title_container {
		  display: block;
		}
		.slider_background_enable .container .av-content-small.units {
		    width: 63%;
		    padding: 25px;
		}
		#home-background-main {
			background-size: cover;
			background-position: top right;
		}
		.ubermenu .ubermenu-item-28587 .ubermenu-custom-content-padded,
		.ubermenu .ubermenu-item-28264 .ubermenu-custom-content-padded,
		.ubermenu .ubermenu-item-28305 .ubermenu-custom-content-padded,
		.ubermenu .ubermenu-item-28588 .ubermenu-custom-content-padded,
		.ubermenu .ubermenu-item-28520 .ubermenu-custom-content-padded {
			padding-top: 5px;
		}
		.ubermenu .ubermenu-item-28587 .ubermenu-custom-content-padded p,
		.ubermenu .ubermenu-item-28264 .ubermenu-custom-content-padded p,
		.ubermenu .ubermenu-item-28305 .ubermenu-custom-content-padded p,
		.ubermenu .ubermenu-item-28588 .ubermenu-custom-content-padded p,
		.ubermenu .ubermenu-item-28520 .ubermenu-custom-content-padded p {
			margin-top: 0px;
		}
		.bellows .bellows-nav .bellows-menu-item-28059 p {
			padding-bottom: 15px;
		}
		#top.post-type-archive-tribe_events .alternate_color.title_container .main-title,
		#top.single-tribe_events .alternate_color.title_container .main-title,
		.single-tribe_events .alternate_color .breadcrumb,
		.single-tribe_events .title_container  {
			display:none !important;
		}
		.single-post .title_container {
		    display: none;
		}
		.single-post .customtitle_container.title_container	{
			display: block;
		}
		.sidebar {
    		 padding-top: 0px;
    	} 
    	.bellows {
    		margin: 25px 0;
    	}
    	@media only screen and (max-width: 767px) {
    		.title_wrap {
    			width: 100%;
    		}
    		.responsive #top .container .av-content-small {
			    width: 92%;
			}
    	}
    	.elsels1 {
    		/*padding: 0 9px;*/
    		display: inline-block;
		    text-align: center;
		    width: 282px;
    	}
    	.sidebar_right {
		    padding-top: 0.85em;
    	}
    	#background-main {
    		background-repeat: no-repeat;
    		background-attachment: scroll !important;
    		background-position: center bottom !important; 
    	}
    	.post-type-archive-tribe_events .alternate_color .breadcrumb{
    		display: none !important;
    	}
    	#main > div.container_wrap.container_wrap_first.main_color.fullsize > div > main {
		    margin-left: 25px;
		    margin-top: inherit;
		}
		.consumers_families_descendant #av_section_3 {
			margin-left:25px;
		}
		#top.providers_descendant #main .avia-section .template-page.av-content-full,
		body.providers_descendant .av-content-full.units.alpha {
			margin-left:25px;
		}
		#menu-item-28087 h3 {
			text-align: left !important;
		    font-size: 22px;
		    color: #333;
		    font-weight: 400;
		}
		#menu-item-30543 span {
			text-align: left !important;
		    font-size: 22px;
		    color: #333;
		    font-weight: 400;
		}
		#bellows_navigation_widget-4 {
			margin-top: -24px;
		}
		#bellows_navigation_widget-3 {
			/*margin-top: -34px;*/
			margin-top: -24px;
		}
		#advanced_menu_toggle:before,
		#advanced_menu_hide:before  {
			display: none;
		}
		.post-type-archive-tribe_events .title_container {
		    height: 0px !important;
		    display: none;
		}
		.bellows_navigation_widget-class .bellows .bellows-nav .bellows-menu-item.bellows-item-level-2 span {
			padding-left: 15px;
		}
	</style>
	<?php
}

// set home click ajax
add_action( 'wp_ajax_alliancebhc_sethomepage', 'alliancebhc_set_homepage' );
add_action( 'wp_ajax_nopriv_alliancebhc_sethomepage', 'alliancebhc_set_homepage' );
function alliancebhc_set_homepage() {
	if ( count( $_POST ) > 0 && $_POST['user']) {
		$sett = $_POST['set_home'];
		$user = $_POST['user'];
		
		if($sett == 'Make this my default homepage') {
			$set_res = 'I dont want this as my homepage';
	 	    update_user_meta(intval($user), 'set_home', true );
	 	    if ( get_user_meta(intval($user),  'set_home', true ) != true ){
	 	    	$res['message'] = 'not set';
	 	    }else {
	 	    	$res['message'] = 'setok'.get_user_meta(intval($user),  'set_home', true );
	 	    }
	

		} else if ($sett == 'I dont want this as my homepage'){
			$set_res = 'Make this my default homepage';
			update_user_meta(intval($user), 'set_home', false );
			if ( get_user_meta(intval($user),  'set_home', true ) != false ){
	 	    	$res['message'] = 'not set';
	 	    }else {
	 	    	$res['message'] = 'setok'.get_user_meta(intval($user),  'set_home', true );
	 	    }
		} else {

		}

		$res['set_home'] = $set_res;
		$res['user'] = $user;
		echo json_encode( $res );
		exit;
	} else {
		$res['error'] = 'You must login';
		echo json_encode( $res );
		exit;
	}
}

// set home click ajax+ cookies
add_action( 'wp_ajax_alliancebhc_sethomepage_cook', 'alliancebhc_set_homepage_cook' );
add_action( 'wp_ajax_nopriv_alliancebhc_sethomepage_cook', 'alliancebhc_set_homepage_cook' );
function alliancebhc_set_homepage_cook() {
	if ( count( $_POST ) > 0 && $_POST['user']) {
		$sett = $_POST['set_home'];
		$user = $_POST['user'];
		
		if($sett == 'Make this my default homepage') {
			$set_res = 'I dont want this as my homepage';
	 	    update_user_meta(intval($user), 'set_home', true );
	 	    if ( get_user_meta(intval($user),  'set_home', true ) != true ){
	 	    	$res['message'] = 'not set';
	 	    }else {
	 	    	$res['message'] = 'setok'.get_user_meta(intval($user),  'set_home', true );
	 	    }
	

		} else if ($sett == 'I dont want this as my homepage'){
			$set_res = 'Make this my default homepage';
			update_user_meta(intval($user), 'set_home', false );
			if ( get_user_meta(intval($user),  'set_home', true ) != false ){
	 	    	$res['message'] = 'not set';
	 	    }else {
	 	    	$res['message'] = 'setok'.get_user_meta(intval($user),  'set_home', true );
	 	    }
		} else {

		}

		$res['set_home'] = $set_res;
		$res['user'] = $user;
		echo json_encode( $res );
		exit;
	} else if ( count( $_POST ) > 0 && $_POST['user'] == 0) { 
		$sett = $_POST['set_home'];

		
		if($sett == 'Make this my default homepage') {
			$set_res = 'I dont want this as my homepage';
			setcookie( 'set_home', $set_res, strtotime('+3 day'), '/' );
			if(isset($_COOKIE['set_home'])){
				$res['message'] = 'setok'.$_COOKIE['set_home'];
			}else{
				$res['message'] = 'not set';
			}
		} else if ($sett == 'I dont want this as my homepage'){
			$set_res = 'Make this my default homepage';
			setcookie( 'set_home', $set_res, strtotime('+3 day'), '/' );
			if(isset($_COOKIE['set_home'])){
				$res['message'] = 'setok'.$_COOKIE['set_home'];
			}else{
				$res['message'] = 'not set';
			}
		} else {

		}

		$res['set_home'] = $set_res;
		$res['user'] = $user;
		echo json_encode( $res );
		exit;
	} else {
		$res['error'] = 'Error!';
		echo json_encode( $res );
		exit;
	}
}

// Replace avia.js
function change_aviajs() {
   wp_dequeue_script( 'avia-default' );
   wp_enqueue_script( 'avia-default-child', get_stylesheet_directory_uri().'/js/avia.js', array('jquery'), 2, true );
}
//add_action( 'wp_enqueue_scripts', 'change_aviajs', 100 );

function my_uber_add_subcontent( $content , $post , $item_id ){
 
   $content.= '<span class="ubermenu-target-description ubermenu-target-text">';
   $content.= mysql2date( get_option( 'date_format' ), $post->post_date );
   $content.= '</span>';
 
   return $content;
}
add_filter( 'ubermenu_dp_subcontent' , 'my_uber_add_subcontent' , 10 , 3 );

add_filter('avia_load_shortcodes', 'avia_include_shortcode_template', 15, 1);
function avia_include_shortcode_template($paths)
{
	$template_url = get_stylesheet_directory();
    	array_unshift($paths, $template_url.'/shortcodes/');

	return $paths;
}




/* Seal Alliance Image */

function getSealImage() {

	if (!isset($_GET['id'])) {
		die("ID parameter is missing.");
	}

	if (!isset($_GET['side'])) {
		die("Side parameter is missing.");
	}

	$idMember = (int)$_GET['id'];
	if (!validateIdMember($idMember)) {
		die("You don't currenlty have permission to access this image.");
	}

	$referer = sanitize_text_field($_SERVER["SERVER_NAME"]);
	if (!validateRefererUrl($idMember, $referer)) {
		die("You don't currenlty have permission to access this image.");
	}

	// 1: front; 2: back;
	$side = (int)$_GET['side'];
	if ($side != 1 && $side != 2) {
		die("The side value has a wrong value.");
	}

	switch ($side) {
		case 1:
			return getFrontImage();
			break;

		case 2:
			return getBackImage();
			break;
	
	}

}

// checks if the provided ID member exists on the system
function validateIdMember() {
	// check on DB
	return true;
}

// checks if the current Url is associated to the provided ID member
function validateRefererUrl($idMember, $referer) {
	// check on DB
	return true;
}

function getFrontImage() {
	$path = dirname(__FILE__);
	$filename = 'zxilh5erupei2oy928o8srofdbes0nsy.png';
	$pathImage = $path . '/images/' . $filename;
	if (file_exists($pathImage)) {
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $pathImage) . "\n";
		finfo_close($finfo);

		header('Content-Type: ' . $mime);
		header('Content-Disposition: inline; filename="seal-alliance.png"');

		$content = file_get_contents($pathImage);
		echo $content;
	}
}

function getBackImage() {

	$path = dirname(__FILE__) . '/images/';
	$originalImage = $path . "zxilh5erupei2oy928o8srofdbes0nsy.png";
	if(file_exists($originalImage)) {

		$im = imagecreatefrompng($originalImage);
		imagesavealpha($im, true);

		if(!$im) {
			die("Source image not found.");
		}

		$black = imagecolorallocate($im, 0, 0, 0);

		$font_path = dirname(__FILE__) . '/fonts/Roboto-Regular.ttf';
		$font_size = 25;
		$angle = 0;

		// Get image Width and Height
		$image_width = imagesx($im);  
		$image_height = imagesy($im);

		$text = "Text line 1";

		// Get Bounding Box Size
		$text_box = imagettfbbox($font_size, $angle, $font_path, $text);

		// Get your Text Width and Height
		$text_width = $text_box[2] - $text_box[0];
		$text_height = $text_box[7] - $text_box[1];

		// Calculate coordinates of the text
		$x = ($image_width/2) - ($text_width/2);
		$y = ($image_height/2) - ($text_height/2);

		// Set Text to Be Printed On Image

		// Print Text On Image
		imagettftext($im, $font_size, $angle, $x, 100, $black, $font_path, $text);

		$text = "Text line 2";
		imagettftext($im, $font_size, $angle, $x, 200, $black, $font_path, $text);


		$text = "Text line 3";
		imagettftext($im, $font_size, $angle, $x, 300, $black, $font_path, $text);
		
		$outputImage = "seal-alliance-back.png";

		// imagepng($im, $outputImage, 0);

		header ('Content-type: image/png');
		header('Content-Disposition: inline; filename="seal-alliance-back.png"');

		imagepng($im);
		imagedestroy($im);

	}

}

//set builder mode to debug
add_action('avia_builder_mode', "builder_set_debug");
function builder_set_debug()
{
	return "debug";
}


function switch_page_template() {
    global $post;
    // Checks if current post type is a page, rather than a post
	if (is_page())
	{	
		// Checks if page is parent, if yes, return
		if ($post->post_parent == 0)
			return true;
		else if ($post->post_parent != $post->ID)
		{
			$parent_page_template = get_post_meta($post->post_parent,'_wp_page_template',true);
 
			$template = get_stylesheet_directory(). "/{$parent_page_template}";
			if (file_exists($template) && $parent_page_template == 'template-thought-leadership.php') {
				load_template($template);
				exit;
			}
		}
	}
}
 
add_action('template_redirect','switch_page_template');

function subtitle_max_charlength( $subtitle, $charlength ){
  $excerpt = $subtitle;
  $charlength++;

  if ( mb_strlen( $excerpt ) > $charlength ) {
    $subex = mb_substr( $excerpt, 0, $charlength - 5 );
    $exwords = explode( ' ', $subex );
    $excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
    $outputsubex = '';

    if ( $excut < 0 ) {
      $outputsubex .= mb_substr( $subex, 0, $excut );
    } else {
      $outputsubex .= $subex;
    }

    $outputsubex .= '...';
    return $outputsubex;

  } else {
    return $excerpt;
  }
}


function my_body_class( $classes ) {
    global $post;

    # Page
    if ( is_page() ) {
        # Has parent / is sub-page
        if ( $post->post_parent ) {
            # Parent post name/slug
            $parent = get_post( $post->post_parent );
            $classes[] = 'parent-slug-'.$parent->post_name;

            # Parent template name
            // $parent_template = get_post_meta( $parent->ID, '_wp_page_template', true);
            // if ( !empty($parent_template) && $parent_page_template == 'template-thought-leadership.php' ) {
            //     $classes[] = 'parent-template-leadership';
            // }
        }
    }

    return $classes;
}
add_filter( 'body_class', 'my_body_class' );

require_once 'core/load.php'; 


// Replace avia.js
function change_aviajs_() {
	$theme = wp_get_theme();
	if( false !== $theme->parent() )
	{
		$theme = $theme->parent();
	}
	$vn = $theme->get( 'Version' );
    wp_dequeue_script( 'avia-lightbox-activation' );
    wp_enqueue_script( 'avia-lightbox-activation', get_stylesheet_directory_uri().'/js/avia.js', array('jquery'), 2, true );
    wp_enqueue_style(  'avia-popup-css-', get_stylesheet_directory_uri()."/js/aviapopup/magnific-popup.css", array('avia-layout'), $vn, 'screen');
	wp_enqueue_style(  'avia-lightbox', get_stylesheet_directory_uri()."/js/avia-snippet-lightbox.css", array('avia-layout'), $vn, 'screen');
    wp_enqueue_script( 'avia-lightbox-activation', get_stylesheet_directory_uri()."/js/avia-snippet-lightbox.js", array('avia-default'), $vn, true);
	wp_enqueue_script( 'avia-popup-js-' , get_stylesheet_directory_uri().'/js/aviapopup/jquery.magnific-popup.min.js', array('jquery'), $vn, true);

	//wp_enqueue_style(  'apopup-css-child', get_stylesheet_directory_uri()."/js/popup/magnific-popup.css");
	//wp_enqueue_script( 'apopup-js-child' , get_stylesheet_directory_uri().'/js/popup/jquery.magnific-popup.min.js', array('jquery'), 2, true);

}
//add_action( 'wp_enqueue_scripts', 'change_aviajs_', 100 );


/* Parse the video uri/url to determine the video type/source and the video id */
	function parse_video_uri( $url ) {
		
		// Parse the url 
		$parse = parse_url( $url );
		
		// Set blank variables
		$video_type = '';
		$video_id = '';
		
		// Url is http://youtu.be/xxxx
		if ( $parse['host'] == 'youtu.be' ) {
		
			$video_type = 'youtube';
			
			$video_id = ltrim( $parse['path'],'/' );	
			
		}
		
		// Url is http://www.youtube.com/watch?v=xxxx 
		// or http://www.youtube.com/watch?feature=player_embedded&v=xxx
		// or http://www.youtube.com/embed/xxxx
		if ( ( $parse['host'] == 'youtube.com' ) || ( $parse['host'] == 'www.youtube.com' ) ) {
		
			$video_type = 'youtube';
			
			parse_str( $parse['query'] );
			
			$video_id = $v;	
			
			if ( !empty( $feature ) )
				$video_id = end( explode( 'v=', $parse['query'] ) );
				
			if ( strpos( $parse['path'], 'embed' ) == 1 )
				$video_id = end( explode( '/', $parse['path'] ) );
			
		}
		
		// Url is http://www.vimeo.com
		if ( ( $parse['host'] == 'vimeo.com' ) || ( $parse['host'] == 'www.vimeo.com' ) ) {
		
			$video_type = 'vimeo';
			
			$video_id = ltrim( $parse['path'],'/' );	
						
		}
		$host_names = explode(".", $parse['host'] );
		$rebuild = ( ! empty( $host_names[1] ) ? $host_names[1] : '') . '.' . ( ! empty($host_names[2] ) ? $host_names[2] : '');
		// Url is an oembed url wistia.com
		if ( ( $rebuild == 'wistia.com' ) || ( $rebuild == 'wi.st.com' ) ) {
		
			$video_type = 'wistia';
				
			if ( strpos( $parse['path'], 'medias' ) == 1 )
					$video_id = end( explode( '/', $parse['path'] ) );
		
		}
		
		// If recognised type return video array
		if ( !empty( $video_type ) ) {
		
			$video_array = array(
				'type' => $video_type,
				'id' => $video_id
			);
		
			return $video_array;
			
		} else {
		
			return false;
			
		}
		
	}
	/*** Remove Query String from Static Resources ***/
function remove_cssjs_ver( $src ) {
	if( strpos( $src, '?ver=' ) )
	$src = remove_query_arg( 'ver', $src );
	return $src;
   }
   add_filter( 'style_loader_src', 'remove_cssjs_ver', 10, 2 );
   add_filter( 'script_loader_src', 'remove_cssjs_ver', 10, 2 );