<?php
if(!defined('ABSPATH')) die('!');
$form_data = get_post_meta(get_the_ID(), 'form_data', true);
$form_emails = get_post_meta(get_the_ID(), 'form_emails', true);

//echo "<pre>".print_r($form_data, 1)."</pre>";die();
?>
<div role="tabpanel" class="tab-pane fade" id="settings">

    <div class="media">
        <div class="pull-left">
            <ul id="settings-tabs" class="nav nav-pills nav-stacked">
                <li class="">
                    <a data-toggle="tab" href="#form-settings">
                        <span class="h"><?= esc_attr__( 'Basic Settings', LF_TEXT_DOMAIN ); ?></span>
                        <span class="n"><?= esc_attr__( 'Basic form settings', LF_TEXT_DOMAIN ); ?></span>
                    </a>
                </li>
                <!-- li class="">
                    <a data-toggle="tab" href="#support-form">
                        <span class="h"><?= esc_attr__( 'User Receipt', LF_TEXT_DOMAIN ); ?></span>
                        <span class="n"><?= esc_attr__( 'Control user communication', LF_TEXT_DOMAIN ); ?></span>
                    </a>
                </li -->
                <li class="">
                    <a data-toggle="tab" href="#email-settings">
                        <span class="h"><?= esc_attr__( 'Email Notifications', LF_TEXT_DOMAIN ); ?></span>
                        <span class="n"><?= esc_attr__( 'Customize email messages', LF_TEXT_DOMAIN ); ?></span>
                    </a>
                </li>
                <li class="">
                    <a data-toggle="tab" href="#ui-settings">
                        <span class="h"><?= esc_attr__( 'Look and Feel', LF_TEXT_DOMAIN ); ?></span>
                        <span class="n"><?= esc_attr__( 'Customize front-end interface', LF_TEXT_DOMAIN ); ?></span>
                    </a>
                </li>
            </ul>
        </div>
        <div  class="media-body">
            <div id="content-tabs" class="tab-content settingsarea">
                <?php
                    include __DIR__.'/settings/basic-settings.php';
                    //include __DIR__.'/settings/support-form.php';
                    include __DIR__.'/settings/email-settings.php';
                    include __DIR__.'/settings/ui-settings.php';
                ?>

                <div class="clear"></div>
            </div>
        </div>
    </div>

</div>
<style>
    #settings-tabs a{
        padding: 14px 24px !important;
        height: auto !important;
    }
    #settings-tabs a span.h{
        font-size: 12pt !important;
        display: block;
        line-height: 1.4;
    }
    #settings-tabs a span.n {
        display: block;
        font-weight: 400;
        line-height: 1.4;
        font-size: 12px;
        letter-spacing: 1px;
        opacity: 0.6;
    }
</style>