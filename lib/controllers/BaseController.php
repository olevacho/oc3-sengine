<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('Oc3Sengine_BaseController')) {

    class Oc3Sengine_BaseController {

        public $view;
        public $view_vars;
        public $model;

        public function load_view($name, $data) {
            $path = OC3SENGINE_PATH . "/views/" . $name . ".php";
            $this->view_vars = $data;
            if (file_exists($path)) {


                $this->view = $path; //new $view_name($data);
                //ucfirst()
            } else {
                $this->view = false;
            }
        }

        public function load_model($name) {
            $path = OC3SENGINE_PATH . "/lib/models/" . $name . ".php";
            if (file_exists($path)) {
                $model_name = OC3SENGINE_CLASS_PREFIX . ucfirst($name);
                if (!class_exists(OC3SENGINE_CLASS_PREFIX.ucfirst($name))) {
                    include_once $path;
                }
                $this->model = new $model_name();
                //ucfirst()
            } else {
                $this->model = false;
            }
        }

        public function render() {
            extract($this->view_vars, EXTR_SKIP);
            
            include $this->view;

        }

        function verifyPostRequest($r, $nonce = '', $action = '') {

            $r['result'] = 0;
            if (!isset($_POST)) {
                $r['result'] = 1;
                $r['msg'] = __('Invalid request','oc3-sengine');
                return $r;
            }

            if (!array_key_exists($nonce, $_POST)) {
                $r['result'] = 2;
                $r['msg'] = __('Security issues','oc3-sengine');
                return $r;
            }

            $verify_nonce = check_ajax_referer($action, $nonce, false);

            if (!$verify_nonce) {
                $r['result'] = 3;
                $r['msg'] = __('Security issues','oc3-sengine');
                return($r);
            }


            return $r;
        }

        
    }

}
