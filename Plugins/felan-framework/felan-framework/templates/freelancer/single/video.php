<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_style('lity');
wp_enqueue_script('lity');
$freelancer_id = get_the_ID();
if (!empty($freelancer_single_id)) {
    $freelancer_id = $freelancer_single_id;
}

$freelancer_meta_data = get_post_custom($freelancer_id);

$freelancer_video_url   = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_video_url']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_video_url'][0] : '';
$freelancer_video_image = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_video_image']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_video_image'][0] : '';
?>
<?php if (!empty($freelancer_video_url)) : ?>
    <div class="block-archive-inner freelancer-single-field">
        <div class="entry-freelancer-element">
            <div class="entry-thumb-wrap">
                <?php if (wp_oembed_get($freelancer_video_url)) : ?>
                    <?php
                    $image_src = felan_image_resize_id($freelancer_video_image, 870, 420, true);
                    $width = '870';
                    $height = '420';
                    if (!empty($image_src)) : ?>
                        <div class="entry-thumbnail">
                            <img class="img-responsive" src="<?php echo esc_url($image_src); ?>" width="<?php echo esc_attr($width) ?>" height="<?php echo esc_attr($height) ?>" alt="<?php the_title_attribute(); ?>" />
                            <a class="view-video" href="<?php echo esc_url($freelancer_video_url); ?>" data-lity><i class="far fa-play-circle icon-large"></i></a>
                        </div>
                    <?php else : ?>
                        <div class="embed-responsive embed-responsive-16by9 embed-responsive-full">
                            <?php echo wp_oembed_get($freelancer_video_url, array('wmode' => 'transparent')); ?>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="embed-responsive embed-responsive-16by9 embed-responsive-full">
                        <?php echo wp_kses_post($freelancer_video_url); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>