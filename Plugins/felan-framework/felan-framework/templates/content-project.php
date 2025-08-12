<?php

/**
 * The Template for displaying content project
 */

defined('ABSPATH') || exit;

wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'login-to-view');
$content_project = felan_get_option('archive_project_layout', 'layout-list');

if (!empty($project_layout)) {
    $content_project = $project_layout;
}

$id = $image_size = '';

$id = get_the_ID();

if (!empty($projects_id)) {
    $id = $projects_id;
}

if (!empty($custom_project_image_size)) {
    $image_size = $custom_project_image_size;
}

$effect_class = 'skeleton-loading';
felan_get_template('project/content/' . $content_project . '.php', array(
    'projects_id'                => $id,
    'custom_project_image_size' => $image_size,
    'layout'                  => $content_project,
    'effect_class'            => $effect_class,
));
