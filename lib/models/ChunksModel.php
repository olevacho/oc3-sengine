<?php

if (!defined('ABSPATH'))
    exit;

if (!class_exists('Oc3Sengine_ChunksModel')) {

    class Oc3Sengine_ChunksModel {
        
        
        public function searchChunks( $srch = '', $page = 1, $records_per_page = 20, $indexed = true){
            
            global $wpdb;
            $par_arr = [0,100];

            $search_present = false;
            //$indexed is passed
            $limit_part_present = false;
            if (strlen($srch) > 0) {
                $search_present = true;
                $search = sanitize_text_field($srch);
            }
            if($records_per_page > 0 && $page > 0){
                $limit_part_present = true;
                $par_arr[0] = ($page - 1) * $records_per_page; 
                $par_arr[1] =  $records_per_page;
            }
            
            if($search_present && $limit_part_present && $indexed){
                $cnt = $wpdb->get_var(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery*/
                            $wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->prefix . "oc3sengine_chunks as a "
                        
                        . " WHERE  a.isdel = 0  AND  (a.title LIKE %s OR a.addinfo LIKE %s ) "
                        . "  AND embedded_result = 200  "  ,  ['%' . $search . '%','%' . $search . '%']));
                
                $rows = $wpdb->get_results(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery*/
                            $wpdb->prepare("SELECT * FROM " . 
                        $wpdb->prefix . "oc3sengine_chunks  as a "
                       
                        . " WHERE   a.isdel = 0  AND  (a.title LIKE %s OR a.addinfo LIKE %s )"
                        . "  AND embedded_result = 200 ORDER BY a.ID   LIMIT  %d,%d  " , ['%' . $search . '%','%' . $search . '%',
                            $par_arr[0],$par_arr[1]]));
                
            }elseif($search_present && $limit_part_present){
                $cnt = $wpdb->get_var(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery*/
                            $wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->prefix . "oc3sengine_chunks as a "
                        
                        . " WHERE  a.isdel = 0  AND  (a.title LIKE %s OR a.addinfo LIKE %s ) "
                        . "   "  ,  ['%' . $search . '%','%' . $search . '%']));
                
                $rows = $wpdb->get_results(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery*/
                            $wpdb->prepare("SELECT * FROM " . 
                        $wpdb->prefix . "oc3sengine_chunks  as a "
                       
                        . " WHERE   a.isdel = 0  AND  (a.title LIKE %s OR a.addinfo LIKE %s )"
                        . "   ORDER BY a.ID   LIMIT  %d,%d  " , ['%' . $search . '%','%' . $search . '%',
                            $par_arr[0],$par_arr[1]]));
            }elseif($indexed && $limit_part_present){
                $cnt = $wpdb->get_var(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery*/
                            $wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->prefix . "oc3sengine_chunks as a "
                        
                        . " WHERE %d AND a.isdel = 0   "
                        . "  AND embedded_result = 200  "  ,  [1]));
                
                $rows = $wpdb->get_results(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery*/
                            $wpdb->prepare("SELECT * FROM " . 
                        $wpdb->prefix . "oc3sengine_chunks  as a "
                       
                        . " WHERE   a.isdel = 0  "
                        . "  AND embedded_result = 200 ORDER BY a.ID   LIMIT  %d,%d  " , [$par_arr[0],$par_arr[1]]));
            }elseif($limit_part_present){
                $cnt = $wpdb->get_var(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery*/
                            $wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->prefix . "oc3sengine_chunks as a "
                        
                        . " WHERE %d AND a.isdel = 0   "
                        . "    "  ,  [1]));
                
                $rows = $wpdb->get_results(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery*/
                            $wpdb->prepare("SELECT * FROM " . 
                        $wpdb->prefix . "oc3sengine_chunks  as a "
                       
                        . " WHERE   a.isdel = 0  "
                        . "   ORDER BY a.ID   LIMIT  %d,%d  " , [$par_arr[0],$par_arr[1]]));
            }else{
                $cnt = $wpdb->get_var(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery*/
                            $wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->prefix . "oc3sengine_chunks as a "
                        
                        . " WHERE %d AND a.isdel = 0   "
                        . "    "  ,  [1]));
                
                $rows = $wpdb->get_results(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery*/
                            $wpdb->prepare("SELECT * FROM " . 
                        $wpdb->prefix . "oc3sengine_chunks  as a "
                       
                        . " WHERE  %d AND  a.isdel = 0  "
                        . "   ORDER BY a.ID  " , [1]));
            }
            
            
            $new_rows = [];
            foreach($rows as $rv){
                $row = new stdClass();

                $row->id = (int)$rv->id;
                $row->typeof_chunk = (int)$rv->typeof_chunk;
                if($row->typeof_chunk == 2){
                    $row->source_type = __('Manual', 'oc3-sengine');
                }else{
                    $row->source_type = __('Post/Page', 'oc3-sengine').':'.$rv->id_sourcepost;
                }
                $row->id_sourcepost = (int)$rv->id_sourcepost;
                $row->embedded_result = (int)$rv->embedded_result;
                
                $row->title = sanitize_textarea_field($rv->title);
                $row->database = sanitize_text_field($rv->database);
                $row->project = sanitize_text_field($rv->project);
                $row->dbindex = sanitize_text_field($rv->dbindex);
                $row->dbnamespace = sanitize_text_field($rv->dbnamespace);
                $row->embedding_ai = sanitize_text_field($rv->embedding_ai);
                $row->embedding_model = sanitize_text_field($rv->embedding_model);
                $row->embedding_cost = sanitize_text_field($rv->embedding_cost);
                $row->embedding_tokens = sanitize_text_field($rv->embedding_tokens);
                $row->addinfo = sanitize_text_field($rv->addinfo);
                $row->dateupdated = sanitize_text_field($rv->dateupdated);
                $row->isdel = (int)$rv->isdel;
                $row->details = $this->formatDetailsColumn($rv);
                
                $new_rows[(int)$rv->id] = $row;
                
            }
            return ['cnt' => $cnt, 'rows' => $new_rows];
        }


        public function formatDetailsColumn($row){
            $details = '';
            if (isset($row->database) && strlen($row->database) > 0) {
                $details .= esc_html__('Db', 'oc3-sengine') . ':' . $row->database . '<br>';
            }
            if (isset($row->project) && strlen($row->project) > 0) {
                $details .= esc_html__('Project', 'oc3-sengine') . ':' . $row->project . '<br>';
            }
            if (isset($row->dbindex) && strlen($row->dbindex) > 0) {
                $details .= esc_html__('Index', 'oc3-sengine') . ':' . $row->dbindex . '<br>';
            }
            if (isset($row->embedding_model) && strlen($row->embedding_model) > 0) {
                $details .= esc_html__('Model', 'oc3-sengine') . ':' . $row->embedding_model . '<br>';
            }
            if (isset($row->embedding_ai) && strlen($row->embedding_ai) > 0) {
                $details .= esc_html__('AI', 'oc3-sengine') . ':' . $row->embedding_ai . '<br>';
            }

            if (isset($row->embedding_tokens) && strlen($row->embedding_tokens) > 0) {
                $details .= 'Tokens:' . $row->embedding_tokens . '<br>';
            }
            if (isset($row->embedding_cost) && strlen($row->embedding_cost) > 0) {
                $details .= 'Cost:' . $row->embedding_cost . '<br>';
            }

            return wp_kses($details, ['br' => []]);
        }
        
        
        public function addChunk($data){
            global $wpdb;
            /*
             * typeof_chunk
             * title
             * id_sourcepost
             * embedded_result
             * database
             * project
             * dbindex
             * embedding_ai
             * embedding_model
             * embedding_cost
             * embedding_tokens
             * chunk_content
             */
            
            global $wpdb;
            $wpdb->insert(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery*/
                        $wpdb->prefix . 'oc3sengine_chunks',
                    array(
                        'typeof_chunk' => (int)$data['typeof_chunk'],
                        'title' => $data['title'],
                        'id_sourcepost' => (int)$data['id_sourcepost'],
                        'embedded_result' => (int)$data['embedded_result'],
                        'database' => $data['database'],
                        'project' => $data['project'],
                        'dbindex' => $data['dbindex'],
                        'embedding_ai' => $data['embedding_ai'],
                        'embedding_model' => $data['embedding_model'],
                        'embedding_cost' => $data['embedding_cost'],
                        'embedding_tokens' => $data['embedding_tokens'],
                        'chunk_content' => $data['chunk_content'],
                        'dateupdated' => gmdate( 'Y-m-d H:i:s' )
                    ),
                    array('%d', '%s','%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s'
                        , '%s', '%s', '%s')
            );
           
            return $wpdb->insert_id;
        }
        
        public function updateEmbeddingResult($id = 0,$emb_res = 0){
            global $wpdb;

            return $wpdb->update(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery*/
                        $wpdb->prefix . 'oc3sengine_chunks', 
                    array(
                        'embedded_result' => (int)$emb_res,
                        'dateupdated' => gmdate( 'Y-m-d H:i:s' )
                        ), 
                    array('id' => (int)$id),
                    array('%d','%s'),
                    array('%d'));
        }
        
        
        public function getChunksByPost($id_post){
            global $wpdb;
            
            $cnt = (int)$wpdb->get_var(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery*/
                        $wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->prefix . "oc3sengine_chunks as a "
                        . " WHERE 1  AND a.id_sourcepost = %d  AND isdel = 0 "  ,  (int)$id_post));
            
            $rows = $wpdb->get_results(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery*/
                        $wpdb->prepare("SELECT *  FROM " . $wpdb->prefix . "oc3sengine_chunks  as a "
                        . " WHERE 1  AND a.id_sourcepost = %d AND isdel = 0 "  ,  (int)$id_post));
            
            $new_rows = [];
            foreach($rows as $rv){
                
                $row = new stdClass();

                $row->id = (int)$rv->id;
                $row->typeof_chunk = (int)$rv->typeof_chunk;

                $row->id_sourcepost = (int)$rv->id_sourcepost;
                $row->embedded_result = (int)$rv->embedded_result;
                
                $row->title = sanitize_textarea_field($rv->title);
                $row->database = sanitize_text_field($rv->database);
                $row->project = sanitize_text_field($rv->project);
                $row->dbindex = sanitize_text_field($rv->dbindex);
                $row->dbnamespace = sanitize_text_field($rv->dbnamespace);
                $row->embedding_ai = sanitize_text_field($rv->embedding_ai);
                $row->embedding_model = sanitize_text_field($rv->embedding_model);
                $row->embedding_cost = sanitize_text_field($rv->embedding_cost);
                $row->embedding_tokens = sanitize_text_field($rv->embedding_tokens);
                $row->addinfo = sanitize_text_field($rv->addinfo);
                $row->dateupdated = sanitize_text_field($rv->dateupdated);
                $row->isdel = (int)$rv->isdel;
                $new_rows[(int)$rv->id] = $row;
                
            }
            return ['cnt' => $cnt, 'rows' => $new_rows];
            
            
        }
        
        public function disableChunks($id_post,$change_by_source){
            $this->changeStatusChunks($id_post, 1,$change_by_source);
        }
        
        public function enableChunks($id_post,$change_by_source){
            $this->changeStatusChunks($id_post, 0,$change_by_source);
        }
        
        public function changeStatusChunks($id_post,$status,$change_by_source ) {

            global $wpdb;
            $statusi = $status == 1? (int)$status:0;
            $change_by = 'id_postchunk';
            if($change_by_source){
                $change_by = 'id_sourcepost';
            }
            $res = $wpdb->update(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery*/
                        $wpdb->prefix . 'oc3sengine_chunks',
                            array(
                                'isdel' => $statusi),
                            array($change_by => $id_post),
                            array('%d'),
                            array('%d'));
            return $res;
        }
        
        public function changeStatusChunk($id_chunk,$status ) {

            global $wpdb;
            $statusi = $status == 1? (int)$status:0;

            $res = $wpdb->update(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery*/
                        $wpdb->prefix . 'oc3sengine_chunks',
                            array(
                                'isdel' => $statusi),
                            array('id' => $id_chunk),
                            array('%d'),
                            array('%d'));
            return $res;
        }
        
        public function disableChunk($id_chunk ) {
            return $this->changeStatusChunk($id_chunk, 1);
        }
        
        public function enableChunk($id_chunk ) {
            return $this->changeStatusChunk($id_chunk, 0);
        }
        
        public function getChunkById($id){
            global $wpdb;
            

            $rows = $wpdb->get_results(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery*/
                        $wpdb->prepare("SELECT *  FROM " . $wpdb->prefix . "oc3sengine_chunks  as a "
                        . " WHERE 1  AND a.id = %d "  ,  (int)$id));
            
            $row = new stdClass();
            $row->id = 0;
            
            foreach($rows as $rv){
                
                

                $row->id = (int)$rv->id;
                $row->typeof_chunk = (int)$rv->typeof_chunk;

                $row->id_sourcepost = (int)$rv->id_sourcepost;
                $row->embedded_result = (int)$rv->embedded_result;
                
                $row->title = sanitize_textarea_field($rv->title);
                $row->database = sanitize_text_field($rv->database);
                $row->project = sanitize_text_field($rv->project);
                $row->dbindex = sanitize_text_field($rv->dbindex);
                $row->dbnamespace = sanitize_text_field($rv->dbnamespace);
                $row->embedding_ai = sanitize_text_field($rv->embedding_ai);
                $row->embedding_model = sanitize_text_field($rv->embedding_model);
                $row->embedding_cost = sanitize_text_field($rv->embedding_cost);
                $row->embedding_tokens = sanitize_text_field($rv->embedding_tokens);
                $row->addinfo = sanitize_text_field($rv->addinfo);
                $row->dateupdated = sanitize_text_field($rv->dateupdated);
                $row->isdel = (int)$rv->isdel;
                $row->chunk_content = sanitize_text_field($rv->chunk_content);
                break;
                
            }
            return $row;
            
            
        }
        public function deleteChunk($id_chunk){
            global $wpdb;

            $table = $wpdb->prefix . 'oc3sengine_chunks';
            return $wpdb->delete(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery*/
                        $table, array( 'id' => $id_chunk ),array('%d') );
        }
        
        public function getChunksCount($indexed = true){
            
            global $wpdb;
            if($indexed){
                $cnt = $wpdb->get_var(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery*/
                        $wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->prefix . "oc3sengine_chunks as a "
                        . "     WHERE %d   AND a.isdel = 0  AND embedded_result = 200   "  ,[1]));
            }else{
                $cnt = $wpdb->get_var(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery*/
                        $wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->prefix . "oc3sengine_chunks as a "
                        . "     WHERE %d   AND a.isdel = 0    "  ,[1]));
            }
            
            return (int)$cnt;
            
        }
        
        
        
        
    }

}