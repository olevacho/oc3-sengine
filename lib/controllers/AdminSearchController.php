<?php
if ( ! defined( 'ABSPATH' ) ) exit;



if (!class_exists('Oc3Sengine_AdminSearchController')) {

    class Oc3Sengine_AdminSearchController extends Oc3Sengine_BaseController {



        
        public function __construct() {
            $this->load_model('SearchModel');
            add_action('wp_ajax_oc3se_store_search_default', [$this, 'processSearchDefSubmit']);
            add_action('admin_enqueue_scripts', [$this, 'adminInlineJs']);

        }
        
        public function registerStyles(){
            wp_register_script(
                    'oc3-sengine-search',
                   false,
                    array(),
                    '1.0.28',false);

        }
        
        
        public function processSearchDefSubmit() {
            
            if ((!isset($_SERVER['REQUEST_METHOD']) || 'POST' !== $_SERVER['REQUEST_METHOD'])) {
                return;
            }

            $r = ['result' => 0, 'msg' => __('Unknow problem','oc3-sengine')];
            $nonce = OC3SENGINE_PREFIX_SHORT . 'gpt_confnonce';
            $r = $this->verifyPostRequest($r, $nonce, $nonce);
            if ($r['result'] > 0) {
                wp_send_json($r);
                exit;
            }
            
            $verify_nonce = check_ajax_referer($nonce, $nonce, false);
            if(!$verify_nonce){
                $result['msg'] = __('Wrong request', 'oc3-sengine');
                $result['result'] = 402;
                wp_send_json($result);
                exit;
            }
            
            if (!Oc3Sengine_Utils::checkEditInstructionAccess()) {
                $r['result'] = 10;
                $r['msg'] = __('Access denied','oc3-sengine');
                wp_send_json($r);
                exit;
            }
            $data = [];
            
            if (isset($_POST[OC3SENGINE_PREFIX_SHORT . 'background_color'])) {
                $data['background_color']  = sanitize_text_field(wp_unslash($_POST[OC3SENGINE_PREFIX_SHORT . 'background_color']));
            }
            
            if (isset($_POST[OC3SENGINE_PREFIX_SHORT . 'border_color'])) {
                $data['border_color']  = sanitize_text_field(wp_unslash($_POST[OC3SENGINE_PREFIX_SHORT . 'border_color']));
            }
            
            if (isset($_POST[OC3SENGINE_PREFIX_SHORT . 'font_color'])) {
                $data['font_color']  = sanitize_text_field(wp_unslash($_POST[OC3SENGINE_PREFIX_SHORT . 'font_color']));
            }
            
            if (isset($_POST[OC3SENGINE_PREFIX_SHORT . 'font_size'])) {
                $data['font_size']  = (int)sanitize_text_field(wp_unslash($_POST[OC3SENGINE_PREFIX_SHORT . 'font_size']));
            }
            if (isset($_POST[OC3SENGINE_PREFIX_SHORT . 'search_box_height'])) {
                $data['search_box_height']  = (int)$_POST[OC3SENGINE_PREFIX_SHORT . 'search_box_height'];
            }
            
            if (isset($_POST[OC3SENGINE_PREFIX_SHORT . 'search_box_height_metrics'])) {
                $data['search_box_height_metrics']  = sanitize_text_field(wp_unslash($_POST[OC3SENGINE_PREFIX_SHORT . 'search_box_height_metrics']));
            }
            
            if (isset($_POST[OC3SENGINE_PREFIX_SHORT . 'search_box_width'])) {
                $data['search_box_width']  = sanitize_text_field(wp_unslash($_POST[OC3SENGINE_PREFIX_SHORT . 'search_box_width']));
            }
            
            if (isset($_POST[OC3SENGINE_PREFIX_SHORT . 'search_box_width_metrics'])) {
                $data['search_box_width_metrics']  = sanitize_text_field(wp_unslash($_POST[OC3SENGINE_PREFIX_SHORT . 'search_box_width_metrics']));
            }
            
            if (isset($_POST[OC3SENGINE_PREFIX_SHORT . 'search_button_text_color'])) {
                $data['search_button_text_color']  = sanitize_text_field(wp_unslash($_POST[OC3SENGINE_PREFIX_SHORT . 'search_button_text_color']));
            }
            //search_button_color
            if (isset($_POST[OC3SENGINE_PREFIX_SHORT . 'search_button_color'])) {
                $data['search_button_color']  = sanitize_text_field(wp_unslash($_POST[OC3SENGINE_PREFIX_SHORT . 'search_button_color']));
            }
            
            if (isset($_POST[OC3SENGINE_PREFIX_SHORT . 'search_loader_color'])) {
                $data['search_loader_color']  = sanitize_text_field(wp_unslash($_POST[OC3SENGINE_PREFIX_SHORT . 'search_loader_color']));
            }
            
            if (isset($_POST[OC3SENGINE_PREFIX_SHORT . 'number_results'])) {
                $data['number_results']  = (int)$_POST[OC3SENGINE_PREFIX_SHORT . 'number_results'];
            }

            
            if (isset($_POST[OC3SENGINE_PREFIX_SHORT . 'results_background_color'])) {
                $data['results_background_color']  = sanitize_text_field(wp_unslash($_POST[OC3SENGINE_PREFIX_SHORT . 'results_background_color']));
            }
            if (isset($_POST[OC3SENGINE_PREFIX_SHORT . 'results_font_color'])) {
                $data['results_font_color']  = sanitize_text_field(wp_unslash($_POST[OC3SENGINE_PREFIX_SHORT . 'results_font_color']));
            }
            if (isset($_POST[OC3SENGINE_PREFIX_SHORT . 'results_font_size'])) {
                $data['results_font_size']  = (int)$_POST[OC3SENGINE_PREFIX_SHORT . 'results_font_size'];
            }
            
            if (isset($_POST[OC3SENGINE_PREFIX_SHORT . 'access_for_guests'])) {
                $data['access_for_guests']  = (int)$_POST[OC3SENGINE_PREFIX_SHORT . 'access_for_guests'] > 0?1:0;
            }else{
                $data['access_for_guests']  = 0;
            }
            
            
            
            $data['id_type']  = 1;
            $chat_bot_hash = 'default';
            
            $res = $this->model->storeSearchOptions($chat_bot_hash,$data);
            
            if($res){
                $r['result'] = 200;
                $r['msg'] = __('OK','oc3-sengine');
            }else{
                $r['result'] = 500;
                $r['msg'] = __('Error','oc3-sengine');
            }
            wp_send_json($r);
            exit;
            
        }

        public function registerAdminMenu() {

        }
        
        function adminInlineJs(){ 
            
            $current_screen = get_current_screen();
            //echo $current_screen->id;
            if ($current_screen->id !== 'oc3-sengine_page_oc3sengine_search') {
                return;
            }
            
            wp_enqueue_script('oc3-sengine-search',OC3SENGINE_URL . '/views/resources/js/oc3-sengine-dummy.js', [], '1.0.0', true);

            $inline_js = "const oc3sengineajaxAction = '".esc_url(admin_url('admin-ajax.php'))."';\n".

                "let oc3se_search_confnonce = '".esc_html(wp_create_nonce('oc3se_search_confnonce'))."';".


                "jQuery(function () {
                        jQuery('#oc3sengine_searchconfigtabs').tabs({activate: function (event, ui) {
                            let oc3sengine_active_tab = jQuery('#oc3sengine_searchconfigtabs .oc3sengine_tab_panel:visible').attr('id');
                        }});
                });
                jQuery(document).ready(function () {

                });".
                "let oc3sengine_message_config_general_error = '".esc_html('There were errors during store configuration.', 'oc3-sengine')."';
                let oc3sengine_message_config_general_succes1 = '".esc_html('Configuration stored successfully.', 'oc3-sengine')."';"
                    . "";
            wp_add_inline_script('oc3-sengine-search', $inline_js);
            
         } 
         
        function showMainView(){
            $this->showSearchSettings();
        }
        
        function showSearchSettings() {
            if (!Oc3Sengine_Utils::checkEditInstructionAccess()) {
                return;
            }

            $model = $this->model;
            //var_dump($model);
            $default_settings = $model->getSearchAgentSettings('default');
            $this->load_view('backend/search/search', ['default_agent' => $default_settings, 'search_agent_options' => $default_settings->agent_options ]);
            $this->render();
            
        }

    }

}
