<?php
global $phoneField;
$phoneField = false;
class Phone extends Field {

    public function field_id(){
        return get_class($this);
    }

	public function control_button() {
		ob_start();
		?>
		<li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="Phone">
			<span class="lfi lfi-name"><i class="fa fa-phone"></i></span> Phone
			<a title="Phone" rel="Phone" class="add pull-right add-form-field" data-template='Phone' href="#"><i class="fas fa-plus-circle ttipf" title=""></i></a>
	    </li>
	    <?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}

	public function field_preview_html($fieldindex, $fieldid, $field_infos) {
		ob_start();
		?>
		<div class='form-group'>
            <input type='text' disabled='disabled' class='form-control' name='' value='' placeholder='Number' />
		</div>
		<?php
        $preview = ob_get_clean();
        ob_start();
        include LF_BASE_DIR.'views/field-settings/field-preview.php';
        $field_render_html = ob_get_clean();
        return $field_render_html;
	}

	public function field_html($params = array()) {
        global $phoneField;
		ob_start();

	    ?>
			<div class='form-group'>
                <input id="phone_<?= wplf_valueof($params, 'id'); ?>" type='tel' data-fieldidex="<?php echo $params['id'] ?>" name="<?php echo $params['id'] ?>" class='form-control' value='' placeholder='<?= wplf_valueof($params,'placeholder') ?>' <?php echo required($params) ?>/>
                <input type="hidden" name="submitform[<?php echo $params['id'] ?>]" id="<?php echo $params['id'] ?>_intl_full">
            </div>
        <?php  if(!$phoneField) { ?>
            <script>
            jQuery(function ($){
                $('<link/>', {rel: 'stylesheet', href: "<?= LF_ASSET_URL; ?>intl-tel-input/css/intlTelInput.min.css"}).appendTo('head');
                $.getScript("<?= LF_ASSET_URL; ?>intl-tel-input/js/intlTelInput.min.js", function (){
                    $('#formarea input[type=tel]').each(function (){
                        var init = window.intlTelInput(this, {
                            initialCountry: "auto",
                            geoIpLookup: function (callback) {
                                $.get('https://ipinfo.io', function () {
                                }, "jsonp").always(function (resp) {
                                    var countryCode = (resp && resp.country) ? resp.country : "us";
                                    callback(countryCode);
                                });
                            },
                            utilsScript: "<?= LF_ASSET_URL; ?>intl-tel-input/js/utils.js?21",
                        });
                        $(this).on('change', function (){
                            $('#'+$(this).data('fieldidex')+'_intl_full').val(init.getNumber());
                        });
                    });
                });

            });

            </script>
        <?php }  ?>
		<?php
		$field_html = ob_get_clean();
        $phoneField = true;
		return $field_html;
	}

	function process_field() {

	}

}
