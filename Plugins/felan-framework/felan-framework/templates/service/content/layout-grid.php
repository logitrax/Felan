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
if ($currency_position == 'before') {
    $price = $currency_sign_default . felan_get_format_number( $number_start_price );
} else {
    $price = felan_get_format_number( $number_start_price ) . $currency_sign_default;
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
			<a href="<?php echo esc_url(get_permalink($freelancer_id)); ?>">
            	<?php if (!empty($freelancer_avatar)) : ?>
                    <img class="image-freelancers" src="<?php echo esc_attr($freelancer_avatar) ?>" alt="" />
				<?php endif; ?>
                <span><?php echo get_the_title($freelancer_id); ?></span>
            </a>
        </div>
        <?php if (!empty(get_the_title($service_id))) : ?>
            <h2 class="service-title">
                <a href="<?php echo get_the_permalink($service_id); ?>"><?php echo get_the_title($service_id); ?></a>
            </h2>
        <?php endif; ?>
        <div class="service-meta">
            <?php if ($enable_service_review) : ?>
                <?php echo felan_get_total_rating('service', $service_id); ?>
            <?php endif; ?>
            <div class="count-sales">
                <i class="fal fa-shopping-basket"></i>
                <?php echo felan_service_count_sale($author_id,$service_id); ?>
            </div>
            <?php felan_total_view_service_details($service_id); ?>
        </div>
        <div class="service-bottom">
            <?php if (!empty($price)) : ?>
                <div class="price-inner">
                    <span><?php esc_html_e('From', 'felan-framework') ?></span>
                    <span class="price"><?php echo $price; ?></span>
                </div>
            <?php endif; ?>
            <?php if (!empty(felan_service_delivery_time($service_id, $number_delivery_time))) : ?>
                <div class="delivery tooltip" data-title="<?php esc_attr_e('Delivery time', 'felan-framework') ?>">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 4.5V9H12.375" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="9" cy="9" r="6.75" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <?php echo sprintf(esc_html__('%s', 'felan-framework'), felan_service_delivery_time($service_id, $number_delivery_time)) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php if ($enable_service_single_popup === '1' && is_archive()) { ?>
        <a class="felan-link-item btn-single-settings" data-post-id="<?php echo esc_attr($service_id) ?>" data-post-type="service" href="#"></a>
    <?php } ?>
</div>