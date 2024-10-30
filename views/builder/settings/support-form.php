<?php if (!defined('ABSPATH')) die('!');
$agents = get_users(array('role' => 'agent'));
?>
<div id="support-form" class="tab-pane fade">
    <div class="panel panel-lf">
        <div class="panel-heading"><?= esc_attr__('User Communication', LF_TEXT_DOMAIN); ?></div>
        <div class="panel-body">
            <div class="form-group">
                <label>
                    <input type="hidden" name="contact[support_token]" value="0">
                    <input type="checkbox" name="contact[support_token]"
                           value="1" <?php checked(wplf_valueof($form_data, 'support_token'), 1); ?> > <?= esc_attr__('Send a unique query identifier', LF_TEXT_DOMAIN); ?>
                </label>
                <p style="font-size: 11px;color: #888;font-style: italic;margin-bottom: 15px;display: block;letter-spacing: 0.6px">
                    <?= esc_attr__('If you check the following option, form submitter will received a token number to get access to the replies for their query.', LF_TEXT_DOMAIN); ?>
                </p>
            </div>
            <div class="form-group">
                <label>
                    <input type="hidden" name="contact[no_receipt]" value="0">
                    <input type="checkbox" name="contact[no_receipt]"
                           value="1" <?php checked(wplf_valueof($form_data, 'no_receipt'), 1); ?> > <?= esc_attr__('Do Not Send Receipt', LF_TEXT_DOMAIN); ?>
                </label>
                <p style="font-size: 11px;color: #888;font-style: italic;margin-bottom: 15px;display: block;letter-spacing: 0.6px">
                    <?= esc_attr__('Do not send receiving confirmation email to user.', LF_TEXT_DOMAIN); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="panel panel-lf">
        <div class="panel-heading"><?= esc_attr__('Assign Form Handler', LF_TEXT_DOMAIN); ?>:</div>
        <div class="panel-body">
            <select name='contact[agent]' class='form-control input-lg'>
                <option value=''>Myself</option>
                <?php foreach ($agents as $agent) { ?>
                    <?php $agent_data = $agent->data; ?>
                    <option <?php echo($agent_data->ID == wplf_valueof($form_data, 'agent') ? 'selected="selected"' : '') ?>
                            value='<?php echo $agent_data->ID ?>'><?php echo $agent_data->display_name ?></option>
                <?php } ?>
            </select>
            <p style="font-size: 11px;color: #888;font-style: italic;display: block;letter-spacing: 0.6px;margin-top: 5px">
                <?= esc_attr__('You may assign this form to any member of your team, who will also have access to this form, can see the form submissions and manage them.', LF_TEXT_DOMAIN); ?>
            </p>
        </div>
    </div>

</div>
