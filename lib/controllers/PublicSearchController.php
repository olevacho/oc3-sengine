<?php
if ( ! defined( 'ABSPATH' ) ) exit;



if (!class_exists('Oc3Sengine_PublicSearchController')) {

    class Oc3Sengine_PublicSearchController extends Oc3Sengine_BaseController {
        
        public $agent = false;
        public $id_agent = 0;

        
        public function __construct() {
            
            if (!class_exists('Oc3Sengine_SearchUtils')) {
                require_once OC3SENGINE_PATH . '/lib/helpers/SearchUtils.php';
            }
            $this->load_model('SearchModel');
            
            add_action('wp_ajax_oc3_ajax_search_data',[$this,'searchSemantic']);
            add_action('wp_ajax_nopriv_oc3_ajax_search_data',[$this,'searchSemantic']);
            add_shortcode( 'oc3-sengine', array( $this, 'searchShortcode' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'registerScripts' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'registerStyles' ) );
        }
        
        public function registerScripts(){
            
            wp_enqueue_script( 'oc3sengine-default-agent', OC3SENGINE_URL . '/views/frontend/resources/js/oc3sengine-default-agent.js'
                    ,  array(), '1.0.12', true );
            
            
            wp_localize_script( 'oc3sengine-default-agent', 'oc3sengineParams', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'search_nonce' => wp_create_nonce( 'search_nonce' ),
                'source_title' => esc_html__('Sources','oc3-sengine'),
                'no_result_msg' => esc_html__('Nothing found','oc3-sengine'),
                'wrong_msg' => esc_html__('Something went wrong','oc3-sengine'),
                'read_more_msg' => esc_html__('Read more','oc3-sengine')
            ));
            
            $new_script = apply_filters( 'oc3sengine_enqueue_script_agent',['include' => false,'handle'=>'','src'=>'','deps'=>[],'ver'=>false,'args'=>[]] );
            if(is_array($new_script) && isset($new_script['include']) && $new_script['include']){
                wp_enqueue_script( $new_script['handle'], $new_script['src'],  $new_script['deps'], $new_script['ver'], $new_script['args'] );
            }
        }
        
        
        public function registerStyles(){
            wp_register_style(
                    'oc3-sengine-agent',
                    OC3SENGINE_URL . '/views/frontend/resources/css/oc3sengine-agent.css',
                    array(),
                    '1.0.27');
            
            
            
            $new_style = apply_filters( 'oc3sengine_enqueue_style_agent',['include' => false,'handle'=>'','src'=>'','deps'=>[],'ver'=>false,'media'=>'all'] );
            if(is_array($new_style) && isset($new_style['include']) && $new_style['include']){
                wp_enqueue_style(
                    $new_style['handle'],
                    $new_style['src'],
                    $new_style['deps'],
                    $new_style['ver'],
                    $new_style['media']    
                );
            }
        }
        
        //returns   post, title of post and excerpt from chunk
        
        public function searchSemantic(){

            $result = ['msg' => __('Unknow problem', 'oc3-sengine'), 'result' => 0, 'data' => []];
            
            $nonce = '_wpnonce';
            $r = $this->verifyPostRequest($result, $nonce, 'search_nonce');
            if ($r['result'] > 0) {
                $result['msg'] = __('Security problem', 'oc3-sengine');
                $result['result'] = 401;
                wp_send_json($result);
                exit;
            }
            
            $verify_nonce = check_ajax_referer('search_nonce', $nonce, false);
            if(!$verify_nonce){
                $result['msg'] = __('Wrong request', 'oc3-sengine');
                $result['result'] = 402;
                wp_send_json($result);
                exit;
            }
            
            if (!isset($_POST) || !is_array($_POST) || !isset($_POST['search'])) {
                $result['msg'] = __('Wrong request', 'oc3-sengine');
                $result['result'] = 403;
                wp_send_json($result);
                exit;
            }
            $search = sanitize_text_field(wp_unslash($_POST['search']));

            $search_content_success = false;

            $pinecone_api_key = sanitize_text_field(get_option(OC3SENGINE_PREFIX_LOW . 'pinecone_key', ''));
            $oc3_config_pinecone_index = sanitize_text_field(get_option(OC3SENGINE_PREFIX_LOW . 'config_pinecone_index', ''));
            $config_pinecone_namespace = $namespace = sanitize_text_field(get_option(OC3SENGINE_PREFIX_LOW . 'config_pinecone_namespace', ''));
            $config_pinecone_topk = sanitize_text_field(get_option(OC3SENGINE_PREFIX_LOW . 'config_pinecone_topk', 10));
            $config_pinecone_confidence = (int) (get_option(OC3SENGINE_PREFIX_LOW . 'config_pinecone_confidence', ''));

            if (!class_exists('Oc3Sengine_RagUtils')) {
                require_once OC3SENGINE_PATH . '/lib/helpers/RagUtils.php';
            }
            $embedded_res = Oc3Sengine_RagUtils::makeEmbedingCall($search);
            if (isset($embedded_res['status']) && $embedded_res['status'] == 200) {
                $embedded_question = $embedded_res['embedding'];
                if (!empty($embedded_question)) {

                    $headers = array(
                        'Content-Type' => 'application/json',
                        'Api-Key' => $pinecone_api_key
                    );
                    $pinecone_body = array(
                        'vector' => $embedded_question,
                        'topK' => $config_pinecone_topk
                    );
                    if (isset($config_pinecone_namespace) && strlen($config_pinecone_namespace) > 0) {
                        $pinecone_body['namespace'] = $namespace;
                    }
                    $response = wp_remote_post('https://' . $oc3_config_pinecone_index . '/query', array(
                        'headers' => $headers,
                        'body' => wp_json_encode($pinecone_body)
                    ));

                    $context = '';
                    $log_result = [];
                    if (is_wp_error($response)) {
                        $log_result['data'] = esc_html($response->get_error_message());
                    } else {
                        //$body_content = wp_remote_retrieve_body($response);
                        $body = json_decode($response['body'], true);
                        if ($body) {
                            if (isset($body['matches']) && is_array($body['matches']) && count($body['matches'])) {
                                $this->load_model('ChunksModel');
                                $model = $this->model;
                                $log_result['result'] = 505;
                                $result['msg'] = __('Empty list of matches', 'oc3-sengine');
                                foreach ($body['matches'] as $match) {
                                    if ($match['score'] >= $config_pinecone_confidence / 100) {
                                        $chunk = $model->getChunkById($match['id']);
                                        if (is_object($chunk) && isset($chunk->chunk_content)) {
                                            $search_content_success = true;
                                            $content = preg_replace('~Title\:.+?Content:~','',$chunk->chunk_content);
                                            
                                            $post_id = (int) $chunk->id_sourcepost;
                                            $row = ['id' => $post_id, 'title' => sanitize_text_field($chunk->title),
                                                'link' => '#',
                                                'excerpt' => sanitize_text_field(substr($content, 0, 100))];
                                            if ($post_id > 0) {
                                                $post = get_post($post_id);
                                                
                                                if (is_object($post) && isset($post->ID) && $post->ID > 0) {
                                                    $row['title'] = $post->post_title;
                                                    $row['link'] = sanitize_url(get_permalink($post));
                                                }
                                            }
                                            if('publish' === get_post_status( $post_id )){
                                                $result['data'][] = $row;
                                            }
                                        }
                                    }
                                }
                                if ($search_content_success == true) {
                                    $result['result'] = 200;
                                    $result['msg'] = __('Success', 'oc3-sengine');
                                }
                            } else {
                                $result['result'] = 504;
                                $result['msg'] = __('Empty response', 'oc3-sengine');
                            }
                        } else {
                            $result['result'] = 503;
                            $result['msg'] = __('Wrong format of response', 'oc3-sengine');
                        }
                    }
                }
            } else {
                $result['result'] = 502;
                $result['msg'] = __('Network error', 'oc3-sengine');
            }


            wp_send_json($result);
            exit;
        }
        
        public function searchShortcode($atts){
            
            $atts = empty( $atts ) ? [] : $atts;
            $atts = apply_filters( 'oc3sengine_search_params', $atts );
            $attr_agent_id = isset($atts['agent_id'])?$atts['agent_id']:'default';
            
            $resolved_agent = $this->getAgentInfo( $attr_agent_id );
            
            if ( isset( $resolved_agent['error'] ) ) {
              return $resolved_agent['error'];
            }
            $data_parameters = $this->getFrontParams($resolved_agent);
            $access_for_guest = isset($data_parameters['access_for_guests'])?(int)$data_parameters['access_for_guests']:1;
            $user_id = get_current_user_id();
            $content = '';
            if($access_for_guest < 1 && $user_id < 1){
                return $content;
            }
            
            $data_par = htmlspecialchars( wp_json_encode( $data_parameters ), ENT_QUOTES, 'UTF-8' );
            $data_parameters['agent_id'] = !isset($data_parameters['agent_id'])?$attr_agent_id:$data_parameters['agent_id'];
            $data_parameters['view_mode'] = $this->getViewMode($atts);

            $view = $this->getView($resolved_agent, $atts);
            switch($resolved_agent['provider']){
                case 'pinecone':
                        $data_parameters['agent_view'] = 1;
                        if($view !== 'default'){
                            $content = $this->showView($view, $data_par, $data_parameters);
                        }else{
                            $content = $this->showClassicDefaultSearch($data_par,$data_parameters);
                        }
                    break;
  
                default:
                    
                            $data_parameters['agent_view'] = 1;
                            $content = $this->showClassicDefaultSearch($data_par,$data_parameters);

            }
            $fcontent = apply_filters( 'oc3sengine_draw_agent',$content, $data_par,$data_parameters );
            return $fcontent;

        }
        
        public function showClassicDefaultSearch($data_par,$data_parameters){
            if (!class_exists('Oc3Sengine_SearchAgentClassicDefaultView')) {
                                $classview_path = OC3SENGINE_PATH . "/views/frontend/agents/SearchAgentClassicDefaultView.php";
                                include_once $classview_path;
                            }
                            $this->view = new Oc3Sengine_SearchAgentClassicDefaultView();
                return  $this->view->render($data_par,$data_parameters);
        }
        
        public function showView($view,$data_par,$data_parameters){
            $view_class = 'Oc3Sengine_SearchAgent'.$view.'View';
            if (!class_exists($view_class)) {
                                $classview_path = OC3SENGINE_PATH . "/views/frontend/agents/SearchAgent".ucfirst($view)."View.php";
                                include_once $classview_path;
                            }
                $this->view = new $view_class();
                return    $this->view->render($data_par,$data_parameters);
        }
        
        public function getView($resolved_agent,$atts){

           if(is_array($atts) && isset($atts['view'])){
               $file_suffix = sanitize_text_field($atts['view']);
               $view_file =  OC3SENGINE_PATH . "/views/frontend/agents/SearchAgent".ucfirst($file_suffix)."View.php";
               if(file_exists($view_file)){
                   return ucfirst($file_suffix);
               }
           }
           if(is_array($resolved_agent) && isset($resolved_agent['view'])){
               return $resolved_agent['view'];
           }
           return 'default';
        }
        
        public function getViewMode($atts){//modification of view which can be defined in attributes
            return 0;
        }
        
        public function getFrontParams($resolved_agent){
            if(is_object($resolved_agent) && isset($resolved_agent->agent_options) && is_array($resolved_agent->agent_options)){
                return $resolved_agent->agent_options;
            }
            $this->load_model('SearchModel');
            $model = $this->model;
            $default_settings = $model->getSearchAgentSettings('default');
            if(is_object($default_settings) && isset($default_settings->agent_options) && is_array($default_settings->agent_options)){
                return $default_settings->agent_options;
            }
            return [
                'background_color'=>'#ffffff',
                'border_color'=>'#000000',
                'font_color'=>'#000000',
                'font_size'=>12,
                'search_button_color'=>'#0E5381',
                'search_button_text_color'=>'ffffff',
                
                'search_box_width'=> 25,
                'search_box_width_metrics'=>'%',
                'search_box_height'=>15,
                'search_box_height_metrics'=>'px',
             
                'number_results'=>3,
                'results_font_size'=>10,
                'results_font_color'=>'#000000',
                'results_background_color'=>'#000000',
                'access_for_guests' => 1,
                'view' => 'default',
                ];
        }

        public function getAgentInfo( $agent_id ){// id of agent which starts from default is for default modes 
            //++++++++++ need to get info from database
            $agent = $this->getAgentByHash($agent_id);
            $provider = $this->getSearchProvider($agent);
            $view = $this->getSearchView($agent);

            return apply_filters('oc3sengine_get_agent_info',[ 
                    'provider' => $provider,
                    'view' => $view, 
                    'id' => $agent_id, 
                    'custom' => 0,
                    'agent'=>$agent]);
        }
        
        public function getSearchView($agent){
            if(is_object($agent) && isset($agent->agent_options) && is_array($agent->agent_options) && isset($agent->agent_options['view']) && strlen($agent->agent_options['view']) > 0 ){
                return sanitize_text_field($agent->agent_options['view']);
            }
            return 'default';
        }
        
        public function getSearchProvider($agent){
            return 'pinecone';
        }
        
        public function getAgentByHash($agent_hash = ''){
            if($this->agent == false){
                if (!class_exists('Oc3Sengine_SearchModel')) {
                    require_once OC3SENGINE_PATH . '/lib/models/SearchModel.php';
                }
                $ch_model = new Oc3Sengine_SearchModel();
                $chb = $ch_model->getSearchAgentSettings($agent_hash);
                if(is_object($chb) && isset($chb->id) && $chb->id > 0){
                    $this->id_agent = (int)$chb->id;
                    $this->agent = $chb;
                    return $this->agent;
                }
                
            }
            return $this->agent;
        }
        
        
  
    }

}
