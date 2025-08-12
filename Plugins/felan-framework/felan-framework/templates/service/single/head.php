<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$service_id = get_the_ID();
if (!empty($service_single_id)) {
    $service_id = $service_single_id;
}
$author_id = get_post_field('post_author', $service_id);
$freelancer_id = felan_id_service_to_freelancer($service_id);
$freelancer_current_position = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_current_position', true);
$freelancer_featured = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_featured', true);
$service_featured  = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_featured', true);
$enable_service_review = felan_get_option('enable_single_service_review', '1');
?>
<div class="container">
    <div class="service-head-details">
        <div class="head-left">
            <?php get_template_part('templates/global/breadcrumb'); ?>
            <h1>
                <?php echo get_the_title($service_id); ?>
                <?php if ($service_featured == '1') : ?>
                    <span class="felan-label-yellow"><?php echo esc_html__('Featured', 'felan-framework'); ?></span>
                <?php endif; ?>
            </h1>
            <div class="info">
                <div class="info-left">
                    <div class="info-inner">
                        <?php if (!empty(get_the_title($freelancer_id))) : ?>
                            <h4 class="title">
                                <span class="by"><?php esc_html_e('by', 'felan-framework'); ?></span>
                                <a href="<?php echo esc_url(get_permalink($freelancer_id)); ?>">
                                    <?php echo get_the_title($freelancer_id); ?>
                                </a>
                            </h4>
                            <?php if ($freelancer_featured == 1) : ?>
                                <span class="tooltip" data-title="<?php echo esc_attr('Featured', 'felan-framework') ?>"><i class="far fa-check"></i></span>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if ($enable_service_review) : ?>
                            <?php echo felan_get_total_rating('service', $service_id,true); ?>
                        <?php endif; ?>
                       <div class="count-sales">
                           <i class="fal fa-shopping-basket"></i>
                           <?php echo felan_service_count_sale($author_id,$service_id); ?>
                       </div>
                        <?php felan_total_view_service_details($service_id); ?>
                        <span class="date">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 6V12L16 14" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <circle cx="12" cy="12" r="9" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span class="time"><?php echo get_the_date(get_option('date_format'), $service_id); ?></span>
                        </span>
                    </div>
                </div>
                <div class="info-right">
                    <?php felan_get_template('service/wishlist.php', array(
                        'service_id' => $service_id,
                    )); ?>
                    <div class="toggle-social">
                        <a href="#" class="jobs-share btn-share tooltip" data-title="<?php esc_attr_e('Share', 'felan-framework') ?>">
                            <i class="far fa-share-alt"></i>
                        </a>
                        <?php felan_get_template('global/social-share.php', array(
                            'post_id' => $service_id,
                        )); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>