<?php
/**
 * Base: LiveForms
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 5/8/20 03:49
 */
if(!defined("ABSPATH")) die();
?>


<div class="panel panel-default">
    <label class="panel-heading d-block">
        <input rel="req-params" class="req" type="checkbox"
               name="contact[fieldsinfo][<?php echo $fieldindex ?>][show_cp]"
               value="1" <?php checked(wplf_valueof($field_infos,"{$fieldindex}/show_cp", false), true); ?> />
        <span class="checkx"><i class="fas fa-check-double"></i></span>
        <?= __('Show confirm password field', LF_TEXT_DOMAIN); ?>
    </label>
    <div class="panel-body req-params"  <?php echo(!wplf_valueof($field_infos,"{$fieldindex}/show_cp", false) ? "style='display: none'" : "") ?>>

        <div class="form-group">
            <input type="text"
                   name="contact[fieldsinfo][<?php echo $fieldindex ?>][show_cp_label]"
                   placeholder="Confirm password field label"
                   value="<?php echo wplf_valueof($field_infos, "{$fieldindex}/show_cp_label"); ?>"
                   class="form-control"/>
        </div>
    </div>
</div>