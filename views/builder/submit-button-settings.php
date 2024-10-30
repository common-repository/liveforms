<?php
if(!defined("ABSPATH")) die("Shit happens!");
?>
<div id="button-settings">
    <div class="form-group row">
        <div class="col-md-6">
            <label><?= esc_attr__('Button label', LF_TEXT_DOMAIN); ?>:</label>
            <input type="text" class="form-control" name="contact[buttontext]" placeholder="Submit button text" id="buttontext" data-target="#submit_button_sample" value="<?php echo (isset($form_data['buttontext']) ? $form_data['buttontext'] : "Submit") ?>"/>
        </div>
        <div class="col-md-6">
            <label><?= esc_attr__('Processing label', LF_TEXT_DOMAIN); ?>:</label>
            <input type="text" class="form-control" name="contact[processingtext]" placeholder="Submit button text" id="processingtext" value="<?php echo (isset($form_data['processingtext']) ? $form_data['processingtext'] : "Processing...") ?>"/>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-4">
            <label>Button Color:</label>
            <?php $color_selection = array (
                'primary' => 'Primary',
                'default' => 'Default',
                'danger' => 'Danger',
                'warning'=> 'Warning',
                'info' => 'Info',
                'success' => 'Success',
                'inverse' => 'Inverse'
            ) ?>
            <select id="button-color-selector" class="form-control" name="contact[buttoncolor]">
                <?php foreach($color_selection as $ccolor => $clabel) { ?>
                    <option <?php if ( isset($form_data['buttoncolor']) and $form_data['buttoncolor'] == $ccolor)  echo 'selected="selected"' ?> value="<?php echo $ccolor ?>"><?php echo $clabel ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-4">
            <label>Button Size:</label>
            <?php $btn_size = array (
                '' => 'Default',
                'btn-lg' => 'Large',
            ) ?>
            <select id="button-size-selector" class="form-control" name="contact[buttonsize]">
                <?php foreach($btn_size as $ccolor => $clabel) { ?>
                    <option <?php if ( isset($form_data['buttonsize']) and $form_data['buttonsize'] == $ccolor)  echo 'selected="selected"' ?> value="<?php echo $ccolor ?>"><?php echo $clabel ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-4">
            <label>Button Position:</label>
            <?php
            $btn_pos = array (
                'text-right' => 'Right',
                'text-left' => 'Left',
                'text-center' => 'Center',
                'block' => 'Full Width'
            );
            ?>
            <select id="button-position-selector" class="form-control" name="contact[buttonpos]">
                <?php foreach($btn_pos as $ccolor => $clabel) { ?>
                    <option <?php if ( isset($form_data['buttonpos']) and $form_data['buttonpos'] == $ccolor)  echo 'selected="selected"' ?> value="<?php echo $ccolor ?>"><?php echo $clabel ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <?php do_action("liveforms_form_settings", $form_data); ?>
</div>
