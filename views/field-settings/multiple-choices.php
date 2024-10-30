<?php
/**
 * Base: LiveForms
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 4/8/20 22:02
 */
if(!defined("ABSPATH")) die();
//echo "<pre>";print_r($field_infos);

?>
<div class="form-group">
    <div class="panel panel-default">
        <div class="panel-heading"><?= esc_attr__( 'Options', LF_TEXT_DOMAIN ); ?></div>
        <table class="options table table-bordered" id="option_<?php echo $fieldindex ?>">
            <tbody>
            <?php for ($i = 0; $i < count(wplf_valueof($field_infos, "{$fieldindex}/options/name", [ "default" => ["default"]])); $i++): ?>
                <tr>
                    <td><input type="text" rel="<?= $fieldindex ?>"
                               name="contact[fieldsinfo][<?php echo $fieldindex; ?>][options][name][]"
                               class="form-control input-sm <?= $fieldindex ?>_name radio_option_label" placeholder="Name"
                               value="<?php echo wplf_valueof($field_infos, "{$fieldindex}/options/name/{$i}"); ?>"/>
                    </td>
                    <td><input type="text"
                               name="contact[fieldsinfo][<?php echo $fieldindex; ?>][options][value][]"
                               class="form-control input-sm <?= $fieldindex ?>_value" placeholder="Value"
                               value="<?php echo wplf_valueof($field_infos, "{$fieldindex}/options/value/{$i}") ?>"/>
                    </td>
                    <td style="width: 48px;">
                        <div class="btn-htoup">
                            <a href="#" class="del-option btn btn-danger btn-block btn-sm"
                               rel="<?php echo $fieldindex ?>"><i
                                    class="fa fa-times"></i></a>
                        </div>
                    </td>
                </tr>
            <?php endfor; ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="3">
                    <a href="#" class="add-option btn btn-info btn-sm"
                       rel="<?php echo $fieldindex ?>"><i
                                class="fa fa-plus-circle"></i> <?= esc_attr__( 'Add New Option', LF_TEXT_DOMAIN ); ?></a>
                </th>
            </tr>
            </tfoot>
        </table>
        <?php if($this->field_id() === 'Select') { ?>
        <label class="panel-heading d-block"><input <?php checked(1, wplf_valueof($field_infos, "{$fieldindex}/multiselect")); ?> type="checkbox" name="contact[fieldsinfo][<?php echo $fieldindex; ?>][multiselect]" value="1"><span class="checkx"><i class="fas fa-check-double"></i></span> <?= esc_attr__( 'Multi-Select', LF_TEXT_DOMAIN ); ?></label>
        <?php } ?>
        <?php if($this->field_id() !== 'Select') { ?>
        <div class="panel-heading"><?= esc_attr__( 'Option Alignment', LF_TEXT_DOMAIN ); ?></div>
        <div class="panel-body">
            <select class="form-control set-option-alignment" data-target="#field_<?php echo $fieldindex; ?>" name="contact[fieldsinfo][<?php echo $fieldindex; ?>][style]">
                <option value="pos-h"><?= esc_attr__( 'Align Horizontally', LF_TEXT_DOMAIN ); ?></option>
                <option value="pos-v" <?php selected('pos-v', wplf_valueof($field_infos, "{$fieldindex}/style")); ?>><?= esc_attr__( 'Align Vertically', LF_TEXT_DOMAIN ); ?></option>
            </select>
        </div>
        <?php } ?>
    </div>
</div>

