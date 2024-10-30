<?php

namespace LiveForms\Settings;

use LiveForms\Form\Form;

if(!defined("ABSPATH")) die("Shit happens!");


class License extends Section
{

    public $disabled_styles;
    public $query_status_page;
    public $chosen_js;

    function __construct()
    {
        $this->section = 'license';
        $this->title = __('License', LF_TEXT_DOMAIN);
        $this->icon = 'fa fa-key';
        $this->settings_groups = [
            'wplf_lic_settings'      => __('License Key', LF_TEXT_DOMAIN)
        ];
        $this->success_msg = esc_attr__( 'Congratulations! Your license key is validated successfully.', LF_TEXT_DOMAIN );
        $this->settings_fields['__wplf_license_key'] = ['group' => 'wplf_lic_settings', 'validate_callback' => [$this, 'validateKey'], 'type' => 'text', 'label' => '', 'note' => __("Please enter your license key", "liveforms"), 'attrs' => ['placeholder' => __("License Key", "liveforms"), 'name' => '__wplf_license_key', 'id' => 'license_key', 'class' => 'form-control input-lg', 'value' => get_option('__wplf_license_key', '')]];

    }

    function validateKey($key)
    {
        $ret = wplf_validate_license($key);
        if(!$ret)
            return  new \WP_Error( 'invalidkey', __( "Invalid or expired license key!", "liveforms" ) );
        else
            return $key;
    }
}