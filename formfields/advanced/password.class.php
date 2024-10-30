<?php

class Password extends Field {

    public function __construct()
    {

    }

    public function field_id()
    {
        return get_class($this);
    }

	public function control_button() {
		ob_start();
		?>
		<li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="Password">
			<span class="lfi lfi-name"><i class="fa fa-ellipsis-h"></i></span> Password
			<a title="Password" rel="Password" class="add pull-right add-form-field" data-template='Password' href="#"><i class="fas fa-plus-circle ttipf" title=""></i></a>
	    </li>
	    <?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}

    public function password_field_settings($fieldindex, $fieldid, $field_infos){
        include LF_BASE_DIR.'views/field-settings/password-options.php';
    }

    public function field_preview_html($fieldindex, $fieldid, $field_infos) {
        ob_start();
        $preview = "<input type='password' disabled='disabled' name='submitform[]' placeholder='".wplf_valueof($field_infos, "placeholder")."' class='form-control' value='' />";
        include LF_BASE_DIR.'views/field-settings/field-preview.php';
        $field_render_html = ob_get_clean();
        return $field_render_html;
    }

    function field_html($params = array())
    {
        $cssClass = wplf_valueof($params, 'cssclass', ['default' => 'form-control']);
        $required = required($params);
        ob_start();
        ?>
        <input type='password' id="password_<?php echo $params['id'] ?>" name='submitform[<?php echo isset($params['id'])?$params['id']:'' ?>]' placeholder='<?= wplf_valueof($params, "placeholder") ?>' class='form-control' value='' <?php echo required($params)?> />
        <?php if(isset($params['show_cp']) && (int)$params['show_cp'] === 1) { ?>
        <label>Confirm password:</label>
        <input type='password' id="password_confirm_<?php echo $params['id'] ?>" data-rule-equalTo="#password_<?php echo $params['id'] ?>" name='submitform[<?php echo isset($params['id'])?$params['id']:'' ?>]' class='form-control' value='' <?php echo required($params)?> />
        <?php } ?>
        <?php
        return ob_get_clean();
    }


}

add_action("Password_field_options", [new Password(),  'password_field_settings'], 10, 3);
