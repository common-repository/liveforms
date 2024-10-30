<?php

namespace LiveForms\Form;


use LiveForms\__\__;
use LiveForms\__\Crypt;
use LiveForms\__\FileSystem;
use LiveForms\Payment;

class SubmitHandler
{
    function __construct()
    {
        add_action('init', array($this, 'process'));
    }

    /**
     * @function process_form_submission
     * @uses Submit form using AJAX
     */
    public function process()
    {

        if (__::is_ajax() && isset($_REQUEST['action']) && $_REQUEST['action'] == 'submit_form' && isset($_REQUEST['__isliveforms'])) {

            /*if (!wp_verify_nonce($_REQUEST['__isliveforms'], NONCE_KEY)) {
                $return_data = array();
                $return_data['message'] = apply_filters("liveform_submitform_error_message", 'Invalid Data!');
                $return_data['action'] = 'danger';
                wp_send_json($return_data);
            }*/

            $form_id = __::query_var('form_id', 'int');
            $form_validator = __::query_var('form_validator', 'txt');

            if($form_id !== (int)Crypt::decrypt($form_validator)) {
                $return_data = array();
                $return_data['message'] = apply_filters("liveform_submitform_error_message", __('Something went wrong! Looks like form is manipulated externally!', 'liveforms'));
                $return_data['action'] = 'error';
                $return_data['ids'] = Crypt::decrypt($form_validator);
                wp_send_json($return_data);
            }

            $field_types = Crypt::decrypt($_REQUEST['fields'], true);
            $data = isset($_REQUEST['submitform']) ? $_REQUEST['submitform'] : array();

            $form_data = get_post_meta($form_id, 'form_data', $single = true);

            $data_validation = $this->validateData($data, $field_types, $form_data['fieldsinfo']);

            if (!$data_validation->success) {
                $return_data = array();
                $return_data['message'] = apply_filters("liveform_submitform_error_message", __('Please recheck your input data. The following error(s) found:', 'liveforms') . "<ul><li>" . implode("</li><li>", $data_validation->errors) . "</li></ul>");
                $return_data['action'] = 'error';
                wp_send_json($return_data);
            }

            // Update the submit count for this form
            $this->recordStat($form_id, get_client_ip());

            $file_paths = $this->uploadFiles($form_data);

            if (count($file_paths)) {
                $data = array_merge($data, $file_paths);
            }

            $data = serialize($data);
            $token = uniqid();
            $emails = $this->parseEmailFields(maybe_unserialize($data));
            $form_entry = array('uid' => get_current_user_id(), 'data' => $data, 'fid' => $form_id, 'status' => 'new', 'token' => $token, 'time' => time());

            $form_entry = apply_filters("liveform_before_form_submit", $form_entry);

            do_action("liveform_before_form_submit", $form_entry);

            global $wpdb;

            // Insert the request into the database
            $noentry = __::valueof($form_data, 'nodbentry', ['validate' => 'int']);
            $submission_id = 0;
            if(!$noentry) {
                $wpdb->insert(
                    "{$wpdb->prefix}liveforms_conreqs", $form_entry
                );

                $submission_id = $wpdb->insert_id;
            }

            do_action("liveform_after_form_submitted", $form_entry, $submission_id);

            //Preparing Email
            $form_emails = get_post_meta($form_id, 'form_emails', true);
            $dataset = wplf_query_var('submitform', ['validate' => 'html']);
            $dataset += ['admin_email' => get_option('admin_email'), 'sitename' => get_option('blogname')];
            $dataset['form_entry_data'] = $this->formattedEntryData(__::query_var('submitform', ['validate' => 'html']), __::valueof($form_data, 'fieldsinfo'));
            $dataset['entry_identifier'] = $token;
            $dataset['reply_link'] = $submission_id ? "<a class=\"__button\" href='".admin_url("edit.php?post_type=form&page=form-entries&form_id={$form_id}&req_id={$submission_id}")."'>".esc_attr__( 'Reply', LF_TEXT_DOMAIN )."</a>" : '';
            foreach ($_REQUEST as $key => $value) {
                $dataset["req_{$key}"] = $value;
            }
            foreach ($_SERVER as $key => $value) {
                $dataset["SRV_{$key}"] = $value;
            }
            if (is_user_logged_in()) {
                global $current_user;
                $dataset['user_email'] = $current_user->user_email;
                $dataset['user_name'] = $current_user->display_name;
            }
            if(__::valueof($form_data, 'no_receipt', ['validate' => 'int']) === 0) {
                $admin_notify = $form_data['admin_email'];
                $email = LiveForms()->email->compile($admin_notify, $dataset);
                $email['subject'] = sprintf(__("[ %s ► %s ] You have a new query", "liveforms"), get_option('blogname'), get_the_title($form_id));;
                if($file_paths)
                    $email['attachments'] = array_values($file_paths);
                LiveForms()->email->send($email);
            }
            //Change reply link for users
            $query_page_id = get_option('__wplf_query_status_page');
            $dataset['reply_link'] = $query_page_id && $submission_id ? "<a class=\"__button\" href='".add_query_arg(['wplf-token' => $token], get_permalink($query_page_id))."'>".esc_attr__( 'Reply', LF_TEXT_DOMAIN )."</a>" : "";

            if (is_array($form_emails)) {
                foreach ($form_emails as $email_id => $form_email) {
                    $email = LiveForms()->email->compile($form_email, $dataset);
                    LiveForms()->email->send($email);
                }
            }


            // Increment the form submit count by 1
            // $this->form_submit_count($form_id);


            $data = maybe_unserialize($data);
            if ($this->hasPaymentFields($data)) {
                $pay_details = $this->parsePaymentFields($data);
                $payment_field = $pay_details['field'];
                $amount = (double)$form_data['fieldsinfo'][$payment_field]['amount'];
                if (isset($form_data['fieldsinfo'][$payment_field]['donation_field']) && $form_data['fieldsinfo'][$payment_field]['donation_field'] == 1 && wplf_query_var('__amount', 'double') > 0)
                    $amount = wplf_query_var('__amount', 'double');
                $payment_data = array(
                    'form_id' => $form_id,
                    'entry_id' => $submission_id,
                    'field_id' => $payment_field,
                    'method' => $pay_details['method'],
                    'amount' => $amount,
                    'currency' => $form_data['fieldsinfo'][$payment_field]['currency'],
                    'extraparams' => $submission_id,
                    'methodparams' => $form_data['fieldsinfo'][$payment_field]['paymethods'][$pay_details['method']]
                );

                $pay_object = new Payment();
                Payment::add($form_id, $submission_id, $amount, $form_data['fieldsinfo'][$payment_field]['currency'], sanitize_text_field($pay_details['method']));
                $jdata['paymentform'] = $pay_object->pay($payment_data);
                $jdata['action'] = 'payment';
                wp_send_json($jdata);
            }

            $return_data = array();
            $thank_you_msg = stripslashes($form_data['thankyou']);
            $thank_you_msg = LiveForms()->template->fetch($thank_you_msg, $dataset);
            $return_data['message'] = apply_filters("liveform_submitform_thankyou_message", $thank_you_msg);
            $return_data['action'] = 'success';
            if ($form_data['redirect_to'] === 'page') {
                $return_data['redirect_to'] = get_permalink($form_data['redirect_to_page']);
            }
            if ($form_data['redirect_to'] === 'url') {
                $return_data['redirect_to'] = $form_data['redirect_to_url'];
            }
            wp_send_json($return_data);
        }
    }

    function validateData($data, $field_types, $form_data)
    {
        $validation['success'] = true;
        $validation['errors'] = [];
        foreach ($data as $field_index => $field_value) {
            $field_type = wplf_valueof($field_types, $field_index);
            if($field_type && class_exists($field_type)) {
                $field = new $field_type();
                if (method_exists($field, 'validate_field')) {
                    if (!$field->validate_field($field_index, $field_value, wplf_valueof($form_data, $field_index))) {
                        $validation['success'] = false;
                        $validation['errors'][] = $field->validation_error;
                    }
                }
            }

        }

        $validation = json_encode($validation);
        return json_decode($validation);
    }




    /**
     * @function entry_has_emails
     * @return type formatted array of string
     * @uses Check if form submission (form structure) has any email fields or not
     * @returns List of emails submitted via the form
     */
    function parseEmailFields($data)
    {
        $emails = array();
        if (!is_array($data))
            return $emails;
        foreach ($data as $value) {
            if (is_valid_email($value))
                $emails[] = $value;
        }
        return $emails;
    }


    /**
     * @function has_payment_fields
     * @return boolean
     * @uses Checks if submission has any pay methods
     */
    function hasPaymentFields($submission)
    {
        foreach ($submission as $key => $value) {
            if (substr_count($key, 'PaymentMethods_') && $value != '') {
                return true;
            }
        }

        return false;
    }

    function parsePaymentFields($submission)
    {
        $payment_fields = array();
        foreach ($submission as $key => $value) {
            if (strstr($key, 'aymentMethods')) {
                $payment_fields = array('field' => $key,
                    'method' => $submission[$key]
                );
                return $payment_fields;
            }
        }

        return null;
    }


    function uploadFiles($form_data)
    {
        $file_paths = array();
        if (count($_FILES)) {
            foreach ($_FILES['upload']['name'] as $file_index => $file_name) {
                $prepend_key = uniqid("liveforms_", $more_entropy = true) . '_';
                if (!file_exists(LF_UPLOAD_PATH)) {
                    mkdir(LF_UPLOAD_PATH);
                    if (class_exists('\WPDM\FileSystem'))
                        FileSystem::blockHTTPAccess(LF_UPLOAD_PATH);
                }
                $new_path = LF_UPLOAD_PATH . $prepend_key . $file_name;

                $ext = explode(".", $file_name);
                $ext = end($ext);
                $ext = strtolower($ext);
                $allowed_exts = strtolower($form_data['fieldsinfo'][$file_index]['extensions']);
                $allowed_exts = explode(",", $allowed_exts);
                $unsafe_file_exts = array('php', 'js', 'html');
                $unsafe_file_exts = apply_filters("liveforms_blocked_file_exts", $unsafe_file_exts);

                if (!in_array($ext, $unsafe_file_exts) && in_array($ext, $allowed_exts)) {
                    move_uploaded_file($_FILES['upload']['tmp_name'][$file_index], $new_path);
                    $file_paths[$file_index] = $new_path;
                } else {
                    $return_data = array();
                    $return_data['message'] = apply_filters("liveform_submitform_error_message", __('Invalid File Type', 'liveforms'));
                    $return_data['action'] = 'error';
                    wp_send_json($return_data);
                }

                $filesize = $_FILES['upload']['size'][$file_index];
                $filesize = $filesize / 1048576;
                if ($filesize > ($form_data['fieldsinfo'][$file_index]['filesize'])) {
                    $return_data = array();
                    $return_data['message'] = apply_filters("liveform_submitform_error_message", __(sprintf('Max allowed file size: %d MB, You tried to upload: > %d MB', $form_data['fieldsinfo'][$file_index]['filesize'], (int)(($_FILES['upload']['size'][$file_index] / 1048576))), 'liveforms'));
                    $return_data['action'] = 'error';
                    wp_send_json($return_data);
                }

            }
        }
        return $file_paths;
    }

    /**
     * Record and increment the submission count of a form by 1 and store the ip used
     * @param $form_id
     * @param string $ip
     */
    function recordStat($form_id, $ip = 'not acquired')
    {
        global $wpdb;
        $form_data = get_post($form_id);
        $form_author_id = $form_data->post_author;
        $submit_count = get_post_meta($form_id, 'submit_count', true);
        if ($submit_count == '') {
            $submit_count = 0;
        }
        update_post_meta($form_id, 'submit_count', $submit_count + 1);

        $current_time = time();
        $wpdb->query("INSERT into {$wpdb->prefix}liveforms_stats SET `fid`='{$form_id}', `author_id`='{$form_author_id}', `action`='s', `ip`='{$ip}', `time`='{$current_time}' ");
    }

    function formattedEntryData($entry, $form)
    {
        $html = "<table style='border:0;width:100%;background: #fcfcfc'>";
        foreach ($entry as $key => $value){
            $label = __::valueof($form, "{$key}/label");
            if(is_array($value)) $value = implode(", ", $value);
            $html .= "<tr><td style='padding: 10px;border-bottom: 1px solid #dddddd'>{$label}</td><td style='padding: 10px;border-bottom: 1px solid #dddddd'>{$value}</td></tr>";
        }
        $html .= "</table>";
        return apply_filters("liveform_formatted_entry_data", $html, $entry, $form);
    }
}
