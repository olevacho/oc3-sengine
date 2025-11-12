<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('Oc3Sengine_UpdateUtils')) {

    class Oc3Sengine_UpdateUtils {
        
        
        public static function upgrade(){
            
            $current_db_version = get_option('oc3sengine_database_version', 0);
            if($current_db_version < 2){
                self::version2();
                update_option('oc3sengine_database_version', 2);
            }
            
            
        }
        
        public static function version2(){
            
            global $wpdb;
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            $charset_collate = $wpdb->get_charset_collate();
            
            $sql1 = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'oc3sengine_chunks' . '` (
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `typeof_chunk` int(11) NOT NULL DEFAULT "1"   ,
                `title`  varchar(500) NOT NULL DEFAULT "" ,
                `id_sourcepost` int(11) NOT NULL DEFAULT "0"  ,
                `embedded_result` int(11) NOT NULL DEFAULT "0" ,
                `database`  varchar(100) NOT NULL DEFAULT "" ,
                `project`  varchar(100) NOT NULL DEFAULT "" ,
                `dbindex`  varchar(200) NOT NULL DEFAULT "" ,
                `dbnamespace`  varchar(100) NOT NULL DEFAULT "" ,
                `embedding_ai`  varchar(100) NOT NULL DEFAULT "" ,
                `embedding_model`  varchar(100) NOT NULL DEFAULT "" ,
                `embedding_cost`  varchar(50) NOT NULL DEFAULT "" ,
                `embedding_tokens`  varchar(50) NOT NULL DEFAULT "" ,
                `addinfo`  varchar(500) NOT NULL DEFAULT "" ,
                `dateupdated`  varchar(50) NOT NULL DEFAULT "" ,
                `chunk_content`  text DEFAULT NULL ,
                `isdel`  SMALLINT NOT NULL DEFAULT "0"
                ) ENGINE = INNODB '. $charset_collate;
            dbDelta( $sql1 );
            
            $sql2 = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'oc3sengine_search_agents' . '` (
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `hash_code`  varchar(100) NOT NULL DEFAULT "" ,
                `title`  varchar(200) NOT NULL DEFAULT "" ,
                `id_type` int(11) NOT NULL DEFAULT "0"  ,
                `agent_options`  text DEFAULT NULL ,
                `isdel`  SMALLINT NOT NULL DEFAULT "0"
                ) ENGINE = INNODB '. $charset_collate;
            dbDelta( $sql2 );
            self::version2DataAdd();
            
            update_option( "oc3sengine_database_version", 2 );
                
        }
        
        public static function version2DataAdd(){
            global $wpdb;
            $default_option = [
                'background_color'=>'#ffffff',
                'border_color'=>'#000000',
                'font_color'=>'#000000',
                'font_size'=>14,
                'search_button_color'=>'#0E5381',
                'search_button_text_color'=>'ffffff',
                
                'search_box_width'=> 100,
                'search_box_width_metrics'=>'%',
                'search_box_height'=>0,
                'search_box_height_metrics'=>'px',
             
                'number_results'=>3,
                'results_font_size'=>10,
                'results_font_color'=>'#000000',
                'results_background_color'=>'#FFFFFF',
                'access_for_guests' => 1,
                'view' => 'default',
                'search_loader_color' => '#CCCCCC'
                ];
                    
            $wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
                    $wpdb->prefix . 'oc3sengine_search_agents', array(
                'hash_code' => 'default',
                'title' => 'default',
                'id_type'=> 1,
                'agent_options' => wp_json_encode($default_option)
            ),
            array( '%s', '%s', '%d', '%s'));// phpcs:ignore WordPress.DB.DirectDatabaseQuery
            
            
           
        }
        
    }

}
