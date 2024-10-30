<?php
/**
 * Base: LiveForms
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 4/8/20 00:39
 */
if(!defined("ABSPATH")) die();
//echo"<pre>".print_r($field_infos[$fieldindex], 1)."</pre>";
//lfprecho($field_infos[$fieldindex]);
$cond_list = wplf_valueof($field_infos, "{$fieldindex}/condition/value",  [ 'default'  => [''  => ''] ]);
if(!$cond_list) $cond_list = [''  => ''];
if($this->conditional_logic) {
?>
<?php if(is_pro()){  ?>
<div class='form-group'>
    <div class="panel panel-default">
        <label class="panel-heading d-block">
            <input rel='condition-params' class='cond' type='checkbox' name='contact[fieldsinfo][<?php echo $fieldindex ?>][conditioned]' value='1' <?php if (isset($field_infos[$fieldindex]['conditioned'])) echo 'checked="checked"'; ?>/>
            <span class="checkx"><i class="fas fa-check-double"></i></span>
            Conditional logic
        </label>
        <div id="cond_<?php echo $fieldindex ?>" class='cond-params' style='display:none'>
            <div class="panel-body">
                <div class="row row-bottom-buffer met-cond">
                    <div class="col-md-12">
                        <select class="select" name="contact[fieldsinfo][<?php echo $fieldindex ?>][condition][action]">
                            <option <?php if (wplf_valueof($field_infos,  "{$fieldindex}/condition/action") === 'show') echo 'selected="selected"' ?> value="show">Show</option>
                            <option <?php if (wplf_valueof($field_infos,  "{$fieldindex}/condition/action") === 'hide') echo 'selected="selected"' ?> value="hide">Hide</option>
                        </select>
                        this field if
                        <select class="select" name="contact[fieldsinfo][<?php echo $fieldindex ?>][condition][boolean_op]">
                            <option <?php if (wplf_valueof($field_infos,  "{$fieldindex}/condition/boolean_op") === 'all') echo 'selected="selected"' ?> value="all">All</option>
                            <option <?php if (wplf_valueof($field_infos,  "{$fieldindex}/condition/boolean_op") === 'any') echo 'selected="selected"' ?> value="any">Any</option>
                        </select>
                        of these conditions are met
                    </div>
                </div>
                <div id="conditions-<?php echo $fieldindex ?>" class="conditional-logics">
                <?php

                foreach($cond_list as $key => $value) {
                    $data_selection_value = wplf_valueof($field_infos, "{$fieldindex}/condition/value/{$key}");
                    if(wplf_valueof($field_infos, "{$fieldindex}/condition/field/{$key}") !== ''){
                    ?>
                    <div class='row row-bottom-buffer cond-row' rel="row">
                        <div class='col-md-4'>
                            <select class='form-control cond-field-selector' data-fieldindex="<?php echo $fieldindex ?>"  data-target="#cond_value_<?php echo $fieldindex ?>_<?= $key ?>" data-selection='<?php echo wplf_valueof($field_infos, "{$fieldindex}/condition/field/{$key}");  ?>' name='contact[fieldsinfo][<?php echo $fieldindex ?>][condition][field][]'>
                                <option value="">Select a field</option>
                            </select>
                        </div>
                        <div class='col-md-3'>
                            <select class='form-control cond-operator' data-selection='<?php echo wplf_valueof($field_infos, "{$fieldindex}/condition/op/{$key}") ?>' name='contact[fieldsinfo][<?php echo $fieldindex ?>][condition][op][]'>
                                <option value='is'>Is</option>
                                <option value='is-not'>Is not</option>
                                <option value='less-than'>Less than</option>
                                <option value='greater-than'>Greater than</option>
                                <option value='contains'>Contains</option>
                                <option value='starts-with'>Starts with</option>
                                <option value='ends-with'>Ends with</option>
                            </select>
                        </div>
                        <div class='col-md-4'>
                            <div id="cond_value_<?php echo $fieldindex ?>_<?= $key ?>" data-cond_value="<?php echo $data_selection_value;  ?>">
                                <input type='text' value='<?php echo $data_selection_value; ?>' class="is-cond-data form-control" name='contact[fieldsinfo][<?php echo $fieldindex ?>][condition][value][]'/>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <a href="#" class="btn btn-link del-cond-option"
                               rel="<?php echo $fieldindex ?>"><i
                                    class="fas fa-times text-danger"></i></a>

                        </div>
                    </div>
                <?php }
                }
                ?>
                </div>
            </div>
            <div class="panel-footer">
                <a href="#" class="add-cond-option btn btn-sm btn-info" rel="<?php echo $fieldindex ?>">
                    <i class="fas fa-plus-circle"></i> Add New Condition
                </a>
            </div>
        </div>
    </div>
</div>
<?php } else { ?>
    <div class="panel panel-default">
        <div class="panel-body" data-target="#pronotice" data-toggle="modal">
            <label style="margin: 0"><input type="checkbox" disabled="disabled"> Conditional logic</label><br/>
        </div>
    </div>
<?php }
}
