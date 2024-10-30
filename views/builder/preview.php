<?php

use LiveForms\__\__;

if (!defined('ABSPATH')) die('!');

?>
<div class="media-body" id="liveforms-builder-container">

    <div class="panel panel-default" id="builder-panel">
        <div class="panel-heading">
            <div class="input-group input-group-lg">
                <div class="input-group-addon"><span class="input-group-text"><?= esc_attr__( 'Form Name', LF_TEXT_DOMAIN ); ?>:</span></div>
                <input type="text" value="<?php the_title() ?>" placeholder="Untitled Form" name="post_title"
                       required="required" class="form-control"/>
            </div>
        </div>
        <div class="panel-heading" id="form-layouts" style="padding: 10px;text-align: right;background: #f7f9ff">
            <a class="wplf-layout btn-addrow"><i
                        class="fas fa-columns"></i> <?= esc_attr__('Add New Section', LF_TEXT_DOMAIN); ?></a>
        </div>
        <div class="panel-body">

            <div class="container-fluid p-0" id="wplf-form-preview"><?php

                if (is_array($form_layout)) {
                    foreach ($form_layout as $row_id => $form_row) { ?>
                        <div class="row" id="<?= $row_id ?>">
                            <?php foreach ($form_row as $column_id => $column) { ?>
                                <div class="wplf-col col-md-<?= $column_width[$column_id]; ?>" data-width="<?= $column_width[$column_id]; ?>" id="<?= $column_id ?>">
                                    <ul class="list-group noborder wplf-field-container">
                                        <?php
                                        $form_fields_list = '';
                                        $checked_fields = $column;
                                        foreach ($checked_fields as $fieldindex => $fieldid) {
                                            if ($fieldid && class_exists($fieldid)) {
                                                if (!in_array($fieldid, $advanced_fields) || is_pro()) {
                                                    $tmp_obj = new $fieldid();
                                                    if (isset($field_infos[$fieldindex])) {
                                                        $field_infos[$fieldindex]['row_id'] = $row_id;
                                                        $field_infos[$fieldindex]['column_id'] = $column_id;

                                                        echo $tmp_obj->field_preview_html($fieldindex, $fieldid, $field_infos[$fieldindex]);

                                                        $form_fields_list .=
                                                            "<div class='hide fl-field' id='{$fieldindex}'>
                                                    <div class='hide' id='{$fieldindex}_CLASS' rel='{$fieldid}'></div>
                                                    <div class='hide' id='{$fieldindex}_LABEL' rel='".wplf_valueof($field_infos, "$fieldindex/label")."'></div>
                                                </div>";
                                                    }
                                                }
                                            }
                                        }

                                        ?>
                                    </ul>
                                    <input type="hidden" id="<?= $column_id ?>_width" name="contact[col_width][<?= $column_id ?>]" value="<?= $column_width[$column_id]; ?>">
                                    <div class="wplf-col-controls">
                                        <div class="btn-group btn-group-sm">
                                            <div type="button" class="btn btn-secondary" disabled="disabled">
                                                <?= esc_attr__( 'Column', LF_TEXT_DOMAIN ); ?>
                                            </div>
                                            <div type="button" class="btn btn-secondary move-col"><i class="fa fa-arrows-alt"></i></div>
                                            <button type="button" class="btn btn-danger btn-del-col" data-colid="#<?= $column_id ?>"><i class="fa fa-times"></i></button>
                                            <button type="button" class="btn btn-secondary btn-cshrink" data-colid="#<?= $column_id ?>" title="<?= esc_attr__( 'Decrease Width', LF_TEXT_DOMAIN ); ?>"><i class="fa fa-minus"></i></button>
                                            <button type="button" class="btn btn-secondary btn-cxpand" data-colid="#<?= $column_id ?>" title="<?= esc_attr__( 'Increase Width', LF_TEXT_DOMAIN ); ?>"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="wplf-row-controls">
                                <div class="btn-group btn-group-sm">
                                    <div type="button" class="btn btn-secondary" disabled="disabled"><?= esc_attr__( 'Section/Page', LF_TEXT_DOMAIN ); ?></div>
                                    <div type="button" class="btn btn-secondary move-row"><i class="fa fa-arrows-alt"></i></div>
                                    <button type="button" class="btn btn-secondary cog-trigger" data-fieldsettings="#row_settings_<?= $row_id; ?>" data-rowid="<?= $row_id; ?>"><i class="fa fa-cog"></i></button>
                                    <button type="button" class="btn btn-secondary btn-insert-col" data-rowid="#<?= $row_id; ?>" title="<?= esc_attr__( 'Insert Column', LF_TEXT_DOMAIN ); ?>"><i class="fa fa-columns"></i></button>
                                    <button type="button" class="btn btn-danger btn-del-row" data-rowid="#<?= $row_id; ?>"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                }
                ?></div>

            <div class="hide" id="selectedfields-list">
                <div class="hide fl-template">
                    <div class="hide fl-field" id="{{INDEX}}">
                        <div class="hide" id="{{INDEX}}_CLASS" rel="{{CLASS}}"></div>
                        <div class="hide" id="{{INDEX}}_LABEL" rel="{{LABEL}}"></div>
                    </div>
                </div>
                <?php echo $form_fields_list ?>
            </div>
            <div class="<?php if (wplf_valueof($form_data, 'buttonpos') !== 'block') echo wplf_valueof($form_data, 'buttonpos', 'text-right'); ?>"
                 id="form-button" style="padding-top: 0">
                <button id="submit_button_sample" type="button"
                        class="btn btn-<?php echo isset($form_data['buttoncolor']) ? $form_data['buttoncolor'] : 'primary' ?> <?php echo isset($form_data['buttonsize']) ? $form_data['buttonsize'] : '' ?> <?php echo wplf_valueof($form_data, 'buttonpos') === 'block' ? "btn-block" : '' ?>"><?php
                    if (!isset($form_data['buttontext']) || $form_data['buttontext'] == '')
                        echo "Submit";
                    else
                        echo $form_data['buttontext'];
                    ?></button>
            </div>

            <?php include __DIR__ . '/cond-logic-template.php'; ?>

        </div>
        <div class="panel-footer">
            <?php include __DIR__ . '/submit-button-settings.php'; ?>
        </div>
    </div>
</div>
