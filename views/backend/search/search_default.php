<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$wp_nonce = wp_create_nonce(OC3SENGINE_PREFIX_SHORT . 'gpt_confnonce');
$metrics = Oc3Sengine_Utils::getMetrics();
//var_dump($search_agent_options);
?>
<div id="oc3sengine-tabs-1" class="oc3sengine_tab_panel" data-oc3sengine="1">
    <div class="inside">
        <div class="oc3sengine_config_items_wrapper">
            <form action="" method="post" id="oc3sengine_searchgen_form">    
                <input type="hidden" name="oc3se_gpt_confnonce" value="<?php echo esc_html($wp_nonce); ?>"/>
                <input type="hidden" name="action" value="oc3se_store_search_default"/>
                <div class="oc3sengine_block_content">

                    <div class="oc3sengine_row_content oc3sengine_pr">

                        <div class="oc3sengine_bloader oc3sengine_gbutton_container">
                            <div style="padding: 1em 1.4em;">
                                <input type="submit" value="<?php echo esc_html__('Save', 'oc3-sengine') ?>" name="oc3sengine_submit" id="oc3sengine_submit" class="button button-primary button-large" onclick="oc3sengineSaveSearch(event);" >

                            </div>

                            <div class="oc3sengine-custom-loader oc3sengine-general-loader" style="display: none;"></div>
                        </div>
                    </div>
                </div>

                <div class="oc3sengine_data_column_container">
                    <div class="oc3sengine_data_column">
                        <div class="oc3sengine_block ">
                            <div style="position:relative;">
                                <div class="oc3sengine_block_header">
                                    <h3><?php esc_html_e('Search box', 'oc3-sengine'); ?></h3>
                                </div>
                                
                                <div class="oc3sengine_block_content" >
                                    <div class="oc3sengine_row_header">
                                        <label for="oc3se_background_color"><?php esc_html_e('Background Color', 'oc3-sengine'); ?>:</label>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                        <div  style="position:relative;">
                                            <?php
                                            $background_color = isset($search_agent_options['background_color'])?esc_html($search_agent_options['background_color']):'#FFFFFF';

                                            ?>
                                            <input type="color" 
                                                   name="oc3se_background_color" 
                                                   id="oc3se_background_color" 
                                                   value="<?php echo esc_html($background_color); ?>">
                                        </div>
                                        <p class="oc3sengine_input_description">
                                            <span style="display: inline;">
                                                <?php echo esc_html('Specify the color of the background of search input box.', 'oc3-sengine'); ?> 
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                
                                <div class="oc3sengine_block_content" >
                                    <div class="oc3sengine_row_header">
                                        <label for="oc3se_border_color"><?php esc_html_e('Border Color', 'oc3-sengine'); ?>:</label>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                        <div  style="position:relative;">
                                            <?php
                                            $border_color = isset($search_agent_options['border_color'])?esc_html($search_agent_options['border_color']):'#000000';

                                            ?>
                                            <input type="color" 
                                                   name="oc3se_border_color" 
                                                   id="oc3se_border_color" 
                                                   value="<?php echo esc_html($border_color); ?>">
                                        </div>
                                        <p class="oc3sengine_input_description">
                                            <span style="display: inline;">
                                                <?php echo esc_html('Specify the color of the border of search input box.', 'oc3-sengine'); ?> 
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="oc3sengine_block_content" >
                                    <div class="oc3sengine_row_header">
                                        <label for="oc3se_font_color"><?php esc_html_e('Font Color', 'oc3-sengine'); ?>:</label>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                        <div  style="position:relative;">
                                            <?php
                                            $font_color = isset($search_agent_options['font_color'])?esc_html($search_agent_options['font_color']):'#000000';

                                            ?>
                                            <input type="color" 
                                                   name="oc3se_font_color" 
                                                   id="oc3se_font_color" 
                                                   value="<?php echo esc_html($font_color); ?>">
                                        </div>
                                        <p class="oc3sengine_input_description">
                                            <span style="display: inline;">
                                                <?php echo esc_html('Specify the color of the search box text.', 'oc3-sengine'); ?> 
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="oc3sengine_block_content" >
                                        <div class="oc3sengine_row_header">
                                            <label for="oc3se_font_size">
                                                <?php esc_html_e('Font size', 'oc3-sengine'); ?>:
                                            </label>
                                        </div>
                                        <div  class="oc3sengine_row_content oc3sengine_pr">
                                            <div  style="position:relative;">
                                                <?php $message_font_size = isset($search_agent_options['font_size'])?(int)$search_agent_options['font_size']:0; ?>
                                                <input class="oc3sengine_input oc3sengine_20pc"  name="oc3se_font_size"  
                                                       id="oc3se_font_size" type="number" 
                                                       step="1" maxlength="4" autocomplete="off"  
                                                       value="<?php echo (int)$message_font_size; ?>">

                                            </div>
                                            <p class="oc3sengine_input_description">
                                                <span style="display: inline;">
                                                    <?php esc_html_e('Font size of text in search box. Input 0 (zero) if you want to have it automatic! ', 'oc3-sengine'); ?>
                                                </span>
                                            </p>
                                        </div>
                                </div>
                                
                                <div class="oc3sengine_block_content" >
                                    <div class="oc3sengine_row_header">
                                        <label for="oc3se_search_button_color"><?php esc_html_e('Search Button Color', 'oc3-sengine'); ?>:</label>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                        <div  style="position:relative;">
                                            <?php
                                            $button_color = isset($search_agent_options['search_button_color'])?esc_html($search_agent_options['search_button_color']):'#ffffff';

                                            ?>
                                            <input type="color" 
                                                   name="oc3se_search_button_color" 
                                                   id="oc3se_search_button_color" 
                                                   value="<?php echo esc_html($button_color); ?>">
                                        </div>
                                        <p class="oc3sengine_input_description">
                                            <span style="display: inline;">
                                                <?php echo esc_html('Specify the color of the search button.', 'oc3-sengine'); ?> 
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="oc3sengine_block_content" >
                                    <div class="oc3sengine_row_header">
                                        <label for="oc3se_search_button_text_color"><?php esc_html_e('Search Button Text Color', 'oc3-sengine'); ?>:</label>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                        <div  style="position:relative;">
                                            <?php
                                            $search_button_text_color = isset($search_agent_options['search_button_text_color'])?esc_html($search_agent_options['search_button_text_color']):'#000000';

                                            ?>
                                            <input type="color" 
                                                   name="oc3se_search_button_text_color" 
                                                   id="oc3se_search_button_text_color" 
                                                   value="<?php echo esc_html($search_button_text_color); ?>">
                                        </div>
                                        <p class="oc3sengine_input_description">
                                            <span style="display: inline;">
                                                <?php echo esc_html('Specify the color of the button text.', 'oc3-sengine'); ?> 
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="oc3sengine_block_content" >
                                    <div class="oc3sengine_row_header">
                                        <label for="oc3se_search_button_text_color"><?php esc_html_e('Loader Color', 'oc3-sengine'); ?>:</label>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                        <div  style="position:relative;">
                                            <?php
                                            $search_loader_color = isset($search_agent_options['search_loader_color'])?esc_html($search_agent_options['search_loader_color']):'#CCCCCC';

                                            ?>
                                            <input type="color" 
                                                   name="oc3se_search_loader_color" 
                                                   id="oc3se_search_loader_color" 
                                                   value="<?php echo esc_html($search_loader_color); ?>">
                                        </div>
                                        <p class="oc3sengine_input_description">
                                            <span style="display: inline;">
                                                <?php echo esc_html('Specify the color of the loader.', 'oc3-sengine'); ?> 
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                
                                <div class="oc3sengine_block_content" >
                                    <div class="oc3sengine_row_header">
                                        <label for="oc3sengine_chatbot_config_chat_height">
                                            <?php esc_html_e('Search box height', 'oc3-sengine'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                        <div  style="position:relative;">
                                            <?php $search_box_height = isset($search_agent_options['search_box_height'])?(int)$search_agent_options['search_box_height']:0; ?>
                                            <input class="oc3sengine_input oc3sengine_20pc"  
                                                   name="oc3se_search_box_height"  
                                                   id="oc3se_search_box_height" type="number" 
                                                   step="1" maxlength="4" autocomplete="off"  
                                                   placeholder="<?php  ?>" value="<?php echo (int)$search_box_height; ?>">

                                        </div>
                                        <p class="oc3sengine_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Select height of Search Box.', 'oc3-sengine'); ?>
                                            </span>
                                        </p>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                        <div  style="position:relative;">
                                            <span><?php   esc_html_e('Pixels', 'oc3-sengine');  ?></span>
                                            <?php 
                                            if(false){
                                            $search_box_height_metrics = isset($search_agent_options['search_box_height_metrics'])?$search_agent_options['search_box_height_metrics']:'%'; ?>
                                        
                                            <select id="oc3se_search_box_height_metrics" name="oc3se_search_box_height_metrics">
                                                <?php

                                                foreach($metrics as $idx => $met_val){

                                                    if($search_box_height_metrics == $met_val){
                                                        $sel_opt = 'selected';
                                                    }else{
                                                        $sel_opt = '';
                                                    }
                                                    ?>
                                                    <option value="<?php echo esc_html($met_val); ?>" <?php echo esc_html($sel_opt);  ?>> <?php echo esc_html($met_val); ?> </option>
                                                    <?php
                                                }
                                                ?>
                                            </select>   
                                            <?php
                                            }
                                            ?>
                                        </div>
                                        <p class="oc3sengine_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Input 0 (zero) if you want to have it automatic!', 'oc3-sengine'); ?>
                                            </span>
                                        </p>
                                    </div>
                                    
                                    
                                    
                                    
                                </div>

                               <div class="oc3sengine_block_content" >
                                    <div class="oc3sengine_row_header">
                                        <label for="oc3sengine_chatbot_config_chat_width">
                                            <?php esc_html_e('Search box width', 'oc3-sengine'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                        <div  style="position:relative;">
                                            <?php $search_box_width_width = isset($search_agent_options['search_box_width'])?(int)$search_agent_options['search_box_width']:100; ?>
                                            <input class="oc3sengine_input oc3sengine_20pc"  
                                                   name="oc3se_search_box_width"  
                                                   id="oc3se_search_box_width" type="number" 
                                                   step="1" maxlength="4" autocomplete="off"  
                                                   placeholder="<?php  ?>" value="<?php echo (int)$search_box_width_width; ?>">

                                        </div>
                                        <p class="oc3sengine_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Select width of Search Box.', 'oc3-sengine'); ?>
                                            </span>
                                        </p>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                        <div  style="position:relative;">
                                            <?php $search_box_width_metrics = isset($search_agent_options['search_box_width_metrics'])?$search_agent_options['search_box_width_metrics']:'%'; ?>
                                        
                                            <select id="oc3se_search_box_width_metrics" name="oc3se_search_box_width_metrics">
                                                <?php

                                                foreach($metrics as $idx => $met_val){

                                                    if($search_box_width_metrics == $met_val){
                                                        $sel_opt = 'selected';
                                                    }else{
                                                        $sel_opt = '';
                                                    }
                                                    ?>
                                                    <option value="<?php echo esc_html($met_val); ?>" <?php echo esc_html($sel_opt);  ?>> <?php echo esc_html($met_val); ?> </option>
                                                    <?php
                                                }
                                                ?>
                                            </select>                                        
                                        </div>
                                        <p class="oc3sengine_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Select units of measurement.', 'oc3-sengine'); ?>
                                            </span>
                                        </p>
                                    </div>
                                    
                                    
                                    
                                    
                                </div>
                                


                            </div>
                        </div>   
   
                    </div>
                    <div class="oc3sengine_data_column">
                        <div class="oc3sengine_block ">
                            <div style="position:relative;">
                              <div class="oc3sengine_block_header">
                                    <h3><?php esc_html_e('Search results', 'oc3-sengine'); ?></h3>
                              </div>
                              <?php
                                if(false){
                                ?>  
                              <div class="oc3sengine_block_content" >
                                        <div class="oc3sengine_row_header">
                                            <label for="oc3se_font_size">
                                                <?php esc_html_e('Number of search results', 'oc3-sengine'); ?>:
                                            </label>
                                        </div>
                                  
                                        <div  class="oc3sengine_row_content oc3sengine_pr">
                                            <div  style="position:relative;">
                                                <?php $number_results = isset($search_agent_options['number_results'])?(int)$search_agent_options['number_results']:5; ?>
                                                <input class="oc3sengine_input oc3sengine_20pc"  name="oc3se_number_results"  
                                                       id="oc3se_number_results" type="number" 
                                                       step="1" maxlength="4" autocomplete="off"  
                                                       value="<?php echo (int)$number_results; ?>">

                                            </div>
                                            <p class="oc3sengine_input_description">
                                                <span style="display: inline;">
                                                    <?php esc_html_e('Number of results returned by search.', 'oc3-sengine'); ?>
                                                </span>
                                            </p>
                                        </div>
                                </div>
                                <?php
                                }
                                ?>
                                <div class="oc3sengine_block_content" >
                                        <div class="oc3sengine_row_header">
                                            <label for="oc3se_results_font_size">
                                                <?php esc_html_e('Font size of results text', 'oc3-sengine'); ?>:
                                            </label>
                                        </div>
                                        <div  class="oc3sengine_row_content oc3sengine_pr">
                                            <div  style="position:relative;">
                                                <?php $results_font_size = isset($search_agent_options['results_font_size'])?(int)$search_agent_options['results_font_size']:0; ?>
                                                <input class="oc3sengine_input oc3sengine_20pc"  name="oc3se_results_font_size"  
                                                       id="oc3se_results_font_size" type="number" 
                                                       step="1" maxlength="4" autocomplete="off"  
                                                       value="<?php echo (int)$results_font_size; ?>">

                                            </div>
                                            <p class="oc3sengine_input_description">
                                                <span style="display: inline;">
                                                    <?php esc_html_e('Font size of results text. Input 0 (zero) if you want to have it automatic! ', 'oc3-sengine'); ?>
                                                </span>
                                            </p>
                                        </div>
                                </div>
                                
                                
                                 <div class="oc3sengine_block_content" >
                                    <div class="oc3sengine_row_header">
                                        <label for="oc3se_results_font_color"><?php esc_html_e('Results Font Color', 'oc3-sengine'); ?>:</label>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                        <div  style="position:relative;">
                                            <?php
                                            $results_font_color = isset($search_agent_options['results_font_color'])?esc_html($search_agent_options['results_font_color']):'#000000';

                                            ?>
                                            <input type="color" 
                                                   name="oc3se_results_font_color" 
                                                   id="oc3se_results_font_color" 
                                                   value="<?php echo esc_html($results_font_color); ?>">
                                        </div>
                                        <p class="oc3sengine_input_description">
                                            <span style="display: inline;">
                                                <?php echo esc_html('Specify the color of results text.', 'oc3-sengine'); ?> 
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="oc3sengine_block_content" >
                                    <div class="oc3sengine_row_header">
                                        <label for="oc3se_results_background_color"><?php esc_html_e('Results Background Color', 'oc3-sengine'); ?>:</label>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                        <div  style="position:relative;">
                                            <?php
                                            $results_baclground_color = isset($search_agent_options['results_background_color'])?esc_html($search_agent_options['results_background_color']):'#ffffff';

                                            ?>
                                            <input type="color" 
                                                   name="oc3se_results_background_color" 
                                                   id="oc3se_results_background_color" 
                                                   value="<?php echo esc_html($results_baclground_color); ?>">
                                        </div>
                                        <p class="oc3sengine_input_description">
                                            <span style="display: inline;">
                                                <?php echo esc_html('Specify the color of background of box with search results.', 'oc3-sengine'); ?> 
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                
                                <div class="oc3sengine_block_header">
                                    <h3><?php esc_html_e('Search box behavior', 'oc3-sengine'); ?></h3>
                                </div>
                                <div class="oc3sengine_block_content" >
                                    <div class="oc3sengine_row_header">
                                        <label for="oc3se_access_for_guests">
                                            <?php esc_html_e('Access for guests', 'oc3-sengine'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3sengine_row_content oc3sengine_pr">
                                        <div  style="position:relative;">
                                            <?php 

                                            $checked = '';
                                            $access_for_guests = isset($search_agent_options['access_for_guests'])?(int)$search_agent_options['access_for_guests']:1; 
                                            if ($access_for_guests == 1) {
                                                    $checked = ' checked ';
                                                }
                                            ?>
                                            
                                            <input type="checkbox" id="oc3se_access_for_guests" 
                                                   name="oc3se_access_for_guests" 
                                                       <?php echo esc_html($checked); ?> value="1"  >

                                        </div>
                                        <p class="s2baia_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Check box if you want to make search box accessible for anonimous visitors', 'oc3-sengine'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                </div>  
                            </div>
                        </div> 
                    </div>

                
            </form>
            </div>

        </div>
</div>
    


