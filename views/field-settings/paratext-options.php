<?php
/**
 * Base: LiveForms
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 5/8/20 14:06
 */
if(!defined("ABSPATH")) die();
?>
<div class="panel panel-default">
    <div  class="panel-heading"><?= __('Paragraph Text', LF_TEXT_DOMAIN); ?></div>
    <div class="panel-body">
        <textarea id="cont_<?php echo $fieldindex ?>" placeholder="This will be shown in the form" class="form-control" type="text" name="contact[fieldsinfo][<?php echo $fieldindex ?>][paragraph_text_value]"><?php echo isset($field_infos[$fieldindex]['paragraph_text_value']) ? $field_infos[$fieldindex]['paragraph_text_value'] : '' ?></textarea>
    </div>
</div>
