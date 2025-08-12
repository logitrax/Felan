<?php

/**
 * The Template for displaying all single jobs
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header('felan');

/**
 * @Hook: felan_single_jobs_before
 *
 * @hooked gallery_jobs
 */
do_action('felan_single_jobs_before');

?>

<?php
/**
 * @Hook: felan_layout_wrapper_start
 *
 * @hooked layout_wrapper_start
 */
do_action('felan_layout_wrapper_start');
?>


<?php
/**
 * @Hook: felan_output_content_jobs_wrapper_start
 *
 * @hooked output_content_wrapper_start
 */
do_action('felan_output_content_jobs_wrapper_start');
?>

<?php while (have_posts()) : the_post(); ?>

    <?php felan_get_template_part('content', 'single-jobs'); ?>

<?php endwhile; // end of the loop.
?>

<?php
/**
 * @Hook: felan_output_content_wrapper_end
 *
 * @hooked output_content_wrapper_end
 */
do_action('felan_output_content_wrapper_end');
?>

<?php

/**
 * @hooked felan_sidebar_jobs
 */
do_action('felan_sidebar_jobs');

?>

<?php
/**
 * @Hook: felan_layout_wrapper_end
 *
 * @hooked layout_wrapper_end
 */
do_action('felan_layout_wrapper_end');
?>

<?php
/**
 * @Hook: felan_single_jobs_after
 *
 * @hooked related_jobs
 */
do_action('felan_single_jobs_after');

get_footer('felan');
