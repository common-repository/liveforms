<?php
class Captcha extends Field {

    public function field_id(){
        return get_class($this);
    }

	public function control_button() {
		ob_start();
		?>
		<li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="Captcha">
			<span class="lfi lfi-name"><i class="fas fa-user-shield"></i></span> Math Captcha
			<a title="Captcha" rel="Captcha" class="add pull-right add-form-field" data-template='Captcha' href="#"><i class="fas fa-plus-circle ttipf" title=""></i></a>
	    </li>
	    <?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}

    static function challenge($params){

        if(!function_exists('imagecreate')) return '<div class="input-group"><div class="input-group-addon bg-white"  style="padding: 0 0 0 20px !important;background: #fff"><div class="input-group-text" style="width: 80px;background: #ffffff;border: 0;"> GD library is not active!</div></div><input name="submitform['. wplf_valueof($params, 'id') .']"   placeholder="'.wplf_valueof($params, "placeholder").'" type="text" class="form-control"></div>';

        $img = imagecreate(70, 24);

        $textbgcolor = imagecolorallocate($img, 255, 255, 255);
        $textcolor = imagecolorallocate($img, 0, 192, 255);
        $ops = array('+', 'x', '-');
        $op = $ops[random_int(0, 2)];
        $num1 = random_int(1, 9);
        $num2 = random_int(1, 9);
        $value = self::getResult($num1, $num2, $op);
        $value = \LiveForms\__\Crypt::encrypt($value);
        $txt = "$num1 $op $num2";
        imagestring($img, 5, 5, 5, $txt, $textcolor);
        ob_start();
        imagepng($img);
        $img = ob_get_clean();

        return sprintf('<div class="input-group"><div class="input-group-addon bg-white"  style="padding: 0 0 0 20px !important;background: #fff"><div class="input-group-text" style="width: 80px;background: #ffffff;border: 0;"> <img style="padding:0;margin:0;" src="data:image/png;base64,%s" /><input type="hidden" name="'.wplf_valueof($params, 'id').'_hash" value="'.$value.'" /></div></div><input name="submitform['. wplf_valueof($params, 'id') .']"   placeholder="'.wplf_valueof($params, "placeholder").'" type="text" class="form-control"></div>', base64_encode($img));

    }

    private static function getResult($num1, $num2, $op){
        $result = 0;
        if($op == '-') $result = $num1 - $num2;
        if($op == '+') $result = $num1 + $num2;
        if($op == 'x') $result = $num1 * $num2;
        return $result;
    }

    public function field_preview_html($fieldindex, $fieldid, $field_infos) {
        $preview = Captcha::challenge($field_infos);
        ob_start();
        include LF_BASE_DIR.'views/field-settings/field-preview.php';
        $field_render_html = ob_get_clean();
        return $field_render_html;
    }

	public function field_render_html($params = array()) {
		ob_start();
		$url = get_post_permalink();
		$condition_fields = '';
		$cond_action = '';
		$cond_boolean = '';
		if (isset($params['condition']) and isset($params['conditioned'])) {
			$cond_boolean = $params['condition']['boolean_op'];
			$cond_action = $params['condition']['action'];
			foreach($params['condition']['field'] as $key => $value) {
				$field_id = $value;
				$field_op = $params['condition']['op'][$key];
				$field_value = $params['condition']['value'][$key];
				$condition_fields .= ($field_id.':'.$field_op.':'.$field_value . '|');
			}
			$condition_fields = rtrim($condition_fields, '|');
		}
		?>
		<div id="<?php echo $params['id'] ?>" class='form-group <?php if (isset($params['conditioned'])) echo " conditioned hide "?>' data-cond-fields="<?php echo $condition_fields ?>" data-cond-action="<?php echo $cond_action.':'.$cond_boolean ?>" >
			<label style="display: block; clear: both"><?php echo $params['label'] ?><span class="note"><?php echo $params['note']; ?></span></label>
			<div class='form-group' >
				<div class="row">
					<div class="col-md-12 row-bottom-buffer">
                        <?php echo Captcha::challenge($params); ?>
					</div>
				</div>
			</div>
		</div>

		<?php
		$field_render_html = ob_get_clean();
		return $field_render_html;
	}

	function validate_field($field_id, $value)
    {
        $valid = \LiveForms\__\Crypt::decrypt(wplf_query_var("{$field_id}_hash"));
        if( $value === $valid )
            return $value;
        $this->validation_error = 'Invalid CAPTCHA value!';
        return false;
    }

	function process_field() {

	}

}
