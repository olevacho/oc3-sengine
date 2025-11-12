<?php

if (!defined('ABSPATH'))
    exit;

if (!class_exists('Oc3Sengine_AiRequest')) {

    class Oc3Sengine_AiRequest {

        public static $gpt_key = '';
        public static $model = 'gpt-4o-mini';
        public static $files_api_url = 'https://api.openai.com/v1/files';
        public static $chat_completion_endpoint = 'https://api.openai.com/v1/chat/completions';
        public static $assistant_api = 'https://api.openai.com/v1/assistants';
        public static $thread_url = "https://api.openai.com/v1/threads";


        /* prepares chat completion chatGPT API request and calls :sendChatGptRequest
          @param $data     array  received from Edit&Extend metabox form
         * see https://platform.openai.com/docs/api-reference/chat/create 
          Returns the array in format  [error_code,response] see sendChatGptRequest
         *          */

        public static function sendChatGptEdit($data) {

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
            $result = self::sendChatGptRequest($body_str);
            return $result;
        }

        /* prepares chat completion chatGPT API request and calls :sendChatGptRequest
          @param $data     array  received from client side form
         * see https://platform.openai.com/docs/api-reference/chat/create 
          Returns the array in format  [error_code,response] see sendChatGptRequest
         *          */

        public static function sendChatGptCompletion($data) {

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
            $result = self::sendChatGptRequest($body_str);
            //var_dump($result);
            return $result;
        }

        /* sends request to chatGPT API and returns response in format:       [error_code,response]
          @param $body_str     string  that is json encoded array in format
         * defined https://platform.openai.com/docs/api-reference/chat/create */

        public static function sendChatGptRequest($body_str = '', $method = 'POST', $url = '') {

            $r_url = is_string($url) && strlen($url) > 0 ? $url : self::$chat_completion_endpoint;
            if (strlen(self::$gpt_key) == 0) {
                self::$gpt_key = get_option(OC3SENGINE_PREFIX_LOW . 'open_ai_gpt_key', ''); 
            }
            $headers = [
                "Content-Type" => "application/json",
                "Authorization" => "Bearer " . self::$gpt_key
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

        public static function getFromUrl($url) {

            return self::sendChatGptRequest('', 'GET', $url);
        }

        /* tests if response from ChatGPT API has correct fromat and contains all respected fields
         * $response - json decoded response from ChatGPT API
         *  */

        public static function testChatGptResponse($response) {
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

        public static function getChatGptResponseEditMessage($response) {

            if (is_object($response) && isset($response->choices) && is_array($response->choices) && count($response->choices) > 0) {
                $choice = $response->choices[0];
                if (is_object($choice) && isset($choice->message) && is_object($choice->message) && isset($choice->message->content)) {
                    $resp_text = $choice->message->content;
                    return $resp_text;
                }
            }
            return '';
        }




    }

}