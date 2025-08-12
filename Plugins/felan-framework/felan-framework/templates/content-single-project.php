<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$project_single_id = get_the_ID();
if (!empty($post_id)) {
    $project_single_id = $post_id;
}

$classes = array('felan-project-wrap', 'single-project-area');

?>
<div id="project-<?php the_ID(); ?>" <?php post_class($classes); ?>>
    <div class="block-project-warrper">
        <?php
        /**
         * Hook: felan_single_project_after_summary hook.
         */
        do_action('felan_single_project_after_summary');

        /**
         * Hook: felan_single_project_summary hook.
         */
        do_action('felan_single_project_summary', $project_single_id);

        /**
         * Hook: felan_after_content_single_project_summary hook.
         */
        do_action('felan_after_content_single_project_summary'); ?>
    </div>
</div>