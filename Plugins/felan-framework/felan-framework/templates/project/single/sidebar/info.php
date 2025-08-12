<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$project_id = get_the_ID();
if (!empty($project_single_id)) {
    $project_id = $project_single_id;
}
$author_id = get_post_field('post_author', $project_id);
$company_id = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_select_company', true);
$company_logo = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_logo', true);
$company_featured = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_featured', true);
$company_founded = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_founded');
$company_size = get_the_terms($company_id, 'company-size');
$company_location = get_the_terms($company_id, 'felan_location');
$company_phone = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_phone', true);
$company_email = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_email', true);

$classes = array();
$enable_sticky_sidebar_type = felan_get_option('enable_sticky_project_sidebar_type');
$currency_sign_default = felan_get_option('currency_sign_default');
if ($enable_sticky_sidebar_type) {
    $classes[] = 'has-sticky';
}
if (empty($company_logo)) {
    return;
}
?>
<div class="project-info-sidebar block-archive-sidebar <?php echo implode(" ", $classes); ?>">
    <div class="project-info-warpper">
        <div class="info-inner">
            <?php if (!empty($company_logo)) : ?>
                <img class="image-company" src="<?php echo esc_attr($company_logo['url']) ?>" alt="" />
            <?php else : ?>
                <div class="image-company"><i class="far fa-camera"></i></div>
            <?php endif; ?>
            <div class="inner-right">
                <?php if (!empty(get_the_title($company_id))) : ?>
                    <h4>
                        <a href="<?php echo esc_url(get_permalink($company_id)); ?>">
                            <?php echo get_the_title($company_id); ?>
                            <?php if ($company_featured == 1) : ?>
                                <span class="tooltip" data-title="<?php echo esc_attr('Featured', 'felan-framework') ?>"><i class="far fa-check"></i></span>
                            <?php endif; ?>
                        </a>
                    </h4>
                <?php endif; ?>
                <?php echo felan_get_total_rating('company', $company_id); ?>
            </div>
        </div>
        <div class="info-content company-sidebar">
            <?php if (is_array($company_size)) : ?>
                <div class="info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17 19.5C17 17.8431 14.7614 16.5 12 16.5C9.23858 16.5 7 17.8431 7 19.5M21 16.5004C21 15.2702 19.7659 14.2129 18 13.75M3 16.5004C3 15.2702 4.2341 14.2129 6 13.75M18 9.73611C18.6137 9.18679 19 8.3885 19 7.5C19 5.84315 17.6569 4.5 16 4.5C15.2316 4.5 14.5308 4.78885 14 5.26389M6 9.73611C5.38625 9.18679 5 8.3885 5 7.5C5 5.84315 6.34315 4.5 8 4.5C8.76835 4.5 9.46924 4.78885 10 5.26389M12 13.5C10.3431 13.5 9 12.1569 9 10.5C9 8.84315 10.3431 7.5 12 7.5C13.6569 7.5 15 8.84315 15 10.5C15 12.1569 13.6569 13.5 12 13.5Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="list-cate">
                        <?php foreach ($company_size as $size) {
                            echo $size->name;
                        } ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (is_array($company_location)) { ?>
                <div class="info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.5599 20.8207C12.2247 21.0598 11.7753 21.0598 11.4401 20.8207C6.61138 17.3773 1.48557 10.2971 6.6667 5.18128C8.08118 3.78463 9.99963 3 12 3C14.0004 3 15.9188 3.78463 17.3333 5.18128C22.5144 10.2971 17.3886 17.3773 12.5599 20.8207Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M12 12C13.1046 12 14 11.1046 14 10C14 8.89543 13.1046 8 12 8C10.8954 8 10 8.89543 10 10C10 11.1046 10.8954 12 12 12Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <?php foreach ($company_location as $location) {
                        $cate_link = get_term_link($location, 'company_locations'); ?>
                        <div class="cate-warpper">
                            <a href="<?php echo esc_url($cate_link); ?>" class="cate">
                                <?php echo $location->name; ?>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <?php if ($company_phone) : ?>
                <div class="info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.6 14.5215C13.205 17.0421 7.09582 10.9878 9.49995 8.45753C10.9678 6.91263 9.30963 5.14707 8.3918 3.84934C6.66924 1.41378 2.88771 4.77641 3.00256 6.91544C3.36473 13.6609 10.6615 21.6546 17.7275 20.9574C19.9381 20.7393 22.4778 16.7471 19.9423 15.2882C18.6745 14.5587 16.9342 13.1172 15.6 14.5215Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="details-info"><a href="tel:<?php esc_attr_e($company_phone); ?>"><?php esc_attr_e($company_phone); ?></a>
                    </p>
                </div>
            <?php endif; ?>
            <?php if ($company_email) : ?>
                <div class="info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 12C2 8.22876 2 6.34315 3.46447 5.17157C4.92893 4 7.28595 4 12 4C16.714 4 19.0711 4 20.5355 5.17157C22 6.34315 22 8.22876 22 12C22 15.7712 22 17.6569 20.5355 18.8284C19.0711 20 16.714 20 12 20C7.28595 20 4.92893 20 3.46447 18.8284C2 17.6569 2 15.7712 2 12Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M20.6667 5.31018L15.8412 9.79909C14.0045 11.3296 13.0862 12.0949 12.0001 12.0949C10.9139 12.0949 9.99561 11.3296 8.15897 9.7991L3.3335 5.31018" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="details-info email"><a href="mailto:<?php esc_attr_e($company_email) ?>"><?php esc_attr_e($company_email); ?></a>
                    </p>
                </div>
            <?php endif; ?>
            <div class="button-warpper">
                <a href="<?php echo get_the_permalink($company_id); ?>" class="felan-button button-outline">
                    <?php esc_html_e('View profile', 'felan-framework'); ?>
                </a>
                <?php felan_get_template('project/messages.php', array(
                    'company_id' => $company_id,
                )); ?>
            </div>
        </div>
    </div>
</div>