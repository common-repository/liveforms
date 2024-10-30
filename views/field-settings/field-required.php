<?php
/**
 * Base: LiveForms
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 4/8/20 00:49
 */
if (!defined("ABSPATH")) die();

if( $this->required_option ) {
?>
<div class="panel panel-default">
    <label class="panel-heading d-block">
        <input rel="req-params" class="req" type="checkbox"
                                                name="contact[fieldsinfo][<?php echo $fieldindex ?>][required]"
                                                value="1" <?php checked(wplf_valueof($field_infos,"{$fieldindex}/required", false), true); ?> />
        <span class="checkx"><i class="fas fa-check-double"></i></span>
        Required
    </label>
    <div class="panel-body req-params"  <?php echo(!wplf_valueof($field_infos,"{$fieldindex}/required", false) ? "style='display: none'" : "") ?>>

            <div class="form-group">
                <input type="text"
                       name="contact[fieldsinfo][<?php echo $fieldindex ?>][reqmsg]"
                       placeholder="Field Required Message"
                       value="<?php echo wplf_valueof($field_infos, "{$fieldindex}/reqmsg"); ?>"
                       class="form-control"/>
            </div>
        <div class="form-group">
            <label>Validation:</label>
            <select name="contact[fieldsinfo][<?php echo $fieldindex ?>][validation]" class="form-control field-validate" data-target="<?php echo $fieldindex ?>">
                <?php
                $validation_ops = get_validation_ops();
                foreach ($validation_ops as $value => $validation) {
                    if(count(array_intersect($validation['fields'], ['*', $this->field_id()])) > 0)
                        echo '<option value="' . $value . '" ' . (wplf_valueof($field_infos, "{$fieldindex}/validation") === $value ? 'selected="selected "' : "") . '>' . $validation[$value] . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="form-group valid-data-src" id="remote_validation_<?php echo $fieldindex ?>" style="<?= wplf_valueof($field_infos, "{$fieldindex}/validation") !== 'remote' ? 'display: none' : '' ?>">
            <label><?= __('API URL', 'liveforms') ?>:</label>
            <input type="url" name="contact[fieldsinfo][<?php echo $fieldindex ?>][datasource]" value="<?= wplf_valueof($field_infos, "{$fieldindex}/datasource") ?>" class="form-control">
            <em><?= __("Input value will be sent to api URL for validation, the return should be in json format {success: true}, or {success: false, message: 'Error message'}") ?></em>
        </div>
        <div class="form-group valid-data-src" id="predef_validation_<?php echo $fieldindex ?>" style="<?= wplf_valueof($field_infos, "{$fieldindex}/validation") !== 'predef' ? 'display: none' : '' ?>">
            <label><?= __('Valid Dataset', 'liveforms') ?>:</label>
            <textarea type="url" name="contact[fieldsinfo][<?php echo $fieldindex ?>][dataset]" class="form-control" placeholder="entry 1\nentry 2\n..."><?= wplf_valueof($field_infos, "{$fieldindex}/dataset") ?></textarea>
            <em><?= __("1 entry per line") ?></em>
        </div>



    </div>
</div>
<?php
}
