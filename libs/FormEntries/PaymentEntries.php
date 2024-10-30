<?php


namespace LiveForms\FormEntries;

class PaymentEntries
{
    private $dbtable;

    public function __construct()
    {
        global $wpdb;
        $this->dbtable = "{$wpdb->prefix}liveforms_payments";

    }

    public function get($form_id)
    {
        global $wpdb;
        $entries = $wpdb->get_results("select * from {$this->dbtable} where form_id = '{$form_id}' and payment_status = 'Completed' order by id desc limit 0, 99999");
        $allentries = [];
        foreach ($entries as $entry){
            $allentries[$entry->id] = new PaymentEntry($entry);
        }
        return $allentries;
    }

}