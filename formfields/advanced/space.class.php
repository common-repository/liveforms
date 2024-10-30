<?php
class Space extends Field {

    public $default_options = false;
    public $conditional_logic = false;
    public $required_option = false;

    public function field_id(){
        return get_class($this);
    }

	public function control_button() {
		ob_start();
		?>
		<li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="<?php echo __CLASS__ ?>">
			<span class="lfi lfi-space"><i class="fas fa-arrows-alt-v"></i></span> Space
			<a title="<?php echo __CLASS__ ?>" rel="<?php echo __CLASS__ ?>" class="add pull-right add-form-field" data-template='<?php echo __CLASS__ ?>' href="#"><i class="fas fa-plus-circle ttipf" title=""></i></a>
		</li>
	    <?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}

	public function field_preview_html($fieldindex, $fieldid, $field_infos) {
        $height = wplf_valueof($field_infos, 'height', ['default' => 50]);
        $preview = "<div class='form-group' style='margin: 0;padding: 0'><style>#label_{$fieldindex} { display: none !important; } #field_{$fieldindex}{ padding: 0; }</style><div id='{$fieldindex}' class='wplf-space' style='height: {$height}px;text-align: center'><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' version='1.1' id='Layer_1' x='0px' y='0px' viewBox='0 0 512.568 512.568' style='enable-background:new 0 0 512.568 512.568;height: 100%;opacity: 0.2;' xml:space='preserve' width='512' height='512'><path d='M184.284,235.284h140v40h-140V235.284z M275.284,436V315.284h-40V436l-53.858-53.858l-28.284,28.285l102.142,102.142  l105.142-105.142l-28.284-28.285L275.284,436z M235.284,76.568v118.716h40V76.568l56.858,56.858l28.284-28.284L255.284,0  L152.142,103.142l28.284,28.284L235.284,76.568z'/></svg></div></div>";
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
        $height = wplf_valueof($params, 'height', ['default' => 50]);
        $fieldindex = $params['id'];
        $field = "<div class='wplf-space' style='height: {$height}px;text-align: center'><style>#{$fieldindex} label { display: none !important; } #{$fieldindex}, #field_{$fieldindex}{ padding: 0; margin: 0; }</style></div>";
        return $field;
    }

	function process_field() {

	}

    public function space_field_settings($fieldindex, $fieldid, $field_infos){
        include LF_BASE_DIR.'views/field-settings/space-options.php';
    }

}

add_action("Space_field_options", [new Space(),  'space_field_settings'], 10, 3);
