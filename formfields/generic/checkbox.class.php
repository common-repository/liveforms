<?php
class Checkbox extends Field {

    public $multiple_values = true;

    public function field_id(){
        return get_class($this);
    }

	public function control_button() {
		ob_start();
		?>
		<li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="Checkbox">
			<span class="lfi lfi-name"><i class="fa fa-check-square"></i></span> Checkbox
			<a title="Checkbox" rel="Checkbox" class="add pull-right add-form-field" data-template='Checkbox' href="#"><i class="fas fa-plus-circle ttipf" title=""></i></a>
		</li>
	    <?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}

    public function field_preview_html($fieldindex, $fieldid, $field_infos) {
        $option_names = isset($field_infos['options']['name']) ? $field_infos['options']['name'] : ["demo1" => "Random Choice 1", "demo2" => "Random Choice 2", "demo3" => "Random Choice 3"];
        $style = wplf_valueof($field_infos, 'style', ['default' => 'pos-h']);
        ob_start();
        ?>
        <div class='checkboxes <?= $style ?>'  id="<?= $fieldindex; ?>_values">
            <?php
            foreach ($option_names as $id => $label) {
                ?>
                <label><input type='checkbox' disabled="disabled"    /> <?php echo $label ?></label>
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
			<div class='checkboxes <?= $style ?>' >
			<?php
			foreach ($option_names as $id => $label) {
			?>
				<label><input class="wplf-checkbox" type='checkbox' value='<?php echo $option_values[$id] ?>' name='submitform[<?php echo $params['id'] ?>][]' <?php echo required($params) ?> /> <?php echo $label ?></label>
			<?php
			}
			?>
			</div>
		<?php
		$field_render_html = ob_get_clean();
		return $field_render_html;
	}

	function process_field() {

	}

}
