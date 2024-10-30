<?php
if(!defined("ABSPATH")) die("!");
?><div class="pull-left" id="allformfields">
    <div class="panel panel-default">
        <div class="panel-heading">
            <ul id="form-options-tabs" class="nav nav-tabs  nav-justified panel-heading-tabs">
                <li role="presentation" class="active"><a href="#tab-fields" role="tab" data-toggle="tab"><?php _e('Fields', 'liveforms'); ?></a></li>
                <li role="presentation"><a id="field-settings-tab" href="#tab-field-settings" role="tab" data-toggle="tab"><?php _e('Field Settings', 'liveforms'); ?></a></li>
            </ul>
        </div>
        <div class="panel-body">
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="tab-fields">

                    <div class="panel-group" id="accordion">
                        <div class="panel panel-default">
                            <div class="panel-heading"><a class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#commonfields">Commonly Used Fields</a></div>
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
                            <div class="panel-heading"><a class="panel-title" class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#genericfields">Generic Fields</a></div>
                            <div id="genericfields" class="panel-collapse collapse">
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
                            <div class="panel-heading"><a class="panel-title" class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#advancedfields">Advanced Fields</a></div>
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
                    </div>

                </div>
                <div role="tabpanel" class="tab-pane" id="tab-field-settings">
                    <?php
                    if (isset($checked_fields)) {
                        foreach ($checked_fields as $fieldindex => $fieldid) {
                            if($fieldid && class_exists($fieldid)) {
                                $tmp_obj = new $fieldid();
                                echo $tmp_obj->field_settings($fieldindex, $fieldid, $field_infos);
                            }
                        }
                    } ?>
                </div>
            </div>
        </div>
    </div>

</div>