<?php
if (!defined('ABSPATH')) die('!');
// Admin panel access
global $current_user;
$req_id = wplf_query_var('req_id', 'int');
$non_submit_fields = array('Pageseparator', 'Mathresult');
$formdata = LiveForms()->getForm(wplf_query_var('form_id'));
$form_fields = wplf_valueof($formdata, 'fieldsinfo');
$entry = LiveForms()->entry->get($req_id);
$fields = $formdata['fields'];
?>
<div class="wrap w3eden">
    <div id="liveforms-admin-container">
        <nav class="navbar navbar-default navbar-fixed-top-">
            <div class="navbar-header">
                <div class="navbar-brand">
                    <div class="d-flex">
                        <div class="logo">
                            <img src="<?= LF_BASE_URL ?>assets/images/liveforms-logo.png" style="width: 40px" alt="LF"/>
                        </div>
                        <div>
                            <?php _e('Form Entry Details', 'liveforms'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="<?php echo admin_url("/edit.php?post_type=form&page=form-entries&form_id=" . wplf_query_var('form_id')); ?>"><?php _e('View All Entries', 'liveforms') ?></a>
                </li>
            </ul>
        </nav>
        <div id="liveforms-admin-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default panel-wplf">
                            <div class="panel-heading">
                                <?= get_the_title($_REQUEST['form_id']). ' :: '.esc_attr__('Entry Details', LF_TEXT_DOMAIN); ?>
                            </div>
                            <div class="list-group list-group-flush">
                                <?php foreach ($form_fields as $field_id => $field_pref) { ?>
                                    <?php if (!in_array(substr($field_id, 0, strpos($field_id, '_')), $non_submit_fields)) { ?>
                                        <div class="list-group-item">
                                            <label><?php echo wplf_valueof($field_pref, 'label') ?> </label>
                                            <?php
                                            $value = wplf_valueof($entry, $field_id);
                                            if (isset($fields[$field_id])) {
                                                $field = new $fields[$field_id]();
                                                $value = $field->print_value($value, $field_id, $req_id, wplf_query_var('form_id'));

                                                if (substr_count($field_id, 'Payment')) {
                                                    $payment = LiveForms()->paymentEntry->forFormEntry($req_id);
                                                    if ($payment->amount > 0)
                                                        $value = "Paid {$payment->amount} {$payment->currency} using {$value}";
                                                    else
                                                        $value = $value . "( ".__('No Payment', 'liveforms')." )";
                                                }
                                            } else {

                                            }
                                            ?>
                                            <div><?php echo is_array($value) ? implode(", ", $value) : wpautop(stripslashes($value)); ?></div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                        <form id="replyform" method="post" action="">
                            <div class="panel panel-default">
                                <div class="panel-body p-0">
                                    <textarea class="form-control border-0 no-shadow" style="padding: 20px"
                                              name="reply_msg"
                                              placeholder="<?= esc_attr__('Add Your Reply...', LF_TEXT_DOMAIN); ?>"></textarea>
                                    <input type='hidden' name="action" value='admin_reply'/>
                                    <input type='hidden' name="type" value='admin'/>
                                    <input type="hidden" name="req_id" value="<?php echo (int)$entry->id ?>"/>
                                </div>
                                <div class="panel-footer text-right">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i>
                                        &nbsp;<?php _e('Send Reply', LF_TEXT_DOMAIN); ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-md-12" id="replies">

                                <div class="panel panel-default reply-panel" v-for="reply in replies">
                                    <div class="panel-body">
                                        <div class="media">
                                            <div class="pull-left">
                                                <span v-html="reply.avatar"></span>
                                            </div>
                                            <div class="media-body">
                                                <h3 class="media-heading">{{reply.sender}}</h3>
                                                <em>{{reply.date_time}}</em>
                                                <span v-html="reply.message"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= LF_BASE_URL ?>assets/js/vue.min.js"></script>
<script type="text/javascript">
    var entry_replies = new Vue({
        el: '#replies',
        data: {
            replies: []
        }
    });

    function load_replies() {
        jQuery.get('<?=get_rest_url(null, '/liveforms/v1/entry-replies'); ?>', {entry_id: <?php echo wplf_query_var('req_id'); ?>}, function (result) {
            entry_replies.replies = result.replies;
        });
    }


    jQuery(function ($) {
        var options = {
            url: ajaxurl,
            success: function (response) {
                $('#replyform').removeClass('blockui');
                load_replies();
            }
        };

        $('#replyform').on('submit', function () {
            $(this).addClass('blockui');
            $(this).ajaxSubmit(options);
            return false;
        });

        load_replies();

    });
</script>
