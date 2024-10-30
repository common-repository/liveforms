<?php
/**
 * Base: LiveForms
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 5/8/20 14:06
 */
if (!defined("ABSPATH")) die();
?>
<div class="panel panel-default">
    <div class="panel-heading"><?= __('Payment Options', LF_TEXT_DOMAIN); ?></div>
    <div class="panel-body">
        <div class='form-group'>
            <div class='row'>
                <div class='col-md-4'>
                    <label>Amount</label>
                    <input type="text" class="form-control" value="<?php echo wplf_valueof($field_infos, "{$fieldindex}/amount"); ?>"
                           placeholder="0.00"
                           name="contact[fieldsinfo][<?php echo $fieldindex ?>][amount]"/>
                </div>
                <div class='col-md-8'>
                    <label>Currency</label>
                    <?php $current_selection = wplf_valueof($field_infos, "{$fieldindex}/currency"); ?>
                    <select class='form-control' name="contact[fieldsinfo][<?php echo $fieldindex ?>][currency]">
                        <option value="none" <?php if ($current_selection == 'none') echo 'selected="selected"' ?>>
                            Select a Currency
                        </option>
                        <?php foreach (currencies() as $value => $currency) { ?>
                            <option <?php if ($current_selection == $value) echo 'selected="selected"' ?>
                                    value="<?php echo $value ?>"><?php echo $currency ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="checkboxes">

                <input type="hidden"
                       name="contact[fieldsinfo][<?php echo $fieldindex ?>][payment][]"
                       value="none"
                />
                <?php
                $methods_set = \LiveForms\Payment::getMethods();
                foreach ($methods_set as $PaymentMethodID => $PaymentMethod) {
                    $pmlist = wplf_valueof($field_infos, "{$fieldindex}/payment");
                    if(!is_array($pmlist)) $pmlist = [];
                    ?>

                    <div class="panel panel-default panel-pms mb-0">
                        <div class="panel-heading" style="margin-bottom: -1px">
                            <label><input type="checkbox"
                                          name="contact[fieldsinfo][<?php echo $fieldindex ?>][payment][]"
                                          value="<?php echo $PaymentMethodID ?>" <?php if (in_array($PaymentMethodID, wplf_valueof($field_infos, "{$fieldindex}/payment", [ 'default' => [] ]))) echo 'checked="checked"'; ?>
                                          class="payment-method-select"
                                          data-config-panel="payment-<?php echo $fieldindex . '-' . $PaymentMethodID; ?>" <?php if (!is_object($PaymentMethod)) echo 'disabled="disabled"'; ?>/>
                                <span class="checkx"><i class="fas fa-check-double"></i></span>
                                &nbsp;<?php echo $PaymentMethod->label; ?>
                            </label></div>
                        <?php
                        if (is_object($PaymentMethod)): ?>
                            <div id='configs-payment-<?php echo $fieldindex . '-' . $PaymentMethodID ?>'
                                 class='panel-body <?php if (!in_array($PaymentMethodID, $pmlist)) echo 'hidden'; ?>'>

                                <?php
                                $fieldprefix = "contact[fieldsinfo][{$fieldindex}][paymethods][$PaymentMethodID]";
                                $cache = wplf_valueof($field_infos, "{$fieldindex}/paymethods/{$PaymentMethodID}");
                                echo $this->configForm($PaymentMethod->configOptions($cache), $fieldprefix); ?>
                            </div>
                        <?php endif ?>
                    </div>
                    <?php
                }
                ?>

            </div>
        </div>
    </div>
    <label class="panel-heading d-block">
        <input type="hidden" name="contact[fieldsinfo][<?php echo $fieldindex ?>][donation_field]" value="0">
        <input class="show-on-check" data-target="#donations-<?php echo $fieldindex ?>" type="checkbox" name="contact[fieldsinfo][<?php echo $fieldindex ?>][donation_field]"
                  value="1" <?php checked(wplf_valueof($field_infos, "{$fieldindex}/donation_field"), 1); ?>><span class="checkx"><i class="fas fa-check-double"></i></span> Donation / Fund Raising
    </label>
    <div id="donations-<?php echo $fieldindex ?>" class="panel-body" <?php if(!(int)wplf_valueof($field_infos, "{$fieldindex}/donation_field")) echo 'style="display: none"'; ?> >
        <label>Pledge Amount:</label>
        <input type="number" class="form-control"
               name="contact[fieldsinfo][<?php echo $fieldindex ?>][pledge_amount]"
               value="<?php echo wplf_valueof($field_infos, "{$fieldindex}/pledge_amount"); ?>"/>
    </div>
</div>
