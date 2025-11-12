<?php
if ( ! defined( 'ABSPATH' ) ) exit;



if (!class_exists('Oc3Sengine_AdminConfigController')) {

    class Oc3Sengine_AdminConfigController extends Oc3Sengine_BaseController {
        public $wp_nonce = '';
        private $source_rows = null;
        private $chunk_rows = null;
        


        
        public function __construct() {
            
            add_action('wp_ajax_oc3se_store_general_tab', [$this, 'processSettingsSubmit']);
            add_action('wp_ajax_oc3se_gpt_conf_models', [$this, 'gptGetModels']);
            add_action('wp_ajax_oc3sengine_pinecone_indexes', [$this, 'processIndexesSubmit']);
            add_action('wp_ajax_oc3se_load_source', [$this, 'searchSourceRows']);
            add_action('wp_ajax_oc3sengine_embed', [$this, 'indexContent']);
            add_action('wp_ajax_oc3se_delete_indexed', [$this, 'deleteEmbeddings']);
            add_action('wp_ajax_oc3se_load_indexed', [$this, 'searchIndexedRows']);
            add_action('wp_ajax_oc3se_show_indexed_content', [$this, 'showIndexedContent']);
            
            //add_action('admin_enqueue_scripts', [$this, 'adminInlineJs']);
            add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
            add_action('admin_footer', [$this, 'adminInlineJs']);
            add_action( 'init', [$this, 'adminInit'] );
            
        }
        
        public function adminInit(){
            $this->wp_nonce = wp_create_nonce(OC3SENGINE_PREFIX_SHORT . 'gpt_confnonce');
        }
        
        public function adminEnqueueScripts() {
        $current_screen = get_current_screen();

        // Ensure this is the correct page
        if ($current_screen->id !== 'toplevel_page_oc3sengine_settings') {
            return;
        }

        // Enqueue a script as a placeholder
        wp_enqueue_script('oc3-sengine-config',OC3SENGINE_URL . '/views/resources/js/oc3-sengine-dummy.js', [], '1.0.0', true);
    }

    public function generateJsData() {
        $current_screen = get_current_screen();

        // Ensure this is the correct page
        if ($current_screen->id !== 'toplevel_page_oc3sengine_settings') {
            return;
        }

        // Generate the data dynamically
        $this->js_data = [
            'sourceRows' => ['row1', 'row2', 'row3'], // Replace with your actual data
            'indexedRows' => ['chunk1', 'chunk2'],    // Replace with your actual data
            'rowsOptions' => [
                'row_DellogNonce' => wp_create_nonce('delete_nonce_action')
            ]
        ];

        // Encode the data into JavaScript
        $inline_js = 'let oc3sengineData = ' . wp_json_encode($this->js_data, JSON_HEX_TAG) . ';';
        $inline_js .= "\nconsole.log(oc3sengineData);";

        // Add the inline script
        wp_add_inline_script('oc3-search', $inline_js);
    }
    
        function adminInlineJs(){ 
            
            $current_screen = get_current_screen();

            if ($current_screen->id !== 'toplevel_page_oc3sengine_settings') {
                return;
            }
            
            //wp_enqueue_script('oc3-sengine-config',OC3SENGINE_URL . '/views/resources/js/oc3-sengine-dummy.js', [], '1.0.0', true);
            
            $inline_js = "const oc3sengineajaxAction = '".esc_url(admin_url('admin-ajax.php'))."';\n".

                "let oc3se_gpt_confnonce = '".esc_html(wp_create_nonce('oc3se_gpt_confnonce'))."';\n".


                " let oc3sengine_instruction_table_height = 0;
                    jQuery(function () {
                        jQuery('#oc3sengine_configtabs').tabs({activate: function (event, ui) {
                            let oc3sengine_active_tab = jQuery('#oc3sengine_configtabs .oc3sengine_tab_panel:visible').attr('id');
                        }});
                });
                jQuery(document).ready(function () {

                });"
                    . ""
                    . "let oc3sengine_message_config_general_error = '".esc_html('There were errors during store configuration.', 'oc3-sengine')."';\n
    let oc3sengine_message_config_general_succes1 = '".esc_html('Configuration stored successfully.', 'oc3-sengine')."';\n

    function oc3sengineShowModels(e){
        e.preventDefault();
        jQuery( '#oc3sengine_configtabs' ).tabs({ active: 1 });
    }
    
    jQuery('.oc3sengine_sync_pinecone').click(function (event){ 
        event.preventDefault();
            let btn = jQuery(this);
            let oc3sengine_pinecone_key = jQuery('#oc3sengine_pinecone_key').val();
            let old_value = jQuery('.oc3sengine_pinecone_en').attr('old-value');
            if(oc3sengine_pinecone_key !== ''){
                jQuery.ajax({
                    url: 'https://api.pinecone.io/indexes',
                    headers: {'Api-Key': oc3sengine_pinecone_key},
                    dataType: 'json',
                    beforeSend: function (){
                        
                        btn.html('Syncing...');
                    },
                    success: function (res){
                        if(res.indexes && res.indexes.length){
                            let selectList = '<option value=\'\'>Select Index</option>';
                            let pinecone_indexes = [];

                            res.indexes.forEach(function(index){
                                let displayText = index.name + ' (' + index.dimension + ')';
                                selectList += '<option value=\'' + index.host + '\'' + (old_value === index.host ? ' selected' : '') + '>' + displayText + '</option>';
                                pinecone_indexes.push({ title: index.name, url: index.host, dimension: index.dimension });
                            });

                            jQuery('#oc3sengine_config_pinecone_index').html(selectList);

                            // Save formatted indexes to the database
                            
                            jQuery.post('".esc_url(admin_url('admin-ajax.php'))."', {
                                action: 'oc3sengine_pinecone_indexes',
                                oc3se_gpt_confnonce: '".esc_html($this->wp_nonce)."',
                                indexes: JSON.stringify(pinecone_indexes),
                                api_key: oc3sengine_pinecone_key
                            });
                            
                        }
                        btn.html('Sync Indexes');
                        
                    },
                    error: function (e){
                        btn.html('Sync Indexes');
                        
                        alert(e.responseText);
                    }
                });
            }
            else{
                alert('Please add Pinecone API key before start sync')
            }
        });";
            
$load_nonce = wp_create_nonce(OC3SENGINE_PREFIX_SHORT . 'datasource_loadnonce');
$wp_del_nonce = wp_create_nonce('oc3se_row_dellognonce');

$wp_embed_nonce = wp_create_nonce(OC3SENGINE_PREFIX_SHORT . 'datasource_embednonce');
            $inline_js .= $pinecone_script = "\n
                let oc3se_sources_list = null;
    let oc3sengine_edited_chatbot_id = '';
    let oc3se_indxed_list = null;
    
    let oc3sengine_source_rows = ".wp_json_encode($this->source_rows,JSON_HEX_TAG).";
    let oc3sengine_indexed_rows = ".wp_json_encode($this->chunk_rows,JSON_HEX_TAG).";
    let oc2sengine_error_1 = '".esc_html__('Unknown Index Error!', 'oc3-sengine')."';
    jQuery(document).ready(function () {

        oc3sengineRowsOptions['ajax_Action'] = oc3sengineajaxAction;
        oc3sengineRowsOptions['delete_RowsAction'] = '';
        oc3sengineRowsOptions['row_DellogNonce'] = '".esc_html($wp_del_nonce)."';
        oc3sengineRowsOptions['message_LogConfirmDelete'] = '';
        oc3sengineRowsOptions['table_Row_Href_Prefix'] = '';
        oc3sengineRowsOptions['row_Loadnonce'] = '".esc_html($load_nonce)."';
        oc3sengineRowsOptions['message_Update_Success'] = '".esc_html__('Bot updated successfully', 'oc3-sengine')."';
        oc3sengineRowsOptions['message_New_Success'] = '".esc_html__('Bot created successfully', 'oc3-sengine')."';
        oc3sengineRowsOptions['load_RowsAction'] = 'oc3se_load_source';
        oc3sengineRowsOptions['rows_PerPageId'] = '#rows_per_page';
        oc3sengineRowsOptions['row_PageId'] = '#oc3sengine_page';
        oc3sengineRowsOptions['table_ElementId'] = '#oc3sengine_sourceitems';
        oc3sengineRowsOptions['search_InputId'] = '#oc3sengine_search_source';
        oc3sengineRowsOptions['table_Container'] = '#oc3sengine_container2';
        oc3sengineRowsOptions['page_Numbers'] = '.page-numbers2src';
        oc3sengineRowsOptions['row_items'] = oc3sengine_source_rows;
        oc3sengineRowsOptions['app_suffix'] = '';
        oc3sengineRowsOptions['total_Rows'] = '.oc3sengine_totals';
        oc3sengineRowsOptions['post_type'] = document.querySelector('#oc3sengine_source_post_type').value;
        oc3sengineRowsOptions['index_action'] = 'oc3sengine_embed';
        oc3sengineRowsOptions['row_Embednonce'] = '".esc_html($wp_embed_nonce)."';
        oc3sengineRowsOptions['indexed_Manager'] = oc3se_indxed_list;
        oc3sengineRowsOptions['source_RowsProp'] = 'source_rows';
        oc3sengineRowsOptions['tbody_Element'] = 'oc3sengine-rows-list';
        oc3se_sources_list = new Oc3SengineSourceManager(oc3sengineRowsOptions);
        
        let oc3sengineIndexedOptions = {
            row_Loadnonce: '".esc_html($load_nonce)."',
            selected_Span: '',
            selected_Href:'',
            table_ElementId:'#oc3sengine_indexeditems',
            search_SubmitElementId:'#oc3sengine_search_submit2',
            message_LogConfirmDelete:'".esc_html__('Do you want to delete indexed chunk ?', 'oc3-sengine')."',
            row_DellogNonce:'".esc_html($wp_del_nonce)."',
            table_Row_Href_Prefix:'oc3se_indexedtblrow',
            row_PageId:'#oc3sengineidx_page',
            rows_PerPageId:'#idxrows_per_page',
            rows_ContainerId:'#oc3sengine_container3',
            search_InputId:'#oc3sengine_search_indexed',
            load_RowsAction:'oc3se_load_indexed',
            delete_RowsAction:'oc3se_delete_indexed',
            row_items:oc3sengine_indexed_rows,
            loader_Selector: null,
            ajax_Action: oc3sengineajaxAction,
            app_suffix:'',
            page_Numbers:'.page-numbers2src2',
            total_Rows: '.oc3sengine_totals2',
            source_RowsProp:'index_rows',
            tbody_Element:'oc3sengine-indexed-list'
        };          
        
        oc3se_indxed_list = new Oc3SengineIndexedManager(oc3sengineIndexedOptions);
        oc3se_sources_list.indexedManager = oc3se_indxed_list;
        });"
                    . "";
            
            wp_add_inline_script('oc3-sengine-config', $inline_js);
            
         }
         
        
        function searchIndexedRows(){
            $r = ['result' => 0, 'msg' => __('Unknow problem', 'oc3-sengine')];
            $nonce = 'loadnonce';
            $r = $this->verifyPostRequest($r, $nonce, 'oc3se_datasource_loadnonce');
            if ($r['result'] > 0) {
                wp_send_json($r);
                exit;
            }
            $verify_nonce = check_ajax_referer('oc3se_datasource_loadnonce', $nonce, false);
            if(!$verify_nonce){
                $result['msg'] = __('Wrong request', 'oc3-sengine');
                $result['result'] = 402;
                wp_send_json($result);
                exit;
            }
            $rows_per_page = isset($_POST['rows_per_page'])?(int)$_POST['rows_per_page']:20;
            $search = isset($_POST['search'])?sanitize_text_field(wp_unslash($_POST['search'])):'';
            $page = isset($_POST['page']) && ((int) $_POST['page']) > 0 ? (int) $_POST['page'] : 1;
            

            $this->load_model('ChunksModel');
            $model = $this->model;
            //var_dump($model);
            $rows = $model->searchChunks( $search, $page, $rows_per_page);
            
            //'cnt' => $cnt, 'rows' => $rows
            if(is_array($rows) && isset($rows['rows']) && isset($rows['cnt'])){
                $r['index_rows'] = $rows['rows'];
                $r['index_cnt'] = (int)$rows['cnt'];
                $r['result'] = 200;
                $r['msg'] = __('OK','oc3-sengine');
                $r['total'] = $r['index_cnt'];
                $r['page'] = $page;
                $r['rows_per_page'] = $rows_per_page;
                
            }else{
                $r['result'] = 500;
                $r['msg'] = __('Error','oc3-sengine');
            }
            
            wp_send_json($r);
            exit;
        }
        
        
        
        function searchSourceRows(){
            
            $r = ['result' => 0, 'msg' => __('Unknow problem', 'oc3-sengine')];
            $nonce = 'loadnonce';
            $r = $this->verifyPostRequest($r, $nonce, 'oc3se_datasource_loadnonce');
            if ($r['result'] > 0) {
                wp_send_json($r);
                exit;
            }
            $verify_nonce = check_ajax_referer('oc3se_datasource_loadnonce', $nonce, false);
            if(!$verify_nonce){
                $result['msg'] = __('Wrong request', 'oc3-sengine');
                $result['result'] = 402;
                wp_send_json($result);
                exit;
            }
            $rows_per_page = isset($_POST['rows_per_page'])?(int)$_POST['rows_per_page']:20;
            $search = isset($_POST['search'])?sanitize_text_field(wp_unslash($_POST['search'])):'';
            $page = isset($_POST['page']) && ((int) $_POST['page']) > 0 ? (int) $_POST['page'] : 1;
            

            $this->load_model('DataSourceModel');
            $model = $this->model;
            //var_dump($model);
            $posts = $model->searchPosts('', $search, $page, $rows_per_page);
            
            //'cnt' => $cnt, 'rows' => $rows
            if(is_array($posts) && isset($posts['rows']) && isset($posts['cnt'])){
                $r['source_rows'] = $posts['rows'];
                $r['source_cnt'] = (int)$posts['cnt'];
                $r['result'] = 200;
                $r['msg'] = __('OK','oc3-sengine');
                $r['total'] = $r['source_cnt'];
                $r['page'] = $page;
                $r['rows_per_page'] = $rows_per_page;
                
            }else{
                $r['result'] = 500;
                $r['msg'] = __('Error','oc3-sengine');
            }
            
            wp_send_json($r);
            exit;
        }
        
        function processIndexesSubmit(){
            if ((!isset($_SERVER['REQUEST_METHOD']) || 'POST' !== $_SERVER['REQUEST_METHOD'])) {
                return;
            }
            $r = ['result' => 0, 'msg' => __('Unknow problem','oc3-sengine')];
            $nonce =  'oc3se_gpt_confnonce';
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
            
            if (isset($_POST[ 'indexes'])) {
  
                $indexes = str_replace("\\",'',sanitize_text_field(wp_unslash($_POST['indexes'])));
                update_option(OC3SENGINE_PREFIX_LOW . 'config_pinecone_indexes', $indexes);

            }
            
            
            if (isset($_POST['api_key'])) {
                $api_key = sanitize_text_field(wp_unslash($_POST['api_key']));
                update_option(OC3SENGINE_PREFIX_LOW . 'pinecone_key', $api_key);
            }
            
            $r['result'] = 200;
            $r['msg'] = __('OK','oc3-sengine');
            wp_send_json($r);
            exit;
        }
        
        
        
        public function registerAdminMenu() {

            if (!Oc3Sengine_Utils::checkEditInstructionAccess()) {
                return false;
            }

            
            $settings_hook = add_submenu_page(OC3SENGINE_PREFIX_LOW . 'settings', __('Settings', 'oc3-sengine'), __('Settings', 'oc3-sengine'), 'edit_posts', OC3SENGINE_PREFIX_LOW . 'settings', [$this, 'showSettings']);
            
            add_action('load-' . $settings_hook, [$this, 'processSettingsSubmit']);
        }

        public function processSettingsSubmit() {

            if (!isset($_SERVER['REQUEST_METHOD']) || ('POST' !== $_SERVER['REQUEST_METHOD'])) {
                return;
            }

            $r = ['result' => 0, 'msg' => __('Unknow problem','oc3-sengine')];
            $nonce =  'oc3se_gpt_confnonce';
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

            if (isset($_POST['oc3sengine_open_ai_gpt_key'])) {
                $api_key = sanitize_text_field(wp_unslash($_POST['oc3sengine_open_ai_gpt_key']));
                update_option(OC3SENGINE_PREFIX_LOW . 'open_ai_gpt_key', $api_key);
            }
            
            if (isset($_POST['oc3sengine_config_embedding_context_not_found_msg'])) {
                $oc3sengine_config_embedding_context_not_found_msg = sanitize_text_field(wp_unslash($_POST['oc3sengine_config_embedding_context_not_found_msg']));
                update_option(OC3SENGINE_PREFIX_LOW . 'config_embedding_context_not_found_msg', $oc3sengine_config_embedding_context_not_found_msg);
            }
            
            
            
            
            if (isset($_POST[OC3SENGINE_PREFIX_LOW . 'response_timeout'])) {
                $response_timeout = (int) $_POST[OC3SENGINE_PREFIX_LOW . 'response_timeout'];
                if($response_timeout <= 0){
                    $response_timeout = 50;
                }
                if($response_timeout >300){
                    $response_timeout = 200;
                }
                update_option(OC3SENGINE_PREFIX_LOW . 'response_timeout', $response_timeout);
            }

            if (isset($_POST[OC3SENGINE_PREFIX_LOW . 'max_tokens'])) {
                $max_tokens = (int) $_POST[OC3SENGINE_PREFIX_LOW . 'max_tokens'];
                if($max_tokens <= 0){
                    $max_tokens = 1024;
                }
                update_option(OC3SENGINE_PREFIX_LOW . 'max_tokens', $max_tokens);
            }

            if (isset($_POST[OC3SENGINE_PREFIX_LOW . 'prompt_model'])) {
                $model = sanitize_text_field(wp_unslash($_POST[OC3SENGINE_PREFIX_LOW . 'prompt_model']));

                update_option(OC3SENGINE_PREFIX_LOW . 'prompt_model', $model);
            }
            
            if (isset($_POST['oc3sengine_config_temperature'])) {
                $temperature = sanitize_text_field(wp_unslash($_POST[ 'oc3sengine_config_temperature']));
                update_option(OC3SENGINE_PREFIX_LOW . 'temperature', $temperature);
            }
            //
            //oc3sengine_config_frequency_penalty
            if (isset($_POST['oc3sengine_config_frequency_penalty'])) {
                $frequency_penalty = sanitize_text_field(wp_unslash($_POST[ 'oc3sengine_config_frequency_penalty']));
                update_option(OC3SENGINE_PREFIX_LOW . 'frequency_penalty', $frequency_penalty);
            }
            

            if (isset($_POST['oc3sengine_config_presence_penalty'])) {
                $presence_penalty = sanitize_text_field(wp_unslash($_POST[ 'oc3sengine_config_presence_penalty']));
                update_option(OC3SENGINE_PREFIX_LOW . 'presence_penalty', $presence_penalty);
            }
            
            if (isset($_POST[OC3SENGINE_PREFIX_LOW . 'pinecone_key'])) {
                $oc3sengine_pinecone_key = sanitize_text_field(wp_unslash($_POST[OC3SENGINE_PREFIX_LOW . 'pinecone_key']));
                update_option(OC3SENGINE_PREFIX_LOW . 'pinecone_key', $oc3sengine_pinecone_key);
            }
            
            if (isset($_POST[OC3SENGINE_PREFIX_LOW . 'config_emb_model'])) {
                $config_emb_model = sanitize_text_field(wp_unslash($_POST[OC3SENGINE_PREFIX_LOW . 'config_emb_model']));
                update_option(OC3SENGINE_PREFIX_LOW . 'config_emb_model', $config_emb_model);
            }
            
            if (isset($_POST[OC3SENGINE_PREFIX_LOW . 'config_pinecone_index'])) {
                $oc3sengine_config_pinecone_index = sanitize_text_field(wp_unslash($_POST[OC3SENGINE_PREFIX_LOW . 'config_pinecone_index']));
                update_option(OC3SENGINE_PREFIX_LOW . 'config_pinecone_index', $oc3sengine_config_pinecone_index);
            }
            
            if (isset($_POST[OC3SENGINE_PREFIX_LOW . 'config_pinecone_index_title'])) {
                $config_pinecone_index_title = sanitize_text_field(wp_unslash($_POST[OC3SENGINE_PREFIX_LOW . 'config_pinecone_index_title']));
                update_option(OC3SENGINE_PREFIX_LOW . 'config_pinecone_index_title', $config_pinecone_index_title);
            }
            
            if (isset($_POST[OC3SENGINE_PREFIX_LOW . 'config_pinecone_confidence'])) {
                $config_pinecone_confidence = (int)$_POST[OC3SENGINE_PREFIX_LOW . 'config_pinecone_confidence'];
                update_option(OC3SENGINE_PREFIX_LOW . 'config_pinecone_confidence', $config_pinecone_confidence);
            }
            
            if (isset($_POST[OC3SENGINE_PREFIX_LOW . 'config_pinecone_topk'])) {
                $config_pinecone_topk = (int)$_POST[OC3SENGINE_PREFIX_LOW . 'config_pinecone_topk'];
                update_option(OC3SENGINE_PREFIX_LOW . 'config_pinecone_topk', $config_pinecone_topk);
            }
            
            
            $r['result'] = 200;
            $r['msg'] = __('OK','oc3-sengine');
            wp_send_json($r);
            exit;
        }


        public function showSettings() {

            if (!Oc3Sengine_Utils::checkEditInstructionAccess()) {
                return;
            }
            
            $oc3sengine_open_ai_gpt_key = get_option(OC3SENGINE_PREFIX_LOW . 'open_ai_gpt_key', '');
            $oc3sengine_pinecone_key = get_option(OC3SENGINE_PREFIX_LOW . 'opinecone_key', '');

            $this->load_model('DataSourceModel');
            $model = $this->model;
            //var_dump($model);
            $posts = $model->searchPosts('', '', 1, 20);
            
            $this->load_model('ChunksModel');
            $model = $this->model;
            $chunks = $model->searchChunks( '', 1, 20);
            //var_dump($chunks);
            $conf_contr = $this;
            $conf_contr->load_view('backend/config', ['oc3sengine_open_ai_gpt_key' => $oc3sengine_open_ai_gpt_key,
            'oc3sengine_pinecone_key' => $oc3sengine_pinecone_key,
                'source_rows' => $posts['rows'],'source_cnt'=>$posts['cnt'],
                'chunk_rows' => $chunks['rows'],'chunk_cnt'=>$chunks['cnt'],
                'wp_nonce'=>$this->wp_nonce]);
            
            $conf_contr->render();
        }



        function parseModelResponseObject($model) {
            
            $model_id = isset($model->id)?sanitize_text_field($model->id):'';
            
            return ['id' => $model_id, 'textmodel' => strlen($model->id) > 0];
            
        }
        
        public function gptGetModels() {

            $r = ['result' => 0, 'msg' => __('Unknow problem','oc3-sengine')];

            $nonce = 'oc3se_gpt_confnonce';
            $r = $this->verifyPostRequest($r, $nonce, $nonce);
            if ($r['result'] > 1) {
                wp_send_json($r);
                exit;
            }

            $user_can_chat_gpt = Oc3Sengine_Utils::checkEditInstructionAccess();
            if (!$user_can_chat_gpt) {
                $r['result'] = 4;
                $r['msg'] = __('Access denied','oc3-sengine');
                wp_send_json($r);
            }


            $openai = Oc3Sengine_Utils::getAIProvider('openai');
            
            $response = $openai->getFromUrl('https://api.openai.com/v1/models');
            if ($response[0] != 1) {
                $r['result'] = 401;
                $r['msg'] = __('Network error','oc3-sengine');

            } else {
                $resp = json_decode($response[1]);
                $umodels = unserialize(get_option('oc3sengine_chatgpt_models', []));
                $local_models = Oc3Sengine_Utils::sanitizeArrayModels($umodels);


                if (isset($resp->data) && is_array($resp->data)) {
                    $models = $resp->data;

                    foreach ($models as $model) {
                        $parsed_model = $this->parseModelResponseObject($model);

                        if (!array_key_exists($parsed_model['id'], $local_models)) {
                            if ($parsed_model['textmodel']) {
                                $local_models[$parsed_model['id']] = 2;
                            } else {
                                $local_models[$parsed_model['id']] = 0;
                            }
                        }

                    }
                }

                $r['result'] = 200;
                $r['msg'] = 'OK';
                $r['models'] = $local_models;


                update_option('oc3sengine_chatgpt_models', serialize($local_models));

            }

            wp_send_json($r);
            exit;
        }
        
        function filterDbProvider($provider){
            return sanitize_text_field($provider);
        }
        
        function indexContent(){
            
            $result = array('result' => 0,'msg' => esc_html__('Unknown issue','oc3-sengine'));
            if(!current_user_can('manage_options')){
                $result['result'] = 403;
                $result['msg'] = esc_html__('Access denied.','oc3-sengine');
                wp_send_json($result);
            }
            $nonce_ = isset($_POST['oc3se_embednonce'])? sanitize_text_field(wp_unslash($_POST['oc3se_embednonce'])):'';
            if ( ! wp_verify_nonce( $nonce_, 'oc3se_datasource_embednonce' ) ) {
                $result['result'] = 401;
                $result['msg'] = esc_html__('Security issue','oc3-sengine');
                wp_send_json($result);
            }
            if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
                $id = (int)$_REQUEST['id'];
                $vector_db_provider = isset($_POST['vector_provider'])?$this->filterDbProvider(sanitize_text_field(wp_unslash($_POST['vector_provider']))):'pinecone';
                $post_data = get_post($id);
                if($post_data){
                    $rescode = $this->indexData($post_data,$vector_db_provider);
                    if($rescode == 'success'){
                        $result['result'] = 200;
                        $result['msg'] = esc_html__('Content indexed successfully','oc3-sengine');
                    }
                    else{
                        $result['result'] = 500;
                        $result['msg'] = $rescode;
                    }
                }
                else{
                    $result['msg'] = esc_html__('Data not found','oc3-sengine');
                }
            }
            wp_send_json($result);
        }
        
        public function indexData($post_data,$vector_db_provider)
        {

            $oc3sengine_provider = get_option('oc3sengine_provider', 'OpenAI');
            $oc3sengine_content = $post_data->post_content;
            preg_match_all('@\[([^<>&/\[\]\x00-\x20=]++)@', $oc3sengine_content, $matches);
            if ($matches && is_array($matches) && count($matches)) {
                $pattern = get_shortcode_regex($matches[1]);
                $oc3sengine_content = preg_replace_callback("/$pattern/", 'strip_shortcode_tag', $oc3sengine_content);
            }
            $oc3sengine_content = trim($oc3sengine_content);
            $oc3sengine_content = preg_replace("/<((?:style)).*>.*<\/style>/si", ' ',$oc3sengine_content);
            $oc3sengine_content = preg_replace("/<((?:script)).*>.*<\/script>/si", ' ',$oc3sengine_content);
            $oc3sengine_content = preg_replace('/<a(.*)href="([^"]*)"(.*)>(.*?)<\/a>/i', '$2', $oc3sengine_content);
            $oc3sengine_content = wp_strip_all_tags($oc3sengine_content);
            $oc3sengine_content = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $oc3sengine_content);
            $oc3sengine_content = trim($oc3sengine_content);
            if (strlen($oc3sengine_content) <= 0) {
                update_post_meta($post_data->ID, 'oc3sengine_indexed', 0);
                return esc_html__('Empty content or shortcode','oc3-sengine');
            } else {
                $oc3sengine_old_builder = false;
                $this->load_model('ChunksModel');
                $model = $this->model;
                
                $indexed = $model->getChunksByPost($post_data->ID);
                
                if (isset($indexed['rows']) && is_array($indexed['rows']) && count($indexed['rows']) > 0 ) {
                    foreach($indexed['rows'] as $roww){
                        if($roww->embedded_result == 200){
                            return esc_html__('Post ID:','oc3-sengine').(int)$post_data->ID.' '.esc_html__('already indexed. Please delete old post first.','oc3-sengine');
                        }
                    }
                    
                }
                $oc3sengine_new_content = '';
                /*For Post*/
                if($post_data->post_type == 'post'){
                    $oc3sengine_new_content = esc_html__('Title','oc3-sengine').': '.$post_data->post_title."\n";
                    $oc3sengine_new_content .= esc_html__('Content','oc3-sengine').': '.$oc3sengine_content."\n";
                    $oc3sengine_new_content .= esc_html__('URL','oc3-sengine').': '.get_permalink($post_data->ID);
                    /*Categories*/
                    $categories_name = wp_get_post_categories($post_data->ID, array('fields' => 'names'));
                    if($categories_name && is_array($categories_name) && count($categories_name)){
                        $oc3sengine_new_content .= "\n".esc_html__('Categories','oc3-sengine').": ".implode(',',$categories_name);
                    }
                    $oc3sengine_content = $oc3sengine_new_content;
                }
                /*For Page*/
                if($post_data->post_type == 'page'){
                    $oc3sengine_new_content = esc_html__('Page Title','oc3-sengine').': '.$post_data->post_title."\n";
                    $oc3sengine_new_content .= esc_html__('Page Content','oc3-sengine').': '.$oc3sengine_content."\n";
                    $oc3sengine_new_content .= esc_html__('Page URL','oc3-sengine').': '.get_permalink($post_data->ID);
                    $oc3sengine_content = $oc3sengine_new_content;
                }
                
                
                $oc3sengine_result = $this->saveEmbedding($oc3sengine_content,  $post_data, $oc3sengine_old_builder, $vector_db_provider);
                if ($oc3sengine_result && is_array($oc3sengine_result) && isset($oc3sengine_result['status'])) {
                    if ($oc3sengine_result['status'] == 'error') {

                        
                        return $oc3sengine_result['msg'];
                    } else {

                        return 'success';
                    }
                } else {

                    return esc_html__('Something went wrong','oc3-sengine');
                }
            }
        }
        
        public function saveEmbedding($content,  $post = '', $embeddings_id = false, $vector_db_provider = 'pinecone')
        {
            global $wpdb;
            $this->load_model('ChunksModel');
            $model = $this->model;
            $oc3sengine_config_pinecone_index_title = sanitize_text_field(get_option(OC3SENGINE_PREFIX_LOW . 'config_pinecone_index_title', ''));
            $oc3sengine_config_pinecone_index = sanitize_text_field(get_option(OC3SENGINE_PREFIX_LOW . 'config_pinecone_index', ''));
            $result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','oc3-sengine'));
            $oc3se_provider = get_option('oc3sengine_provider', 'openai');
            $ai_name = 'openai';
            $openai = Oc3Sengine_Utils::getAIProvider($ai_name);
            

            $oc3se_main_embedding_model = get_option(OC3SENGINE_PREFIX_LOW . 'config_emb_model', '');//OpenAI:text-embedding-3-small
            

            $content = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $content);
            
            if($openai){

                // Prepare the API call parameters
                $apiParams = [
                    'input' => $content,
                    'model' => $oc3se_main_embedding_model
                ];

                // Make the API call
                $response = $openai->doEmbeddings($apiParams);
                $response = json_decode($response,true);
                if(isset($response['error']) && !empty($response['error'])) {
                    $result['msg'] = $response['error']['message'];
                    if(empty($result['msg']) && isset($response['error']['code']) && $response['error']['code'] == 'invalid_api_key'){
                        $result['msg'] = 'Incorrect API key provided. You can find your API key at https://platform.openai.com/account/api-keys.';
                    }
                }
                else{
                    $embedding = $response['data'][0]['embedding'];
                    if(empty($embedding)){
                        $result['msg'] = esc_html__('No data returned','oc3-sengine');
                    }
                    else{
                        
                        if(!$embeddings_id) {//insert chunk
                            $data_to_insert = [];
                            $data_to_insert['typeof_chunk'] = 1;//post or page
                            $embedding_title = empty($post->post_title) ? mb_substr($content,0,100,'utf-8') : mb_substr($post->post_title,0,100,'utf-8');
                            $data_to_insert['title'] = $embedding_title;//
                            $data_to_insert['chunk_content'] = sanitize_text_field($content);//
                            $data_to_insert['id_sourcepost'] = (int)$post->ID;//
                            $data_to_insert['typeof_chunk'] = 1;
                            $data_to_insert['embedded_result'] = 0;
                            $data_to_insert['database'] = sanitize_text_field($oc3se_provider);
                            $data_to_insert['project'] = $oc3sengine_config_pinecone_index_title;
                            $data_to_insert['dbindex'] = $oc3sengine_config_pinecone_index;
                            $data_to_insert['embedding_ai'] = $ai_name;
                            $data_to_insert['embedding_model'] = $oc3se_main_embedding_model;
                            $data_to_insert['embedding_tokens'] =  $response['usage']['total_tokens'];
                            if (!class_exists('Oc3Sengine_RagUtils')) {
                                require_once OC3SENGINE_PATH . '/lib/helpers/RagUtils.php';
                            }
                            $data_to_insert['embedding_cost'] =  Oc3Sengine_RagUtils::calculateEstimatedEmbeddingCost($data_to_insert['embedding_tokens'],$oc3se_main_embedding_model);
                            $embeddings_id = $model->addChunk($data_to_insert);
                            
                            $oc3se_emb_index = get_option('oc3se_pinecone_environment', '');
                            if ($vector_db_provider === 'qdrant') {
                                $oc3se_emb_index = get_option('oc3se_qdrant_default_collection', '');
                            }


                            if(!$embeddings_id > 0) {
                                $result['msg'] = 'Error inserting embedding info';
                                $result['status'] = 'error';
                                return $result;
                            }

                        }
                        
                        //update existing chunk
                           
                            $vectors = array(
                                array(
                                    'id' => (string)$embeddings_id,
                                    'values' => $embedding,
                                    'metadata' => [
                                        'title' => (string)$embedding_title,
                                        'id_post' => (string)$post->ID
                                    ]
                                )
                            );

                                $oc3se_pinecone_api = get_option('oc3sengine_pinecone_key','');
                                $oc3se_pinecone_environment = $oc3sengine_config_pinecone_index;
                                $headers = array(
                                    'Content-Type' => 'application/json',
                                    'Api-Key' => $oc3se_pinecone_api
                                );
                                //Test Pinecone 
                                $response = wp_remote_get('https://api.pinecone.io/indexes',array(
                                    'headers' => $headers
                                ));
                                if(is_wp_error($response)){
                                    $result['msg'] = $response->get_error_message();
                                    return $result;
                                }
                
                                $response_code = $response['response']['code'];
                                if($response_code !== 200){
                                    $result['msg'] = $response['response']['message'];
                                    return $result;
                                }
                                $pinecone_url = 'https://' . $oc3se_pinecone_environment . '/vectors/upsert';
                                $response = wp_remote_post($pinecone_url, array(
                                    'headers' => $headers,
                                    'body' => wp_json_encode(array('vectors' => $vectors)),
                                    'timeout' => 30
                                ));
                                if(is_wp_error($response)){
                                    $result['msg'] = $response->get_error_message();
                                    
                                }
                                else{
                                    $body = json_decode($response['body'],true); 
                                    if($body){
                                        if(isset($body['code']) && isset($body['message'])){
                                            $result['msg'] = wp_strip_all_tags($body['message']);
                                            $model->updateEmbeddingResult($embeddings_id,500);
                                        }
                                        else{
                                            $result['status'] = 'success';
                                            $result['id'] = $embeddings_id;
                                            $model->updateEmbeddingResult($embeddings_id,200);
                                            $result['msg'] = esc_html__('Content indexed','oc3-sengine');
                                        }
                                    }
                                    else{
                                        $result['msg'] = esc_html__('No data returned','oc3-sengine');
                                        $model->updateEmbeddingResult($embeddings_id,404);
                                    }
                                }

                        
                    }
                }
            }
            else{
                $result['msg'] = esc_html__('Missing API details.','oc3-sengine');
            }
            return $result;
        }
        
        public function deleteEmbeddings()
        {
            $result = array('status' => 'none','msg'=>esc_html__('Unknown reason','oc3-sengine'),'result'=>0,'del_row' => 0);
            if(!current_user_can('manage_options')){
                $result['status'] = 'error';
                $result['msg'] = esc_html__('Access denied.','oc3-sengine');
                $result['result'] = 403;
                wp_send_json($result);
            }
            $nonce_ = isset($_POST['oc3se_row_dellognonce'])? sanitize_text_field(wp_unslash($_POST['oc3se_row_dellognonce'])):'';
            if ( ! wp_verify_nonce( $nonce_, 'oc3se_row_dellognonce' ) ) {
                $result['status'] = 'error';
                $result['msg'] = esc_html__('Access denied. Code 2.','oc3-sengine');
                $result['result'] = 401;
                wp_send_json($result);
            }

            $id = isset($_REQUEST['id'])?sanitize_text_field(wp_unslash($_REQUEST['id'])):0;
            $del_res = $this->deleteEmbeddingItems([$id]);
            if($del_res == 1){
                $result['result'] = 200;
                $result['msg'] = esc_html__('Success','oc3-sengine');
                $result['del_row'] = $id;
            }
            wp_send_json($result);
        }

        
        
        public function deleteEmbeddingItems($ids)
        {

            $oc3se_pinecone_api = get_option('oc3sengine_pinecone_key', '');
            $this->load_model('ChunksModel');
            $model = $this->model;
            $oc3se_index = get_option(OC3SENGINE_PREFIX_LOW . 'config_pinecone_index', '');
            foreach ($ids as $id) {
                

                if (!empty($oc3se_index) || strpos($oc3se_index, 'pinecone.io') !== false) {
                    // Determine index host
                    
                    $index_host = $oc3se_index;
                    $index_host_url = 'https://' . $index_host . '/vectors/delete';

                    // Pinecone deletion logic
                    try {
                        $headers = array(
                            'Content-Type' => 'application/json',
                            'Api-Key' => $oc3se_pinecone_api
                        );
                        $body = wp_json_encode([
                            'deleteAll' => 'false',
                            'ids' => [$id]
                        ]);
                        $response = wp_remote_post($index_host_url, array(
                            'headers' => $headers,
                            'body' => $body,
                            'timeout' => 30
                        ));

                        if (is_wp_error($response)) {

                            //error_log(print_r($response, true));
                        }else{
                            return $model->disableChunk($id);
                        }
                    } catch (\Exception $exception) {
                        //error_log(print_r($exception->getMessage(), true));
                    }
                }

            }
        }
        
        public function showIndexedContent(){
            $result = array('result' => 0,'content' => '', 'msg'=>esc_html__('Unknown reason','oc3-sengine'),'result'=>0,'del_row' => 0);
            if(!current_user_can('manage_options')){

                $result['msg'] = esc_html__('Access denied.','oc3-sengine');
                $result['result'] = 403;
                wp_send_json($result);
            }
            $nonce_ = isset($_POST['oc3se_gpt_loadnonce'])? sanitize_text_field(wp_unslash($_POST['oc3se_gpt_loadnonce'])):'';
            
            if ( ! wp_verify_nonce( $nonce_, OC3SENGINE_PREFIX_SHORT.'datasource_loadnonce' ) ) {
                $result['status'] = 'error';
                $result['msg'] = esc_html__('Access denied. Code 2.','oc3-sengine');
                $result['result'] = 401;
                wp_send_json($result);
            }
            $id = isset($_REQUEST['chunk_id'])?sanitize_text_field(wp_unslash($_REQUEST['chunk_id'])):0;
            $this->load_model('ChunksModel');
            $model = $this->model;
            $chunk = $model->getChunkById($id);
            if (is_object($chunk) && isset($chunk->chunk_content)) {
                $result['content'] = $chunk->chunk_content;
                $result['result'] = 200;
            }
            wp_send_json($result);                        
        }
        
    }

}
