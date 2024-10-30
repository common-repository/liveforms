<?php
/*
  Plugin Name: Live Forms
  Plugin URI: https://wpliveforms.com/
  Description: Drag and Drop Form Builder Form WordPress
  Author: W3 Eden, Inc.
  Version: 4.8.1
  Author URI: https://wpliveforms.com/
  Text Domain: liveforms
  Domain Path: /languages
*/

namespace LiveForms;

define("LF_PLUGIN_DIR", basename(__DIR__));
define("LF_TEXT_DOMAIN", basename(__DIR__));
define("LF_BASE_DIR", dirname(__FILE__) . "/");
define("LF_BASE_URL", plugins_url(LF_PLUGIN_DIR."/"));
define("LF_ASSET_URL", plugins_url(LF_PLUGIN_DIR."/assets/"));
$dir = wp_upload_dir();
define("LF_UPLOAD_PATH", $dir['basedir'] . '/liveform-files/');
define('LF_ACTIVATED', true);

use LiveForms\__\__;
use LiveForms\__\Crypt;
use LiveForms\__\Email;
use LiveForms\__\Session;
use LiveForms\__\Template;
use LiveForms\Api\Api;
use LiveForms\ElementorWidget\ElementorWidget;
use LiveForms\Form\AccessControl;
use LiveForms\Form\Actions;
use LiveForms\Form\SubmitHandler;
use LiveForms\FormEntries\FormEntries;
use LiveForms\FormEntries\FormEntry;
use LiveForms\FormEntries\FormEntryManager;
use LiveForms\FormEntries\FormEntryReplies;
use LiveForms\FormEntries\PaymentEntries;
use LiveForms\FormEntries\PaymentEntry;
use LiveForms\GutenbergBlock\GutenbergFormBlock;
use LiveForms\GutenbergBlock\GutenbergQueryStatusBlock;
use LiveForms\Settings\SettingsController;


if (!defined('LF_ADMIN_CAP'))
    define('LF_ADMIN_CAP', 'manage_options');

global $live_forms;

// Include libraries
require_once LF_BASE_DIR . 'libs/Field.php';
require_once LF_BASE_DIR . 'libs/field_defs.php';
require_once LF_BASE_DIR . 'libs/functions.php';

class LiveForms
{
    public $fields_common;
    public $fields_generic;
    public $fields_advaced;
    public $fields_custom;
    public $set_methods;
    public $form;
    public $accessControl;
    public $template;
    public $email;
    public $shortCodes;
    public $payment;
    public $paymentEntry;
    public $payments;
    public $entries;
    public $settings;
    public $API;

    public static function getInstance()
    {
        static $instance;
        if ($instance == null) {
            $instance = new self;
        }
        return $instance;
    }

    /**
     * Constructor function
     */
    private function __construct()
    {
        spl_autoload_register(array($this, 'autoLoad'));

        $this->autoLoadClasses();

        $this->accessControl = new AccessControl();
        $this->template = new Template();
        $this->email = new Email();
        $this->shortCodes = new ShortCodes();
        $this->paymentEntry = new PaymentEntry();
        $this->payments = new PaymentEntries();
        $this->entries = new FormEntries();
        $this->entry = new FormEntry();
        $this->entryReplies = new FormEntryReplies();
        $this->payment = new Payment();
        $this->API = new Api();

        add_action('init', [$this->payment, 'verifyPayment']);

        new FormEntryManager();
        new GutenbergFormBlock();
        new GutenbergQueryStatusBlock();
        new Actions();

        ElementorWidget::getInstance();

        // Public view shortcodes
        add_shortcode('liveform', array($this, 'render'));
        add_shortcode('liveform_agent', array($this, 'view_agent'));
        add_shortcode('liveform_query', array($this, 'view_public_token'));

        // Deploy installer
        register_activation_hook(__FILE__, array($this, 'install'));

        // Activate init hooks
        add_action('init', array($this, 'download'));
        add_action('wp', array($this, 'form_preview'));
        add_action('init', array($this, 'form_post_type_init'));
        add_action('init', array($this, 'ajax_get_request_list'));
        add_action('init', array($this, 'ajax_submit_reply'));
        add_action('init', array($this, 'ajax_submit_change_request_state'));
        add_action('init', array($this, 'ajax_action_upadate_agent'));

        add_action('init', array($this, 'autoload_field_classes'));

        new SubmitHandler();


        add_action('plugin_loaded', array($this, 'checkDB'));
        add_action( 'plugins_loaded', array($this, 'loadTextdomain') );

        // Custom UI elements
        add_action('admin_menu', array($this, 'register_custom_menu_items'));
        add_action('add_meta_boxes', array($this, 'addMetaBox'));
        add_filter('post_row_actions', array($this, 'add_option_showreqs'), 10, 2);
        add_filter('manage_form_posts_columns', array($this, 'add_columns_to_form_list'));
        add_action('manage_form_posts_custom_column', array($this, 'populate_form_list_custom_columns'), 10, 2);
        add_filter("liveform_submitform_thankyou_message", array($this, 'liveform_submitform_thankyou_message'), 10, 1);

        // Liveform bindings
        add_action('save_post', array($this, 'action_save_form'));
        add_action("wp_ajax_get_reqlist", array($this, "action_get_reqlist"));
        //add_filter("the_content", array($this, "form_preview"));
        //add_action('init', array($this, 'validate_connect'));

        add_action("admin_head", [$this, "adminHead"]);
        add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
        add_action('admin_enqueue_scripts', array($this, 'adminEnqueueScripts'));


        $this->setup_fields();

        $this->settings = SettingsController::getInstance();


    }

    /**
     * Class autoloader
     */
    function autoLoadClasses()
    {
        spl_autoload_register(function ($class) {

            $prefix = 'LiveForms\\';
            $base_dir = __DIR__ . '/libs/';

            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }

            $relative_class = substr($class, $len);
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

            if (file_exists($file)) {
                require $file;
            }
        });
    }

    /*
     * Installer script to create
     * - Necessary custom tables
     * - Add additional roles
     */

    function install()
    {
        // Invoke wordpress Database object
        global $wpdb;

        // SQLs for creating custom tables
        // Create table to save the "contact requests"/"form entries"

        Installer::init();

        // Add necessary roles
        // Agent role that helps "agent" users to manage
        // the forms that have been assigned to them
        //$agent_caps = get_role('subscriber');
        //add_role('agent', 'Agent', $agent_caps->capabilities);

        $this->form_post_type_init();
        flush_rewrite_rules();

        if (!file_exists(LF_BASE_DIR . 'cache')) {
            mkdir(LF_BASE_DIR . 'cache', 0755);
        }

    }

    function checkDB()
    {
        if (is_admin() && \LiveForms\Installer::dbUpdateRequired()) {
            \LiveForms\Installer::updateDB();
        }
    }

    /**
     * @usage Load Plugin Text Domain
     */
    function loadTextdomain(){
        load_plugin_textdomain(LF_TEXT_DOMAIN, WP_PLUGIN_URL . "/download-manager/languages/", 'download-manager/languages/');
    }

    /**
     * @function        setup_fields
     * @uses            Add the field definitions
     *                    - Common Field types
     *                    - Advanced Field types
     *                    - Generic Field types
     *                    - Method Set
     */
    function setup_fields()
    {
        $this->fields_common = apply_filters("common_fields", $this->fields_common);
        $this->fields_generic = apply_filters("generic_fields", $this->fields_generic);
        $this->fields_advanced = apply_filters("advanced_fields", $this->fields_advaced);
        $this->fields_custom = apply_filters("custom_fields", $this->fields_custom);
        //$this->set_methods = apply_filters("method_set", $this->set_methods);
    }

// Custom menu items for the Admin UI

    /**
     * @function    register_custom_menu_items
     * @uses        Adds various additional menu and list items to wordpress
     * @global type $submenu to modify the wordpress menu items
     */
    function register_custom_menu_items()
    {
        // Submenu item in the "Forms" menu item
        add_submenu_page('edit.php?post_type=form', __('Form Entries'), __('Form Entries'), LF_ADMIN_CAP, 'form-entries', array($this, 'form_entries'));
        add_submenu_page('edit.php?post_type=form', __('Statistics'), __('Statistics'), LF_ADMIN_CAP, 'statistics', array($this, 'admin_view_global_stats'));
        //add_submenu_page('edit.php?post_type=form', __('Add-ons'), __('Add-ons'), 'manage_options', 'addons', array($this, 'addons_list'));
    }

    /**
     * Add a link to all the 'Entries' that have been posted through a 'Form'. This link is added to the Forms list in the Administration backend
     * @param $actions
     * @param $post
     * @return mixed
     */
    function add_option_showreqs($actions, $post)
    {
        if ($post->post_type === 'form') {
            // Entries finder item for the "Forms" list
            $actions['clone'] = "<a title='" . esc_attr(__('Clone', LF_TEXT_DOMAIN)) . "' href='" . admin_url("/?wplftask=clone&form_id={$post->ID}") . "'>" . esc_attr__('Clone', LF_TEXT_DOMAIN) . "</a>";
            $actions['export'] = "<a title='" . esc_attr(__('Export', LF_TEXT_DOMAIN)) . "' href='" . admin_url("/?wplftask=export&form_id={$post->ID}") . "'>" . esc_attr__('Export', LF_TEXT_DOMAIN) . "</a>";
            $actions['showstats'] = "<a title='" . esc_attr(__('Statistics', LF_TEXT_DOMAIN)) . "' href='" . admin_url("edit.php?post_type=form&page=statistics&form_id={$post->ID}&ipp=5&paged=1") . "'>" . esc_attr__('Statistics', LF_TEXT_DOMAIN) . "</a>";
            $actions['form-preview'] = "<a target='_blank' title='" . esc_attr(__('Preview', LF_TEXT_DOMAIN)) . "' href='" . home_url("/?lfpreview={$post->ID}") . "'>" . esc_attr__('Preview', LF_TEXT_DOMAIN) . "</a>";
        }
        return $actions;
    }

    /**
     * @param $url
     * @return mixed
     */

    function remote_get($url)
    {
        $options = array(
            CURLOPT_RETURNTRANSFER => true, // return web page
            CURLOPT_HEADER => false, // don't return headers
            CURLOPT_FOLLOWLOCATION => true, // follow redirects
            CURLOPT_ENCODING => "", // handle all encodings
            CURLOPT_USERAGENT => "spider", // who am i
            CURLOPT_AUTOREFERER => true, // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
            CURLOPT_TIMEOUT => 120, // timeout on response
            CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);
        return $content;
    }

    function getForms()
    {
        $forms = get_posts(['post_type' => 'form', 'posts_per_page' => -1]);
        $_forms = [];
        foreach ($forms as $form) {
            $_forms[$form->ID] = $form->post_title;
        }
        return $_forms;
    }

    function getForm($ID)
    {
        $formdata = get_post_meta($ID, 'form_data', true);
        if(isset($formdata['fieldsinfo'], $formdata['fieldsinfo']['{{fieldindex}}'])) unset($formdata['fieldsinfo']['{{fieldindex}}']);
        return $formdata;
    }

    /**
     * @function is_ajax
     * @return boolean
     * @uses Library fucntion to check if an ajax request
     * is being handled
     */
    function is_ajax()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    /**
     * @function enqueueScripts
     * @uses Add the JS and CSS dependencies for loading on the public accessible pages
     *
     */
    function enqueueScripts()
    {
        global $post;

        if(!get_option('__wplf_disable_styles', 0))
        wp_enqueue_style("liveforms-ui", LF_BASE_URL . "assets/css/liveform-ui.min.css");

        wp_enqueue_script("jquery");
        wp_enqueue_script('jquery-form');

        wp_enqueue_script('jquery-validate', plugins_url('/liveforms/assets/js/jquery.validate.min.js'), array('jquery'));
        if((is_object($post) && (has_block('liveforms/form') || has_shortcode($post->post_content,"liveforms"))) && get_option('__wplf_chosen_js', 0)) {
            wp_enqueue_script("wplf-chosen-js", LF_ASSET_URL.'chosen-select/chosen.jquery.min.js');
            wp_enqueue_style("wplf-chosen-css", LF_ASSET_URL.'chosen-select/chosen.min.css');
        }

        if((is_object($post) && (has_block('liveforms/form_entries') || has_shortcode($post->post_content,"wplf_form_entries"))) ) {
            wp_enqueue_style("liveforms-bootstrap", LF_BASE_URL . "assets/css/bootstrap.css");
            wp_enqueue_style("liveforms-fontawesome", LF_BASE_URL . "assets/fontawesome/css/all.css");
            wp_enqueue_script("jquery");
            wp_enqueue_script("liveforms-bootstrap", LF_BASE_URL . "assets/js/bootstrap.min.js", array('jquery'));
        }

    }

    /**
     * @function adminEnqueueScripts
     * @uses Add the JS and CSS dependencies for loading on the admin accessible sections
     */
    function adminEnqueueScripts()
    {
        if (get_post_type() != 'form' && !(isset($_GET['post_type']) && $_GET['post_type'] == 'form')) return;
        wp_enqueue_style("liveforms-bootstrap", LF_BASE_URL . "assets/css/bootstrap.css");
        wp_enqueue_style("liveforms-fontawesome", LF_BASE_URL . "assets/fontawesome/css/all.css");
        wp_enqueue_style("liveforms-styles", LF_BASE_URL . "assets/css/style.css");
        wp_enqueue_style("liveforms-jqui", LF_BASE_URL . "assets/jqui/theme/jquery-ui.min.css");

        wp_enqueue_script("jquery");
        wp_enqueue_script('jquery-form');
        wp_register_script('jquery-validation-plugin', LF_BASE_URL . 'assets/js/jquery.validate.min.js', array('jquery'));
        wp_enqueue_script('jquery-validation-plugin');
        wp_enqueue_script("liveforms-bootstrap", LF_BASE_URL . "assets/js/bootstrap.min.js");
        wp_enqueue_script("liveforms-mustache", LF_BASE_URL . "assets/js/mustache.js");
        wp_enqueue_script("jquery-ui-core");
        wp_enqueue_script("jquery-ui-sortable");

        wp_enqueue_script("jquery-ui-draggable");
        wp_enqueue_script("jquery-ui-droppable");

        wp_enqueue_script("jquery-ui-datepicker");

        wp_enqueue_script("quicktags" );

        if(wplf_query_var('post') > 0) {
            $cm_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/css'));
            wp_localize_script('jquery', 'wplfcm_css_settings', $cm_settings);
            wp_enqueue_script('wp-theme-plugin-editor');
            wp_enqueue_style('wp-codemirror');
        }

        wp_enqueue_script("liveforms-admin", LF_BASE_URL . 'assets/js/liveforms-admin.js');

    }

    function adminHead()
    {


    }

    /**
     * @function addMetaBox
     * @uses Adds the metaboxes in the 'Form' creation
     *        section of the Administration dashboard
     *        -- Form creation panel
     *        -- Agent selection panel
     */
    public function addMetaBox($post_type)
    {
        $post_types = array('form'); //limit meta box to certain post types
        //if (in_array($post_type, $post_types)) {
        // Add the 'Form' creation panel
        add_meta_box(
            'createnew'
            , __("Form builder", 'liveforms')
            , array($this, 'view_createnew')
            , 'form'
            , 'advanced'
            , 'high'
        );
    }

    /**
     * @function form_post_type_init
     * @uses Initiate the custom post type
     */
    function form_post_type_init()
    {
        $form_post_type_labels = array(
            'name' => _x('Forms', 'post type general name', 'liveforms'),
            'singular_name' => _x('Form', 'post type singular name', 'liveforms'),
            'menu_name' => _x('Forms', 'admin menu', 'liveforms'),
            'name_admin_bar' => _x('Form', 'add new on admin bar', 'liveforms'),
            'add_new' => _x('Add New', 'book', 'liveforms'),
            'add_new_item' => __('Add New Form', 'liveforms'),
            'new_item' => __('New Form', 'liveforms'),
            'edit_item' => __('Edit Form', 'liveforms'),
            'view_item' => __('View Form', 'liveforms'),
            'all_items' => __('All Forms', 'liveforms'),
            'search_items' => __('Search Forms', 'liveforms'),
            'parent_item_colon' => __('Parent Forms:', 'liveforms'),
            'not_found' => __('No forms found.', 'liveforms'),
            'not_found_in_trash' => __('No forms found in Trash.', 'liveforms'),
        );

        $form_post_type_args = array(
            'labels' => $form_post_type_labels,
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'form'),
            'capability_type' => 'page',
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title'),
            //'menu_icon' => 'dashicons-feedback'
            'menu_icon' => 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 427.62 465.65"><defs><style>.cls-1{fill:transparent !important;stroke:#000;stroke-miterlimit:10;stroke-width:10px;opacity:0.2;}.cls-2{fill:#ffffff;}.cls-3{fill:#ffffff;}.cls-4{fill:#fff;}</style></defs><title>liveform-outline</title><g id="Layer_2" data-name="Layer 2"><g id="ai"><path class="cls-1" d="M2.5,160.83v144a86.62,86.62,0,0,0,43.3,75l123.81,71.48a88.41,88.41,0,0,0,88.4,0l123.81-71.48a86.59,86.59,0,0,0,43.3-75v-144a86.62,86.62,0,0,0-43.3-75L258,14.34a88.41,88.41,0,0,0-88.4,0L45.8,85.82A86.64,86.64,0,0,0,2.5,160.83Z"/><rect class="cls-2" x="108.5" y="286.37" width="208.17" height="37.1" rx="12.8"/><rect class="cls-3" x="207.28" y="268.23" width="9.69" height="73.96" rx="4.56" transform="translate(517.34 93.09) rotate(90)"/><rect class="cls-4" x="108.04" y="216.87" width="208.17" height="37.1" rx="12.8"/><rect class="cls-4" x="108.5" y="147.37" width="208.17" height="37.1" rx="12.8"/></g></g></svg>')
        );

        register_post_type('form', $form_post_type_args);
    }

    /**
     * @function action_save_form
     * @uses Save the form after creation through the 'Form' creation panel
     */
    function action_save_form($post_id)
    {
        if (isset($_REQUEST['form_template_id']) && $_REQUEST['form_template_id'] !== '' && $_REQUEST['form_template_id'] !== 'blank') {
            $template_file = LF_BASE_DIR . 'assets/form-templates/' . wplf_query_var('form_template_id', 'txt') . '.form';
            if (file_exists($template_file)) {
                $formadata = file_get_contents($template_file);
                $formadata = maybe_unserialize($formadata);
                $formadata['desciption'] = wplf_valueof($_REQUEST, 'contact/description');
                //lfprecho($formadata);die();
                update_post_meta($post_id, 'form_data', $formadata);
                global $wpdb;
                $wpdb->update("{$wpdb->prefix}posts", ['post_status' => 'publish'], ['ID' => $post_id]);
            }
            return;
        }
        if (isset($_REQUEST['contact'])) {
            $formadata = $_REQUEST['contact'];
            if (count($formadata) > 0 && get_post_type() == 'form') {
                $prev_data = get_post_meta($post_id, 'form_data', $single = true);
                $prev_agent_id = isset($prev_data['agent']) ? $prev_data['agent'] : '';
                if ((empty($formadata['agent']) && !empty($prev_agent_id)) || (!empty($formadata['agent']) && $formadata['agent'] != $prev_agent_id)) {
                    $prev_agent_forms = get_user_meta($prev_agent_id, 'form_ids', true);
                    if (!empty($prev_agent_forms)) {
                        $prev_agent_forms = $prev_agent_forms;
                        foreach ($prev_agent_forms as $key => $value) {
                            if ($value == $post_id) {
                                unset($prev_agent_forms[$key]);
                            }
                        }
                    }
                    update_user_meta($prev_agent_id, 'form_ids', $prev_agent_forms);
                }

                update_post_meta($post_id, 'form_data', $formadata);
                update_post_meta($post_id, 'form_emails', wplf_query_var('form_emails', ['validate' => 'html']));
                update_post_meta($post_id, 'frontend_owner_id', get_current_user_id());
                // Add form to agent's formlist
                if (!empty($formadata['agent'])) {
                    $agent_id = $formadata['agent'];
                    $prev_forms = get_user_meta($user_id = $agent_id, 'form_ids', true);
                    if (empty($prev_forms)) {
                        $prev_forms = array($post_id);
                    } else {
                        $prev_forms = $prev_forms;
                        foreach ($prev_forms as $key => $value) {
                            if ($value == $post_id) {
                                unset($prev_forms[$key]);
                            }
                        }
                        $prev_forms[] = $post_id;
                    }
                    update_user_meta($user_id = $agent_id, 'form_ids', $meta_value = $prev_forms);
                }
                //wp_update_post(['ID' => $post_id, 'post_status' => 'publish']);
                global $wpdb;
                $wpdb->update("{$wpdb->prefix}posts", ['post_status' => 'publish'], ['ID' => $post_id]);
                if (wplf_is_ajax()) {
                    wp_send_json(['success' => true]);
                }
            }
        }
    }


    /**
     * @function ajax_get_request_list
     * @return string HTML output for the table of requests
     * @uses Respond to ajax request for list of "List of entry replies"
     */
    function ajax_get_request_list()
    {
        if ($this->is_ajax() && isset($_REQUEST['section']) && $_REQUEST['section'] == 'stat_req') {
            $_REQUEST['paged'] = 1;
            $ajax_html = $this->action_get_reqlist($args = array(
                'form_id' => (int)$_REQUEST['form_id'],
                'status' => esc_attr($_REQUEST['status']),
                'template' => 'showreqs_ajax'
            ));
            echo $ajax_html;
            die();
        }
    }

    /**
     * @function ajax_submit_reply
     * @return string HTML output for the recent reply
     * @uses Respond to ajax request for list of "List of entry replies"
     */
    function ajax_submit_reply()
    {
        if ($this->is_ajax() && isset($_REQUEST['section']) && $_REQUEST['section'] == 'reply') {
            // Add reply to DB
            $reply_id = $this->handle_replies();
            global $wpdb;
            $reply = $wpdb->get_row("select * from {$wpdb->prefix}liveforms_conreqs where `id`='{$reply_id}'", ARRAY_A);
            $replier_id = $reply['uid'];
            if ($replier_id != -1) {
                $replier_data = get_userdata($replier_id);
                if (!$replier_data) {
                    $replier_id = $reply['agent_id'];
                    $replier_data = get_userdata($replier_id);
                }
                $reply['icon'] = md5(strtolower(trim($replier_data->user_email)));
            } else {
                $reply['icon'] = md5(rand());
            }

            if ($reply_id) {
                $image_code = base64_encode($reply['icon']);
                $reply_time = date('Y-m-d H:m', $reply['time']);
                $reply['user_name'] = esc_attr($_REQUEST['user_name']);
                $reply['data'] = esc_attr($reply['data']);
                $ajax_html = " <div class='media thumbnail'><div class='pull-left'>
									<img src='http://www.gravatar.com/avatar/{$image_code}' />
								</div>
								<div class='media-body'>
									<h3 class='media-heading'>{$reply['user_name']}</h3>
									({$reply_time})
									<p>{$reply['data']}</p>
								</div>
							</div>";
                echo $ajax_html;
            } else {
                echo "<<div class='media thumbnail'><div class='pull-left'>"
                    . "Sorry!"
                    . "</div>"
                    . "<div class='media-body'>"
                    . "<h3 class='media-heading'>Failed</h3>"
                    . "<p>The reply could not be saved</p>"
                    . "</div></div>";
            }
            die();
        }
    }

    /**
     * @function ajax_submit_change_request_state
     * @uses Respond to ajax request to change the state of a request
     * @global type $wpdb Wordpress databse object
     */
    function ajax_submit_change_request_state()
    {
        if ($this->is_ajax() && isset($_REQUEST['action']) && $_REQUEST['action'] == 'change_req_state') {
            if (isset($_REQUEST['ids'])) {
                foreach ($_REQUEST['ids'] as $id) {
                    $ids[] = (int)$id;
                }
                $ids = implode(",", $ids);
            }
            $args = array();

            if (isset($_REQUEST['status'])) {
                global $wpdb;
                $status = esc_attr($_REQUEST['status']);
                $query_status = esc_attr($_REQUEST['query_status']);
                $args['status'] = $query_status;
                $query = '';
                switch ($status) {
                    case "delete":
                        $query = "delete from {$wpdb->prefix}liveforms_conreqs where `id` in ({$ids})";
                        break;
                    default:
                        $query = "update {$wpdb->prefix}liveforms_conreqs set `status`='{$status}' where `id` in ({$ids})";
//						$get_count_query = "select * from {$wpdb->prefix}liveforms_conreqs where `status`='{$query_status}'";
//						$new_stat_count = $wpdb->query($get_count_query, ARRAY_A);
                }
                $query = apply_filters('liveform_form-entries_action_query', $query, $status, $ids);
                $wpdb->query($query);

                // Get counts
                $form_id = (int)$_REQUEST['form_id'];
                $get_count_query = "select * from {$wpdb->prefix}liveforms_conreqs where `status`='{$query_status}' and `fid`='{$form_id}'";
                $request_count = $wpdb->query($get_count_query, ARRAY_A);
            }

            if (isset($_REQUEST['form_id'])) {
                $form_id = (int)$_REQUEST['form_id'];
                $args['form_id'] = $form_id;
            }

            $args['template'] = 'showreqs_ajax';
            $ajax_html = $this->action_get_reqlist($args);

            $data = array(
                'count' => $request_count,
                'html' => $ajax_html,
                'changed' => isset($_REQUEST['ids']) ? count($_REQUEST['ids']) : 0
            );
            echo json_encode($data);
            die();
        }
    }

    /**
     * @function view_public_token
     * @return type string(html)
     * @uses Render view for Token/Query entry page
     */
    function view_public_token()
    {
        $html = '';
        $html_data = array();
        if(__::query_var('wplf-token') !== '')
            Session::set('wplf_entry_token', __::query_var('wplf-token'));
        $html .= $this->get_html('query', $html_data);
        return $html;
    }

    /**
     * @function view_agent
     * @return type string HTML
     * @uses Render view for the agent
     */
    function view_agent()
    {
        $html = '';
        // Check if the current user is agent
        if (current_user_can('agent')) {
            // Validate if a certain section was requested
            if (isset($_REQUEST['section'])) {
                // Setup default arguments to fetch data for the view HTML
                $args = array(
                    'fid' => (int)$_REQUEST['form_id'],
                );
                // Return HTML for request/entry list
                switch ($_REQUEST['section']) {
                    case 'requests':
                        $html .= $this->view_agent_requests();
                        break;
                    case 'request':
                        $args['reply_for'] = (int)$_REQUEST['req_id'];
                        $html .= $this->view_get_request_data($args);
                        break;
                    case 'reply':
                        $this->handle_replies();
                        $html .= $this->view_get_request_data($args);
                        break;
                }
            } else {
                // Generate list of assigned forms
                $html_data = array();
                $agent_forms = get_user_meta($user_id = get_current_user_id(), $meta_key = 'form_ids', $single = true);
                $forms = array();
                if (is_array($agent_forms) && count($agent_forms) > 0) {
                    foreach ($agent_forms as $form) {
                        $forms[] = get_post($form_id = $form, ARRAY_A);
                    }
                }

                $html_data['agent_forms'] = $forms;
                $html .= $this->get_html('agent_dashboard', $html_data);
            }
        } else {
            $html_data = array();
            $html = $this->get_html('agent_login', $html_data);
        }

        return $html;
    }

    /**
     * @function view_agent_requests
     * @return type string HTML
     * @uses Render view for the list of requests
     *        that are accessilble for the current
     *        logged in agent user
     */
    function view_agent_requests()
    {
        $html = "<div class='w3eden'>";
        $html .= '<div class="wrap">';
        if (current_user_can('agent')) {
            $html .= '<div id="icon-tools" class="icon32">'
                . '</div> ';
            if (isset($_REQUEST['form_id'])) {
                $args = array(
                    'form_id' => (int)$_REQUEST['form_id'],
                );
                if (isset($_REQUEST['status']))
                    $args['status'] = esc_attr($_REQUEST['status']);
                $args['template'] = 'showreqs';
                $html .= $this->action_get_reqlist($args);
            } else {
                $html .= 'You cannot manage this form';
            }
        } else {
            $html .= 'You are not an agent';
        }

        $html .= '</div></div>';

        return $html;
    }

    /**
     * @function admin_view_submitted_forms
     * @return type string HTML
     * @uses Render view for the list of requests
     *        for the Admin
     */
    function form_entries()
    {
        $html = '';

        wp_reset_query();

        if (!isset($_REQUEST['req_id']))
            include LF_BASE_DIR . 'views/admin/form-entries.php';
        else {
            include LF_BASE_DIR . 'views/admin/form-entry-details.php';
        }


    }


    /**
     * @function admin_view_global_stats
     * @return type string HTML
     * @uses Render the statistics page in the Admin panel
     */
    function admin_view_global_stats()
    {
        global $wpdb;

        $form_query = 'post_type=form';

        $all_stats_query = "SELECT * FROM {$wpdb->prefix}liveforms_stats";
        $all_stats = $wpdb->get_results($all_stats_query, ARRAY_A);

        $forms_list = query_posts('post_type=form');
        wp_reset_query();

        $formtitles = array();
        $form_ids = array();
        foreach ($forms_list as $form) {
            $form_ids[$form->ID] = $form->post_title;
            $formtitles[$form->ID] = $form->post_title;
        }

        $max_views = -1;
        $max_submits = -1;

        $max_viewed_form = null;
        $max_submitted_form = null;

        $view_counts = array();
        $submit_counts = array();
        $view_count = 0;
        $submit_count = 0;
        $submit_count_stats = null;
        $view_count_stats = [];

        foreach ($all_stats as $stat) {
            switch ($stat['action']) {
                case 'v':
                    $view_counts[$stat['fid']] = isset($view_counts[$stat['fid']]) ? $view_counts[$stat['fid']]++ : 1;
                    $view_count += $view_counts[$stat['fid']];
                    $view_count_stats[$stat['fid']][] = array(
                        'ip' => $stat['ip'],
                        'time' => array(
                            'second' => date('Y-m-d H:m:s', $stat['time']),
                            'minute' => date('Y-m-d H:m', $stat['time']),
                            'hour' => date('Y-m-d h', $stat['time']),
                            'day' => date('Y-m-d', $stat['time']),
                            'month' => date('Y-m', $stat['time']),
                            'year' => date('Y', $stat['time'])
                        )
                    );
                    break;
                case 's':
                    $submit_counts[$stat['fid']] = isset($submit_counts[$stat['fid']]) ? $submit_counts[$stat['fid']]++ : 1;
                    $submit_count += $submit_counts[$stat['fid']];
                    $submit_count_stats[$stat['fid']][] = array(
                        'ip' => $stat['ip'],
                        'time' => array(
                            'second' => date('Y-m-d H:m:s', $stat['time']),
                            'minute' => date('Y-m-d H:m', $stat['time']),
                            'hour' => date('Y-m-d h', $stat['time']),
                            'day' => date('Y-m-d', $stat['time']),
                            'month' => date('Y-m', $stat['time']),
                            'year' => date('Y', $stat['time'])
                        )
                    );
                    break;
            }
            if (isset($formtitles[$stat['fid']])) {
                if ($view_count > $max_views) {
                    $max_views = $view_count;
                    $max_viewed_form = array(
                        'label' => $formtitles[$stat['fid']],
                        'value' => $stat['fid']
                    );
                }
                if ($submit_count > $max_submits) {
                    $max_submits = $submit_count;
                    $max_submitted_form = array(
                        'label' => $formtitles[$stat['fid']],
                        'value' => $stat['fid']
                    );
                }
            }
        }


        $stats = array(
            'max_submitted_form' => array(
                'label' => 'Most submitted form',
                'value' => $max_submitted_form
            ),
            'max_viewed_form' => array(
                'label' => 'Most viewed form',
                'value' => $max_viewed_form
            ),
            'total_forms' => array(
                'label' => 'Total number of forms',
                'value' => array(
                    'label' => 'Total forms',
                    'value' => count($forms_list)
                )
            )
        );

        // If a single form was requested
        if (isset($_REQUEST['form_id'])) {
            $selected_form_id = (int)$_REQUEST['form_id'];
        } else {
            $selected_form_id = 'none';
        }

        $html_data = array(
            'views' => json_encode($view_count_stats),
            'submits' => json_encode($submit_count_stats),
            'form_ids' => $form_ids,
            'selected_form_id' => $selected_form_id,
            'stats' => $stats
        );

        $html = $this->get_html('admin/stats_global', $html_data);

        echo $html;
    }

    /** View callers * */

    /**
     * @function view_createnew
     * @return type string HTML
     * @uses Render the Form builder window for building form
     * @uses Render the Form builder window for building form
     */
    function view_createnew($post)
    {
        $formdata = get_post_meta($post->ID, 'form_data', true);
        $html_data = array(
            'commonfields' => $this->fields_common,
            'generic_fields' => $this->fields_generic,
            'advanced_fields' => $this->fields_advanced,
            'custom_fields' => $this->fields_custom,
            'methods_set' => $this->set_methods,
            'form_post_id' => $post->ID
        );
        $template = "createnew";
        if (!empty($formdata)) {
            $html_data['form_data'] = $formdata;
            $template = "form-builder";
        }

        $view = $this->get_html($template, $html_data);
        echo $view;
    }


    function form_templates()
    {
        $templates['default'] = ['file' => __DIR__ . "/views/form-ui/default.php", 'name' => 'Default', 'proonly' => false];
        $templates['card'] = ['file' => __DIR__ . "/views/form-ui/card.php", 'name' => 'Card', 'proonly' => false];
        $templates['focus'] = ['file' => __DIR__ . "/views/form-ui/focus.php", 'name' => 'Focus', 'proonly' => false];
        $templates = apply_filters("liveforms_form_templates", $templates);
        return $templates;
    }

    function get_form_template($template_id)
    {
        $templates = $this->form_templates();
        $proonly = isset($templates[$template_id], $templates[$template_id]['proonly']) ? $templates[$template_id]['proonly'] : false;
        $template = isset($templates[$template_id]) ? $templates[$template_id]['file'] : __DIR__ . "/views/form-ui/default.php";
        if ($proonly && !is_pro()) $template = __DIR__ . "/views/form-ui/default.php";
        return $template;
    }

    function render($params)
    {
        $form_id = wplf_valueof($params, 'form_id');
        if(!$form_id)  return __('Form not found!', 'liveforms');
        $formdata = get_post_meta($form_id, 'form_data', $single = true);
        if(!$formdata) return __('Form not found!', 'liveforms');
        $access = $this->accessControl->verifyAccess($form_id, __::valueof($formdata, 'access', ['validate' => 'array', 'default' => []]));
        if(!$access->allowed)
            $form_html = "<div style='text-align:center;padding: 10px;border: 1px solid #d92c4c;background: rgba(255,0,67,0.04);color:  #d92c4c'>{$access->message}</div>";
        else {
            $fields = __::valueof($formdata, 'fields' );
            if(empty($fields)) return "<div class='_wplf'><div class='alert alert-danger'>".__("No form field has been added!", 'liveforms' )."</div></div>";
            $fields = Crypt::encrypt($fields);
            if (!empty($formdata)) {
                $formdata['form_id'] = $form_id;
                ob_start();
                include __DIR__.'/views/form.php';
                $form_html = ob_get_clean();
                /*$paginated_form = paginate_form($formdata, array(
                    'fields_common' => $this->fields_common,
                    'fields_generic' => $this->fields_generic,
                    'fields_advanced' => $this->fields_advanced
                ));
                $html_data = array_merge($paginated_form, array('form_id' => $form_id, 'formsetting' => $formdata, 'fields' => $fields));*/

                //$view = $this->get_html("form", $html_data);

                // Record the view
                $this->record_view_stat($form_id, get_client_ip());
            } else {
                $view = "No forms defined";
            }
        }
        $width = isset($formdata['form_wdith']) && $formdata['form_wdith'] !== '' ? $formdata['form_wdith'] : '100%';
        $formui = wplf_valueof($formdata, 'uitemplate', ['default' => 'default']);
        $form_template = $this->get_form_template($formui);
        $form = $this->template->fetch($form_template, ['form' => $form_html, 'name' => get_the_title($form_id), 'description' => wplf_valueof($formdata, 'description'), 'width' => $width]);
        return $form;
    }

    /** Action callers * */
    /**
     * @function ajax_action_upadate_agent
     * @return type ajax response
     * @uses Update the agent info using AJAX from the User
     */
    public function ajax_action_upadate_agent()
    {
        if ($this->is_ajax() and isset($_REQUEST['section']) and $_REQUEST['section'] == 'update_agent') {
            $agent_info = $_REQUEST['agentinfo'];
            $display_name = esc_attr($agent_info['display_name']);
            $password = esc_attr($agent_info['password']);
            $email = esc_attr($agent_info['email']);

            $response = '';

            if ($password != $agent_info['confirm_password']) {
                $reponse = array('message' => 'Password fields did not match', 'action' => 'danger');
            } else if (strlen($display_name) < 5) {
                $reponse = array('message' => 'Display name must be at least 5 characters long', 'action' => 'danger');
            } else if (!is_valid_email($email)) {
                $reponse = array('message' => 'You must enter a valid email address', 'action' => 'danger');
            } else {
                $reponse = array('message' => 'Your profile has been updated successfully', 'action' => 'success');
                // Update the info
                $info = array(
                    'ID' => get_current_user_id(),
                    'display_name' => $display_name,
                    'email' => $email
                );
                if (strlen($password) > 0) {
                    $info['user_pass'] = $password;
                }
                wp_update_user($info);
            }

            $response = json_encode($reponse);

            echo $response;

            die();
        }
    }


    /**
     * @usgae Block http access to a dir
     * @param $dir
     */
    public static function block_http_access($dir)
    {
        $cont = "RewriteEngine On\r\n<Files *>\r\nDeny from all\r\n</Files>\r\n";
        @file_put_contents($dir . '/.htaccess', $cont);
    }

    /** Library to get template * */
    /**
     * @function get_html
     * @return type HTML output
     * @uses Main rendering engine for views
     */
    function get_html($view, $html_data)
    {
        if (empty($view))
            return null;
        extract($html_data);
        ob_start();
        include(LF_BASE_DIR . "views/{$view}.php");
        $data = ob_get_clean();
        return $data;
    }


    /**
     * @function view_get_request_data
     * @return type HTML output
     * @uses Gather and return all the data submitted during a form submission along
     *          with any responses done afterwards to that request
     */
    function view_get_request_data($args = array())
    {
        global $wpdb;
        // initialize view output

        if (!$args || count($args) == 0) {
            return "No requests found";
        }

        if (isset($args['template'])) {
            $template_name = $args['template'];
            unset($args['template']);
        }
        $html = '';
        // Build the query
        $request_data_query = "select * from {$wpdb->prefix}liveforms_conreqs where ";
        $tmp = array();
        foreach ($args as $key => $value) {
            $tmp[] = "`{$key}`='{$value}'";
        }
        $args_query = implode(" and ", $tmp);
        $request_data_query .= $args_query;


        // Check if token was used to access the response
        // If token is used then fetch the reqply_history using the id of the token
        if (isset($args['token'])) {
            $reply_db_fetch = $wpdb->get_row($request_data_query, ARRAY_A);

            // Terminate further execution since token enquiry is invalid
            if (count($reply_db_fetch) < 1) {
                return "No requests found";
            }

            $args = array(); // rebuild args for second query
            $args['fid'] = $reply_db_fetch['fid'];
            $args['reply_for'] = $reply_db_fetch['id'];
            $request_data_query = "select * from {$wpdb->prefix}liveforms_conreqs where ";
            $tmp = array();
            foreach ($args as $key => $value) {
                $tmp[] = "`{$key}`='{$value}'";
            }
            $args_query = implode(" and ", $tmp);
            $request_data_query .= $args_query;
        }

        $request_data_query .= " order by `time` desc";
        $reply_db_fetch = $wpdb->get_results($request_data_query, ARRAY_A);

        $req_data = $wpdb->get_row("select * from {$wpdb->prefix}liveforms_conreqs where `id`='{$args['reply_for']}'", ARRAY_A);
        $form_data = get_post_meta($post_id = $args['fid'], $meta_key = 'form_data', $single = true);
        $field_values = unserialize($req_data['data']);
        $reply_user_name = '';
        foreach ($form_data['fields'] as $key => $field) {
            if ($field == 'name') {
                $reply_user_name = esc_attr($field_values[$key]);
            }
        }

        if (!isset($_REQUEST['token'])) {
            $current_user = wp_get_current_user();
            $user_name = $current_user->user_login;
        }

        $reply_history = array();
        foreach ($reply_db_fetch as $reply) {
            if ($reply['replied_by'] == 'user') {
                if ($reply['uid'] == -1) {
                    $tmp_user = null;
                } else {
                    $tmp_user = get_userdata(intval($reply['uid']));
                }
            } else {
                $tmp_user = get_userdata(intval($reply['agent_id']));
            }
            $tmp_reply = $reply;
            $tmp_reply['username'] = $tmp_user != null ? $tmp_user->user_login : $reply_user_name;
            $tmp_reply['icon'] = md5(strtolower(trim($tmp_user->user_email)));
            $reply_history[] = $tmp_reply;
        }
        $html_data = array();
        $field_info = $form_data['fieldsinfo'];
        if(isset($field_info['{{fieldindex}}'])) unset($field_info['{{fieldindex}}']);
        $html_data['reply_history'] = $reply_history;
        $html_data['form_fields'] = $field_info;
        $html_data['field_values'] = $field_values;
        $html_data['req_data'] = $req_data;
        $html_data['current_user_name'] = isset($_REQUEST['token']) ? $reply_user_name : $user_name;

        $html .= $this->get_html(isset($template_name) ? $template_name : 'query-status', $html_data);
        return $html;
    }

    /**
     * @function handle_replies
     * @return type Reply insertion id
     * @uses Record replies done via the response system
     */
    function handle_replies()
    {
        global $wpdb;
        $user_id = is_user_logged_in() ? get_current_user_id() : -1;

        $reply_data = array();
        if (!current_user_can('agent') && !current_user_can('manage_options')) {
            $reply_data['uid'] = $user_id;
            $reply_data['replied_by'] = "user";
        } else {
            $reply_data['agent_id'] = $user_id;
            $reply_data['replied_by'] = "agent";
        }
        $reply_data['data'] = esc_attr($_REQUEST['reply_msg']);
        $req_id = $reply_data['reply_for'] = (int)$_REQUEST['req_id'];
        $reply_data['fid'] = (int)$_REQUEST['form_id'];
        $reply_data['time'] = time();


        if ($_REQUEST['req_status'] == "new") { // no previous replies have been issued
            $request_status_update_query = "update {$wpdb->prefix}liveforms_conreqs set `status`='inprogress' where `id`='{$req_id}'";
            $wpdb->query($request_status_update_query);
        }
        $sql_part = '';
        $tmp_sqls_parts = array();
        foreach ($reply_data as $key => $value) {
            $tmp_sqls_parts[] = "`{$key}`='{$value}'";
        }
        $sql_part = implode(", ", $tmp_sqls_parts);
        $reply_add_query = "insert into {$wpdb->prefix}liveforms_conreqs set {$sql_part}";
        $wpdb->query($reply_add_query);

        return $wpdb->insert_id;
    }

    /**
     * @function action_get_reqlist
     * @return type html render ouptut
     * @uses Get a list of requests submitted via a particular form
     */
    function action_get_reqlist($args)
    {
        global $wpdb;

        $form_id = $args['form_id'];

        if (!isset($args['fid'])) {
            $args['fid'] = $form_id;
            unset($args['form_id']);
        }

        if (isset($args['template'])) {
            $template_name = $args['template'];
            unset($args['template']);
        }


        $count_query_prefix = "select count(*) from {$wpdb->prefix}liveforms_conreqs where ";
        $query_prefix = "select * from {$wpdb->prefix}liveforms_conreqs where ";
        $query_args = array();

        foreach ($args as $key => $value) {
            $query_args[] = "`{$key}` = '{$value}'";
        }

        $query_suffix = implode(" and ", $query_args);
        if (!isset($args['token'])) {
            $query_suffix .= " and `token` != ''";
        }
        $query = $query_prefix . $query_suffix;
        $count_query = $count_query_prefix . $query_suffix;
        $req_count = $wpdb->get_row($count_query, ARRAY_A);

        // Counting query states [new, inprogress, onhold, resolved]
        $new_request_query = $count_query . " and `status`='new'";
        $new_request_count = $wpdb->get_row($new_request_query, ARRAY_A);
        $inprogress_request_query = $count_query . " and `status`='inprogress'";
        $inprogress_request_count = $wpdb->get_row($inprogress_request_query, ARRAY_A);
        $onhold_request_query = $count_query . " and `status`='onhold'";
        $onhold_request_count = $wpdb->get_row($onhold_request_query, ARRAY_A);
        $resolved_request_query = $count_query . " and `status`='resolved'";
        $resolved_request_count = $wpdb->get_row($resolved_request_query, ARRAY_A);

        //Pagination
        $items_per_page = isset($_REQUEST['ipp']) ? (int)$_REQUEST['ipp'] : 20;
        $page_id = isset($_REQUEST['paged']) ? intval($_REQUEST['paged']) - 1 : 0;
        $starting_item = intval($page_id) * intval($items_per_page);
        $query .= " limit {$starting_item}, {$items_per_page}";

        $form_meta = get_post_meta($form_id, 'form_data', true);
        $form_title = get_post_field('post_title', $form_id);

        $reqlist = $wpdb->get_results($query, ARRAY_A);

        $form = array(
            'id' => $form_id,
            'title' => $form_title
        );

        $counts = array(
            'inprogress' => $inprogress_request_count['count(*)'],
            'new' => $new_request_count['count(*)'],
            'resolved' => $resolved_request_count['count(*)'],
            'onhold' => $onhold_request_count['count(*)']
        );

        if (empty($reqlist)) {
            return 'No requests found';
        }

        $html_data = array(
            'form' => $form,
            'form_fields' => $form_meta['fieldsinfo'],
            'reqlist' => $reqlist,
            'counts' => $counts,
            'total_request' => $req_count['count(*)'],
        );
        $form_html = $this->get_html(isset($template_name) ? $template_name : 'showreqs', $html_data);
        return $form_html;
    }


    /**
     * @function add_columns_to_form_list
     * @return type modified list of colums for wp native post list
     * @uses Modify the form(post) list in the admin panel and add extra columns
     */
    function add_columns_to_form_list($column)
    {
        $column['form_id'] = 'Shortcode';
        $column['view_count'] = 'Views';
        $column['submit_count'] = 'Submissions';

        return $column;
    }

    /**
     * @function populate_form_list_custom_columns
     * @return type null
     * @uses Fill up the custom columns added via the 'add_columns_to_form_list' method
     */
    function populate_form_list_custom_columns($column_name, $post_id)
    {
        $custom_field = get_post_custom($post_id);
        $view_count = get_post_meta($post_id, 'view_count', true) == '' ? 0 : get_post_meta($post_id, 'view_count', true);
        $submit_count = get_post_meta($post_id, 'submit_count', true) == '' ? 0 : get_post_meta($post_id, 'submit_count', true);
        switch ($column_name) {
            case 'form_id':
                echo "<div class='w3eden' style='width: 300px'><div class='input-group'><input style='font-family: monospace' type='text' class='form-control bg-white' readonly='readonly' id='wplfsc-{$post_id}' value='[liveform form_id={$post_id}]'/><div class='input-group-btn'><button onclick='WPLF.copy(\"wplfsc-{$post_id}\")' type='button' class='btn btn-secondary'><i class='fa fa-copy'></i></button></div></div></div>";
                break;
            case 'view_count':
                echo $view_count;
                break;
            case 'submit_count':
                echo "<a href='edit.php?section=requests&post_type=form&page=form-entries&form_id={$post_id}'>$submit_count</a>";
                break;
            default:
        }
    }

    /**
     * @function form_preview
     * @return String  HTML render string
     * @uses Generate a preview of form
     */
    function form_preview()
    {
        //if (get_post_type() != "form")
        //    return $content;
        if(wplf_query_var('lfpreview', 'int') > 0 && current_user_can(LF_ADMIN_CAP)) {
            include __DIR__.'/views/form-preview.php';
            die();
        }
    }

    /**
     * @function record_view_stat
     * @return type  null
     * @uses Record and increment the view count of a form by 1 and store the ip used
     */
    function record_view_stat($form_id, $ip = 'not acquired')
    {
        global $wpdb;
        $form_data = get_post($form_id);
        $form_author_id = $form_data->post_author;
        $view_count = get_post_meta($form_id, 'view_count', true);
        if ($view_count == '') {
            $view_count = 0;
        }
        update_post_meta($form_id, 'view_count', $view_count + 1);

        $current_time = time();
        $wpdb->query("INSERT into {$wpdb->prefix}liveforms_stats SET `fid`='{$form_id}', `author_id`='{$form_author_id}', `action`='v', `ip`='{$ip}', `time`='{$current_time}' ");
    }


    /**
     * @function get_field_names
     * @return type array
     * @uses Extract field names from serialized form data and prepare an array with ID => Label
     */
    function get_field_names($ef_data, $ef_form_data)
    {
        $ef_data = maybe_unserialize($ef_data);
        $ef_form_data = maybe_unserialize($ef_form_data);
        $ef_prep_fields = array();

        foreach ($ef_data as $ef_name => $ef_value) {
            $ef_prep_fields[$ef_name] = $ef_form_data['fieldsinfo'][$ef_name]['label'];
        }

        return $ef_prep_fields;
    }

    function liveform_submitform_thankyou_message($message)
    {
        return $message;
    }

    /*
    function validate_connect(){
    if(wplf_query_var('wppm_auth_key') !== ''){
    $wppm_auth_key = base64_decode(wplf_query_var('wppm_auth_key'));
    $wppm_auth_key = json_decode($wppm_auth_key);
    if ($wppm_auth_key->validated) {
        update_option("__wplf_pro", Crypt::encrypt($wppm_auth_key));
    }
    ?><!DOCTYPE html>
    <html>
    <body>
    <script>
        window.opener.location.reload();
        window.close();
    </script>
    </body>
    </html>
    <?php
    die();
    }
    }
    */

    /**
     * @function autoload_field_classes
     * @return type  null
     * @uses Autoloader to load field classes when they are used
     */
    public static function autoload_field_classes()
    {
        $field_class_directories = array(
            LF_BASE_DIR . 'formfields/common/',
            LF_BASE_DIR . 'formfields/generic/',
            LF_BASE_DIR . 'formfields/advanced/'
        );
        foreach ($field_class_directories as $dir) {
            $class_files = scandir($dir);
            for ($it = 2; $it < count($class_files); $it++) {
                include $dir . $class_files[$it];
            }
        }
    }

    function download()
    {
        if (isset($_REQUEST['lfdl']) && current_user_can("manage_options")) {
            global $wpdb;
            $lfdl = explode("|", $_REQUEST['lfdl']);
            $id = $lfdl[0];
            $field = $lfdl[1];
            $req = maybe_unserialize($wpdb->get_var("select data from {$wpdb->prefix}liveforms_conreqs where id = '$id'"));
            $filename = wp_basename($req[$field]);
            $content_type = mime_content_type($req[$field]);
            nocache_headers();
            header("X-Robots-Tag: noindex, nofollow", true);
            header("Robots: none");
            header('Content-Description: File Transfer');
            if (strpos($_SERVER['HTTP_USER_AGENT'], "Safari") && !isset($extras['play']) && !get_option('__wpdm_open_in_browser', 0))
                $content_type = "application/octet-stream";
            header("Content-type: $content_type");

            if (get_option('__wpdm_open_in_browser', 0))
                header("Content-disposition: inline;filename=\"{$filename}\"");
            else
                header("Content-disposition: attachment;filename=\"{$filename}\"");

            header("Content-Transfer-Encoding: binary");
            readfile($req[$field]);
            die();
        }
    }

    /**
     * @param $name
     * @usage Class autoloader
     */
    function autoLoad($name)
    {

        if (!substr_count($name, 'LiveForms')) return;

        $originClass = $name;
        $src_path = LF_BASE_DIR . 'libs/';
        $parts = explode("\\", $name);
        $class_file = end($parts);
        $class_file = $class_file . '.php';
        $parts[count($parts) - 1] = $class_file;
        $relative_path = implode("/", $parts);

        $path = str_replace('LiveForms\\', $src_path, $name) . '.php';
        $path = str_replace("\\", "/", $path);
        //lfprecho($path);
        if (file_exists($path)) {
            require_once $path;
        } /*else {
            lfprecho($path);
            die();
        }*/
    }

}

/** Initialize * */
//new liveforms();

$live_forms = LiveForms::getInstance();
