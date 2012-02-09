<?php
/*
Plugin Name: Hyp WP Slider
Plugin URI: http://wp-slider.myhyperspace.com
Description: An admin page slider controller for jQuery Cycle and Flex Slider
Author: Adam J Nowak
Version: 1.0
Author URI: http://hyperspatial.com
*/

/*
* Usage:
*
* <?php wps_slider() //For PHP template file like index.php ?>
*
* [wps_slider] <!-- Shortcode -->
*/

//File Paths
define('WPS_PLUGIN',WP_PLUGIN_URL . '/hyp-wp-slider/');
define('WPS_PLUGIN_SERVERPATH',str_replace('hyp-wp-slider.php','',__FILE__) . '/');

//Files
require(WPS_PLUGIN_SERVERPATH . 'admin.php');

/* Slider */
function wps_slider($shortcode = false){
	if($shortcode) ob_start();
	$type = get_option('wps_slider_type');
	switch($type){
		case 'jQuery Cycle': $wps_slider = new WpsCycleSlider(); $wps_slider->get_slider();
		break;
		case 'Flex Slider': $wps_slider = new WpsFlexSlider(); $wps_slider->get_slider();
		break;
		default:
		return;
	}	
	if($shortcode){
		$main_function_output = ob_get_contents();
		ob_end_clean();
		return $main_function_output;
	}
}
/* Enqueue Styles */
function wps_enqueue_styles(){
	if(is_home() || is_front_page()){
		wp_enqueue_style('wps_style', WPS_PLUGIN . 'sliders/flexslider/flexslider.css');
	}
}
/* Enqueue Javascript */
function wps_enqueue_js(){
	if(is_home() || is_front_page()){
		$chosen_animation = get_option('wps_slider_type');
		wp_enqueue_script('jquery');
		switch($chosen_animation){
			case 'jQuery Cycle': wp_enqueue_script('wps_cycle', WPS_PLUGIN . 'sliders/cycle/jquery.cycle.all.js');
			break;
			case 'Flex Slider': wp_enqueue_script('wps_flex', WPS_PLUGIN . 'sliders/flexslider/jquery.flexslider-min.js');
			break;
			default:
			return;
		}
	}
}
/* Slider Shortcode - [wps_slider] */
function wps_slider_shortcode(){ return wps_slider(true); }

/* ~~~~~~~~~~ Actions ~~~~~~~~~~ */

add_action('wp_print_scripts','wps_enqueue_js'); 
add_action('wp_print_styles', 'wps_enqueue_styles');
add_shortcode('wps_slider','wps_slider_shortcode');

/* ~~ Cycle Class ~~ */
class WpsCycleSlider{
	private static $_element_id,$_instance_number;
	private $_limit,$_width,$_height;
	public function __construct(){
		$this->_limit = get_option('wps_slider_limit');
		$this->_width = get_option('wps_slider_width');
		$this->_height = get_option('wps_slider_height');
		self::$_element_id = 'wps-slider' . self::$_instance_number += 1;
	}	
	/* Get Slider Setting */
	private function get_option($name){
		$slider_option = get_option('wps_slider_' . $name);
		echo $slider_option;
	}		
	/* Print Script */
	private function print_script($element_id){?>
		<script type="text/javascript">
			 jQuery(document).ready(function(){
				jQuery('#<?php echo $element_id ?>').cycle({
					fx: "<?php $this->get_option('animation') ?>", 
					timeout: <?php $this->get_option('slideshowspeed') ?>, 
					speed: <?php $this->get_option('animationduration') ?>,
					start: function(){ setTimeout(wpsShowSlider,100); },
					after: function(){
						var wpsCaption = document.getElementById('<?php echo $element_id ?>-caption');
						if(this.alt){
							wpsCaption.style.visibility = 'visible';
							jQuery('#<?php echo $element_id ?>-caption').html(this.alt);
						}
						else{
							wpsCaption.style.visibility = 'hidden';
							jQuery('#<?php echo $element_id ?>-caption').html('empty');
						}
					},
					<?php echo get_option('wps_slider_extras') ?>
				});
			});
			//Hyperlink
			jQuery('#<?php echo $element_id ?> img').click(function (){
				document.location.href = jQuery(this).attr('rel');
			}); 
			function wpsShowSlider(){ document.getElementById('<?php echo $element_id ?>-container').style.visibility = 'visible'; }
		</script>
		<?php	
	}	
	/* Print Slider Images */
	private function print_slider_images($element_id){
		$i = 1;
		while($i <= $this->_limit){
			$url_option_name = 'wps_slider_' . $i;
			$link_option_name = 'wps_slider_' . $i . '_link';
			$caption_option_name = 'wps_slider_' . $i . '_caption';
			$slider_url =  get_option($url_option_name);
			$slider_link =  get_option($link_option_name);
			$slider_caption =  get_option($caption_option_name);
			if(!$slider_url) break;
			echo '<img src="' . $slider_url . '" alt="' . $slider_caption . '" rel="' . $slider_link . '" width="' . $this->_width . '" />';
			$i++;
		}
	}	
	/* Get Slider */
	public function get_slider(){ /* style="overflow:hidden; height:<?php echo $this->_height ?>px" */
		$element_id = self::$_element_id;
		?>
        <div id="<?php echo $element_id ?>-container">
            <div id="<?php echo $element_id ?>-mask" style="overflow:hidden; height:<?php echo $this->_height ?>px">
                <div id="<?php echo $element_id ?>">
                    <?php $this->print_slider_images($element_id) ?>
                </div>
            </div>
            <p id="<?php echo $element_id ?>-caption" style="visibility:hidden"></p>
        </div>
        <?php
		$this->print_script($element_id);
	}
}//END WpsCycleSlider

/* ~~ Flex Class ~~ */
class WpsFlexSlider{
	private static $_element_id,$_instance_number;
	private $_limit,$_width,$_height;
	public function __construct(){
		$this->_limit = get_option('wps_slider_limit');
		$this->_width = get_option('wps_slider_width');
		self::$_element_id = 'wps-slider' . self::$_instance_number += 1;
	}	
	/* Get Slider Setting */
	private function get_option($name){
		$slider_option = get_option('wps_slider_' . $name);
		echo $slider_option;
	}	
	/* Print Script */
	private function print_script($element_id){?>
		<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery('.flexslider').flexslider({
				animation: "<?php $this->get_option('animation') ?>",
				slideshowSpeed:  <?php $this->get_option('slideshowspeed') ?>,
				animationDuration: <?php $this->get_option('animationduration') ?>,
				controlsContainer: "#<?php echo $element_id ?>-container",   
				start: function(){ setTimeout(wpsShowSlider,100); },
				<?php echo get_option('wps_slider_extras') ?>
			});
		});
		function wpsShowSlider(){ document.getElementById('<?php echo $element_id ?>-container').style.visibility = 'visible'; }
		</script>
		<?php		
	}	
	/* Print Slider Images */
	private function print_slider_images($element_id){
		$i = 1;
		while($i <= $this->_limit){
			$url_option_name = 'wps_slider_' . $i;
			$link_option_name = 'wps_slider_' . $i . '_link';
			$caption_option_name = 'wps_slider_' . $i . '_caption';
			$slider_url =  get_option($url_option_name);
			$slider_link =  get_option($link_option_name);
			$slider_caption =  get_option($caption_option_name);
			if(!$slider_url) break;
			echo '<li>';
			if($slider_link) echo '<a href="' . $slider_link . '">';
			echo '<img src="' . $slider_url . '" alt="' . $slider_caption . '" rel="' . $slider_link . '" width="' . $this->_width . '" />';
			if($slider_link) echo '</a>';
			if($slider_caption) echo '<div class="flex-caption">' . $slider_caption . '</div>';
			echo '</li>';
			$i++;
		}
	}	
	/* Get Slider */
	public function get_slider(){
		$element_id = self::$_element_id;
		?>
        <div id="<?php echo $element_id ?>-container" style="position:relative;max-width:<?php echo $this->_width ?>px">
        	<div id="<?php echo $element_id ?>-mask">
               	<div id="<?php echo $element_id ?>" class="flexslider" style="">
                    <ul class="slides">
                        <?php $this->print_slider_images($element_id) ?>
                    </ul>
            	</div>
            </div>
            <p id="<?php echo $element_id ?>-caption" style="visibility:hidden"></p>
        </div>
        <?php
		$this->print_script($element_id);
	}
}//END WpsFlexSlider
?>