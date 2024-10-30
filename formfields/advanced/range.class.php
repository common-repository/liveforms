<?php
global $range_control;
$range_control = 0;
class Range extends Field {

    public function field_id(){
        return get_class($this);
    }

	public function control_button() {
		ob_start();
		?>
		<li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="Range">
			<span class="lfi lfi-name"><i class="fas fa-sliders-h"></i></span> Range
			<a title="Range" rel="Range" class="add pull-right add-form-field" data-template='Range' href="#"><i class="fas fa-plus-circle ttipf" title=""></i></a>
		</li>
	    <?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}

    public function range_field_settings($fieldindex, $fieldid, $field_infos){
        include LF_BASE_DIR.'views/field-settings/range-options.php';
    }

	public function field_preview_html($fieldindex, $fieldid, $field_infos) {
        $preview = "<input type='range' disabled='disabled' class='__slider' min=0 max=50 name=submitform[] id='rangefield_' />";
        ob_start();
        include LF_BASE_DIR.'views/field-settings/field-preview.php';
        $field_render_html = ob_get_clean();
        return $field_render_html;
	}

	public function field_html($params = array()) {
        global $range_control;
        $cssClass = wplf_valueof($params, 'cssclass', ['default' => '']);
		$required = required($params);
		$min = wplf_valueof($params, 'min', ['validate' => 'double']) ? : 0;
        $max = wplf_valueof($params, 'max', ['validate' => 'double']) ? : 100;
        $step = wplf_valueof($params, 'step', ['validate' => 'double']) ? : 1;
        $field_html = "<input class='__slider {$cssClass}' type='range' min='{$min}' max='{$max}' step='{$step}' name='submitform[{$params['id']}]' id='rangefield_{$params['id']}' {$required} />";
        if($range_control === 0)
        $field_html .= "<style>._wplf .__slider{-webkit-appearance:none;width:100%;height:15px;border-radius:3px;background:#d3d3d3;outline:0;opacity:.7;-webkit-transition:.2s;transition:opacity .2s}._wplf .__slider:hover{opacity:1}._wplf .__slider::-webkit-slider-thumb{-webkit-appearance:none;appearance:none;width:40px;height:15px;border-radius:3px;background:#111;cursor:pointer;border: 3px solid var(--color-primary) !important; box-shadow: var(--input-shadow-primary);}._wplf .__slider::-moz-range-thumb{width:40px;height:15px;border-radius:3px;background:#ffffff;cursor:pointer;border: 3px solid var(--color-primary) !important; box-shadow: var(--input-shadow-primary);}._wplf .__slider::-moz-range-track{box-shadow: none;border: 0;background: transparent;}</style>";
        $range_control++;
		return $field_html;
	}

}


add_action("Range_field_options", [new Range(),  'range_field_settings'], 10, 3);
