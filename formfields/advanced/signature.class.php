<?php
class Signature extends Field {

    public function field_id(){
        return get_class($this);
    }

	public function control_button() {
		ob_start();
		?>
		<li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="Signature">
			<span class="lfi lfi-name"><i class="fas fa-signature"></i></span> Signature
			<a title="Signature" rel="Signature" class="add pull-right add-form-field" data-template='Signature' href="#"><i class="fas fa-plus-circle ttipf" title=""></i></a>
	    </li>
	    <?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}

	public function field_preview_html($fieldindex, $fieldid, $field_infos) {
        $preview = '<img src="'.LF_ASSET_URL.'images/signature.png" style="height: 64px" alt="Signature" />';
        ob_start();
        include LF_BASE_DIR.'views/field-settings/field-preview.php';
        $field_render_html = ob_get_clean();
        return $field_render_html;
	}

	public function field_html($params = array()) {
		ob_start();
        ?>
        <canvas id="sign_<?=$params['id'] ?>" style="border: 1px solid #d9d5d5;border-radius: 4px;width: 100%;max-width: 500px;height: 150px" ></canvas>
        <a href="#" class="clear-spad_<?=$params['id'] ?> note" style="float: right;margin-top: -5px;margin-bottom: 10px"><?=esc_attr__( 'Clear Signature', LF_TEXT_DOMAIN )?></a>
        <input type="hidden" id="sig_<?=$params['id']?>" name='submitform[<?=$params['id']?>]' />
        <script>
            function resizeCanvas_<?=$params['id'] ?>(canvas) {
                var ratio =  Math.max(window.devicePixelRatio || 1, 1);
                let cw = canvas.offsetWidth ? canvas.offsetWidth : 500;
                canvas.width = cw * ratio;
                let ch = canvas.offsetHeight ? canvas.offsetHeight : canvas.height;
                canvas.height = ch * ratio;
                console.log(canvas.height);
                canvas.getContext("2d").scale(ratio, ratio);
            }
            var canvas_<?=$params['id'] ?> = document.getElementById("sign_<?=$params['id'] ?>");
            jQuery(function ($){
                $.getScript("<?= LF_ASSET_URL ?>js/signature_pad.umd.js", function (){
                    var signaturePad = new SignaturePad(canvas_<?=$params['id'] ?>, {
                        minWidth: 1,
                        maxWidth: 2,
                        penColor: "rgb(66, 133, 244)"
                    });
                    canvas_<?=$params['id'] ?>.addEventListener('click', function (){
                        $('#sig_<?=$params['id']?>').val(signaturePad.toDataURL("image/svg+xml"));
                    });
                    resizeCanvas_<?=$params['id'] ?>(canvas_<?=$params['id'] ?>);
                    $('.clear-spad_<?=$params['id'] ?>').on('click', function (e){
                        e.preventDefault();
                        signaturePad.clear();
                        $('#sig_<?=$params['id']?>').val('');
                    });

                });
            });
        </script>


		<?php
		$field_render_html = ob_get_clean();
		return $field_render_html;
	}


    function print_value($data, $field_id, $entry_id, $form_id)
    {
        return $data ? "<img src='{$data}' alt='Signature' />" : '&mdash; NA &mdash;';
    }

}

