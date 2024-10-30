<?php
/**
 * @variable    $formdata
 * @uses        Contains form element configurations
 * @origin      Controller: contactforms, Method: showform()
 */

use LiveForms\__\__;
use LiveForms\__\Crypt;

if (!defined('ABSPATH')) die('!');
$url = home_url('/');
$sap = strpos($url, "?") ? "&" : "?";
$purl = $url . $sap;
$id = uniqid();
$checked_fields = __::valueof($formdata, 'fields');
$form_layout = __::valueof($formdata, 'form_layout');
$field_infos = __::valueof($formdata, 'fieldsinfo');
$column_width = __::valueof($formdata, 'col_width');
$multipage = (int)__::valueof($formdata, 'multipage');
$row_settings = __::valueof($formdata, 'row_settings');

if (count($checked_fields) > 0 && !$form_layout) {
    $row_id = "row_" . uniqid();
    $column_id = "col_" . uniqid();
    $form_layout = [$row_id => [$column_id => $checked_fields]];
    $column_width = [$column_id => 12];
}
$autofill = (int)__::valueof($formdata, 'autofill');
?>

<style>
    <?= wplf_valueof($formdata, 'custom_css'); ?>
</style>

<!-- Start form -->
<div class="_wplf liveforms">
    <div id="liveform_container_<?php echo get_the_ID() ?>">
        <?php if($multipage) { ?>
        <style>
            .nav-progress {
                display: table;
                overflow: hidden;
                margin: 0 0 10px 0;
                width: 100%;
                height: 32px;
                /*border: 1px solid var(--color-primary);*/
                background-color: #eeeeee;
                -webkit-border-radius: 3px;
                -moz-border-radius: 3px;
                border-radius: 3px;
                font-weight: 600;
            }
            .nav-progress > div {
                position: relative;
                display: table-cell;
                padding: 4px 0;
                color: var(--color-secondary);
                text-align: center;
                font-size: 12px;
                line-height: 20px;
            }
            .nav-progress > div.complete {
                background-color: var(--color-secondary);
                color: #FFF;
            }
            .nav-progress > div.complete .arrow {
                border: 3px solid #ffffff !important;
                background: var(--color-secondary) !important;
            }
            .nav-progress > div.active {
                background-color: var(--color-secondary);
                color: #FFF;
            }
            .nav-progress > div.active .arrow {
                background: var(--color-secondary) !important;
            }

            .arrow-wrapper {
                position: absolute;
                top: 0px;
                right: 0px;
            }
            .arrow-wrapper .arrow-cover {
                position: absolute;
                overflow: hidden;
                width: 24px;
                height: 50px;
            }
            .arrow-wrapper .arrow-cover .arrow {
                position: absolute;
                left: -16px;
                z-index: 2;
                width: 32px;
                height: 32px;
                border: 3px solid #ffffff;
                background: transparent;
                -webkit-transform: rotate(45deg);
                -moz-transform: rotate(45deg);
                -ms-transform: rotate(45deg);
                -o-transform: rotate(45deg);
                transform: rotate(45deg);
            }
        </style>
        <div class="nav-progress">
            <?php
            $n = 0;
            foreach ($row_settings as $row_id => $row_setting) { $n++; ?>
            <div class="_step _step_<?= $n ?> <?= $n === 1 ? 'complete' : '' ?>" id="step_<?= $row_id ?>">
                <?= $row_setting['label'] ?>
                <?php if($n < count($row_settings)) { ?>
                <div class="arrow-wrapper">
                    <div class="arrow-cover">
                        <div class="arrow"></div>
                    </div>
                </div>
                <?php } ?>
            </div>
            <?php

            } ?>
        </div>
        <?php } ?>
        <div id="formarea">
            <form id="form-<?php echo $form_id; ?>" action="" method="post" enctype="multipart/form-data">
                <?php wp_nonce_field(NONCE_KEY, '__isliveforms'); ?>
                <input type="hidden" id="formid" name="form_id" value="<?php echo $form_id ?>"/>
                <input type="hidden" id="formid" name="form_validator" value="<?php echo Crypt::encrypt($form_id); ?>"/>
                <input type="hidden" id="fields" name="fields" value="<?php echo $fields ?>"/>
                <?php

                //do something
                do_action('liveform-showform_before_form_fields', $form_id);
                //lfprecho($form_layout);
                $rc = 0;

                if (is_array($form_layout)) {
                    foreach ($form_layout as $row_id => $form_row) {
                        $rc++;
                        ?>
                        <div class="row form-page form-page-<?=$rc ?> <?=$rc === count($form_layout) ? 'last-page' : ''; ?>" id="<?= $row_id ?>">
                            <?php foreach ($form_row as $column_id => $column) { ?>
                                <div class="wplf-col col-md-<?= $column_width[$column_id]; ?>" id="<?= $column_id ?>">
                                    <?php
                                    $form_fields_list = '';
                                    foreach ($column as $fieldindex => $fieldid) {
                                        if ($fieldid && class_exists($fieldid)) {
                                            if (!in_array($fieldid, $this->fields_advanced) || is_pro()) {
                                                $tmp_obj = new $fieldid();
                                                if (isset($field_infos[$fieldindex])) {
                                                    $field_infos[$fieldindex]['id'] = $fieldindex;
                                                    $field_infos[$fieldindex]['row_id'] = $row_id;
                                                    $field_infos[$fieldindex]['column_id'] = $column_id;
                                                    $field_infos[$fieldindex]['form_id'] = $form_id;
                                                    echo $tmp_obj->field_render_html($field_infos[$fieldindex]);

                                                }
                                            }
                                        }
                                    }

                                    ?>

                                </div>
                            <?php } ?>
                            <?php if($multipage && count($form_layout) > 1 && count($form_layout) > $rc) { ?>
                                <div class="col-sm-6"><button <?= $rc === 1 ? 'disabled=disabled' : '' ?> type="button" class="btn btn-default <?= __::valueof($formdata, 'buttonsize', 'btn-lg'); ?> btn-block form-backward" data-pageno="<?=$rc-1 ?>" data-prevpage=".form-page-<?=$rc-1 ?>"><i class="fa fa-arrow-circle-left"></i>Back</button></div>

                                <div class="col-sm-6"><button type="button" data-page="#<?=$row_id ?>" data-pageno="<?=$rc+1 ?>" data-nextpage=".form-page-<?=$rc+1 ?>" class="btn btn-primary <?= __::valueof($formdata, 'buttonsize', 'btn-lg'); ?> btn-block form-proceed">Proceed</button></div>
                                <?php
                            } ?>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="row" id="final-submit">
                        <?php if($multipage && count($form_layout) > 1) { ?>
                            <div class="col-md-4"><button <?= $rc === 1 ? 'disabled=disabled' : '' ?> type="button" class="btn btn-default <?= __::valueof($formdata, 'buttonsize', 'btn-lg'); ?> btn-block form-backward" data-pageno="<?=$rc-1 ?>" data-prevpage=".form-page-<?=$rc-1 ?>"><i class="fa fa-arrow-circle-left"></i>Back</button></div>
                        <?php } ?>
                        <div class="col-md-<?= $multipage && count($form_layout) > 1 ? 8 : 12; ?> <?= __::valueof($formdata, 'buttonpos', 'text-center'); ?>">
                            <?php //__::p($formdata); ?>
                            <button type="submit" class="btn wplf-form-submit-button btn-<?= __::valueof($formdata, 'buttoncolor', 'primary'); ?> <?= __::valueof($formdata, 'buttonsize', 'btn-lg'); ?>"><?= __::valueof($formdata, 'buttontext', esc_attr__('Submit Form', LF_TEXT_DOMAIN)) ?></button>
                        </div>
                    </div>
                    <?php
                }

                //do something
                do_action('liveform-showform_after_form_fields', $form_id);
                ?>
            </form>
        </div>
    </div>
</div>
<!-- End form -->

<script type='text/javascript'>

    function validateForm() {
        var validator = jQuery('#form-<?php echo $form_id; ?>').validate({
            errorPlacement: function (error, element) {
                console.log(element);
                error.insertAfter(element.parent());
                /*if (element.attr("type") == "radio" || element.attr("type") == "checkbox" ) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }*/
            }

        });
        var $form = jQuery('#form-<?php echo $form_id; ?>').find('input,select,textarea');
        var validForm = true;
        $form.each(function () {
            if (!validator.element(this)) {
                validForm = false;
                jQuery(this).parent('.form-group').removeClass('has-success').addClass('has-error');
            } else {
                jQuery(this).parent('.form-group').removeClass('has-error').addClass('has-success');
            }
        });
        return validForm;
    }

    jQuery(function ($) {


        <?= wplf_valueof($formdata, 'custom_js'); ?>

        let $form = $('#form-<?php echo $form_id; ?>');
        const auto_fill = <?= $autofill ?>;

        if(auto_fill) {
            $form.find('.form-control').each(function () {
                if($(this).attr('type') !== 'hidden')
                    $(this).val(localStorage.getItem($(this).attr('id')));
            });
        }

        let submit_btn_text;
        let next_part_id;
        let this_part_id;

        let set_show = {display: 'block'};
        let set_hide = {display: 'none'};
        let validator = $form.validate();
        let validInput = true;

        let submit_button_label = "<?= wplf_valueof($formdata, 'buttontext', 'txt'); ?>";
        let submit_button_busy_label = "<?= wplf_valueof($formdata, 'processingtext', 'txt'); ?>";

        <?php if($multipage) { ?>
        $('.form-page, #final-submit').hide();
        $('.form-page-1').show();
        $('.form-proceed').on('click', function () {
            var validForm = true;
            $($(this).data('page')).find('.form-control').each(function (index, item) {
                console.log();
                if ('DIV' !== item.tagName && !validator.element(this)) {
                    validForm = false;
                    jQuery(this).parent('.form-group').removeClass('has-success').addClass('has-error');
                } else {
                    jQuery(this).parent('.form-group').removeClass('has-error').addClass('has-success');
                }
            });
            if(!validForm)
                return false;

            $('.form-page, #final-submit').hide();
            $($(this).data('nextpage')).show();
            if($($(this).data('nextpage')).hasClass('last-page')) $('#final-submit').show();

            $('._step').removeClass('active');
            let pageno = parseInt($(this).data('pageno'));
            for(let i=2; i <= pageno; i++) {
                $('._step_'+i).addClass('active');
            }


        });

        $('.form-backward').on('click', function () {
            $('.form-page, #final-submit').hide();
            $($(this).data('prevpage')).show();
            $('._step').removeClass('active');
            let pageno = parseInt($(this).data('pageno'));
            for(let i=2; i <= pageno; i++) {
                $('._step_'+i).addClass('active');
            }
        });
        <?php } ?>


        $('.nav-wizard li a').on('click', function () {
            if ($(this).parent('li').hasClass('disabled'))
                return false;
        });

        $('input,select,textarea').on('change', function () {
            var validator = $form.validate();
            if (!validator.element(this)) {
                $(this).parent('.form-group').removeClass('has-success').addClass('has-error');
            } else {
                $(this).parent('.form-group').removeClass('has-error').addClass('has-success');
            }
        });


        $('.change-part').on('click', function () {
            next_part_id = $(this).attr('data-next');
            this_part_id = $(this).attr('data-parent');


            /* Pre validate */
            validInput = true;
            var $inputs = $('#' + this_part_id).find("input,select,textarea");
            $inputs.each(function () {
                if (!validator.element(this)) {
                    validInput = false;
                    $(this).parent('.form-group').removeClass('has-success').addClass('has-error');
                } else {
                    $(this).parent('.form-group').removeClass('has-error').addClass('has-success');
                }
            });

            if (validInput == true) {
                $('.liveforms-nav-wizard li').removeClass('active');
                $('#' + next_part_id + '_crumb').removeClass('disabled').addClass('active');
                $(this).parent('.form-group').removeClass('has-error').addClass('has-success');
                $('#' + this_part_id).removeClass('active');
                $('#' + next_part_id).addClass('active');

            } else {

            }
        });


        /*ajax submit*/
        var options = {
            url: '<?php echo $purl ?>action=submit_form',
            beforeSubmit: function (arr, $_form, options) {
                $form.find('button[type=submit]').html(submit_button_busy_label);
                $('#formarea').addClass('blockui');
                if(auto_fill) {
                    $form.find('.form-control').each(function () {
                        localStorage.setItem($(this).attr('id'), $(this).val());
                    })
                }
            },
            success: function (response) {

                $('#formarea').removeClass('blockui');
                $form.find('button[type=submit]').html(submit_button_label);
                if ($('#formarea .tab-pane').length > 1) {
                    $('#' + this_part_id).css(set_hide);
                    $('#form_part_0').css(set_show);
                }

                if (response.action === 'success') {
                    var msg = "<div class='alert alert-success text-center'>" + response.message + "</div>";
                    $('#formarea').html(msg);
                    if (response.redirect_to !== undefined) {
                        var rmsg = "<div class='alert alert-info text-center' style='margin-top: 20px'>Redirecting...</div>";
                        $('#formarea').append(rmsg);
                        setTimeout(function () {
                            location.href = response.redirect_to;
                        }, 4000);
                    }

                } else if (response.action === 'error') {
                    $('#formarea').append("<div class='alert alert-danger' style='margin-top: 20px' onclick='jQuery(this).fadeOut();'><i class='fas fa-exclamation-triangle'></i> " + response.message + "</div>");
                } else {
                    if (response.action === 'payment') {
                        $('#formarea').html(response.paymentform);
                    } else {

                        showAlerts([response.message], 'danger');
                    }
                }
            },
            error: function (res) {
                console.log(res);
            }
        };
        $form.on('submit', function (e) {
            e.preventDefault();
            if (validateForm()) {
                $form.find('button[type=submit]').html(submit_button_busy_label);
                $(this).ajaxSubmit(options);
            }
            return false;
        });

        $('.conditioned').each(function () {
            var cur_field_id = $(this).attr('id');
            cur_conditioned_fields = $(this).data('cond-fields');
            cur_cond_fields = cur_conditioned_fields.split('|');
            for (i = 0; i < cur_cond_fields.length; i++) {
                var cond_field = cur_cond_fields[i].split(':');
                addConditionClass(jQuery('#' + cond_field[0]), cur_field_id);
            }
            $('.cond_filler_' + cur_field_id).each(function () {
                if ($(this).attr('type') == 'checkbox' || $(this).attr('type') == 'radio') {
                    $(this).on('change', function () {
                        applyRule(cur_field_id);
                    });
                } else if ($(this).attr('type') == 'text' || $(this).attr('type') == 'email') {
                    $(this).on('keyup', function () {
                        applyRule(cur_field_id);
                    });
                } else {
                    $(this).on('change', function () {
                        applyRule(cur_field_id);
                    });
                }
            });
        });


        function showAlerts(msgs, type) {
            jQuery('.formnotice').slideUp();
            alert_box = '<div style="margin-top: 20px" class="alert formnotice alert-' + type + ' disappear"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
            for (i = 0; i < msgs.length; i++) {
                alert_box += '' + msgs[i] + '<br/>';
            }
            alert_box += '</div>';
            jQuery('#form-<?php echo $id; ?>').append(alert_box);

        }

        function addConditionClass(field_id, cond_class) {
            jQuery(field_id).each(function () {
                if (jQuery(this).is('input') || jQuery(this).is('select'))
                    jQuery(this).addClass('cond_filler_' + cond_class);
                jQuery(this).children().each(function () {
                    addConditionClass(jQuery(this), cond_class);
                })
            });
            return false;
        }

        function compareRule(cmp_operator, cmp_value, input_value) {
            var comp_res = false;
            switch (cmp_operator) {
                case 'is':
                    comp_res = input_value === cmp_value;
                    break;
                case 'is-not':
                    comp_res = input_value !== cmp_value;
                    break;
                case 'less-than':
                    comp_res = input_value < cmp_value;
                    break;
                case 'greater-than':
                    comp_res = input_value > cmp_value;
                    break;
                case 'starts-with':
                    comp_res = input_value.indexOf(cmp_value) === 0;
                    break;
                case 'contains':
                    comp_res = input_value.indexOf(cmp_value) !== -1;
                    break;
                case 'ends-with':
                    comp_res = input_value.indexOf(cmp_value) === (input_value.length - cmp_value.length);
                    break;
                default:
                    comp_res = false;
                    break;

            }

            return comp_res;
        }

        function applyRule(field_id) {

            var this_conditions = jQuery('#' + field_id).data('cond-fields').split('|');
            var this_action = jQuery('#' + field_id).data('cond-action').split(':');
            var tmp_res = false;
            var match_all = this_action[1] === 'all';
            var matched = null;

            $(this_conditions).each(function (index, condition) {
                condition = condition.split(":");
                var compare_field = condition[0];
                var operator = condition[1];
                var compare_with = condition[2];
                if (compare_field !== '') {
                    var input_value = 'not-found';
                    var input_field = $('#' + compare_field).find('.cond_filler_' + field_id);
                    if (input_field[0].type === 'radio' || input_field[0].type === 'checlbox')
                        input_value = $('#' + compare_field + ' .cond_filler_' + field_id + ':checked').val();
                    else
                        input_value = input_field.val();
                    if (matched === null) matched = compareRule(operator, compare_with, input_value);
                    else {
                        if (match_all) matched = matched && compareRule(operator, compare_with, input_value);
                        else matched = matched || compareRule(operator, compare_with, input_value);
                    }
                }
            });

            if (matched) {
                jQuery('#' + field_id).removeClass('hide');
            } else {
                jQuery('#' + field_id).addClass('hide');
            }
        }

        <?php if((int)get_option('__wplf_chosen_js', 0) === 1){ ?>
        if ($('#form-<?php echo $form_id; ?> select').length > 0)
            $('#form-<?php echo $form_id; ?> select:not(.ncs)').chosen();
        <?php } ?>

    });
</script>

