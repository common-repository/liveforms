<?php
class Select extends Field {
    public $multiple_values = true;

    public function field_id(){
        return get_class($this);
    }

	public function control_button() {
		ob_start();
		?>
		<li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="Select">
			<span class="lfi lfi-name"><i class="fa fa-caret-down"></i></span> Select
			<a title="Select" rel="Select" class="add pull-right add-form-field" data-template='Select' href="#"><i class="fas fa-plus-circle ttipf" title=""></i></a>
		</li>
	    <?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}


	public function field_preview_html($fieldindex, $fieldid, $field_infos) {
        $option_names = isset($params['options']['name']) ? $params['options']['name'] : array();
        $option_values = isset($params['options']['value']) ? $params['options']['value'] : array();
		ob_start();
		?>
		<select class="form-control wplf-custom-select" disabled="disabled" name='submitform[]' <?php echo isset($field_infos['multi'])?'multiple':'' ?>>
			<option selected='selected' value=''>Please Select</option>
		<?php
        foreach ($option_names as $id => $label) { ?>
            <option value='<?php echo $option_values[$id] ?>'><?php echo $label ?></option>
		<?php
        }
		?>
        </select>
		<?php
        $preview = ob_get_clean();
        ob_start();
        include LF_BASE_DIR.'views/field-settings/field-preview.php';
        $field_render_html = ob_get_clean();
        return $field_render_html;
	}

    function field_html($params = array())
    {
        $option_names = isset($params['options']['name']) ? $params['options']['name'] : array();
        $option_values = isset($params['options']['value']) ? $params['options']['value'] : array();
        $cssClass = wplf_valueof($params, 'cssclass', ['default' => 'form-control']);
        $multiselect = wplf_valueof($params, 'multiselect', ['default' => 0, 'validate' => 'int']);
        $required = required($params);
        ob_start();
        ?>
        <select <?= $multiselect ? 'multiple' : '' ?> placeholder="<?= wplf_valueof($params, 'placeholder'); ?>" name='submitform[<?php echo $params['id'] ?>]' <?php echo isset($params['multi'])?'multiple':'' ?> <?php echo required($params) ?>  class='form-control wplf-custom-select'>
            <?php if(!$multiselect) { ?>
            <option selected='selected' value=''><?= esc_attr__( 'Please Select', LF_TEXT_DOMAIN ); ?></option>
            <?php } ?>
            <?php
            foreach ($option_names as $id => $label) { ?>
                <option value='<?php echo $option_values[$id] ?>' <?php selected($option_values[$id], wplf_valueof($params, 'default_value')); ?>><?php echo $label ?></option>
                <?php
            }
            ?>
        </select>
        <?php
        return  ob_get_clean();
    }

}
