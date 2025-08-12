    <?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$freelancer_id = get_the_ID();
if (!empty($freelancer_single_id)) {
    $freelancer_id = $freelancer_single_id;
}
$freelancer_project = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_project_list', false);
$freelancer_project = !empty($freelancer_project) ? $freelancer_project[0] : '';
if (empty($freelancer_project[0][FELAN_METABOX_PREFIX . 'freelancer_project_image_id']['url'])) {
    return;
}
$show = 2;
?>

<div class="block-archive-inner freelancer-project-details">
    <h4 class="title-freelancer"><?php esc_html_e('Portfolio', 'felan-framework') ?></h4>
    <div class="entry-freelancer-element">
        <div class="single-freelancer-thumbs enable">
            <?php
            $slick_attributes = array(
                '"slidesToShow": ' . $show,
                '"slidesToScroll": 1',
                '"dots": true',
                '"autoplay": false',
                '"autoplaySpeed": 5000',
                '"responsive": [{ "breakpoint": 479, "settings": {"slidesToShow": 1} },{ "breakpoint": 768, "settings": {"slidesToShow": 2}} ]'
            );
            $wrapper_attributes[] = "data-slick='{" . implode(', ', $slick_attributes) . "}'";
            ?>
            <div class="felan-slick-carousel slick-nav" <?php echo implode(' ', $wrapper_attributes); ?>>
                <?php
                foreach ($freelancer_project as $project) :
                    $thumb_src = $project[FELAN_METABOX_PREFIX . 'freelancer_project_image_id']['url'];
                    if (!empty($project[FELAN_METABOX_PREFIX . 'freelancer_project_link'])) {
                        $project_link = $project[FELAN_METABOX_PREFIX . 'freelancer_project_link'];
                    } else {
                        $project_link = '#';
                    }
                ?>
                    <?php if (!empty($project[FELAN_METABOX_PREFIX . 'freelancer_project_image_id']['url'])) : ?>
                        <figure>
                            <a href="<?php echo esc_url($project_link); ?>" target="_blank" class="project">
                                <img src="<?php echo esc_url($thumb_src); ?>" alt="<?php the_title_attribute(); ?>" title="<?php the_title_attribute(); ?>">
                                <div class="content-project">
                                    <?php if (!empty($project[FELAN_METABOX_PREFIX . 'freelancer_project_title'])) : ?>
                                        <h4><?php echo $project[FELAN_METABOX_PREFIX . 'freelancer_project_title']; ?></h4>
                                    <?php endif; ?>
                                    <div class="project-inner">
                                        <?php if (!empty($project[FELAN_METABOX_PREFIX . 'freelancer_project_description'])) : ?>
                                            <p><?php echo $project[FELAN_METABOX_PREFIX . 'freelancer_project_description']; ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($project[FELAN_METABOX_PREFIX . 'freelancer_project_title'])) : ?>
                                            <span class="felan-button button-border-bottom"><?php esc_html_e('View Portfolio', 'felan-framework') ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        </figure>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php felan_custom_field_single_freelancer('projects'); ?>
</div>