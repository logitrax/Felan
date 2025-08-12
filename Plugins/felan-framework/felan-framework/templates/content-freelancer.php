<?php

/**
 * The Template for displaying content freelancer
 */

defined('ABSPATH') || exit;

$content_freelancer = felan_get_option('archive_company_layout', 'layout-list');
$content_button_type = '';
$etw = 0;

if (!empty($freelancer_layout)) {
    $content_freelancer = $freelancer_layout;
}

if (!empty($button_type)) {
    $content_button_type = $button_type;
}

if (!empty($excerpt_trim_words)) {
    $etw = $excerpt_trim_words;
}

$id = $image_size = '';

$id = get_the_ID();

if (!empty($freelancer_id)) {
    $id = $freelancer_id;
}

if (!empty($custom_freelancer_image_size)) {
    $image_size = $custom_company_image_size;
}

$effect_class = 'skeleton-loading';

felan_get_template('freelancer/content/' . $content_freelancer . '.php', array(
    'freelancer_id'                => $id,
    'custom_freelancer_image_size' => $image_size,
    'layout'                      => $content_freelancer,
    'content_button_type'         => $content_button_type,
    'etw'                           => $etw,
    'effect_class'                => $effect_class,
));
