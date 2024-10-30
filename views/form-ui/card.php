<style>
    .__wplf_form {
        background: #ffffff;
        padding: 48px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        border-top: 5px solid var(--color-info);
        margin: 10px auto;
        max-width: 100%;
        width: {{width}};

    }
    .__wplf_form-header:empty{
        display: none;
    }
    .__wplf_form-header {
        margin: 0 0 10px;
        text-align: center;
        font-size: 14pt;
        font-weight: 800;
    }
    .__wplf_form-description:empty{
        display: none;
    }
    .__wplf_form-description{
        text-align: center;
        margin: 0 0 20px;
    }
    .__wplf_form-fields .well{
        text-align: center;
        padding: 10px;
        background: #f5f5f5;
    }
    .__wplf_form-fields select.form-control,
    .__wplf_form-fields input.form-control{
        padding: 0 20px;
        line-height: 52px;
        height: 52px;
    }
    .__wplf_form-fields textarea.form-control {
        padding: 20px;
        line-height: 1.5;
    }
    .__wplf_form-fields #submit{
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

<div class="__wplf_form">
    <h3 class="__wplf_form-header">{{name}}</h3>
    <div class="__wplf_form-description">{{description}}</div>
    <div class="__wplf_form-fields">{{form}}</div>
</div>