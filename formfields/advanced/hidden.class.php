<?php
class Hidden extends Field {

    public function field_id(){
        return get_class($this);
    }

	public function control_button() {
		ob_start();
		?>
		<li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="Hidden">
			<span class="lfi lfi-name"><i class="fa fa-eye-slash"></i></span> Hidden
			<a title="Hidden" rel="Hidden" class="add pull-right add-form-field" data-template='Hidden' href="#"><i class="fas fa-plus-circle ttipf" title=""></i></a>
	    </li>
	    <?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}

    public function hidden_field_settings($fieldindex, $fieldid, $field_infos){
        include LF_BASE_DIR.'views/field-settings/hidden-options.php';
    }

    public function field_preview_html($fieldindex, $fieldid, $field_infos) {
        ob_start();
        $preview = "<input style='border: 1px dashed #dddddd;' type='text' disabled='disabled' name='submitform[]' id='{$fieldindex}' class='form-control' value='{$field_infos['value']}' />";
        include LF_BASE_DIR.'views/field-settings/field-preview.php';
        $field_render_html = ob_get_clean();
        return $field_render_html;
    }

	public function field_render_html($params = array()) {
		ob_start();
		$condition_fields = '';
		$cond_action = '';
		$cond_boolean = '';
		if (isset($params['condition']) and isset($params['conditioned'])) {
			$cond_boolean = $params['condition']['boolean_op'];
			$cond_action = $params['condition']['action'];
            if(is_array($params['condition']['field'])) {
                foreach ($params['condition']['field'] as $key => $value) {
                    $field_id = $value;
                    $field_op = $params['condition']['op'][$key];
                    $field_value = $params['condition']['value'][$key];
                    $condition_fields .= ($field_id . ':' . $field_op . ':' . $field_value . '|');
                }
            }
			$condition_fields = rtrim($condition_fields, '|');
		}


		?>

        <input type='hidden' id="<?php echo $params['id']; ?>_ind" data-index="<?php echo $params['id']; ?>" name='<?php echo isset($params['name']) && $params['name'] != ''?$params['name']:$params['id']; ?>' class='form-control' value='<?php echo  $this->parse_var($params['value'], $params); ?>'  />
        <input type='hidden' id="<?php echo $params['id']; ?>"  data-index="<?php echo $params['id']; ?>" name='submitform[<?php echo isset($params['name']) && $params['name'] != ''?$params['name']:$params['id']; ?>]' class='form-control' value='<?php echo  $this->parse_var($params['value'], $params); ?>' />

        <?php
		$field_render_html = ob_get_clean();
		return $field_render_html;
	}


}

add_action("Hidden_field_options", [new Hidden(),  'hidden_field_settings'], 10, 3);
