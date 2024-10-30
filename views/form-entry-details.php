<?php
if(!defined('ABSPATH')) die('!');
// Admin panel access
global $current_user;
$req_id = wplf_query_var('req_id', 'int');
$non_submit_fields = array('Pageseparator', 'Mathresult');
$formdata = LiveForms()->getForm(wplf_query_var('form_id'));
$form_fields = wplf_valueof($formdata, 'fieldsinfo');
$entry = LiveForms()->entry->get($req_id);

?>
<div class="w3eden">
    <div class="panel panel-default panel-wplf">
        <div class="panel-heading">
            <?= get_the_title($_REQUEST['form_id']). ' :: '.esc_attr__('Request Details', LF_TEXT_DOMAIN); ?>
        </div>
        <div class="list-group list-group-flush">
            <?php foreach ($form_fields as $field_id => $field_pref) { ?>
                <?php if (!in_array(substr($field_id, 0, strpos($field_id, '_')), $non_submit_fields)) { ?>
                    <div class="list-group-item">
                        <label><?php echo wplf_valueof($field_pref, 'label') ?> </label>
                        <?php
                        $value = wplf_valueof($entry, $field_id);
                        if(strstr("_{$field_id}", "File")) {
                            if($value != '') {
                                $path = $value;
                                $ext = $value != '' ? mime_content_type($value) : '';
                                $value = "{$path}<br/><a href='" . home_url("/?lfdl={$req_id}|{$field_id}") . "'>Download ( $ext )</a>";
                            } else {
                                $value = "&mdash;";
                            }
                        }

                        if(substr_count($field_id, 'Payment')) {
                            $payment = LiveForms()->paymentEntry->forFormEntry($req_id);
                            if($payment->amount > 0)
                                $value =  "Paid {$payment->amount} {$payment->currency} using {$value}";
                            else
                                $value = $value . "( No Payment )";
                        }
                        ?>
                        <div><?php echo is_array($value) ? implode(", ", $value) : $value; ?></div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</div>
