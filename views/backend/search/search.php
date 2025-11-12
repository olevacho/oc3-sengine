<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>



<div class="oc3sengine_container">
    <div id="oc3sengine_searchconfigtabs">


        <ul>
            <li><a href="#oc3sengine-tabs-1"><?php echo esc_html__('Default', 'oc3-sengine') ?></a></li>
           
            
        </ul>
        <?php
        include OC3SENGINE_PATH . '/views/backend/search/search_default.php';
        ?>
        <div class="oc3sengine_bloader">
            <div style="padding: 1em 1.4em;"></div>


        </div>

    </div>
</div>