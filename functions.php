<?php

// Defines
define( 'FL_CHILD_THEME_DIR', get_stylesheet_directory() );
define( 'FL_CHILD_THEME_URL', get_stylesheet_directory_uri() );

// Classes
require_once 'classes/class-fl-child-theme.php';

// Actions
add_action( 'wp_enqueue_scripts', 'FLChildTheme::enqueue_scripts', 1000 );

//* Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'parallax_enqueue_scripts_styles', 1000 );
function parallax_enqueue_scripts_styles() {
	// Styles
	wp_enqueue_style( 'custom', get_stylesheet_directory_uri() . '/style.css', array() );
	wp_enqueue_script( 'jquery', get_stylesheet_directory_uri() . '/js/vendor/jquery.js', array() );

	wp_enqueue_style( 'adobefonts', 'https://use.typekit.net/mbb6mpq.css', array() );

	wp_enqueue_style( 'custom-2', get_stylesheet_directory_uri() . '/custom.css', array() );
	wp_enqueue_script( 'custom', get_stylesheet_directory_uri() . '/js/custom.js', array() );
	
}

//include_once('acf-repeater/acf-repeater.php');
// error_reporting(0);

// define( 'WP_DEBUG', true );

/**
 * Replace all prices with "Call for Prices" on products within "PLX" section
 * https://www.attinc.com/targets/plx-ball-mounted-retroreflectors-new-repair-replacement/
 */
add_filter('woocommerce_get_price_html', function($price) {
	global $post;

	if (in_array($post->ID, [1277, 1229, 3450])) {
		return '<strong>Call for Prices</strong>';
	}

	return $price;
});

// Remove WooCommerce "Related Products" section on product detail/single pages
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

// Customize WooCommerce default "Out of stock" text
add_filter('woocommerce_get_availability', function($availability) {
	$availability['availability'] = str_ireplace('Out of stock', 'Call for availability', $availability['availability']);
	return $availability;
});


add_theme_support( 'post-thumbnails', array( 'page', 'post', 'targets', 'projects', 'product' ) );

function foundation_handler( $classes, $item ){
	//if (  is_page($item) ) {
	$classes[] = 'test';
	//}
	return $classes;
}
add_filter( 'nav_menu_css_class', 'foundation_handler', 10, 2 );

function permalink_thingy($atts) {
	extract(shortcode_atts(array(
		'id' => 1,
		'text' => ""  // default value if none supplied
    ), $atts));

    if ($text) {
        $url = get_permalink($id);
        return "<a href='$url'>$text</a>";
    } else {
	   return get_permalink($id);
	}
}
add_shortcode('permalink', 'permalink_thingy');

//========================================Remove Menus (Comments and Posts)
add_action('admin_menu', 'remove_admin_menu_items');
function remove_admin_menu_items() {
	$remove_menu_items = array(__('Comments'),__('Posts'));
	global $menu;
	end ($menu);
	while (prev($menu)){
		$item = explode(' ',$menu[key($menu)][0]);
		if(in_array($item[0] != NULL?$item[0]:"" , $remove_menu_items)){
		unset($menu[key($menu)]);}
	}
}

function hasChildren($id) {
	$args = array(
		'posts_per_page'   => -1,
		'order'            => 'ASC',
		'post_type'        => 'targets',
		'post_status'      => 'publish',
		'post_parent'	   => $id,
		'suppress_filters' => true );

	$arr = get_posts( $args );
	if(count($arr) != 0) return false;
	return true;
}

//=======================================Remove Reviews
add_filter( 'woocommerce_product_tabs', 'sb_woo_remove_reviews_tab', 98);
function sb_woo_remove_reviews_tab($tabs) {

 unset($tabs['reviews']);

 return $tabs;
}

//========================================Create Post Types
add_action( 'init', 'create_post_type' );
function create_post_type() {
	register_post_type( 'projects',
	array(
		  'labels' => array(
		    'name' => __( 'Projects' ),
		    'singular_name' => __( 'Project' )
		  ),
		'public' => true,
		'has_archive' => true,
		'menu_icon' => '',
		'taxonomies' => array('category'),
		'supports' => array(
					'title',
					'author',
					'excerpt',
					'editor',
					'thumbnail',
					'revisions',
					'page-attributes',
					'custom-fields'
			)
		)
	);
	register_post_type( 'news',
	array(
		  'labels' => array(
		    'name' => __( 'News' ),
		    'singular_name' => __( 'news' )
		  ),
		'taxonomies' => array('category'),
		'public' => true,
		'has_archive' => true,
		'menu_icon' => ''
		)
	);
	register_post_type( 'targets',
	  array(
	    'labels' => array(
	      'name' => __( 'Targets' ),
	      'singular_name' => __( 'Target' )
	    ),
	  'hierarchical' => true,
	  'public' => true,
	  'menu_icon' => '',
	  'capability_type' => 'page',
	  'supports' => array(
				'title',
				'author',
				'excerpt',
				'editor',
				'thumbnail',
				'revisions',
				'page-attributes',
				'custom-fields'
			)
		)
	);
}
//==========================CUSTOM ICONS
function add_menu_icons_styles(){
?>

<style>
#adminmenu .menu-icon-projects div.wp-menu-image:before {
  content: '\f481';
}
#adminmenu .menu-icon-news div.wp-menu-image:before {
  content: '\f123';
}
#adminmenu .menu-icon-targets div.wp-menu-image:before {
  content: '\f109';
}
</style>

<?php
}
add_action( 'admin_head', 'add_menu_icons_styles' );


//==========================Targets Shortcode [table]
function table_shortcode($atts, $content = null){
	$field_obj = get_field_objects(get_the_ID());
	$a = shortcode_atts( array(
	    'title_1' => 'Offset',
	    'title_2' => 'Shank Diameter',
	    'title_3' => 'Part Number',
	), $atts );


	$str .= '<table class="table-text" width="100%" align="center"><tbody><tr class="font-bold" align="center">';
	$str .= '<td width="40%">'.$a['title_1'].'</td>';
	$str .= '<td width="30%">'.$a['title_2'].'</td>';
	$str .= '<td width="30%">'.$a['title_3'].'</td></tr>';

	foreach ($field_obj['tables']['value'] as $val) {
		global $post;
		// Replace prices in product variation tables with "Call for Prices" for PLX products
		$price = in_array($post->ID, [1277, 1229, 3450]) ? 'Call for Prices' : do_shortcode($val['part_number']);

		$str .= '<tr>';
		$str .= '<td>'.do_shortcode($val['offset']).'</td>';
		$str .= '<td>'.do_shortcode($val['shank_diameter']).'</td>';
		$str .= '<td>'.$price.'</td>';
		$str .= '</tr>';
	}

	$str .= '</tbody></table>';
	return $str;
}
add_shortcode( 'table', 'table_shortcode' );

//==========================Targets Shortcode [table]
function table_shortcode2($atts, $content = null){
	$field_obj = get_field_objects(get_the_ID());
	$a = shortcode_atts( array(
	    'title_1' => 'Description',
	    'title_2' => 'Part Number & Diameter'
	), $atts );

	$str .= '<table class="table-text" width="100%" align="center"><tbody><tr class="font-bold" align="center">';
	$str .= '<td width="40%">'.$a['title_1'].'</td>';
	$str .= '<td width="60%">'.$a['title_2'].'</td></tr>';

	foreach ($field_obj['tables-2']['value'] as $val) {
		$str .= '<tr>';
		$str .= '<td>'.do_shortcode($val['description']).'</td>';
		$str .= '<td>'.do_shortcode($val['part_number']).'</td>';
		$str .= '</tr>';
	}

	$str .= '</tbody></table>';
	return $str;
}
add_shortcode( 'table-2', 'table_shortcode2' );

//==========================Targets Shortcode [table]
function table_shortcode3($atts, $content = null){
	$field_obj = get_field_objects(get_the_ID());
	$a = shortcode_atts( array(
	    'title_1' => 'Description',
	    'title_2' => 'Part Number',
		'title_3' => 'Ball Diameter',
		'title_4' => 'Clear Aperture',
		'title_5' => 'Centering Accuracy',
		'title_6' => 'Price'
	), $atts );

	$str .= '<table class="table-text" width="100%" align="center"><tbody><tr class="font-bold" align="center">';
	$str .= '<td width="18%">'.$a['title_1'].'</td>';
	$str .= '<td width="16%">'.$a['title_2'].'</td>';
	$str .= '<td width="16%">'.$a['title_3'].'</td>';
	$str .= '<td width="16%">'.$a['title_4'].'</td>';
	$str .= '<td width="16%">'.$a['title_5'].'</td>';
	$str .= '<td>'.$a['title_6'].'</td></tr>';

	foreach ($field_obj['tables3']['value'] as $val) {
		global $post;
		// Replace prices in product variation tables with "Call for Prices" for PLX products
		$price = in_array($post->ID, [1277, 1229, 3450]) ? 'Call for Prices' : do_shortcode($val['price']);

		$str .= '<tr>';
		$str .= '<td>'.do_shortcode($val['description']).'</td>';
		$str .= '<td>'.do_shortcode($val['part_number']).'</td>';
		$str .= '<td>'.do_shortcode($val['ball_diameter']).'</td>';
		$str .= '<td>'.do_shortcode($val['clear_aperture']).'</td>';
		$str .= '<td>'.do_shortcode($val['centering_accuracy']).'</td>';
		$str .= '<td>'.$price.'</td>';
		$str .= '</tr>';
	}

	$str .= '</tbody></table>';
	return $str;
}
add_shortcode( 'table3', 'table_shortcode3' );

//==========================Targets Shortcode [table]
function table_shortcode4($atts, $content = null){
	$field_obj = get_field_objects(get_the_ID());
	$a = shortcode_atts( array(
	    'title_1' => 'Description',
	    'title_2' => 'Part Number',
		'title_3' => 'Centering Accuracy',
		'title_4' => 'Price'
	), $atts );

	$str .= '<table class="table-text" width="100%" align="center"><tbody><tr class="font-bold" align="center">';
	$str .= '<td width="30%">'.$a['title_1'].'</td>';
	$str .= '<td width="23%">'.$a['title_2'].'</td>';
	$str .= '<td width="23%">'.$a['title_3'].'</td>';
	$str .= '<td>'.$a['title_4'].'</td></tr>';

	foreach ($field_obj['tables4']['value'] as $val) {
		global $post;
		// Replace prices in product variation tables with "Call for Prices" for PLX products
		$price = in_array($post->ID, [1277, 1229, 3450]) ? 'Call for Prices' : do_shortcode($val['price']);

		$str .= '<tr>';
		$str .= '<td>'.do_shortcode($val['description']).'</td>';
		$str .= '<td>'.do_shortcode($val['part_number']).'</td>';
		$str .= '<td>'.do_shortcode($val['centering_accuracy']).'</td>';
		$str .= '<td>'.$price.'</td>';
		$str .= '</tr>';
	}

	$str .= '</tbody></table>';
	return $str;
}
add_shortcode( 'table4', 'table_shortcode4' );

//==========================Services-CTA [services-cta]
function services_cta_shortcode($atts, $content = null){
	$a = shortcode_atts( array(
	    'cat' => 'projects-inspection-services',
	), $atts );

	$proj_args = array(
		'posts_per_page'   => 4,
		'order'            => 'DESC',
		'post_type'        => 'projects',
		'category_name'	   => $a['cat'],
		'post_status'      => 'publish',
		'suppress_filters' => true );
	$posts_array = get_posts( $proj_args );

	$category = get_category_by_slug($a['cat']);
	$category_id = $category ? $category->term_id : 84;

	$ret = '<ul class="project-list">';

	foreach ($posts_array as $key => $value) {
		$catArr = get_the_category($value->ID);
		$tempCat = array();
		foreach($catArr as $cat)
			array_push($tempCat, $cat->slug);

		$ret .= '<li class="'.implode( ' ', $tempCat).'"><a href="'.$value->guid.'"><div class="overlay">'.$value->post_title.'</div>'.get_the_post_thumbnail($value->ID).'</a></li>';
	}
	$ret .= '</ul><a class="proj-link" href="'.get_category_link($category_id).'">SEE ALL PROJECTS</a>';
	$ret .= '<div class="quote-container"><a href="'.get_permalink(88).'" class="quote"><span>GET A QUOTE TODAY!</span></a></div>';

	return $ret;
}
add_shortcode( 'services', 'services_cta_shortcode' );

//==========================Equipment Link - CTA [equipment]
function equipment_cta_shortcode($atts, $content = null){
	$a = shortcode_atts( array(
		'page_id' => 1,
	), $atts );

	$ret = '<a class="proj-link" href="'.get_permalink($a['page_id']).'">SEE ALL EQUIPMENT</a>';
	$ret .= '<div class="quote-container"><a href="'.get_permalink(88).'" class="quote"><span>GET A QUOTE TODAY!</span></a></div>';

	return $ret;
}
add_shortcode( 'equipment', 'equipment_cta_shortcode' );

//==========================Quote CTA [quote]
function quote_cta_shortcode($atts, $content = null){
	return '<div class="quote-container"><a href="'.get_permalink(88).'" class="quote"><span>GET A QUOTE TODAY!</span></a></div>';
}
add_shortcode( 'quote', 'quote_cta_shortcode' );

// Make theme available for translation
// Translations can be filed in the /languages/ directory
load_theme_textdomain( 'your-theme', TEMPLATEPATH . '/languages' );

$locale = get_locale();
$locale_file = TEMPLATEPATH . "/languages/$locale.php";
if ( is_readable($locale_file) )
    require_once($locale_file);

// Get the page number
function get_page_number() {
    if ( get_query_var('paged') ) {
        print ' | ' . __( 'Page ' , 'your-theme') . get_query_var('paged');
    }
} // end get_page_number

function get_parent_id($post) {
	if ($post->post_parent)  {
	    $parentArray=get_post_ancestors($post->ID);
	    $pageParent = $parentArray[0];
	} else {
	    $pageParent = null;
	}
	return $pageParent;
}



function ellipsis($text, $max=75, $append='&hellip;')
{
    if (strlen($text) <= $max) return $text;
    $out = substr($text,0,$max);
    if (strpos($text,' ') === FALSE) return $out.$append;
    return preg_replace('/\w+$/','',$out).$append;
}

function enqueueScriptsFrontEnd(){
	//wp_register_script( 'modernizr', get_stylesheet_directory_uri().'/js/vendor/modernizr.js', false, null, false );

	if(preg_match('/(?i)msie [1-8]/',$_SERVER['HTTP_USER_AGENT'])){
		wp_deregister_script('jquery');
		wp_register_script('jquery', ("//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"), false, null, false);
		$scriptArr = array( 'modernizr', 'jquery' );
		wp_enqueue_script( $scriptArr );

		//wp_register_style( 'style', get_stylesheet_directory_uri().'/style.css?date=20210614.3', array(), null, 'all' );
    //wp_register_style( 'style-nav', get_stylesheet_directory_uri().'/css/style-nav.css', array( 'style' ), null, 'all' );
		//wp_register_style( 'explorer', get_stylesheet_directory_uri().'/ie.css', array( 'style' ), null, 'all' );
		//$styleArr = array( 'style', 'style-nav', 'explorer' );
		//wp_enqueue_style( $styleArr );
	}else{
		 //wp_deregister_script('jquery');
		 //wp_register_script('jquery', (get_stylesheet_directory_uri()."/js/vendor/jquery.js"), false, null, false);

		 //wp_register_script( 'foundation', get_stylesheet_directory_uri().'/js/foundation.min.js', array('jquery'), null, true );
		 //wp_register_script( 'foundation-equalizer', get_stylesheet_directory_uri().'/js/foundation/foundation.equalizer.js', array('jquery', 'foundation'), null, true );

		 //wp_register_script( 'script', get_stylesheet_directory_uri().'/js/script.js?date=20210614.3', array('jquery', 'foundation', 'foundation-equalizer'), null, true );
		 //$scriptArr = array( 'modernizr', 'jquery', 'foundation', 'foundation-equalizer', 'script' );
		 //wp_enqueue_script( $scriptArr );

		wp_register_style( 'foundation', get_stylesheet_directory_uri().'/css/foundation.css', false, false, 'all' );
		//wp_register_style( 'style', get_stylesheet_directory_uri().'/style.css?date=20210610.1', false, false, 'all' );
    //wp_register_style( 'style-nav', get_stylesheet_directory_uri().'/css/style-nav.css', array( 'style' ), null, 'all' );
		//$styleArr = array( 'foundation', 'style' );
		//wp_enqueue_style( $styleArr );
	}
}
add_action( 'wp_enqueue_scripts', 'enqueueScriptsFrontEnd' );

add_filter( 'post_password_expires', 'wpse_custom_post_password_expires' );
function wpse_custom_post_password_expires( $expires ) {
    return time() + (60 * 60); // Expire in 1 hours
}

/* Disable Woocommerce script and style */

add_action( 'wp_enqueue_scripts', 'dequeue_woocommerce_styles_scripts', 99 );
function dequeue_woocommerce_styles_scripts() {
if ( function_exists( 'is_woocommerce' ) ) {
if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
# Styles
wp_dequeue_style( 'woocommerce-general' );
wp_dequeue_style( 'woocommerce-layout' );
wp_dequeue_style( 'woocommerce-smallscreen' );
wp_dequeue_style( 'woocommerce_frontend_styles' );
wp_dequeue_style( 'woocommerce_fancybox_styles' );
wp_dequeue_style( 'woocommerce_chosen_styles' );
wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
# Scripts
wp_dequeue_script( 'wc_price_slider' );
wp_dequeue_script( 'wc-single-product' );
wp_dequeue_script( 'wc-add-to-cart' );
wp_dequeue_script( 'wc-cart-fragments' );
wp_dequeue_script( 'wc-checkout' );
wp_dequeue_script( 'wc-add-to-cart-variation' );
wp_dequeue_script( 'wc-single-product' );
wp_dequeue_script( 'wc-cart' );
wp_dequeue_script( 'wc-chosen' );
wp_dequeue_script( 'woocommerce' );
wp_dequeue_script( 'prettyPhoto' );
wp_dequeue_script( 'prettyPhoto-init' );
wp_dequeue_script( 'jquery-blockui' );
wp_dequeue_script( 'jquery-placeholder' );
wp_dequeue_script( 'fancybox' );
wp_dequeue_script( 'jqueryui' );
}
}
}
/** Disable WooCommerce  Block Styles */
function disable_woocommerce_block_styles() {
  wp_dequeue_style( 'wc-blocks-style' );
}
add_action( 'wp_enqueue_scripts', 'disable_woocommerce_block_styles' );

//get top cat
function get_top_cat() {

	ob_start();
	$term_id = get_queried_object()->term_id; 
	$ancestors = get_ancestors( $term_id, 'product_cat' ); 
	$ancestors = array_reverse($ancestors); 
	$ancestors[0] ? $top_term_id = $ancestors[0] : $top_term_id = $term_id; 
	$term = get_term( $top_term_id, 'product_cat' ); 
	//echo $term->name; 
	?>
	<?php /*<div class="main-cat-content">
		<h3 class="cat-title"><?php echo $term->name; ?></h3>
		<div class="cat-content">
			<?php echo $term->description; ?>
		</div>
	</div>
	*/ ?>

	<input class="input-cat-title" type="hidden" value="<?php echo $term->name; ?>" slug="<?php echo $term->slug; ?>" />

	<?php
	$content = ob_get_clean();
	return $content;
}

function top_cat(){
   add_shortcode('top-cat', 'get_top_cat');
}

add_action( 'init', 'top_cat');

//get prodcut single cat
function get_single_cat() {

	ob_start();
	global $product;

	$terms = get_the_terms( $product->get_id(), 'product_cat' );

	foreach ($terms  as $term  ) {
		$product_cat_link = $term->slug;
		$product_cat_id = $term->term_id;
		$product_cat_name = $term->name;
		break;
	}

	?>
	<div class="goback-product-title">
		<a href="<?php echo get_category_link($product_cat_id); ?>"><span class="cat-title">
		<i class="pp-button-icon pp-button-icon-before fas fa-chevron-left"></i><?php echo $product_cat_name; ?></span></a>
	</div>

	<?php
	$content = ob_get_clean();
	return $content;
}

function single_cat(){
   add_shortcode('get-cat', 'get_single_cat');
}

add_action( 'init', 'single_cat');

//get prodcut single cat 2
function get_current_cat() {

	ob_start();

	$term = get_queried_object();
	$term_parent = $term->category_parent;
	$term_id = get_queried_object()->term_id; 
	$ancestors = get_ancestors( $term_id, 'product_cat' ); 
	$ancestors = array_reverse($ancestors);
	if($ancestors[0]){
		$has_parent = 'has-parent';
	}else{
		$has_parent = 'no-parent';
	}

	$children = get_terms( $term->taxonomy, array(
	'parent'    => $term->term_id,
	'hide_empty' => false
	) );
	//echo $term_parent;
	if($children) { // get_terms will return false if tax does not exist or term wasn't found.
		// term has children

		echo 'has-child';
	}else{
		echo 'no-child '.$has_parent;
	}

	$content = ob_get_clean();
	return $content;
}

function current_cat(){
   add_shortcode('current-cat', 'get_current_cat');
}

add_action( 'init', 'current_cat');

//get parent and current cat
/*function get_current_parent_cat() {

	ob_start();
	$term_id = get_queried_object()->term_id; 
	$ancestors = get_ancestors( $term_id, 'product_cat' ); 
	$ancestors = array_reverse($ancestors); 
	$ancestors[0] ? $top_term_id = $ancestors[0] : $top_term_id = $term_id; 
	$term = get_term( $top_term_id, 'product_cat' ); 

	$current_cat = get_queried_object();

	echo $current_cat->name;
	//echo $term->name; 
	?>


	<?php echo $term->name; ?>

	<?php
	$content = ob_get_clean();
	return $content;
}

function current_parent_cat(){
   add_shortcode('current-parent-cat', 'get_current_parent_cat');
}

add_action( 'init', 'current_parent_cat');
*/

//shop read more button


add_filter( 'gettext', 'ds_change_readmore_text', 20, 3 );

function ds_change_readmore_text( $translated_text, $text, $domain ) {
if ( ! is_admin() && $domain === 'woocommerce' && $translated_text === 'Read more') {
$translated_text = 'View Details';
}
return $translated_text;
}

function my_text_change() {
	return __( 'View Details', 'woocommerce' ); 
  }
  add_filter( 'woocommerce_product_add_to_cart_text', 'my_text_change' );

/**
 * Change number of products that are displayed per page (shop page)
 */
add_filter( 'loop_shop_per_page', 'new_loop_shop_per_page', 20 );

function new_loop_shop_per_page( $cols ) {
  // $cols contains the current number of products per page based on the value stored on Options –> Reading
  // Return the number of products you wanna show per page.
  $cols = -1;
  return $cols;
}
