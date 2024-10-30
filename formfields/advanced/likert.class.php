<?php
class Likert extends Field {

    public function field_id(){
        return get_class($this);
    }

    public function control_button() {
		ob_start();
		?>
		<li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="Likert">
			<span class="lfi lfi-name"><i class="fas fa-tasks"></i></span> Likert
			<a title="Likert" rel="Likert" class="add pull-right add-form-field" data-template='Likert' href="#"><i class="fas fa-plus-circle ttipf" title=""></i></a>
		</li>
	    <?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}

    public function field_preview_html($fieldindex, $fieldid, $field_infos) {
        $preview = "<img src='".LF_ASSET_URL."images/likert.jpg' alt='Likert Preview' style='width: 400px;max-width:100%' />";
        ob_start();
        include LF_BASE_DIR.'views/field-settings/field-preview.php';
        $field_render_html = ob_get_clean();
        return $field_render_html;
    }

	public function field_html($params = array()) {
		$option_rows = isset($params['options']['row']) ? $params['options']['row'] : array();
        $option_columns = isset($params['options']['column']) ? $params['options']['column'] : array();
        $style = wplf_valueof($params, 'style', ['default' => 'pos-h']);
		ob_start();
        ?>
			<div class='likert <?php echo $style; ?>'>
                <table class="table table-striped likert">
                    <thead>
                    <tr>
                        <th></th>
                        <?php
                        foreach ($option_columns as  $column) echo "<th>{$column}</th>";
                        ?>
                    </tr>
                    </thead>
			<?php
			foreach ($option_rows as  $row) {
			?>
                    <tr>
                        <th><?=$row?></th>
                        <?php
                        foreach ($option_columns as  $column) echo "<td><input type='radio' name='submitform[{$params['id']}][{$row}]' value='{$column}' /></td>";
                        ?>
                    </tr>
			<?php
			}
			?>
                </table>
			</div>
		<?php
		$field_html = ob_get_clean();
		return $field_html;

	}

    public function likert_field_settings($fieldindex, $fieldid, $field_infos){
        include LF_BASE_DIR.'views/field-settings/likert-options.php';
    }

    function print_value($data, $field_id, $entry_id, $form_id)
    {
        $values = [];
        foreach ($data as $key => $value) {
            $values[] = "{$key} = {$value}";
        }
        $values = implode(", ", $values);
        return $values;
    }

	function process_field() {

	}

}


add_action("Likert_field_options", [new Likert(),  'likert_field_settings'], 10, 3);

