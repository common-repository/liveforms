<?php


namespace LiveForms;


use LiveForms\__\__;
use LiveForms\__\Crypt;
use LiveForms\__\Email;
use LiveForms\__\Session;


class RestAPI
{
    function __construct()
    {
        add_action( 'rest_api_init', [$this, 'apiInit'] );
    }

    function url($endpoint)
    {
        return get_rest_url(null, "liveforms/v1/{$endpoint}");
    }

    function apiInit(){


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


    }

}