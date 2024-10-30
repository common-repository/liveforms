<?php
class ReCaptcha extends Field {

    public function field_id(){
        return get_class($this);
    }

	public function control_button() {
		ob_start();
		?>
		<li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="ReCaptcha">
			<span class="lfi lfi-name"><i class="fas fa-user-shield"></i></span> ReCaptcha
			<a title="ReCaptcha" rel="ReCaptcha" class="add pull-right add-form-field" data-template='ReCaptcha' href="#"><i class="fas fa-plus-circle ttipf" title=""></i></a>
	    </li>
	    <?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}

    public function field_preview_html($fieldindex, $fieldid, $field_infos) {
        $preview = "<img src='".LF_ASSET_URL."images/recaptcha.gif' alt='ReCaptcha' style='height: 96px;width:auto;'/>";
        ob_start();
        include LF_BASE_DIR.'views/field-settings/field-preview.php';
        $field_render_html = ob_get_clean();
        return $field_render_html;
    }

	public function field_html($params = array()) {
		ob_start();
		$id = wplf_valueof($params, 'id');
		?>
        <input type="hidden" id="field_<?php echo $id ?>" name="submitform[<?php echo $id ?>]" value=""/>
        <script src="https://www.google.com/recaptcha/api.js?onload=rcl_<?= $id; ?>&render=explicit" async defer></script>
        <div id="rcc_<?php echo $id ?>"></div>
        <script type="text/javascript">
            var rcv_<?php echo $id ?> = function (response) {
                jQuery('#field_<?php echo $id ?>').val(response);
            };
            var rcl_<?= $id; ?> = function () {
                grecaptcha.render('rcc_<?php echo $id ?>', {
                    'sitekey': '<?php echo get_option('__wplf_recaptcha_site_key', '6LfKqgQTAAAAAFsaZG4Had75rFxTcsGdWH7a42qf'); ?>',
                    'callback': rcv_<?php echo $id ?>,
                    'theme': 'light'
                });
            };
        </script>
        <div class="g-recaptcha" data-sitekey="6LfKqgQTAAAAAFsaZG4Had75rFxTcsGdWH7a42qf"></div>
        <?php
		$field_html = ob_get_clean();
		return $field_html;
	}

	function validate_field($field_id, $value)
    {
        $ret = LiveForms\__\__::remote_post('https://www.google.com/recaptcha/api/siteverify', array('secret' => get_option('__wplf_recaptcha_secret_key', '6LfKqgQTAAAAADzZmQ3eoXECUSzrayMUwE271dTr'), 'response' => $value));
        $ret = json_decode($ret);
        if ($ret->success) return true;
        $this->validation_error = esc_attr__( 'Captcha validation failed!', LF_TEXT_DOMAIN );
        return false;
    }

    function print_value($data, $field_id, $entry_id, $form_id)
    {
        return '<i class="fa fa-check-double text-success"></i> '.esc_attr__( 'Validated', LF_TEXT_DOMAIN );
    }

	function process_field() {

	}

}
