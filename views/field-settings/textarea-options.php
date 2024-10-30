<?php
if(!defined("ABSPATH")) die("Shit happens!");
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <?= __('Textarea Settings', LF_TEXT_DOMAIN); ?>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <label><?= __('Min Height', LF_TEXT_DOMAIN); ?>:</label>
                <div class="input-group">
                    <input type="number" class="form-control" name="contact[fieldsinfo][<?php echo $fieldindex ?>][minheight]" placeholder="100" value="<?php echo(isset($field_infos[$fieldindex]['minheight']) ? $field_infos[$fieldindex]['minheight'] : '') ?>"/>
                    <div class="input-group-addon">px</div>
                </div>
            </div>
            <div class="col-md-6">
                <label><?= __('Max Height', LF_TEXT_DOMAIN); ?>:</label>
                <div class="input-group">
                    <input type="number" class="form-control" name="contact[fieldsinfo][<?php echo $fieldindex ?>][maxheight]" placeholder="300" value="<?php echo(isset($field_infos[$fieldindex]['maxheight']) ? $field_infos[$fieldindex]['maxheight'] : '') ?>"/>
                    <div class="input-group-addon">px</div>
                </div>
            </div>
        </div>
    </div>
</div>

