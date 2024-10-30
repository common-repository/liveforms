<div class="w3eden">
    <?php
    $entry = end($form_entries);
    $entry_data_formatted = $entry->entry_data_formatted;
    reset($form_entries);
    $non_submit_fields = array('Pageseparator', 'Mathresult');
    ?>
    <table class='table table-striped table-hover'>
        <thead>
        <tr>
            <th><?=__('Date', LF_TEXT_DOMAIN); ?></th>
            <?php
            foreach ($field_heads as $id => $field_label) {
                if($id === '{{fieldindex}}' || !isset($fields[$id])) continue;
                if (!in_array(substr($id, 0, strpos($id, '_')), $non_submit_fields)) {
                    $fieldids[] = $id;
                    //$fields[] = $field[];
                    if($id === '{{fieldindex}}') continue;
                    echo "<th class='text-left'>".$field_label."</th>";
                }
            }

            ?>
            <th><?=__('Action', LF_TEXT_DOMAIN); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php

        foreach ($form_entries as $entry) {
            //lfprecho($entry);
            $req_id = $entry->id;
            $form_data = $entry->entry_data;
            $time = date(get_option('date_format'), $entry->time);
            $url = admin_url("/admin-ajax.php?action=wplf_view_entry&form={$form_id}&entry={$entry->id}");
            $viewaction = "<button data-formid='{$form_id}' data-entryid='{$entry->id}' onclick='wplf_alert(\"Entry Details\", {url: \"{$url}\"})' class='btn btn-primary'>".__('View', LF_TEXT_DOMAIN)."</button>";
            $deleteaction = current_user_can(LF_ADMIN_CAP) ? "<a data-id='{$req_id}' href='#' class='btn btn-danger delete-entry'>".__('Delete', LF_TEXT_DOMAIN)."</a>" : '';
            echo "<tr id='fer_{$req_id}'><td>{$time}</td>";
            //$req = unserialize($req['data']);

            foreach ($fieldids as $id) {
                if($id === '{{fieldindex}}' || !isset($fields[$id])) continue;
                $value = isset($form_data[$id]) ? $form_data[$id] : '';

                $field = new $fields[$id]();
                $value = $field->print_value($value, $id, $req_id, $form_id);

                if(substr_count($id, 'Payment')) {
                    $payment = LiveForms()->paymentEntry->forFormEntry($req_id);
                    if($payment->amount > 0)
                        $value =  "Paid {$payment->amount} {$payment->currency} using {$value}";
                    else
                        $value = $value . "( No Payment )";
                }
                echo "<td>{$value}&nbsp;</td>";

            }
            echo "<td class='text-right' style='white-space: nowrap'>{$viewaction} {$deleteaction}</td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<script>
    let $modal_id = '';
    function wplf_alert (heading, content, width) {
        var html;
        if (!width) width = 400;
        if ($modal_id !== '') jQuery($modal_id).remove();
        var modal_id = '<?= uniqid('__bootModal_') ?>';
        if(typeof content === 'object') {
            url = content.url;
            content = `<div id='${modal_id}_cont'><i class='fa fa-sun fa-spin'></i> Loading...</div>`;
        }
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
        if(url !== '') {
            url = url.indexOf('?') > 0 ? url+'&__mdid=' + modal_id : url+'?__mdid=' + modal_id;
            jQuery("#" + modal_id + "_cont").load(url);
        }
        return jQuery("#" + modal_id);
    }
</script>