<?php
if (!defined('ABSPATH')) die('!');
// Setup wordpress URL prefix
//$url = get_permalink(get_the_ID());
//$sap = strpos($url, "?") ? "&" : "?";
//$purl = $url . $sap;
//// To get access to administration panel
//$purl .= "post_type={$_REQUEST['post_type']}&page={$_REQUEST['page']}&form_id={$_REQUEST['form_id']}&section={$_REQUEST['section']}&";
$purl = '?';
$params = array('post_type', 'page', 'page_id', 'form_id', 'post_id', 'status', 'ipp', 'paged', 'section',);
foreach ($params as $param) {
    if (isset($_REQUEST[$param]))
        $purl .= "{$param}=" . esc_attr($_REQUEST[$param]) . "&";
}
$forms_list = get_posts(['post_type' => 'form', 'posts_per_page' => -1]);
$non_submit_fields = array('Pageseparator', 'Mathresult');
$form_id = wplf_query_var('form_id');
$items_paer_page = 30;
if ($form_id) {
    $_form = get_post($form_id);
    $form_data = get_post_meta($form_id, 'form_data', true);
    $form_fields = wplf_valueof($form_data, 'fieldsinfo');
    $fields = wplf_valueof($form_data, 'fields');
    global $wpdb;
    $start = ( wplf_query_var('paged', ['default' => 1, 'validate' => 'int']) -1 ) * $items_paer_page;
    $total_entries = $wpdb->get_var("select count(*) from {$wpdb->prefix}liveforms_conreqs where fid = '{$form_id}'");
    $form_entries = $wpdb->get_results("select * from {$wpdb->prefix}liveforms_conreqs where fid = '{$form_id}' order by id desc limit $start, {$items_paer_page}");
}

?>
<div class="wrap w3eden">
    <div id="liveforms-admin-container">
        <nav class="navbar navbar-default navbar-fixed-top-">
            <div class="navbar-header">
                <div class="navbar-brand">
                    <div class="d-flex">
                        <div class="logo">
                            <img src="<?= LF_BASE_URL ?>assets/images/liveforms-logo.png" style="width: 40px" alt="LF" />
                        </div>
                        <div>
                            <?= __('Form Entries', LF_TEXT_DOMAIN); ?>
                        </div>
                    </div>
                </div>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                       aria-haspopup="true" aria-expanded="false"><?= __('Select Form', LF_TEXT_DOMAIN); ?> <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <?php foreach ($forms_list as $form) { ?>
                            <li>
                                <a href="<?php echo admin_url("/edit.php?post_type=form&page=form-entries&form_id={$form->ID}"); ?>"><?php echo esc_attr($form->post_title); ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="liveforms-admin-content">
        <?php if ($form_id) { ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9">
                    <div class="panel panel-default">
                        <div class="panel-heading">Form Name:</div>
                        <div class="panel-body"><h4 style="margin:  0;"><?php echo $_form->post_title; ?></h4></div>
                    </div>
                </div>
                <div class="col-md-3 text-right">
                    <div class="panel panel-default text-center showreqs" data-status="new">
                        <div class="panel-body"><h4 id="new" style="margin: 0"><?php echo $total_entries ?></h4></div>
                        <div class="panel-footer">Total Entries</div>
                    </div>
                </div>
            </div>
            <?php if($form_entries) { ?>
            <form method="post" action='' id="reqform">
                <p><button type="button" class="btn btn-primary delete-entries"><?= __('Delete Selected', LF_TEXT_DOMAIN); ?></button></p>
                <div class="panel panel-default">
                    <table class='table table-striped table-hover'>
                        <thead>
                        <tr>
                            <th><input id="fic" type='checkbox'/></th>

                            <th><?=__('Date', LF_TEXT_DOMAIN); ?></th>
                            <?php
                            foreach ($form_fields as $id => $field) {
                                if($id === '{{fieldindex}}' || !isset($fields[$id])) continue;
                                if (!in_array(substr($id, 0, strpos($id, '_')), $non_submit_fields)) {
                                    $fieldids[] = $id;
                                    if($id === '{{fieldindex}}') continue;
                                    echo "<th>".wplf_valueof($field,'label')."</th>";
                                }
                            }
                            ?>
                            <th><?=__('Action', LF_TEXT_DOMAIN); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php

                        foreach ($form_entries as $entry) {
                            $req_id = $entry->id;
                            $form_data = maybe_unserialize($entry->data);
                            $time = date(get_option('date_format'), $entry->time);
                            $viewaction = "<a href='".admin_url("/edit.php?post_type=form&page=form-entries&form_id={$form_id}&req_id={$entry->id}")."' class='btn btn-primary btn-xs'>".__('View', LF_TEXT_DOMAIN)."</a>";
                            $deleteaction = "<a data-id='{$req_id}' href='#' class='btn btn-danger delete-entry btn-xs'>".__('Delete', LF_TEXT_DOMAIN)."</a>";
                            echo "<tr id='fer_{$req_id}'><th style='width: 20px'><input type='checkbox' class='fic' name='entry[{$entry->id}]' value='{$entry->id}' /></th><td>{$time}</td>";
                            //$req = unserialize($req['data']);
                            foreach ($fieldids as $id) {
                                if($id === '{{fieldindex}}' || !isset($fields[$id])) continue;
                                $value = isset($form_data[$id]) ? $form_data[$id] : '';
                                $field = new $fields[$id]();
                                $value = $field->print_value($value, $id, $req_id, $form_id);

                                if(substr_count($id, 'Payment')) {
                                    $payment = LiveForms()->paymentEntry->forFormEntry($req_id);
                                    if($payment->amount > 0)
                                        $value =  "Paid {$payment->amount} {$payment->currency} using {$value}";
                                    else
                                        $value = $value . "( No Payment )";
                                }
                                echo "<td>{$value}&nbsp;</td>";

                            }
                            echo "<td style='white-space: nowrap'>{$viewaction} {$deleteaction}</td>";
                            echo "</tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>

                <?php echo wplf_paginate_links($total_entries, $items_paer_page, wplf_query_var('paged', ['default' => 1]),  'paged') ?>

            </form>
            <?php } else { ?>
                <div class="alert alert-warning">
                    <i class="fa fa-times-circle"></i> <?= __('No entry found!', LF_TEXT_DOMAIN); ?>
                </div>
            <?php } ?>
        </div>
        <?php } else { ?>
            <div class="alert alert-info">
                <i class="fa fa-bars"></i> <?= __('Select from to view the entries', LF_TEXT_DOMAIN); ?>
            </div>
        <?php } ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(function ($) {
        $('#fic').on('click', function () {
            if (this.checked)
                $('.fic').prop('checked', true);
            else
                $('.fic').prop('checked', false);
        });
        $('#fef').submit(function () {
            $(this).ajaxSubmit({
                beforeSubmit: function (reqs) {
                }
            });
            return false;
        });

        var options = {
            url: '<?php echo $purl ?>action=change_req_state&form_id=<?php echo isset($_REQUEST[$param]) ? (int)$_REQUEST['form_id'] : 0 ?>&status=',
            reqstatus: 'new',
            newstatus: 'new',
            beforeSubmit: function () {
                $('#form-entries').prepend("<div class='data-loading'><i class='fa fa-spinner fa-spin'></i> &nbsp; loading...</div>");
            },
            success: function (response) {
                var jsonData = JSON.parse(response);

                if (jsonData['html'] != '') {
                    $('#form-entries').html(jsonData['html']);
                }
                $('#' + this.reqstatus).html(jsonData['count']);
                if (this.reqstatus != this.newstatus) {
                    // update
                    $old_count = parseInt($('#' + this.newstatus).html());
                    $new_count = parseInt(jsonData['changed']) + $old_count;
                    $('#' + this.newstatus).html($new_count);
                }
            }
        }
        $('#reqform').on('submit', function () {
            var new_status = $('button[type=submit][clicked=true]').val();
            // Deep copy
            var current_options = jQuery.extend(true, {}, options);
            current_options.newstatus = new_status;
            current_options.url += new_status + '&query_status=' + current_options.reqstatus;
            $(this).ajaxSubmit(current_options);

            return false;
        });


        $('#reqform button[type=submit]').click(function () {
            $("button[type=submit]", $(this).parents("#reqform")).removeAttr("clicked");
            $(this).attr("clicked", "true");
        });
        $('.showreqs').on('click', function (e) {
            e.preventDefault();
            var status = $(this).attr('data-status');
            options.reqstatus = status;
            $('#form-entries').prepend("<div class='data-loading'><i class='fa fa-spinner fa-spin'></i> &nbsp; loading...</div>").load('<?php echo $purl; ?>section=stat_req&form_id=<?php echo (int)$_REQUEST['form_id']; ?>&status=' + status, function () {
                window.history.pushState("", "Title", '<?php echo $purl; ?>section=stat_req&form_id=<?php echo (int)$_REQUEST['form_id'];?>&status=' + status);
                $('#fic').on('click', function () {
                    if (this.checked)
                        $('.fic').prop('checked', true);
                    else
                        $('.fic').prop('checked', false);
                });
            });
        });
    });
</script>
