<?php
if(!defined("ABSPATH")) die("Shit happens!");



$general_setting_form = new LiveForms\Form\Form($general_setting_fields, ['id' => 'liveform-settings-form', 'class' => 'liveform-settings-form']);

echo $general_setting_form->render();

