<?php
if(!defined("ABSPATH")) die("Shit happens!");
?>
<div id="form-settings" class="tab-pane fade">

    <div class="panel panel-lf">
        <div class="panel-heading"><?= esc_attr__('Form description', LF_TEXT_DOMAIN); ?>:</div>
        <div class="panel-body">
            <textarea rows="2" class="form-control" name="contact[description]" placeholder="Form description" id="form_description"><?php echo wplf_valueof($form_data, 'description'); ?></textarea>
        </div>
    </div>

    <div class="panel panel-lf">
        <div class="panel-heading"><?= esc_attr__('Successful form submission message', LF_TEXT_DOMAIN); ?>:</div>
        <div class="panel-body">
                <textarea rows="2" class="form-control" name="contact[thankyou]" placeholder="Thank you message" id="thankyou"><?php echo (isset($form_data['thankyou']) ? $form_data['thankyou'] : "Thank You!") ?></textarea>
        </div>
    </div>

    <div class="panel panel-lf">
        <div class="panel-heading"><?= esc_attr__('Redirect to', LF_TEXT_DOMAIN); ?>:</div>
        <div class="panel-heading bg-white">
            <label><input class="wplf_redirect_to" type="radio" name="contact[redirect_to]" value="self" <?php checked(true, (!wplf_valueof($form_data, 'redirect_to') || wplf_valueof($form_data, 'redirect_to') === 'self')); ?>> Same page </label>
            <label><input class="wplf_redirect_to" type="radio" name="contact[redirect_to]" value="page" <?php checked('page', wplf_valueof($form_data, 'redirect_to')) ?>> Custom page </label>
            <label><input class="wplf_redirect_to" type="radio" name="contact[redirect_to]" value="url" <?php checked('url', wplf_valueof($form_data, 'redirect_to')) ?>> Custom url</label>
        </div>
        <div class="panel-body wplf-redirect-selection">
                <div class="form-control input-lg <?php if(!(!wplf_valueof($form_data, 'redirect_to') || wplf_valueof($form_data, 'redirect_to') === 'self')) echo 'hide'; ?>" id="redirect_to_self">Stay on the same page after form submission</div>
                <?php wp_dropdown_pages(['selected' => wplf_valueof($form_data, 'redirect_to_page'), 'name' => 'contact[redirect_to_page]', 'id' => 'redirect_to_page' , 'class' => 'form-control input-lg '.((wplf_valueof($form_data, 'redirect_to') !== 'page') ? 'hide' : '')]) ?>
                <input placeholder="Enter a valid url" class="form-control input-lg <?= ((wplf_valueof($form_data, 'redirect_to') !== 'url') ? 'hide' : '') ?>" type="url" value="<?= wplf_valueof($form_data, 'redirect_to_url') ?>" id="redirect_to_url" name="contact[redirect_to_url]" />
        </div>
    </div>

    <div class="panel panel-lf">
        <div class="panel-heading"><?= esc_attr__('Access Control', LF_TEXT_DOMAIN); ?>:</div>
        <div class="panel-body">
            <p><?= esc_attr__( 'Who has access to this form:', LF_TEXT_DOMAIN ); ?></p>
            <?php
            if(isset($form_data['access']))
                $form_access = wplf_valueof($form_data, 'access', ['validate' => 'array', 'default' => []]);
            else
                $form_access = ['everyone'];
            //lfprecho($form_access);
            ?>
            <label class="d-block"><input type="checkbox" name="contact[access][]" value="everyone" <?php checked(1, in_array('everyone', $form_access)) ?>> <?= esc_attr__('Everyone', LF_TEXT_DOMAIN); ?></label>
            <input type="hidden" name="contact[access][]" value="none" />
            <?php
            global $wp_roles;
            foreach ($wp_roles->roles as $role_id => $role){ ?>
            <label class="d-block"><input type="checkbox" name="contact[access][]" value="<?= $role_id ?>" <?php checked(1, in_array($role_id, $form_access)) ?>> <?= $role['name']; ?></label>
            <?php } ?>
            <hr/>
            <label><?= esc_attr__( 'Login required message', LF_TEXT_DOMAIN ); ?></label>
            <textarea name="contact[login_to_access]" class="form-control"><?=wplf_valueof($form_data, 'login_to_access', ['validate' => 'html']); ?></textarea><hr/>
            <label><?= esc_attr__( 'Permission denied message', LF_TEXT_DOMAIN ); ?></label>
            <textarea name="contact[permission_denied]" class="form-control"><?=wplf_valueof($form_data, 'permission_denied', ['validate' => 'html']); ?></textarea>
        </div>
    </div>
    <div class="panel panel-lf">
        <div class="panel-heading"><?= esc_attr__('Form Data Processing', LF_TEXT_DOMAIN); ?>:</div>
        <div class="panel-body">
            <label class="d-block"><input type="checkbox" name="contact[antispam]" value="1" <?php checked(1, wplf_valueof($form_data, 'antispam')) ?>> <?= esc_attr__('Enable anti-spam protection', LF_TEXT_DOMAIN); ?></label>
            <label class="d-block"><input type="checkbox" name="contact[nodbentry]" value="1" <?php checked(1, wplf_valueof($form_data, 'nodbentry')) ?>> <?= esc_attr__('Disable storing form entry in database', LF_TEXT_DOMAIN); ?></label>
            <label class="d-block"><input type="checkbox" name="contact[autofill]" value="1" <?php checked(1, wplf_valueof($form_data, 'autofill')) ?>> <?= esc_attr__('Enable form auto-fill', LF_TEXT_DOMAIN); ?></label>
        </div>
    </div>

</div>
