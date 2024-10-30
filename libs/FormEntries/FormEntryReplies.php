<?php

namespace LiveForms\FormEntries;

use LiveForms\__\Session;

class FormEntryReplies
{
    public $id;
    public $form_id;
    public $user_email;
    public $avatar;
    public $entry_id;
    public $time;
    public $sender;
    public $date_time;
    public $message;
    public $type;
    private $dbtable;

    public function __construct($dbrow = null)
    {

        global $wpdb;
        $this->dbtable = "{$wpdb->prefix}liveforms_replies";
        if(is_object($dbrow)) {
            $this->id = $dbrow->id;
            $this->form_id = $dbrow->form_id;
            $this->entry_id = $dbrow->entry_id;
            $this->user_email = $dbrow->user_email;
            $this->avatar = get_avatar($dbrow->user_email, 256);
            $this->time = $dbrow->time;
            $this->date_time = wp_date(get_option('date_format').' '.get_option('time_format'), $dbrow->time);
            $this->type = $dbrow->type;
            $this->sender = ucwords($dbrow->type);
            $this->message = wpautop($dbrow->message);
        }

        add_action("wp_ajax_admin_reply", [ $this, 'createReply']);
        //add_action("wp_ajax_get_replies", [ $this, 'getReplies']);

        return $this;
    }

    function getAll($entry_id)
    {
        global $wpdb;
        $replies = $wpdb->get_results("select * from {$this->dbtable} where entry_id = '{$entry_id}' ORDER BY `time` DESC limit 0, 9999");
        $allreplies = [];
        foreach ($replies as $reply){
            $allreplies[] = new FormEntryReplies($reply);
        }
        return ($allreplies);
    }

    function getReplies()
    {
        $replies = $this->getAll(wplf_query_var('entry_id', 'int'));
        wp_send_json(['success' => true, 'replies' => $replies]);
    }

    function get($id)
    {
        global $wpdb;
        $reply = $wpdb->get_row("select * from {$this->dbtable} where id = '{$id}'");
        $reply = new FormEntryReplies($reply);
        return $reply;
    }

    function create($entry_id, $message, $type, $send_email = 1)
    {
        global $wpdb, $current_user;
        $email = '';
        $entry = LiveForms()->entry->get($entry_id);
        if(is_user_logged_in()) $email = $current_user->user_email;
        else {
            foreach ($entry->entry_data as $value)
            {
                if(is_valid_email($value)) {
                    $email = $value;
                    break;
                }
            }
        }
        $wpdb->insert($this->dbtable, ['form_id' => $entry->form_id, 'entry_id' => $entry->id, 'message' => $message, 'user_email' => $email, 'time' => time(), 'type' => $type]);
        $doamin = parse_url(home_url());
        $doamin = $doamin['host'];
        if($send_email) {
            if ($type === 'admin') {
                $message .= "<br/><a class=\"__button\" href='".admin_url("edit.php?post_type=form&page=form-entries&form_id={$entry->form_id}&req_id={$entry->id}")."'>".esc_attr__( 'Reply', LF_TEXT_DOMAIN )."</a>";;
                foreach ($entry->emails as $email) {
                    $params = [
                        'send_to' => $email,
                        'subject' => sprintf(__("[ %s ► %s ] Your got a reply for request # %d", "liveforms"), get_option('blogname'), get_the_title($entry->form_id), $entry->id),
                        'email_message' => wpautop($message),
                        'from_emailname' => get_option('blogname'),
                        'from_email' => "noreply@{$doamin}"
                    ];
                    LiveForms()->email->send($params);
                }
            } else {
                $formdata = LiveForms()->getForm(wplf_query_var('form_id'));
                $token = Session::get('wplf_entry_token');
                $query_page_id = get_option('__wplf_query_status_page');
                $message = $query_page_id ? $message."<br/><a class=\"__button\" href='".add_query_arg(['wplf-token' => $token], get_permalink($query_page_id))."'>".esc_attr__( 'Reply', LF_TEXT_DOMAIN )."</a>" : "";
                $email = wplf_valueof($formdata, 'admin_email/send_to', get_option('admin_email'));
                $params = [
                    'send_to' => $email,
                    'subject' => sprintf(__("[ %s ► %s ] Your got a reply for request # %d", "liveforms"), get_option('blogname'), get_the_title($entry->form_id), $entry->id),
                    'email_message' => wpautop($message),
                    'from_emailname' => get_option('blogname'),
                    'from_email' => "noreply@{$doamin}"
                ];
                LiveForms()->email->send($params);
            }
        }
    }

    function delete($reply_id)
    {
        global $wpdb;
        return $wpdb->delete($this->dbtable, ['id' => (int)$reply_id]);
    }

    function createReply()
    {
        $entry_id = wplf_query_var('req_id');
        if(!$entry_id) {
            $token = Session::get('wplf_entry_token');
            $entry = LiveForms()->entry->getByToken($token);
            $entry_id = $entry->id;
        }
        if(!$entry_id) wp_send_json(['success' => false, 'message' => esc_attr__( 'No entry ID is given!', LF_TEXT_DOMAIN )]);
        $this->create($entry_id, wplf_query_var('reply_msg', ['validate' => 'kses']), wplf_query_var('type', ['default' => 'user']));

        $replies = $this->getAll($entry_id);

        wp_send_json(['success' => true, 'replies' => $replies]);
    }

    function queryStatus()
    {
        ob_start();
        include LF_BASE_DIR.'views/query-status.php';
        return ob_get_clean();
    }

}
