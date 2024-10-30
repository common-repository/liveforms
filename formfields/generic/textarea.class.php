<?php
class Textarea extends Field {

    public function field_id(){
        return get_class($this);
    }

	public function control_button() {
		ob_start();
		?>
		<li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="Textarea">
			<span class="lfi lfi-name"><i class="fa fa-align-justify"></i></span> Textarea
			<a title="Textarea" rel="Textarea" class="add pull-right add-form-field" data-template='Textarea' href="#"><i class="fas fa-plus-circle ttipf" title=""></i></a>
		</li>
	    <?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}

    public function textarea_field_settings($fieldindex, $fieldid, $field_infos){
        include LF_BASE_DIR.'views/field-settings/textarea-options.php';
    }

    public function field_preview_html($fieldindex, $fieldid, $field_infos) {
        ob_start();
        $preview = "<textarea name='submitform[]' placeholder='".wplf_valueof($field_infos, "placeholder")."' disabled='disabled' class='form-control'></textarea>";
        include LF_BASE_DIR.'views/field-settings/field-preview.php';
        $field_render_html = ob_get_clean();
        return $field_render_html;
    }

    function field_html($params = array())
    {
        $cssClass = wplf_valueof($params, 'cssclass', ['default' => 'form-control']);
        $required = required($params);
        $minheight = wplf_valueof($params, 'minheight');
        $minheight = $minheight ? $minheight : 100;
        $maxheight = wplf_valueof($params, 'maxheight');
        $maxheight = $maxheight ? $maxheight : 300;
        $default_value = \LiveForms\__\__::valueof($params, 'default_value');
        $default_value = $this->parse_var($default_value, $params);
        return "<textarea type='text' style='height: {$minheight}px;min-height: {$minheight}px;max-height: {$maxheight}px;' name='submitform[{$params['id']}]' ".$this->attributes($params['attributes'])." placeholder='".wplf_valueof($params, "placeholder")."' class='{$cssClass}' {$required} >{$default_value}</textarea>";
    }

}

add_action("Textarea_field_options", [new Textarea(),  'textarea_field_settings'], 10, 3);
