<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$freelancer_id = get_the_ID();
if (!empty($freelancer_single_id)) {
    $freelancer_id = $freelancer_single_id;
}
$classes = array();
$custom_freelancer_image_size = felan_get_option('single_freelancer_image_size');
$width = $height = '';
if (preg_match('/\d+x\d+/', $custom_freelancer_image_size)) {
    $attach_id = get_post_thumbnail_id($freelancer_id);
    $image_sizes = explode('x', $custom_freelancer_image_size);
    $width         = $image_sizes[0];
    $height         = $image_sizes[1];
    $image_src      = felan_image_resize_id($attach_id, $width, $height, true);
}

$single_freelancer_style = felan_get_option('single_freelancer_style');
$single_freelancer_style = !empty($_GET['layout']) ? felan_clean(wp_unslash($_GET['layout'])) : $single_freelancer_style;
if ($single_freelancer_style == 'large-cover-img') {
    $classes[] = 'has-large-thumbnail';
}

if (has_post_thumbnail($freelancer_id)) : ?>
    <div class="freelancer-thumbnail-details <?php echo implode(" ", $classes); ?>">
        <div class="container">
            <?php if ($width !== '' & $height !== '') { ?>
                <img width="<?php echo esc_attr($width) ?>" height="<?php echo esc_attr($height) ?>" src="<?php echo esc_url($image_src) ?>" alt="<?php echo get_the_title($freelancer_id); ?>" />
            <?php } else { ?>
                <?php echo get_the_post_thumbnail($freelancer_id); ?>
            <?php } ?>
        </div>
    </div>
<?php endif; ?>