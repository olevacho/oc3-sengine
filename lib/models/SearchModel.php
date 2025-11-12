<?php

if (!defined('ABSPATH'))
    exit;

if (!class_exists('Oc3Sengine_SearchModel')) {

    class Oc3Sengine_SearchModel {
        
        
        public function storeSearchOptions($search_hash = '',$new_agent = []){
            $current_search = $this->getSearchAgentSettings($search_hash);
            if (is_object($current_search) && isset($current_search->id) && $current_search->id > 0) {
                $res = $this->updateSearchOptions($search_hash, $new_agent, $current_search->agent_options);
            } else {
                $res = $this->insertSearchOptions($search_hash, $new_agent);
            }
            return $res !== false;
        }

        
        public function insertSearchOptions($search_hash = '', $data = []) {

            global $wpdb;
            $botprovider = (int)$data['id_type'];
            unset($data['id_type']);
            $encoded = wp_json_encode($data);
            $wpdb->insert(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery */
                        $wpdb->prefix . 'oc3sengine_search_agents',
                    array(
                        'hash_code' => $search_hash,
                        'agent_options' => $encoded,
                        'id_type' => $botprovider
                    ),
                    array('%s', '%s','%d')
            );

            return $wpdb->insert_id;
        }

        public function updateSearchOptions($search_hash = '', $data = [], $old_data = []) {

            global $wpdb;
            $donottouched = [];

            foreach ($old_data as $key => $value) {
                if (!array_key_exists($key, $data)) {
                    $donottouched[$key] = $value;
                }
            }
            $new = array_merge($donottouched, $data);
            $encoded = wp_json_encode($new);
            $res = $wpdb->update(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery */
                        $wpdb->prefix . 'oc3sengine_search_agents',
                            array(
                                'agent_options' => $encoded),
                            array('hash_code' => $search_hash),
                            array('%s'),
                            array('%s'));
            return $res;
        }
        
        public function getSearchAgentSettings($search_hash = '') {
            
            global $wpdb;
            $row = $wpdb->get_row(/* phpcs:ignore WordPress.DB.DirectDatabaseQuery */
                        $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "oc3sengine_search_agents WHERE hash_code LIKE %s  ", [$search_hash]));
            if (is_object($row) && isset($row->agent_options) && strlen($row->agent_options) > 1 && $row->agent_options != null) {
                $row->agent_options = json_decode($row->agent_options);
                $agent_options = $row->agent_options;
                $new_agent_options = [];
                foreach ($agent_options as $idx => $b_opt) {

                    $new_agent_options[sanitize_text_field($idx)] = sanitize_text_field($b_opt);
                }
                $row->agent_options = $new_agent_options;
                
            } else {
                $row = new stdClass();
                $row->agent_options = [];
                $row->title = '';
                $row->id = 0;
                $row->hash_code = '';
                $row->id_type = '';
                
            }

            return $row;
        }
        
        
        
    }

}