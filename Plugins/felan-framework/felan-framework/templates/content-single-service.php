<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$service_single_id = get_the_ID();
if (!empty($post_id)) {
    $service_single_id = $post_id;
}

$classes = array('felan-service-wrap', 'single-service-area');

?>
<div id="service-<?php the_ID(); ?>" <?php post_class($classes); ?>>
    <div class="block-service-warrper">
        <?php
        /**
         * Hook: felan_single_service_after_summary hook.
         */
        do_action('felan_single_service_after_summary');

        /**
         * Hook: felan_single_service_summary hook.
         */
        do_action('felan_single_service_summary', $service_single_id);

        /**
         * Hook: felan_after_content_single_service_summary hook.
         */
        do_action('felan_after_content_single_service_summary'); ?>
    </div>
</div>