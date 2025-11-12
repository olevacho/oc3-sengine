<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$display_pagination = true;
$chatbots_per_page = 20;
$chunks_per_page = 10;
$search_string = '';
$current_page = 1;

$wp_nonce = wp_create_nonce(OC3SENGINE_PREFIX_SHORT . 'datasource_nonce');
/*$load_nonce = wp_create_nonce(OC3SENGINE_PREFIX_SHORT . 'datasource_loadnonce');
$wp_del_nonce = wp_create_nonce('oc3se_row_dellognonce');

$wp_embed_nonce = wp_create_nonce(OC3SENGINE_PREFIX_SHORT . 'datasource_embednonce');
*/
$need_pkey_enter = true;
$oc3sengine_pinecone_key = get_option(OC3SENGINE_PREFIX_LOW . 'pinecone_key', '');
if(strlen($oc3sengine_pinecone_key) > 0){
                $need_pkey_enter = false;
            }
$need_pinecone_configure = true;            
$oc3sengine_config_emb_model = get_option(OC3SENGINE_PREFIX_LOW . 'config_emb_model', '');
$oc3sengine_config_pinecone_index = get_option(OC3SENGINE_PREFIX_LOW . 'config_pinecone_index', '');
if(strlen($oc3sengine_config_emb_model) > 0 && strlen($oc3sengine_config_pinecone_index) > 0 ){
     $need_pinecone_configure = false;
}

?>
<div id="oc3sengine-tabs-2" class="oc3sengine_tab_panel" data-oc3sengine="3">
    <div class="inside">
        <div class="oc3sengine_config_items_wrapper">
            <?php
            //var_dump($default_chat_bot);
            $need_key_enter = true;
            $api_key = get_option(OC3SENGINE_PREFIX_LOW . 'open_ai_gpt_key', '');
            if(strlen($api_key) > 0){
                $need_key_enter = false;
            }
if(true){
?>
            <form action="" method="post" id="oc3sengine_chatbot_edit_form">    
                <input type="hidden" id="oc3sengine_randpar" name="oc3sengine_randpar" value="45"/>
                <input type="hidden" name='oc3se_chatbot_nonce' value="<?php echo esc_html($wp_nonce); ?>"/>
                <input type="hidden" id="oc3sengine_source_post_type" name="oc3sengine_source_post_type" value="post"/>
                
                <div class="oc3sengine_block_content">

                    <div class="oc3sengine_row_content oc3sengine_pr">

                        <div class="oc3sengine_bloader oc3sengine_gbutton_container">
                            <div style="padding: 1em 1.4em;">
                                

                            </div>

                            <div class="oc3sengine-custom-loader oc3sengine-general-loader" style="display: none;"></div>
                        </div>
                    </div>
                </div>
                <?php
                if($need_key_enter){
                
                ?>
                <h1 style="color:red;text-align: center;" id="oc3sengine_need_openaikey" ><?php echo esc_html__('You need to enter Open AI API Key before configurations start working', 'oc3-sengine'); ?>. <?php echo esc_html__('Please open', 'oc3-sengine'); ?>  <a href="<?php echo esc_url(admin_url()) . 'admin.php?page=oc3sengine_settings'; ?>"><?php echo esc_html__('this page', 'oc3-sengine'); ?></a> <?php esc_html__('and enter Open AI key', 'oc3-sengine'); ?>.</h1>
                <?php
                }
                if($need_pkey_enter){
                ?>
                <h1 style="color:red;text-align: center;" id="oc3sengine_need_pineconekey" ><?php echo esc_html__('You need to enter Pinecone API Key before configurations start working', 'oc3-sengine'); ?>. <?php echo esc_html__('Please open', 'oc3-sengine'); ?>  <a href="<?php echo esc_url(admin_url()) . 'admin.php?page=oc3sengine_settings'; ?>"><?php echo esc_html__('this page', 'oc3-sengine'); ?></a> <?php esc_html__('and enter Pinecone key', 'oc3-sengine'); ?>.</h1>
                <?php
                }
                
                if($need_pinecone_configure){
                ?>
                
               
                <h1 style="color:red;text-align: center;" id="oc3sengine_need_pineconeconfigure" ><?php echo esc_html__('You need to all of following fields: Embedding model, Pinecone Index before you can use features on this tab', 'oc3-sengine'); ?>.</h1> <h1  style="color:red;text-align: center;" id="oc3sengine_need_pineconeconfigure2"> <?php echo esc_html__('Please open', 'oc3-sengine'); ?>  <a href="<?php echo esc_url(admin_url()) . 'admin.php?page=oc3sengine_settings'; ?>"><?php echo esc_html__('this page', 'oc3-sengine'); ?></a> <?php echo esc_html__(' and make sure that fields Embedding model and Pinecone Index are filled', 'oc3-sengine'); ?>.</h1>
                <?php
                }
                ?>
                <div class="oc3sengine_data_column_container">
                    <div class="oc3sengine_data_column">
                        <div class="oc3sengine_block ">
                            <div style="position:relative;">
                                
                                <div class="oc3sengine_block_header">
                                    <h3><?php esc_html_e('Datasource', 'oc3-sengine'); ?></h3>
                                </div>
                                <?php
                                if(false){
                                ?>
                                <div class="oc3sengine_block_content">
                                            <div class="oc3sengine_row_header">
                                                <label for="oc3sengine_chatbot_chat_model"><?php esc_html_e('Source', 'oc3-sengine'); ?>:</label>
                                            </div>
                                            <div class="oc3sengine_row_content oc3sengine_pr">
                                                <div style="position:relative;">
                                                    <select id="oc3sengine_chatbot_chat_model" name="oc3sengine_chatbot_chat_model">
                                                        <?php
                                                        $pinedatasources = ['Posts & Pages','Manual Entry'];

                                                        foreach ($pinedatasources as $pvalue) {
                                                            
                                                            ?>
                                                            <option value="<?php echo esc_html($pvalue); ?>" ><?php echo esc_html($pvalue); ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <p class="oc3sengine_input_description">
                                                    <span style="display: inline;"><?php esc_html_e('Select model', 'oc3-sengine'); ?></span>
                                                </p>
                                            </div>
                                </div>
                                <?php
                                }
                                ?>
            <div class="oc3sengine_block_content2" >                    

                                        <div class="tablenav-pages">
            <?php
            if ($display_pagination) {
                ?>
                <div class="tablenav top">
                    <div class="alignleft ">
                        <label><?php esc_html_e('Items per page', 'oc3-sengine'); ?>:</label>
                        <select name="rows_per_page" id="rows_per_page" onchange="oc3se_sources_list.changeRowPerPage(this);">
                            <option <?php echo $chatbots_per_page == 10 ? 'selected="selected"' : ''; ?> value="10">10</option>
                            <option  <?php echo $chatbots_per_page == 20 ? 'selected="selected"' : ''; ?>  value="20">20</option>
                            <option  <?php echo $chatbots_per_page == 50 ? 'selected="selected"' : ''; ?>  value="50">50</option>
                            <option  <?php echo $chatbots_per_page == 100 ? 'selected="selected"' : ''; ?>  value="100">100</option>
                        </select>
                        <input type="hidden" id="oc3sengine_page" name="oc3sengine_page" value="1"/>

                    </div>
                </div> 

                <div class="oc3sengine_pagination">
                    <?php
                    echo '<span class="oc3sengine_page_lbl" style=""> ' . esc_html__('Page', 'oc3-sengine') . ':</span>';

                    echo '<span aria-current="page" class="page-numbers current page-numbers2src" >' . esc_html($current_page) . '</span>';
                    echo '<a class="oc3seprevious page-numbers page-numbers2src" href="#" onclick="oc3se_sources_list.prevRowPage(event);" style="display:none;" >&lt;&lt;</a>';
                    if ($current_page * $chatbots_per_page < $source_cnt) {
                        echo '<a class="oc3senext page-numbers page-numbers2src" href="#" style="" onclick="oc3se_sources_list.nextRowPage(event);" >&gt;&gt;</a>';
                    }
                    echo '<span class="oc3sengine_total_rows oc3sengine_totals" style="padding-left:20px;"> ';
                    /* translators: placeholder means total items picked up for source. Normaly they include pages, posts etc. */
                    printf(esc_html__( 'Total: %s items', 'oc3-sengine' ),esc_html($source_cnt));
                    echo '</span>   ';
                    echo '';
                    echo '';
                    ?>    
                </div>

                <?php
            }
            ?>
            <p class="search-box2">
                <span title="clear" id="oc3sengineclear" class="dashicons dashicons-no" onclick="oc3se_sources_list.clearSearch(event);"></span>
                <input type="search" id="oc3sengine_search_source" name="oc3sengine_search" value="<?php echo esc_html($search_string); ?>" onkeyup="oc3se_sources_list.searchRowKeyUp(event);" >
                <input type="button" id="oc3sengine_search_submit" class="button" value="Search" onclick="oc3se_sources_list.loadRowsE(event);">
            </p>
        </div>                    
        <?php
        if (true) {
            ?>
            <div id="oc3sengine_container2" class="  ">

                <table id="oc3sengine_sourceitems" class="wp-list-table widefat fixed striped pages">
                    <thead>

                    <th class="manage-column id_column" style="width: 10%;"><?php esc_html_e('ID', 'oc3-sengine'); ?></th>

                    <th class="manage-column"  style="width: 60%;"><?php esc_html_e('Title', 'oc3-sengine'); ?></th>
                    <th class="manage-column"  style="width: 20%;"><?php esc_html_e('Post type', 'oc3-sengine'); ?></th>
                    <th class="manage-column"  style="width: 10%;"><?php esc_html_e('Actions', 'oc3-sengine'); ?></th>

                    </thead>
                    <tbody id="oc3sengine-rows-list">
                        <?php
                        $js_source_rows = [];
                        $current_row = 0;
                        foreach ($source_rows as $row) {

                            //var_dump($row);

                            $js_source_rows[(int) $row->id] = $row;

                            
                            ?>
                            <tr class="<?php  ?>">
                                <td class="id_column">
                                    <?php
                                    $displayed_id = (int) $row->id;
                                    ?>

                                    <?php
                                    echo esc_html($displayed_id);
                                    ?>

                                </td>
                                <?php ?> 
                                <td>
                                    <a href="<?php echo esc_url($row->post_editurl); ?>" target="blank" id="oc3sengine_bot_href_<?php echo (int) $row->id; ?>">
                                        <?php
                                        echo esc_html($row->post_title);
                                        ?>
                                    </a>


                                </td>
                                <td>
                                    <a href="<?php echo esc_url(get_edit_post_link($row->id,'edit')); ?>" target="blank" id="oc3sengine_bot_href_<?php echo (int) $row->id; ?>">
                                        <?php
                                        echo esc_html($row->post_type);
                                        ?>
                                    </a>


                                </td>

                                <td class="oc3sengine_flags_td">
                                    <?php
                                    
                                    ?>
                                    <span title="edit" class="dashicons dashicons-controls-play"  onclick="oc3se_sources_list.indexRow(event,<?php echo (int) $row->id; ?>,'')" ></span>
                                   

                                </td>


                            </tr>
                            <?php
                            $current_row++;
                            if($current_row >= $chatbots_per_page){
                                break;
                            }
                        }
                        ?>

                    </tbody>
                </table>


                <?php
            }
            ?>            
                
        </div>
                            </div>
                                <?php
                                if(false){
                                ?>
                                        <div class="oc3sengine_block_content">
                                            <div class="oc3sengine_row_header">
                                                <label for="oc3sengine_chatbot_context"><?php esc_html_e('Context', 'oc3-sengine'); ?>:</label>
                                            </div>
                                            <div class="oc3sengine_row_content oc3sengine_pr">
                                                <div style="position: relative;">
                                                    <?php $context = isset($chat_bot_options['context']) ? $chat_bot_options['context'] : ''; ?>
                                                    <textarea id="oc3sengine_chatbot_context" 
                                                              name="oc3sengine_chatbot_context"><?php echo esc_html($context); ?></textarea>
                                                    
                                                </div>
                                                <p class="oc3sengine_input_description">
                                                    <span style="display: inline;">
                                                        <?php esc_html_e('The text that you will write in the Context field will be added to the beginning of the prompt. Note, in case you want to use the default message, you will need to leave the field blank.', 'oc3-sengine'); ?>
                                                    </span>
                                                </p>
                                            </div>
                                        </div>                        


                                <?php
                                }
                                ?>

                                
                            </div>
                        </div>   
                              
                    </div>
                    <div class="oc3sengine_data_column">
                        <div class="oc3sengine_block ">
                            <div style="position:relative;">
                                <div class="oc3sengine_block_header">
                                    <h3><?php esc_html_e('Indexed content', 'oc3-sengine'); ?></h3>
                                </div>

                                <div class="oc3sengine_block_content2" >                    

                                        <div class="tablenav-pages">
            <?php
            if ($display_pagination) {
                ?>
                <div class="tablenav top">
                    <div class="alignleft ">
                        <label><?php esc_html_e('Items per page', 'oc3-sengine'); ?>:</label>
                        <select name="idxrows_per_page" id="idxrows_per_page" onchange="oc3se_indxed_list.changeRowPerPage(this);">
                            <option <?php echo $chunks_per_page == 10 ? 'selected="selected"' : ''; ?> value="10">10</option>
                            <option  <?php echo $chunks_per_page == 20 ? 'selected="selected"' : ''; ?>  value="20">20</option>
                            <option  <?php echo $chunks_per_page == 50 ? 'selected="selected"' : ''; ?>  value="50">50</option>
                            <option  <?php echo $chunks_per_page == 100 ? 'selected="selected"' : ''; ?>  value="100">100</option>
                        </select>
                        <input type="hidden" id="oc3sengineidx_page" name="oc3sengineidx_page" value="1"/>

                    </div>
                </div> 

                <div class="oc3sengine_pagination">
                    <?php
                    echo '<span class="oc3sengineidx_page_lbl" style=""> ' . esc_html__('Page', 'oc3-sengine') . ':</span>';

                    echo '<span aria-current="page" class="page-numbers2 current page-numbers2src2" >' . esc_html($current_page) . '</span>';
                    echo '<a class="oc3seprevious page-numbers2 page-numbers2src2" href="#" onclick="oc3se_indxed_list.prevRowPage(event);" style="display:none;" >&lt;&lt;</a>';
                    if ($current_page * $chunks_per_page < $chunk_cnt) {
                        echo '<a class="oc3senext page-numbers2 page-numbers2src2" href="#" style="" onclick="oc3se_indxed_list.nextRowPage(event);" >&gt;&gt;</a>';
                    }
                    echo '<span class="oc3sengine_total_rows oc3sengine_totals2" style="padding-left:20px;"> ';
                    /* translators: placeholder means count of articles stored into vector database */
                    printf(esc_html__( 'Total: %s items', 'oc3-sengine' ),esc_html($chunk_cnt));
                    echo '</span>   ';
                    echo '';
                    echo '';
                    ?>    
                </div>

                <?php
            }
            ?>
            <p class="search-box2">
                <span title="clear" id="oc3sengineclear" class="dashicons dashicons-no" onclick="oc3se_indxed_list.clearSearch(event);"></span>
                <input type="search" id="oc3sengine_search_indexed" name="oc3sengine_search" value="<?php echo esc_html($search_string); ?>" onkeyup="oc3se_indxed_list.searchRowKeyUp(event);" >
                <input type="button" id="oc3sengine_search_submit2" class="button" value="Search" onclick="oc3se_indxed_list.loadRowsE(event);">
            </p>
        </div>                    
        <?php
        if (true) {
            ?>
            <div id="oc3sengine_container3" class="  ">

                <table id="oc3sengine_indexeditems" class="wp-list-table widefat fixed striped pages">
                    <thead>

                    <th class="manage-column id_column" style="width: 10%;"><?php esc_html_e('ID', 'oc3-sengine'); ?></th>

                    <th class="manage-column"  style="width: 30%;"><?php esc_html_e('Title', 'oc3-sengine'); ?></th>
                    <th class="manage-column"  style="width: 30%;"><?php esc_html_e('Details', 'oc3-sengine'); ?></th>
                    <th class="manage-column"  style="width: 10%;"><?php esc_html_e('Source', 'oc3-sengine'); ?></th>
                    <th class="manage-column"  style="width: 10%;"><?php esc_html_e('Date', 'oc3-sengine'); ?></th>
                    <th class="manage-column"  style="width: 10%;"><?php esc_html_e('Actions', 'oc3-sengine'); ?></th>

                    </thead>
                    <tbody id="oc3sengine-indexed-list">
                        <?php
                        $js_chunk_rows = [];
                        $current_row = 0;
                        //var_dump($chunk_rows);
                        foreach ($chunk_rows as $row) {

                            //var_dump($row);
                            
                            $js_chunk_rows[(int) $row->id] = $row;
   

                            ?>
                            <tr id="oc3se_indexedtblrow<?php echo (int) $row->id; ?>">
                                <td class="id_column">
                                    <?php
                                    $displayed_id = (int) $row->id;
                                    ?>

                                    <?php
                                    echo esc_html($displayed_id);
                                    ?>

                                </td>
                                <?php ?> 
                                <td>
                                    <a href="#" onclick="oc3se_indxed_list.showRowDetails(event,<?php echo (int) $row->id; ?>,'')" id="oc3sengine_bot_href_<?php echo (int) $row->id; ?>">
                                        <?php
                                        echo esc_html($row->title);
                                        ?>
                                    </a>


                                </td>
                                
                                <td>
                                    
                                        <?php
                                        $details = '';
                                        if(isset($row->database) && strlen($row->database) > 0){
                                            $details .= esc_html__('Db', 'oc3-sengine').':'.$row->database.'<br>';
                                        }
                                        if(isset($row->project) && strlen($row->project) > 0){
                                            $details .= esc_html__('Project', 'oc3-sengine').':'.$row->project.'<br>';
                                        }
                                        if(isset($row->dbindex) && strlen($row->dbindex) > 0){
                                            $details .= esc_html__('Index', 'oc3-sengine').':'.$row->dbindex.'<br>';
                                        }
                                        if(isset($row->embedding_model) && strlen($row->embedding_model) > 0){
                                            $details .= esc_html__('Model', 'oc3-sengine').':'.$row->embedding_model.'<br>';
                                        }
                                        if(isset($row->embedding_ai) && strlen($row->embedding_ai) > 0){
                                            $details .= esc_html__('AI', 'oc3-sengine').':'.$row->embedding_ai.'<br>';
                                        }
                                        
                                        if(isset($row->embedding_tokens) && strlen($row->embedding_tokens) > 0){
                                            $details .= 'Tokens:'.$row->embedding_tokens.'<br>';
                                        }
                                        if(isset($row->embedding_cost) && strlen($row->embedding_cost) > 0){
                                            $details .= 'Cost:'.$row->embedding_cost.'<br>';
                                        }
                                        
                                        echo wp_kses($details,['br'=>[]]);
                                        ?>

                                </td>
                                <td>
                                        <?php
                                        if($row->typeof_chunk == 2){
                                            $source = __('Manual', 'oc3-sengine');
                                        }else{
                                            $source = __('Post/Page', 'oc3-sengine').':'.$row->id_sourcepost;
                                        }
                                        echo esc_html($source);
                                        ?>



                                </td>
                                <td>
                                        <?php
                                        echo esc_html($row->dateupdated);
                                        ?>
                                </td>
                                <td class="oc3sengine_flags_td">
                                    <?php
                                   
                                    ?>
                                    
                                    <span title="remove"  class="dashicons dashicons-trash" onclick="oc3se_indxed_list.removeIdxRow(event,'<?php echo esc_html($row->id); ?>')"></span>

                                </td>


                            </tr>
                            <?php
                            $current_row++;
                            if($current_row >= $chunks_per_page){
                                break;
                            }
                        }
                        ?>

                    </tbody>
                </table>


                <?php
            }
            ?>            
                 
        </div>
                            </div>
                                

                                
                            </div>
                        </div> 
                    </div>

                </div>
<?php
}
?>
            
                   
                
                
            </form>
           
         


            
            
        </div>

    </div>
</div>
<div class="oc3seox-overlay" style="display: none">
    <div class="oc3seox_modal">
        <div class="oc3seox_modal_head">
            <span class="oc3seox_modal_title"><?php echo esc_html__('Chunk content', 'oc3-sengine') ?></span>
            <span class="oc3seox_modal_close">&times;</span>
        </div>
        <div class="oc3seox_modal_content"></div>
    </div>
</div>
<div class="oc3seox-overlay-second" style="display: none">
    <div class="oc3seox_modal_second">
        <div class="oc3seox_modal_head_second">
            <span class="oc3seox_modal_title_second"><?php echo esc_html__('Chunk content', 'oc3-sengine') ?></span>
            <span class="oc3seox_modal_close_second">&times;</span>
        </div>
        <div class="oc3seox_modal_content_second"></div>
    </div>
</div>
