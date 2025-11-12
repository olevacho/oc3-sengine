<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>



<div class="oc3sengine_container">
    <div id="oc3sengine_configtabs">


        <ul>
            <li><a href="#oc3sengine-tabs-1"><?php echo esc_html__('General', 'oc3-sengine') ?></a></li>
            <li><a href="#oc3sengine-tabs-2"><?php echo esc_html__('Pinecone', 'oc3-sengine') ?></a></li>
            
        </ul>
        <?php
        include OC3SENGINE_PATH . '/views/backend/config_gpt_general.php';
        include OC3SENGINE_PATH . '/views/backend/config_pinecone.php';

        ?>
        <div class="oc3sengine_bloader">
            <div style="padding: 1em 1.4em;"></div>


        </div>

    </div>
</div>



