<?php

if (!defined('ABSPATH'))
    exit;

if (!class_exists('Oc3Sengine_RagUtils')) {

    class Oc3Sengine_RagUtils {


        public static function getEmbeddingModels() {

            $emb_models = ['text-embedding-3-small' => 'text-embedding-3-small(1536)',
                'text-embedding-3-large'=>'text-embedding-3-large(3072)',
                'text-embedding-ada-002'=>'text-embedding-ada-002(1536)'];
            
            return $emb_models; //in format [1=>'gpt-3.5-turbo',2=>'gpt-3.5-turbo-16k'];
        }
    
        public static function getAIProvider($provider_name = 'openai'){
            switch($provider_name){
                case 'openai':
                    if (!class_exists('Oc3Sengine_OpenAi')) {
                        require_once OC3SENGINE_PATH . '/lib/classes/OpenAi.php';
                    }
                    return new Oc3Sengine_OpenAi();

                default:
                    
                    if (!class_exists('Oc3Sengine_OpenAi')) {
                        require_once OC3SENGINE_PATH . '/lib/classes/OpenAi.php';
                    }
                    return new Oc3Sengine_OpenAi();
            }
        }
        
        public static function  calculateEstimatedEmbeddingCost($tokens,$model) {
        
            $cost_per_poken = 0.00010 / 1000; // Default cost 
        
            switch ($model) {
                case 'text-embedding-3-small':
                    $cost_per_poken = 0.00002 / 1000;
                    break;
                case 'text-embedding-3-large':
                    $cost_per_poken = 0.00013 / 1000;
                    break;
                case 'embedding-001':
                    $cost_per_poken = 0.0002 / 1000;
                    break;
                case 'text-embedding-004':
                    $cost_per_poken = 0.0002 / 1000;
                    break;
            }
        
            $estimated_cost = !empty($tokens) ? number_format((int)$tokens * $cost_per_poken, 8) . '$' : '--';
            return $estimated_cost;
        }
        
        public static function makeEmbedingCall($content = ''){
            
            $result = array('status' => 0, 'embedding' => [] ,'tokens'=> 0, 'msg' => esc_html__('Something went wrong','oc3-sengine'));
            $ai_name = 'openai';
            $openai = self::getAIProvider($ai_name);
            $oc3_main_embedding_model = get_option(OC3SENGINE_PREFIX_LOW . 'config_emb_model', '');
            $apiParams = [
                    'input' => $content,
                    'model' => $oc3_main_embedding_model
                ];
            $encresponse = $openai->doEmbeddings($apiParams);
            $response = json_decode($encresponse,true);
            
            if(isset($response['error']) && !empty($response['error'])) {
                    $result['msg'] = $response['error']['message'];
                    if(empty($result['msg']) && isset($response['error']['code']) && $response['error']['code'] == 'invalid_api_key'){
                        $result['msg'] = 'Incorrect API key provided. You can find your API key at https://platform.openai.com/account/api-keys.';
                        $result['status'] = 500;
                    }
                    return $result;
                }
                else{
                    $embedding = $response['data'][0]['embedding'];
                    if(empty($embedding)){
                        $result['msg'] = esc_html__('No data returned','oc3-sengine');
                        $result['status'] = 500;
                        return $result;
                    }
                    else{
                        
                        $result['status'] = 200;
                        $result['embedding'] = $embedding;
                        if(isset($response['usage']) && is_array($response['usage']) && isset($response['usage']['total_tokens'])){
                            $result['tokens'] = $response['usage']['total_tokens'];
                        }
                        return $result;
                              
                    }
                }
            return $result;
        }
    }

}