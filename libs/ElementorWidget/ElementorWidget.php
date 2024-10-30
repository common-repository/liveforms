<?php

namespace LiveForms\ElementorWidget;

use Elementor\Elements_Manager;
use Elementor\Widgets_Manager;


class ElementorWidget
{

    /**
     * 
     * 
     */
    public static function getInstance()
    {
        static $instance;
        if (is_null($instance)) {
            $instance = new self;
        }
        return $instance;
    }

    /**
     * 
     * 
     */
    private function __construct()
    {
        add_action("plugin_loaded", [$this, 'pluginLoaded']);

    }

    function pluginLoaded(){
        add_action( 'elementor/init', [ $this, 'addHooks' ] );
    }

    /**
     * 
     * 
     */
    function addHooks()
    {
        //add_action('elementor/elements/categories_registered', [$this, 'registerCategory'], 0);
        add_action('elementor/widgets/register', [$this, 'registerWidgets'], 99);
    }

    /**
     * 
     * 
     */
    public function registerCategory(Elements_Manager $elementsManager)
    {
        $elementsManager->add_category('wpdm', ['title' => 'Download Manager']);
    }

    /**
     * 
     * 
     */
    public function registerWidgets(Widgets_Manager $widget_manager)
    {

        require_once __DIR__.'/ElementorFormWidget.php';

        $widget_manager->register(new ElementorFormWidget());

    }

}
