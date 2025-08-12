<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$freelancer_galleries     = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_galleries', true);
$attach_id         = get_post_thumbnail_id($freelancer_id);
?>
<?php if (!empty($freelancer_galleries)) : ?>
    <div class="block-archive-inner freelancer-gallery-details">
        <h4 class="title-freelancer"><?php esc_html_e('Photos', 'felan-framework') ?></h4>
        <div class="entry-freelancer-element">
            <div class="row">
                <?php
                $felan_freelancer_galleries = explode('|', $freelancer_galleries);
                $count = count($felan_freelancer_galleries);
                foreach ($felan_freelancer_galleries as $key => $image) :
                    if ($image) {
                        $image_full_src = wp_get_attachment_image_src($image, 'full');
                        if (isset($image_full_src[0])) {
                            $thumb_src      = $image_full_src[0];
                        }
                    }
                    if (!empty($thumb_src)) {
                ?>
                        <div class="col-4">
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
<?php endif; ?>