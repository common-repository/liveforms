<?php
/**
 * Base: LiveForms
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 5/8/20 03:49
 */
if (!defined("ABSPATH")) die();
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <?= __('Range Settings', LF_TEXT_DOMAIN); ?>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-4">
                <label>Min:</label>
                <input type="number" class="form-control" name="contact[fieldsinfo][<?php echo $fieldindex ?>][min]"
                       placeholder="0"
                       value="<?php echo(isset($field_infos[$fieldindex]['min']) ? $field_infos[$fieldindex]['min'] : 0) ?>"/>
            </div>
            <div class="col-md-4">
                <label>Max:</label>
                <input type="number" class="form-control" name="contact[fieldsinfo][<?php echo $fieldindex ?>][max]"
                       placeholder="100"
                       value="<?php echo(isset($field_infos[$fieldindex]['max']) ? $field_infos[$fieldindex]['max'] : 100) ?>"/>
            </div>
            <div class="col-md-4">
                <label>Step:</label>
                <input type="number" class="form-control" name="contact[fieldsinfo][<?php echo $fieldindex ?>][step]"
                       placeholder="0"
                       value="<?php echo(isset($field_infos[$fieldindex]['step']) ? $field_infos[$fieldindex]['step'] : 1) ?>"/>
            </div>
        </div>
    </div>
</div>
