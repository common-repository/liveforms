<?php


namespace LiveForms\Api;


use LiveForms\__\__;

class Api
{
    function __construct()
    {
        add_action( 'rest_api_init', [$this, 'apiInit'] );
    }

    function apiInit(){


        register_rest_route( 'liveforms/v1', '/authorize', array(
            'methods' => 'GET',
            'callback' => array($this, 'validateKey'),
            'permission_callback' => '__return_true'
        ) );

        register_rest_route( 'liveforms/v1', '/entry/validate-token', array(
            'methods' => 'POST',
            'callback' => array(LiveForms()->entry, 'validateToken'),
            'permission_callback' => '__return_true'
        ) );

        register_rest_route( 'liveforms/v1', '/entry/logout', array(
            'methods' => 'GET',
            'callback' => array(LiveForms()->entry, 'logout'),
            'permission_callback' => '__return_true'
        ) );

        register_rest_route( 'liveforms/v1', '/entry/user-reply', array(
            'methods' => 'POST',
            'callback' => array(LiveForms()->entryReplies, 'createReply'),
            'permission_callback' => '__return_true'
        ) );

        register_rest_route( 'liveforms/v1', '/entry/all-replies', array(
            'methods' => 'POST',
            'callback' => array(LiveForms()->entryReplies, 'getRepliesByToken'),
            'permission_callback' => '__return_true'
        ) );

        register_rest_route( 'liveforms/v1', '/forms', array(
            'methods' => 'GET',
            'callback' => array($this, 'getForms'),
            'permission_callback' => 'is_pro'
        ) );

        register_rest_route( 'liveforms/v1', '/form-entries', array(
            'methods' => 'GET',
            'callback' => array($this, 'getFormEntries'),
            'permission_callback' => 'is_pro'
        ) );

        register_rest_route( 'liveforms/v1', '/entry-replies', array(
            'methods' => 'GET',
            'callback' => array(LiveForms()->entryReplies, 'getReplies'),
            'permission_callback' => 'is_pro'
        ) );



    }

    function url($endpoint)
    {
        return get_rest_url(null, "liveforms/v1/{$endpoint}");
    }

    function isKeyValid()
    {
        return ( LiveForms()->settings->general->api_key === __::query_var('wplf_key') );
    }

    function validateKey(){
        global $current_user;
        if( LiveForms()->settings->general->api_key === __::query_var('wplf_key') ) {
            wp_send_json(['success' => true, 'access_token' => wp_create_nonce( 'wp_rest' )]);
        }
        wp_send_json(['success' => false, 'message' => esc_attr__( 'Invalid api key!', LF_TEXT_DOMAIN )]);
    }

    function getForms()
    {
        if(!$this->isKeyValid()) {
            wp_send_json(['success' => false, 'message' => esc_attr__( 'Invalid api key!', LF_TEXT_DOMAIN )]);
        }

        $forms = get_posts(['post_type' => 'form', 'posts_per_page' => -1]);
        $_forms = [];
        foreach ($forms as $form) {
            $_forms[] = [ 'id' => $form->ID, 'name' => $form->post_title ];
        }
        wp_send_json($_forms);
    }

    function getFormEntries()
    {
        if(!$this->isKeyValid()) {
            wp_send_json(['success' => false, 'message' => esc_attr__( 'Invalid api key!', LF_TEXT_DOMAIN )]);
        }
        $entries = LiveForms()->entries->get(__::query_var('form_id'));
        wp_send_json($entries);
    }

}
