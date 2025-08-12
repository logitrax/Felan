<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$service_id = get_the_ID();
if (!empty($service_single_id)) {
    $service_id = $service_single_id;
}
$service_gallery  = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_images', true);
$attach_id   = get_post_thumbnail_id();
$show = 4;
$slick_attributes = array(
    '"slidesToShow": 1',
    '"slidesToScroll": 1',
    '"fade": true',
    '"asNavFor": ".slick-nav"',
);
$wrapper_attributes[] = "data-slick='{" . implode(', ', $slick_attributes) . "}'";

$slick_nav = array(
    '"slidesToShow": ' . $show,
    '"slidesToScroll": 1',
    '"focusOnSelect": true',
    '"arrows": false',
    '"asNavFor": ".slick-for"',
);
$nav_attributes[] = "data-slick='{" . implode(', ', $slick_nav) . "}'";

$felan_service_gallery = explode('|', $service_gallery);
$count = count($felan_service_gallery);
?>
<?php if (!empty($service_gallery)) : ?>
    <div class="service-gallery-details">
        <div class="entry-service-element">
            <div class="single-service-thumbs enable">
                <div class="felan-slick-carousel slick-for">
                    <?php foreach ($felan_service_gallery as $key => $image) :
                        if ($image) {
                            $image_full_src = wp_get_attachment_image_src($image, 'full');
                            if (isset($image_full_src[0])) {
                                $thumb_src      = $image_full_src[0];
                            }
                        }
                        if (!empty($thumb_src)) {
                    ?>
                            <figure>
                                <a href="<?php echo esc_url($thumb_src); ?>">
                                    <img src="<?php echo esc_url($thumb_src); ?>" alt="<?php the_title_attribute(); ?>" title="<?php the_title_attribute(); ?>">
                                </a>
                            </figure>
                        <?php } ?>
                    <?php endforeach; ?>
                </div>

                <div class="felan-slick-carousel slick-nav" <?php echo implode(' ', $nav_attributes); ?>>
                    <?php foreach ($felan_service_gallery as $key => $image) :
                        if ($image) {
                            $image_full_src = wp_get_attachment_image_src($image, 'full');
                            if (isset($image_full_src[0])) {
                                $thumb_src      = $image_full_src[0];
                            }
                        }
                        if (!empty($thumb_src)) {
                    ?>
                            <figure class="felan-image-nav">
                                <span>
                                    <img src="<?php echo esc_url($thumb_src); ?>" alt="<?php the_title_attribute(); ?>" title="<?php the_title_attribute(); ?>">
                                </span>
                            </figure>
                        <?php } ?>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </div>
<?php endif; ?>