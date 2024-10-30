<?php
/**
 * User: shahnuralam
 * Date: 8/4/18
 * Time: 4:05 PM
 */

namespace LiveForms\GutenbergBlock;

if (!defined('ABSPATH')) die();

class GutenbergQueryStatusBlock{

    function __construct(){
        add_action( 'init', array($this, 'block'), 9 );
        add_action( 'admin_head', array($this, 'allFormsJSON'), 9 );

    }

    function block(){

        wp_register_script(
            'liveforms-gbbqs',
            plugins_url( '/js/query-status.js', __FILE__ ),
            ['wp-blocks', 'wp-element', 'wp-components', 'wp-editor'],
            1.0
        );
        wp_register_style(
            'liveforms-gbb-css',
            LF_BASE_URL.'assets/css/liveform-ui.min.css',
            [],
            1.0
        );
        register_block_type( 'liveforms/query-status', array(
            'render_callback' => array($this, 'output'),
            'editor_script' => 'liveforms-gbbqs',
            'editor_style' => 'liveforms-gbb-css',
        ) );
    }

    function allFormsJSON()
    {
        $__lf_allforms = get_posts(['post_type' => 'form', 'posts_per_page' => -1]);
        $forms = [['label' => __('Select Form', 'liveforms'), 'value' => 0]];
        foreach ($__lf_allforms as $form) {
            $forms[] = ['label' => $form->post_title, 'value' => $form->ID];
        }
        ?>
        <script>
            var __lf_allforms = <?= json_encode($forms); ?>;
        </script>
        <?php
    }

    function output( $attributes, $content){
        return "<section class='__wplf_gb_section __wplf_gb_form w3eden'>".do_shortcode("[liveform_query]")."</section>";
    }

}
