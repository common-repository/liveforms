<?php

namespace LiveForms\FormEntries;


class FormEntries
{
    private $dbtable;

    public function __construct()
    {
        global $wpdb;
        $this->dbtable = "{$wpdb->prefix}liveforms_conreqs";

    }

    public function get($form_id)
    {
        global $wpdb;
        $entries = $wpdb->get_results("select * from {$this->dbtable} where fid = '{$form_id}'  order by id desc limit 0, 99999");
        $allentries = [];
        foreach ($entries as $entry){
            $allentries[] = new FormEntry($entry);
        }
        return $allentries;
    }
}
