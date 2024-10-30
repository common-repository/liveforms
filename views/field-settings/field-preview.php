<?php
/**
 * Base: LiveForms
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 4/8/20 12:52
 */
if(!defined("ABSPATH")) die();
?>
<li class="list-group-item cog-trigger" data-field="#field_<?php echo $fieldindex; ?>" data-fieldsettings="#cog_<?php echo $fieldindex; ?>" data-type="<?php echo get_class($this) ?>" id="field_<?php echo $fieldindex; ?>">
    <input type="hidden" name="contact[fields][<?php echo $fieldindex ?>]" value="<?php echo $fieldid; ?>">
    <span id="label_<?php echo $fieldindex; ?>" class="<?php echo wplf_valueof($field_infos, "hide_label", ['validate' => 'int']) === 1 ? 'hide' : ''; ?>"><?php echo wplf_valueof($field_infos, "label", get_class($this)); ?></span>

    <div class="field-controller">
        <div  class="control-buttons">
            <button type="button" class="btn btn-info"><i class="fa fa-cog"></i></button>
            <button type="button" class="btn btn-primary clone-field"  data-type="<?php echo get_class($this) ?>" data-fieldindex="<?php echo $fieldindex; ?>"><i class="fa fa-copy"></i></button>
            <button type="button" rel="field_<?php echo $fieldindex; ?>" class="btn btn-danger remove"><i class="fas fa-times"></i></button>
        </div>
    </div>

    <div class="field-preview" id="Field_Preview_<?php echo $fieldindex; ?>">
        <?php echo $preview; ?>
    </div>
    <?php if(wplf_valueof($field_infos, "row_id")) { ?>
    <input type="hidden" name="contact[form_layout][<?php echo wplf_valueof($field_infos, "row_id"); ?>][<?= wplf_valueof($field_infos, "column_id") ?>][<?= $fieldindex ?>]" value="<?php echo $fieldid; ?>">
    <?php } ?>
</li>

