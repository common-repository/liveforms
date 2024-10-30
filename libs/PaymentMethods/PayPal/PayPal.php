<?php
namespace LiveForms\PaymentMethods\PayPal;

global $methods_set;

class PayPal {
    var $mode;

    public $gatewayURL = "https://www.Paypal.com/cgi-bin/webscr";
    public $gatewayURL_TestMode = "https://www.sandbox.Paypal.com/cgi-bin/webscr";
    public $label = 'PayPal';
    public $business;
    public $returnurl;
    public $notifyurl;
    public $cancelurl;
    public $custom;
    public $enabled;
    public $currency;
    public $order_id;
    public $client_id;
    public $client_secret;


    function __construct($options = array()){

        global $wpdmpp_settings; /* To use premium package add-on cofiguration option by default when no value is set */

        $this->mode = wplf_valueof($options, 'mode', ['default' => 'sandbox']);
        $this->email = wplf_valueof($options, 'email', ['default' => wplf_valueof($wpdmpp_settings,'Paypal/Paypal_email')]);
        $this->cancelurl = wplf_valueof($options, 'cancelurl', ['default' => 'sandbox']);
        $this->returnurl = wplf_valueof($options, 'returnurl', ['default' => 'sandbox']);
        $this->webhookurl = wplf_valueof($options, 'webhookurl', ['default' => 'sandbox']);
        $this->imageurl = wplf_valueof($options, 'imageurl', ['default' => 'sandbox']);
        $this->client_id = wplf_valueof($options, 'client_id', ['default' => wplf_valueof($wpdmpp_settings,'Paypal/client_id')]);
        $this->client_secret = wplf_valueof($options, 'client_secret', ['default' => wplf_valueof($wpdmpp_settings,'Paypal/client_secret')]);
        $this->label = "<img style='margin: 0;width: 80px;' src='".plugins_url('liveforms/assets/images/paypal.svg')."' alt='PayPal' />";
    }


    public function configOptions($cache = array()){
       $enabled = 'checked="checked"';
	   $op_mode = (isset($cache['mode']) ? $cache['mode'] : '');

        $options = array(

            'mode' => array(
                'label' => __("Payment Mode:", "liveforms"),
                'type' => 'select',
                'options' => array('live' => 'Live', 'sandbox' => 'Test'),
                'selected' => $this->mode
            ),
            /*'email' => array(
                'label' => __("Paypal Email:", "liveforms"),
                'type' => 'text',
                'placeholder' => '',
                'value' => $this->email
            ),*/
            'client_id' => array(
                'label' => __("Client ID:", "liveforms"),
                'type' => 'text',
                'placeholder' => '',
                'value' => wplf_valueof($cache, 'client_id')
            ),
            'client_secret' => array(
                'label' => __("Client Secret:", "liveforms"),
                'type' => 'text',
                'placeholder' => '',
                'value' => wplf_valueof($cache, 'client_secret')
            ),
            'cancelurl' => array(
                'label' => __("Cancel URL:", "liveforms"),
                'type' => 'text',
                'placeholder' => '',
                'value' => wplf_valueof($cache, 'cancelurl')
            ),
            'returnurl' => array(
                'label' => __("Return URL:", "liveforms"),
                'type' => 'text',
                'placeholder' => '',
                'value' => wplf_valueof($cache, 'returnurl')
            ),

            'webhookurl' => array(
                'label' => __("WebHook URL:", "liveforms"),
                'type' => 'text',
                'placeholder' => '',
                'value' => $this->webhookurl
            ),


            'imageurl' => array(
                'label' => __("Checkout Page Logo URL:", "liveforms"),
                'type' => 'text',
                'placeholder' => '150x50 px',
                'value' => $this->imageurl
            ),
        );
        return $options;
    }

	/**
	 * Payment form for Paypal. Fired after form submission
	 * @param type $submission : Holds submitted form related data
	 * @param type $AutoSubmit : Whether or not redirect to the payment gateway automatically
	 * @return string : HTML for the Form for Paypal
	 */
    function showPaymentForm($submission, $AutoSubmit = 0){
        if($AutoSubmit==1) $hide = "display:none;'";

        $invoice_no = uniqid();
		if ($submission['methodparams']['mode'] == 'sandbox') {
			$this->gatewayURL = $this->gatewayURL_TestMode;
		}
		$env = $submission['methodparams']['mode'];
		$amount = number_format($submission['amount'], 2); // two decimal points
		$custom = base64_encode($submission['extraparams'].'|'.$submission['methodparams']['mode']);
        //$email = $submission['methodparams']['email'] != ''?$submission['methodparams']['email']:'';
        $client_id = wplf_valueof($submission, 'methodparams/client_id');
        $currency = wplf_valueof($submission, 'currency');
        $returnurl = $submission['methodparams']['returnurl'] != ''?$submission['methodparams']['returnurl']:home_url('/');
        $cancelurl = $submission['methodparams']['cancelurl'] != ''?$submission['methodparams']['cancelurl']:home_url('/');
        $notify_url = add_url_fragment( home_url('/'), array('validatepayment' => 'PayPal', 'form_id' => $submission['form_id'], 'field_id' => $submission['field_id'], 'entry_id' => $submission['entry_id']) );
        ob_start();
        ?>
        <div class="panel panel-default">
            <div class="panel-heading"><?= esc_attr__( 'Please complete payment', LF_TEXT_DOMAIN ); ?></div>
            <div class="panel-body">
                <div id="#wplf-paypal-button-container"></div>
            </div>
        </div>
        <!-- Include the PayPal JavaScript SDK -->

        <script>
            jQuery(function ($){
                $.getScript('https://www.paypalobjects.com/api/checkout.js', function () {

                    paypal.Button.render({

                        env: '<?php echo $env; ?>',


                        style: {
                            layout: 'horizontal',  /* horizontal | vertical */
                            size: 'medium',    /* medium | large | responsive  */
                            shape: 'rect',      /* pill | rect  */
                            color: 'blue',       /* gold | blue | silver | white | black  */
                            label: 'checkout',
                            tagline: false
                        },


                        funding: {
                            allowed: [
                                paypal.FUNDING.CARD

                            ],
                            disallowed: [paypal.FUNDING.CREDIT]
                        },
                        client: {
                            sandbox: '<?php echo $client_id; ?>',
                            production: '<?php echo $client_id; ?>'
                        },

                        payment: function (data, actions) {
                            return actions.payment.create({
                                payment: {
                                    transactions: [
                                        {
                                            amount: {
                                                total: '<?php echo $amount; ?>',
                                                currency: '<?php echo $currency; ?>'
                                            },
                                            description: 'Thanks for you donation!',
                                            custom: '<?php echo $submission['form_id'].'_'.$submission['entry_id']; ?>'
                                        }
                                    ]
                                }
                            });
                        },

                        onAuthorize: function (data, actions) {
                            return actions.payment.execute()
                                .then(function (response) {
                                    //console.log(response);
                                    $('#formarea').append("<div class='alert alert-info'>Registering Your Payment...</div>");
                                    $.post('<?php echo $notify_url; ?>', response, function (res) {
                                        console.log(res);
                                        if (res.success === true) {
                                            $('#formarea').append("<div class='alert alert-success'>Redirecting...</div>");
                                            location.href = '<?= add_query_arg(['thanks' => 1], $returnurl) ?>';
                                        }
                                    });
                                });
                        }
                    }, '#wplf-paypal-button-container');

                });
            });
        </script>
        <?php
        return ob_get_clean();
        /*
        $Form = "
                    <form method='post' style='margin:0px;' name='_wpdm_bnf_{$invoice_no}' id='_wpdm_bnf' target='_parent' action='{$this->gatewayURL}'>

                    <input type='hidden' name='business' value='{$email}' />

                    <input type='hidden' name='cmd' value='_xclick' />
                    <!-- the next three need to be created -->
                    <input type='hidden' name='return' value='{$returnurl}' />
                    <input type='hidden' name='cancel_return' value='{$cancelurl}' />
                    <input type='hidden' name='notify_url' value='{$notify_url}' />
                    <input type='hidden' name='rm' value='2' />
                    <input type='hidden' name='currency_code' value='{$submission['currency']}' />
                    <input type='hidden' name='lc' value='US' />
                    <input type='hidden' name='bn' value='toolkit-php' />

                    <input type='hidden' name='cbt' value='Continue' />

                    <!-- Payment Page Information -->
                    <input type='hidden' name='no_shipping' value='' />
                    <input type='hidden' name='no_note' value='1' />
                    <input type='hidden' name='cn' value='Comments' />
                    <input type='hidden' name='cs' value='' />

                    <!-- Product Information -->
                    <input type='hidden' name='item_name' value='' />
                    <input type='hidden' name='amount' value='{$amount}' />

                    <input type='hidden' name='quantity' value='1' />
                    <input type='hidden' name='item_number' value='{$invoice_no}' />
                    <input type='hidden' name='email' value='' />
                    <input type='hidden' name='custom' value='{$custom}' />

                    <!-- Shipping and Misc Information -->

                    <input type='hidden' name='invoice' value='{$invoice_no}' />

                    <noscript><p>Your browser doesn't support Javscript, click the button below to process the transaction.</p>
                    <a style=\"{$hide}\" href=\"#\" onclick=\"jQuery('#_wpdm_bnf').submit();return false;\">Buy Now</a>                    </noscript>
                    </form>


        ";

        if($AutoSubmit==1)
        $Form .= "Proceeding to Paypal....<script language=javascript>setTimeout('jQuery(\"#_wpdm_bnf\").submit();',2000);</script>";

        return $Form;
        */
    }

    function verifyNotification()
    {

        global $current_user;

        //Verify regular checkout payment
        $payment = $this->paymentDetails(wplf_query_var('id'));

        if ($payment->state === 'approved') {
            list($form_id, $entry_id) = explode("_", $payment->transactions[0]->custom);
            $payment_amount = $payment->transactions[0]->amount->total;
            return $payment;
        }

        return false;


    }

    function loadConfig($config)
    {
        global $wpdmpp_settings; /* To use premium package add-on cofiguration option by default when no value is set */

        $this->mode = wplf_valueof($config, 'mode', ['default' => 'sandbox']);
        $this->email = wplf_valueof($config, 'email', ['default' => wplf_valueof($wpdmpp_settings,'Paypal/Paypal_email')]);
        $this->cancelurl = wplf_valueof($config, 'cancelurl', ['default' => 'sandbox']);
        $this->returnurl = wplf_valueof($config, 'returnurl', ['default' => 'sandbox']);
        $this->webhookurl = wplf_valueof($config, 'webhookurl', ['default' => 'sandbox']);
        $this->imageurl = wplf_valueof($config, 'imageurl');
        $this->client_id = wplf_valueof($config, 'client_id', ['default' => wplf_valueof($wpdmpp_settings,'Paypal/client_id')]);
        $this->client_secret = wplf_valueof($config, 'client_secret', ['default' => wplf_valueof($wpdmpp_settings,'Paypal/client_secret')]);
        $this->label = "<img style='margin: 0;width: 80px;' src='".plugins_url('liveforms/assets/images/paypal.svg')."' alt='PayPal' />";
    }

    function getAccessToken()
    {

        $headers = array();
        $env = $this->mode == 'sandbox' ? 'sandbox' : 'production';
        //if(current_user_can('manage_options')) $env = 'sandbox';

        $apidomain = $env === 'sandbox' ? 'api.sandbox.paypal.com' : 'api.paypal.com';

        $auth = base64_encode($this->client_id . ':' . $this->client_secret);
        $headers['Accept'] = "application/json";
        $headers['Accept-Language'] = "en_US";
        $headers['Content-Type'] = "application/x-www-form-urlencoded";
        $headers['Authorization'] = "Basic $auth";

        $body['grant_type'] = 'client_credentials';


        $args['body'] = $body;
        $args['headers'] = $headers;

        $data = wp_remote_post("https://{$apidomain}/v1/oauth2/token", $args);
        return json_decode($data['body'])->access_token;

    }

    function paymentDetails($payID)
    {

        $env = $this->mode == 'sandbox' ? 'sandbox' : 'production';
        //if(current_user_can('manage_options')) $env = 'sandbox';
        $apidomain = $env === 'sandbox' ? 'api.sandbox.paypal.com' : 'api.paypal.com';

        $accessToken = $this->getAccessToken();
        $url = "https://{$apidomain}/v1/payments/payment/{$payID}";
        $headers['Accept'] = "application/json";
        $headers['Accept-Language'] = "en_US";
        $headers['Content-Type'] = "application/json";
        $headers['Authorization'] = "Bearer $accessToken";
        $args['headers'] = $headers;
        $data = wp_remote_get($url, $args);

        $data = json_decode($data['body']);
        //wp_send_json($data);
        return $data;

    }

	public function GetExtraParams() {
	   if (isset($_POST) && isset($_POST['custom'])) {
		   $custom_params = explode('|',base64_decode(esc_attr($_POST['custom'])));
		  return $custom_params[0];
	   }
	   return null;
   }

   public function GetcustomVars() {
	   if (isset($_POST) && isset($_POST['custom'])) {
		   $custom_params = explode('|',base64_decode(esc_attr($_POST['custom'])));
		  return $custom_params[1];
	   }
	   return null;
   }


}

