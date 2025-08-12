<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$user_id = $current_user->ID;
$freelancer_id = get_the_ID();
if (!empty($freelancer_single_id)) {
    $freelancer_id = $freelancer_single_id['post_id'];
}
$author_id = get_post_field('post_author', $freelancer_id);
$author_name = felan_get_author_name_by_id($author_id);
$freelancer_salary          = !empty(get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_offer_salary')) ? get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_offer_salary')[0] : '';
$freelancer_languages       = get_the_terms($freelancer_id, 'freelancer_languages');
$freelancer_location        = get_the_terms($freelancer_id, 'felan_location');
$freelancer_phone           = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_phone', true);
$freelancer_email           = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_email', true);
$freelancer_twitter         = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_twitter', true);
$freelancer_facebook        = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_facebook', true);
$freelancer_instagram       = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_instagram', true);
$freelancer_linkedin        = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_linkedin', true);

$enable_social_twitter     = felan_get_option('enable_social_twitter', '1');
$enable_social_linkedin    = felan_get_option('enable_social_linkedin', '1');
$enable_social_facebook    = felan_get_option('enable_social_facebook', '1');
$enable_social_instagram   = felan_get_option('enable_social_instagram', '1');

$freelancer_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
$freelancer_featured = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_featured', true);
$freelancer_current_position = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_current_position', true);
$freelancer_location = get_the_terms($freelancer_id, 'felan_location');
$enable_freelancer_review = felan_get_option('enable_single_freelancer_review', '1');
$check_package_invite = felan_get_field_check_employer_package('invite');

$classes = array();
$enable_sticky_sidebar_type = felan_get_option('enable_sticky_freelancer_sidebar_type', 1);
if ($enable_sticky_sidebar_type) {
    $classes[] = 'has-sticky';
};

$check_package_employer = felan_get_field_check_employer_package('info');
$hide_info_freelancer_fields = felan_get_option('hide_company_freelancer_info_fields', array());
if (!is_array($hide_info_freelancer_fields)) {
    $hide_info_freelancer_fields = array();
}
if (in_array("felan_user_employer", (array)$current_user->roles)) {
    $notice =  esc_attr__("Please renew the package to view", "felan-framework");
} else {
    $notice =  esc_attr__("Please access the role Employer and purchase the package to view", "felan-framework");
}

$currency_sign_default = felan_get_option('currency_sign_default');
$withdraw_price = get_user_meta($author_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_total_price', true);
$withdraw_price = $currency_sign_default . felan_get_format_number($withdraw_price);

$args_freelancer = array(
    'post_type' => 'service_order',
    'posts_per_page' => -1,
    'ignore_sticky_posts' => 1,
    'meta_query' => array(
        array(
            'key' => FELAN_METABOX_PREFIX . 'service_order_author_service',
            'value' => $author_name,
            'compare' => '==',
        ),
        array(
            'key' => FELAN_METABOX_PREFIX . 'service_order_payment_status',
            'value' => 'completed',
            'compare' => '==',
        )
    ),
);
$get_service = get_posts($args_freelancer);
$total_service_completed = !empty($get_service) ? count($get_service) : 0;
$enable_post_type_service = felan_get_option('enable_post_type_service', '1');

$verify_status = get_the_author_meta( 'user_verify', $author_id );
$show_badge    = felan_get_option( 'show_freelancer_verification', '1' );

?>
<?php if (($check_package_employer == -1 || $check_package_employer == 0) && $user_id != $author_id) { ?>
    <div class="freelancer-sidebar block-archive-sidebar <?php echo implode(" ", $classes); ?>">
        <div class="info-top">
            <?php if (!empty($freelancer_avatar)) : ?>
                <img class="image-freelancers" src="<?php echo esc_attr($freelancer_avatar) ?>" alt="" />
            <?php else : ?>
                <div class="image-freelancers"><i class="far fa-camera"></i></div>
            <?php endif; ?>
            <div class="title-wapper">
                <?php if (!empty(get_the_title($freelancer_id))) : ?>
                    <h2><?php echo get_the_title($freelancer_id); ?></h2>
                    <?php if ($freelancer_featured == 1) : ?>
                        <span class="felan-label felan-label-yellow"><?php echo esc_html__('Featured', 'felan-framework') ?></span>
                    <?php endif; ?>

					<?php if ( $verify_status == 1 && $show_badge == 1 ) : ?>
						<img style="width: 20px" class="image-freelancers-verify" src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/check-badge-verified.png'); ?>" alt="" />
					<?php endif; ?>
                <?php endif; ?>
            </div>
            <?php if (!empty($freelancer_current_position)) { ?>
                <div class="freelancer-current-position">
                    <?php esc_html_e($freelancer_current_position); ?>
                </div>
            <?php } ?>
            <?php if ($enable_freelancer_review) : ?>
                <?php echo felan_get_total_rating('freelancer', $freelancer_id); ?>
            <?php endif; ?>
            <?php if (!empty($freelancer_salary)) : ?>
                <div class="details-info salary">
                    <?php if (!in_array("salary", $hide_info_freelancer_fields)) : ?>
                        <?php felan_get_salary_freelancer($freelancer_id); ?>
                    <?php else : ?>
                        *************
                        <a class="btn-add-to-message" href="#" data-text="<?php echo $notice; ?>">
                            <i class="far fa-eye"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <ul class="list-social">
                <?php if (!in_array("social", $hide_info_freelancer_fields)) : ?>
                    <?php if (!empty($freelancer_facebook) && $enable_social_facebook == 1) : ?>
                        <li><a target="_blank" href="<?php echo $freelancer_facebook; ?>">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.5 10V14H9.5V21H13.5V14H16.5L17.5 10H13.5V8C13.5 7.45533 13.9553 7 14.5 7H17.5V3H14.5C11.7767 3 9.5 5.27667 9.5 8V10H6.5Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($freelancer_twitter) && $enable_social_twitter == 1) : ?>
                        <li>
                            <a target="_blank" href="<?php echo $freelancer_twitter; ?>">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19 4L13.0697 10.9303M5 20L10.9303 13.0697M10.9303 13.0697L16.725 19.6568C16.9155 19.8734 17.2084 20 17.5186 20H18.9928C19.8294 20 20.3 19.1493 19.7864 18.5654L13.0697 10.9303M10.9303 13.0697L4.21364 5.43461C3.70001 4.85074 4.17062 4 5.00724 4H6.4814C6.79165 4 7.08452 4.12664 7.275 4.34316L13.0697 10.9303" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($freelancer_linkedin) && $enable_social_linkedin == 1) : ?>
                        <li>
                            <a target="_blank" href="<?php echo $freelancer_linkedin; ?>">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.5 3H7.5C5.01472 3 3 5.01472 3 7.5V16.5C3 18.9853 5.01472 21 7.5 21H16.5C18.9853 21 21 18.9853 21 16.5V7.5C21 5.01472 18.9853 3 16.5 3Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M8 16.375V10.75M12 16.375V13.5M12 13.5V10.75M12 13.5C12 12.2124 13.222 11.5 14.4 11.5C16 11.5 16 12.875 16 14.375V16.375" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M8 7.625L8 8.125" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($freelancer_instagram) && $enable_social_instagram == 1) : ?>
                        <li><a target="_blank" href="<?php echo $freelancer_instagram; ?>">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.5 3H7.5C5.01472 3 3 5.01472 3 7.5V16.5C3 18.9853 5.01472 21 7.5 21H16.5C18.9853 21 21 18.9853 21 16.5V7.5C21 5.01472 18.9853 3 16.5 3Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M15.4621 11.4866C15.5701 12.2148 15.4457 12.9585 15.1067 13.612C14.7676 14.2654 14.2311 14.7953 13.5736 15.1263C12.916 15.4573 12.1708 15.5725 11.444 15.4555C10.7171 15.3386 10.0457 14.9954 9.52513 14.4749C9.00457 13.9543 8.66141 13.2829 8.54446 12.556C8.4275 11.8292 8.5427 11.084 8.87368 10.4264C9.20466 9.76886 9.73456 9.23238 10.388 8.89332C11.0415 8.55426 11.7852 8.42988 12.5134 8.53786C13.2562 8.64801 13.9439 8.99414 14.4749 9.52513C15.0059 10.0561 15.352 10.7438 15.4621 11.4866Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M17 6.5H17.5" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if (!empty($freelancer_facebook) && $enable_social_facebook == 1) : ?>
                        <li>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.5 10V14H9.5V21H13.5V14H16.5L17.5 10H13.5V8C13.5 7.45533 13.9553 7 14.5 7H17.5V3H14.5C11.7767 3 9.5 5.27667 9.5 8V10H6.5Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($freelancer_twitter) && $enable_social_twitter == 1) : ?>
                        <li><a class="btn-add-to-message" href="#" data-text="<?php echo $notice; ?>">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19 4L13.0697 10.9303M5 20L10.9303 13.0697M10.9303 13.0697L16.725 19.6568C16.9155 19.8734 17.2084 20 17.5186 20H18.9928C19.8294 20 20.3 19.1493 19.7864 18.5654L13.0697 10.9303M10.9303 13.0697L4.21364 5.43461C3.70001 4.85074 4.17062 4 5.00724 4H6.4814C6.79165 4 7.08452 4.12664 7.275 4.34316L13.0697 10.9303" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a></li>
                    <?php endif; ?>
                    <?php if (!empty($freelancer_linkedin) && $enable_social_linkedin == 1) : ?>
                        <li><a class="btn-add-to-message" href="#" data-text="<?php echo $notice; ?>">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.5 3H7.5C5.01472 3 3 5.01472 3 7.5V16.5C3 18.9853 5.01472 21 7.5 21H16.5C18.9853 21 21 18.9853 21 16.5V7.5C21 5.01472 18.9853 3 16.5 3Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M8 16.375V10.75M12 16.375V13.5M12 13.5V10.75M12 13.5C12 12.2124 13.222 11.5 14.4 11.5C16 11.5 16 12.875 16 14.375V16.375" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M8 7.625L8 8.125" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a></li>
                    <?php endif; ?>
                    <?php if (!empty($freelancer_instagram) && $enable_social_instagram == 1) : ?>
                        <li><a class="btn-add-to-message" href="#" data-text="<?php echo $notice; ?>">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.5 3H7.5C5.01472 3 3 5.01472 3 7.5V16.5C3 18.9853 5.01472 21 7.5 21H16.5C18.9853 21 21 18.9853 21 16.5V7.5C21 5.01472 18.9853 3 16.5 3Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M15.4621 11.4866C15.5701 12.2148 15.4457 12.9585 15.1067 13.612C14.7676 14.2654 14.2311 14.7953 13.5736 15.1263C12.916 15.4573 12.1708 15.5725 11.444 15.4555C10.7171 15.3386 10.0457 14.9954 9.52513 14.4749C9.00457 13.9543 8.66141 13.2829 8.54446 12.556C8.4275 11.8292 8.5427 11.084 8.87368 10.4264C9.20466 9.76886 9.73456 9.23238 10.388 8.89332C11.0415 8.55426 11.7852 8.42988 12.5134 8.53786C13.2562 8.64801 13.9439 8.99414 14.4749 9.52513C15.0059 10.0561 15.352 10.7438 15.4621 11.4866Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M17 6.5H17.5" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a></li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php $felan_social_fields = felan_get_option('felan_social_fields');
                if (is_array($felan_social_fields) && !empty($felan_social_fields)) {
                    foreach ($felan_social_fields as $key => $value) {
                        $freelancer_social_val = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_' . $value['social_name'], true);
                ?>
                        <li><a target="_blank" href="<?php echo $freelancer_social_val; ?>"><?php echo $value['social_icon']; ?></a></li>
                <?php }
                } ?>
                <?php felan_get_social_network($freelancer_id, 'freelancer'); ?>
            </ul>
        </div>
        <div class="info-center">
            <?php if (is_array($freelancer_location)) { ?>
                <div class="info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.5599 20.8207C12.2247 21.0598 11.7753 21.0598 11.4401 20.8207C6.61138 17.3773 1.48557 10.2971 6.6667 5.18128C8.08118 3.78463 9.99963 3 12 3C14.0004 3 15.9188 3.78463 17.3333 5.18128C22.5144 10.2971 17.3886 17.3773 12.5599 20.8207Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M12 12C13.1046 12 14 11.1046 14 10C14 8.89543 13.1046 8 12 8C10.8954 8 10 8.89543 10 10C10 11.1046 10.8954 12 12 12Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <?php if (!in_array("locations", $hide_info_freelancer_fields)) : ?>
                        <?php foreach ($freelancer_location as $location) {
                            $cate_link = get_term_link($location, 'felan_location'); ?>
                            <div class="cate-warpper">
                                <a href="<?php echo esc_url($cate_link); ?>" class="cate"><?php echo $location->name; ?></a>
                            </div>
                        <?php } ?>
                    <?php else : ?>
                        *************
                        <a class="btn-add-to-message" href="#" data-text="<?php echo $notice; ?>">
                            <i class="far fa-eye"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php } ?>

            <?php if (is_array($freelancer_languages)) : ?>
                <div class="info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3M12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3M12 21C14.7614 21 15.9413 15.837 15.9413 12C15.9413 8.16303 14.7614 3 12 3M12 21C9.23858 21 8.05895 15.8369 8.05895 12C8.05895 8.16307 9.23858 3 12 3M3.49988 8.99998C10.1388 8.99998 13.861 8.99998 20.4999 8.99998M3.49988 15C10.1388 15 13.861 15 20.4999 15" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <?php if (!in_array("languages", $hide_info_freelancer_fields)) : ?>
                        <?php foreach ($freelancer_languages as $language) {
                            $cate_link = get_term_link($language, 'felan_location'); ?>
                            <div class="cate-warpper">
                                <a href="<?php echo esc_url($cate_link); ?>" class="cate"><?php echo trim($language->name); ?></a>
                            </div>
                        <?php } ?>
                    <?php else : ?>
                        *************
                        <a class="btn-add-to-message" href="#" data-text="<?php echo $notice; ?>">
                            <i class="far fa-eye"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="info-bottom">
            <div class="info-bottom-btn">
                <?php felan_get_template('freelancer/follow.php', array(
                    'freelancer_id' => $freelancer_id,
                )); ?>
                <?php if (is_user_logged_in() && in_array('felan_user_employer', (array)$current_user->roles)) { ?>
                    <?php if ($user_id == $author_id) { ?>
                        <a href="#" class="felan-button felan-invite button-outline btn-add-to-message"
                            data-text="<?php echo esc_attr('This feature is not available for the same user ID', 'felan-framework'); ?>">
                            <i class="far fa-inbox"></i>
                            <?php esc_html_e('Send Job Invitation', 'felan-framework') ?>
                        </a>
                    <?php } else { ?>
                        <?php if ($check_package_invite == -1 || $check_package_invite == 0) { ?>
                            <a href="#" class="felan-button felan-invite button-outline btn-add-to-message" data-text="<?php echo esc_attr('Package expired. Please select a new one.', 'felan-framework'); ?>">
                                <i class="far fa-inbox"></i>
                                <?php esc_html_e('Send Job Invitation', 'felan-framework') ?>
                            </a>
                        <?php } else { ?>
                            <a href="#" class="felan-button felan-invite button-outline" id="btn-invite-freelancer">
                                <i class="far fa-inbox"></i>
                                <?php esc_html_e('Send Job Invitation', 'felan-framework') ?>
                            </a>
                        <?php } ?>
                    <?php } ?>
                <?php } else { ?>
                    <div class="logged-out">
                        <a href="#popup-form" class="felan-button felan-invite button-outline btn-login notice-employer" data-notice="<?php esc_attr_e('Please login role Employer to view', 'felan-framework') ?>">
                            <i class="far fa-inbox"></i>
                            <?php esc_html_e('Send Job Invitation', 'felan-framework') ?>
                        </a>
                    </div>
                <?php } ?>
            </div>

            <?php felan_get_template('freelancer/messages.php', array(
                'freelancer_id' => $freelancer_id,
            )); ?>

            <?php felan_get_template('freelancer/single/skills.php'); ?>

            <div class="freelancer-insights">
                <h3><?php esc_html_e('Insights', 'felan-framework') ?></h3>
                <ul class="coo-list-insights">
                    <?php if ($enable_post_type_service == '1') { ?>
                        <li>
                            <span class="text"><?php echo esc_html_e('All-Time Earnings', 'felan-framework') ?></span>
                            <span class="value"><?php echo $withdraw_price; ?></span>
                        </li>
                        <li>
                            <span class="text"><?php echo esc_html_e('Services Completed', 'felan-framework') ?></span>
                            <span class="value"><?php echo $total_service_completed ?></span>
                        </li>
                    <?php } ?>
                    <li>
                        <span class="text"><?php echo esc_html_e('Member Since', 'felan-framework') ?></span>
                        <span class="value"><?php echo  get_the_date(); ?></span>
                    </li>
                </ul>
            </div>
        </div>
        <?php
        do_action('felan/freelancer/single/sidebar/info/after');
        ?>
    </div>
<?php } ?>
<?php if ($check_package_employer == 1 || $check_package_employer == 2 || $user_id == $author_id) : ?>
    <div class="freelancer-sidebar block-archive-sidebar <?php echo implode(" ", $classes); ?>">
        <div class="info-top">
            <?php if (!empty($freelancer_avatar)) : ?>
                <img class="image-freelancers" src="<?php echo esc_attr($freelancer_avatar) ?>" alt="" />
            <?php else : ?>
                <div class="image-freelancers"><i class="far fa-camera"></i></div>
            <?php endif; ?>
            <div class="title-wapper">
                <?php if (!empty(get_the_title($freelancer_id))) : ?>
                    <h2><?php echo get_the_title($freelancer_id); ?></h2>
                    <?php if ($freelancer_featured == 1) : ?>
                        <span class="felan-label felan-label-yellow"><?php echo esc_html__('Featured', 'felan-framework') ?></span>
                    <?php endif; ?>

					<?php if ( $verify_status == 1 && $show_badge == 1 ) : ?>
						<img style="width: 20px" class="image-freelancers-verify" src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/check-badge-verified.png'); ?>" alt="" />
					<?php endif; ?>

                <?php endif; ?>
            </div>
            <?php if (!empty($freelancer_current_position)) { ?>
                <div class="freelancer-current-position">
                    <?php esc_html_e($freelancer_current_position); ?>
                </div>
            <?php } ?>
            <?php if ($enable_freelancer_review) : ?>
                <?php echo felan_get_total_rating('freelancer', $freelancer_id); ?>
            <?php endif; ?>
            <?php if (!empty($freelancer_salary)) : ?>
                <div class="details-info salary">
                    <?php felan_get_salary_freelancer($freelancer_id); ?>
                </div>
            <?php endif; ?>
            <ul class="list-social">
                <?php if (!empty($freelancer_facebook) && $enable_social_facebook == 1) : ?>
                    <li><a target="_blank" href="<?php echo $freelancer_facebook; ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.5 10V14H9.5V21H13.5V14H16.5L17.5 10H13.5V8C13.5 7.45533 13.9553 7 14.5 7H17.5V3H14.5C11.7767 3 9.5 5.27667 9.5 8V10H6.5Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a></li>
                <?php endif; ?>
                <?php if (!empty($freelancer_twitter) && $enable_social_twitter == 1) : ?>
                    <li><a target="_blank" href="<?php echo $freelancer_twitter; ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19 4L13.0697 10.9303M5 20L10.9303 13.0697M10.9303 13.0697L16.725 19.6568C16.9155 19.8734 17.2084 20 17.5186 20H18.9928C19.8294 20 20.3 19.1493 19.7864 18.5654L13.0697 10.9303M10.9303 13.0697L4.21364 5.43461C3.70001 4.85074 4.17062 4 5.00724 4H6.4814C6.79165 4 7.08452 4.12664 7.275 4.34316L13.0697 10.9303" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a></li>
                <?php endif; ?>
                <?php if (!empty($freelancer_linkedin) && $enable_social_linkedin == 1) : ?>
                    <li><a target="_blank" href="<?php echo $freelancer_linkedin; ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16.5 3H7.5C5.01472 3 3 5.01472 3 7.5V16.5C3 18.9853 5.01472 21 7.5 21H16.5C18.9853 21 21 18.9853 21 16.5V7.5C21 5.01472 18.9853 3 16.5 3Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M8 16.375V10.75M12 16.375V13.5M12 13.5V10.75M12 13.5C12 12.2124 13.222 11.5 14.4 11.5C16 11.5 16 12.875 16 14.375V16.375" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M8 7.625L8 8.125" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a></li>
                <?php endif; ?>
                <?php if (!empty($freelancer_instagram) && $enable_social_instagram == 1) : ?>
                    <li><a target="_blank" href="<?php echo $freelancer_instagram; ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16.5 3H7.5C5.01472 3 3 5.01472 3 7.5V16.5C3 18.9853 5.01472 21 7.5 21H16.5C18.9853 21 21 18.9853 21 16.5V7.5C21 5.01472 18.9853 3 16.5 3Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M15.4621 11.4866C15.5701 12.2148 15.4457 12.9585 15.1067 13.612C14.7676 14.2654 14.2311 14.7953 13.5736 15.1263C12.916 15.4573 12.1708 15.5725 11.444 15.4555C10.7171 15.3386 10.0457 14.9954 9.52513 14.4749C9.00457 13.9543 8.66141 13.2829 8.54446 12.556C8.4275 11.8292 8.5427 11.084 8.87368 10.4264C9.20466 9.76886 9.73456 9.23238 10.388 8.89332C11.0415 8.55426 11.7852 8.42988 12.5134 8.53786C13.2562 8.64801 13.9439 8.99414 14.4749 9.52513C15.0059 10.0561 15.352 10.7438 15.4621 11.4866Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M17 6.5H17.5" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a></li>
                <?php endif; ?>
                <?php $felan_social_fields = felan_get_option('felan_social_fields');
                if (is_array($felan_social_fields) && !empty($felan_social_fields)) {
                    foreach ($felan_social_fields as $key => $value) {
                        $freelancer_social_val = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_' . $value['social_name'], true);
                        if (!empty($freelancer_social_val)) { ?>
                            <li><a target="_blank" href="<?php echo $freelancer_social_val; ?>"><?php echo $value['social_icon']; ?></a></li>
                <?php }
                    }
                } ?>
                <?php felan_get_social_network($freelancer_id, 'freelancer'); ?>
            </ul>
        </div>
        <div class="info-center">
            <?php if (is_array($freelancer_location)) { ?>
                <div class="info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.5599 20.8207C12.2247 21.0598 11.7753 21.0598 11.4401 20.8207C6.61138 17.3773 1.48557 10.2971 6.6667 5.18128C8.08118 3.78463 9.99963 3 12 3C14.0004 3 15.9188 3.78463 17.3333 5.18128C22.5144 10.2971 17.3886 17.3773 12.5599 20.8207Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M12 12C13.1046 12 14 11.1046 14 10C14 8.89543 13.1046 8 12 8C10.8954 8 10 8.89543 10 10C10 11.1046 10.8954 12 12 12Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <?php foreach ($freelancer_location as $location) {
                        $cate_link = get_term_link($location, 'felan_location'); ?>
                        <div class="cate-warpper">
                            <a href="<?php echo esc_url($cate_link); ?>" class="cate">
                                <?php echo $location->name; ?>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <?php if (is_array($freelancer_languages)) : ?>
                <div class="info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3M12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3M12 21C14.7614 21 15.9413 15.837 15.9413 12C15.9413 8.16303 14.7614 3 12 3M12 21C9.23858 21 8.05895 15.8369 8.05895 12C8.05895 8.16307 9.23858 3 12 3M3.49988 8.99998C10.1388 8.99998 13.861 8.99998 20.4999 8.99998M3.49988 15C10.1388 15 13.861 15 20.4999 15" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <?php foreach ($freelancer_languages as $language) {
                        $cate_link = get_term_link($language, 'felan_location'); ?>
                        <div class="cate-warpper">
                            <a href="<?php echo esc_url($cate_link); ?>" class="cate"><?php echo trim($language->name); ?></a>
                        </div>
                    <?php } ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="info-bottom">
            <div class="info-bottom-btn">
                <?php felan_get_template('freelancer/follow.php', array(
                    'freelancer_id' => $freelancer_id,
                )); ?>
                <?php if (is_user_logged_in() && in_array('felan_user_employer', (array)$current_user->roles)) { ?>
                    <?php if ($user_id == $author_id) { ?>
                        <a href="#" class="felan-button felan-invite button-outline btn-add-to-message"
                            data-text="<?php echo esc_attr('This feature is not available for the same user ID', 'felan-framework'); ?>">
                            <i class="far fa-inbox"></i>
                            <?php esc_html_e('Send Job Invitation', 'felan-framework') ?>
                        </a>
                    <?php } else { ?>
                        <?php if ($check_package_invite == -1 || $check_package_invite == 0) { ?>
                            <a href="#" class="felan-button felan-invite button-outline btn-add-to-message" data-text="<?php echo esc_attr('Package expired. Please select a new one.', 'felan-framework'); ?>">
                                <i class="far fa-inbox"></i>
                                <?php esc_html_e('Send Job Invitation', 'felan-framework') ?>
                            </a>
                        <?php } else { ?>
                            <a href="#" class="felan-button felan-invite button-outline" id="btn-invite-freelancer">
                                <i class="far fa-inbox"></i>
                                <?php esc_html_e('Send Job Invitation', 'felan-framework') ?>
                            </a>
                        <?php } ?>
                    <?php } ?>
                <?php } else { ?>
                    <div class="logged-out">
                        <a href="#popup-form" class="felan-button felan-invite button-outline btn-login notice-employer" data-notice="<?php esc_attr_e('Please login role Employer to view', 'felan-framework') ?>">
                            <i class="far fa-inbox"></i>
                            <?php esc_html_e('Send Job Invitation', 'felan-framework') ?>
                        </a>
                    </div>
                <?php } ?>
            </div>

            <?php felan_get_template('freelancer/messages.php', array(
                'freelancer_id' => $freelancer_id,
            )); ?>

            <?php felan_get_template('freelancer/single/skills.php'); ?>

            <div class="freelancer-insights">
                <h3><?php esc_html_e('Insights', 'felan-framework') ?></h3>
                <ul class="coo-list-insights">
                    <?php if ($enable_post_type_service == '1') { ?>
                        <li>
                            <span class="text"><?php echo esc_html_e('All-Time Earnings', 'felan-framework') ?></span>
                            <span class="value"><?php echo $withdraw_price; ?></span>
                        </li>
                        <li>
                            <span class="text"><?php echo esc_html_e('Services Completed', 'felan-framework') ?></span>
                            <span class="value"><?php echo $total_service_completed ?></span>
                        </li>
                    <?php } ?>
                    <li>
                        <span class="text"><?php echo esc_html_e('Member Since', 'felan-framework') ?></span>
                        <span class="value"><?php echo felan_get_member_since($freelancer_id); ?></span>
                    </li>
                </ul>
            </div>
        </div>
        <?php
        do_action('felan/freelancer/single/sidebar/info/after');
        ?>
    </div>
    <?php felan_custom_field_single_freelancer('info'); ?>
<?php endif; ?>