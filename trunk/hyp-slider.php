<?php
/*
Plugin Name: WP Slider
Plugin URI: http://hyperspatial.com
Description: An admin page slider controller or jQuery Cycle
Author: Adam J Nowak
Version: 1.0
Author URI: http://hyperspatial.com
*/

//Init
add_action('get_header','wpsl_init');

//Options
define('WPSL_LIMIT',5);
define('WPSL_WIDTH',636);
define('WPSL_HEIGHT',256);

//Plugin Url
define('WPSL_PLUGIN',WP_PLUGIN_URL . '/hyp-slider/');

$wpsl_field_names = array(
	'wpsl_slider_animation',
	'wpsl_slider_slideshowspeed',
	'wpsl_slider_animationduration',
);

$wpsl_animations = array(
	'blindX',
	'blindY',
	'blindZ',
    'cover',
	'curtainX',
	'curtainY',
	'fade',
	'fadeZoom',
	'growX',
	'growY',
	'none',
	'scrollUp',
	'scrollDown',
	'scrollLeft',
	'scrollRight',
	'scrollHorz',
	'scrollVert',
	'shuffle',
	'slideX',
	'slideY',
	'toss',
	'turnUp',
	'turnDown',
	'turnLeft',
	'turnRight',
	'uncover',
	'wipe',
	'zoom'
);

/* ~~~~~~~~~~~ Admin Page ~~~~~~~~~~~ */

/* Admin Menu */
function wpsl_create_admin_menu(){
	add_menu_page('WP Slider','WP&nbsp;Slider',3,'wpsl-settings','wpsl_settings_page',WPSL_PLUGIN . 'icon.png');
	add_action('admin_init','wpsl_register_options');
}
add_action('admin_menu','wpsl_create_admin_menu');
/* Register wpsl Options */
function wpsl_register_options(){
	global $wpsl_field_names;
	$slider_option_array = wpsl_get_slider_dynamic_options();
	$wpsl_field_names = array_merge($wpsl_field_names,$slider_option_array);
	foreach($wpsl_field_names as $field_name){ register_setting('wpsl_options',$field_name); }
}
/* Settings Page */
function wpsl_settings_page(){
	global $wpsl_field_names,$wpsl_animations;
	$slider_option_array = wpsl_get_slider_dynamic_options();
	$wpsl_field_names = array_merge($wpsl_field_names,$slider_option_array);
	foreach($wpsl_field_names as $field_name){ ${$field_name} = get_option($field_name); }
	?>
	<style type="text/css">
	.shadow{box-shadow:1px 1px 4px #333; -webkit-box-shadow:1px 1px 4px #333}
	input[type="text"],textarea{color:#222; background-color:#f4f4f4}
	.slide-card input[type="text"]{width:380px}
	.help-text{font-size:11px; font-style:italic;}
	.top-help{display:block; margin-top:-12px}
	.example{color:#669;}
	form {margin-top:20px;}
	h3{margin-top:40px;border-bottom:1px solid #8E7556; width:620px; color:#8E7556; padding-bottom:5px; font-size:21px}
	h4{font-size:16px; margin:0; padding:4px; background:#C9BBA8}
	label{font-weight:bold;}
	input.button-primary{margin-top:20px;}
	.html-textarea{width:620px; height:140px; }
	form p{margin-bottom:12px;}
	.anchor{padding-top:20px}
	#theme-dropdown{font-size:1.4em; height:1.6em; min-width:140px}
	form .remaining{background-color:#FFF0D3; width:3em}
	div.left{float:left; margin:0 32px 20px 0}
	.slide-card{width:598px; padding:1em; margin-bottom:1.5em; background:#E8E0D5}
	.slide-card img{float:right; margin-top:64px}
	.wrap .save-btn{float:right; border:none; background:none; color:#21759B; font-size:14px; font-weight:normal; cursor:pointer; margin:0; padding:0}
	.wrap .save-btn:hover{color:#D54E21}
	.wrap #anim-dropdown{height:24px; font-size:13px}
	</style>
	<div class="wrap">
		<h2>WP Slider Settings</h2>
		<form name="settings" method="post" action="options.php">
			<?php settings_fields('wpsl_options') ?>
            <h3>Slider<input type="submit" class="save-btn" value="<?php _e('save') ?>" /></h3>
            <p><em>Limit: <?php echo WPSL_LIMIT ?> Slides - Image Size: <?php echo WPSL_WIDTH ?> X <?php echo WPSL_HEIGHT ?></em></p>
            <div class="left">
            	<label>Animation Type</label><br />
            	<select id="anim-dropdown" name="wpsl_slider_animation">
					<?php foreach($wpsl_animations as $animation): ?>
                    <option <?php if ($animation == $wpsl_slider_animation) echo 'selected="selected"' ?>><?php echo $animation ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="left">
            	<label>Slideshow Speed</label><br />
            	<input name="wpsl_slider_slideshowspeed" type="text" value="<?php echo $wpsl_slider_slideshowspeed ?>" size="8"/> ms<br />
                <span class="help-text">Default: 4000</span>
            </div>
            <div class="left">
            	<label>Animation Speed</label><br />
            	<input name="wpsl_slider_animationduration" type="text" value="<?php echo $wpsl_slider_animationduration ?>" size="8"/> ms<br />
                <span class="help-text">Default: 1000</span>
            </div>
            <div style="clear:left">
            	<?php wpsl_get_slider_cards($slider_option_array) ?>
            </div>
            
            <p>
                <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
    	</form>
    </div><!--/.wrap-->
    <?php
}

/* ~~~~~~~~~~~ Functions ~~~~~~~~~~~ */

/* Get Slider Cards */
function wpsl_get_slider_cards($slider_option_array){
	$i = 1;
	foreach($slider_option_array as $option){
		$value = get_option($option);
		if(preg_match('/link/',$option)) echo '<label>Hyperlink Url</label><br /><input name="wpsl_slider_' . $i . '_link" type="text" value="' . $value . '" /><br />';
		else if(preg_match('/caption/',$option)){
			echo '<label>Caption</label><br /><input name="wpsl_slider_' . $i . '_caption" type="text" value="' . $value . '" /></div>';
			$i++;
		}
		else{
			if($value != '') $imgtag = '<img src="' . $value . '" width="195"/>';
			else $imgtag = '';
			echo '<div class="slide-card shadow">' . $imgtag . '<h4>Slide ' . $i . ':</h4><br><label>Image Url</label><br /><input name="wpsl_slider_' . $i . '" type="text" value="' . $value . '" /><br />';
		}
	}	
}

/* Get Slider Dynamic Options */
function wpsl_get_slider_dynamic_options(){
	$slider_option_array = array();
	$i = 1;
	while($i <= WPSL_LIMIT){
		$option_name = 'wpsl_slider_' . $i;
		$slider_option = get_option($option_name);
		$slider_option_array[] = $option_name;
		$slider_option_array[] = $option_name . '_link';
		$slider_option_array[] = $option_name . '_caption';
		if(!$slider_option) return $slider_option_array;
		$i++;
	}
	return $slider_option_array;
}

/* Get Slider Dynamic Urls */
function wpsl_get_slider(){
	$i = 1;
	while($i <= WPSL_LIMIT){
		$url_option_name = 'wpsl_slider_' . $i;
		$link_option_name = 'wpsl_slider_' . $i . '_link';
		$caption_option_name = 'wpsl_slider_' . $i . '_caption';
		$slider_url =  get_option($url_option_name);
		$slider_link =  get_option($link_option_name);
		$slider_caption =  get_option($caption_option_name);
		if(!$slider_url) return;
		echo '<img src="' . $slider_url . '" alt="' . $slider_caption . '" rel="' . $slider_link . '" width="' . WPSL_WIDTH . '" height="' . WPSL_HEIGHT . '" />';
		$i++;
	}
}

/* Get Slider Setting */
function wpsl_get_slider_setting($name){
	global $wpsl_animations;
	$slider_setting = get_option('wpsl_slider_' . $name);
	switch($name){
		case 'animation': 
		if(in_array($slider_setting,$wpsl_animations)) echo $slider_setting;
		else echo 'slide';
		break;
		case 'slideshowspeed': 
		if($slider_setting) echo $slider_setting;
		else echo '4000';
		break;
		case 'animationduration': 
		if($slider_setting) echo $slider_setting;
		else echo '1000';
		break;
		default:
		return;
	}
	return $slider_setting;
}

/* Init */
function wpsl_init(){ if(is_home()) add_action('wp_head','wpsl_print_script'); }

/* Print Script */
function wpsl_print_script(){?>
	<script type="text/javascript">
    jQuery(document).ready(function() {
		 jQuery(document).ready(function(){
			jQuery('#home-slider').cycle({
				fx:        "<?php wpsl_get_slider_setting('animation') ?>", 
				timeout:    <?php wpsl_get_slider_setting('slideshowspeed') ?>, 
				speed:		<?php wpsl_get_slider_setting('animationduration') ?>,
				after: function(){
					var wpslCaption = document.getElementById('wpsl-caption');
					if(this.alt){
						wpslCaption.style.visibility = 'visible';
						jQuery('#wpsl-caption').html(this.alt);
					}
					else{
						wpslCaption.style.visibility = 'hidden';
						jQuery('#wpsl-caption').html('empty');
					}
				}
			});
		});
		//Hyperlink
		jQuery('#home-slider img').click(function (){
			document.location.href = jQuery(this).attr('rel');
		}); 
	});
	</script>
	<?php	
}
?>