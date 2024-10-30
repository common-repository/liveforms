<?php
/**
 * Base: LiveForms
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 4/8/20 00:51
 */

if (!defined("ABSPATH")) die();
?>
<div class="panel  panel-default">
    <div class="panel-body"><span class="pull-right"><strong class="text-success"><?php echo get_class($this); ?></strong></span>Field Type:
    </div>
    <div class="panel-body" style="background: #fafafa;border-top: 1px solid #ddd"><span class="pull-right ttip" title="<?php esc_attr_e('Click to copy', LF_TEXT_DOMAIN); ?>" onclick="WPLF.copyTxt('<?php echo $fieldindex; ?>')" style="cursor: pointer"><strong class="text-primary"><?php echo $fieldindex; ?></strong></span>Field ID</div>
    <?php if($this->default_options) { ?>
    <div class="panel-heading">Introduction</div>
    <div class="panel-body">
        <div class="form-group">
            <label>Label: </label>             <label class="font-weight-normal pull-right"> <input type="checkbox" class="hide-label"  data-target="#label_<?php echo $fieldindex; ?>" value="1" <?php checked(1, wplf_valueof($field_infos, "{$fieldindex}/hide_label")) ?> name="contact[fieldsinfo][<?php echo $fieldindex ?>][hide_label]"> <?=esc_attr__('Hide label', LF_TEXT_DOMAIN); ?></label>
            <input class="form-control form-field-label" data-target="#label_<?php echo $fieldindex; ?>" type="text"
                   value="<?php echo wplf_valueof($field_infos, "{$fieldindex}/label", get_class($this)); ?>"
                   name="contact[fieldsinfo][<?php echo $fieldindex ?>][label]"/>
        </div>
        <div class="form-group">
            <label>Placeholder Text:</label>
            <input class="form-control form-field-placeholder" data-placeholder="#field_<?php echo $fieldindex; ?>" type="text"
                   value="<?php echo wplf_valueof($field_infos, "{$fieldindex}/placeholder"); ?>"
                   name="contact[fieldsinfo][<?php echo $fieldindex ?>][placeholder]"/>
        </div>
        <div class="form-group">
            <label>Note</label>
            <textarea class="form-control" type="text" value=""
                      name="contact[fieldsinfo][<?php echo $fieldindex ?>][note]"><?php echo wplf_valueof($field_infos, "{$fieldindex}/note"); ?></textarea>
        </div>
        <div class="form-group">

            <label><?=esc_attr__('Note Position', LF_TEXT_DOMAIN); ?>:</label>
            <select class="form-control" name="contact[fieldsinfo][<?php echo $fieldindex ?>][note_pos]">
                <option value="above"><?=esc_attr__('Above the field', LF_TEXT_DOMAIN); ?></option>
                <option value="below" <?php selected(wplf_valueof($field_infos, "{$fieldindex}/note_pos"), 'below') ?> ><?=esc_attr__('Below the field', LF_TEXT_DOMAIN); ?></option>
            </select>
        </div>
        <div class="form-group">
            <label><?=esc_attr__('Default Value', LF_TEXT_DOMAIN); ?></label>
            <input type="text" class="form-control" name="contact[fieldsinfo][<?php echo $fieldindex ?>][default_value]" value="<?php echo wplf_valueof($field_infos, "{$fieldindex}/default_value"); ?>" />
        </div>
        <div class="form-group mb-0">
            <label><?=esc_attr__('Custom CSS Classes', LF_TEXT_DOMAIN); ?></label>
            <input type="text" class="form-control" name="contact[fieldsinfo][<?php echo $fieldindex ?>][form_group_class]" value="<?php echo wplf_valueof($field_infos, "{$fieldindex}/form_group_class"); ?>" />
        </div>

    </div>
    <?php } ?>
</div>
<?php do_action(get_class($this) . "_field_options", $fieldindex, $fieldid, $field_infos); ?>
