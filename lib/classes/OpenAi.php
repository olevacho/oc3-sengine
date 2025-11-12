<?php

if (!defined('ABSPATH'))
    exit;

if (!class_exists('Oc3Sengine_OpenAi')) {

    class Oc3Sengine_OpenAi {

        public  $gpt_key = '';
        public  $model = 'gpt-4o-mini';
        public  $files_api_url = 'https://api.openai.com/v1/files';
        public  $chat_completion_endpoint = 'https://api.openai.com/v1/chat/completions';
        public  $assistant_api = 'https://api.openai.com/v1/assistants';
        public  $thread_url = "https://api.openai.com/v1/threads";
        public  $http_client = 'wp';
        public  $embeddings_api_url = 'https://api.openai.com/v1/embeddings';
        public  $headers = [];
        public $timeout = 50;
        
        public  $o1_models = [
            'o1-preview' => 'O1 Preview',
            'o1-mini' => 'O1 Mini',
        ];
        
        
        public function __construct() {
            
            $api_key = sanitize_text_field(get_option(OC3SENGINE_PREFIX_LOW . 'open_ai_gpt_key', ''));
            $this->headers['Authorization'] = 'Bearer ' . $api_key;
        }
        
        /* prepares chat completion chatGPT API request and calls :sendChatGptRequest
          @param $data     array  received from Edit&Extend metabox form
         * see https://platform.openai.com/docs/api-reference/chat/create 
          Returns the array in format  [error_code,response] see sendChatGptRequest
         *          */

        public function sendChatGptEdit($data) {

            $model = $data['model'];
            $temperature = $data['temperature'];
            $instruction = $data['instruction'];
            $max_tokens = $data['max_tokens'];
            $body = ["model" => $model, "temperature" => $temperature, "max_tokens" => $max_tokens,
                "messages" => [
                    ["role" => "system", "content" => "Help to change user\'s text according to such instruction:" . $instruction],
                    ["role" => "user", "content" => $data['text']]
                ]
            ];
            $body_str = wp_json_encode($body);
            $result = $this->sendChatGptRequest($body_str);
            return $result;
        }
        
        /* prepares chat completion chatGPT API request and calls :sendChatGptRequest
          @param $data     array  received from client side form
         * see https://platform.openai.com/docs/api-reference/chat/create 
          Returns the array in format  [error_code,response] see sendChatGptRequest
         *          */

        public function sendChatGptCompletion($data) {

            $model = $data['model'];
            $temperature = $data['temperature'];

            $max_tokens = $data['max_tokens'];
            $msgs = [["role" => "system", "content" => $data['system']]];
            if (isset($data["messages"])) {
                foreach ($data["messages"] as $msg) {
                    $msgs[] = ['role' => $msg['role'], 'content' => $msg['content']];
                }
            }
            $body = ["model" => $model, "temperature" => $temperature, "max_tokens" => $max_tokens,
                "top_p" => $data['top_p'], "presence_penalty" => $data['presence_penalty'],
                "frequency_penalty" => $data['frequency_penalty'],
                "messages" => $msgs
            ];
            $body_str = wp_json_encode($body);
            $result = $this->sendChatGptRequest($body_str);
            //var_dump($result);
            return $result;
        }

        /* sends request to chatGPT API and returns response in format:       [error_code,response]
          @param $body_str     string  that is json encoded array in format
         * defined https://platform.openai.com/docs/api-reference/chat/create */

        public function sendChatGptRequest($body_str = '', $method = 'POST', $url = '') {

            $r_url = is_string($url) && strlen($url) > 0 ? $url : $this->chat_completion_endpoint;
            if (strlen($this->gpt_key) == 0) {
                $this->gpt_key = get_option(OC3SENGINE_PREFIX_LOW . 'open_ai_gpt_key', ''); 
            }
            $headers = [
                "Content-Type" => "application/json",
                "Authorization" => "Bearer " . $this->gpt_key
            ];
            global $wp_version;
            $response_timeout = (int) get_option(OC3SENGINE_PREFIX_LOW . 'response_timeout', 120);

            $request_options = array(
                'method' => $method,
                'body' => $body_str,
                'headers' => $headers,
                'user-agent' => $wp_version . '; ' . home_url(),
                'httpversion' => '1.1',
                'timeout' => $response_timeout,
                'sslverify' => false,
                'stream' => false);
            $response = wp_remote_request($r_url, $request_options);
            if (is_wp_error($response)) {
                return array(0, [$response->get_error_code(), $response->get_error_message()]);
            } else {
                if (is_array($response) && array_key_exists('response', $response) && is_array($response['response']) && array_key_exists('code', $response['response'])) {
                    $code = $response['response']['code'];
                    if ($code == 200) {
                        return [1, wp_remote_retrieve_body($response)];
                    } else {
                        if (array_key_exists('body', $response) && is_string($response['body'])) {
                            $resp_body = json_decode($response['body']);
                            if (isset($resp_body->error) && isset($resp_body->error->message)) {
                                return [0, $resp_body->error->message];
                            }
                        }
                        return [0, $response['response']['message']];
                    }
                } else {
                    return [0, wp_remote_retrieve_body($response)];
                }
            }

            return array(0, [0, 'unknown error']);
        }

        /* sends GET request to chatGPT API . It is used for example when getting all list of models
          @param $url     string  that is url of API Endpoint
         */

        public function getFromUrl($url) {

            return $this->sendChatGptRequest('', 'GET', $url);
        }

        /* tests if response from ChatGPT API has correct fromat and contains all respected fields
         * $response - json decoded response from ChatGPT API
         *  */

        public function testChatGptResponse($response) {
            if (is_object($response) && isset($response->choices) && is_array($response->choices) && count($response->choices) > 0) {
                $choice = $response->choices[0];
                if (is_object($choice) && isset($choice->message) && is_object($choice->message) && isset($choice->message->content)) {
                    return true;
                }
            }
            return false;
        }

        /*
          method parses response from ChatGPT chat completion API and gets message
         *  $response - json decoded response from ChatGPT API
         *          */

        public function getChatGptResponseEditMessage($response) {

            if (is_object($response) && isset($response->choices) && is_array($response->choices) && count($response->choices) > 0) {
                $choice = $response->choices[0];
                if (is_object($choice) && isset($choice->message) && is_object($choice->message) && isset($choice->message->content)) {
                    $resp_text = $choice->message->content;
                    return $resp_text;
                }
            }
            return '';
        }

        /**
         * @param $opts
         * @return bool|string
         */
        public function doEmbeddings($opts)
        {
            $url = $this->embeddings_api_url;

            return $this->sendRequest($url, 'POST', $opts);
        }
        
         /**
         new send method
         */
        private function sendRequest(string $url, string $method, array $opts = [])
        {
            // Handle model-specific adjustments (like for o1-mini)
            $opts = $this->processO1Models($opts);

            $post_fields = wp_json_encode($opts);

            $this->headers['Content-Type'] = 'application/json';
 
            $stream = false;
            if (array_key_exists('stream', $opts) && $opts['stream']) {
                $stream = true;
            }
            $request_options = array(
                'timeout' => $this->timeout,
                'headers' => $this->headers,
                'method' => $method,
                'body' => $post_fields,
                'stream' => $stream
            );
            if($post_fields == '[]'){
                unset($request_options['body']);
            }
            $response = wp_remote_request($url,$request_options);
            if(is_wp_error($response)){
                return wp_json_encode(array('error' => array('message' => $response->get_error_message())));
            }
            else{
                if ($stream){
                    return $this->response;
                }
                else{
                    return wp_remote_retrieve_body($response);
                }
            }
        }
        
        
        private function processO1Models(array $opts): array
        {

            $o1_models = $this->o1_models;
            if (isset($opts['model']) && array_key_exists($opts['model'], $o1_models)) {
                //  'max_completion_tokens' instead of 'max_tokens'
                if (array_key_exists('max_tokens', $opts)) {
                    $opts['max_completion_tokens'] = $opts['max_tokens'];
                    unset($opts['max_tokens']);
                }
        
                if (isset($opts['top_p']) && $opts['top_p'] != 1) {
                    $opts['top_p'] = 1;
                }
        
                if (isset($opts['presence_penalty']) && $opts['presence_penalty'] != 0) {
                    $opts['presence_penalty'] = 0;
                }
        

                if (isset($opts['frequency_penalty']) && $opts['frequency_penalty'] != 0) {
                    $opts['frequency_penalty'] = 0;
                }

                if (isset($opts['temperature']) && $opts['temperature'] != 1) {
                    $opts['temperature'] = 1;
                }
            }
        
            return $opts;
        }


    }

}