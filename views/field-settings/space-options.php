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
        <?= __('Height', LF_TEXT_DOMAIN); ?>
    </div>
    <div class="panel-body">
        <input type="number" class="form-control input-group-lg wplf-space-height" data-target="#<?php echo $fieldindex ?>" name="contact[fieldsinfo][<?php echo $fieldindex ?>][height]"
               placeholder="50"
               value="<?php echo(isset($field_infos[$fieldindex], $field_infos[$fieldindex]['height']) ? $field_infos[$fieldindex]['height'] : 50) ?>"/>
    </div>
</div>
