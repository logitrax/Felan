<?php

/**
 * The Template for displaying content jobs
 */

defined('ABSPATH') || exit;

wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'login-to-view');
$content_jobs = felan_get_option('archive_jobs_layout', 'layout-list');
$content_jobs = !empty($_GET['layout']) ? Felan_Helper::felan_clean(wp_unslash($_GET['layout'])) : $content_jobs;

if (!empty($jobs_layout)) {
    $content_jobs = $jobs_layout;
}

$id = $image_size = '';

$id = get_the_ID();

if (!empty($jobs_id)) {
    $id = $jobs_id;
}

if (!empty($custom_jobs_image_size)) {
    $image_size = $custom_jobs_image_size;
}

$effect_class = 'skeleton-loading';

felan_get_template('jobs/content/' . $content_jobs . '.php', array(
    'jobs_id'                => $id,
    'custom_jobs_image_size' => $image_size,
    'layout'                  => $content_jobs,
    'effect_class'            => $effect_class,
));
