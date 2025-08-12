<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_style('lightgallery');
wp_enqueue_script('lightgallery');
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'light-gallery');

$company_id = get_the_ID();
if (!empty($company_single_id)) {
    $company_id = $company_single_id;
}
$company_gallery = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_images', true);
$company_gallery = explode('|', $company_gallery);
?>
<?php if (!empty($company_gallery)) : ?>
    <div class="block-archive-inner company-gallery-details">
        <div class="entry-company-element">
            <div class="single-company-thumbs felan-light-gallery">
                <div class="row">
                    <?php foreach ($company_gallery as $key => $image) :
                        if ($image) {
                            $image_full_src = wp_get_attachment_image_src($image, 'full');
                            if (isset($image_full_src[0])) {
                                $thumb_src = $image_full_src[0];
                            }
                        }
                        if (!empty($thumb_src)) {
                    ?>
                            <div class="felan-photo-item col-md-4 col-sm-6">
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
    </div>
<?php else: ?>
    <p class="noti-collections"><?php echo esc_html__('You do not have any collections yet','felan-framework'); ?></p>
<?php endif; ?>