<?php

use LiveForms\__\Session;

function LiveForms()
{
    global $live_forms;
    return $live_forms;
}


function required($reqparams)
{
    if (!isset($reqparams['required']))
        return '';

    $type = $reqparams['validation'];
    $msg = ($reqparams['reqmsg'] != '' ? $reqparams['reqmsg'] : 'Please fill out this field');

//	$patterns['numeric'] = '[0-9]';
//	$patterns['email'] = '*@-.-';
//	$patterns['url'] = 'https?://.+';
//	$patterns['creditcard'] = '[0-9]{13,16}';
//	$patterns['text'] = '*[a-zA-Z0-9-_.';
    $str = " required='required' data-rule-{$type}='true' data-msg-required='".esc_attr($msg)."' ";
    return $str;
}

function is_valid_email($email, $skipDNS = true)
{
    $isValid = true;
    if (!is_string($email))
        return false;
    $atIndex = strrpos($email, "@");
    if (is_bool($atIndex) && !$atIndex) {
        $isValid = false;
    } else {
        $domain = substr($email, $atIndex + 1);
        $local = substr($email, 0, $atIndex);
        $localLen = strlen($local);
        $domainLen = strlen($domain);
        if ($localLen < 1 || $localLen > 64) {
            // local part length exceeded
            $isValid = false;
        } else if ($domainLen < 1 || $domainLen > 255) {
            // domain part length exceeded
            $isValid = false;
        } else if ($local[0] == '.' || $local[$localLen - 1] == '.') {
            // local part starts or ends with '.'
            $isValid = false;
        } else if (preg_match('/\\.\\./', $local)) {
            // local part has two consecutive dots
            $isValid = false;
        } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
            // character not valid in domain part
            $isValid = false;
        } else if (preg_match('/\\.\\./', $domain)) {
            // domain part has two consecutive dots
            $isValid = false;
        } else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
            // character not valid in local part unless
            // local part is quoted
            if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
                $isValid = false;
            }
        }

        if (!$skipDNS) {
            if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
                // domain not found in DNS
                $isValid = false;
            }
        }
    }
    return $isValid;
}

function is_pro()
{
    if(substr_count(wplf_valueof($_SERVER, 'HTTP_HOST'), 'wpliveforms.com')) return true;

    $is_pro = get_option('__wplf_pro');
    $is_pro = $is_pro ? json_decode($is_pro) : false;
    if ($is_pro && is_object($is_pro)) {
        if (($is_pro->expire_date < time() && !get_option("__lf_rev", false)) || wplf_check_again()) {
            $validated = wplf_validate_license(get_option('__wplf_license_key'));
            if (!$validated)
                update_option("__lf_rev", 1);
            else {
                delete_option("__lf_rev");
                $is_pro = get_option('__wplf_pro');
                $is_pro = $is_pro ? json_decode($is_pro) : false;
            }

        }
        if (is_object($is_pro) && $is_pro->status === 'VALID') {
            return true;
        }
    }
    return false;
}

function ph($str)
{
    return "placeholder='{$str}' ";
}

/**
 *
 * @param type $formsetting Contains form data and fields info. Basically a definition of the form's structure.
 *                                - Which field takes which type of input
 *                                - If a field is required
 *                                - What message to show if not filled
 *                                - Field label
 * @param type $field_defs Predefined structure of each of the field types. Serves the HTML used to render each field
 *                                - Definition of field types
 *                                - HTML for field types
 * @return type
 *                array(
 *                    array( strings containing HTML of the form partitions, each partion in a separate index ),
 *                    array( strings conaining each breadcrumb for individual form parts )
 *                )
 */
function paginate_form($formsetting, $field_defs)
{
    $formsetting_raw = $formsetting;
    $forms_view = $formsetting_raw['form_layout'];
    $forms_pref = $formsetting_raw['fieldsinfo'];
    $commonfields = $field_defs['fields_common'];
    $generic_fields = $field_defs['fields_generic'];
    $advanced_fields = $field_defs['fields_advanced'];
    $form_parts_html = array();
    $part_number = 0;
    $part_html = "<div class='tab-pane active' id='form_part_{$part_number}'>";
    $form_parts_names = array("form_part_{$part_number}" => 'Start');
    foreach ($forms_view as $id => $type) {

        $cur_pref = $forms_pref[$id];
        $cur_pref['id'] = $id;
        if ($type == 'Pageseparator') {
            $prev_part = $part_number - 1;
            $tmp_obj = new $type();
            $part_name = $tmp_obj->field_render_html($cur_pref);
            $parent_part = $part_number;

            $part_number++;

            $back_button_html = "<a id='goto_part_{$prev_part}' data-parent='form_part_{$parent_part}' data-next='form_part_{$prev_part}' class='btn btn-{$formsetting_raw['buttoncolor']} {$formsetting_raw['buttonsize']} pull-left change-part'><i class='fa fa-long-arrow-left'></i>&nbsp; Back</a>";
            $next_button_html = "<a id='goto_part_{$part_number}' data-parent='form_part_{$parent_part}' data-next='form_part_{$part_number}' class='btn btn-{$formsetting_raw['buttoncolor']} {$formsetting_raw['buttonsize']} pull-right change-part'>Next &nbsp;<i class='fa fa-long-arrow-right'></i></a>";
            $change_part_button_html = ($prev_part < 0 ? "<div class='col-md-12'>" : "<div class='col-md-6'>{$back_button_html}</div><div class='col-md-6'>") . $next_button_html . "</div>";
            $form_parts_html[] = $part_html . "<div class='row'>{$change_part_button_html}</div></div>"; // @todo: Change part button has to be added
            $part_html = "<div class='tab-pane' id='form_part_{$part_number}'>";
            if (empty($part_name))
                $part_name = 'Untitled';
            $form_parts_names["form_part_{$part_number}"] = $part_name;
            continue;
        }

        //if ($type != 'Password')
        //$part_html .= "<label for='field_' style='display: block;clear: both'>{$cur_pref['label']}</label>";
        if (!in_array($type, $advanced_fields) || is_pro()) {
            $tmp_obj = new $type();
            $cur_pref['form_id'] = $formsetting['form_id'];
            $part_html .= $tmp_obj->field_render_html($cur_pref);
        }

    }
    if (!empty($part_html)) {
        $prev_part = $part_number - 1;
        $parent_part = $part_number;
        $btnpos = wplf_valueof($formsetting_raw, 'buttonpos') === 'block' ? 'btn-block' : '';
        $back_button_html = "<a id='goto_part_{$prev_part}' data-parent='form_part_{$parent_part}' data-next='form_part_{$prev_part}' class='btn btn-{$formsetting_raw['buttoncolor']} {$formsetting_raw['buttonsize']} pull-left change-part'><i class='fa fa-long-arrow-left'></i>&nbsp; Back</a>";
        $submit_button_html = "<div class='{$formsetting_raw['buttonpos']}' ><button type='submit' id='submit' class='submit-btn btn btn-{$formsetting_raw['buttoncolor']} {$formsetting_raw['buttonsize']} {$btnpos}' data-parent='form_part_{$parent_part}'>" . ((isset($formsetting_raw['buttontext']) == false || $formsetting_raw['buttontext'] == '') ? "Submit" : $formsetting_raw['buttontext']) . "</button></div>";
        $submit_section_html = "<div class='row'>" . ($part_number > 0 ? "<div class='col-md-6'>{$back_button_html}</div><div class='col-md-6'>" : "<div class='col-md-12'>") . $submit_button_html . "</div></div>";
        $form_parts_html[] = $part_html . $submit_section_html . "</div>"; // @todo: Final submit button html has to be added
    }

    return array(
        'form_parts_html' => $form_parts_html,
        'form_parts_names' => $form_parts_names
    );
}

/**
 * Convet and object into an array (Recursively)
 * @param $object
 * @return array|mixed
 */
function make_array($object)
{
    if (!is_object($object) && !is_array($object)) {
        return $object;
    } else {
        $object = get_object_vars($object);
    }
    return array_map('make_array', $object);
}

function wplf_validate_license($licenseKey, $force = false)
{
    if(!$licenseKey) return false;

    if(!$force && time() < (int)get_option('__wplf_last_check') + 300) {
        $is_pro = get_option('__wplf_pro');
        $validity = false;
        if($is_pro) {
            $is_pro = json_decode( $is_pro );
            $validity = is_object($is_pro) && $is_pro->status === 'VALID';
        }
        return $validity;
    }

    $license_server = "https://wpliveforms.com/";
    $domain = $_SERVER['HTTP_HOST'];
    $productId = 'LIVEFORMS';
    $args = ['wpdmLicense' => 'validate', 'domain' => $domain, 'licenseKey' => $licenseKey];
    $request = array(
        'method' => 'POST',
        'timeout' => 45,
        'redirection' => 3,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => [],
        'body' => $args,
        'cookies' => array()
    );
    $response = wp_remote_post($license_server, $request);
    update_option("__wplf_last_check", time(), false);
    if (is_wp_error($response)) {
        Session::set("settings_error", "Invalid License Key!");
        return false;
    } else {
        $response = json_decode($response['body']);
        if ($response && $response->status === 'VALID') {
            update_option('__wplf_pro', json_encode($response));
            Session::set("settings_success", "Congratulation! Your Live Forms Pro license activated successfully.");
            return $licenseKey;
        } else {
            //delete_option('__wplf_pro');
            Session::set("settings_error", "Invalid License Key.");
            return false;
        }
    }
}

function wplf_check_again()
{
    $last_check = get_option('__wplf_last_check');
    if(!$last_check) return true;
    $check_period = 1296000;
    if(time() - $last_check > $check_period) return true;
    return false;
}

/**
 * Get the client's IP address
 *
 */
function get_client_ip()
{
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if (getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if (getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if (getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if (getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if (getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';

    return $ipaddress;
}

if (!function_exists('my_pagination')) :

    function my_pagination()
    {

        global $wp_query;

        $big = 999999999; // need an unlikely integer

        echo paginate_links(array(
            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $wp_query->max_num_pages
        ));
    }

endif;

function get_concatenated_string($var)
{
    if (is_object($var)) {
        $array = make_array($var);
    } else {
        $array = $var;
    }
    $string = implode(' ', $array);

    return $string;
}

if (!function_exists('add_url_fragment')) {
    function add_url_fragment($url, $fragments = array())
    {
        $fragments_str = '';
        if (is_array($fragments)) {
            foreach ($fragments as $frag_key => $frag_val) {
                $fragments_str .= ($frag_key . '=' . $frag_val . '&');
            }
            $fragments_str = trim($fragments_str, '&');
        } else {
            $fragments_str = $fragments;
        }

        if (strstr($url, '?')) {
            $url = $url . '&' . $fragments_str;
        } else {
            $url = $url . '?' . $fragments_str;
        }

        return str_replace('#', '', $url);
    }
}

function wplf_media_field($data)
{
    ob_start();
    ?>
    <div class="input-group">
        <input placeholder="<?php echo $data['placeholder']; ?>" type="url" name="<?php echo $data['name']; ?>"
               id="<?php echo isset($data['id']) ? $data['id'] : ($id = uniqid()); ?>" class="form-control"
               value="<?php echo isset($data['value']) ? $data['value'] : ''; ?>"/>
        <span class="input-group-btn">
                        <button class="btn btn-default btn-media-upload" type="button"
                                rel="#<?php echo isset($data['id']) ? $data['id'] : $id; ?>"><i
                                    class="fa fa-picture-o"></i></button>
                    </span>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * @usage Genrate option fields
 * @param $data
 * @return mixed|string
 */
function wplf_option_field($data, $fieldprefix)
{
    $desc = isset($data['description']) ? "<em class='note'>{$data['description']}</em>" : "";
    $class = isset($data['class']) ? $data['class'] : "";
    $data['placeholder'] = isset($data['placeholder']) ? $data['placeholder'] : '';
    switch ($data['type']):
        case 'text':
            return "<input type='text' name='{$fieldprefix}[{$data['name']}]' class='form-control {$class}' id='$data[id]' value='$data[value]' placeholder='{$data['placeholder']}'  />$desc";
            break;
        case 'select':
        case 'dropdown':
            $html = "<select name='{$fieldprefix}[{$data['name']}]'  id='{$data['id']}' class='form-control {$class}' style='width:100%;min-width:150px;' >";
            foreach ($data['options'] as $value => $label) {

                $html .= "<option value='{$value}' " . selected($data['selected'], $value, false) . ">$label</option>";
            }
            $html .= "</select>";
            return $html . $desc;
            break;
        case 'notice':
            return "<div class='alert alert-info' style='margin: 0'>$data[notice]</div>" . $desc;
        case 'textarea':
            return "<textarea name='{$fieldprefix}[{$data['name']}]' id='$data[id]' class='form-control {$class}' style='min-height: 100px'>$data[value]</textarea>$desc";
            break;
        case 'checkbox':
            return "<input type='hidden' name='{$fieldprefix}[{$data['name']}]' value='0' /><input type='checkbox' class='{$class}' name='$data[name]' id='$data[id]' value='$data[value]' " . checked($data['checked'], $data['value'], false) . " />" . $desc;
            break;
        case 'callback':
            return call_user_func($data['dom_callback'], $data['dom_callback_params']) . $desc;
            break;
        case 'heading':
            return "<h3>" . $data['label'] . "</h3>";
            break;
        case 'media':
            return wplf_media_field($data);
            break;
        default:
            return "<input type='{$data['type']}' name='{$fieldprefix}[{$data['name']}]' class='form-control {$class}' id='$data[id]' value='$data[value]' placeholder='{$data['placeholder']}'  />$desc";
            break;
    endswitch;
}

/**
 * @param $options
 * @return string
 */
function wplf_option_page($options, $fieldprefix = '')
{
    $html = "<div class='wpdm-settings-fields'>";
    foreach ($options as $id => $option) {
        $option['name'] = $id;
        if (!isset($option['id'])) $option['id'] = $id;
        if (in_array($option['type'], array('checkbox', 'radio')))
            $html .= "<div class='form-group'><label>" . wplf_option_field($option) . " {$option['label']}</label></div>";
        else if ($option['type'] == 'heading')
            $html .= "<h3>{$option['label']}</h3>";
        else
            $html .= "<div class='form-group'><label>{$option['label']}</label>" . wplf_option_field($option, $fieldprefix) . "</div>";
    }
    $html .= "</div>";
    return $html;
}


/**
 * @param $name
 * @param $options
 * @return string
 */
function wplf_settings_section($name, $options)
{
    return "<div class='panel panel-default'><div class='panel-heading'>{$name}</div><div class='panel-body'>" . wplf_option_page($options) . "</div></div>";
}


/**
 * @param $var
 * @param $index
 * @param array $params
 * @return array|bool|float|int|mixed|string|string[]|null
 */

function wplf_valueof($var, $index, $params = [])
{
    $index = explode("/", $index);
    $default = is_string($params) ? $params : '';
    if(is_object($var)) $var = (array)$var;
    $default = is_array($params) && isset($params['default']) ? $params['default'] : $default;
    if (count($index) > 1) {
        $val = $var;
        foreach ($index as $key) {
            $val = is_array($val) && isset($val[$key]) ? $val[$key] : '__not__set__';
            if ($val === '__not__set__') return $default;
        }
    } else
        $val = isset($var[$index[0]]) ? $var[$index[0]] : $default;

    if (is_array($params) && isset($params['validate'])) {
        if (!is_array($val) && $params['validate'] === 'array') return $default ? $default : [];
        else if (!is_array($val))
            $val = wplf_sanitize_var($val, $params['validate']);
        else
            $val = wplf_sanitize_array($val, $params['validate']);
    }

    return $val;
}

/**
 * @usage Validate and sanitize input data
 * @param $var
 * @param array $params
 * @return int|null|string
 */
function wplf_query_var($var, $params = array())
{
    $_var = explode("/", $var);
    if (count($_var) > 1) {
        $val = $_REQUEST;
        foreach ($_var as $key) {
            $val = is_array($val) && isset($val[$key]) ? $val[$key] : false;
        }
    } else
        $val = isset($_REQUEST[$var]) ? $_REQUEST[$var] : (isset($params['default']) ? $params['default'] : null);
    $validate = is_string($params) ? $params : '';
    $validate = is_array($params) && isset($params['validate']) ? $params['validate'] : $validate;

    if (!is_array($val))
        $val = wplf_sanitize_var($val, $validate);
    else
        $val = wplf_sanitize_array($val, $validate);

    return $val;
}

/**
 * Sanitize an array or any single value
 * @param $array
 * @return mixed
 */
function wplf_sanitize_array($array, $sanitize = 'kses')
{
    if (!is_array($array)) return esc_attr($array);
    foreach ($array as $key => &$value) {
        $validate = is_array($sanitize) && isset($sanitize[$key]) ? $sanitize[$key] : $sanitize;
        if (is_array($value))
            wplf_sanitize_array($value, $validate);
        else {
            $value = wplf_sanitize_var($value, $validate);
        }
        $array[$key] = &$value;
    }
    return $array;
}

/**
 * Sanitize any single value
 * @param $value
 * @return string
 */
function wplf_sanitize_var($value, $sanitize = 'kses')
{
    if (is_array($value))
        return wplf_sanitize_array($value, $sanitize);
    else {
        switch ($sanitize) {
            case 'int':
            case 'num':
                return (int)$value;
                break;
            case 'double':
            case 'float':
                return (double)($value);
                break;
            case 'txt':
            case 'str':
                $value = esc_attr($value);
                break;
            case 'kses':
                $allowedtags = wp_kses_allowed_html();
                $allowedtags['div'] = array('class' => true);
                $allowedtags['strong'] = array('class' => true);
                $allowedtags['b'] = array('class' => true);
                $allowedtags['i'] = array('class' => true);
                $allowedtags['img'] = array('class' => true);
                $allowedtags['a'] = array('class' => true, 'href' => true);
                $value = wp_kses($value, $allowedtags);
                break;
            case 'serverpath':
                $value = realpath($value);
                $value = str_replace("\\", "/", $value);
                break;
            case 'txts':
                $value = sanitize_textarea_field($value);
                break;
            case 'url':
                $value = esc_url($value);
                break;
            case 'noscript':
            case 'escs':
                $value = wplf_escs($value);
                break;
            case 'filename':
                $value = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '_', $value);
                $value = mb_ereg_replace("([\.]+)", '_', $value);
                break;
            case 'html':

                break;
            default:
                $value = esc_sql(esc_attr($value));
                break;
        }
        $value = wplf_escs($value);
    }
    return $value;
}

/**
 * @usage Escape script tag
 * @param $html
 * @return null|string|string[]
 */
function wplf_escs($html)
{
    return preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
}

/**
 * @return bool
 */
function wplf_is_ajax()
{
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        return true;
    return false;
}

/**
 * @param $total
 * @param $item_per_page
 * @param int $page
 * @param string $var
 * @return string
 */
function wplf_paginate_links($total, $items_per_page, $current_page = 1, $var = 'cp', $params = array())
{

    $pages = ceil($total / $items_per_page);
    $format = isset($params['format']) ? $params['format'] : "?{$var}=%#%";
    $args = array(
        //'base'               => '%_%',
        'format' => $format,
        'total' => $pages,
        'current' => $current_page,
        //'show_all'           => false,
        //'end_size'           => 2,
        //'mid_size'           => 1,
        //'prev_next'          => true,
        'prev_text' => isset($params['prev_text']) ? $params['prev_text'] : __('Previous'),
        'next_text' => isset($params['prev_text']) ? $params['next_text'] : __('Next'),
        'type' => 'array',
        //'add_args'           => false,
        //'add_fragment'       => '',
        //'before_page_number' => '',
        //'after_page_number'  => ''
    );
    //wpdmprecho($args);
    $pags = paginate_links($args);
    //wpdmprecho($pags);
    $phtml = "";
    if (is_array($pags)) {
        foreach ($pags as $pagl) {
            if (isset($params['container'])) {
                $pagl = str_replace("<a", "<a data-container='{$params['container']}'", $pagl);
            }
            $phtml .= "<li>{$pagl}</li>";
        }
    }
    $async = isset($params['async']) && $params['async'] ? ' async' : '';
    $phtml = "<div class='text-center'><ul class='pagination wpdm-pagination pagination-centered text-center{$async}'>{$phtml}</ul></div>";
    return $phtml;
}

function wplf_compile_email_message($template, $data)
{
    $data = apply_filters("wplf_email_template_tags", $data);
    return LiveForms()->template->fetch($template, $data);
}

function currencies() {
    $currencies_list = array (
        'USD' => 'United States Dollar',
        'EUR' => 'EURO',
        'ALL' => 'Albania Lek',
        'AFN' => 'Afghanistan Afghani',
        'ARS' => 'Argentina Peso',
        'AWG' => 'Aruba Guilder',
        'AUD' => 'Australia Dollar',
        'AZN' => 'Azerbaijan New Manat',
        'BSD' => 'Bahamas Dollar',
        'BBD' => 'Barbados Dollar',
        'BDT' => 'Bangladeshi taka',
        'BYR' => 'Belarus Ruble',
        'BZD' => 'Belize Dollar',
        'BMD' => 'Bermuda Dollar',
        'BOB' => 'Bolivia Boliviano',
        'BAM' => 'Bosnia and Herzegovina Convertible Marka',
        'BWP' => 'Botswana Pula',
        'BGN' => 'Bulgaria Lev',
        'BRL' => 'Brazil Real',
        'BND' => 'Brunei Darussalam Dollar',
        'KHR' => 'Cambodia Riel',
        'CAD' => 'Canada Dollar',
        'KYD' => 'Cayman Islands Dollar',
        'CLP' => 'Chile Peso',
        'CNY' => 'China Yuan Renminbi',
        'COP' => 'Colombia Peso',
        'CRC' => 'Costa Rica Colon',
        'HRK' => 'Croatia Kuna',
        'CUP' => 'Cuba Peso',
        'CZK' => 'Czech Republic Koruna',
        'DKK' => 'Denmark Krone',
        'DOP' => 'Dominican Republic Peso',
        'XCD' => 'East Caribbean Dollar',
        'EGP' => 'Egypt Pound',
        'SVC' => 'El Salvador Colon',
        'EEK' => 'Estonia Kroon',
        'FKP' => 'Falkland Islands (Malvinas) Pound',
        'FJD' => 'Fiji Dollar',
        'GHC' => 'Ghana Cedis',
        'GIP' => 'Gibraltar Pound',
        'GTQ' => 'Guatemala Quetzal',
        'GGP' => 'Guernsey Pound',
        'GYD' => 'Guyana Dollar',
        'HNL' => 'Honduras Lempira',
        'HKD' => 'Hong Kong Dollar',
        'HUF' => 'Hungary Forint',
        'ISK' => 'Iceland Krona',
        'INR' => 'India Rupee',
        'IDR' => 'Indonesia Rupiah',
        'IRR' => 'Iran Rial',
        'IMP' => 'Isle of Man Pound',
        'ILS' => 'Israel Shekel',
        'JMD' => 'Jamaica Dollar',
        'JPY' => 'Japan Yen',
        'JEP' => 'Jersey Pound',
        'KZT' => 'Kazakhstan Tenge',
        'KPW' => 'Korea (North) Won',
        'KRW' => 'Korea (South) Won',
        'KGS' => 'Kyrgyzstan Som',
        'LAK' => 'Laos Kip',
        'LVL' => 'Latvia Lat',
        'LBP' => 'Lebanon Pound',
        'LRD' => 'Liberia Dollar',
        'LTL' => 'Lithuania Litas',
        'MKD' => 'Macedonia Denar',
        'MYR' => 'Malaysia Ringgit',
        'MUR' => 'Mauritius Rupee',
        'MXN' => 'Mexico Peso',
        'MNT' => 'Mongolia Tughrik',
        'MZN' => 'Mozambique Metical',
        'NAD' => 'Namibia Dollar',
        'NPR' => 'Nepal Rupee',
        'ANG' => 'Netherlands Antilles Guilder',
        'NZD' => 'New Zealand Dollar',
        'NIO' => 'Nicaragua Cordoba',
        'NGN' => 'Nigeria Naira',
        'NOK' => 'Norway Krone',
        'OMR' => 'Oman Rial',
        'PKR' => 'Pakistan Rupee',
        'PAB' => 'Panama Balboa',
        'PYG' => 'Paraguay Guarani',
        'PEN' => 'Peru Nuevo Sol',
        'PHP' => 'Philippines Peso',
        'PLN' => 'Poland Zloty',
        'QAR' => 'Qatar Riyal',
        'RON' => 'Romania New Leu',
        'RUB' => 'Russia Ruble',
        'SHP' => 'Saint Helena Pound',
        'SAR' => 'Saudi Arabia Riyal',
        'RSD' => 'Serbia Dinar',
        'SCR' => 'Seychelles Rupee',
        'SGD' => 'Singapore Dollar',
        'SBD' => 'Solomon Islands Dollar',
        'SOS' => 'Somalia Shilling',
        'ZAR' => 'South Africa Rand',
        'LKR' => 'Sri Lanka Rupee',
        'SEK' => 'Sweden Krona',
        'CHF' => 'Switzerland Franc',
        'SRD' => 'Suriname Dollar',
        'SYP' => 'Syria Pound',
        'TWD' => 'Taiwan New Dollar',
        'THB' => 'Thailand Baht',
        'TTD' => 'Trinidad and Tobago Dollar',
        'TRY' => 'Turkey Lira',
        'TRL' => 'Turkey Lira',
        'TVD' => 'Tuvalu Dollar',
        'UAH' => 'Ukraine Hryvna',
        'GBP' => 'United Kingdom Pound',
        'UYU' => 'Uruguay Peso',
        'UZS' => 'Uzbekistan Som',
        'VEF' => 'Venezuela Bolivar',
        'VND' => 'Viet Nam Dong',
        'YER' => 'Yemen Rial',
        'ZWD' => 'Zimbabwe Dollar'
    );
    return $currencies_list;
}


/**dev fns**/
function lfprecho($data, $echo = true)
{
    $data = "<pre>" . print_r($data, 1) . "</pre>";
    if (!$echo) return $data;
    echo $data;
}
