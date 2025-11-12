<?php

if (!defined('ABSPATH'))
    exit;

if (!class_exists('Oc3Sengine_SearchUtils')) {

    class Oc3Sengine_SearchUtils {
        
        //$data_parameters
        public static function getSearchStyles($data_parameters) {

            if(!isset($data_parameters['search_loader_color'])){
                $data_parameters['search_loader_color'] = '#CCCCCC';
            }
            $styles = '';

            $styles .= ' 
            .oc3sengine-search .oc3sengine-search-field{
                    background-color:'.esc_html($data_parameters['background_color']).';
                    border-color: '.esc_html($data_parameters['border_color']).';
                    color:  '.esc_html($data_parameters['font_color']).';  
  
            }
            
            .oc3sengine-search-result .oc3sengine_search_item{
                background-color:'.esc_html($data_parameters['results_background_color']).';
                color:'.esc_html($data_parameters['results_font_color']).';    
            }
            
            div.oc3sengine-search{
                width:'.esc_html($data_parameters['search_box_width']).esc_html($data_parameters['search_box_width_metrics']).'; 
            }
            div.oc3sengine-custom-loader.oc3sengine-general-loader{
                color:'.esc_html($data_parameters['search_loader_color']).'; 
            }
                        
			';
            if(isset($data_parameters['search_box_height']) && $data_parameters['search_box_height'] > 0){
                $styles .= 'input.oc3sengine-search-field{
                height:'.esc_html($data_parameters['search_box_height']).'px; 
            }';
            }
            
            if(isset($data_parameters['results_font_size']) && ((int)$data_parameters['results_font_size']) > 0){
                $styles .= 'div.oc3sengine_search_item{
                font-size:'.esc_html($data_parameters['results_font_size']).'px !important; 
            }';
            }
            
            if(isset($data_parameters['font_size']) && ((int)$data_parameters['font_size']) > 0){
                $styles .= 'input.oc3sengine-search-field{
                font-size:'.esc_html($data_parameters['font_size']).'px !important; 
            }';
            }
            
            return $styles;
        }

        public static function getSearchDefaultStyles() {
            $styles = [
                'background_color' => '#fff',
                'border_color' => '#555753',
                'font_color' => '#ffffff',
                'font_size' => 'bottom-right',
                'results_background_color' => '#0C476E',
                'results_font_color' => '#0E5381',
                'send_button_text_color' => '#ffffff',
                'send_button_hover_color' => '#126AA5',
                'color' => '#ffefea',
                'chatbot_border_radius' => 10,
                'header_text_color' => '#ffffff',
                'response_bg_color' => '#5AB2ED',
                'response_text_color' => '#000000',
                'response_icons_color' => '#000',
                'message_border_radius' => 10,
                'message_bg_color' => '#1476B8',
                'message_text_color' => '#fff',
                'message_font_size' => 16,
                'message_margin' => 5,
                'chat_width' => 25,
                'chat_width_metrics' => '%',
                'chat_height' => 15,
                'chat_height_metrics' => '%',
                'access_for_guests' => 1,
            ];
            return $styles;
        }


    }

}