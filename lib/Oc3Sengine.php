<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('Oc3Sengine')) {

    class Oc3Sengine {
        public static $database_version = 2;
        public $admin_controller;
        public $frontend_dispatcher;

        
        public function __construct() {
            $this->admin_controller = new Oc3Sengine_AdminController();
            $this->frontend_dispatcher = new Oc3Sengine_FrontendDispatcher();
            add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
            add_filter('plugin_action_links', [$this, 'actionLinks'], 10, 2);
        }

        public function enqueueScripts() {
            wp_enqueue_script('oc3sengine_backend', OC3SENGINE_URL . '/views/resources/js/oc3sengine-admin.js', [], '1.03', true);
            wp_enqueue_script('oc3sengine_backendv2', OC3SENGINE_URL . '/views/resources/js/oc3sengine-admin-v2.js', [], '1.28', true);
        }

        public function actionLinks($links, $file) {
            $fl2 = plugin_basename(dirname(dirname(__FILE__))) . '/oc3-searchengine.php';
            if ($file == $fl2) {
                $mylinks[] = '<a href="' . esc_url(get_admin_url(null, 'admin.php?page=oc3sengine_settings')) . '">' . __('Settings', 'oc3-sengine') . '</a>';

                return $mylinks + $links;
            }
            return $links;
        }

        public static function install() {
            $models = ['gpt-4o' => 3, 'gpt-4o-mini' => 3, 'gpt-4' => 3]; //1-not text selected,2-text not selected, 3-text and selected
            update_option('oc3sengine_chatgpt_models', serialize($models));
        }

        public static function deactivate() {
            
        }

        public static function uninstall() {
            
        }

        public function bootstrap() {
            
        }

        
    }

}
