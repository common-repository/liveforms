<?php
if(!defined("ABSPATH")) die("Shit happens!");



$license_setting_form = new \LiveForms\Form\Form($license_setting_fields, ['id' => 'liveform-settings-form', 'class' => 'liveform-settings-form']);

echo $license_setting_form->render();

?>