<?php
/**
 * Base: LiveForms
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 5/8/20 03:49
 */
if(!defined("ABSPATH")) die();
?>
<div class="panel panel-default" style="overflow: visible">
    <div class="panel-heading">
        Field Value
    </div>
    <div class="panel-body" style="overflow: visible">
        <div class="btn-group dropup" style="display: flex">
            <input style="margin-right: 10px" type="text" class="form-control" id="<?php echo $fieldindex ?>_value" name="contact[fieldsinfo][<?php echo $fieldindex ?>][value]"  placeholder="<?php _e('Hidden Field Value', 'liveforms'); ?>" value="<?php echo wplf_valueof($field_infos, "{$fieldindex}/value"); ?>" />
            <button type="button" class="btn btn-info dropdown-toggle" style="border-radius: 3px" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
            <ul class="dropdown-menu field_value" style="right: 0">
                <li><a href="#" data-target="#<?php echo $fieldindex ?>_value" data-value="{{ID}}">Post ID</a></li>
                <li><a href="#" data-target="#<?php echo $fieldindex ?>_value" data-value="{{title}}">Post Title</a></li>
                <li><a href="#" data-target="#<?php echo $fieldindex ?>_value" data-value="{{cf_***}}">Custom Field</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="#" data-target="#<?php echo $fieldindex ?>_value" data-value="{{req_***}}">URL Parameter (GET)</a></li>
                <li><a href="#" data-target="#<?php echo $fieldindex ?>_value" data-value="{{CIENT_IP}}">Client IP</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="#" data-target="#<?php echo $fieldindex ?>_value" data-value="{{DATE}}">Date</a></li>
                <li><a href="#" data-target="#<?php echo $fieldindex ?>_value" data-value="{{TIMESTAMP}}">Time Stamp</a></li>
            </ul>
        </div>
        <em><?php _e('When you see ***, replace it with proper variable name', 'liveforms'); ?></em>
    </div>
</div>
