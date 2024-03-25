<?php
	global $avia_config;

	$style 		= $avia_config['box_class'];
	$responsive	= avia_get_option('responsive_active') != "disabled" ? "responsive" : "fixed_layout";
	$blank 		= isset($avia_config['template']) ? $avia_config['template'] : "";
	$av_lightbox= avia_get_option('lightbox_active') != "disabled" ? 'av-default-lightbox' : 'av-custom-lightbox';
	$preloader	= avia_get_option('preloader') == "preloader" ? 'av-preloader-active av-preloader-enabled' : 'av-preloader-disabled';


?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="<?php echo " html_{$style} ".$responsive." ".$preloader." ".$av_lightbox." ".avia_header_class_string();?> ">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />

<!-- page title, displayed in your browser bar -->
<!-- <title> --><?php //if(function_exists('avia_set_title_tag')) { echo avia_set_title_tag(); } ?><!-- </title> -->

<?php
/*
 * outputs a rel=follow or nofollow tag to circumvent google duplicate content for archives
 * located in framework/php/function-set-avia-frontend.php
 */
 if (function_exists('avia_set_follow')) { echo avia_set_follow(); }


 /*
 * outputs a favicon if defined
 */
 if (function_exists('avia_favicon'))    { echo avia_favicon(avia_get_option('favicon')); }
?>


<!-- mobile setting -->
<?php

if( strpos($responsive, 'responsive') !== false ) echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">';
?>


<!-- Scripts/CSS and wp_head hook -->
<?php
/* Always have wp_head() just before the closing </head>
 * tag of your theme, or you will break many plugins, which
 * generally use this hook to add elements to <head> such
 * as styles, scripts, and meta tags.
 */
gravity_form_enqueue_scripts( 6, true );
wp_head();

?>

</head>




<body id="top" <?php body_class($style." ".$avia_config['font_stack']." ".$blank); avia_markup_helper(array('context' => 'body')); ?>>

	<?php

	if("av-preloader-active av-preloader-enabled" === $preloader)
	{
		echo avia_preload_screen();
	}

	?>

	<div id='wrap_all'>

	<?php
	if(!$blank) //blank templates dont display header nor footer
	{
		 //fetch the template file that holds the main menu, located in includes/helper-menu-main.php
         get_template_part( 'includes/helper', 'main-menu' );

	} ?>

	<?php
	global $post;
	// vars
	$queried_object = get_queried_object();
	$taxonomy = $queried_object->taxonomy;
	$term_id = $queried_object->term_id;
	if( is_category()){
		$alliancebhc_slider_in_header = get_field('alliancebhc_slider_in_header',  $taxonomy . '_' . $term_id );
	} else {
		$alliancebhc_slider_in_header = get_field('alliancebhc_slider_in_header', $post->ID );
	}

	
	if($alliancebhc_slider_in_header):
		get_template_part( 'includes/header', 'slider' );
	elseif( is_page()):
		//get_template_part( 'includes/header', 'title' );
	elseif( is_singular('post') && is_single() ):
		get_template_part( 'includes/header', 'slider' );
	endif;
	?>

	<div id='main' data-scroll-offset='<?php echo avia_header_setting('header_scroll_offset'); ?>'>

	<?php do_action('ava_after_main_container'); ?>


	 <?php
		// global $current_user;
		// $set_home = get_user_meta( $current_user->ID, 'set_home');
		// var_dump($set_home);
	?>