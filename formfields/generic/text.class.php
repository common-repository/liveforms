<?php

use LiveForms\__\__;

class Text extends Field
{

    public function field_id()
    {
        return get_class($this);
    }

    public function control_button()
    {
        ob_start();
        ?>
        <li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="Text">
            <span class="lfi lfi-name"><i class="fa fa-minus"></i></span> Text
            <a title="Text" rel="Text" class="add pull-right add-form-field" data-template='Text' href="#"><i
                        class="fas fa-plus-circle ttipf" title=""></i></a>
        </li>
        <?php
        $control_button_html = ob_get_clean();
        return $control_button_html;
    }

    public function field_preview_html($fieldindex, $fieldid, $field_infos)
    {
        ob_start();
        $preview = "<input type='text' disabled='disabled' placeholder='".wplf_valueof($field_infos, "placeholder")."' name='submitform[]' class='form-control' value='' />";
        include LF_BASE_DIR . 'views/field-settings/field-preview.php';
        $field_render_html = ob_get_clean();
        return $field_render_html;
    }

    function field_html($params = array())
    {
        $cssClass = wplf_valueof($params, 'cssclass', ['default' => 'form-control']);
        $required = required($params);
        $default_value = __::valueof($params, 'default_value');
        $default_value = $this->parse_var($default_value, $params);
        $params['attributes']['value'] = $default_value;
        return "<input type='text' name='submitform[{$params['id']}]' ".$this->attributes($params['attributes'])." placeholder='".wplf_valueof($params, "placeholder")."' class='{$cssClass}' {$required} />";
    }

    function validate_field($field_id, $value, $field_info)
    {
        $validation = wplf_valueof($field_info, 'validation');

        if($validation === 'remote')
            return $this->validate_remote($field_info['datasource'], $value, $field_info);
        if($validation === 'predef')
            return $this->validate_predef($field_info['dataset'], $value, $field_info);

        return true;
    }

}
