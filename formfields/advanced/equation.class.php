<?php
class Equation extends Field {

    public function field_id(){
        return get_class($this);
    }

	public function control_button() {
		ob_start();
		?>
		<li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="Equation">
			<span class="lfi lfi-name"><i class="fa fa-calculator"></i></span> <?= esc_attr__( 'Equation', LF_TEXT_DOMAIN ); ?>
			<a title="Equation" rel="Equation" class="add pull-right add-form-field" data-template='Equation' href="#"><i class="fas fa-plus-circle ttipf" title=""></i></a>
	    </li>
	    <?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}

    public function equation_field_settings($fieldindex, $fieldid, $field_infos){
        include LF_BASE_DIR.'views/field-settings/equation-options.php';
    }

	public function field_preview_html($fieldindex, $fieldid, $field_infos) {
		ob_start();
		?>

			<code class="eqpreview">
                <?php
                echo wplf_valueof($field_infos, "equation", '[EQUATION]');
                ?>
            </code>

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
				<?php echo  $this->process_equation($params) ?>
			</div>
		<?php
		$field_render_html = ob_get_clean();
		return $field_render_html;
	}

    function print_value($data, $field_id, $entry_id, $form_id)
    {
        return $data;
    }

    function process_equation($params)
    {
        $equation = wplf_valueof($params, 'equation');
        //lfprecho($params);
        $mat = [];
        preg_match_all("/\{\{([^\}]+)\}\}/", $equation, $mat);
        $eq_fields = wplf_valueof($mat, 1);


        ?>
            <style>
                .blink_me {
                    animation: blinker 2s linear infinite;
                }

                @keyframes blinker {
                    50% {
                        opacity: 0.2;
                    }
                }
            </style>
        <div class="panel panel-default">
            <div class="panel-body" id="_eqr_<?= $params['id'] ?>" style="font-family: monospace">[...]</div>
            <input type="hidden" name="submitform[<?php echo esc_attr($params['id']); ?>]" id="hdn_<?= $params['id'] ?>" />
        </div>
        <script>

            jQuery(function ($) {
                function wplf_valuate_equation(equation, fields) {
                    for(i = 0; i < fields.length; i++) {
                        let val = parseFloat($('#field_'+fields[i]).val());
                        let index = "{{"+fields[i]+"}}";
                        equation = equation.replaceAll(index, val);
                    }
                    return encodeURIComponent(equation);
                }

                let eq = '<?= $equation; ?>';
                let vals = {}, result;
                let eq_fields = <?= json_encode($eq_fields); ?>;
                $.each(eq_fields, function (index, field) {
                    $('body').on('keyup', '#field_'+field, function () {
                        result = wplf_valuate_equation(eq, eq_fields);
                        console.log(result);
                        $('#_eqr_<?= $params['id'] ?>').addClass('blink_me').html('Calculating...');
                        $.get("https://api.mathjs.org/v4/?expr="+result, function (res) {
                            $('#_eqr_<?= $params['id'] ?>').removeClass('blink_me').html(res);
                            $('#hdn_<?= $params['id'] ?>').val(res);
                        }).fail(function() {
                            $('#_eqr_<?= $params['id'] ?>').removeClass('blink_me').html('Calculation error!');
                        });

                    });
                });
            });
        </script>
        <?php
    }

	function process_field() {

	}

}

add_action("Equation_field_options", [new Equation(),  'equation_field_settings'], 10, 3);
