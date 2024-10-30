<?php


namespace LiveForms;


class ShortCodes
{

    public function __construct()
    {
        add_shortcode("wplf_payments", [$this, 'payments']);
        add_shortcode("wplf_form_entries", [$this, 'formEntries']);
    }

    function payments($params)
    {
        if(!isset($params['form_id'])) return __('Form ID is missing!', 'liveforms');

        $payments = LiveForms()->payments->get((int)$params['form_id']);
        $headings = explode(",", wplf_valueof($params, 'heading'));
        $data_ids = explode(",", wplf_valueof($params, 'fields'));
        $name = wplf_valueof($params, 'name');
        ob_start();
        //lfprecho($payments);
        //lfprecho($form_entries);
        include LF_BASE_DIR.'views/payments.php';
        return ob_get_clean();

    }
    function formEntries($params)
    {
        if(!current_user_can(LF_ADMIN_CAP))
            return 'Coming Soon...';

        if(!isset($params['form_id'])) return __('Form ID is missing!', 'liveforms');

        $headings = explode(",", wplf_valueof($params, 'heading'));
        $data_ids = explode(",", wplf_valueof($params, 'fields'));
        $form_entries = LiveForms()->entries->get((int)$params['form_id']);
        $form = LiveForms()->getForm($params['form_id']);
        $fields = $form['fields'];
        $field_heads = array_map(function ($field_info) { return $field_info['label']; }, $form['fieldsinfo']);

        ob_start();

        include LF_BASE_DIR.'views/form/entries.php';
        return ob_get_clean();

    }

}