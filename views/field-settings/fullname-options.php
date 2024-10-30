<?php
if(!defined("ABSPATH")) die("Shit happens!");
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <?= esc_attr__( 'Title', LF_TEXT_DOMAIN ); ?>:
    </div>
    <div class="panel-body">
        <textarea class="form-control" name="contact[fieldsinfo][<?php echo $fieldindex ?>][person_title]"><?php echo (isset($field_infos[$fieldindex]['person_title']) ? $field_infos[$fieldindex]['person_title'] : '') ?></textarea>
        <em class="note"><?= esc_attr__( 'Ex: Mr., Mrs., Dr., and add one title per line', LF_TEXT_DOMAIN ); ?></em>
    </div>
</div>

