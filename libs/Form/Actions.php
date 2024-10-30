<?php


namespace LiveForms\Form;


use LiveForms\__\__;

class Actions
{
    function __construct()
    {
        add_action("admin_init", [$this, 'duplicate']);
        add_action("admin_init", [$this, 'export']);
    }

    function duplicate()
    {
        if (__::query_var('wplftask') === 'clone') {
            $id = __::query_var('form_id', ['validate' => 'int']);
            $formdata = get_post_meta($id, 'form_data', true);
            $old_form = get_post($id, ARRAY_A);
            unset($old_form['ID']);
            $new_post_id = wp_insert_post($old_form);
            update_post_meta($new_post_id, 'form_data', $formdata);
            wp_redirect(admin_url('/edit.php?post_type=form'));
            die();
        }
    }
    function export()
    {
        if (__::query_var('wplftask') === 'export') {
            $id = __::query_var('form_id', ['validate' => 'int']);
            $form = get_post($id);
            $formdata = get_post_meta($id, 'form_data', true);
            @ob_end_clean();
            nocache_headers();
            header("X-Robots-Tag: noindex, nofollow", true);
            header("Robots: none");
            header("Content-Description: File Transfer");
            header("Content-Type: text/plain");
            header("Content-disposition: attachment;filename=\"{$form->post_name}.form\"");
            header("Content-Transfer-Encoding: Binary");
            header("Content-Length: " . strlen($formdata));
            echo $formdata;
            die();
        }
    }
}
