<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$service_id = get_the_ID();
if (!empty($services_id)) {
    $service_id = $services_id;
}
$freelancer_id = felan_id_service_to_freelancer($service_id);
$enable_service_des = felan_get_option('enable_service_show_des');
$currency_sign_default = felan_get_option('currency_sign_default');
$currency_position = felan_get_option('currency_position');
$enable_service_review = felan_get_option('enable_single_service_review', '1');
$enable_service_single_popup = felan_get_option('enable_service_single_popup', '0');
$enable_service_single_popup = !empty($_GET['has_popup']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_popup'])) : $enable_service_single_popup;

$author_id = get_post_field('post_author', $service_id);
$freelancer_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
$service_featured  = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_featured', true);
$service_item_class[] = 'felan-service-item';
if (!empty($layout)) {
    $service_item_class[] = $layout;
}
if ($service_featured == 1) {
    $service_item_class[] = 'felan-service-featured';
}
$service_item_class[] = 'service-' . $service_id;

$number_delivery_time = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_basic_time', true);
$number_start_price = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_basic_price', true);
$freelancer_current_position = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_current_position', true);
if ($currency_position == 'before') {
    $price = $currency_sign_default . $number_start_price;
} else {
    $price = $number_start_price . $currency_sign_default;
}
if (has_post_thumbnail($service_id)) {
    $thumbnail_url = get_the_post_thumbnail_url($service_id);
} else {
    $thumbnail_url = FELAN_PLUGIN_URL . 'assets/images/no-image.jpg';
}

if (!empty($custom_service_image_size)) {
    $image_sizes          = explode('x', $custom_service_image_size);
    $width                = $image_sizes[0];
    $height               = $image_sizes[1];
    $image_crop = felan_image_resize_url($thumbnail_url, $width, $height);
    if (!is_wp_error($image_crop)) {
        $thumbnail_url = $image_crop['url'];
    } else {
        error_log($image_crop->get_error_message());
        $thumbnail_url = '';
    }
}
?>
<div class="<?php echo join(' ', $service_item_class); ?>">
    <div class="service-thumbnail">
        <a href="<?php echo get_the_permalink($service_id); ?>">
            <img src="<?php echo $thumbnail_url; ?>" alt="<?php echo get_the_title($service_id); ?>">
        </a>
        <?php if ($service_featured == 1) : ?>
            <span class="featured">
                <?php echo esc_html__('Featured', 'felan-framework') ?>
            </span>
        <?php endif; ?>
        <div class="service-status-inner">
            <?php felan_get_template('service/wishlist.php', array(
                'service_id' => $service_id,
            )); ?>
        </div>
    </div>
    <div class="service-content">
        <div class="service-author">
            <?php if (!empty($freelancer_avatar)) : ?>
                <a href="<?php echo esc_url(get_permalink($freelancer_id)); ?>">
                    <img class="image-freelancers" src="<?php echo esc_attr($freelancer_avatar) ?>" alt="" />
                </a>
            <?php else : ?>
                <div class="image-freelancers"><i class="far fa-camera"></i></div>
            <?php endif; ?>
            <?php if (!empty(get_the_title($freelancer_id))) : ?>
                <a href="<?php echo esc_url(get_permalink($freelancer_id)); ?>">
                    <span><?php echo get_the_title($freelancer_id); ?></span>
                </a>
            <?php endif; ?>
        </div>
        <div class="service-header">
            <div class="service-info">
                <?php if (!empty(get_the_title($service_id))) : ?>
                    <h2 class="service-title">
                        <a href="<?php echo get_the_permalink($service_id); ?>"><?php echo get_the_title($service_id); ?></a>
                    </h2>
                <?php endif; ?>
            </div>
        </div>

        <div class="service-bottom">
            <?php if ($enable_service_review) : ?>
                <?php echo felan_get_total_rating('service', $service_id); ?>
            <?php endif; ?>
            <?php if (!empty($price)) : ?>
                <div class="price-inner">
                    <span><?php esc_html_e('From', 'felan-framework') ?></span>
                    <span class="price"><?php echo $price; ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php if ($enable_service_single_popup === '1' && is_archive()) { ?>
        <a class="felan-link-item btn-single-settings" data-post-id="<?php echo esc_attr($service_id) ?>" data-post-type="service" href="#"></a>
    <?php } ?>
</div>