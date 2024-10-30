<?php

if(!defined("ABSPATH")) die("Shit happens!");
$field_params = $params;
$field_params['attributes'] = [ 'id' => 'field_'.$params['id'] ];
?>
<div id="<?php echo $params['id'] ?>" class='form-group <?= wplf_valueof($params, 'form_group_class') ?> <?php if (isset($params['conditioned'])) echo " conditioned hide "?>' data-cond-fields="<?php echo $condition_fields ?>" data-cond-action="<?php echo $cond_action.':'.$cond_boolean ?>" >
    <label for='field' style='display: <?php echo  (wplf_valueof($params, 'hide_label', ['validate' => 'int']) === 1) ? "none" : "block" ?>;clear: both'>
        <?php echo $params['label'] ?> <?php if(wplf_valueof($params, 'required', ['validate' => 'int']) === 1) echo " <span title='".esc_attr__( 'Required Field', LF_TEXT_DOMAIN )."' style='color: var(--color-danger);'>*</span>"; ?>
        <?php if(wplf_valueof($params, 'note_pos') === 'above'){ ?>
            <span class="note"><?php echo $params['note']; ?></span>
        <?php } ?>
    </label>
    <?php
        //\LiveForms\__\__::p($field_params);
        $field_html = $this->field_html($field_params);
        echo apply_filters("wplf_field_html", $field_html, $this->field_id(), $params);

    ?>
    <?php if(wplf_valueof($params, 'note_pos') === 'below'){ ?>
        <span class="note"><?php echo $params['note']; ?></span>
    <?php } ?>
</div>
