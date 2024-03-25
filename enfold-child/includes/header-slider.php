<?php 
$header_settings = avia_header_setting();
$titlr_true = true;
$bread_true = true;

if($header_settings['header_title_bar'] == 'header_title_bar') {
	$titlr_true = true;
	$bread_true = true;
}else if($header_settings['header_title_bar']=='title_bar'){
	$titlr_true = true;
	$bread_true = false;

} else if ($header_settings['header_title_bar']=='breadcrumbs_only'){
	$titlr_true = false;
	$bread_true = true;	
		
} else if($header_settings['header_title_bar']=='hidden_title_bar'){
	$titlr_true = false;
	$bread_true = false;	
}

$slider = array(
	"http://alliancebhc.staging.wpengine.com/wp-content/headers/1.jpg",
	"http://alliancebhc.staging.wpengine.com/wp-content/headers/2.jpg",
	"http://alliancebhc.staging.wpengine.com/wp-content/headers/3.jpg",
	"http://alliancebhc.staging.wpengine.com/wp-content/headers/4.jpg",
	"http://alliancebhc.staging.wpengine.com/wp-content/headers/5.jpg",
	"http://alliancebhc.staging.wpengine.com/wp-content/headers/6.jpg",
	"http://alliancebhc.staging.wpengine.com/wp-content/headers/7.jpg",
	"http://alliancebhc.staging.wpengine.com/wp-content/headers/8.jpg",
	"http://alliancebhc.staging.wpengine.com/wp-content/headers/9.jpg");

$rand_slide = array_rand($slider, 1);

if( is_category()){
	$title = get_queried_object()->name;
} elseif(is_search()){
	$title = 'Search AllianceBHC.org';
}else {
	$title = get_the_title( $post->ID );
}
?>

<div id="background-main" class="avia-section main_color avia-section-default avia-no-shadow avia-full-stretch avia-bg-style-fixed  avia-builder-el-0  el_before_av_section  avia-builder-el-first   av-minimum-height av-minimum-height-custom container_wrap fullsize" style="background-image: url(<?php echo $slider[$rand_slide];?>);" data-section-bg-repeat="stretch">
	<div class="container background_on_image" style="height:280px">
		<div class="title_wrap">
			<?php if($titlr_true): ?>
				<h1><?php echo $title; ?></h1>
			<?php endif;?>

			<?php if($bread_true): ?>
					<div class='customtitle_container title_container'>
						<div class='container breadcrumbs_after'>
				    		<?php echo  avia_breadcrumbs(array('separator' => '/', 'richsnippet' => true));?>
						</div>
					</div>
			<?php endif;?>
		</div>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		if( $('#background-main'.length > 0) ) {
			$('body').addClass('slider_background_enable');
		}
	});
</script>

