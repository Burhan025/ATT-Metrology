<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden.
}
?>
<?php get_header(); ?>

<div class="fl-archive <?php FLLayout::container_class(); ?>">
	<div class="<?php FLLayout::row_class(); ?>">

		<?php FLTheme::sidebar( 'left' ); ?>

		<div class="fl-content <?php FLLayout::content_class(); ?>"<?php FLTheme::print_schema( ' itemscope="itemscope" itemtype="https://schema.org/Blog"' ); ?>>

			<?php FLTheme::archive_page_header(); ?>

			<?php if ( have_posts() ) : ?>

				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<?php get_template_part( 'content', get_post_format() ); ?>
				<?php endwhile; ?>

				<?php FLTheme::archive_nav(); ?>

			<?php else : ?>

				<?php get_template_part( 'content', 'no-results' ); ?>

			<?php endif; ?>

		</div>

		<?php FLTheme::sidebar( 'right' ); ?>

	</div>
</div>

<?php get_footer(); ?>


<?php 
/*
get_header();
$style_dir = get_bloginfo('stylesheet_directory');
$_acf = get_field_objects(get_the_ID());

?>

<div class="main-content row">
<div class="large-10 large-offset-2 medium-11 columns medium-offset-1">

<div class="back-news"><a href="<?php echo get_permalink(149); ?>">Back to NEWS</a></div>
<?php echo '<h1>'.get_the_title().'</h1>'; ?>
<?php

	if (have_posts()) : while (have_posts()) : the_post();
		the_content(__('(more...)'));
		endwhile;
	else:
		_e('Sorry, no posts matched your criteria.');
	endif;
?>

</div>
</div><!--eo Main Content-->
</div><!--eo topRow-->

<?php get_footer(); ?>

</body>
</html>

*/ ?>


