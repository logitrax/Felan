<?php

/**
 * The Template for displaying all single freelancer
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


get_header('felan');

/**
 * @Hook: felan_single_freelancer_before
 *
 * @hooked gallery_freelancer
 */
do_action('felan_single_freelancer_before');

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
 * @Hook: felan_single_freelancer_hero
 *
 * @hooked felan_single_freelancer_hero
 */
do_action('felan_single_freelancer_hero');
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

    <?php felan_get_template_part('content', 'single-freelancer'); ?>

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
 * @hooked felan_sidebar_freelancer
 */
do_action('felan_freelancer_sidebar');
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
 * @Hook: felan_single_freelancer_after
 *
 * @hooked related_freelancer
 */
do_action('felan_single_freelancer_after');

get_footer('felan');
