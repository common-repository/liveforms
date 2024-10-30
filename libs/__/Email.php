<?php
/**
 * Email Handler Class for Live Forms
 * Since: v4.0.0
 * Author: Shahjada
 */

namespace LiveForms\__;

class Email
{

    function __construct()
    {

    }

    public static function template($id = 'default')
    {
        $file = LF_BASE_DIR . "assets/email-templates/{$id}.html";
        $file = file_exists($file) ? $file : LF_BASE_DIR . "assets/email-templates/default.html";
        $template = file_get_contents($file);
        return $template;
    }

    public function compile($params, $form_data)
    {
        foreach ($params as &$value){
            $value = wplf_compile_email_message($value, $form_data);
        }
        return $params;
    }

    public function prepare($template, $params)
    {
        $template_data = self::template($template);

        $logo = get_site_icon_url();
        if(!isset($params['logo']) || $params['logo'] === '') $params['logo'] = $logo;
        $banner = isset($params['banner']) ? esc_url($params['banner']) : '';
        $params['date'] = wp_date(get_option('date_format'), time());
        $params['site_url'] = home_url('/');
        $params['site_name'] = get_option('blogname');
        $params['site_tagline'] = get_option('blogdescription');
        $params['footer_text'] = get_option('blogname');
        $params['img_logo'] = isset($params['logo']) && $params['logo'] != '' ? "<img style='max-width: 60%;max-height: 96px' src='{$params['logo']}' alt='" . esc_attr(get_option('blogname')) . "' />" : '<h2>'.esc_attr(get_option('blogname')).'</h2>';
        $body = LiveForms()->template->fetch($template_data, $params);
        $email = [
            'send_to' => wplf_valueof($params, 'send_to'),
            'from_email' => wplf_valueof($params, 'from_email'),
            'from_name' => wplf_valueof($params, 'from_name'),
            'subject' => wplf_valueof($params, 'subject'),
            'body' => $body
        ];
        return $email;
    }

    public function send($params)
    {
        $template = LiveForms()->settings->general->email_template;
        $email = $this->prepare($template, $params);
        $headers[] = "From: " . $email['from_name'] . " <" . $email['from_email'] . ">";
        $headers[] = "Content-type: text/html";
        if (!isset($email['send_to'])) {
            $email['send_to'] = get_option('admin_email');
        }
        $headers = apply_filters("wplf_email_headers" , $headers, $params);
        if (isset($params['cc'])) {
            $headers[] = "CC: {$params['cc']}";
            unset($params['cc']);
        }
        if (isset($params['bcc'])) {
            $headers[] = "Bcc: {$params['bcc']}";
            unset($params['bcc']);
        }
        $attachments = wplf_valueof($params, 'attachments'); //apply_filters("wplf_email_attachments", wplf_valueof($params, 'attachments'), $params);
        if(is_email($email['send_to']))
            return wp_mail($email['send_to'], $email['subject'], $email['body'], $headers, $attachments);
    }

    /*public function sendDirect($id, $email)
    {

        $headers[] = "From: " . $email['from_name'] . " <" . $email['from_email'] . ">";
        $headers[] = "Content-type: text/html";
        if (!isset($email['send_to'])) {
            $email['to_email'] = get_option('admin_email');
        }
        $headers = apply_filters("wplf_email_headers_" . str_replace("-", "_", $id), $headers);
        if (isset($email['cc'])) {
            $headers[] = "CC: {$email['cc']}";
            unset($email['cc']);
        }
        if (isset($email['bcc'])) {
            $headers[] = "Bcc: {$email['bcc']}";
            unset($email['bcc']);
        }
        $message = self::template($this);
        $message
        $attachments = apply_filters("wplf_email_attachments_" . str_replace("-", "_", $id), array(), $email);
        if(is_email($email['send_to']))
            return wp_mail($email['send_to'], $email['subject'], $message, $headers, $attachments);
    }*/

}

