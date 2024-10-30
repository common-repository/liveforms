/** Insert at caret **/
jQuery.fn.extend({
    insertAtCaret: function (myValue) {
        return this.each(function (i) {
            if (document.selection) {
                //For browsers like Internet Explorer
                this.focus();
                var sel = document.selection.createRange();
                sel.text = myValue;
                this.focus();
            } else if (this.selectionStart || this.selectionStart == '0') {
                //For browsers like Firefox and Webkit based
                var startPos = this.selectionStart;
                var endPos = this.selectionEnd;
                var scrollTop = this.scrollTop;
                this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos, this.value.length);
                this.focus();
                this.selectionStart = startPos + myValue.length;
                this.selectionEnd = startPos + myValue.length;
                this.scrollTop = scrollTop;
            } else {
                this.value += myValue;
                this.focus();
            }
        });
    }
});


var $modal_id = '';
String.prototype.wplf_shuffle = function () {
    var a = this.split(""),
        n = a.length;

    for (var i = n - 1; i > 0; i--) {
        var j = Math.floor(Math.random() * (i + 1));
        var tmp = a[i];
        a[i] = a[j];
        a[j] = tmp;
    }
    return a.join("");
}
/** Utility **/
var WPLF = {

    init: function ($) {

    },

    copy: function ($id) {
        var copyText = document.getElementById($id);
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        WPLF.notify('<i class="fa fa-check-double"></i> Copied', 'success', 'top-center', 1000);
    },

    copyTxt: function (textToCopy) {
        WPLF.notify('<i class="fa fa-check-double"></i> Copied', 'success', 'top-center', 1000);
        // navigator clipboard api needs a secure context (https)
        if (navigator.clipboard && window.isSecureContext) {
            // navigator clipboard api method'
            return navigator.clipboard.writeText(textToCopy);
        } else {
            // text area method
            let textArea = document.createElement("textarea");
            textArea.value = textToCopy;
            // make the textarea out of viewport
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            textArea.style.top = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            return new Promise((res, rej) => {
                // here the magic happens
                document.execCommand('copy') ? res() : rej();
                textArea.remove();
            });
        }
    },

    uniqueID: function (prefix) {
        var uniq = Date.now() + "abcdefghijklmnopqrstuvwxyz";
        uniq = uniq.wplf_shuffle();
        uniq = uniq.substring(1, 10);
        return ((prefix !== undefined) ? prefix : '') + uniq;
    },

    popupWindow: function (url, title, w, h) {
        /* Fixes dual-screen position                         Most browsers      Firefox */
        var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
        var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

        var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

        var left = ((width / 2) - (w / 2)) + dualScreenLeft;
        var top = ((height / 2) - (h / 2)) + dualScreenTop;
        var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

        /* Puts focus on the newWindow */
        if (window.focus) {
            newWindow.focus();
        }

        return false;
    },

    bootAlert: function (heading, content, width) {
        var html;
        if (!width) width = 400;
        if ($modal_id !== '') jQuery($modal_id).remove();
        var modal_id = '__bootModal_' + WPLF.uniqueID();
        $modal_id = '#' + modal_id;
        html = '<div class="w3eden" id="w3eden' + modal_id + '"><div id="' + modal_id + '" class="modal fade" tabindex="-1" role="dialog">\n' +
            '  <div class="modal-dialog" style="width: ' + width + 'px" role="document">\n' +
            '    <div class="modal-content">\n' +
            '      <div class="modal-header" style="padding: 12px 15px;background: rgba(0,0,0,0.02);">\n' +
            '        <h4 class="modal-title" style="font-size: 10pt;font-weight: 600;padding: 0;margin: 0;letter-spacing: 0.5px">' + heading + '</h4>\n' +
            '      </div>\n' +
            '      <div class="modal-body fetfont" style="line-height: 1.5;text-transform: unset;font-weight:400;letter-spacing:0.5px;font-size: 12px">\n' +
            '        ' + content + '\n' +
            '      </div>\n' +
            '      <div class="modal-footer" style="padding: 10px 15px">\n' +
            '        <button type="button" class="btn btn-secondary btn-xs" data-target="#' + modal_id + '" data-dismiss="modal">Close</button>\n' +
            '      </div>\n' +
            '    </div>\n' +
            '  </div>\n' +
            '</div></div>';
        jQuery('body').append(html);
        jQuery("#" + modal_id).modal({show: true, backdrop: 'static'});
        return jQuery("#" + modal_id);
    },


    /**
     * Local push notification
     * @param title
     * @param message
     * @param icon
     * @param image
     * @param url
     */
    pushNotify: function (title, message, icon, image, url) {
        if (!('Notification' in window) || !('ServiceWorkerRegistration' in window)) {
            return;
        }

        Notification.requestPermission(function (result) {
            if (result === 'granted') {
                console.log('Notification: ' + result);
                try {
                    var notification = new Notification(title, {
                        body: message,
                        icon: icon ? icon : 'https://cdn1.iconfinder.com/data/icons/hawcons/32/698558-icon-47-note-important-512.png',
                        image: image ? image : ''
                    });
                    if (url) {
                        notification.onclick = function (e) {
                            e.preventDefault();
                            window.open(url, '_blank');
                        };
                    }
                } catch (err) {
                    console.log('Notification API error: ' + err);
                }
            } else {
                console.log('Notification: ' + result);
            }
        });

    },


    /**
     * Shows notification
     * @param message
     * @param type
     * @param position
     */
    notify: function (message, type, position, autoclose) {
        var $ = jQuery;
        if (type === undefined || !type) type = 'info';
        if (position === undefined || !position) position = 'top-right';
        if (type === 'danger') type = 'error';
        var notifycont = position.indexOf('#') >= 0 ? position : '#wplf-notify-' + position;
        if ($(notifycont).length == 0)
            $('body').prepend("<div id='wplf-notify-" + position + "'></div>");
        var notif = $("<div class='wplf-notify fetfont wplf-notify-" + type + "' style='display: none'>" + message + "</div>");
        $(notifycont).append(notif);
        $(notif).fadeIn();
        if (autoclose !== undefined) {
            setTimeout(function () {

                $(notif).animate({
                    opacity: 0
                }, 1000, function () {
                    $(this).slideUp();
                });

            }, autoclose);
        }
        return $(notif);
    },

    /**
     * Shows notification
     * @param message
     * @param type
     * @param position
     */
    floatify: function (html, position) {
        var $ = jQuery;
        if (position === undefined || !position) position = 'top-right';
        var floatifycont = '#wplf-floatify-' + position;
        if ($(floatifycont).length == 0)
            $('body').prepend("<div class='w3eden' id='wplf-floatify-" + position + "'></div>");
        var floatify = $("<div class='wplf-floatify fetfont style='margin-right: -500px'>" + html + "</div>");
        $(floatifycont).append(floatify);
        $(floatify).animate({marginRight: '0px'});
        return $(floatify);
    },

    blockUI: function (element, xhr) {
        jQuery(element).addClass("blockui");
        if (xhr)
            xhr.addEventListener("load", function () {
                jQuery(element).removeClass("blockui");
            });
    },

    unblockUI: function (element) {
        jQuery(element).removeClass("blockui");
    },

    overlay: function (element, html) {
        var $ = jQuery;
        var overlaycontent = $("<div class='wplf-overlay-content' style='display: none'>" + html + "<div class='wplf-overlay-close' style='cursor: pointer'><i class='far fa-times-circle'></i> close</div></div>");
        $(element).addClass('wplf-overlay').append(overlaycontent);
        $(overlaycontent).fadeIn();
        $('body').on('click', '.wplf-overlay-close', function () {
            $(overlaycontent).fadeOut(function () {
                $(this).remove();
            });
        });
        return $(overlaycontent);
    },


    confirm: function (heading, content, buttons) {
        var html, $ = jQuery;
        var modal_id = '__boot_popup_' + WPLF.uniqueID();
        $("#w3eden__boot_popup").remove();
        var _buttons = '';
        if (buttons) {
            _buttons = '<div class="modal-footer" style="padding: 8px 15px;">\n';
            $.each(buttons, function (i, button) {
                var id = 'btx_' + i;
                _buttons += "<button id='" + id + "' class='" + button.class + " btn-xs' style='font-size: 10px;padding: 3px 20px;'>" + button.label + "</button> ";
            });
            _buttons += '</div>\n';
        }

        html = '<div class="w3eden" id="w3eden' + modal_id + '"><div id="' + modal_id + '" style="z-index: 9999999 !important;" class="modal fade" tabindex="-1" role="dialog">\n' +
            '  <div class="modal-dialog" role="document" style="max-width: 100%;width: 350px">\n' +
            '    <div class="modal-content" style="border-radius: 3px;overflow: hidden">\n' +
            '      <div class="modal-header" style="padding: 12px 15px;background: #f5f5f5;">\n' +
            '        <h4 class="modal-title" style="font-size: 9pt;font-weight: 500;padding: 0;margin: 0;font-family:var(--wplf-font), san-serif;letter-spacing: 0.5px">' + heading + '</h4>\n' +
            '      </div>\n' +
            '      <div class="modal-body text-center" style="font-family:var(--wplf-font), san-serif;letter-spacing: 0.5px;font-size: 10pt;font-weight: 300;padding: 25px;line-height: 1.5">\n' +
            '        ' + content + '\n' +
            '      </div>\n' + _buttons +
            '    </div>\n' +
            '  </div>\n' +
            '</div></div>';
        $('body').append(html);
        $("#" + modal_id).modal('show');
        $.each(buttons, function (i, button) {
            var id = 'btx_' + i;
            $('#' + id).unbind('click');
            $('#' + id).bind('click', function () {
                button.callback.call($("#" + modal_id));
                return false;
            });
        });
        return $("#" + modal_id);
    },


};

/** on load **/

jQuery(function ($) {

    $('textarea:not(.noqt)').each(function () {
        settings = {
            id: this.id
        }
        quicktags(settings);

    });

    if($('#custom_css').length > 0) {
        editor = wp.codeEditor.initialize($('#custom_css'), wplfcm_css_settings);
        editor.codemirror.setOption('mode', 'css');
        editor = wp.codeEditor.initialize($('#custom_js'), wplfcm_css_settings);
        editor.codemirror.setOption('mode', 'javascript');
    }

    let $body = $('body');

    let ddn_items = "", eq_num_fields = "";

    function prepare_ddn_items() {
        ddn_items = "<li><a href=\"#\" data-target=\"__target__\" data-value=\"{{form_entry_data}}\">From Entry ( All Fields )</a></li>";
        ddn_items += "<li><a href=\"#\" data-target=\"__target__\" data-value=\"{{entry_identifier}}\">Tracking Number</a></li>";
        ddn_items += "<li><a href=\"#\" data-target=\"__target__\" data-value=\"{{reply_link}}\">Reply Link</a></li>";
        $('#wplf-form-preview .cog-trigger').each(function () {
            let id = $(this).data('field');
            if(id) {
                let _id = id.replace("#field_", "");
                let label = $('#label_' + _id).html();
                label = label ? label : _id;
                if (_id.indexOf("Space_") < 0)
                    ddn_items += '<li><a href="#" data-target="__target__" data-value="{{' + _id + '}}">' + label + '</a></li>';
            }
        });
    }

    function prepare_num_fields() {
        eq_num_fields = '';
        $('#wplf-form-preview .cog-trigger').each(function () {
            let id = $(this).data('field');
            if(id) {
                let _id = id.replace("#field_", "");
                let label = $('#label_' + _id).html();
                label = label ? label : _id;
                if (_id.indexOf("umber_") > 0)
                    eq_num_fields += '<li><a class="eq_field" href="#" data-target="__target__" data-value="{{' + _id + '}}">' + label + '</a></li>';
            }
        });
    }

    function add_qt_button($toolbar) {
        var textareaid = '#' + $toolbar.attr('id').replace('qt_', '').replace('_toolbar', '');
        var btnid = $toolbar.attr('id') + '_stbtn';
        var $qtbtn = $('#' + btnid);
        ddn = ddn_items.replaceAll("__target__", textareaid);
        ddn = '<ul class="dropdown-menu lf-dropdown" style="max-height: 200px;overflow: auto;" aria-labelledby="dLabel">' + ddn + '</ul>';
        /*console.log($qtbtn);*/
        if ($qtbtn.length === 0)
            $toolbar.prepend("<div id='" + btnid + "' style='margin-right: 10px;display: inline-block'></div>");
        $('#' + btnid).html("<div class='dropdown'><button type='button' data-tid='" + textareaid + "' data-toggle='dropdown' class='btn btn-primary btn-sm qtdd-trigger' style='border-radius: 500px;padding: 5px 20px !important;'><i class='fa fa-bars'></i> &nbsp;Form Field</button>" + ddn + "</div>");
    }

    function populate_option_field_preview($fieldid) {
        var preview_html = "";
        var type = $fieldid.split("_");
        type = type[0].toLowerCase();
        if(['radio', 'checkbox'].includes(type)) {
            $('.' + $fieldid + '_name').each(function () {
                preview_html += '<label><input type="' + type + '" disabled="disabled" value="" name="submitform[]"> <span>' + $(this).val() + '</span></label>';
            });
            $('#' + $fieldid + '_values').html(preview_html);
        }
    }

    setTimeout(function () {

        var ddn = '';

        prepare_ddn_items();
        prepare_num_fields();

        $('.quicktags-toolbar').each(function () {
            add_qt_button($(this));
        });

        $('.eq-num-fields').each(function () {
            $(this).html(eq_num_fields.replaceAll('__target__', $(this).data('target')));
        });



    }, 2000);

    $('#form_emails').collapse({
        toggle: true
    });

    $body.on('click', '.eq_field', function () {
        var eqf = $($(this).data('target'));
        eqf.val(eqf.val() + $(this).data('value'));
    });

    $body.on('click', '.qtdd-trigger', function () {
        prepare_ddn_items();
        var textareaid = $(this).data('tid');
        var ddn = ddn_items.replaceAll("__target__", textareaid);
        $(this).next('.dropdown-menu.lf-dropdown').html(ddn);
    });

    $body.on('click', '.wplf_redirect_to', function () {
        $('.wplf-redirect-selection .form-control').addClass('hide');
        $('#redirect_to_' + $(this).val()).removeClass('hide');
    });

    $body.on('click', '.dropdown-menu.email_vars a', function (e) {
        e.preventDefault();
        $($(this).data('target')).val($(this).data('value'));
    });

    $body.on('click', '.quicktags-toolbar .dropdown-menu a', function (e) {
        e.preventDefault();
        $($(this).data('target')).insertAtCaret($(this).data('value'));
    });

    $body.on('click', '.btn-smart-tags', function () {
        var ddn = '<li class="active"><a href="#">Form Fields</a></li>';
        var target = $(this).data('field');
        $('#wplf-form-preview .email-fields').each(function () {
            var field_id = $(this).data('id');
            var field_label = $('#label_' + $(this).data('id')).html();
            ddn += '<li><a href="#" data-target="' + target + '" data-value="{{' + field_id + '}}">' + field_label + '</a></li>';
        });
        ddn += '<li class="active"><a href="#">Account</a></li>';
        ddn += '<li><a href="#" data-target="' + target + '" data-value="{{user_email}}">User Email</a></li>';
        ddn += '<li><a href="#" data-target="' + target + '" data-value="{{admin_email}}">Admin Email</a></li>';
        $('.dropdown-menu.email_vars').html(ddn);
    });

    $body.on('click', '.btn-del-row', function (e) {
        e.preventDefault();
        $($(this).data('rowid')).addClass('delete-area');
        if (confirm("Deleting Row!\r\nAre you sure?")) {
            $($(this).data('rowid')).slideUp(function () {
                $(this).remove();
            });
        }
        $($(this).data('rowid')).removeClass('delete-area');
    });

    $body.on('click', '.btn-del-col', function (e) {
        e.preventDefault();
        $($(this).data('colid')).addClass('delete-area');
        if (confirm("Deleting Column!\r\nAre you sure?")) {
            $($(this).data('colid')).slideUp(function () {
                $(this).remove();
            });
        }
        $($(this).data('colid')).removeClass('delete-area');
    });

    $body.on('click', '.btn-collapse-delete', function (e) {
        e.preventDefault();
        if (confirm('Are you sure?')) {
            $($(this).attr('href')).slideUp(function () {
                $(this).remove();
            });
        }
    });

    $body.on('click', '.btn-collapse-control', function () {
        $('#form_emails .panel').addClass('panel-collapsed');
        //$('#form_emails .panel').addClass('panel-collapsed');
        $('#form_emails .btn-collapse-control').find('i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
        if ($(this).hasClass('collapsed')) {
            $(this).find('i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
            $($(this).attr('href') + '_panel').removeClass('panel-collapsed');
        }

    });

    $body.on('click', '.show-on-check', function () {
        if ($(this).is(':checked')) {
            $($(this).data('target')).slideDown();
        } else {
            $($(this).data('target')).slideUp();
        }
    });

    $body.on('change', '.set-option-alignment', function () {
        var $target = $(this).data('target');
        if ($target.indexOf('Radio'))
            $($target + " .radiobuttons").removeClass('pos-h pos-v').addClass($(this).val());
        if ($target.indexOf('Checkbox'))
            $($target + " .checkboxes").removeClass('pos-h pos-v').addClass($(this).val());
    });

    $body.on('change', '.wplf-space-height', function () {
        $($(this).data('target')).css('height', $(this).val()+'px');
    });

    $body.on('click', '#createemailaction', function () {
        _.templateSettings = {
            interpolate: /\{\{(.+?)\}\}/g
        };
        var emailTempalate = _.template($('#email_notification_template').html())
        var emlid = 'email_' + _.now();
        $('#form_emails').append(emailTempalate({id: emlid, name: $('#emailname').val()}));
        quicktags({id: emlid + "_message"});
        $('#newemail').modal('hide');
        setTimeout(function () {
            prepare_ddn_items();
            add_qt_button($('#qt_' + emlid + '_message_toolbar'));
        }, 2000);
    });

    $body.on('click', '.add-option', function () {
        $("#option_" + this.rel + " tbody").append($("#option_" + this.rel + " tbody tr:last-child").clone());
        populate_option_field_preview($(this).attr('rel'));
        return false;
    });

    $body.on('click', '.del-option', function () {
        if ($('#option_' + this.rel + " tbody tr").length > 1)
            $(this).parents('tr').remove();
        else
            alert("Can't be deleted. At least One Option is required.");
        populate_option_field_preview($(this).attr('rel'));
        return false;
    });

    $body.on('keyup', '.radio_option_label', function () {
        populate_option_field_preview($(this).attr('rel'));
    });

    $body.on('click', '.hide-label', function () {
        var target = $(this).data('target');
        if($(this).is(":checked"))
            $(target).addClass('hide')
        else
            $(target).removeClass('hide')
    });

    /* Delte form entry */
    $body.on('click', '.delete-entry', function (e) {
        if(!confirm('Are you sure?')) return false;
        e.preventDefault();
        var rowid = '#fer_'+$(this).data('id');
        $.get(ajaxurl, {action: 'wplf_delete_entry', entry: $(this).data('id')}, function (res){
            $(rowid).hide();
        });
    });
    $body.on('click', '.delete-entries', function (e) {
        if(!confirm('Are you sure?')) return false;
        e.preventDefault();
        $.get(ajaxurl+'?'+$('.fic').serialize(), {action: 'wplf_delete_entry' }, function (res){
            location.reload();
        });
    });

    $body.on('change', '.field-validate', function () {
        let index = $(this).data('target');
        $('.valid-data-src').hide();
        if(this.value === 'remote' || this.value === 'predef') {
            $('#'+this.value+'_validation_'+index).show();
        }
    });

    var textAreas = document.getElementsByTagName('textarea');
    Array.prototype.forEach.call(textAreas, function(elem) {
        elem.placeholder = elem.placeholder.replace(/\\n/g, '\n');
    });

    $('.ttip').tooltip();

});
