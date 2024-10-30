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
    <div class="panel-heading">
        File Upload Settings
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <label>Max Filesize:</label>
                <div class="input-group"><input type="number" class="form-control" name="contact[fieldsinfo][<?php echo $fieldindex ?>][filesize]" placeholder="File size in MB" value="<?php echo (isset($field_infos[$fieldindex]['filesize']) ? $field_infos[$fieldindex]['filesize'] : '') ?>"/><span class="input-group-addon">MB</span></div>
            </div>
            <div class="col-md-6"><label>Allowed Extensions:</label>
                <input type="text" class="form-control" name="contact[fieldsinfo][<?php echo $fieldindex ?>][extensions]" placeholder="ex: jpg,png,gif" value="<?php echo (isset($field_infos[$fieldindex]['extensions']) ? $field_infos[$fieldindex]['extensions'] : '') ?>"/></div>
        </div>
    </div>
</div>
