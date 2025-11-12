<?php

if ( ! defined( 'ABSPATH' ) ) exit;


if (!class_exists('Oc3Sengine_FrontendDispatcher')) {

    class Oc3Sengine_FrontendDispatcher{
        
        public $search_controller;
        

        public function __construct() {
            if (!class_exists('Oc3Sengine_PublicSearchController')) {
                $contr_path = OC3SENGINE_PATH . "/lib/controllers/PublicSearchController.php";
                include_once $contr_path;
            }
            $this->search_controller = new Oc3Sengine_PublicSearchController();
        }
        
        

    }

}
