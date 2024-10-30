<?php


namespace LiveForms\FormEntries;


class PaymentEntry
{
    public $id;
    public $form_id;
    public $entry_id;
    public $payer_name = 'Anonymous';
    public $payer_email;
    public $amount;
    public $currency;
    public $payment_method;
    public $payment_status;
    public $timestamp;
    public $date;
    public $transaction_id;
    public $payment_data;
    private $dbtable;

    public function __construct($entry = null)
    {
        global $wpdb;
        $this->dbtable = "{$wpdb->prefix}liveforms_payments";

        if($entry) {
            $this->id = $entry->id;
            $this->form_id = $entry->form_id;
            $this->entry_id = $entry->entry_id;
            $this->entry_data = LiveForms()->entry->get($entry->entry_id)->entry_data;
            $this->amount = $entry->amount;
            $this->currency = $entry->currency;
            $this->payment_method = $entry->payment_method;
            $this->transaction_id = $entry->transaction_id;
            if($entry->payment_data) {
                $this->payment_data = json_decode($entry->payment_data, true);
            }

            $this->date = wp_date(get_option('date_format'), $entry->date);
        }

    }

    public function get($id)
    {
        global $wpdb;
        $row = $wpdb->get_row("select * from {$this->dbtable} where id = '{$id}'", ARRAY_A);
        foreach ($row as $key => $value){
            $this->$key = $value;
        }
        return $this;
    }

    function forFormEntry($form_entry_id)
    {
        global $wpdb;
        $entry = $wpdb->get_row("select * from {$this->dbtable} where entry_id = '{$form_entry_id}' and payment_status = 'Completed'");
        return new PaymentEntry($entry);
    }
}