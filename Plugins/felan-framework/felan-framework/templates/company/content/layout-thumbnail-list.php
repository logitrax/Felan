<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $wpdb;

$id = get_the_ID();
if (!empty($company_id)) {
    $id = $company_id;
}
$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
$company_meta_data = get_post_custom($id);
$company_location = get_the_terms($company_id, 'felan_location');
$company_categories =  get_the_terms($company_id, 'company-categories');
$company_size =  get_the_terms($company_id,  'company-size');
$company_logo   = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_logo');
$company_item_class[] = 'felan-company-item';
if (!empty($layout)) {
    $company_item_class[] = $layout;
}
$company_item_class[] = 'company-' . $id;
$meta_query = felan_posts_company($id);

$custom_company_image_size = felan_get_option('archive_company_thumbnail_size');
$width = $height = '';
if (preg_match('/\d+x\d+/', $custom_company_image_size)) {
    $attach_id = get_post_thumbnail_id($company_id);
    $image_sizes = explode('x', $custom_company_image_size);
    $width         = $image_sizes[0];
    $height         = $image_sizes[1];
    $image_src      = felan_image_resize_id($attach_id, $width, $height, true);
}

?>
<div class="<?php echo join(' ', $company_item_class); ?>">
    <div class="company-content-wrapper">
        <?php if (has_post_thumbnail()) : ?>
            <div class="company-thumbnail">
                <?php if ($width !== '' & $height !== '') { ?>
                    <img width="<?php echo esc_attr($width) ?>" height="<?php echo esc_attr($height) ?>" src="<?php echo esc_url($image_src) ?>" alt="<?php echo get_the_title($company_id); ?>" />
                <?php } else { ?>
                    <?php echo the_post_thumbnail(); ?>
                <?php } ?>
            </div>
        <?php endif; ?>
        <div class="company-pos">
            <div class="company-top">
                <div class="company-logo">
                    <a class="company-img" href="<?php echo get_the_permalink($company_id); ?>">
                        <?php if (!empty($company_logo[0]['url'])) : ?>
                            <img class="logo-company" src="<?php echo $company_logo[0]['url'] ?>" alt="" />
                        <?php else : ?>
                            <div class="logo-company"><i class="far fa-camera"></i></div>
                        <?php endif; ?>
                    </a>
                </div>
                <div class="company-content">
                    <?php if (!empty(get_the_title($company_id))) : ?>
                        <h2 class="company-title">
                            <a href="<?php echo get_the_permalink($company_id); ?>"><?php echo get_the_title($company_id); ?></a>
                        </h2>
                        <?php felan_company_green_tick($company_id); ?>
                    <?php endif; ?>
                    <div class="company-inner">
                        <?php if (is_array($company_location)) { ?>
                            <div class="company-location">
                                <?php foreach ($company_location as $location) {
                                    $location_link = get_term_link($location, 'company-size'); ?>
                                    <a href="<?php echo esc_url($location_link); ?>" class="cate felan-link-bottom">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12.5599 20.8207C12.2247 21.0598 11.7753 21.0598 11.4401 20.8207C6.61138 17.3773 1.48557 10.2971 6.6667 5.18128C8.08118 3.78463 9.99963 3 12 3C14.0004 3 15.9188 3.78463 17.3333 5.18128C22.5144 10.2971 17.3886 17.3773 12.5599 20.8207Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M12 12C13.1046 12 14 11.1046 14 10C14 8.89543 13.1046 8 12 8C10.8954 8 10 8.89543 10 10C10 11.1046 10.8954 12 12 12Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <?php esc_html_e($location->name); ?>
                                    </a>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php echo felan_get_total_rating('company', $company_id); ?>
                    </div>
                </div>
            </div>
            <div class="company-bottom">
                <?php if (is_array($company_categories)) { ?>
                    <div class="company-cate">
                        <?php foreach ($company_categories as $categories) {
                            $cate_link = get_term_link($categories, 'company-categories'); ?>
                            <a href="<?php echo esc_url($cate_link); ?>" class="label label-categories">
                                <i class="far fa-folder"></i><?php esc_html_e($categories->name); ?>
                            </a>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if ($enable_post_type_jobs == '1') { ?>
                    <?php if ($meta_query->post_count > 0) : ?>
                        <div class="company-available">
                            <span><?php echo $meta_query->post_count; ?></span> <?php esc_html_e('jobs available', 'felan-framework') ?>
                        </div>
                    <?php endif; ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>