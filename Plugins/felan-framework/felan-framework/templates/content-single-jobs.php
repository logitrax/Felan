<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $post;
$id = get_the_ID();
if (!empty($post_id)) {
    $id = $post_id;
}

$classes = array('felan-jobs-wrap', 'single-jobs-area');

?>
<div id="jobs-<?php the_ID(); ?>" <?php post_class($classes); ?>>
    <div class="block-jobs-warrper">
        <div class="block-archive-top">
            <?php
            /**
             * Hook: felan_single_jobs_after_summary hook.
             */
            do_action('felan_single_jobs_after_summary'); ?>
            <?php
            /**
             * Hook: felan_single_jobs_summary hook.
             */
            do_action('felan_single_jobs_summary', $id);
            ?>
        </div>
        <?php
        /**
         * Hook: felan_after_content_single_jobs_summary hook.
         */
        do_action('felan_after_content_single_jobs_summary');
        ?>
        <?php
        /**
         * Hook: felan_apply_single_jobs hook.
         */
        do_action('felan_apply_single_jobs', $id);
        ?>
    </div>
</div>