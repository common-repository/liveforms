<?php

namespace LiveForms\FormEntries;


class FormEntryManager
{
    private $dbtable;

    public function __construct()
    {
        global $wpdb;
        $this->dbtable = "{$wpdb->prefix}liveforms_conreqs";

        add_action("wp_ajax_wplf_delete_entry", [ $this, 'deleteEntry' ]);
        add_action("wp_ajax_wplf_view_entry", [ $this, 'viewEntryFrontend' ]);
    }

    function deleteEntry()
    {
        $entryID = wplf_query_var('entry');
        LiveForms()->entry->delete($entryID);
        wp_send_json(['success' => true]);
    }

    function viewEntryFrontend()
    {
        echo "Work in progress...";
        die();
    }



}