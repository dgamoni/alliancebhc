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


$sluuug ='';

if( is_category()){
	$title = get_queried_object()->name;
} elseif(is_single() && get_the_category()[0]->name =="Provider News"){
	$title = get_the_category()[0]->name;
	$sluuug = get_the_category()[0]->slug;
} elseif(is_single() && get_the_category()[0]->name =="Alliance News"){
	$title = get_the_category()[0]->name;
	$sluuug = get_the_category()[0]->slug;
} elseif(is_search()){
	$title = 'Search AllianceBHC.org';
	$sluuug ='';
}else {
	$title = get_the_title( $post->ID );
	$sluuug ='';
}
?>

<div id="background-main" class="avia-section main_color avia-section-default avia-no-shadow avia-full-stretch avia-bg-style-fixed  avia-builder-el-0  el_before_av_section  avia-builder-el-first   av-minimum-height av-minimum-height-custom container_wrap fullsize">
	<div class="container background_on_image" style="height:100px">
		<div class="title_wrap">
			<?php if($titlr_true): ?>
				<?php //var_dump(get_the_category()[0]->name); ?>
				<h1><?php echo $title; ?></h1>
			<?php endif;?>

			<?php if($bread_true): ?>
					<div class='customtitle_container title_container <?php echo $sluuug; ?>'>
						<div class='container breadcrumbs_after'>
				    		<?php echo  avia_breadcrumbs(array('separator' => '/', 'richsnippet' => true));?>
						</div>
					</div>
			<?php endif;?>
		</div>
	</div>
</div>


