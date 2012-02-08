<?php /* Admin Page */

$wps_field_names = array('wps_slider_type','wps_slider_limit','wps_slider_width','wps_slider_height','wps_slider_animation','wps_slider_slideshowspeed','wps_slider_animationduration','wps_slider_extras');
$wps_sliders = array('jQuery Cycle','Flex Slider');
/* Admin Menu */
function wps_create_admin_menu(){
	add_menu_page('WP Slider','WP&nbsp;Slider',3,'wps-settings','wps_settings_page',WPS_PLUGIN . 'icon.png');
	add_action('admin_init','wps_register_options');
}
add_action('admin_menu','wps_create_admin_menu');
/* Register wps Options */
function wps_register_options(){
	global $wps_field_names;
	$slider_option_array = wps_get_slider_dynamic_options();
	$wps_field_names = array_merge($wps_field_names,$slider_option_array);
	foreach($wps_field_names as $field_name){ register_setting('wps_options',$field_name); }
}
/* Settings Page */
function wps_settings_page(){
	wps_set_option_defaults();
	global $wps_field_names,$wps_sliders,$wps_animations;
	$slider_option_array = wps_get_slider_dynamic_options();
	$wps_field_names = array_merge($wps_field_names,$slider_option_array);
	foreach($wps_field_names as $field_name){ ${$field_name} = get_option($field_name); }
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
	.slide-card{width:598px; padding:1em; margin-bottom:1.5em; background:#E8E0D5; overflow:hidden}
	.slide-card img{float:right; margin-top:37px}
	.wrap .save-btn{float:right; border:none; background:none; color:#21759B; font-size:14px; font-weight:normal; cursor:pointer; margin:0; padding:0}
	.wrap .save-btn:hover{color:#D54E21}
	.wrap #anim-dropdown{height:24px; font-size:13px}
	textarea{min-width:392px; min-height:144px;}
	#slider-dropdown,#anim-dropdown{min-width:100px}
	</style>
	<div class="wrap">
		<h2>WP Slider Settings</h2>
		<form name="settings" method="post" action="options.php">
			<?php settings_fields('wps_options') ?>
            <h3>Basic Slider Options<input type="submit" class="save-btn" value="<?php _e('save') ?>" /></h3>
            <div class="left">
            	<label>Slider</label><br />
            	<select id="slider-dropdown" name="wps_slider_type" onChange="this.form.submit();">
					<?php foreach($wps_sliders as $slider): ?>
                    <option <?php if ($slider == $wps_slider_type) echo 'selected="selected"' ?>><?php echo $slider ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="left">
            	<label>Animation Type</label><br />
            	<select id="anim-dropdown" name="wps_slider_animation">
                	<?php $wps_animations = wps_get_animations() ?>
					<?php foreach($wps_animations as $animation): ?>
                    <option <?php if ($animation == $wps_slider_animation) echo 'selected="selected"' ?>><?php echo $animation ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="clear left">
            	<label>Slide Limit</label><br />
            	<input name="wps_slider_limit" type="text" value="<?php echo $wps_slider_limit ?>" size="6"/><br />
            </div>
            <div class="left">
            	<label>Max Width</label><br />
            	<input name="wps_slider_width" type="text" value="<?php echo $wps_slider_width ?>" size="6"/> px<br />
            </div>
            <div class="left">
            	<label>Slideshow Speed</label><br />
            	<input name="wps_slider_slideshowspeed" type="text" value="<?php echo $wps_slider_slideshowspeed ?>" size="8"/> ms<br />
                <span class="help-text">Default: 4000</span>
            </div>
            <div class="left">
            	<label>Animation Speed</label><br />
            	<input name="wps_slider_animationduration" type="text" value="<?php echo $wps_slider_animationduration ?>" size="8"/> ms<br />
                <span class="help-text">Default: 1000</span>
            </div>
			<?php if($wps_slider_type == 'Flex Slider') $show_mask = 'style="visibility:hidden"' ?>
            <div class="left" <?php echo $show_mask ?>>
            	<label>Mask Height</label><br />
            	<input name="wps_slider_height" type="text" value="<?php echo $wps_slider_height ?>" size="6"/> px<br />
            </div>
            <h3 class="clear">Custom Slider Options<input type="submit" class="save-btn" value="<?php _e('save') ?>" /></h3>
            <p>
                <textarea name="wps_slider_extras"><?php echo get_option('wps_slider_extras') ?></textarea><br />
                <span class="help-text">Enter a comma separated list of custom slider options. Ex = pause: 1, random: 1</span>
            </p>
            <h3>Images<input type="submit" class="save-btn" value="<?php _e('save') ?>" /></h3>
            <div class="clear">
            	<?php wps_get_slider_cards($slider_option_array) ?>
            </div>         
            <p>
                <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
    	</form>
    </div><!--/.wrap-->
    <?php
}
/* ~~~~~~~~~~~ Functions ~~~~~~~~~~~ */

/* Get Animations */
function wps_get_animations(){
	$chosen_animation = get_option('wps_slider_type');
	switch($chosen_animation){
		case 'jQuery Cycle':
		$wps_animations = array('blindX','blindY','blindZ','cover','curtainX','curtainY','fade','fadeZoom','growX','growY','none','scrollUp','scrollDown','scrollLeft','scrollRight','scrollHorz','scrollVert','shuffle','slideX','slideY','toss','turnUp','turnDown','turnLeft','turnRight','uncover','wipe','zoom');
		break;
		case 'Flex Slider':
		$wps_animations = array('fade','slide');
		break;
		default:
		return;
	}
	return $wps_animations;
}
/* Get Slider Cards */
function wps_get_slider_cards($slider_option_array){
	$i = 1;
	foreach($slider_option_array as $option){
		$value = get_option($option);
		if(preg_match('/link/',$option)) echo '<label>Hyperlink Url</label><br /><input name="wps_slider_' . $i . '_link" type="text" value="' . $value . '" /><br />';
		else if(preg_match('/caption/',$option)){
			echo '<label>Caption</label><br /><input name="wps_slider_' . $i . '_caption" type="text" value="' . $value . '" /></div>';
			$i++;
		}
		else{
			if($value != '') $imgtag = '<img src="' . $value . '" width="195"/>';
			else $imgtag = '';
			echo '<div class="slide-card shadow">' . $imgtag . '<h4>Slide ' . $i . ':</h4><br><label>Image Url</label><br /><input name="wps_slider_' . $i . '" type="text" value="' . $value . '" /><br />';
		}
	}	
}
/* Get Slider Dynamic Options */
function wps_get_slider_dynamic_options(){
	$slider_option_array = array();
	$wps_slider_limit = get_option('wps_slider_limit');
	$i = 1;
	while($i <= $wps_slider_limit){
		$option_name = 'wps_slider_' . $i;
		$slider_option = get_option($option_name);
		$slider_option_array[] = $option_name;
		$slider_option_array[] = $option_name . '_link';
		$slider_option_array[] = $option_name . '_caption';
		if(!$slider_option) return $slider_option_array;
		$i++;
	}
	return $slider_option_array;
}
/* Defaults */
function wps_set_option_defaults(){
	global $wps_field_names;
	foreach($wps_field_names as $field){
		switch($field){
			case 'wps_slider_type':
			if(get_option('wps_slider_type') == '') update_option('wps_slider_type','jQuery Cycle');
			break;
			case 'wps_slider_limit':
			if(get_option('wps_slider_limit') == '') update_option('wps_slider_limit','5');
			break;
			case 'wps_slider_width':
			if(get_option('wps_slider_width') == '') update_option('wps_slider_width','600');
			break;
			case 'wps_slider_animation':
			if(get_option('wps_slider_animation') == '') update_option('wps_slider_animation','fade');
			break;
			case 'wps_slider_slideshowspeed':
			if(get_option('wps_slider_slideshowspeed') == '') update_option('wps_slider_slideshowspeed','4000');
			break;
			case 'wps_slider_animationduration':
			if(get_option('wps_slider_animationduration') == '') update_option('wps_slider_animationduration','1000');
			break;
			default:
			return;
		}
	}	
	return true;
}
?>