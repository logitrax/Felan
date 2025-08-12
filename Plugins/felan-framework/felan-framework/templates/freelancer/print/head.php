<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'freelancer-print');
$freelancer_location = get_the_terms($freelancer_id, 'felan_location');
$freelancer_categories = get_the_terms($freelancer_id, 'freelancer_categories');
$freelancer_resume = wp_get_attachment_url(get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_resume_id_list', true));
$freelancer_featured = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_featured', true);
$freelancer_current_position = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_current_position', true);
$author_id = get_post_field('post_author', $freelancer_id);
$freelancer_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
$freelancer_website = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_website', true);
$offer_salary = !empty(get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_offer_salary')) ? get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_offer_salary')[0] : '';
?>
<div class="block-archive-inner freelancer-head-details">
    <div class="felan-freelancer-header-top">
        <?php if (!empty($freelancer_avatar)) : ?>
            <img class="image-freelancers" src="<?php echo esc_attr($freelancer_avatar) ?>" alt="" />
        <?php else : ?>
            <div class="image-freelancers"><i class="far fa-camera"></i></div>
        <?php endif; ?>
        <div class="info">
            <div class="title-wapper">
                <?php if (!empty(get_the_title($freelancer_id))) : ?>
                    <h1><?php echo get_the_title($freelancer_id); ?></h1>
                    <?php if ($freelancer_featured == 1) : ?>
                        <span class="tooltip" data-title="<?php echo esc_attr('Featured', 'felan-framework') ?>"><i class="far fa-check"></i></span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="freelancer-info">
                <?php if (!empty($freelancer_current_position)) { ?>
                    <div class="freelancer-current-position">
                        <?php esc_html_e($freelancer_current_position); ?>
                    </div>
                <?php } ?>
                <?php if (is_array($freelancer_location)) { ?>
                    <div class="freelancer-warpper">
                        <i class="far fa-map-marker-alt"></i>
                        <?php foreach ($freelancer_location as $location) {
                            $cate_link = get_term_link($location, 'felan_location'); ?>
                            <div class="cate-warpper">
                                <a href="<?php echo esc_url($cate_link); ?>" class="cate felan-link-bottom">
                                    <?php echo $location->name; ?>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if (!empty($offer_salary)) { ?>
                    <div class="freelancer-warpper salary">
                        <i class="far fa-money-bill-alt"></i>
                        <?php felan_get_salary_freelancer($freelancer_id); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php
    $custom_field_freelancer = felan_render_custom_field('freelancer');
    $freelancer_meta_data = get_post_custom($freelancer_id);
    $freelancer_data = get_post($freelancer_id);
    $check_tabs = false;
    foreach ($custom_field_freelancer as $field) {
        if ($field['tabs'] == 'info') {
            $check_tabs = true;
        }
    }

    if (count($custom_field_freelancer) > 0) {
        if ($check_tabs == true) : ?>
            <?php foreach ($custom_field_freelancer as $field) {
                if ($field['tabs'] == 'info') { ?>
            <?php felan_get_template("freelancer/print/additional/field.php", array(
                        'field' => $field,
                        'freelancer_data' => $freelancer_data,
                        'freelancer_meta_data' => $freelancer_meta_data
                    ));
                }
            } ?>
    <?php endif;
    }
    ?>
</div>