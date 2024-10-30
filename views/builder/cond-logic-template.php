<?php
if(!defined("ABSPATH")) die("Shit happens!");
?>
<div  id="cond-logic-template" style="display: none">
    <div class='row row-bottom-buffer cond-row' rel="row">
        <div class='col-md-4'>
            <select class='form-control cond-field-selector' data-fieldindex="{{fieldindex}}" data-target="#cond_value_{{fieldindex}}_{{key}}" data-selection='' name='contact[fieldsinfo][{{fieldindex}}][condition][field][]'>
                <option value="">Select a field</option>
            </select>
        </div>
        <div class='col-md-3'>
            <select class='form-control cond-operator' data-selection='' name='contact[fieldsinfo][{{fieldindex}}][condition][op][]'>
                <option value='is'>Is</option>
                <option value='is-not'>Is not</option>
                <option value='less-than'>Less than</option>
                <option value='greater-than'>Greater than</option>
                <option value='contains'>Contains</option>
                <option value='starts-with'>Starts with</option>
                <option value='ends-with'>Ends with</option>
            </select>
        </div>
        <div class='col-md-4'>
            <div id="cond_value_{{fieldindex}}_{{key}}">
                <input type='text' value='' class="is-cond-data form-control" name='contact[fieldsinfo][{{fieldindex}}][condition][value][]'/>
            </div>
        </div>
        <div class="col-md-1">
            <a href="#" class="btn btn-link del-cond-option" rel="{{fieldindex}}"><i class="fas fa-times text-danger"></i></a>
        </div>
    </div>
</div>
