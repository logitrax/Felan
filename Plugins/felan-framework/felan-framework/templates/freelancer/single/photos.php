<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_style('lightgallery');
wp_enqueue_script('lightgallery');
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'light-gallery');
wp_enqueue_script('slick');

$freelancer_id = get_the_ID();
if (!empty($freelancer_single_id)) {
    $freelancer_id = $freelancer_single_id;
}
$freelancer_galleries = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_galleries', true);
$felan_freelancer_galleries = explode('|', $freelancer_galleries);
$count = count($felan_freelancer_galleries);
?>
<?php if (!empty($freelancer_galleries)) : ?>
    <div class="block-archive-inner freelancer-gallery-details" data-count="<?php echo esc_attr($count); ?>">
        <div class="entry-freelancer-element">
            <div class="single-freelancer-portfolio enable felan-light-gallery row">
                <?php foreach ($felan_freelancer_galleries as $key => $image) :
                    if ($image) {
                        $image_full_src = wp_get_attachment_image_src($image, 'full');
                        if (isset($image_full_src[0])) {
                            $thumb_src      = $image_full_src[0];
                        }
                    }
                    if (!empty($thumb_src)) {
                ?>
                        <div class="felan-freelancer-portfolio-item col-md-4 col-sm-6">
                            <figure>
                                <a href="<?php echo esc_url($thumb_src); ?>" class="lgbox">
                                    <img src="<?php echo esc_url($thumb_src); ?>" alt="<?php the_title_attribute(); ?>" title="<?php the_title_attribute(); ?>">
                                </a>
                            </figure>
                        </div>
                    <?php } ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <p class="noti-collections"><?php echo esc_html__('You do not have any collections yet','felan-framework'); ?></p>
<?php endif; ?>