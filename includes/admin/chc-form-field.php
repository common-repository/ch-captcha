<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WP_CHC_Form_Field{

	function __construct() {
		add_action('admin_enqueue_scripts', array($this,'colpick_scripts'), 100);
	}
	
	function colpick_scripts() {
		wp_enqueue_style( 'wp-color-picker' );
    	wp_enqueue_script( 'my-script-handle', plugins_url('my-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
    	

	    $colorpicker_l10n = array('clear' => __('Clear'), 'defaultString' => __('Default'), 'pick' => __('Select Color'));
 		wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );
	}


	function get_field($args){

		if(method_exists($this, $args['type'])){
			if(!isset($args['value'])){
				$value = get_option($args['name']);
				if($value){
					$args['value'] = $value;
				}else{
					$args['value'] = $args['default'] ? $args['default'] : '';
				}
			}
			return $this->{$args['type']}($args);
		}else{
			return 'Invalid Input Type';
		}
	}

	function text($args){
		$args['type'] = 'text';
		return $this->input_field($args);
	}
	function email($args){
		$args['type'] = 'email';

		return $this->input_field($args);
	}
	function search($args){
		$args['type'] = 'search';
		return $this->input_field($args);

	}
	function password($args){
		$args['type'] = 'password';
		return $this->input_field($args);

	}

	function input_field($args){
		$key = str_replace(array('[',']',' ','_'), '-', $args['name']);
		ob_start();
		?>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="chc_<?php echo $key; ?>"><?php echo $args['label']; ?></label>
					<span class="woocommerce-help-tip"><?php echo $args['help']; ?></span>
				</th>
				<td>
					<input name="<?php echo $args['name'] ?>" type="<?php echo $args['type'] ?>" id="<?php echo $key; ?>" value="<?php echo !empty($args['value']) ? $args['value'] : $args['default']; ?>" class="regular-text <?php echo $args['class']; ?> ltr">
					<?php if(!empty($args['description'])){?>
					<p class="description"><?php echo $args['description']; ?></p>
					<?php } ?>
					
				</td>
			</tr>
		<?php
		return ob_get_clean();
	}

	function select($args){
		$key = str_replace(array('[',']',' ','_'), '-', $args['name']);
		ob_start();
		?>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="chc_<?php echo $key; ?>"><?php echo $args['label']; ?></label>
					<span class="woocommerce-help-tip"><?php echo $args['help']; ?></span>
				</th>
				<td>
					<select name="<?php echo $args['name'] ?>" id="<?php echo $key; ?>" class="regular-text ltr <?php echo $args['class']; ?>">
						<?php foreach ($args['options'] as $key => $value) { 
							if(is_array($value)){
								echo "<optgroup>";
								foreach ($value as $k => $v) {
									if(is_array($args['value']) && in_array($k, $args['value'])){
										$selected = 'selected="selected"';
									}else if($args['value'] == $k){
										$selected = 'selected="selected"';
									}else{
										$selected = "";
									}
									?><option name="<?php echo $k; ?>" <?php echo $selected; ?>><?php echo $v; ?></option><?php
								}
								echo "</optgroup>";
							}else{
								if(is_array($args['value']) && in_array($key, $args['value'])){
									$selected = 'selected="selected"';
								}else if($args['value'] == $key){
									$selected = 'selected="selected"';
								}else{
									$selected = "";
								}
								?><option name="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option><?php
							}
							?>
							
						<?php } ?>
					</select>
					<?php if(!empty($args['description'])){?>
					<p class="description"><?php echo $args['description']; ?></p>
					<?php } ?>
				</td>
			</tr>
		<?php
		return ob_get_clean();
	}

	function textarea($args){
		$key = str_replace(array('[',']',' ','_'), '-', $args['name']);
		ob_start();
		?>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="chc_<?php echo $key; ?>"><?php echo $args['label']; ?></label>
					<span class="woocommerce-help-tip"><?php echo $args['help']; ?></span>
				</th>
				<td>
					<textarea name="<?php echo $args['name'] ?>" id="<?php echo $key; ?>" class="regular-textarea ltr <?php echo $args['class']; ?>"><?php echo !empty($args['value']) ? $args['value'] : $args['default']; ?></textarea>
					<?php if(!empty($args['description'])){?>
					<p class="description"><?php echo $args['description']; ?></p>
					<?php } ?>
				</td>
			</tr>
		<?php
	}

	function radio($args){
		$key = str_replace(array('[',']',' ','_'), '-', $args['name']);
		ob_start();
		?>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="chc_<?php echo $key; ?>"><?php echo $args['label']; ?></label>
					<span class="woocommerce-help-tip"><?php echo $args['help']; ?></span>
				</th>
				<td>
					<fieldset>
						<?php foreach ($args['options'] as $key => $value) { 

							if(is_array($args['value']) && in_array($key, $args['value'])){
								$checked = 'checked="checked"';
							}else if($args['value'] == $key){
								$checked = 'checked="checked"';
							}else{
								$checked = "";
							}
							?>
							<label title="<?php echo $key; ?>">
								<input type="radio" name="<?php echo $args['name'] ?>" value="<?php echo $key; ?>" <?php echo $checked; ?>> <?php echo $value; ?>
							</label><br>
						<?php } ?>
						
						<p class="description"><?php echo $args['description']; ?></p>
					</fieldset>
				</td>
			</tr>
		<?php
		return ob_get_clean();
	}

	function checkbox($args){
		$key = str_replace(array('[',']',' ','_'), '-', $args['name']);
		ob_start();
		?>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="chc_<?php echo $key; ?>"><?php echo $args['label']; ?></label>
					<span class="woocommerce-help-tip"><?php echo $args['help']; ?></span>
				</th>
				<td>
					<fieldset>
						<?php 
						if(isset($args['options'])){
							foreach ($args['options'] as $key => $value) { 
								if(is_array($args['value']) && in_array($key, $args['value'])){
									$checked = 'checked="checked"';
								}else if($args['value'] == $key){
									$checked = 'checked="checked"';
								}else{
									$checked = "";
								}
								?>
								<label title="<?php echo $key; ?>">
									<input type="checkbox" name="<?php echo $args['name'] ?>[]" value="<?php echo $key ?>" <?php echo $checked ?>> <?php echo $value; ?>
								</label><br>
							<?php }
							}else{
								?>
								<label title="<?php echo $key; ?>">
									<input type="checkbox" name="<?php echo $args['name'] ?>" value="yes" <?php echo $checked ?>> <?php echo $value; ?>
								</label>
								<?php
							} ?>
						
						<p class="description"><?php echo $args['description']; ?></p>
					</fieldset>
				</td>
			</tr>
		<?php
		return ob_get_clean();
	}

	function color_picker($args){
		$args['class'] = 'chc-color-picker '.$args['class'];
		ob_start();
		echo $this->text($args);
		?>
		<script>
	    	(function ($) {
	    		$(document).ready(function(){
	    			var myOptions = {
					    change: function(event, ui){
					    	<?php echo $args['change']; ?>
					    },
					    clear: function() {
					    	<?php echo $args['clear']; ?>
					    }
					};
			    	$('.chc-color-picker').wpColorPicker(myOptions);
			  	});
			}(jQuery));
    	</script>
		<?php
		return ob_get_clean();
	}

	function date_picker($args){
		return $this->text($args);
	}

	function custom($args){
		return $args;
	}

	function file($args){

	}

}
