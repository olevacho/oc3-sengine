<?php
if ( ! defined( 'ABSPATH' ) ) exit;

//$wp_nonce = wp_create_nonce(OC3SENGINE_PREFIX_SHORT . 'gpt_confnonce');
$menu_page = OC3SENGINE_PREFIX_LOW . 'settings';


$response_timeout = (int) get_option(OC3SENGINE_PREFIX_LOW . 'response_timeout', 120);
$max_tokens = (int) get_option(OC3SENGINE_PREFIX_LOW . 'max_tokens', 1024);

$models = Oc3Sengine_Utils::getGPTModels();
if (!class_exists('Oc3Sengine_RagUtils')) {
    require_once OC3SENGINE_PATH . '/lib/helpers/RagUtils.php';
}
$emb_models = Oc3Sengine_RagUtils::getEmbeddingModels();
$oc3sengine_pinecone_key = get_option(OC3SENGINE_PREFIX_LOW . 'pinecone_key', '');

                                               
?>
<div id="oc3sengine-tabs-1" class="oc3sengine_tab_panel" data-oc3sengine="1">
    <div class="inside">
        <div class="oc3sengine_config_items_wrapper">
            <form action="" method="post" id="oc3sengine_gen_form">    
                <input type="hidden" name="oc3se_gpt_confnonce" value="<?php echo esc_html($wp_nonce); ?>"/>
                <input type="hidden" name="action" value="oc3se_store_general_tab"/>
                <div class="oc3sengine_block_content">

                    <div class="oc3sengine_row_content oc3sengine_pr">

                        <div class="oc3sengine_bloader oc3sengine_gbutton_container">
                            <div style="padding: 1em 1.4em;">
                                <input type="submit" value="<?php echo esc_html__('Save', 'oc3-sengine') ?>" name="oc3sengine_submit" id="oc3sengine_submit" class="button button-primary button-large" onclick="oc3sengineSaveGeneral(event);" >

                            </div>

                            <div class="oc3sengine-custom-loader oc3sengine-general-loader" style="display: none;"></div>
                        </div>
                    </div>
                </div>
		 <h3 class="oc3sengine_instruction" style="text-align: center;">
                                                    <?php echo esc_html__('Put', 'oc3-sengine'); ?> <span id="oc3sengine_shortcode" style="cursor: pointer;" onClick="oc3copyToClipboardInnerHtml(event,'oc3sengine_shortcode','<?php echo esc_html__('Copied successfully', 'oc3-sengine'); ?>','<?php echo esc_html__('Can not copy', 'oc3-sengine'); ?>');">[oc3-sengine]</span> <?php echo esc_html__('into any page or post to display search box.', 'oc3-sengine'); ?> 
                </h3>
                <div class="oc3sengine_data_column_container">
                    <div class="oc3sengine_data_column">
                        <div class="oc3sengine_block ">
                            <div style="position:relative;">
                                <div class="oc3sengine_block_header">
                                    <h3><?php esc_html_e('Common', 'oc3-sengine'); ?></h3>
                                </div>
                                <div  class="oc3sengine_block_header">
                                    <h4><?php esc_html_e('OpenAI', 'oc3-sengine'); ?></h4>
                                </div> 
                                <div class="oc3sengine_block_content" >
                                    <div class="oc3sengine_row_header">
                                        <label for="oc3sengine_open_ai_gpt_key"><?php esc_html_e('Open AI Key', 'oc3-sengine'); ?>:</label>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                        <div  style="position:relative;">

                                            <input type="text"  name="oc3sengine_open_ai_gpt_key"   id="oc3sengine_open_ai_gpt_key"  value="<?php echo esc_html($oc3sengine_open_ai_gpt_key); ?>">
                                        </div>
                                        <p class="oc3sengine_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('You can get your API Keys in your', 'oc3-sengine'); ?> <a href="https://beta.openai.com/account/api-keys" target="_blank"><?php esc_html_e('OpenAI Account', 'oc3-sengine'); ?></a>.
                                                <?php esc_html_e('OpenAI service is needed for embedding content', 'oc3-sengine'); ?> 
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <?php 
                                if(false){
                                ?>
                                <div  class="oc3sengine_block_header">
                                    <h4><?php esc_html_e('Search Results', 'oc3-sengine'); ?></h4>
                                </div> 
                                <div class="oc3sengine_block_content" >
                                
                                    <div class="oc3-sengine_block_header">
                                        <label for="oc3sengine_config_embedding_context_not_found_msg"><?php esc_html_e('Not found content message', 'oc3-sengine'); ?>:</label>
                                    </div>
                                    <?php
                                    $oc3se_config_embedding_context_not_found_msg = sanitize_text_field(get_option(OC3SENGINE_PREFIX_LOW . 'config_embedding_context_not_found_msg', ''));
                                    
                                    ?>
                                    <div  class="oc3se_row_content oc3se_pr">
                                        <div  style="position:relative;">

                                            <input type="text"  name="oc3sengine_config_embedding_context_not_found_msg"  
                                                   id="oc3sengine_config_embedding_context_not_found_msg" 
                                                   value="<?php echo esc_html($oc3se_config_embedding_context_not_found_msg); ?>">
                                        </div>
                                        <p class="oc3se_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('This message will be sent to visitor when content could not be found in search index. ', 'oc3-sengine'); ?> 
                                            </span>
                                        </p>

                                    </div>
                            
                            </div>
                                
                                <?php
                                }
                                if(false){
                                ?>
                                <div class="oc3sengine_block_content" >
                                    <div class="oc3sengine_row_header">
                                        <label for="oc3sengine_response_timeout">
                                            <?php esc_html_e('Response Timeout (sec)', 'oc3-sengine'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                        <div  style="position:relative;">

                                            <input class="oc3sengine_input oc3sengine_20pc"  name="oc3sengine_response_timeout"  id="oc3sengine_response_timeout" type="number" 
                                                   step="1"  max="200" maxlength="3" autocomplete="off"  
                                                   placeholder="<?php esc_html_e('Response Timeout', 'oc3-sengine'); ?>" value="<?php echo (int)$response_timeout; ?>">

                                        </div>
                                        <p class="oc3sengine_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Make this value higher for bad internet connection.', 'oc3-sengine'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>

                                <div class="oc3sengine_block_content" >
                                    <div class="oc3sengine_row_header">
                                        <label for="oc3sengine_response_timeout">
                                            <?php esc_html_e('Default request text length (tokens)', 'oc3-sengine'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                        <div  style="position:relative;">

                                            <input class="oc3sengine_input oc3sengine_20pc"  name="oc3sengine_max_tokens"  id="oc3sengine_max_tokens" type="number" 
                                                   step="1" maxlength="4" autocomplete="off"  
                                                   placeholder="<?php esc_html_e('Max tokens', 'oc3-sengine'); ?>" value="<?php echo esc_html($max_tokens); ?>">

                                        </div>
                                        <p class="oc3sengine_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Make this value higher for larger text.', 'oc3-sengine'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <?php
                                }
                                ?>
                                <?php
                                if(false){
                                ?>
                                <div class="oc3sengine_block_content">
                                            <div class="oc3sengine_row_header">
                                                <label for="oc3sengine_prompt_model"><?php esc_html_e('Model', 'oc3-sengine'); ?>:</label>
                                            </div>
                                            <div class="oc3sengine_row_content oc3sengine_pr">
                                                <div style="position:relative;">
                                                    <select id="oc3sengine_prompt_model" name="oc3sengine_prompt_model">
                                                        <?php
                                                        $model = get_option(OC3SENGINE_PREFIX_LOW . 'prompt_model', 'gpt-4o') ;

                                                        foreach ($models as $value) {
                                                            if ($model == $value) {
                                                                $sel_opt = 'selected';
                                                            } else {
                                                                $sel_opt = '';
                                                            }
                                                            ?>
                                                            <option value="<?php echo esc_html($value); ?>" <?php echo esc_html($sel_opt); ?>><?php echo esc_html($value); ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <p class="oc3sengine_input_description">
                                                    <span style="display: inline;"><?php esc_html_e('Select model, which is used for search. More models are ', 'oc3-sengine'); ?></span><a href="#" onclick="oc3sengineShowModels(event);" >here</a>
                                                </p>
                                            </div>
                                </div>
                                
                                <div class="oc3sengine_block_content" >
                                    <div class="oc3sengine_row_header">
                                        <label for="oc3sengine_chatbot_config_chat_temperature">
                                            <?php esc_html_e('Temperature', 'oc3-sengine'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                        <div  style="position:relative;">
                                            <?php $temperature = get_option(OC3SENGINE_PREFIX_LOW . 'temperature', 0.8) ; ?>
                                            <input class="oc3sengine_input oc3sengine_20pc"  name="oc3sengine_config_temperature"  
                                                   id="oc3sengine_config_temperature" type="number" 
                                                   step="0.1" min="0" max="2" maxlength="4" autocomplete="off"  
                                                   placeholder="<?php esc_html_e('Enter number pixels or percent', 'oc3-sengine'); ?>"
                                                   value="<?php echo esc_html($temperature); ?>">

                                        </div>
                                        <p class="oc3sengine_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Input temperature from 0 to 2.', 'oc3-sengine'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <?php
                                
                                }
                                ?>
                                <?php
                                if(false){
                                ?>
                                <div class="oc3sengine_block_content" >
                                    <div class="oc3sengine_row_header">
                                        <label for="oc3sengine_config_frequency_penalty">
                                            <?php esc_html_e('Frequency penalty', 'oc3-sengine'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                        <div  style="position:relative;">
                                            <?php $frequency_penalty = get_option(OC3SENGINE_PREFIX_LOW . 'frequency_penalty', 0); ?>
                                            <input class="oc3sengine_input oc3sengine_20pc"  name="oc3sengine_config_frequency_penalty"  
                                                   id="oc3sengine_config_frequency_penalty" type="number" 
                                                   step="0.01" min="-2" max="2" maxlength="4" autocomplete="off"  
                                                   value="<?php echo esc_html($frequency_penalty); ?>">

                                        </div>
                                        <p class="oc3sengine_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Encourages the search engine to generate text with a more diverse vocabulary. A higher frequency penalty value will reduce the likelihood of the chatbot repeating words that have already been used in the generated text. Number between -2.0 and 2.0.', 'oc3-sengine'); ?>
                                            </span>
                                        </p>
                                    </div>
                                    <div class="oc3sengine_block_content" >
                                    <div class="oc3sengine_row_header">
                                        <label for="oc3sengine_config_presence_penalty">
                                            <?php esc_html_e('Presence penalty', 'oc3-sengine'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                        <div  style="position:relative;">
                                            <?php $presence_penalty = get_option(OC3SENGINE_PREFIX_LOW . 'presence_penalty', 0); ?>
                                            <input class="oc3sengine_input oc3sengine_20pc"  name="oc3sengine_config_presence_penalty"  
                                                   id="oc3sengine_config_presence_penalty" type="number" 
                                                   step="0.01" min="-2" max="2" maxlength="4" autocomplete="off"  
                                                   value="<?php echo esc_html($presence_penalty); ?>">

                                        </div>
                                        <p class="oc3sengine_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Encourages the engine to generate text that includes specific phrases or concepts. Number between -2.0 and 2.0.', 'oc3-sengine'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                    
                                </div>
                                
<?php
                                }
                                ?>
                            </div>
                        </div>   
                       <?php if (current_user_can('manage_options')) {

                           $oc3sengine_user_roles = Oc3Sengine_Utils::getInstructionRoles();
                           
                           ?> 
                        <div class="oc3sengine_block ">

                </div> 
                <?php }  ?>        
                    </div>
                    <div class="oc3sengine_data_column">
                        <div class="oc3sengine_block ">
                            <div style="position:relative;">
                              <div class="oc3sengine_block_header">
                                    <h3><?php esc_html_e('Embedding', 'oc3-sengine'); ?></h3>
                              </div>
                                <div  class="oc3sengine_block_header">
                                    <h4><?php esc_html_e('Pinecone', 'oc3-sengine'); ?></h4>
                                </div> 
                                
                                <div class="oc3sengine_block_content" >
                                
                                    
                                    <div  class="oc3sengine_row_content oc3sengine_pr">

                                        <p class="oc3sengine_input_description">
                                        <ol  class="oc3sengine_instruction">
                                            <li>
                                                <?php esc_html_e('Create your  ', 'oc3-sengine'); ?> <a href="https://www.pinecone.io/" target="_blank"><?php esc_html_e('Pinecone Account', 'oc3-sengine'); ?></a>.
                                            </li>
                                            <li>
                                                <?php esc_html_e('Create index here ', 'oc3-sengine'); ?> <a href="https://www.pinecone.io/" target="_blank"><?php esc_html_e('Pinecone', 'oc3-sengine'); ?></a>.
                                            </li>
                                            <li>
                                                <?php esc_html_e('Get your API KEY from', 'oc3-sengine'); ?> <a href="https://www.pinecone.io/" target="_blank"><?php esc_html_e('Pinecone', 'oc3-sengine'); ?></a>.
                                            </li>
                                            <li>
                                                <?php esc_html_e('Fill Pinecone API Key: field bellow', 'oc3-sengine'); ?> .
                                            </li>
                                            <li>
                                                <?php esc_html_e('Click Sync Indexes button ', 'oc3-sengine'); ?> .
                                            </li>
                                            <li>
                                                <?php esc_html_e('Select Pinecode Index: ', 'oc3-sengine'); ?> .
                                            </li>
                                            <li>
                                                <?php esc_html_e('Select Embedding model ', 'oc3-sengine'); ?> .
                                            </li>
                                            <li>
                                                <?php esc_html_e('Click Save button ', 'oc3-sengine'); ?> .
                                            </li>
                                        </ol>
                                        </p>

                                    </div>
                            
                                </div>
                                <div class="oc3sengine_block_content" >
                                
                                    <div class="oc3sengine_block_header">
                                        <label for="oc3sengine_pinecone_key"><?php esc_html_e('Pinecone API Key', 'oc3-sengine'); ?>:</label>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                        <div  style="position:relative;">

                                            <input type="text"  name="oc3sengine_pinecone_key"   id="oc3sengine_pinecone_key"  value="<?php echo esc_html($oc3sengine_pinecone_key); ?>">
                                        </div>
                                        <p class="oc3sengine_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Get your API KEY from', 'oc3-sengine'); ?> <a href="https://www.pinecone.io/" target="_blank"><?php esc_html_e('Pinecone Account', 'oc3-sengine'); ?></a>.
                                            </span>
                                        </p>

                                    </div>
                            
                            </div>
                                <div class="oc3sengine_block_content">
                                     <div class="oc3sengine_block_header">
                                        <label for="oc3sengine_config_emb_model"><?php esc_html_e('Embedding model', 'oc3-sengine'); ?>:</label>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                            <div  style="position:relative;">
<?php

?>
                                                <select id="oc3sengine_config_emb_model" name="oc3sengine_config_emb_model">
                                                <option value="" > <?php esc_html_e('Select Embedding Model', 'oc3-sengine');  ?> </option>
                                                <?php
                                                $oc3sengine_config_emb_model = get_option(OC3SENGINE_PREFIX_LOW . 'config_emb_model', '');
                                                //var_dump($oc3sengine_config_emb_model);
                                                foreach($emb_models as $e_key => $e_model){
                                                    if($oc3sengine_config_emb_model == $e_key){
                                                        $sel_opt = 'selected';
                                                    }else{
                                                        $sel_opt = '';
                                                    }
                                                    ?>
                                                    <option value="<?php echo esc_html($e_key); ?>" <?php echo esc_html($sel_opt);  ?>> <?php echo esc_html($e_model); ?> </option>
                                                    <?php
                                                }
                                                ?>
                                                </select>

                                            </div>
                                            <p class="oc3sengine_input_description">
                                                <span style="display: inline;">
                                                    <?php esc_html_e('Select model which you use for create embeddings before storing them into pinecode database.  ', 'oc3-sengine'); ?>
                                                </span>
                                            </p>
                                    </div>
                                </div>  
                                
                                <div class="oc3sengine_block_content">
                                     <div class="oc3sengine_block_header">
                                        <label for="oc3sengine_config_pinecone_index"><?php esc_html_e('Pinecode Index', 'oc3-sengine'); ?>:</label>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                            <div  style="position:relative;">

                                                <select id="oc3sengine_config_pinecone_index" name="oc3sengine_config_pinecone_index">
                                                <option value="" > <?php esc_html_e('Select Index', 'oc3-sengine');  ?> </option>
                                                <?php
                                                $oc3sengine_config_pinecone_index_title = sanitize_text_field(get_option(OC3SENGINE_PREFIX_LOW . 'config_pinecone_index_title', ''));
                                                $oc3sengine_config_pinecone_index = get_option(OC3SENGINE_PREFIX_LOW . 'config_pinecone_index', '');
                                                $oc3sengine_config_pinecone_indexes = json_decode(get_option(OC3SENGINE_PREFIX_LOW . 'config_pinecone_indexes', ''));
                                                if(is_array($oc3sengine_config_pinecone_indexes) && count($oc3sengine_config_pinecone_indexes) > 0){
                                                    foreach($oc3sengine_config_pinecone_indexes as $pindex){
                                                        if($oc3sengine_config_pinecone_index == $pindex->url){
                                                            $sel_opt = 'selected';
                                                        }else{
                                                            $sel_opt = '';
                                                        }
                                                        ?>
                                                        <option value="<?php echo esc_html($pindex->url); ?>" <?php echo esc_html($sel_opt);  ?>> <?php echo esc_html($pindex->title.' ('.$pindex->dimension.')'); ?> </option>
                                                        <?php
                                                    }
                                                }elseif(strlen($oc3sengine_config_pinecone_index) > 0){
                                                    ?>
                                                    <option value="<?php echo esc_html($oc3sengine_config_pinecone_index); ?>" <?php echo 'selected';  ?>> <?php echo esc_html($oc3sengine_config_pinecone_index_title); ?> </option>    
                                                <?php
                                                }
                                                ?>
                                                </select>
                                                <?php
                                                
                                                
                                                ?>
                                                <input type="hidden" id="oc3sengine_config_pinecone_index_title" name="oc3sengine_config_pinecone_index_title" value="<?php echo esc_html($oc3sengine_config_pinecone_index_title); ?>"/>
                                                <p class="oc3sengine_input_description">
                                                    <button class="oc3sengine_sync_pinecone" value="Sync Indexes"/>Sync Indexes</button>
                                                </p>
                                            </div>
                                            <p class="oc3sengine_input_description">
                                                <span style="display: inline;">
                                                    <?php esc_html_e('Click Sync Indexes button to get list of your pinecode indexes. After select index and click Save button. You need to fill  Pinecone API Key before clicking "Sync Indexes".', 'oc3-sengine'); ?>
                                                </span>
                                            </p>
                                    </div>
                                </div>  
                                
                                <div class="oc3sengine_block_content" >
                                    <div class="oc3sengine_row_header">
                                        <label for="oc3sengine_config_pinecone_topk">
                                            <?php esc_html_e('Top K/Number of search results', 'oc3-sengine'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                        <div  style="position:relative;">
                                            <?php $oc3sengine_config_pinecone_topk = get_option('oc3sengine_config_pinecone_topk', 5); ?>
                                            <input class="oc3sengine_input oc3sengine_20pc"  name="oc3sengine_config_pinecone_topk"  
                                                   id="oc3sengine_config_pinecone_topk" type="number" 
                                                   step="1" min="0" max="100" maxlength="4" autocomplete="off"  
                                                   value="<?php echo esc_html($oc3sengine_config_pinecone_topk); ?>">

                                        </div>
                                        <p class="oc3sengine_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e(' Number of results to return from pinecone index.', 'oc3-sengine'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="oc3sengine_block_content" >
                                    <div class="oc3sengine_row_header">
                                        <label for="oc3sengine_config_confidence">
                                            <?php esc_html_e('Confidence', 'oc3-sengine'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                        <div  style="position:relative;">
                                            <?php $oc3sengine_config_pinecone_confidence = get_option('oc3sengine_config_pinecone_confidence', 40); ?>
                                            <input class="oc3sengine_input oc3sengine_20pc"  name="oc3sengine_config_pinecone_confidence"  
                                                   id="oc3sengine_config_pinecone_confidence" type="number" 
                                                   step="1" min="10" max="100" maxlength="4" autocomplete="off"  
                                                   value="<?php echo (int)$oc3sengine_config_pinecone_confidence; ?>">

                                        </div>
                                        <p class="oc3sengine_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Select the minimum confidence value at which the plugin will show search results to users. Value can be from 0 to 100.', 'oc3-sengine'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                
                                </div>  
                            </div>
                        </div> 
                    </div>

                </div>

            </form>
        </div>

    </div>

