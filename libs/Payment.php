<?php
namespace LiveForms;

use LiveForms\PaymentMethods\PayPal\PayPal;

class Payment {

	public $params;

	function __construct() {

	}

	static function getMethods()
    {
        $payment_methods = ['PayPal' => new PayPal()];
        $payment_methods = apply_filters("wplf_payment_method", $payment_methods);
        return $payment_methods;
    }

	public static function add($form_id, $entry_id, $amount, $currency, $payment_method, $payment_status = 'Processing'){
	    global $wpdb;
	    $wpdb->insert("{$wpdb->prefix}liveforms_payments", array(
	        'form_id' => (int)$form_id,
	        'entry_id' => (int)$entry_id,
	        'amount' => (double)$amount,
	        'currency' => sanitize_text_field($currency),
	        'payment_method' => sanitize_text_field($payment_method),
	        'payment_status' => sanitize_text_field($payment_status),
	        'date' => time(),
        ));
	    return $wpdb->insert_id;
    }

    public static function get($entry_id){
	    global $wpdb;
	    $entry_id = (int)$entry_id;
	    return $wpdb->get_row("select * from {$wpdb->prefix}liveforms_payments where entry_id = '$entry_id'");
    }

    public static function complete($entry_id, $transaction_id, $payment_data){
	    global $wpdb;
	    $wpdb->update("{$wpdb->prefix}liveforms_payments", array('payment_status' => 'Completed', 'transaction_id' => $transaction_id, 'payment_data' => json_encode($payment_data)), array('entry_id' => $entry_id));
    }

    public static function total($form_id){
	    global $wpdb;
	    $form_id = (int)$form_id;
        return $wpdb->get_var("select sum(amount) from {$wpdb->prefix}liveforms_payments where form_id = '$form_id' and payment_status='Completed'");
    }

    function getPaymentMethodObject($ID) {
	    $methods = self::getMethods();
	    return wplf_valueof($methods, $ID);
    }

	public function pay($params) {
		$payment_class = $params['method'];
		$pay_object = self::getPaymentMethodObject($params['method']);
        $notify_url = add_url_fragment( home_url('/'), array('paymethod' => $payment_class, 'form_id' => $params['form_id'], 'field_id' => $params['field_id'], 'entry_id' => $params['entry_id']) );
        $params['__payment_notify_url'] = $notify_url;
        return $pay_object->ShowPaymentForm($params, 1);
	}

	function verifyPayment() {
        $PaymentMethodID = wplf_query_var('validatepayment');
		if ($PaymentMethodID) {
            $field_id = wplf_query_var('field_id');
		    $form_id = wplf_query_var('form_id', 'int');
		    $entry_id = wplf_query_var('entry_id', 'int');
            $form_settings = get_post_meta($form_id, 'form_data', $single = true);
            $payment_settings = wplf_valueof($form_settings,"fieldsinfo/$field_id/paymethods/$PaymentMethodID");
			$payment = self::getPaymentMethodObject($PaymentMethodID);
			if(!is_object($payment)) wp_send_json(['success' => false, 'message' => "Invalid payment method ( $PaymentMethodID )"]);
			$submission_id = wplf_query_var('entry_id', 'int');
			$submission_method_custom_params = method_exists($payment, 'getCustomVars')?$payment->getCustomVars():null;
			$creds_data = $this->fetchFormData($submission_id);
			$payment->loadConfig($payment_settings);
			$payment_data = $payment->verifyNotification($payment_settings);
			
			if ($payment_data) {
			    self::complete($submission_id, wplf_query_var('id'), $payment_data);
				$this->send_emails($creds_data);
                wp_send_json(['success' => true, 'redirect' => add_query_arg(['thanks' => 1], $payment_settings['returnurl'])]);
			}
            wp_send_json(['success' => false]);
		}
	}

	function fetchFormData($submit_id, $table_name = 'liveforms_conreqs') {
		global $wpdb;
		$query = "select * from {$wpdb->prefix}{$table_name} where `id`='{$submit_id}'";
		$submission = $wpdb->get_row($query, ARRAY_A);

		$form_id = $submission['fid'];
		$form_settings = get_post_meta($form_id, 'form_data', true);

		$form_creds = array(
			'submit_id' => $submit_id,
			'form_id' => $form_id,
			'submission_details' => $submission,
			'form_data' => $form_settings,
		);

		return $form_creds;
	}

	public function get_field_names($ef_data, $ef_form_data) {
		$ef_data = maybe_unserialize($ef_data);
		$ef_form_data = maybe_unserialize($ef_form_data);
		$ef_prep_fields = array();

		foreach($ef_data as $ef_name => $ef_value) {
			$ef_prep_fields[$ef_name] = $ef_form_data['fieldsinfo'][$ef_name]['label'];
		}

		return $ef_prep_fields;
	}

	public function send_emails($form_creds) {

		$form_id = $form_creds['form_id'];
		$form_data = $form_creds['form_data'];

		//Preparing Email
		//Fetching user infos for email
		$form_agent_id = $form_data['agent'];
		if($form_agent_id > 0) {
            $form_agent_info = get_userdata($form_agent_id);
            if (is_object($form_agent_info))
                $form_agent_email = $form_agent_info->user_email;
        }


		$token = $form_creds['submission_details']['token'];
		$data = maybe_unserialize($form_creds['submission_details']['data']);
		$emails = $this->entry_has_emails(maybe_unserialize($data));

		// Prepare entry data for email template injection
		$email_template_data = array_merge(array('fid' => $form_id, 'status' => 'new', 'token' => $token), maybe_unserialize($data));

		$field_names_for_email = $this->get_field_names($data, $form_data);

		//to user
		$site_name = get_bloginfo('name');
		$user_email_text = isset($form_data['email_text']) ? $form_data['email_text'] : "Thanks for your visit to {$site_name}. We are glad that you contacted with us. ";
		$user_email_data['subject'] = "[{$site_name}] Thanks, your payment was successful";
		$user_email_data['message'] = "{$user_email_text} To gain further access to your submitted request, use this token: [ {$token} ]<br/>Submission details:<br/>";
		foreach (maybe_unserialize($data) as $field_name => $entry_value) {
			if (!is_string($entry_value)) $entry_value = get_concatenated_string($entry_value);

			$user_email_data['message'] .= "{$field_names_for_email[$field_name]}: {$entry_value}<br/>";
		}
		$user_email_data['to'] = $emails;
		$user_email_data['from_email'] = get_option('admin_email');
		$user_email_data['from_name'] = get_bloginfo('name');

		$user_email_data = apply_filters('user_email_payment_data', $user_email_data, $form_id, maybe_unserialize($email_template_data));
		$headers = "{$user_email_data['from_name']} <{$user_email_data['from_email']}>\r\n";
		$headers .= "Content-type: text/html";
		if (isset($user_email_data['subject']) || isset($user_email_data['message'])) {
			foreach ($user_email_data['to'] as $email) {
				wp_mail($email, $user_email_data['subject'], $user_email_data['message'], $headers);
			}
		}

		//to form admin
		$admin_email_data['subject'] = "[{$site_name}] Payment succeeded";
		$admin_email_data['message'] = "New payment succeeded from you site {$site_name}.<br/>Submission details:<br/>";
		foreach (maybe_unserialize($data) as $field_name => $entry_value) {
			if (!is_string($entry_value)) $entry_value = get_concatenated_string($entry_value);

			$admin_email_data['message'] .= "{$field_names_for_email[$field_name]}: {$entry_value}<br/>";
		}
		$admin_email_data['to'] = wplf_valueof($form_creds, 'form_data/admin_email/send_to');
		$admin_email_data['from_email'] = wplf_valueof($form_creds, 'form_data/admin_email/from_email');;
		$admin_email_data['from_name'] = wplf_valueof($form_creds, 'form_data/admin_email/from_name');;
		$admin_email_data = apply_filters('admin_email_payment_data', $admin_email_data, $form_id, maybe_unserialize($email_template_data));
		$headers = "{$admin_email_data['from_name']} <{$admin_email_data['from_email']}>\r\n";
		$headers .= "Content-type: text/html";
		wp_mail($admin_email_data['to'], $admin_email_data['subject'], $admin_email_data['message'], $headers);

		//to form agent
		/*if ($form_agent_id) {
			$agent_email_data['subject'] = "[{$site_name}] Payment recieved";
			$agent_email_data['message'] = "Succesfully recieved payment to {$site_name} through a form you have been assigned to. Please check back.<br/>Submission details:<br/>";
			foreach (maybe_unserialize($data) as $field_name => $entry_value) {
				if (!is_string($entry_value)) $entry_value = get_concatenated_string($entry_value);

				$agent_email_data['message'] .= "{$field_names_for_email[$field_name]}: {$entry_value}<br/>";
			}
			$agent_email_data['to'] = $form_agent_email;
			$agent_email_data['from_email'] = $from_email;
			$agent_email_data['from_name'] = $from_name;
			$agent_email_data = apply_filters('agent_email_payment_data', $agent_email_data, $form_id, maybe_unserialize($email_template_data));
			$headers = "{$agent_email_data['from_name']} <{$agent_email_data['from_email']}>\r\n";
			$headers .= "Content-type: text/html";
			wp_mail($agent_email_data['to'], $agent_email_data['subject'], $agent_email_data['message'], $headers);
		}*/
	}

	function entry_has_emails($data) {
		$emails = array();
		if (!is_array($data))
			return $emails;
		foreach ($data as $value) {
			if (is_valid_email($value))
				$emails[] = $value;
		}
		return $emails;
	}

}
