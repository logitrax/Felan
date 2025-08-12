<?php

/**
 * The Template for displaying all single project
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header('felan');

/**
 * @Hook: felan_single_project_before
 *
 * @hooked single_project_thumbnail
 */
do_action('felan_single_project_before');

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
 * @Hook: felan_output_content_project_wrapper_start
 *
 * @hooked output_content_wrapper_start
 */
do_action('felan_output_content_project_wrapper_start');
?>


<?php while (have_posts()) : the_post(); ?>

    <?php felan_get_template_part('content', 'single-project'); ?>

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
 * @hooked felan_sidebar_project
 */
do_action('felan_sidebar_project');
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
 * @Hook: felan_single_project_after
 *
 * @hooked related_project
 */
do_action('felan_single_project_after');

get_footer('felan');
