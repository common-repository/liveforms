<?php
class Address extends Field {

    public function field_id(){
        return get_class($this);
    }

	public function control_button() {
		ob_start();
		?>
		<li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="Address">
			<span class="lfi lfi-name"><i class="fas fa-map-marker-alt"></i></span> Address
			<a title="<?php echo __CLASS__ ?>" rel="<?php echo __CLASS__ ?>" class="add pull-right add-form-field" data-template='Address' href="#"><i class="fas fa-plus-circle ttipf" title=""></i></a>
	</li>
	    <?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}

	public function field_preview_html($fieldindex, $fieldid, $field_infos) {
		ob_start();
		?>
		<div class='address'>
			<div class='form-group'>

					<input disabled='disabled' class='form-control row-bottom-buffer' type='text' name='submitform[][address1]' id='street1' placeholder="Address 1" />

			</div>
			<div class='form-group'>

					<input disabled='disabled' class='form-control row-bottom-buffer' type='text' name='submitform[][address2]' id='street2' placeholder="Address 2" />

			</div>
			<div class='form-group'>

					<input disabled='disabled' class='form-control row-bottom-buffer' type='text' name='submitform[][city]' id='city' placeholder="City" />

			</div>
			<div class='form-group row'>
				<div class='col-md-6'>
					<select data-placeholder='Choose a country' disabled='disabled' style='width: 100%' class='form-control' id='_selector_country' name='submitform[][country]' >
						<option value='none'>Choose a country</option>
					</select>
				</div>
				<div class='col-md-6'>
					<select data-placeholder='Choose a state' disabled='disabled' style='width: 100%' class='form-control' id='_selector_state' name='submitform[][state]' >
					<option value='none'>Choose a state</option>
					</select>
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

	public function field_render_html($params = array()) {
		ob_start();

		$locations_file = file_get_contents(LF_BASE_DIR . '/assets/data/locations.json');
		$locations_array = json_decode($locations_file, true);
		$countries = $locations_array['countries'];
		$states = $locations_array['states'];

		$all_countries = array();
		foreach($countries as $region => $reg_conts) {
			$all_countries = array_merge($all_countries, $reg_conts);
		}

		$countries = json_encode($locations_array['countries']);
		$states = json_encode($locations_array['states']);

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
			<label style='display: block; clear: both'><?php echo $params['label'] ?><span class="note"><?php echo $params['note']; ?></span></label>
			<div class='address'>
				<div class='form-group'>

						<input class='form-control row-bottom-buffer' type='text' name='submitform[<?php echo $params['id'] ?>][address1]' id='street1' placeholder="Address 1" <?php echo required($params) ?>/>

				</div>
				<div class='form-group'>

						<input class='form-control row-bottom-buffer' type='text' name='submitform[<?php echo $params['id'] ?>][address2]' id='street2' placeholder="Address 2"  <?php echo required($params) ?> />

				</div>
				<div class='form-group'>

						<input class='form-control row-bottom-buffer' type='text' name='submitform[<?php echo $params['id'] ?>][city]' id='city' placeholder="City" <?php echo required($params) ?> />

				</div>
				<div class='form-group row'>
					<div class='col-md-6'>
						<select data-placeholder='Choose a country' style='width: 100%' class='form-control wplf-custom-select' id='<?php echo $params['id']?>_selector_country' name='submitform[<?php echo $params['id'] ?>][country]' <?php echo required($params) ?>>
							<option value='none'><?php _e('Choose a country', LF_TEXT_DOMAIN); ?></option>
							<?php
							foreach($all_countries as $country) { ?>
							<option value='<?php echo $country ?>'><?php echo $country ?></option>
							<?php } ?>
						</select>
					</div>
					<div class='col-md-6'>
						<select data-placeholder='Choose a state' style='width: 100%' class='form-control wplf-custom-select' id='<?php echo $params['id'] ?>_selector_state' name='submitform[<?php echo $params['id'] ?>][state]' <?php required($params) ?>>
						<option value='none'>Choose a state</option>
						</select>
					</div>
				</div>
				<div class='hidden' style="display:none;" id='liveform_json_countries'><?php echo $countries ?></div>
				<script type='text/javascript'>
					jQuery(function ($){
                        $('#<?php echo $params['id'] ?>_selector_country').on('change',function(){
                            var sel_country = $(this).val();
                            var json_state = <?= $states ?>;
                            json_state['none'] = [];
                            $('#<?php echo $params['id']?>_selector_state').html(get_selections(json_state[sel_country]));
                            <?php if((int)get_option('__wplf_chosen_js', 0) === 1){ ?>
                            $('#<?php echo $params['id']?>_selector_state').trigger("chosen:updated");
                            <?php } ?>
                        });
                        function get_selections(states) {
                            options_html = '<option selected=\'selected\' value=\'\'>Choose a state</option>';
                            for (i = 0 ; i<states.length ; i++) {
                                options_html += ('<option value=\''+states[i]+'\'>'+states[i]+'</option>')
                            };
                            return options_html;
                        };
                    });
				</script>
			</div>
		</div>
		<?php
		$field_render_html = ob_get_clean();
		return $field_render_html;
	}

	function process_field() {

	}

}
