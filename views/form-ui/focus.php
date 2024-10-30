<style>
    .__wplf_form_focus {
        background: #ffffff;
        padding: 0;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin: 10px auto;
        border-radius: 16px;
        max-width: 100%;
        width: {{width}};

    }
    .__wplf_form_focus-top{
        background: var(--color-primary);
        padding: 48px 48px 120px;
    }
    .__wplf_form_focus-header:empty{
        display: none;
    }
    .__wplf_form_focus-header {
        margin: 0 0 10px;
        text-align: center;
        font-size: 14pt;
        font-weight: 800;
        color: #ffffff !important;
    }
    .__wplf_form_focus-description:empty{
        display: none;
    }
    .__wplf_form_focus-description{
        text-align: center;
        margin: 0 0 20px;
        color: #ffffff;
    }
    .__wplf_form_focus-fields{
        padding: 30px;
        margin: -100px 48px 48px 48px;
        border-radius: 8px;
        background: #ffffff;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    .__wplf_form-fields input.form-control{
        padding: 0 20px;
        line-height: 52px;
        height: 52px;
    }
    .__wplf_form-fields textarea.form-control {
        padding: 20px;
        line-height: 1.5;
    }

    .__wplf_form_focus-fields #submit{
        padding: 0 20px;
        line-height: 52px;
        height: 52px;
        margin-top: 10px;
        font-weight: 600;
    }
    .__wplf_form-fields .iti.iti--allow-dropdown input.form-control {
        padding-left: 48px !important;
    }

</style>

<div class="__wplf_form_focus">
    <div class="__wplf_form_focus-top">
        <h3 class="__wplf_form_focus-header">{{name}}</h3>
        <div class="__wplf_form_focus-description">{{description}}</div>
    </div>
    <div class="__wplf_form_focus-fields">{{form}}</div>
</div>