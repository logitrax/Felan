<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $wpdb;

$id = get_the_ID();
if (!empty($jobs_id)) {
    $id = $jobs_id;
}
$jobs_meta_data = get_post_custom($id);
$jobs_type = get_the_terms($jobs_id, 'jobs-type');
$jobs_location = get_the_terms($jobs_id, 'felan_location');
$jobs_location_city = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_location_city', true);
$jobs_categories = get_the_terms($jobs_id, 'jobs-categories');
$jobs_select_company = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_select_company');
$company_id = isset($jobs_select_company[0]) ? $jobs_select_company[0] : '';
$company_logo = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_logo');

$jobs_salary_active   = felan_get_option('enable_single_jobs_salary', '1');
$jobs_salary_show = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_salary_show', true);
$jobs_salary_rate = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_salary_rate', true);
$jobs_salary_minimum = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_salary_minimum', true);
$jobs_salary_maximum = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_salary_maximum', true);
$jobs_maximum_price = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_maximum_price', true);
$jobs_minimum_price = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_minimum_price', true);
$jobs_currency_type = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_currency_type', true);

$jobs_item_class[] = 'felan-jobs-item';
if (!empty($layout)) {
    $jobs_item_class[] = $layout;
}
if (felan_get_expiration_apply($jobs_id) !== 0) {
    $jobs_featured = isset($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_featured']) ? $jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_featured'][0] : '0';
    if ($jobs_featured == '1') {
        $jobs_item_class[] = 'felan-jobs-featured';
    } else {
        $enable_status_urgent = felan_get_option('enable_status_urgent', '1');
        $number_status_urgent = felan_get_option('number_status_urgent', '3');
        if ($number_status_urgent > felan_get_expiration_apply($jobs_id) && $enable_status_urgent == '1' && $number_status_urgent !== '') {
            $jobs_item_class[] = 'felan-jobs-urgent';
        }
    }
}

$jobs_item_class[] = 'jobs-' . $id;
$enable_jobs_des = felan_get_option('enable_jobs_show_des');
$enable_jobs_single_popup = felan_get_option('enable_jobs_single_popup', '0');
$enable_jobs_single_popup = !empty($_GET['has_popup']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_popup'])) : $enable_jobs_single_popup;
$expiration_apply = felan_get_expiration_apply($jobs_id);
?>
<div class="<?php echo join(' ', $jobs_item_class); ?>" data-jobid="<?php echo esc_attr($id); ?>">
    <div class="jobs-archive-header">
        <div class="jobs-logo-company">
            <div class="logo-wrapper">
                <?php if (!empty($company_logo[0]['url'])) : ?>
                    <img class="logo-company" src="<?php echo $company_logo[0]['url'] ?>" alt="" />
                <?php else : ?>
                    <div class="logo-company"><i class="far fa-camera"></i></div>
                <?php endif; ?>
                <div class="info-company">
                    <?php if (!empty($company_id)) : ?>
                        <a class="authour" href="<?php echo get_post_permalink($company_id) ?>"><?php echo get_the_title($company_id); ?></a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="jobs-status-inner">
                <span class="jobs-status"><?php echo felan_get_icon_status($jobs_id); ?></span>
                <?php felan_get_template('jobs/wishlist.php', array(
                    'jobs_id' => $jobs_id,
                )); ?>
            </div>
        </div>
        <div class="jobs-title-inner">
            <?php if (!empty(get_the_title($jobs_id))) : ?>
                <h3 class="jobs-title"><a href="<?php echo get_post_permalink($jobs_id) ?>"><?php echo get_the_title($jobs_id) ?></a>
                </h3>
            <?php endif; ?>
        </div>
    </div>
    <?php if (!empty(get_the_content($jobs_id)) && $enable_jobs_des) : ?>
        <div class="jobs-des">
            <?php echo wp_trim_words(get_post_field('post_content', $jobs_id), 15); ?>
        </div>
    <?php endif; ?>
    <div class="jobs-archive-center">
        <ul>
            <?php if (is_array($jobs_location)) { ?>
                <li>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.5599 20.8207C12.2247 21.0598 11.7753 21.0598 11.4401 20.8207C6.61138 17.3773 1.48557 10.2971 6.6667 5.18128C8.08118 3.78463 9.99963 3 12 3C14.0004 3 15.9188 3.78463 17.3333 5.18128C22.5144 10.2971 17.3886 17.3773 12.5599 20.8207Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M12 12C13.1046 12 14 11.1046 14 10C14 8.89543 13.1046 8 12 8C10.8954 8 10 8.89543 10 10C10 11.1046 10.8954 12 12 12Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <?php foreach ($jobs_location as $location) { ?>
                        <?php
						$jobs_url_slug = felan_get_option('jobs_url_slug');
						$location_link = home_url( '/' ) . $jobs_url_slug . '/?felan_location=' . $location->slug;
						?>
                        <a class="cate-location" href="<?php echo esc_url($location_link); ?>"><?php esc_html_e($location->name); ?></a>
                    <?php } ?>
                </li>
            <?php } ?>
            <li>
                <?php if (is_array($jobs_type)) { ?>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 6V12H16.5" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="12" cy="12" r="9" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <?php foreach ($jobs_type as $type) {
                        $type_link = get_term_link($type, 'jobs-type');
                    ?>
                        <a class="cate-type" href="<?php echo esc_url($type_link); ?>"><?php echo trim($type->name); ?></a>
                <?php }
                } ?>
            </li>
            <?php if (is_array($jobs_categories) || is_object($jobs_categories)) { ?>
                <li class="categories-warpper">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.9804 20.1944L3.68188 12.8959C3.31214 12.5261 3.10149 12.0265 3.09488 11.5037L3.00008 4.01504C2.99295 3.45157 3.45156 2.99295 4.01504 3.00008L11.5037 3.09488C12.0265 3.10149 12.5261 3.31214 12.8959 3.68188L20.1944 10.9804C20.8675 11.6535 21.3858 12.939 20.6177 13.7072L13.7072 20.6177C12.939 21.3858 11.6535 20.8675 10.9804 20.1944Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M8.019 7.55232L7.31189 6.84521" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <?php foreach ($jobs_categories as $categories) {
                        $cate_link = get_term_link($categories, 'jobs-categories');
                        if (is_object($categories) && property_exists($categories, 'term_id') && !empty($categories->term_id)) {
                    ?>
                            <div class="cate-warpper">
                                <a href="<?php echo esc_url($cate_link); ?>" class="cate">
                                    <?php echo $categories->name; ?>
                                </a>
                            </div>
                    <?php }
                    } ?>
                </li>
            <?php } ?>
        </ul>
    </div>
    <div class="jobs-archive-footer">
        <?php if (($jobs_salary_active && $jobs_salary_show == 'range' && $jobs_salary_minimum !== '' && $jobs_salary_maximum !== '')
            || ($jobs_salary_active && $jobs_salary_show == 'starting_amount' && $jobs_minimum_price !== '')
            || ($jobs_salary_active && $jobs_salary_show == 'maximum_amount' && $jobs_maximum_price !== '') || ($jobs_salary_active && $jobs_salary_show == 'agree')
        ) : ?>
            <div class="price">
                <?php echo felan_get_salary_jobs($jobs_id); ?>
            </div>
        <?php endif; ?>
        <div class="days">
            <span style="<?php if (intval($expiration_apply) <= 3) {
                                echo 'color:red';
                            } else {
                                echo 'color:green';
                            } ?>"> <?php echo felan_get_expiration_apply($jobs_id); ?> </span><?php esc_html_e('days left', 'felan-framework') ?>
        </div>
    </div>
    <?php if ($enable_jobs_single_popup === '1' && is_archive()) { ?>
        <a class="felan-link-item btn-single-settings" data-post-id="<?php echo esc_attr($id) ?>" data-post-type="jobs" href="#"></a>
    <?php } ?>
</div>