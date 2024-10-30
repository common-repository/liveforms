<?php
class Radio extends Field {

    public $multiple_values = true;

    public function field_id(){
        return get_class($this);
    }

    public function control_button() {
		ob_start();
		?>
		<li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="Radio">
			<span class="lfi lfi-name"><i class="far fa-dot-circle"></i></span> Radio
			<a title="Radio" rel="Radio" class="add pull-right add-form-field" data-template='Radio' href="#"><i class="fas fa-plus-circle ttipf" title=""></i></a>
		</li>
	    <?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}

    public function field_preview_html($fieldindex, $fieldid, $field_infos) {
        $option_names = isset($field_infos['options']['name']) ? $field_infos['options']['name'] : ["demo1" => "Random Choice 1", "demo2" => "Random Choice 2", "demo3" => "Random Choice 3"];
        $style = wplf_valueof($field_infos, 'style', ['default' => 'pos-h']);
        ob_start();
        //echo"<pre>".print_r($field_infos[$fieldindex], 1)."</pre>";
        ?>
        <div class='radiobuttons <?php echo $style; ?>' id="<?= $fieldindex; ?>_values">
            <?php

            foreach ($option_names as $id => $label) {
                ?>
                <label><input type='radio' disabled="disabled" value='' name='submitform[]' /> <span><?php echo $label ?></span></label>
                <?php
            }
            ?>
        </div>
        <?php
        $preview = ob_get_clean();
        ob_start();
        include LF_BASE_DIR.'views/field-settings/field-preview.php';
        $field_render_html = ob_get_clean();
        return $field_render_html;
    }

	public function field_html($params = array()) {
		$option_names = isset($params['options']['name']) ? $params['options']['name'] : array();
        $option_values = isset($params['options']['value']) ? $params['options']['value'] : array();
        $style = wplf_valueof($params, 'style', ['default' => 'pos-h']);
		ob_start();

		?>
			<div class='radiobuttons <?php echo $style; ?>'>
			<?php
			foreach ($option_names as $id => $label) {
			?>
				<label class="c-pointer" for="<?php echo $params['id']."_".$id; ?>"><input id="<?php echo $params['id']."_".$id; ?>" class="wplf-radio" type='radio' value='<?php echo $option_values[$id] ?>' name='submitform[<?php echo $params['id'] ?>]' <?php checked(0, $id) ?> <?php echo required($params) ?> /> <span><?php echo $label ?></span> </label>
			<?php
			}
			?>
			</div>
		<?php
		$field_html = ob_get_clean();
		return $field_html;

	}

	function process_field() {

	}

}
