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
        <div class="panel-heading"><?= esc_attr__( 'Row', LF_TEXT_DOMAIN ); ?></div>
        <table class="options table table-bordered" id="option_rows_<?php echo $fieldindex ?>">
            <tbody>
            <?php for ($i = 0; $i < count(wplf_valueof($field_infos, "{$fieldindex}/options/row", [ "default" => ["default"]])); $i++): ?>
                <tr>
                    <td><input type="text" rel="<?= $fieldindex ?>"
                               name="contact[fieldsinfo][<?php echo $fieldindex; ?>][options][row][]"
                               class="form-control input-sm <?= $fieldindex ?>_name radio_option_label" placeholder="Name"
                               value="<?php echo wplf_valueof($field_infos, "{$fieldindex}/options/row/{$i}"); ?>"/>
                    </td>
                    <td style="width: 48px;">
                        <div class="btn-htoup">
                            <a href="#" class="del-option btn btn-danger btn-block btn-sm"
                               rel="rows_<?php echo $fieldindex ?>"><i
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
                       rel="rows_<?php echo $fieldindex ?>"><i
                                class="fa fa-plus-circle"></i> <?= esc_attr__( 'Add New Row', LF_TEXT_DOMAIN ); ?></a>
                </th>
            </tr>
            </tfoot>
        </table>
        <div class="panel-heading"><?= esc_attr__( 'Columns', LF_TEXT_DOMAIN ); ?></div>
        <table class="options table table-bordered" id="option_columns_<?php echo $fieldindex ?>">
            <tbody>
            <?php for ($i = 0; $i < count(wplf_valueof($field_infos, "{$fieldindex}/options/column", [ "default" => ["default"]])); $i++): ?>
                <tr>
                    <td><input type="text" rel="<?= $fieldindex ?>"
                               name="contact[fieldsinfo][<?php echo $fieldindex; ?>][options][column][]"
                               class="form-control input-sm <?= $fieldindex ?>_column radio_option_label" placeholder="Column"
                               value="<?php echo wplf_valueof($field_infos, "{$fieldindex}/options/column/{$i}"); ?>"/>
                    </td>
                    <td style="width: 48px;">
                        <div class="btn-htoup">
                            <a href="#" class="del-option btn btn-danger btn-block btn-sm"
                               rel="columns_<?php echo $fieldindex ?>"><i
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
                       rel="columns_<?php echo $fieldindex ?>"><i
                                class="fa fa-plus-circle"></i> <?= esc_attr__( 'Add New Column', LF_TEXT_DOMAIN ); ?></a>
                </th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>

