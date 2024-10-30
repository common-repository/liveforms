<?php
if(!defined("ABSPATH")) die("Shit happens!");
?>
<div id="ui-settings" class="tab-pane fade">

    <div class="panel panel-lf">
        <div class="panel-heading"><?=esc_attr__('Multi-page form', LF_TEXT_DOMAIN); ?>:</div>
        <div class="panel-body">
            <input type="hidden" name="contact[multipage]" value="0" />
            <label><input type="checkbox" name="contact[multipage]" placeholder="Form width, ex: 400px or 70%" id="multipage" value="1" <?php echo checked(1, wplf_valueof($form_data, 'multipage')); ?> /> <?php _e('Enable multi-page form', LF_TEXT_DOMAIN); ?></label>
            <p class="note"><?php _e('Each section will be treated as a form page when you add multiple section in your form', LF_TEXT_DOMAIN); ?></p>
        </div>
    </div>

    <div class="panel panel-lf">
        <div class="panel-heading"><?=esc_attr__('Form width', LF_TEXT_DOMAIN); ?>:</div>
        <div class="panel-body">
            <input type="text" class="form-control" name="contact[form_wdith]" placeholder="Form width, ex: 400px or 70%" id="form_wdith" value="<?php echo wplf_valueof($form_data, 'form_wdith'); ?>" />
        </div>
    </div>

    <div class="panel panel-lf">
        <div class="panel-heading"><?=esc_attr__('Template', LF_TEXT_DOMAIN); ?>:</div>
        <div class="panel-body">
            <select class="form-control" name="contact[uitemplate]">
                <option value="default"><?=esc_attr__('Select Template', LF_TEXT_DOMAIN); ?></option>
                <?php
                $templates = LiveForms()->form_templates();
                foreach ($templates as $id => $template){
                    ?>
                    <option value="<?= $id; ?>" <?php selected( $id, wplf_valueof($form_data, 'uitemplate')); ?> ><?= $template['name']; ?></option>
                <?php
                }
                ?>
            </select>
        </div>
    </div>
    <div class="panel panel-lf">
        <div class="panel-heading"><?=esc_attr__('Custom Class and ID', LF_TEXT_DOMAIN); ?>:</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <label><?=esc_attr__('Form ID'); ?></label>
                    <div class="input-group">
                        <div class="input-group-addon">#</div>
                        <input type="text" name="contact[formid]" class="form-control" value="<?=wplf_valueof($form_data, 'formid', ['default' => 'form-'.get_the_ID()]); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <label><?=esc_attr__('Form Class'); ?></label>
                    <div class="input-group">
                        <div class="input-group-addon">.</div>
                        <input type="text" name="contact[formclass]" class="form-control" value="<?=wplf_valueof($form_data, 'formclass', ['default' => 'form-'.get_the_ID()]); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-lf">
        <div class="panel-heading"><?=esc_attr__('Custom CSS', LF_TEXT_DOMAIN); ?>:</div>
        <div class="panel-body p-0">
            <textarea id="custom_css" class="noqt" name="contact[custom_css]"  aria-describedby="editor-keyboard-trap-help-1 editor-keyboard-trap-help-2 editor-keyboard-trap-help-3 editor-keyboard-trap-help-4"><?php echo wplf_valueof($form_data, 'custom_css'); ?></textarea>
        </div>
    </div>
    <div class="panel panel-lf">
        <div class="panel-heading"><?=esc_attr__('Custom JS', LF_TEXT_DOMAIN); ?>:</div>
        <div class="panel-body p-0">
            <textarea id="custom_js" class="noqt" name="contact[custom_js]"><?php echo wplf_valueof($form_data, 'custom_js'); ?></textarea>
        </div>
    </div>


</div>
