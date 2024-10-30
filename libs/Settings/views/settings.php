<?php
/**
 * Base: LiveForms
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 7/8/20 17:55
 */

if(!defined("ABSPATH")) die();
?>
<div class="wrap w3eden fixed-top with-sidebar">
    <div id="liveforms-admin-container">
        <nav class="navbar navbar-default navbar-fixed-top-">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <div class="navbar-brand">
                    <div class="d-flex">
                        <div class="logo">
                            <img src="<?= LF_BASE_URL ?>assets/images/liveforms-logo.png" style="width: 40px" alt="LF" />
                        </div>
                        <div>
                            Settings
                        </div>
                    </div>
                </div>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li><button type="button" class="btn btn-lg btn-primary" id="wplf_save_settings_btn"><i class="fas fa-hdd"></i> Save Settings</button></li>
            </ul>
        </nav>

        <div id="liveforms-admin-content" class="with-sidebar">
            <div id="liveforms-content-sidebar">

                <?php //echo $this->settings_api->section_tabs(); ?>

                <ul id="tabs" class="nav nav-pills nav-stacked settings-tabs">
                    <?php LiveForms()->settings->renderMenu($tab); ?>
                </ul>
            </div>
            <div id="liveforms-content-container">
                <div id="liveforms-settings-content">
                    <?php
                    if(\LiveForms\__\Session::get('settings_error')) {
                        ?>
                        <div class="alert alert-danger">
                            <?php  echo \LiveForms\__\Session::get('settings_error');?>
                        </div>
                        <?php
                        \LiveForms\__\Session::clear('settings_error');
                    }
                    if(\LiveForms\__\Session::get('settings_success')) {
                        ?>
                        <div class="alert alert-success">
                            <?php  echo \LiveForms\__\Session::get('settings_success');?>
                        </div>
                        <?php
                        \LiveForms\__\Session::clear('settings_success');
                    }

                    LiveForms()->settings->renderSettingsTab($tab);

                    ?>
                </div>
                <div id="footernotice">
                </div>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">

    var _notice = {
        show: function (message, success) {
            if(success === true)
                _notice.success(message);
            else
                _notice.error(message);
        },
        error: function (message) {
            jQuery('#footernotice').html(message).removeClass('show error success').addClass('show error');
        },
        success: function (message) {
            jQuery('#footernotice').html(message).removeClass('show error success').addClass('show success');
        },
        hide: function (message) {
            jQuery('#footernotice').html(message).removeClass('show error success');
        }
    }

    function reload_tab(tabid) {
        jQuery('#tabs #' + tabid).trigger('click');
    }

    jQuery(function($){

        $("ul#tabs li").click(function() {

        });
        $('#footernotice').on('click', function (){
            $(this).removeClass('show error success');
        });
        $('#message').removeClass('hide').hide();
        $("ul#tabs li a").click(function() {
            $("ul#tabs li").removeClass("active");
            $(this).parent('li').addClass('active');
            $('#wdms_loading').addClass('wpdm-spin');
            $('.wplf-menu-loading').remove();
            var secid = 'wplf-menu-loading-'+this.id;
            $(this).append('<i class="far fa-sun fa-spin  pull-right" id="'+secid+'" style="line-height: 46px"></i>')
            var section = this.id;
            _notice.hide();
            $.post(ajaxurl,{action:'liveforms_settings',section:this.id},function(res){
                $('#liveforms-settings-content').html(res);
                $('#'+secid).remove();
                window.history.pushState({"html":res,"pageTitle":"response.pageTitle"},"", "edit.php?post_type=form&page=wplf-settings&tab="+section);
            });
            return false;
        });

        window.onpopstate = function(e){
            if(e.state){
                jQuery("#fm_settings").html(e.state.html);
                //document.title = e.state.pageTitle;
            }
        };


        $('#wplf_save_settings_btn').click(function(){
            $('#liveforms-content-container').addClass('blockui');
            var $btn = $(this);
            var btntxt = $btn.html();
            var w = (parseInt($btn.width())+60)+'px';
            $btn.attr('disabled', 'disabled').css('min-width', w).html('<i class="far fa-sun fa-spin"></i> <?= __('Saving...', 'liveforms'); ?>');
            _notice.hide();
            $('#liveforms-settings-form').ajaxSubmit({
                url: ajaxurl,
                beforeSubmit: function(formData, jqForm, options){

                },
                success: function(response, statusText, xhr, $form){
                    $('#liveforms-content-container').removeClass('blockui');
                    $btn.removeAttr('disabled', 'disabled').html(btntxt);
                    if(response.success === true)
                        _notice.success(response.message);
                    if(response.success === false)
                        _notice.error(response.message);
                }
            });

            return false;
        });

        $('body').on("click",'.nav-tabs a', function (e) {
            e.preventDefault();
            $(this).tab('show');
        });



    });

</script>