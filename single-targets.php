<?php
get_header();
$style_dir = get_bloginfo('stylesheet_directory');
?>

<div class="main-content row">
<div class="columns large-10 large-offset-2 medium-12 medium-offset-0">
<div class="back-target">
	<a href="<?php echo get_permalink(8); ?>">View All Targets</a>
	<span class="separator">|</span>
	<a href="javascript:history.go(-1)">Go Back</a>
</div>

<!--<h1 class="alert">The ATT Store is temporarily down for maintenance.<br />Please call us at 888-320-7011 to place your order.</h1>-->

<?php

$proj_args = array(
	'posts_per_page'   => -1,
	'order'            => 'ASC',
	'post_type'        => 'targets',
	'post_status'      => 'publish',
	'post_parent'      => get_the_ID(),
	'suppress_filters' => true );
$posts_array = get_posts( $proj_args );

foreach ($posts_array as $key => $value) {
	$text = "SEE  MORE";
	$class = 'has-child';

	if( hasChildren($value->ID) ){
		$text = 'BUY HERE';
		$class = '';
	}

	echo '<div class="item"><a href="'.get_permalink($value->ID).'">';
	echo '<div class="title">'.$value->post_title.'</div>';
	echo '<div class="thumb">'.get_the_post_thumbnail($value->ID).'</div>';
	echo '<div class="link-container '.$class.'"><span>'.$text.'</span></div>';
	echo '</a></div>';
}
//echo '</ul>';

if (have_posts()) : while (have_posts()) : the_post();

	the_content(__('(more...)'));
	endwhile;
else:
	_e('Sorry, no posts matched your criteria.');
endif;

?>
</div>
</div>

</div><!--eo topRow-->

<div class="clear"></div>

<?php get_footer(); ?>

</body>
</html>
