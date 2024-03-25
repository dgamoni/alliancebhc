<?php 
global $post;
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
} ?>

		<div class="stretch_full container_wrap alternate_color light_bg_color title_container">
			<div class="container">
				<?php if($titlr_true): ?>
					<h1><?php echo get_the_title( $post->ID ); ?></h1>
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