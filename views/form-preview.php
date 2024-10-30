<?php
if(!defined("ABSPATH")) die("Shit happens!");
get_header();
?>
<div style="width: 1000px;max-width: 100%;margin: 32px auto;border: 1px solid #dddddd;background: #ffffff;padding: 64px">
    <?= do_shortcode("[liveform form_id=".wplf_query_var('lfpreview', 'int')."]"); ?>
</div>
<?php
get_footer();
?>

