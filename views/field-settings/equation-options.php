<?php
/**
 * Base: LiveForms
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 5/8/20 14:06
 */
if(!defined("ABSPATH")) die();
?>
<div class="panel panel-default" style="overflow: visible !important;">
    <div  class="panel-heading"><?= __('Equation', LF_TEXT_DOMAIN); ?></div>
    <div class="panel-body">
        <div class="dropdown" style="display: inline-block">
            <button class="btn btn-info btn-sm dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                Insert Field
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu eq-num-fields" data-target="#eqf_<?php echo $fieldindex; ?>">
            </ul>
        </div>
        <div class="dropdown" style="display: inline-block">
            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                Operator
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a class="eq_field" href="#" data-value="+" data-target="#eqf_<?php echo $fieldindex; ?>">Plus</a></li>
                <li><a class="eq_field" href="#" data-value="-" data-target="#eqf_<?php echo $fieldindex; ?>">Minus</a></li>
                <li><a class="eq_field" href="#" data-value="/" data-target="#eqf_<?php echo $fieldindex; ?>">Division</a></li>
                <li><a class="eq_field" href="#" data-value="*" data-target="#eqf_<?php echo $fieldindex; ?>">Multiply</a></li>
            </ul>
        </div>

        <input style="margin-top: 10px" type="text" id="eqf_<?php echo $fieldindex; ?>" onkeyup="(jQuery(this).val() !== '' ? jQuery('#Field_Preview_<?php echo $fieldindex; ?> .eqpreview').html(jQuery(this).val()) : '[WRITE YOUR EQUATION]')" placeholder="<?php esc_attr_e('Build your equation', LF_TEXT_DOMAIN); ?>" class="form-control"  name="contact[fieldsinfo][<?php echo $fieldindex ?>][equation]" value="<?php echo wplf_valueof($field_infos, "{$fieldindex}/equation"); ?>" />
        <em>
            You also can use complex math expressions like:
            <code>{{field_id}+{{field_id}*sqrt({{field_id})</code> or conversion expression like: <code>{{field_id}} cm in feet</code>
        </em>
    </div>
</div>
