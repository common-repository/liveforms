<?php
if(!defined("ABSPATH")) die("Shit happens!");
?><div id="email-settings" class="tab-pane fade">
    <div class="text-right" style="margin-top: -20px;margin-bottom: 15px">
        <button type="button" class="btn btn-primary" data-target="#newemail" data-toggle="modal">Add New Email</button>
    </div>
    <div id="form_emails" class="panel-group">
        <div class="panel panel-lf" id="defaultemail_panel">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-5">
                        <input readonly="readonly" placeholder="<?php _e('Email Name','liveforms'); ?>" type="text" class="form-control bg-white" value="Admin Notification" />
                    </div>
                    <div class="col-md-7 text-right">
                        <a type="button" class="btn btn-secondary btn-collapse-control" data-toggle="collapse" href="#defaultemail" data-parent="#form_emails"  aria-expanded="false" aria-controls="defaultemail"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>
            </div>
            <div class="panel-body collapse in" id="defaultemail">
                <div class="form-group">
                    <label>
                        <input type="hidden" name="contact[no_receipt]" value="0">
                        <input type="checkbox" name="contact[no_receipt]" value="1" <?php checked(wplf_valueof($form_data, 'no_receipt'), 1); ?> > Disable
                    </label>
                    <p  class="note">
                        Do not send form submission email to admin.
                    </p>
                </div>
                <hr/>
                <div class="form-group">
                    <label for="email">Send Submission Notification:</label>
                    <input type="text" class="form-control" name="contact[admin_email][send_to]" placeholder="Email Address" id="email" value="<?php echo wplf_valueof($form_data, 'admin_email/send_to'); ?>"/>
                    <p class="note">
                        You may also need to send form submission notification to multiple email addresses, here you can set multiple email addresses separated by comma.
                    </p>
                </div>
                <hr/>
                <div class="form-group">

                    <label form="email">From Email: </label>
                    <input type="text" class="form-control" name="contact[admin_email][from_email]" placeholder="Email address" id="email" value="<?php echo wplf_valueof($form_data, 'admin_email/from_email') ?>"/>
                </div>
                <div class="form-group">

                    <label form="email">From Name: </label>
                    <input type="text" class="form-control" name="contact[admin_email][from_name]" placeholder="Name to show in From field" id="from" value="<?php echo wplf_valueof($form_data, 'admin_email/from_name') ?>"/>

                </div>
                <div class='form-group'>
                    <label >Email Message: </label>
                    <textarea class='form-control email-message' id="email_text" name="contact[admin_email][email_message]"><?php echo wplf_valueof($form_data, 'admin_email/email_message') ?></textarea>
                    <div class="note">Add request variable: <code>{{req_***}}</code>, server variable: <code>{{SRV_****}}</code></div>
                    <?php ///wp_editor(wplf_valueof($emails, 'email')); ?>
                </div>
            </div>
        </div>
        <?php
        if(is_array($form_emails))
            foreach ($form_emails as $emailid => $form_email){ ?>
                <div class="panel panel-lf panel-collapsed" id="form_<?= $emailid; ?>_panel">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-5">
                                <input name="form_emails[<?= $emailid; ?>][name]" placeholder="<?php _e('Email Name','liveforms'); ?>" type="text" class="form-control bg-white" value="<?= wplf_valueof($form_email, 'name'); ?>" />
                            </div>
                            <div class="col-md-7 text-right">
                                <a type="button" class="btn btn-secondary btn-collapse-delete" href="#form_<?= $emailid; ?>_panel"><i class="fa fa-trash text-danger"></i></a>
                                <a type="button" class="btn btn-secondary btn-collapse-control collapsed" data-toggle="collapse" data-parent="#form_emails" href="#form_<?= $emailid; ?>"  aria-expanded="false" aria-controls="form_<?= $emailid; ?>"><i class="fa fa-chevron-down"></i></a>
                            </div>
                        </div>
                    </div>

                    <div id="form_<?= $emailid ?>"  role="tabpanel" aria-labelledby="form_<?= $emailid ?>" class="panel-collapse panel-body collapse">
                        <div class="form-group">
                            <label for="email">Send To:</label>
                            <div class="input-group">
                                <div class="input-group-btn">
                                    <button type="button" data-field="#<?= $emailid; ?>_send_to" class="btn btn-secondary btn-smart-tags dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                                    <ul class="dropdown-menu email_vars">
                                    </ul>
                                </div>
                                <input type="text" class="form-control" id="<?= $emailid; ?>_send_to" name="form_emails[<?= $emailid; ?>][send_to]" placeholder="Email Address" value="<?= wplf_valueof($form_email, 'send_to'); ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label form="email">From Email: </label>
                            <div class="input-group">
                                <div class="input-group-btn">
                                    <button type="button" data-field="#<?= $emailid; ?>_from_email" class="btn btn-secondary btn-smart-tags dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                                    <ul class="dropdown-menu email_vars">
                                    </ul>
                                </div>
                                <input type="text" class="form-control" id="<?= $emailid; ?>_from_email" name="form_emails[<?= $emailid; ?>][from_email]" placeholder="Email address" value="<?= wplf_valueof($form_email, 'from_email'); ?>"/>
                            </div>
                        </div>
                        <div class="form-group">

                            <label form="email">From Name: </label>
                            <input type="text" class="form-control" name="form_emails[<?= $emailid; ?>][from_name]" placeholder="Name to show in From field" id="from" value="<?= wplf_valueof($form_email, 'from_name'); ?>"/>

                        </div>
                        <div class="form-group">

                            <label form="email">Email Subject: </label>
                            <input type="text" class="form-control" name="form_emails[<?= $emailid; ?>][subject]" placeholder="" value="<?= wplf_valueof($form_email, 'subject'); ?>"/>

                        </div>
                        <div class='form-group'>
                            <label >Email Message: </label>
                            <textarea class='form-control email-message' id="<?= $emailid ?>_message" name="form_emails[<?= $emailid; ?>][email_message]" ><?= wplf_valueof($form_email, 'email_message'); ?></textarea>
                            <div class="note">Add request variable: <code>{{req_***}}</code>, server variable: <code>{{SRV_****}}</code></div>
                        </div>
                    </div>
                </div>
            <?php } ?>
    </div>


    <div id="newemail" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add New Email</h4>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control input-lg" id="emailname" placeholder="Email Name">
                    <em class="text-muted">Ex: User notification</em>
                </div>
                <div class="modal-footer">
                    <button type="button" id="createemailaction" class="btn btn-primary">Create</button>
                </div>
            </div>

        </div>
    </div>


    <script type="text/liveforms-email-notification-template" id="email_notification_template" style="display: none">
        <div class="panel panel-lf">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-5">
                        <input name="form_emails[{{ id }}][name]" placeholder="<?php _e('Email Name','liveforms'); ?>" type="text" class="form-control bg-white" value="{{ name }}" />
                    </div>
                    <div class="col-md-7 text-right">
                        <a type="button" class="btn btn-secondary btn-collapse-delete" href="#form_{{ id }}_panel"><i class="fa fa-trash text-danger"></i></a>
                        <a type="button" class="btn btn-secondary btn-collapse-control" data-toggle="collapse" href="#form_{{ id }}"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>
            </div>
            <div class="panel-body" id="form_{{ id }}">
                <div class="form-group">
                    <label for="email">Send To:</label>
                    <div class="input-group">
                        <div class="input-group-btn">
                            <button type="button" data-field="#{{ id }}_send_to" class="btn btn-secondary btn-smart-tags dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                            <ul class="dropdown-menu email_vars">
                            </ul>
                        </div>
                        <input type="text" class="form-control" name="form_emails[{{ id }}][send_to]" placeholder="Email Address" id="{{ id }}_send_to" value=""/>
                    </div>
                </div>
                <div class="form-group">

                    <label form="email">From Email: </label>
                    <div class="input-group">
                        <div class="input-group-btn">
                            <button type="button" data-field="#{{ id }}_from_email" class="btn btn-secondary btn-smart-tags dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></button>
                            <ul class="dropdown-menu email_vars">
                            </ul>
                        </div>
                        <input type="text" class="form-control" name="form_emails[{{ id }}][from_email]" placeholder="Email address" id="{{ id }}_from_email" value="<?php echo (isset($form_data['email']) ? $form_data['email'] : "") ?>"/>
                    </div>
                </div>
                <div class="form-group">

                    <label form="email">From Name: </label>
                    <input type="text" class="form-control" name="form_emails[{{ id }}][from_name]" placeholder="Name to show in From field" id="from" value="<?php echo (isset($form_data['from']) ? $form_data['from'] : "") ?>"/>

                </div>
                <div class="form-group">

                    <label form="email">Email Subject: </label>
                    <input type="text" class="form-control" name="form_emails[{{ id }}][subject]" placeholder="" value=""/>

                </div>
                <div class='form-group'>
                    <label >Email Message: </label>
                    <textarea class='form-control email-message' name="form_emails[{{ id }}][email_message]" id="{{ id }}_message"><?php if (isset($form_data['email_text'])) echo $form_data['email_text'] ?></textarea>
                    <div class="note">Add request variable: <code>{{req_***}}</code>, server variable: <code>{{SRV_****}}</code></div>
                </div>
            </div>
        </div>
    </script>


</div>
