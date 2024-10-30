<?php
class FullName extends Field {

    public function field_id(){
        return get_class($this);
    }

	public function control_button() {
		ob_start();
		?>
		<li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="FullName">
			<span class="lfi lfi-name"><i class="fas fa-user-circle"></i></span> Full Name
			<a title="FullName" rel="FullName" class="add pull-right add-form-field" data-template='FullName' href="#"><i class="fas fa-plus-circle ttipf" title=""></i></a>
	    </li>
	    <?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}

	public function field_preview_html($fieldindex, $fieldid, $field_infos) {
		ob_start();
		?>
		<div class='form-group'>
			<div class='row'>
				<div class='col-md-1'>
					<select disabled="disabled" placeholder="Mr./Mrs." class='form-control' type='text' name='submitform[][title]' id='title'>
                        <option>Mr.</option>
                    </select>
                    <em>Title</em>
				</div>
				<div class="col-md-6">
					<input disabled="disabled" class='form-control' type='text' name='submitform[][first_name]' id='first_name' />
                    <em>First name</em>
				</div>
				<div class='col-md-5'>
					<input disabled="disabled" class='form-control' type='text' name='submitform[][last_name]' id='last_name' />
                    <em>Last name</em>
				</div>
			</div>
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
        $titles = trim(wplf_valueof($params, 'person_title'));
        $titles = str_replace("\r", "", $titles);
        $titles = explode("\n", $titles);
        $default_value = \LiveForms\__\__::valueof($params, 'default_value');
        $default_value = $this->parse_var($default_value, $params);
        $default_value = explode(" ", $default_value);
        ?>

				<div style="display: flex">
                    <?php if($titles[0] !== '') { ?>
					<div style="width: 110px;margin-right: 10px">
						<select class='form-control wplf-custom-select ncs' placeholder="Er." type='text' name='submitform[<?php echo $params['id'] ?>][title]' id='title' <?php echo required($params) ?>>
                            <?php foreach ($titles as $title){ ?>
                            <option value="<?=$title?>"><?=$title?></option>
                            <?php } ?>
                        </select>
                        <em>Title</em>
					</div>
                    <?php } ?>
					<div  style="width: 50%;margin-right: 10px">
						<input class='form-control' type='text' value="<?= wplf_valueof($default_value, 0); ?>" name='submitform[<?php echo $params['id'] ?>][first_name]' id='first_name' <?php echo required($params) ?> />
                        <em class="note">First name</em>
					</div>
					<div  style="width: 50%">
						<input class='form-control' type='text' value="<?= wplf_valueof($default_value, 1); ?>" name='submitform[<?php echo $params['id'] ?>][last_name]' id='last_name' <?php echo required($params) ?> />
                        <em class="note">Last name</em>
					</div>
				</div>


		<?php
		$field_render_html = ob_get_clean();
		return $field_render_html;
	}

    public function fullname_field_settings($fieldindex, $fieldid, $field_infos){
        include LF_BASE_DIR.'views/field-settings/fullname-options.php';
    }

	function process_field() {

	}

}

add_action("FullName_field_options", [new FullName(),  'fullname_field_settings'], 10, 3);
