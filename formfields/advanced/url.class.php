<?php
class Url extends Field {

    public function field_id(){
        return get_class($this);
    }

	public function control_button() {
		ob_start();
		?>
		<li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="Url">
			<span class="lfi lfi-name"><i class="fa fa-link"></i></span> URL
			<a title="Url" rel="Url" class="add pull-right add-form-field" data-template='Url' href="#"><i class="fas fa-plus-circle ttipf" title=""></i></a>
		</li>
	    <?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}

	public function field_preview_html($fieldindex, $fieldid, $field_infos) {
		ob_start();
		?>
		<div class='form-group'>
			<input type='url' disabled="disabled" class='form-control' name=submitform[] id='urlfield_'  placeholder='<?= wplf_valueof($field_infos, "placeholder") ?>' value='<?php echo isset($params['url']) ? $params['url'] :''  ?>'/>
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
        return "<input type='url' name='submitform[{$params['id']}]' ".$this->attributes($params['attributes'])." placeholder='".wplf_valueof($params, "placeholder")."' class='{$cssClass}' {$required} />";
    }

	function process_field() {

	}

}
