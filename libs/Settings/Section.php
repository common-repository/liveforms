<?php


namespace LiveForms\Settings;


use LiveForms\__\__;
use LiveForms\Form\Form;

abstract class Section
{
    public $section = '';
    public $icon = 'fa fa-cog';
    public $title = '';
    public $settings_groups = '';
    public $settings_fields;
    public $success_msg = 'Settings saved successfully';

    function render()
    {
        $this->settings_fields['action'] = ['type' => 'hidden', 'attrs' => ['name' => 'action', 'value' => 'liveforms_save_settings']];
        $this->settings_fields['section'] = ['type' => 'hidden', 'attrs' => ['name' => 'section', 'value' => $this->section]];
        $attrs = ['id' => 'liveforms-settings-form', 'class' => 'liveforms-settings-form'];
        if($this->settings_groups)
            $attrs['groups'] = $this->settings_groups;
        $form = new Form($this->settings_fields, $attrs);
        //if(__::is_ajax())
        //    $form->noForm = true;
        echo $form->render();
    }
}