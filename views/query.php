<?php
if(!defined('ABSPATH')) die('!');
// Setup wordpress URL prefix
$url = get_permalink(get_the_ID());
$sap = strpos($url, "?")?"&":"?";
$purl = $url.$sap;
?>
<link rel="stylesheet" href="<?= LF_ASSET_URL ?>css/bootstrap.min.css" />
<link rel="stylesheet" href="<?= LF_ASSET_URL ?>css/liveform-ui.min.css" />

    <div class="w3eden _wplf">

        <div id="query-status">
            <form class='form' method="post" id="query-status-form" action="<?= LiveForms()->API->url('entry/validate-token');?>">
            <div class="panel panel-default">

                <div class="panel-heading">
                    <?= esc_attr__( 'Check Query Status', LF_TEXT_DOMAIN ); ?>
                </div>

                <div class="panel-body">
                    <div class='input-group input-group-lg'>
                        <input required="required" class='form-control' type="text" placeholder="<?= esc_attr__( 'Enter your token here', LF_TEXT_DOMAIN ); ?>" id="wplf-token" name='wplf-token'/>
                        <div class="input-group-btn">
                            <button class='btn btn-primary'><?= esc_attr__( 'Check Status', LF_TEXT_DOMAIN ); ?></button>
                        </div>
                    </div>

                </div>

            </div>
            </form>
            <div id="query-details" style="display: none">
                <div  id="entry-details">
                    <div class="panel panel-default panel-wplf">
                        <div class="panel-heading">
                            <a href="#" class="pull-right" id="reply-logout"><?= esc_attr__( 'Logout', LF_TEXT_DOMAIN ); ?></a>
                            <?=esc_attr__('Request Details', LF_TEXT_DOMAIN); ?>
                        </div>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item" v-for="data in entry.entry_data_formatted">
                                        <label class="label label-info">{{ data.name }}</label>
                                        <div>{{ data.value }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="replyform" method="post" action="">
                    <div class="panel panel-default">
                        <div class="panel-body p-0">
                            <textarea class="form-control border-0 no-shadow" style="padding: 20px" id="reply_msg" name="reply_msg" placeholder="<?=esc_attr__('Add Your Reply...', LF_TEXT_DOMAIN); ?>"></textarea>
                        </div>
                        <div class="panel-footer text-right">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-reply"></i> Send Reply</button>
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
<script src="<?=LF_BASE_URL ?>assets/js/vue.min.js"></script>
<script>

    var entry_details = new Vue({
        el: '#entry-details',
        data: {
            entry: []
        }
    });
    var entry_replies = new Vue({
        el: '#replies',
        data: {
            replies: []
        }
    });

    /*function load_replies()
    {
        jQuery.get(ajaxurl, {action: 'get_replies', entry_id: <?php echo wplf_query_var('req_id'); ?>}, function (result){
            entry_replies.replies = result.replies;
        });
    }*/

    jQuery(function ($){
        var token = '<?= \LiveForms\__\Session::get('wplf_entry_token') ?>';
        var $body = $('body');
        var $qsform = $('#query-status-form');
        var $qstatus = $('#query-status');

        $qsform.submit(function (e){
            e.preventDefault();
            $qstatus.addClass('blockui');
            $(this).ajaxSubmit({
                success: function (response){
                    $qstatus.removeClass('blockui');
                    if(!response.success)
                        alert(response.message);
                    else {
                        $qstatus.removeClass('blockui');
                        $qsform.slideUp();
                        entry_details.entry = response.entry;
                        entry_replies.replies = response.replies;
                        $('#query-details').slideDown();
                    }
                    return false;
                },
                error: function (response){
                    console.log(response);
                    $qstatus.removeClass('blockui');
                    return false;
                }
            });
        });

        $body.on('click', '#reply-logout', function (e){
            e.preventDefault();
            $.get('<?= LiveForms()->API->url('entry/logout'); ?>', function (response){
                $('#query-details').slideUp();
                $qstatus.removeClass('blockui');
                $qsform.slideDown();
            });
        });

        $body.on('submit', '#replyform', function (e){
            e.preventDefault();
            $(this).addClass('blockui');
            $.ajax({
                url: '<?= LiveForms()->API->url('entry/user-reply'); ?>',
                method: 'POST',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', '<?= wp_create_nonce( 'wp_rest' ); ?>');
                },
                data: { reply_msg: $('#reply_msg').val() }
            }).done( function ( response ) {
                $qstatus.removeClass('blockui');
                if(response.success === true)
                    entry_replies.replies = response.replies;
                else
                    alert(response.message);
            });
        });

        if(token !== ''){
            $('#wplf-token').val(token);
            $qsform.submit();
        }


    });
</script>

<style>
    .p-0{ padding: 0 !important; }
    .border-0{ border: 0 !important; }
    .no-shadow{ box-shadow: none !important; text-shadow: none !important; }
    .reply-panel .avatar{
        width: 64px;
        height: 64px;
        border-radius: 500px;
        margin-right: 12px;
    }
    .reply-panel h3{
        font-size: 12pt;
        font-weight: 700;
    }
    .reply-panel em{
        font-size: 11px;
    }
</style>