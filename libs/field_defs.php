<?php

function set_commonfields()
{

    $fields = ['Name', 'Email', 'Subject', 'Message'];

    return $fields;
}

add_filter("common_fields", "set_commonfields");

function set_genericfields()
{
    return ['Text', 'Textarea', 'Number', 'Radio', 'Select', 'Checkbox', 'Date', 'Time'];

}

add_filter("generic_fields", "set_genericfields");

function set_advancedfields()
{
    return ['File', 'FullName', 'Hidden', 'Password', 'Address', 'Url', 'Paratext', 'Phone', 'PaymentMethods', 'Range', 'ReCaptcha', 'Captcha', 'Signature', 'Rating', 'Likert', 'Space', 'Equation'];
}

add_filter("advanced_fields", "set_advancedfields");

function set_customfields()
{
    return [];
}

add_filter("custom_fields", "set_customfields");


function get_validation_ops($type = '')
{
    $default_validation_ops = array(
        'url' => array('url' => 'URL', 'fields' => ['*']),
        'email' => array('email' => 'Email', 'fields' => ['Text', 'Email']),
        'date' => array('date' => 'Date', 'fields' => ['Text']),
        'text' => array('text' => 'Text', 'fields' => ['*']),
        'numeric' => array('numeric' => 'Numeric', 'fields' => ['*']),
        'remote' => array('remote' => 'Remote Source', 'fields' => ['Text', 'Number', 'Textarea']),
        'predef' => array('predef' => 'Valid Dataset', 'fields' => ['Text', 'Number', 'Textarea'])
    );

    //$default_validation_ops = array('text' => 'Text', 'numeric' => 'Numeric', 'email' => 'Email', 'url' => 'URL', 'date' => 'Date', 'remo');
    if ($type == '') $validation_ops = $default_validation_ops;
    else {
        $validation_ops = $default_validation_ops[$type];
    }

    return $validation_ops;
}

