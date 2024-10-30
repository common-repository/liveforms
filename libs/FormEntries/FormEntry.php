<?php

namespace LiveForms\FormEntries;

use LiveForms\__\__;
use LiveForms\__\Session;

class FormEntry
{
    public $id = null;
    public $form_id;
    public $user_id;
    public $status;
    public $token;
    public $time;
    public $agent_id;
    public $reply_for;
    public $replied_by;
    public $entry_data;
    public $entry_data_formatted;
    public $emails = [];
    private $dbtable;

    public function __construct($dbrow = null)
    {

        global $wpdb;
        $this->dbtable = "{$wpdb->prefix}liveforms_conreqs";
        if(is_object($dbrow)) {
            $this->id = $dbrow->id;
            $this->form_id = $dbrow->fid;
            $this->user_id = $dbrow->uid;
            $this->status = $dbrow->status;
            $this->time = $dbrow->time;
            $this->agent_id = $dbrow->agent_id;
            $this->reply_for = $dbrow->reply_for;
            $this->replied_by = $dbrow->replied_by;
            $this->token = $dbrow->token;
            $this->entry_data = maybe_unserialize($dbrow->data);
            $form_data = get_post_meta($this->form_id, 'form_data', true);
            $field_info = __::valueof($form_data, 'fieldsinfo');
            $formatted_data = [];
            foreach ($this->entry_data as $key => $val) {
                $this->$key = $val;
                $formatted_data[] = ['name' => __::valueof($field_info,"{$key}/label"), 'id' => $key,  'value' => $val];
            }
            $this->entry_data_formatted = $formatted_data;
            $this->fetchEmails();
        }

        return $this;
    }

    function fetchEmails()
    {
        foreach ($this->entry_data as $key => $val) {
            if(is_valid_email($val))
                $this->emails[] = $val;
        }
    }

    function get($id)
    {
        global $wpdb;
        $dbrow = $wpdb->get_row("select * from {$this->dbtable} where id = '{$id}'");
        return new FormEntry($dbrow);
    }

    function getByToken($token = null)
    {
        global $wpdb;
        $token = $token ? $token : Session::get('wplf_entry_token');
        $entry = $wpdb->get_row("select * from {$this->dbtable} where token = '{$token}'");
        return new FormEntry($entry);
    }

    function delete($id)
    {
        global $wpdb;
        if(!is_array($id))
            return $wpdb->delete($this->dbtable, ['id' => $id]);
        else {
            $id = wplf_sanitize_array($id, ['validate' => 'int']);
            $wpdb->query("delete from {$this->dbtable} where id in (" . implode(',', $id) . ")");
        }
    }

    function validateToken()
    {
        global $wpdb;
        $token = wplf_query_var('wplf-token');
        $entry = $this->getByToken($token);
        if($entry->id) {
            Session::set('wplf_entry_token', $token);
            wp_send_json(['success' => true, 'entry' => $entry, 'replies' => LiveForms()->entryReplies->getAll($entry->id)]);
        }
        wp_send_json(['success' => false, 'message' => esc_attr__( 'Invalid Toeken', LF_TEXT_DOMAIN )]);
    }

    function logout()
    {
        Session::clear('wplf_entry_token');
        wp_send_json(['success' => true]);
    }

}
