<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$project_id = get_the_ID();
if (!empty($project_single_id)) {
    $project_id = $project_single_id;
}
$project_gallery  = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_images', true);
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

$felan_project_gallery = explode('|', $project_gallery);
$count = count($felan_project_gallery);
?>
<?php if (!empty($project_gallery)) : ?>
    <div class="project-gallery-details">
        <div class="entry-project-element">
            <div class="single-project-thumbs enable">
                <div class="felan-slick-carousel slick-for">
                    <?php foreach ($felan_project_gallery as $key => $image) :
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
                    <?php foreach ($felan_project_gallery as $key => $image) :
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