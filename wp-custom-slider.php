<?php
/**
 * @package wp_custom_slider
 * @version 0.2
 */
/*
Plugin Name: WP Custom Slider
Plugin URI: http://torounit.com
Description: WP Custom Slider
Author: Toro_Unit
Version: 0.2
Author URI: http://torounit.com
*/



class wp_custom_slider {

	public $post_type = 'wp_custom_slider';

	public $labels = array (
			'name' => 'Slide',
			'singular_name' => 'Slide',
			'add_new' => 'Add New Slide',
			'add_new_item' => 'Add New Slide',
			'edit_item' => 'Edit Slide',
			'new_item' => 'New Slide',
			'all_items' => 'All Slides',
			'view_item' => 'View Slide',
			'search_items' => 'Search Slides',
			'not_found' =>  'No Slides found',
			'not_found_in_trash' => 'No Slides found in Trash',
			'parent_item_colon' => '',
			'menu_name' => 'Slides'
		);

	public function add_hooks() {

		$this->post_type = apply_filters("wp_custom_slider_post_type", $this->post_type);
		$this->labels = apply_filters("wp_custom_slider_labels", $this->labels);
		add_action('wp_loaded', array( $this, 'register_custom_post_type' ));
		add_action("wp_loaded", array( $this, "enqueue" ));
		add_action('after_setup_theme', array( $this, "set_image_size" ) , 100);
	}


	public function register_custom_post_type() {

		$args = array(
			'labels' => $this->labels,
			'public' => false,
			'show_ui' => true,
			'show_in_menu' => true,
			'query_var' => false,
			'capability_type' => 'post',
			'has_archive' => false,
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array( 'title', 'editor', 'thumbnail' )
		);
		register_post_type($this->post_type ,$args);
	}


	public function set_image_size() {
		global $content_width;

		if($content_width) {
			$width = $content_width;
		}else {
			$width = 960;
		}
		$size = apply_filters("wp_custom_slider_size",array( $width, 350 ));
		add_image_size("slide", $size[0], $size[1], true);
	}

	public function enqueue() {
		wp_enqueue_style('nivo-slider', plugins_url('wp-custom-slider/nivo-slider/nivo-slider.css'), false, '1.0', 'all');
		wp_enqueue_style('nivo-slider-default', plugins_url('wp-custom-slider/nivo-slider/themes/default/default.css'), false, '1.0', 'all');
		wp_enqueue_script('nivo-slider', plugins_url('wp-custom-slider/nivo-slider/jquery.nivo.slider.pack.js'), array('jquery'), '1.0', false);
		wp_enqueue_script('script', plugins_url('wp-custom-slider/script.js'), array('jquery','nivo-slider'), '1.0', false);
	}

	public function slider($post_type = "") {
		if(!$post_type) {
			$post_type = $this->post_type;
		}
		$slides = get_posts('post_type='.$post_type.'&posts_per_page=-1');

		$captions = array();
		?>

		<div class="slider-wrapper theme-default">
		<div id="wp_custom_slider">
		<?php
		foreach ($slides as $slide) {
			$img = wp_get_attachment_image_src(get_post_thumbnail_id($slide->ID), "slide");
			if($slide->post_content) {
				$captions["slide".$slide->ID] = $slide->post_content;
				$title = 'title="#slide'.$slide->ID.'"';
			}else{
				$title = "";
			}?>

			<img src="<?php echo $img[0];?>" alt="" width="<?php echo $img[1];?>" height="<?php echo $img[2];?>" <?php echo $title;?> />
			<?php
		}
		?>

		</div>

		<?php
		foreach ($captions as $key => $caption) {
			?>

			<div id="<?php echo $key;?>" class="nivo-html-caption">
			<?php echo $caption = apply_filters('the_content', $caption); ?>
			</div>
			<?php
		}
		?>

		</div>

		<?php
	}
}


function instance_wp_custom_slider() {
	global $wp_custom_slider;
	$wp_custom_slider = new wp_custom_slider();
	$wp_custom_slider->add_hooks();
}
add_action("after_setup_theme", "instance_wp_custom_slider");


function wp_custom_slider($post_type ="") {
	global $wp_custom_slider;
	$wp_custom_slider->slider();
}

