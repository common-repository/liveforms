<?php
if(!defined('ABSPATH')) die('!');
$templates = [
        'blank' => [ 'preview' => LF_BASE_URL.'assets/previews/blank.png', 'name' => 'Start From Ground' ],
        'contact' => [ 'preview' => LF_BASE_URL.'assets/previews/contact.png', 'name' => 'Contact Form' ],
        'donation' => [ 'preview' => LF_BASE_URL.'assets/previews/donation.png', 'name' => 'Donation Form' ],
        'login' => [ 'preview' => LF_BASE_URL.'assets/previews/login.png', 'name' => 'Login Form' ],
        'support' => [ 'preview' => LF_BASE_URL.'assets/previews/support.png', 'name' => 'Support Form' ],
        'job-application' => [ 'preview' => LF_BASE_URL.'assets/previews/job-application.png', 'name' => 'Job Application Form' ]
];
?>
<div class="w3eden">
   <div id="liveforms-admin-container" class="liveforms-builder">
       <nav class="navbar navbar-default navbar-fixed-top-">

               <!-- Brand and toggle get grouped for better mobile display -->
               <div class="navbar-header">
                   <div class="navbar-brand">
                       <div class="d-flex">
                           <div class="logo">
                               <img src="<?= LF_BASE_URL ?>assets/images/liveforms-logo.png" style="width: 40px" alt="LF" />
                           </div>
                           <div style="font-weight: 600;letter-spacing: 1px">
                                <?= __('Create New Form', 'liveforms'); ?> &nbsp; <i class="fa fa-chevron-right"></i> &nbsp; <?= __('Select Template', 'liveforms'); ?>
                           </div>
                       </div>
                   </div>
               </div>

           <ul class="nav navbar-nav navbar-right">
               <li><a href="edit.php?post_type=form" class="close-btn"><i class="fas fa-times-circle"></i></a></li>
           </ul>



       </nav>

           <div class="tab-content">
               <div role="tabpanel" class="tab-pane fade in active" id="builder">
                   <div style="position: fixed;width: 100%;top: 64px;bottom:0;padding: 40px;overflow: auto">

                       <div class="container">
                           <?php
                           foreach($templates as $template_id => $template){ ?>

                               <div class="col-md-4">
                                   <div class="panel panel-default c-pointer formtemplate" data-template="<?= $template_id; ?>" data-formname="<?= $template_id === 'blank' ? 'New Form' : $template['name']; ?>">
                                       <div class="panel-body-np">
                                           <img class="img-responsive m-0" src="<?= $template['preview']; ?>" alt="" />
                                       </div>
                                       <div class="panel-footer bg-white"><strong><?= $template['name']; ?></strong></div>
                                   </div>
                               </div>

                           <?php }
                           ?>
                       </div>

                   </div>
               </div>

           </div>



    <div class="modal fade" id="newform" tabindex="-1" role="dialog" aria-labelledby="newformLabel" >
        <div class="modal-dialog" role="document" style="width: 350px">
            <div class="modal-content" id="cntp">
                <div class="modal-header" style="font-weight: 800;font-size: 12pt;letter-spacing: 1px">Create New Form</div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Form Name: <span class="text-danger">*</span></label>
                        <input type="text" style="border-radius: 3px" name="post_title" id="__post_title" required="required" class="form-control input-lg" placeholder="Untitled Form">
                    </div>
                    <label>Form Description:</label>
                    <textarea name="contact[description]" placeholder="What is this form about..." class="form-control"></textarea>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="form_template_id" id="form_template_id" />
                    <button class="btn btn-info btn-lg btn-block"><i class="fa fa-check-double"></i> <strong style="letter-spacing: 1px"> Create Form</strong></button>
                </div>
            </div>
        </div>
    </div>



</div>

<script>
    jQuery(function ($){
        $('body').on('click', '.formtemplate', function (){
            if(confirm("Continue with '" + $(this).data('formname') + "' template?")) {
                $('#form_template_id').val($(this).data('template'));
                $('#__post_title').val($(this).data('formname'));
                $('#cntp').addClass('blockui');
                $('#post').submit();
            }
        });
    });
</script>
