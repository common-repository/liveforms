<?php


namespace LiveForms\Form;

if(!defined("ABSPATH")) die("Shit happens!");

use LiveForms\__\__;

class Field
{

    static function hidden($attrs)
    {
        $_attrs = "";
        foreach ($attrs as $key => $value) {
            $_attrs .= "{$key}='{$value}' ";
        }
        $text = "<input type='hidden' $_attrs />";
        return $text;
    }

    static function text($attrs)
    {
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "form-control " . $attrs['class'] : "form-control";
        foreach ($attrs as $key => $value) {
            $_attrs .= "{$key}='{$value}' ";
        }
        $text = "<input type='text' $_attrs />";
        return $text;
    }

    static function textarea($attrs, $value = '')
    {
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "form-control " . $attrs['class'] : "form-control";
        foreach ($attrs as $key => $_value) {
            $_attrs .= "{$key}='{$_value}' ";
        }
        $value = stripslashes_deep($value);
        $text = "<textarea $_attrs>{$value}</textarea>";
        return $text;
    }

    static function number($attrs)
    {
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "form-control " . $attrs['class'] : "form-control";
        foreach ($attrs as $key => $value) {
            $_attrs .= "{$key}='{$value}' ";
        }
        $text = "<input type='number' $_attrs />";
        return $text;
    }

    static function email($attrs)
    {
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "form-control " . $attrs['class'] : "form-control";
        foreach ($attrs as $key => $value) {
            $_attrs .= "{$key}='{$value}' ";
        }
        return "<input type='email' $_attrs />";
    }

    static function password($attrs)
    {
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "form-control " . $attrs['class'] : "form-control";
        foreach ($attrs as $key => $value) {
            $_attrs .= "{$key}='{$value}' ";
        }
        return "<input type='password' $_attrs />";
    }

    static function checkbox($attrs)
    {
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "wplf-checkbox-toggle " . $attrs['class'] : "wplf-checkbox-toggle";
        $title = __::valueof($attrs, 'title');
        if (isset($attrs['title'])) unset($attrs['title']);
        if ("" . __::valueof($attrs, 'value') === "" . __::valueof($attrs, 'checked')) $attrs['checked'] = "checked";
        else if (isset($attrs['checked'])) unset($attrs['checked']);
        foreach ($attrs as $key => $value) {
            $_attrs .= "{$key}='{$value}' ";
        }
        return "<label><input type='checkbox' $_attrs /> {$title}</label>";
    }

    static function select($attrs, $value = '')
    {
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "form-control " . $attrs['class'] : "form-control";
        $options = $attrs['options'];
        unset($attrs['options']);
        foreach ($attrs as $key => $_value) {
            $_attrs .= "{$key}='{$_value}' ";
        }
        $_options = "";
        foreach ($options as $_value => $label) {
            $_options .= "<option value='{$_value}' ".selected($_value, $value, false).">{$label}</option>\r\n";
        }
        return "<select $_attrs>\r\n{$_options}\r\n</select>";
    }

    static function meidapicker($attrs, $value = '')
    {
        ob_start();
        $_attrs = '';
        if(is_array($attrs)) {
            foreach ($attrs as $attr => $value){
                $_attrs .= "$attr='$value' ";
            }
        }
        ?>
        <div class="input-group">
            <input <?php echo $_attrs; ?> type="url" value="<?php echo $value; ?>"/>
            <span class="input-group-append">
                <button class="btn btn-secondary btn-media-upload" type="button" rel="#<?php echo $attrs['id']; ?>"><i class="far fa-image"></i></button>
            </span>
        </div>
        <?php
        return ob_get_clean();
    }

    static function custom($field, $attrs)
    {
        return call_user_func($field, $attrs);
    }

}