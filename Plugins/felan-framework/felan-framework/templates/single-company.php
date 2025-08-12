<?php

/**
 * The Template for displaying all single company
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header('felan');

/**
 * @Hook: felan_single_company_before
 *
 * @hooked single_company_thumbnail
 */
do_action('felan_single_company_before');

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
 * @Hook: felan_output_content_wrapper_start
 *
 * @hooked output_content_wrapper_start
 */
do_action('felan_output_content_wrapper_start');
?>

<?php while (have_posts()) : the_post(); ?>

    <?php felan_get_template_part('content', 'single-company'); ?>

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
 * @hooked felan_sidebar_company
 */
do_action('felan_sidebar_company');
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
 * @Hook: felan_single_company_after
 *
 * @hooked related_company
 */
do_action('felan_single_company_after');

get_footer('felan');
