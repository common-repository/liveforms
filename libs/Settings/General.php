<?php

namespace LiveForms\Settings;

use LiveForms\__\__;
use LiveForms\Form\Form;

if(!defined("ABSPATH")) die("Shit happens!");


class General extends Section
{

    public $disabled_styles;
    public $query_status_page;
    public $chosen_js;
    public $email_template = 'default';
    public $api_key;

    function __construct()
    {
        $this->section = 'general';
        $this->title = __('General', LF_TEXT_DOMAIN);
        $this->icon = 'fa fa-cog';
        $this->settings_groups = [
            'wplf_basic_settings'      => __('Basic Options', LF_TEXT_DOMAIN),
            'wplf_recap_settings'        => __('ReCaptcha Configuration', LF_TEXT_DOMAIN),
            'wplf_api_settings'        => __('API Key', LF_TEXT_DOMAIN),
        ];

        $this->settings_fields['__wplf_disable_styles'] = ['group' => 'wplf_basic_settings','type' => 'checkbox', 'note' => __("Disabled frontend styles", "liveforms"), 'attrs' => ['title' => __("Disable frontend styles", "liveforms"), 'name' => '__wplf_disable_styles', 'value' => 1, 'id' => 'disable_styles', 'checked' => ($this->disabled_styles = get_option('__wplf_disable_styles', false)), 'validate' => 'int']];
        $this->settings_fields['__wplf_query_status_page'] = ['group' => 'wplf_basic_settings','type' => 'custom', 'label' => esc_attr__( 'Front-end Query Status Page', LF_TEXT_DOMAIN ),'custom_control' => 'wp_dropdown_pages', 'note' => sprintf(__("Select the page where you used the shortcode %s", "liveforms"), "<a href='#'><code>[liveform_query]</code></a> or <a href='#'>the query status block</a>"), 'attrs' => ['name' => '__wplf_query_status_page', 'selected' => ($this->query_status_page = get_option('__wplf_query_status_page')), 'id' => '__wplf_query_status_page', 'class' => 'form-control', 'validate' => 'int', 'echo' => 0, 'show_option_none' => esc_attr__( 'Select Query Status Page', LF_TEXT_DOMAIN )]];
        $this->settings_fields['__wplf_chosen_js'] = ['group' => 'wplf_basic_settings','type' => 'checkbox', 'note' => __("Enable advanced searchable dropdown using chosen js library", "liveforms"), 'attrs' => ['title' => __("Enable advanced dropdown", "liveforms"), 'name' => '__wplf_chosen_js', 'value' => 1, 'id' => '__wplf_chosen_js', 'checked' => ($this->chosen_js = get_option('__wplf_chosen_js', 0)), 'validate' => 'int']];
        $this->settings_fields['__wplf_email_template'] = ['group' => 'wplf_basic_settings','label' => __("Email template", "liveforms"),'type' => 'custom', 'custom_control' => [$this, 'emailTemplateDropdown'], 'note' => __("Select email template", "liveforms"), 'attrs' => ['title' => __("Email template", "liveforms"), 'name' => '__wplf_email_template', 'id' => '__wplf_email_template', 'selected' => ($this->email_template = get_option('__wplf_email_template', 'default')), 'validate' => 'txt']];

        $this->settings_fields['__wplf_recaptcha_site_key'] = ['group' => 'wplf_recap_settings', 'type' => 'text', 'label' => esc_attr__( 'Site Key', LF_TEXT_DOMAIN ), 'note' => "<a target='_blank' href='https://www.google.com/recaptcha/admin/create'>".esc_attr__( 'Get the keys', LF_TEXT_DOMAIN )."</a>", 'attrs' => ['placeholder' => __("ReCaptcha Site Key", "liveforms"), 'name' => '__wplf_recaptcha_site_key', 'id' => '__wplf_recaptcha_site_key', 'class' => 'form-control', 'value' => get_option('__wplf_recaptcha_site_key', '')]];
        $this->settings_fields['__wplf_recaptcha_secret_key'] = ['group' => 'wplf_recap_settings', 'type' => 'text', 'label' => esc_attr__( 'Secret Key', LF_TEXT_DOMAIN ), 'note' => '', 'attrs' => ['placeholder' => __("ReCaptcha Secret Key", "liveforms"), 'name' => '__wplf_recaptcha_secret_key', 'id' => '__wplf_recaptcha_secret_key', 'class' => 'form-control', 'value' => get_option('__wplf_recaptcha_secret_key', '')]];

        $default_key = 'LIVEFORM'.strtoupper(uniqid());
        $this->api_key = get_option('__wplf_api_key', false);
        if(!$this->api_key) {
            update_option('__wplf_api_key', $default_key);
            $this->api_key = $default_key;
        }
        $zapier_app = "";
        if(is_pro()) $zapier_app = "<a href='https://zapier.com/developer/public-invite/125173/d2bfb5696da9eb1e2a1d8af0a60d5588/'>Get access to LiveForms Zaiper App</a>";

        $this->settings_fields['__wplf_api_key'] = ['group' => 'wplf_api_settings', 'type' => 'text', 'label' => '', 'note' => esc_attr__( 'To integrate with the zapier you will need the pro version!', LF_TEXT_DOMAIN )." ".$zapier_app, 'attrs' => ['placeholder' => __("API Key", "liveforms"), 'name' => '__wplf_api_key', 'id' => '__wplf_api_key', 'class' => 'form-control input-lg', 'value' => $this->api_key]];


    }

    function emailTemplateDropdown($attrs)
    {
        ob_start();
        ?>

        <select class="form-control custom-select" name="<?= $attrs['name']; ?>" id="<?= $attrs['id']; ?>">
            <option value="default" <?php selected('default', $attrs['selected']) ?>>Default</option>
            <option value="modern" <?php selected('modern', $attrs['selected']) ?>>Modern</option>
            <option value="card" <?php selected('card', $attrs['selected']) ?>>Card</option>
        </select>
        <?php
        return ob_get_clean();
    }
}
