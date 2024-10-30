<?php
class Paratext extends Field {

    public function field_id(){
        return get_class($this);
    }

	public function control_button() {
		ob_start();
		?>
		<li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="Paratext">
			<span class="lfi lfi-name"><i class="fa fa-align-justify"></i></span> <?= esc_attr__( 'Paratext', LF_TEXT_DOMAIN ); ?>
			<a title="Paratext" rel="Paratext" class="add pull-right add-form-field" data-template='Paratext' href="#"><i class="fas fa-plus-circle ttipf" title=""></i></a>
	    </li>
	    <?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}

    public function paratext_field_settings($fieldindex, $fieldid, $field_infos){
        include LF_BASE_DIR.'views/field-settings/paratext-options.php';
    }

	public function field_preview_html($fieldindex, $fieldid, $field_infos) {
		ob_start();
		?>
		<div class='form-group'>
			<?php
            echo wplf_valueof($field_infos, "paragraph_text_value", '[Here will be your paragraph]');
            ?>
		</div>
		<?php
        $preview = ob_get_clean();
        ob_start();
        include LF_BASE_DIR.'views/field-settings/field-preview.php';
        $field_render_html = ob_get_clean();
        return $field_render_html;
	}

	public function field_html($params = array()) {
		ob_start();
		?>
			<div class='form-group'>
				<?php echo isset($params['paragraph_text_value']) ? $params['paragraph_text_value'] : '[Here will be your paragraph]' ?>
			</div>
		<?php
		$field_render_html = ob_get_clean();
		return $field_render_html;
	}

    function print_value($data, $field_id, $entry_id, $form_id)
    {
        return $data;
    }

	function process_field() {

	}

}

add_action("Paratext_field_options", [new Paratext(),  'paratext_field_settings'], 10, 3);
