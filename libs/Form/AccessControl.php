<?php


namespace LiveForms\Form;


use LiveForms\__\__;

class AccessControl
{
    public $allowed = false;
    public $message = '';
    function __construct()
    {

    }

    function verifyAccess( $form_id )
    {
        global $current_user;
        $formdata = get_post_meta($form_id, 'form_data', true);
        $formdata = maybe_unserialize($formdata);
        $access = __::valueof($formdata, 'access', ['validate' => 'array', 'default' => []]);
        $access = maybe_unserialize($access);

        if(in_array('everyone', $access) ||  count($access) === 0) {
            $this->allowed = true;
        }
        else if(is_user_logged_in() && count(array_intersect($current_user->roles, $access)) > 0) {
            $this->allowed = true;
        }
        else if(is_user_logged_in() && count(array_intersect($current_user->roles, $access)) === 0) {
            $this->allowed = false;
            $this->message = __::valueof($formdata, 'permission_denied');
            if(!$this->message)
                $this->message = "<div class='alert alert-danger'>".esc_attr__( 'You are not allowed to access this form!', LF_TEXT_DOMAIN )."</div>";
        }
        else if(!is_user_logged_in()) {
            $this->allowed = false;
            $this->message = __::valueof($formdata, 'login_to_access');
            if(!$this->message)
                $this->message = "<div class='alert alert-danger'>".esc_attr__( 'Please login to get access to this form', LF_TEXT_DOMAIN )."</div>";
        }
        return $this;
    }
}
