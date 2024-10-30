<?php
class PaymentMethods extends Field {

    public function field_id(){
        return get_class($this);
    }

	public function control_button() {
		ob_start();
		?>
		<li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="PaymentMethods">
			<span class="lfi lfi-name"><i class="fa fa-credit-card"></i></span> Payment
			<a title="Payment Methods" rel="PaymentMethods" class="add pull-right add-form-field" data-template='PaymentMethods' href="#"><i class="fas fa-plus-circle ttipf" title=""></i></a>
	    </li>
	    <?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}

	function paymentmethods_field_settings($fieldindex, $fieldid, $field_infos){
        include LF_BASE_DIR.'views/field-settings/payment-options.php';
    }

	function configForm($options, $fieldprefix){
	    if(is_array($options)){
            return wplf_option_page($options, $fieldprefix);
        } else
            return $options;
    }

	public function field_preview_html($fieldindex, $fieldid, $field_infos) {
		ob_start();
		?>
		<div  class='form-group' >
			<div class="panel panel-default" style="margin-top: 10px">
			<div class="panel-heading"><span class="pull-right"><?php echo (isset($params['amount']) ? $params['amount'] : '0.00')." ". ( isset($params['currency']) ? $params['currency'] : 'USD'); ?></span> Amount:</div>
                <div class="panel-heading bg-white">
                    Select Payment Method:
                </div>
			<div class="panel-body">
				<input type="radio" checked='checked' value=''> &nbsp;<?php _e('Skip Payment', 'liveforms'); ?>

			</div>
			</div>
		</div>
		<?php
        $preview = ob_get_clean();
        ob_start();
        include LF_BASE_DIR.'views/field-settings/field-preview.php';
        $field_render_html = ob_get_clean();
        return $field_render_html;
	}

    public function field_html($params = array()) {
        ob_start();
        $currency = isset($params['currency']) ? $params['currency'] : 'USD';
        $selected_methods = isset( $params['payment'] ) ? $params['payment'] : array() ;  // get the list of selected types of methods
        if(count($selected_methods) > 0 || 1){

            ?>
                <div style="margin: 0;" id="<?php echo $params['id'] ?>" class='panel panel-default panel-payment-methods'>
                    <div class="panel-heading" style="font-weight: 900;">
                        <?php if((double)$params['amount'] > 0 && !(int)$params['donation_field']){ ?>
                            <span class="pull-right" style="font-weight: 900"><?php echo ( isset($params['amount']) ? number_format($params['amount'], 2) : '0.00' ) ." ". ( isset($params['currency']) ? $params['currency'] : 'USD' ); ?></span>
                        <?php } else { ?>
                            <span class="pull-right" style="font-weight: 900;margin-top: -3px">
                                <div class="input-group input-group-sm" style="width: 160px">
                                    <input type="number" required="required" min="1" value="<?= (double)$params['amount'] ?>" class="form-control" name="__amount" />
                                    <span class="input-group-addon"><?php echo isset($params['currency'])?$params['currency']:'USD'; ?></span>
                                </div>
                            </span>
                        <?php } ?>

                        Amount:
                    </div>
                    <div class="panel-heading" style="background: white">
                        Select Payment Method:
                    </div>
                    <div class="panel-body">
                        <?php if(!isset($params['required'])) { ?>
                            <label><input name=submitform[<?php echo $params['id']; ?>] type="radio" checked='checked' value=''> <?php _e('Skip Payment', 'liveforms'); ?></label> &nbsp;
                            <?php
                        }
                        $methods = \LiveForms\Payment::getMethods();
                        $pi = 0;
                        foreach ( $selected_methods as $method_name ) {
                            if ( isset( $methods[$method_name] ) ) {  ?>
                                <label><input name=submitform[<?php echo $params['id']; ?>] <?php if(isset($params['required']) && $pi === 0) echo "checked=checked"; ?> type="radio" value='<?php echo $method_name; ?>'> <?php echo $methods[$method_name]->label ?></label> &nbsp;
                                <?php
                                $pi++; }
                        }
                        ?>

                    </div>
                    <?php if((int)$params['donation_field'] === 1){ ?>
                        <div class="panel-footer">
                <span class="pull-right">
                    Pledged: <strong><?php echo number_format($pledged = (double)$params['pledge_amount'], 2); ?> <?= $currency ?></strong>
                </span>
                            Received: <strong><?php echo number_format($collected = \LiveForms\Payment::total($params['form_id']), 2); ?> <?= $currency ?></strong>
                            <div class="progress" data-toggle="tooltip" style="margin: 5px 0;background: #dddddd;height: 16px" title="<?php echo (int)(($collected/$pledged)*100); ?>% Collected">
                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo (int)(($collected/$pledged)*100); ?>" aria-valuemin="0" aria-valuemax="100" style="height: 16px;background: var(--color-success);width: <?php echo (int)(($collected/$pledged)*100); ?>%">
                                    <span class="sr-only"><?php echo (int)(($collected/$pledged)*100); ?>% Collected</span>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>


            <?php
        }
        $field_html = ob_get_clean();
        return $field_html;
    }

}

add_action("PaymentMethods_field_options", [new PaymentMethods(),  'paymentmethods_field_settings'], 10, 3);
