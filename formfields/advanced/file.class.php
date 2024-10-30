<?php

class File extends Field {

    public function field_id(){
        return get_class($this);
    }

	public function control_button() {
		ob_start();
		?>
		<li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="File">
			<span class="lfi lfi-name"><i class="fa fa-file-upload"></i></span> File Upload
			<a title="File" rel="File" class="add pull-right add-form-field" data-template='File' href="#"><i class="fas fa-plus-circle ttipf" title=""></i></a>
		</li>
		<?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}

	public function file_field_settings($fieldindex, $fieldid, $field_infos){
        include LF_BASE_DIR.'views/field-settings/file-options.php';
    }

	public function field_preview_html($fieldindex, $fieldid, $field_infos) {
		ob_start();
		?>
		<div class="input-group">
				<span class="input-group-btn">
					<button type="button" class="btn btn-default"><i class="fa fa-folder-open-o"></i> Browse</button>
				</span>
			<input class="form-control" style="background: #ffffff" type="text" readonly="readonly">
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
			<div class="fileUpload input-group form-control" style="padding: 0 !important;">
				<span class="input-group-btn" style="position: relative;background: rgba(0,0,0,0.03);border-right: 1px solid rgba(0,0,0,0.2);">
					<button type="button" class="btn btn-default" style="border: 0 !important;background: transparent;font-weight: 800"><i class="fa fa-folder-open"></i> Browse</button>
					<input style="position: absolute;width: 100%;height: 100%;left: 0;top: 0;opacity: 0;z-index: 99;cursor: pointer;" onchange="jQuery('#uppp_<?php echo $params['id'] ?>').val(jQuery(this).val().replace(/^.*[\\\/]/, ''));" type="file" class="upload"  name='upload[<?php echo $params['id'] ?>]' <?php echo required($params) ?> />
				</span>

				<input class="form-control" placeholder="<?= wplf_valueof($params, 'placeholder'); ?>" id="uppp_<?php echo $params['id'] ?>" style="background: #ffffff;border: 0 !important;box-shadow: none !important;" type="text" readonly="readonly">
			</div>
		<?php
        $field_html = ob_get_clean();
		return $field_html;
	}

	function print_value($data, $field_id, $entry_id, $form_id)
    {
        if ($data != '') {
            $ext = $data != '' && function_exists('mime_content_type' ) ? mime_content_type($data) : '';
            $data = "<a href='" . home_url("/?lfdl={$entry_id}|{$field_id}") . "'>Download ( $ext )</a>";
        } else {
            $data = "&mdash;";
        }
        return $data;
    }

	function process_field() {

	}

}

add_action("File_field_options", [new File(),  'file_field_settings'], 10, 3);
