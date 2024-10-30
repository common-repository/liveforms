<?php
if(!defined("ABSPATH")) die("Shit happens!");
?>
<style>
    ._wplf .table{
        width: 100%;
        border: 0;
        margin: 0;
    }
    ._wplf .table th{
        background: rgba(0,0,0,0.05);
    }
    ._wplf .table th,
    ._wplf .table td{
        border: 0;
        border-bottom: 1px solid rgba(0,0,0,0.03);
    }
    ._wplf .table tr:last-child td{
        border: 0 !important;
    }
    ._wplf .table tr:nth-child(even) td {
        background: rgba(0,0,0,0.02);
    }
</style>
<div class="_wplf">
    <?php if(isset($_REQUEST['thanks'])) { ?>
        <div class="alert alert-success text-center lead">Thanks for your generous donation</div>
        <br/>
    <?php } ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong><?= wplf_valueof($params, 'title'); ?></strong>
        </div>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Date</th>
                <th>Name</th>
                <th>Amount</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($payments as $payment){ ?>
                <tr>
                    <td><?php echo $payment->date; ?></td>
                    <td><?php echo wplf_valueof($payment->entry_data, $name); ?></td>
                    <td><?php echo $payment->amount.' '.wplf_valueof($payment->payment_data, 'transactions/0/amount/currency'); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
