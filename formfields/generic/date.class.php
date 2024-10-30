<?php
class Date extends Field {

    public function field_id(){
        return get_class($this);
    }

	public function control_button() {
		ob_start();
		?>
		<li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="Date">
			<span class="lfi lfi-name"><i class="fa fa-calendar"></i></span> Date
			<a title="Date" rel="Date" class="add pull-right add-form-field" data-template='Date' href="#"><i class="fas fa-plus-circle ttipf" title=""></i></a>
		</li>
	    <?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}


    public function field_preview_html($fieldindex, $fieldid, $field_infos) {
        ob_start();
        ?>
        <div class='form-group'>
            <input type='date' disabled="disabled" class='form-control p-0' style="padding-left: 15px !important;" name=submitform[]  />
        </div>
        <?php
        $preview = ob_get_clean();
        ob_start();
        include LF_BASE_DIR.'views/field-settings/field-preview.php';
        $field_render_html = ob_get_clean();
        return $field_render_html;
    }

    function field_html($params = array())
    {
        $cssClass = wplf_valueof($params, 'cssclass', ['default' => 'form-control']);
        $required = required($params);
        $default_value = \LiveForms\__\__::valueof($params, 'default_value');
        $default_value = $this->parse_var($default_value, $params);
        $params['attributes']['value'] = $default_value;
        $datepicker_field_id = "datepicker_{$params['id']}";
        $params['attributes']['id'] = $datepicker_field_id;
        return "<input type='date' name='submitform[{$params['id']}]' ".$this->attributes($params['attributes'])." placeholder='".wplf_valueof($params, "placeholder")."' class='{$cssClass}' {$required} />";
    }

	function process_field() {

	}

}
