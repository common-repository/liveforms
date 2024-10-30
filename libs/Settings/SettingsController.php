<?php
namespace LiveForms\Settings;

if (!defined('WPINC')) {
    exit;
}

use LiveForms\__\__;


class SettingsController
{
    private static $instance;

    /**
     * @var Section[]
     */
    public $settings_sections;
    public $settings_fields;

    /**
     * @var General
     */
    public $general;

    /**
     * @var License
     */
    public $license;


    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
            self::$instance->actions();
        }
        return self::$instance;
    }

    function __construct()
    {
        $this->initiateSettings();
    }

    private function actions()
    {

        add_action('wp_ajax_liveforms_settings', array($this, 'loadSettingsPage'));
        add_action('wp_ajax_liveforms_save_settings', array($this, 'saveSettings'));
        add_action('admin_menu', array($this, 'adminMenu'), 99999);

    }

    /**
     * @usage Initiate Settings Tabs
     */
    function initiateSettings()
    {

        $tabs = [];
        $this->general = $tabs['general'] = new General();
        $this->license = $tabs['license'] = new License();

        $this->settings_sections = apply_filters("add_liveforms_settings_tab", $tabs);

    }


    static function settingsPageUrl($id = null)
    {
        $params = ['page' => 'wplf-settings'];
        if ($id) $params['tab'] = $id;
        return add_query_arg($params);
    }

    /**
     * @usage  Admin Settings Tab Helper
     * @param string $sel
     */
    public function renderMenu($sel = '')
    {

        foreach ($this->settings_sections as $id => $tab) {
            if ($sel === $id)
                echo "<li class='active'><a class='nav-link' id='{$id}' href='" . self::settingsPageUrl($id) . "'><i class='{$tab->icon} mr-2'></i> {$tab->title}</a></li>";
            else
                echo "<li><a class='nav-link' id='{$id}' href='" . self::settingsPageUrl($id) . "'><i class='{$tab->icon} mr-2'></i> {$tab->title}</a></li>";

        }
    }

    public function renderSettingsTab($tab_id)
    {
        call_user_func([$this->settings_sections[$tab_id], 'render']);

    }

    function loadSettingsPage()
    {
        global $stabs;
        if (current_user_can(LF_ADMIN_CAP)) {
            $this->renderSettingsTab(__::query_var('section'));
        }
        die();
    }

    function saveSettings()
    {
        $settings_section = $this->settings_sections[__::query_var('section')];
        $setting_fields = $settings_section->settings_fields;

        foreach ($setting_fields as $name => $field) {
            $value = __::query_var($name, ['validate' => __::valueof($field, 'validate')]);
            $value_original = $value;
            if(__::valueof($field, 'save_callback') !== '') {
              call_user_func(__::valueof($field, 'save_callback'), $value, $field);
            } else {
                if (__::valueof($field, 'validate_callback') !== '') {
                    $value = call_user_func(__::valueof($field, 'validate_callback'), $value);
                }
                if (!is_wp_error($value))
                    update_option($name, $value, false);
                else
                    wp_send_json(['success' => false, 'message' => $value->get_error_message()]);
            }
        }
        wp_send_json(['success' => true, 'message' => $settings_section->success_msg ]);
    }


    function adminMenu()
    {
        add_submenu_page("edit.php?post_type=form", __('Settings', LF_TEXT_DOMAIN), __('Settings', LF_TEXT_DOMAIN), LF_ADMIN_CAP, 'wplf-settings', array($this, 'settings'), 999);
    }

    function settings()
    {
        $tab =  __::query_var('tab', ['default' => 'general']);
        include __DIR__ . '/views/settings.php';
    }


}


