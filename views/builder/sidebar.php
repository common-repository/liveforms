<?php
if(!defined("ABSPATH")) die("!");
?><div class="pull-left" id="allformfields">

    <ul id="form-options-tabs" class="nav nav-pills nav-justified">
        <li role="presentation" class="active"><a href="#tab-fields" role="tab" data-toggle="tab"><?php _e('Fields', 'liveforms'); ?></a></li>
        <li role="presentation"><a id="field-settings-tab" href="#tab-field-settings" role="tab" data-toggle="tab"><?php _e('Field Settings', 'liveforms'); ?></a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="tab-fields">

            <div class="panel-group" id="accordion">
                <div class="panel panel-default">
                    <div class="panel-heading"><a class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#commonfields"><?= esc_attr__( 'Commonly Used Fields', LF_TEXT_DOMAIN ); ?></a></div>
                    <div id="commonfields" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <ul id="availablecfields" class="availablefields list-group">
                                <!-- Populating Common Fields list -->
                                <?php foreach ($fields as $fieldclass): ?>
                                    <?php $tmp_obj = new $fieldclass(); echo $tmp_obj->control_button();?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading"><a class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#genericfields"><?= esc_attr__( 'Generic Fields', LF_TEXT_DOMAIN ); ?></a></div>
                    <div id="genericfields" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <ul id="availablegfields" class="availablefields list-group">
                                <!-- Populating Generic Fields list -->
                                <?php foreach ($generic_fields as $fieldclass): ?>
                                    <?php $tmp_obj = new $fieldclass(); echo $tmp_obj->control_button();?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Advanced fields start -->
                <div class="panel panel-default">
                    <div class="panel-heading"><a class="panel-title collapsed" data-toggle="collapse" data-parent="#accordion" href="#advancedfields"><?= esc_attr__( 'Advanced Fields', LF_TEXT_DOMAIN ); ?></a></div>
                    <div id="advancedfields" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul id="availableafields" class="<?php if(is_pro()) echo 'availablefields'; else echo  'pro-only' ?>  list-group">
                                <!-- Populating Advanced Fields list -->
                                <?php foreach ($advanced_fields as $fieldclass): ?>
                                    <?php $tmp_obj = new $fieldclass(); echo $tmp_obj->control_button();?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Advanced fields end -->

                <!-- Custom fields start -->
                <?php if(count($custom_fields) > 0) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading"><a class="panel-title collapsed" data-toggle="collapse" data-parent="#accordion" href="#customfields"><?= esc_attr__( 'Custom Fields', LF_TEXT_DOMAIN ); ?></a></div>
                    <div id="customfields" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul id="availableafields" class="availablefields  list-group">
                                <!-- Populating Advanced Fields list -->
                                <?php foreach ($custom_fields as $fieldclass): ?>
                                    <?php $tmp_obj = new $fieldclass(); echo $tmp_obj->control_button();?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <!-- Custom fields end -->

            </div>

        </div>
        <div role="tabpanel" class="tab-pane" id="tab-field-settings">
            <?php
            if (is_array($form_layout)) {
                foreach ($form_layout as $row_id => $form_row) {
                    $row_setting = wplf_valueof($row_settings, $row_id);
                    ?>
            <div id="row_settings_<?= $row_id ?>" class="cog">
                <div class="panel  panel-default">
                    <div class="panel-body">
                        <strong><?= esc_attr__( 'Section/Page Settings', LF_TEXT_DOMAIN ); ?></strong>
                    </div>
                    <div class="panel-heading" style="background: #fafafa;border-top: 1px solid #eee;border-bottom: 1px solid #eee;"><span class="pull-right">
                            <strong class="text-primary" id="__row_id">#<?= $row_id ?></strong></span><?= esc_attr__( 'ID', LF_TEXT_DOMAIN ); ?></div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label><?= esc_attr__('Section Name', LF_TEXT_DOMAIN); ?></label>
                            <input class="form-control form-field-label" data-target="" type="text" value="<?= wplf_valueof($row_setting, 'label') ?>" name="contact[row_settings][<?= $row_id ?>][label]">
                        </div>
                    </div>
                </div>
            </div>
            <?php } } ?>
            <?php
            if (is_array($form_layout)) {
                foreach ($form_layout as $row_id => $form_row) {
                    foreach ($form_row as $column_id => $column) {
                        foreach ($column as $fieldindex => $fieldid) {
                            if ($fieldid && class_exists($fieldid)) {
                                $tmp_obj = new $fieldid();
                                echo $tmp_obj->field_settings($fieldindex, $fieldid, $field_infos);
                            }
                        }
                    }
                }
            }
            ?>
        </div>
    </div>

</div>
