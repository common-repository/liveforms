<?php

use LiveForms\__\__;

abstract class Field
{

    public $default_options = true;
    public $conditional_logic = true;
    public $required_option = true;
    public $multiple_values = false;
    public $field_control_class = 'list-group-item wplf-form-field';
    public $validation_error = '';

    abstract function control_button();

    abstract function field_preview_html($fieldindex, $fieldid, $field_infos);

    abstract function field_id();

    function field_html($params = array())
    {
        return "";
    }

    function field_render_html($params = array())
    {
        ob_start();
        $condition_fields = '';
        $cond_action = '';
        $cond_boolean = '';
        if (isset($params['condition']) and isset($params['conditioned'])) {
            $cond_boolean = $params['condition']['boolean_op'];
            $cond_action = $params['condition']['action'];
            if(is_array($params['condition']['field'])) {
                foreach ($params['condition']['field'] as $key => $value) {
                    $field_id = $value;
                    $field_op = $params['condition']['op'][$key];
                    $field_value = $params['condition']['value'][$key];
                    $condition_fields .= ($field_id . ':' . $field_op . ':' . $field_value . '|');
                }
            }
            $condition_fields = rtrim($condition_fields, '|');
        }
        $cssClass = wplf_valueof($params, 'cssclass', ['default' => 'form-control']);
        $required = required($params);
        //$field_html = "<input type='text' name='submitform[{$params['id']}]' class='{$cssClass}' {$required} />";
        include LF_BASE_DIR . 'views/form/field.php';
        $field_render_html = ob_get_clean();
        return $field_render_html;
    }

    public function field_settings($fieldindex, $fieldid, $field_infos)
    {
        ob_start();
        ?>
        <div class="cog" id="cog_<?php echo $fieldindex; ?>" style='display: none'>

            <?php include LF_BASE_DIR . 'views/field-settings/general-options.php'; ?>
            <?php
            if ($this->multiple_values)
                include LF_BASE_DIR . 'views/field-settings/multiple-choices.php';
            ?>
            <?php include LF_BASE_DIR . 'views/field-settings/conditional-logic.php'; ?>
            <?php include LF_BASE_DIR . 'views/field-settings/field-required.php'; ?>

            <?php do_action("form_field_" . $this->field_id() . "_settings", $fieldindex, $fieldid, $field_infos); ?>
            <?php do_action("form_field_settings", $fieldindex, $fieldid, $field_infos); ?>

        </div>
        <?php
        $field_settings_html = ob_get_clean();
        return $field_settings_html;
    }

    public function configuration_template()
    {
        ob_start();
        ?>
        <script type="text/x-mustache" id="template-<?php echo $this->field_id(); ?>-settings">
             <?php echo $this->field_settings("{{ID}}", $this->field_id(), []) ?>


        </script>
        <script type="text/x-mustache" id="template-<?php echo $this->field_id(); ?>">
            <?php echo $this->field_preview_html("{{ID}}", $this->field_id(), []) ?>


        </script>
        <?php
        $field_configuration_template = ob_get_clean();
        return $field_configuration_template;
    }

    public function parse_var($value, $params)
    {
        global $current_user;
        $logged_in = is_user_logged_in();
        switch (true) {
            case '{{ID}}' === $value:
                $value = isset($_REQUEST['__wpdmlo']) ? (int)$_REQUEST['__wpdmlo'] : get_the_ID();
                break;
            case '{{title}}' === $value:
                $value = esc_attr(get_the_title((isset($_REQUEST['__wpdmlo']) ? (int)$_REQUEST['__wpdmlo'] : get_the_ID())));
                break;
            case '{{TIMESTAMP}}' === $value:
                $value = time();
                break;
            case '{{DATE}}' === $value:
                $value = wp_date(get_option('date_format'));
                break;
            case '{{USER_ID}}' === $value:
                $value = get_current_user_id();
                break;
            case '{{USER_EMAIL}}' === $value:
                $value = $logged_in ? $current_user->user_email : '';
                break;
            case '{{CIENT_IP}}' === $value:
                $value = $_SERVER['REMOTE_ADDR'];
                break;
            case preg_match('/\{\{USER_(.*)\}\}/', $value, $match):
                if($logged_in) {
                    $_field = wplf_valueof($match, 1);
                    if ($_field && isset($current_user->{$_field})) {
                        $value = $current_user->{$_field};
                    }
                }
                break;
            case preg_match('/\{\{USERMETA_(.*)\}\}/', $value, $match):
                if($logged_in) {
                    $_field = wplf_valueof($match, 1);
                    if ($_field) {
                        $value = get_user_meta(get_current_user_id(), $_field, true);
                        $value = is_array($value) ? implode(", ", $value) : $value;
                    }
                }
                break;
            case preg_match('/\{\{POSTMETA_(.*)\}\}/', $value, $match):
                if($logged_in) {
                    $_field = wplf_valueof($match, 1);
                    if ($_field) {
                        $value = get_post_meta(get_the_ID(), $_field, true);
                        $value = is_array($value) ? implode(", ", $value) : $value;
                    }
                }
                break;
            case preg_match('/\{\{REQUEST_(.*)\}\}/i', $value, $match):
                $_field = wplf_valueof($match, 1);
                if ($_field)
                    $value = wplf_valueof($_REQUEST, $_field, ['validate' => 'txt']);
                break;
            default:
                break;

        }
        $value = apply_filters('liveforms_field_value', $value, $params);
        return $value;
    }

    function attributes($attrs)
    {
        $_attrs = "";
        foreach ($attrs as $name => $value) {
            $name = wplf_sanitize_var($name, 'txt');
            $value = wplf_sanitize_var($value, 'txt');
            $_attrs .= " {$name} = '{$value}' ";
        }
        return $_attrs;
    }

    function print_value($data, $field_id, $entry_id, $form_id)
    {
        $data = is_array($data) ? implode(", ", $data) : $data;
        $value = esc_attr($data);
        return $value;
    }

    public function process_field()
    {

    }

    function validate_remote($url, $value)
    {
        $parameters = apply_filters("liveforms_remote_data_validation_params", [ 'data' => $value ], $url);
        $response = __::remote_post($url, $parameters);
        if(!$response->success) $this->validation_error = $response->message;
        return $response->success;
    }

    function validate_predef($dataset, $value, $field_info)
    {
        $dataset = str_replace("\r", "", $dataset);
        $dataset = explode("\n", $dataset);
        $valid = in_array($value, $dataset);
        if(!$valid) $this->validation_error = "Invalid input value for field <em>{$field_info['label']}</em>";
        return $valid;
    }
}
