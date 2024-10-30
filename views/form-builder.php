<?php

use LiveForms\__\__;

if (!defined('ABSPATH')) die('!');
if (isset($form_data)) {
    /**
     * @variable    $checked_fields
     * @uses        Contains list of fields that were checked
     */
    $checked_fields = isset($form_data['fields']) ? $form_data['fields'] : array();
    $form_layout = __::valueof($form_data, 'form_layout');
    $column_width = __::valueof($form_data, 'col_width');
    $row_settings = __::valueof($form_data, 'row_settings');
    /**
     * @variable    $field_infos
     * @uses        Contains info on each field of the form
     */
    $field_infos = isset($form_data['fieldsinfo']) ? $form_data['fieldsinfo'] : '';

    if (count($checked_fields) > 0 && !$form_layout) {
        $row_id = "row_" . uniqid();
        $column_id = "col_" . uniqid();
        $form_layout = [$row_id => [$column_id => $checked_fields]];
        $column_width = [$column_id => 12];
    }

} else {
    $column_width = $form_layout = $checked_fields = $form_data = array();
}

$fields = $commonfields;
?>

<!-- Preprocessing starts -->
<div class="w3eden">
    <div id="liveforms-admin-container" class="liveforms-builder">
        <nav class="navbar navbar-default navbar-fixed-top-">

            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <div class="navbar-brand">
                    <div class="d-flex">
                        <div class="logo">
                            <img src="<?= LF_BASE_URL ?>assets/images/liveforms-logo.png" style="width: 40px" alt="LF"/>
                        </div>
                        <div>
                            <strong><?= __('Customize Form', LF_TEXT_DOMAIN); ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <ul class="nav navbar-nav">
                <li>
                    <button type="button" class="btn btn-primary btn-shortcode" data-toggle="modal"
                            data-target="#shortcode"><i class="fas fa-code"></i> <?php _e('Embed', 'liveforms') ?>
                    </button>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="active"><a href="#builder" data-toggle="tab" data-target="#builder"><i
                                class="fas fa-tools"></i> <?= esc_attr__('Builder', LF_TEXT_DOMAIN); ?></a></li>
                <li><a href="#settings" data-toggle="tab" data-target="#settings"><i
                                class="fas fa-cogs"></i> <?= esc_attr__('Settings', LF_TEXT_DOMAIN); ?></a></li>
                <li><a href="<?= home_url('/?lfpreview=' . get_the_ID()); ?>" target="_blank"><i
                                class="fas fa-laptop"></i> <?= esc_attr__('Preview', LF_TEXT_DOMAIN); ?></a></li>
                <li>
                    <button type="button" class="btn btn-lg btn-primary" id="saveformbtn"><i
                                class="fas fa-hdd"></i> <?= esc_attr__('Save Form', LF_TEXT_DOMAIN); ?></button>
                </li>
                <li><a href="edit.php?post_type=form" class="close-btn"><i class="fas fa-times-circle"></i></a></li>
            </ul>

        </nav>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active" id="builder">
                <div class="media">
                    <?php require "builder/sidebar.php"; ?>
                    <?php require "builder/preview.php"; ?>
                </div>
            </div>
            <?php require "builder/settings.php"; ?>
        </div>


        <div class="modal fade" id="pronotice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content text-center">
                    <div class="modal-body" style="font-size: 12pt;padding: 40px">
                        <i class="fas fa-lock  fa-2x text-muted"></i><br/>
                        <h4 class="modal-title" id="myModalLabel"
                            style="font-size: 14pt;font-weight: 800;letter-spacing: 1px;margin: 15px 0">Pro Feature</h4>
                        This option is not available on your plan. Please upgrade to the PRO plan to unlock all these
                        awesome features.
                        <div class="d-block  text-danger" style="margin: 10px 0"><i class="fas fa-times-circle"></i>
                            <strike>80% discount</strike> <i class="fas fa-times-circle"></i></div>
                        Yes, no deceptive discount offer!<br/>Get pro for $49.00 only.
                    </div>
                    <div class="modal-footer text-center" style="margin: 0; text-align: center;background: #f5f5f5">
                        <a target="_blank" href="https://wpliveforms.com/form-builder-pricing-plans-wordpress-plugin/"
                           class="btn btn-success btn-lg"
                           style="border-radius: 3px;font-weight: 600;letter-spacing: 1px"><?= esc_attr__('Upgrade To Pro', LF_TEXT_DOMAIN); ?></a>
                    </div>
                    <div class="modal-footer  text-center" style="margin: 0; text-align: center;">
                        <a href="<?= admin_url('/edit.php?post_type=form&page=wplf-settings&tab=license'); ?>"><?= esc_attr__('Already have pro?', LF_TEXT_DOMAIN); ?></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="shortcode" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog  modal-sm" role="document" style="width: 350px">
                <div class="modal-content text-center">
                    <div class="modal-body" style="font-size: 12pt;padding: 20px">
                        <?= esc_attr__('Copy the following shortcode', LF_TEXT_DOMAIN); ?>:
                    </div>
                    <div class="modal-footer text-center" style="margin: 0; text-align: center;background: #f5f5f5">
                        <div class="input-group input-group-lg">
                            <input onclick="WPLF.copy('__wplfsc')" id="__wplfsc" type="text"
                                   class="form-control text-center shortcode-code bg-white input-lg" readonly="readonly"
                                   value="<?php echo "[liveform form_id={$form_post_id}]" ?>"/>
                            <div class="input-group-btn ttip"
                                 title="<?= esc_attr__('Click to copy the shortcode', LF_TEXT_DOMAIN); ?>">
                                <button type="button" class="btn btn-secondary" onclick="WPLF.copy('__wplfsc')"><i
                                            class="fa fa-copy"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer  text-center"
                         style="margin: 0; text-align: center;font-size: 12pt;padding: 20px">
                        <?= esc_attr__('and put it in page or post', LF_TEXT_DOMAIN); ?>.
                    </div>
                </div>
            </div>
        </div>
        <!-- Preprocessing ends -->
        <!-- Teamplates start -->
        <!--
            @script #template
            @uses   To populate 'Settings' panel with
                    'Required message' and 'Validation'
                    type list when [Required] is checked
            @access Mustache (Theme engine) via attr('id')
        -->
        <?php
        foreach ($fields as $fieldclass) {
            if (method_exists($fieldclass, 'configuration_template')) {
                $tmp_obj = new $fieldclass();
                echo $tmp_obj->configuration_template();
            }
        }
        foreach ($generic_fields as $fieldclass) {
            if (method_exists($fieldclass, 'configuration_template')) {
                $tmp_obj = new $fieldclass();
                echo $tmp_obj->configuration_template();
            }
        }
        foreach ($advanced_fields as $fieldclass) {
            if (method_exists($fieldclass, 'configuration_template')) {
                $tmp_obj = new $fieldclass();
                echo $tmp_obj->configuration_template();
            }
        }
        foreach ($custom_fields as $fieldclass) {
            if (method_exists($fieldclass, 'configuration_template')) {
                $tmp_obj = new $fieldclass();
                echo $tmp_obj->configuration_template();
            }
        }
        ?>

    </div>
    <!-- Advanced field part end -->
    <!-- Teamplates end -->
    <!-- Engine functions start -->
    <script type="text/x-mustache" id="wplf-col-template">
                        <div class="wplf-col col-md-{{width}}" id="{{col_id}}" data-width="{{width}}">
                            <ul class="list-group noborder wplf-field-container ui-droppable ui-sortable"></ul>
                            <input type="hidden" id="{{col_id}}_width" name="contact[col_width][{{col_id}}]" value="{{width}}">
                            <div class="wplf-col-controls">
                                <div class="btn-group btn-group-sm">
                                    <div type="button" class="btn btn-secondary" disabled="disabled">
                                        <?= esc_attr__('Column', LF_TEXT_DOMAIN); ?>
                                    </div>
                                    <div type="button" class="btn btn-secondary move-col"><i class="fa fa-arrows-alt"></i></div>
                                    <button type="button" class="btn btn-danger btn-del-col" data-colid="#{{col_id}}"><i class="fa fa-times"></i></button>
                                    <button type="button" class="btn btn-secondary btn-cshrink" data-colid="#{{col_id}}" title="<?= esc_attr__('Decrease Width', LF_TEXT_DOMAIN); ?>"><i class="fa fa-minus"></i></button>
                                    <button type="button" class="btn btn-secondary btn-cxpand" data-colid="#{{col_id}}" title="<?= esc_attr__('Increase Width', LF_TEXT_DOMAIN); ?>"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>

    </script>
    <script type="text/x-mustache" id="wplf-row-template">
                    <div class="row" id="{{row_id}}">
                        <div class="wplf-row-controls">
                                <div class="btn-group btn-group-sm">
                                    <div type="button" class="btn btn-secondary" disabled="disabled"><?= esc_attr__( 'Section/Page', LF_TEXT_DOMAIN ); ?></div>
                                    <div type="button" class="btn btn-secondary move-row"><i class="fa fa-arrows-alt"></i></div>
                                    <button type="button" class="btn btn-secondary cog-trigger" data-fieldsettings="#row_settings_{{row_id}}" data-rowid="{{row_id}}"><i class="fa fa-cog"></i></button>
                                    <button type="button" class="btn btn-secondary btn-insert-col" data-rowid="#{{row_id}}" title="<?= esc_attr__( 'Insert Column', LF_TEXT_DOMAIN ); ?>"><i class="fa fa-columns"></i>
                                    <button type="button" class="btn btn-danger btn-del-row" data-rowid="#{{row_id}}"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                    </div>

    </script>
    <script type="text/javascript">
        var drag_start = 0,
            layout_drag_start = 0,
            icode_pos = '';

        function throttle(fn, time) {
            var t = 0;
            return function () {
                var args = arguments,
                    ctx = this;

                clearTimeout(t);

                t = setTimeout(function () {
                    fn.apply(ctx, args);
                }, time);
            };
        }

        function add_field(obj, position, dropzone) {
            //Add field with form
            var $ = jQuery;

            if (jQuery(obj).attr('data-options') != undefined) {
                var tmp = jQuery("#template-options").html();
                var tmp_settings = jQuery("#template-options-settings").html();
            } else if (jQuery(obj).attr('data-template') != undefined) {
                var tmp = jQuery("#template-" + jQuery(obj).attr('data-template')).html();
                var tmp_settings = jQuery("#template-" + jQuery(obj).attr('data-template') + "-settings").html();
            } else {
                var tmp = jQuery("#template").html();
                var tmp_settings = jQuery("#template-settings").html();
            }

            var ID = obj.attr('rel') + "_" + new Date().getTime();
            console.log(dropzone);
            var row_id, col_id;
            col_id = $(dropzone).parent('div').attr('id');
            row_id = $('#' + col_id).parent('.row').attr('id');
            var form_layout_field = "<input type='hidden' name='contact[form_layout][" + row_id + "][" + col_id + "][" + ID + "]' value='" + jQuery(obj).attr('data-template') + "' />";

            jQuery(position).after(Mustache.render(tmp, {
                title: obj.attr('title'),
                value: obj.attr('rel'),
                ID: ID
            }));

            jQuery('#field_'+ID).append(form_layout_field);

            jQuery('#tab-field-settings').append(Mustache.render(tmp_settings, {
                title: obj.attr('title'),
                value: obj.attr('rel'),
                ID: ID
            }));

            // Update the form fields list
            field_label = jQuery('#label_' + ID).html();
            new_field_meta = Mustache.render(jQuery('.fl-template').html(), {
                INDEX: ID,
                CLASS: jQuery(obj).attr('data-template'),
                LABEL: field_label
            });
            jQuery('#selectedfields-list').html(jQuery('#selectedfields-list').html() + new_field_meta);
            //if(jQuery(obj).attr('rel') === 'Grid')
            //droppable_init();
            return ID;
        }


        function droppable_init() {
            jQuery(".wplf-field-container").droppable({
                activeClass: "ui-state-highlight",
                refreshPositions: true,
                drop: function () {
                    jQuery('#wplf-form-preview .wplf-form-field').removeClass('list-group-item').hide();
                }
            }).sortable({
                //connectWith: ".wplf-field-container",
                receive: function (event, ui) {
                    var position = ui.helper;
                    var obj = position.find("a.add");
                    var ID = add_field(obj, position, this);
                    return false;
                }
            });
        }

        function append_field(obj) {

            if (obj.attr('data-options') !== undefined) {
                var tmp = jQuery("#template-options").html();
                var tmp_settings = jQuery("#template-options-settings").html();
            } else if (obj.attr('data-template') != undefined) {
                var tmp = jQuery("#template-" + obj.attr('data-template')).html();
                var tmp_settings = jQuery("#template-" + obj.attr('data-template') + "-settings").html();
            } else {
                var tmp = jQuery("#template").html();
                var tmp_settings = jQuery("#template-settings").html();
            }

            var ID = obj.attr('rel') + "_" + new Date().getTime();

            jQuery('#selectedfields').append(Mustache.render(tmp, {
                title: obj.attr('title'),
                value: obj.attr('rel'),
                ID: ID
            }));
            jQuery('#tab-field-settings').append(Mustache.render(tmp_settings, {
                title: obj.attr('title'),
                value: obj.attr('rel'),
                ID: ID
            }));

            // Update the form fields list
            field_label = jQuery('#label_' + ID).html();
            new_field_meta = Mustache.render(jQuery('.fl-template').html(), {
                INDEX: ID,
                CLASS: jQuery(obj).attr('data-template'),
                LABEL: field_label
            });
            jQuery('#selectedfields-list').html(jQuery('#selectedfields-list').html() + new_field_meta);

            return ID;
        }

        function total_width($row_id) {
            var total_width = 0;
            var $ = jQuery;
            console.log($row_id);
            $($row_id + " .wplf-col").each(function (){
                total_width += parseInt($(this).data('width'));
            });
            return total_width;
        }

        function add_row(position)
        {
            var $ = jQuery;
            var row_id = WPLF.uniqueID("row_");
            var column_id = WPLF.uniqueID("col_");
            var row_html = $('#wplf-row-template').html();
            row_html = Mustache.render(row_html, {
                row_id: row_id
            });
            var row_settings = $('#layout-tpl').html();
            row_settings = Mustache.render(row_settings, {
                ID: row_id
            });
            if(position)
            {
                $(position).after(row_html);
                $(position).remove();
            } else {
                $('#wplf-form-preview').append(row_html);
            }
            $('#tab-field-settings').append(row_settings);
            add_column('#'+row_id, 12);
            //$(position).after("<div class='row' id='" + row_id + "'><div class='wplf-col col-md-12' id='" + column_id + "'><ul class='list-group noborder wplf-field-container'></ul><input type='hidden' id='" + column_id + "_width' name='contact[col_width][" + column_id + "]' value='12' /></div><div class='wplf-controls'><div class='btn-group'><div type='button' class='btn btn-xs btn-primary move-row'><i class='fa fa-arrows-alt'></i></div><button type='button' class='btn btn-xs btn-info cog-trigger' data-fieldsettings='#row_settings' data-rowid='" + row_id + "'><i class='fa fa-cog'></i></button><button type='button' class='btn btn-xs btn-danger'><i class='fa fa-times'></i></button></div></div></div>");
            //$(position).remove();
            layout_drag_start = 0
            droppable_init();
        }

        function add_column($row_id, $column_width) {
            var $ = jQuery;
            var col_id = WPLF.uniqueID('col_');
            var column_html = $('#wplf-col-template').html();
            column_html = Mustache.render(column_html, {
                col_id: col_id,
                width: $column_width
            });
            $($row_id).find('.wplf-row-controls').before(column_html);
            droppable_init();
        }

        function change_col_width($col_id, $new_width) {
            var $ = jQuery;
            //$($rpw_id).append("<div class='wplf-col col-md-" + $column_width + "'><ul class='list-group wplf-field-container'></ul></div>");
            var $current_width = $($col_id).data('width');
            if ($new_width > 12) $new_width = 12;
            if ($new_width < 1) $new_width = 1;
            $($col_id + '_width').val($new_width);
            $($col_id).data('width', $new_width).addClass('col-md-' + $new_width);
            if ($new_width !== $current_width)
                $($col_id).removeClass('col-md-' + $current_width);
        }

        jQuery(function ($) {

            var $body = $('body');

            if($('#wplf-form-preview .row').length === 0)
                add_row(null);

            $('#saveformbtn').on('click', function () {
                $('#publish').trigger('click');
            });

            $('#post').on('submit', function (e) {
                jQuery('#builder-panel, #content-tabs').addClass('blockui');
                $('#saveformbtn').attr('disabled', 'disabled');
                if (adminpage === 'post-php') {
                    e.preventDefault();
                    $('#post').ajaxSubmit({
                        success: function (response) {
                            jQuery('#builder-panel, #content-tabs').removeClass('blockui');
                            $('#saveformbtn').removeAttr('disabled')
                        }
                    });
                }
            });

            //Form Fields
            $('.availablefields li').draggable({
                start: function () {
                    drag_start = 1;
                },
                //connectToSortable: "#selectedfields",
                connectToSortable: ".wplf-field-container",
                helper: 'clone',
                revert: "invalid",
                refreshPositions: true
            });

            $("#wplf-form-preview").droppable({
                activeClass: "ui-state-highlight",
                refreshPositions: true,
                accept: ".wplf-layout",
                drop: function (event, ui) {
                    var position = ui.draggable;
                    $('#wplf-form-preview .wplf-layout').hide();
                    layout_drag_start = 0
                    return false;

                }
            }).sortable({
                handle: '.move-row',
                receive: function (event, ui) {
                    var row_id = WPLF.uniqueID("row_");
                    var column_id = WPLF.uniqueID("col_");
                    //$(ui.helper).after("<div class='row' id='" + row_id + "'><div class='wplf-col col-md-12' id='" + column_id + "'><ul class='list-group noborder wplf-field-container'></ul><input type='hidden' id='" + column_id + "_width' name='contact[col_width][" + column_id + "]' value='12' /></div><div class='wplf-controls'><div class='btn-group'><div type='button' class='btn btn-xs btn-primary move-row'><i class='fa fa-arrows-alt'></i></div><button type='button' class='btn btn-xs btn-info cog-trigger' data-fieldsettings='#row_settings' data-rowid='" + row_id + "'><i class='fa fa-cog'></i></button><button type='button' class='btn btn-xs btn-danger'><i class='fa fa-times'></i></button></div></div></div>");
                    //$(ui.helper).remove();
                    add_row(ui.helper);
                    layout_drag_start = 0
                    droppable_init();
                    return false;
                }
            });

            $('#form-layouts .wplf-layout').draggable({
                start: function () {
                    layout_drag_start = 1;
                },
                connectToSortable: "#wplf-form-preview",
                helper: "clone",
                revert: "invalid",
                refreshPositions: true
            });


            //$("#selectedfields, .wplf-field-container").droppable({
            /*$(".wplf-field-container").droppable({
                activeClass: "ui-state-highlight",
                refreshPositions: true,
                drop: function (event, ui) {
                    var position = ui.draggable;
                    var obj = position.find("a.add");

                    if(ds===1)
                    var ID = add_field(obj, position);
                    ds = 0;
                    return false;
                }
            }).sortable({connectWith: ".wplf-field-container"});*/

            droppable_init();

            //$(".wplf-field-container").sortable();
            $('#settings-tabs li:eq(0) a').tab('show');
            //$("#selectedfields, .wplf-field-container").sortable();

            /*$('.availablefields .add').click(function () {
                var obj = $(this);
                var ID = append_field(obj);
                return false;
            });*/

            $body.on('click', '.cog-trigger', function () {
                $('#form-options-tabs a#field-settings-tab').tab('show');
                $('#tab-field-settings div.cog').hide();
                $('.selected-field').removeClass('selected-field');
                $($(this).data('field')).addClass('selected-field');
                $($(this).data('fieldsettings')).show();
                //if ($(this).data('fieldsettings') === '#row_settings') {
                //    $('#__row_id').html('#' + $(this).data('rowid'));
                //}
                return false;
            });


            $body.on('click', '.btn-cxpand', function () {
                var col_id = $(this).data('colid');
                var col_width = $(col_id).data('width');
                var new_col_width = parseInt(col_width) + 1;
                change_col_width(col_id, new_col_width);
            });

            $body.on('click', '.btn-cshrink', function () {
                var col_id = $(this).data('colid');
                var col_width = $(col_id).data('width');
                var new_col_width = parseInt(col_width) - 1;
                console.log(new_col_width);
                change_col_width(col_id, new_col_width);
            });

            $body.on('click', '.btn-insert-col', function () {
                var row_id = $(this).data('rowid');
                var _total_width = total_width(row_id);
                console.log(_total_width);
                var new_col_width = 0;
                if(_total_width >= 12) {
                    var col_width = $(row_id + " .wplf-col").last().data('width');
                    new_col_width = parseInt(parseInt(col_width) / 2);
                    var prev_column_width = col_width - new_col_width;
                    prev_column_width = prev_column_width < 1 ? 1 : prev_column_width;
                    change_col_width('#' + $(row_id + " .wplf-col").last().attr('id'), prev_column_width);
                } else
                    new_col_width = 12 - _total_width;
                add_column(row_id, new_col_width);
            });

            $body.on('click', '.req', function () {
                $(this).parent().next('.req-params').slideToggle();
                //return false;
            });

            $body.on('click', '.cond', function () {
                $(this).parent().next('.cond-params').slideToggle();
            });

            $body.on('click', '.clone-field', function () {
                var src_fieldindex = $(this).data('fieldindex');
                var src_type = $(this).data('type');
                var src_element_id = '#field_' + src_fieldindex;
                var src_cog_id = '#cog_' + src_fieldindex;
                var src_content = $(src_element_id).html();
                var src_cog_content = $(src_cog_id).html();
                var regexs = new RegExp(src_fieldindex, 'ig')
                var clone_fieldindex = src_type + '_' + Date.now();
                var clone_content = src_content.replace(regexs, clone_fieldindex);
                var clone_cog_content = src_cog_content.replace(regexs, clone_fieldindex);
                $(src_element_id).after('<li class="list-group-item cog-trigger selected-field" data-field="#field_' + clone_fieldindex + '" data-fieldsettings="#cog_' + clone_fieldindex + '" data-type="' + src_type + '" id="field_' + clone_fieldindex + '">' + clone_content + '</li>');
                $(src_cog_id).after('<div class="cog" id="cog_' + clone_fieldindex + '" style="">' + clone_cog_content + '</li>');
            });

            $body.on('click', '.dropdown-menu.field_value a', function () {
                $($(this).data('target')).val($(this).data('value'));
                $($(this).data('target').replace('_value', '')).val($(this).data('value'));
            });

            $('.req').each(function () {
                if ($(this).attr('checked') == 'checked')
                    $(this).parent().next('.req-params').slideDown();
                //return false;
            });
            $('.cond').each(function () {
                if ($(this).attr('checked') == 'checked')
                    $(this).parent().next('.cond-params').slideDown();
                //return false;
            });

            $('.cond-operator').on('change', function () {
                $op_selection = $(this).val();
            });

            $body.on('click', '.add-cond-option', function (e) {
                e.preventDefault();
                //$('#conditions-'+this.rel).append(jQuery('#cond-template-'+this.rel).html());
                _.templateSettings = {
                    interpolate: /\{\{(.+?)\}\}/g
                };
                var newCond = _.template($('#cond-logic-template').html())
                $('#conditions-' + this.rel).append(newCond({
                    fieldindex: this.rel,
                    key: $('#conditions-' + this.rel + ' .cond-row').length
                }));
                $('#conditions-' + this.rel + ' .cond-row:last-child .cond-field-selector').find('option[value="' + this.rel + '"]').remove();
                //$('#conditions-'+this.rel ).find('option[value="'+$(this).data('fieldindex')+'"]').remove();
                //$('#conditions-'+this.rel).append(chtml);
                return false;
            });
            $body.on('click', '.del-cond-option', function (e) {
                e.preventDefault();
                $(this).parent().parent().remove();
                return false;
            });
            $('.is-cond-selector').on('change', function () {
                $(this).next().next('.is-cond-data').val($(this).val());
            });


            $body.on('click', '.remove', function () {
                if (confirm('Are you sure?')) {
                    $('#' + $(this).attr('rel')).slideUp(function () {
                        $(this).remove();
                        jQuery('#selectedfields-list #' + jQuery(this).attr('id').substring(6)).remove();
                    });
                }
                return false;
            });

            $body.on('keyup', '.form-field-label', function () {
                if ($(this).attr('data-field-type') === 'separator') {
                    $($(this).attr('data-target')).html('[Separator] ' + $(this).val());
                } else {
                    $($(this).attr('data-target')).html($(this).val());
                }
                jQuery('#' + jQuery(this).attr('data-target').substring(7) + '_LABEL').attr('rel', jQuery(this).val());

            });

            $body.on('keyup', '.form-field-placeholder', function () {
                if ($(this).data('field-type') === 'separator') {
                    return;
                } else {
                    var $_field = $($(this).data('placeholder') + ' .form-control');
                    if (typeof $_field === 'object')
                        $_field.attr('placeholder', $(this).val());
                }
                //$('#'+$(this).data('target').substring(7)+'_LABEL').attr('rel', $(this).val());

            });

            $('#buttontext').on('keyup', function () {
                $($(this).attr('data-target')).html($(this).val());
            });

            $('#buttontext').on('change', function () {
                $($(this).attr('data-target')).html($(this).val());
            });

            $('.wplf-field-container').hover(function () {
                $(this).removeClass('noborder');
            }, function () {
                $(this).addClass('noborder');
            });

            $('.payment-method-select').on('change', function () {
                div = $(this).attr('data-config-panel');
                if ($('#configs-' + div).hasClass('hidden')) {
                    $('#configs-' + div).removeClass('hidden');
                } else {
                    $('#configs-' + div).addClass('hidden');
                }
            });

            $('.field-preview input,.field-preview select,.field-preview textarea').attr('disabled', 'disabled');

            $('#button-color-selector, #button-size-selector, #button-position-selector').on('change', function () {
                $('#submit_button_sample').removeClass();
                new_class = $('#button-color-selector').val() + " " + $('#button-size-selector').val();
                if ($('#button-position-selector').val() === 'block') {
                    new_class += " btn-block";
                    $('#form-button').attr('class', '');
                } else
                    $('#form-button').attr('class', '' + $('#button-position-selector').val());
                $('#submit_button_sample').addClass('btn btn-' + new_class);
            });

            <?php if(!is_pro()){  ?>
            $('body').on('click', '#availableafields.pro-only li, #availableafields.pro-only li a', function (e) {
                e.preventDefault();
                $('#pronotice').modal('show');
            });
            <?php } ?>

        });
        <?php if(is_pro()){  ?>
        /* For conditional logic */
        jQuery(function ($) {
            var form_selections = "<option value=''>Selected a field</option>";
            $('#selectedfields-list .fl-field').each(function () {
                this_id = $(this).attr('id');
                var nonCondFields = ["Address", "Captcha", "Daterange", "File", "Fullname", "Location", "Mathresult", "Pageseparator", "Paratext", "Phone", "Password", "Grid"];
                if (this_id != '{{INDEX}}' && $.inArray($('#' + this_id + '_CLASS').attr('rel'), nonCondFields) == -1) {
                    form_selections += "<option value='" + this_id + "'>" + $('#' + this_id + '_LABEL').attr('rel') + "</option>";
                }
            });

            function populate_conditional_field() {
                var $body = $('body');

                $('#tab-field-settings .cond-field-selector').each(function () {
                    var fieldindex = $(this).data('fieldindex');
                    var _fieldindex = $(this).data('selection');
                    var field = _fieldindex.split("_");
                    var target = $(this).data('target');
                    var cond_value = $(target).data('cond_value');
                    cond_value = cond_value === undefined ? '' : cond_value;
                    field = field[0];
                    var fields = ['Radio', 'Checkbox', 'Select'];

                    if (fields.includes(field)) {
                        var cond_value_selector = "<select  name='contact[fieldsinfo][" + fieldindex + "][condition][value][]' class='form-control cond-value-selector is-cond-data'>";
                        $('.' + $(this).val() + '_value').each(function (index) {
                            var selected = cond_value === $(this).val() ? 'selected=selected' : '';
                            cond_value_selector += "<option " + selected + " value='" + $(this).val() + "'>" + $(this).val() + "</option>";
                        });
                        cond_value_selector += "</select>";
                    } else {
                        var cond_value_selector = '<input type="text" value="' + cond_value + '" class="is-cond-data form-control" name="contact[fieldsinfo][' + fieldindex + '][condition][value][]">';
                    }

                    $(target).html(cond_value_selector);
                });
            }

            setTimeout(populate_conditional_field, 3000);

            $('.cond-field-selector , .cond-operator , .is-cond-selector , .is-cond-text').each(function () {
                if ($(this).hasClass('cond-field-selector')) {
                    $(this).html(form_selections);
                    $(this).find('option[value="' + $(this).data('fieldindex') + '"]').remove();
                }
                var preselected_var = $(this).attr('data-selection');
                if ($(this).attr('type') === 'text') $(this).val(preselected_var);
                else {
                    $(this).children().each(function () {
                        if ($(this).attr('value') === preselected_var)
                            $(this).attr('selected', 'selected');
                    });
                }
            });

            var $body = $('body');
            $body.on('change', '.cond-field-selector', function () {
                var fieldindex = $(this).data('fieldindex');
                var _fieldindex = $(this).val();
                var field = _fieldindex.split("_");
                var target = $(this).data('target');
                var cond_value = $(target).data('cond_value');
                cond_value = cond_value === undefined ? '' : cond_value;
                field = field[0];
                var fields = ['Radio', 'Checkbox', 'Select'];
                if (fields.includes(field)) {
                    var cond_value_selector = "<select  name='contact[fieldsinfo][" + fieldindex + "][condition][value][]' class='form-control cond-value-selector is-cond-data'>";
                    $('.' + $(this).val() + '_value').each(function (index) {
                        var selected = cond_value === $(this).val() ? 'selected=selected' : '';
                        cond_value_selector += "<option " + selected + " value='" + $(this).val() + "'>" + $(this).val() + "</option>";
                    });
                    cond_value_selector += "</select>";
                } else {
                    var cond_value_selector = '<input type="text" value="' + cond_value + '" class="is-cond-data form-control" name="contact[fieldsinfo][' + fieldindex + '][condition][value][]">';
                }
                $(target).html(cond_value_selector);
            });


            $('#selectedfields-list').bind('DOMNodeInserted DOMNodeRemoved DOMSubtreeModified', throttle(function () {
                var form_selections = "<option value=''>Selected a field</option>";
                $('#selectedfields-list .fl-field').each(function () {
                    this_id = $(this).attr('id');
                    var nonCondFields = ["Address", "Captcha", "Daterange", "File", "Fullname", "Location", "Mathresult", "Pageseparator", "Paratext", "Phone", "Password"];
                    if (this_id != '{{INDEX}}' && $.inArray($('#' + this_id + '_CLASS').attr('rel'), nonCondFields) == -1) {
                        form_selections += "<option value='" + this_id + "'>" + $('#' + this_id + '_LABEL').attr('rel') + "</option>";
                    }
                });
                $('.cond-field-selector , .cond-operator , .is-cond-selector , .is-cond-text').each(function () {
                    if ($(this).hasClass('cond-field-selector')) {
                        $(this).html(form_selections);
                        $(this).find('option[value="' + $(this).data('fieldindex') + '"]').remove();
                    }
                    var preselected_var = $(this).attr('data-selection');
                    if ($(this).attr('type') == 'text') $(this).val(preselected_var);
                    else {
                        $(this).children().each(function () {
                            if ($(this).attr('value') == preselected_var)
                                $(this).attr('selected', 'selected');
                        });
                    }
                });


            }, 50));


        });
        <?php } ?>
    </script>

<script type="text/x-mustache" id="layout-tpl">
    <div id="row_settings_{{ID}}" class="cog">
                <div class="panel  panel-default">
                    <div class="panel-body">
                        <strong><?= esc_attr__( 'Section/Page Settings', LF_TEXT_DOMAIN ); ?></strong>
                    </div>
                    <div class="panel-heading" style="background: #fafafa;border-top: 1px solid #eee;border-bottom: 1px solid #eee;"><span class="pull-right">
                            <strong class="text-primary" id="__row_id">#{{ID}}</strong></span><?= esc_attr__( 'ID', LF_TEXT_DOMAIN ); ?></div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label><?= esc_attr__('Section Name', LF_TEXT_DOMAIN); ?></label>
                            <input class="form-control form-field-label" data-target="" type="text" value="" name="contact[row_settings][{{ID}}][label]">
                        </div>
                    </div>
                </div>
            </div>
</script>