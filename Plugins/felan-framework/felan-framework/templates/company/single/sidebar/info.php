<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$user_id = $current_user->ID;
$company_id = get_the_ID();
if (!empty($company_single_id)) {
    $company_id = $company_single_id['post_id'];
}
$post_author_id = get_post_field('post_author', $company_id);
$company_logo   = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_logo');
$company_categories =  get_the_terms($company_id, 'company-categories');
$company_size = get_the_terms($company_id, 'company-size');

$company_location =  get_the_terms($company_id, 'felan_location');
$company_logo = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_logo');
$enable_social_twitter = felan_get_option('enable_social_twitter', '1');
$enable_social_linkedin = felan_get_option('enable_social_linkedin', '1');
$enable_social_facebook = felan_get_option('enable_social_facebook', '1');
$enable_social_instagram = felan_get_option('enable_social_instagram', '1');
$company_founded =  get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_founded');
$company_phone =  get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_phone');
$company_email =  get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_email');
$company_website =  get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_website');
$company_website =  !empty($company_website) ? $company_website['0'] : '';
$company_twitter   = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_twitter');
$company_facebook   = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_facebook');
$company_instagram   = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_instagram');
$company_linkedin   = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_linkedin');

$enable_post_type_project = felan_get_option('enable_post_type_project', '1');
$currency_sign_default = felan_get_option('currency_sign_default');
$sending_price = get_user_meta($post_author_id, FELAN_METABOX_PREFIX . 'employer_total_price_sending', true);
$sending_price = $currency_sign_default . felan_get_format_number($sending_price);
$project_completed = get_user_meta($post_author_id, FELAN_METABOX_PREFIX . 'employer_project_completed', true);
if (empty($project_completed)) {
    $project_completed = 0;
}

$classes = array();
$check_package = felan_get_field_check_freelancer_package('info_company');
$enable_sticky_sidebar_type = felan_get_option('enable_sticky_company_sidebar_type', 1);
if ($enable_sticky_sidebar_type) {
    $classes[] = 'has-sticky';
};

$hide_info_company_fields = felan_get_option('hide_freelancer_info_company_fields', array());
if (!is_array($hide_info_company_fields)) {
    $hide_info_company_fields = array();
}
if (in_array("felan_user_freelancer", (array)$current_user->roles)) {
    $notice =  esc_attr__("Please renew the package to view", "felan-framework");
} else {
    $notice =  esc_attr__("Please access the role Freelancer and purchase the package to view", "felan-framework");
}
?>
<?php if (($check_package == -1 || $check_package == 0) && $user_id != $post_author_id) { ?>
    <div class="jobs-company-sidebar block-archive-sidebar company-sidebar <?php echo implode(" ", $classes); ?>">
        <div class="company-sidebar-top">
            <?php if (!empty($company_logo[0]['url'])) : ?>
                <div class="logo-company">
                    <img src="<?php echo $company_logo[0]['url'] ?>" alt="" />
                </div>
            <?php endif; ?>
            <div class="company-sidebar-info">
                <div class="title-wapper">
                    <?php if (!empty(get_the_title($company_id))) : ?>
                        <h2><?php echo get_the_title($company_id); ?></h2>
                        <?php felan_company_green_tick($company_id); ?>
                    <?php endif; ?>
                </div>
                <?php echo felan_get_total_rating('company', $company_id); ?>
            </div>
        </div>
        <ul class="list-social">
            <?php if (!in_array("social", $hide_info_company_fields)) : ?>
                <?php if (!empty($company_facebook[0]) && $enable_social_facebook == 1) : ?>
                    <li><a href="<?php echo $company_facebook[0]; ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.5 10V14H9.5V21H13.5V14H16.5L17.5 10H13.5V8C13.5 7.45533 13.9553 7 14.5 7H17.5V3H14.5C11.7767 3 9.5 5.27667 9.5 8V10H6.5Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (!empty($company_twitter[0]) && $enable_social_twitter == 1) : ?>
                    <li>
                        <a href="<?php echo $company_twitter[0]; ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19 4L13.0697 10.9303M5 20L10.9303 13.0697M10.9303 13.0697L16.725 19.6568C16.9155 19.8734 17.2084 20 17.5186 20H18.9928C19.8294 20 20.3 19.1493 19.7864 18.5654L13.0697 10.9303M10.9303 13.0697L4.21364 5.43461C3.70001 4.85074 4.17062 4 5.00724 4H6.4814C6.79165 4 7.08452 4.12664 7.275 4.34316L13.0697 10.9303" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (!empty($company_linkedin[0]) && $enable_social_linkedin == 1) : ?>
                    <li><a href="<?php echo $company_linkedin[0]; ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16.5 3H7.5C5.01472 3 3 5.01472 3 7.5V16.5C3 18.9853 5.01472 21 7.5 21H16.5C18.9853 21 21 18.9853 21 16.5V7.5C21 5.01472 18.9853 3 16.5 3Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M8 16.375V10.75M12 16.375V13.5M12 13.5V10.75M12 13.5C12 12.2124 13.222 11.5 14.4 11.5C16 11.5 16 12.875 16 14.375V16.375" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M8 7.625L8 8.125" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (!empty($company_instagram[0]) && $enable_social_instagram == 1) : ?>
                    <li><a href="<?php echo $company_instagram[0]; ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16.5 3H7.5C5.01472 3 3 5.01472 3 7.5V16.5C3 18.9853 5.01472 21 7.5 21H16.5C18.9853 21 21 18.9853 21 16.5V7.5C21 5.01472 18.9853 3 16.5 3Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M15.4621 11.4866C15.5701 12.2148 15.4457 12.9585 15.1067 13.612C14.7676 14.2654 14.2311 14.7953 13.5736 15.1263C12.916 15.4573 12.1708 15.5725 11.444 15.4555C10.7171 15.3386 10.0457 14.9954 9.52513 14.4749C9.00457 13.9543 8.66141 13.2829 8.54446 12.556C8.4275 11.8292 8.5427 11.084 8.87368 10.4264C9.20466 9.76886 9.73456 9.23238 10.388 8.89332C11.0415 8.55426 11.7852 8.42988 12.5134 8.53786C13.2562 8.64801 13.9439 8.99414 14.4749 9.52513C15.0059 10.0561 15.352 10.7438 15.4621 11.4866Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M17 6.5H17.5" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    </li>
                <?php endif; ?>
            <?php else : ?>
                <?php if (!empty($company_facebook[0]) && $enable_social_facebook == 1) : ?>
                    <li><a class="btn-add-to-message" href="#" data-text="<?php echo $notice; ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.5 10V14H9.5V21H13.5V14H16.5L17.5 10H13.5V8C13.5 7.45533 13.9553 7 14.5 7H17.5V3H14.5C11.7767 3 9.5 5.27667 9.5 8V10H6.5Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (!empty($company_twitter[0]) && $enable_social_twitter == 1) : ?>
                    <li><a class="btn-add-to-message" href="#" data-text="<?php echo $notice; ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19 4L13.0697 10.9303M5 20L10.9303 13.0697M10.9303 13.0697L16.725 19.6568C16.9155 19.8734 17.2084 20 17.5186 20H18.9928C19.8294 20 20.3 19.1493 19.7864 18.5654L13.0697 10.9303M10.9303 13.0697L4.21364 5.43461C3.70001 4.85074 4.17062 4 5.00724 4H6.4814C6.79165 4 7.08452 4.12664 7.275 4.34316L13.0697 10.9303" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a></li>
                <?php endif; ?>
                <?php if (!empty($company_linkedin[0]) && $enable_social_linkedin == 1) : ?>
                    <li><a class="btn-add-to-message" href="#" data-text="<?php echo $notice; ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16.5 3H7.5C5.01472 3 3 5.01472 3 7.5V16.5C3 18.9853 5.01472 21 7.5 21H16.5C18.9853 21 21 18.9853 21 16.5V7.5C21 5.01472 18.9853 3 16.5 3Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M8 16.375V10.75M12 16.375V13.5M12 13.5V10.75M12 13.5C12 12.2124 13.222 11.5 14.4 11.5C16 11.5 16 12.875 16 14.375V16.375" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M8 7.625L8 8.125" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a></li>
                <?php endif; ?>
                <?php if (!empty($company_instagram[0]) && $enable_social_instagram == 1) : ?>
                    <li><a class="btn-add-to-message" href="#" data-text="<?php echo $notice; ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16.5 3H7.5C5.01472 3 3 5.01472 3 7.5V16.5C3 18.9853 5.01472 21 7.5 21H16.5C18.9853 21 21 18.9853 21 16.5V7.5C21 5.01472 18.9853 3 16.5 3Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M15.4621 11.4866C15.5701 12.2148 15.4457 12.9585 15.1067 13.612C14.7676 14.2654 14.2311 14.7953 13.5736 15.1263C12.916 15.4573 12.1708 15.5725 11.444 15.4555C10.7171 15.3386 10.0457 14.9954 9.52513 14.4749C9.00457 13.9543 8.66141 13.2829 8.54446 12.556C8.4275 11.8292 8.5427 11.084 8.87368 10.4264C9.20466 9.76886 9.73456 9.23238 10.388 8.89332C11.0415 8.55426 11.7852 8.42988 12.5134 8.53786C13.2562 8.64801 13.9439 8.99414 14.4749 9.52513C15.0059 10.0561 15.352 10.7438 15.4621 11.4866Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M17 6.5H17.5" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a></li>
                <?php endif; ?>
            <?php endif; ?>
            <?php felan_get_social_network($company_id, 'company'); ?>
        </ul>
        <div class="company-sidebar-center">
            <?php if (!empty($company_founded[0])) : ?>
                <div class="info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 12C3 7.75736 3 6.63604 4.31802 5.31802C5.63604 4 7.75736 4 12 4C16.2426 4 18.364 4 19.682 5.31802C21 6.63604 21 7.75736 21 12C21 16.2426 21 18.364 19.682 19.682C18.364 21 16.2426 21 12 21C7.75736 21 5.63604 21 4.31802 19.682C3 18.364 3 16.2426 3 12Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M16.5 5V3" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M7.5 5V3" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M3.25 8H20.75" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <?php if (!in_array("founded", $hide_info_company_fields)) : ?>
                        <?php echo esc_html__('Founded in', 'felan-framework') ?>
                        <p class="details-info"><?php echo ' ' . $company_founded[0]; ?></p>
                    <?php else : ?>
                        *************
                        <a class="btn-add-to-message" href="#" data-text="<?php echo $notice; ?>">
                            <i class="far fa-eye"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if (is_array($company_size)) : ?>
                <div class="info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="7.5" r="3" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M19.5 20.5C19.0246 11.1667 4.97538 11.1667 4.5 20.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="list-cate">
                        <?php if (!in_array("size", $hide_info_company_fields)) : ?>
                            <?php foreach ($company_size as $size) {
                                echo $size->name;
                            } ?>
                        <?php else : ?>
                            *************
                            <a class="btn-add-to-message" href="#" data-text="<?php echo $notice; ?>">
                                <i class="far fa-eye"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (is_array($company_location)) : ?>
                <div class="info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.5599 20.8207C12.2247 21.0598 11.7753 21.0598 11.4401 20.8207C6.61138 17.3773 1.48557 10.2971 6.6667 5.18128C8.08118 3.78463 9.99963 3 12 3C14.0004 3 15.9188 3.78463 17.3333 5.18128C22.5144 10.2971 17.3886 17.3773 12.5599 20.8207Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M12 12C13.1046 12 14 11.1046 14 10C14 8.89543 13.1046 8 12 8C10.8954 8 10 8.89543 10 10C10 11.1046 10.8954 12 12 12Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="details-info">
                        <?php if (!in_array("location", $hide_info_company_fields)) : ?>
                            <?php foreach ($company_location as $location) { ?>
                                <span><?php echo $location->name; ?></span>
                            <?php } ?>
                        <?php else : ?>
                            *************
                            <a class="btn-add-to-message" href="#" data-text="<?php echo $notice; ?>">
                                <i class="far fa-eye"></i>
                            </a>
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
            <?php if (!empty($company_phone[0])) : ?>
                <div class="info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.6 14.5215C13.205 17.0421 7.09582 10.9878 9.49995 8.45753C10.9678 6.91263 9.30963 5.14707 8.3918 3.84934C6.66924 1.41378 2.88771 4.77641 3.00256 6.91544C3.36473 13.6609 10.6615 21.6546 17.7275 20.9574C19.9381 20.7393 22.4778 16.7471 19.9423 15.2882C18.6745 14.5587 16.9342 13.1172 15.6 14.5215Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="details-info">
                        <?php if (!in_array("phone", $hide_info_company_fields)) : ?>
                            <a href="tel:<?php echo $company_phone[0]; ?>"><?php echo $company_phone[0]; ?></a>
                        <?php else : ?>
                            ***********
                            <a class="btn-add-to-message" href="#" data-text="<?php echo $notice; ?>">
                                <i class="far fa-eye"></i>
                            </a>
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
            <?php if (!empty($company_email[0])) : ?>
                <div class="info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 12C2 8.22876 2 6.34315 3.46447 5.17157C4.92893 4 7.28595 4 12 4C16.714 4 19.0711 4 20.5355 5.17157C22 6.34315 22 8.22876 22 12C22 15.7712 22 17.6569 20.5355 18.8284C19.0711 20 16.714 20 12 20C7.28595 20 4.92893 20 3.46447 18.8284C2 17.6569 2 15.7712 2 12Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M20.6667 5.31018L15.8412 9.79909C14.0045 11.3296 13.0862 12.0949 12.0001 12.0949C10.9139 12.0949 9.99561 11.3296 8.15897 9.7991L3.3335 5.31018" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="details-info email">
                        <?php if (!in_array("email", $hide_info_company_fields)) : ?>
                            <a href="mailto:<?php echo $company_email[0]; ?>"><?php echo $company_email[0]; ?></a>
                        <?php else : ?>
                            *************
                            <a class="btn-add-to-message" href="#" data-text="<?php echo $notice; ?>">
                                <i class="far fa-eye"></i>
                            </a>
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="action-inner">
                <?php felan_get_template('company/follow.php', array(
                    'company_id' => $company_id,
                )); ?>
                <?php if ($check_package == -1 || $check_package == 0) { ?>
                    <a href="#" class="felan-button button-outline btn-add-to-message button-icon-right" data-text="<?php echo esc_attr('Please renew the package to see website', 'felan-framework'); ?>">
                        <i class="far fa-external-link-alt"></i><?php esc_html_e('Website', 'felan-framework') ?>
                    </a>
                    <?php } else {
                    if (!empty($company_website)) {
                    ?>
                        <a href="<?php echo $company_website; ?>" class="felan-button button-outline btn-webs button-icon-right" target="_blank">
                            <i class="far fa-external-link-alt"></i><?php esc_html_e('Website', 'felan-framework') ?>
                        </a>
                    <?php
                    } ?>
                <?php } ?>
            </div>
            <?php felan_get_template('company/messages.php', array(
                'company_id' => $company_id,
            )); ?>
        </div>
        <div class="company-sidebar-bottom">
            <?php if (is_array($company_categories)) { ?>
                <h3><?php esc_html_e('Categories', 'felan-framework') ?></h3>
                <div class="company-cate">
                    <?php if (!in_array("categories", $hide_info_company_fields)) : ?>
                        <?php foreach ($company_categories as $categories) {
                            $cate_link = get_term_link($categories, 'company-categories'); ?>
                            <a href="<?php echo esc_url($cate_link); ?>" class="label label-categories">
                                <i class="far fa-tag"></i><?php esc_html_e($categories->name); ?>
                            </a>
                        <?php } ?>
                    <?php else : ?>
                        *************
                        <a class="btn-add-to-message" href="#" data-text="<?php echo $notice; ?>">
                            <i class="far fa-eye"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php } ?>
            <div class="conpany-insights">
                <h3><?php esc_html_e('Insights', 'felan-framework') ?></h3>
                <ul class="coo-list-insights">
                    <?php if ($enable_post_type_project == '1') : ?>
                        <li>
                            <span class="text"><?php echo esc_html_e('Total Spending', 'felan-framework') ?></span>
                            <span class="value"><?php echo esc_html($sending_price); ?></span>
                        </li>
                        <li>
                            <span class="text"><?php echo esc_html_e('Projects Completed', 'felan-framework') ?></span>
                            <span class="value"><?php echo esc_html($project_completed); ?></span>
                        </li>
                    <?php endif; ?>
                    <li>
                        <span class="text"><?php echo esc_html_e('Member Since', 'felan-framework') ?></span>
                        <span class="value"><?php echo felan_get_member_since($company_id); ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($company_id !== '' && ($check_package == 1 || $check_package == 2 || $user_id == $post_author_id)) : ?>
    <div class="jobs-company-sidebar block-archive-sidebar company-sidebar <?php echo implode(" ", $classes); ?>">
        <div class="company-sidebar-top">
            <?php if (!empty($company_logo[0]['url'])) : ?>
                <div class="logo-company">
                    <img src="<?php echo $company_logo[0]['url'] ?>" alt="" />
                </div>
            <?php endif; ?>
            <div class="company-sidebar-info">
                <div class="title-wapper">
                    <?php if (!empty(get_the_title($company_id))) : ?>
                        <h2><?php echo get_the_title($company_id); ?></h2>
                        <?php felan_company_green_tick($company_id); ?>
                    <?php endif; ?>
                </div>
                <?php echo felan_get_total_rating('company', $company_id); ?>
            </div>
        </div>
        <ul class="list-social">
            <?php if (!empty($company_facebook[0]) && $enable_social_facebook == 1) : ?>
                <li><a href="<?php echo $company_facebook[0]; ?>">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.5 10V14H9.5V21H13.5V14H16.5L17.5 10H13.5V8C13.5 7.45533 13.9553 7 14.5 7H17.5V3H14.5C11.7767 3 9.5 5.27667 9.5 8V10H6.5Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a></li>
            <?php endif; ?>
            <?php if (!empty($company_twitter[0]) && $enable_social_twitter == 1) : ?>
                <li><a href="<?php echo $company_twitter[0]; ?>">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 4L13.0697 10.9303M5 20L10.9303 13.0697M10.9303 13.0697L16.725 19.6568C16.9155 19.8734 17.2084 20 17.5186 20H18.9928C19.8294 20 20.3 19.1493 19.7864 18.5654L13.0697 10.9303M10.9303 13.0697L4.21364 5.43461C3.70001 4.85074 4.17062 4 5.00724 4H6.4814C6.79165 4 7.08452 4.12664 7.275 4.34316L13.0697 10.9303" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a></li>
            <?php endif; ?>
            <?php if (!empty($company_linkedin[0]) && $enable_social_linkedin == 1) : ?>
                <li><a href="<?php echo $company_linkedin[0]; ?>">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16.5 3H7.5C5.01472 3 3 5.01472 3 7.5V16.5C3 18.9853 5.01472 21 7.5 21H16.5C18.9853 21 21 18.9853 21 16.5V7.5C21 5.01472 18.9853 3 16.5 3Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M8 16.375V10.75M12 16.375V13.5M12 13.5V10.75M12 13.5C12 12.2124 13.222 11.5 14.4 11.5C16 11.5 16 12.875 16 14.375V16.375" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M8 7.625L8 8.125" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a></li>
            <?php endif; ?>
            <?php if (!empty($company_instagram[0]) && $enable_social_instagram == 1) : ?>
                <li><a href="<?php echo $company_instagram[0]; ?>">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16.5 3H7.5C5.01472 3 3 5.01472 3 7.5V16.5C3 18.9853 5.01472 21 7.5 21H16.5C18.9853 21 21 18.9853 21 16.5V7.5C21 5.01472 18.9853 3 16.5 3Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M15.4621 11.4866C15.5701 12.2148 15.4457 12.9585 15.1067 13.612C14.7676 14.2654 14.2311 14.7953 13.5736 15.1263C12.916 15.4573 12.1708 15.5725 11.444 15.4555C10.7171 15.3386 10.0457 14.9954 9.52513 14.4749C9.00457 13.9543 8.66141 13.2829 8.54446 12.556C8.4275 11.8292 8.5427 11.084 8.87368 10.4264C9.20466 9.76886 9.73456 9.23238 10.388 8.89332C11.0415 8.55426 11.7852 8.42988 12.5134 8.53786C13.2562 8.64801 13.9439 8.99414 14.4749 9.52513C15.0059 10.0561 15.352 10.7438 15.4621 11.4866Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M17 6.5H17.5" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a></li>
            <?php endif; ?>
            <?php felan_get_social_network($company_id, 'company'); ?>
        </ul>
        <div class="company-sidebar-center">
            <?php if (!empty($company_founded[0])) : ?>
                <div class="info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 12C3 7.75736 3 6.63604 4.31802 5.31802C5.63604 4 7.75736 4 12 4C16.2426 4 18.364 4 19.682 5.31802C21 6.63604 21 7.75736 21 12C21 16.2426 21 18.364 19.682 19.682C18.364 21 16.2426 21 12 21C7.75736 21 5.63604 21 4.31802 19.682C3 18.364 3 16.2426 3 12Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M16.5 5V3" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M7.5 5V3" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M3.25 8H20.75" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <?php echo esc_html__('Founded in ', 'felan-framework') ?>
                    <p class="details-info"> <?php echo ' ' . $company_founded[0]; ?></p>
                </div>
            <?php endif; ?>
            <?php if (is_array($company_size)) : ?>
                <div class="info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="7.5" r="3" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M19.5 20.5C19.0246 11.1667 4.97538 11.1667 4.5 20.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="list-cate">
                        <?php foreach ($company_size as $size) {
                            echo $size->name;
                        } ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (is_array($company_location)) : ?>
                <div class="info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.5599 20.8207C12.2247 21.0598 11.7753 21.0598 11.4401 20.8207C6.61138 17.3773 1.48557 10.2971 6.6667 5.18128C8.08118 3.78463 9.99963 3 12 3C14.0004 3 15.9188 3.78463 17.3333 5.18128C22.5144 10.2971 17.3886 17.3773 12.5599 20.8207Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M12 12C13.1046 12 14 11.1046 14 10C14 8.89543 13.1046 8 12 8C10.8954 8 10 8.89543 10 10C10 11.1046 10.8954 12 12 12Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="details-info">
                        <?php foreach ($company_location as $location) { ?>
                            <span><?php echo $location->name; ?></span>
                        <?php } ?>
                    </p>
                </div>
            <?php endif; ?>
            <?php if (!empty($company_phone[0])) : ?>
                <div class="info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.6 14.5215C13.205 17.0421 7.09582 10.9878 9.49995 8.45753C10.9678 6.91263 9.30963 5.14707 8.3918 3.84934C6.66924 1.41378 2.88771 4.77641 3.00256 6.91544C3.36473 13.6609 10.6615 21.6546 17.7275 20.9574C19.9381 20.7393 22.4778 16.7471 19.9423 15.2882C18.6745 14.5587 16.9342 13.1172 15.6 14.5215Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="details-info"><a href="tel:<?php echo $company_phone[0]; ?>"><?php echo $company_phone[0]; ?></a></p>
                </div>
            <?php endif; ?>
            <?php if (!empty($company_email[0])) : ?>
                <div class="info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 12C2 8.22876 2 6.34315 3.46447 5.17157C4.92893 4 7.28595 4 12 4C16.714 4 19.0711 4 20.5355 5.17157C22 6.34315 22 8.22876 22 12C22 15.7712 22 17.6569 20.5355 18.8284C19.0711 20 16.714 20 12 20C7.28595 20 4.92893 20 3.46447 18.8284C2 17.6569 2 15.7712 2 12Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M20.6667 5.31018L15.8412 9.79909C14.0045 11.3296 13.0862 12.0949 12.0001 12.0949C10.9139 12.0949 9.99561 11.3296 8.15897 9.7991L3.3335 5.31018" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="details-info email"><a href="mailto:<?php echo $company_email[0]; ?>"><?php echo $company_email[0]; ?></a></p>
                </div>
            <?php endif; ?>

            <div class="action-inner">
                <?php felan_get_template('company/follow.php', array(
                    'company_id' => $company_id,
                )); ?>
                <?php if ($check_package == -1 || $check_package == 0) { ?>
                    <a href="#" class="felan-button button-outline btn-add-to-message button-icon-right" data-text="<?php echo esc_attr('Please renew the package to see website', 'felan-framework'); ?>">
                        <?php esc_html_e('Website', 'felan-framework') ?><i class="far fa-external-link-alt"></i>
                    </a>
                    <?php } else {
                    if (!empty($company_website)) {
                    ?>
                        <a href="<?php echo $company_website; ?>" class="felan-button button-outline btn-webs button-icon-right" target="_blank">
                            <i class="far fa-external-link-alt"></i><?php esc_html_e('Website', 'felan-framework') ?>
                        </a>
                    <?php
                    } ?>
                <?php } ?>
            </div>
            <?php felan_get_template('company/messages.php', array(
                'company_id' => $company_id,
            )); ?>
        </div>
        <div class="company-sidebar-bottom">
            <?php if (is_array($company_categories)) { ?>
                <h3><?php esc_html_e('Categories', 'felan-framework') ?></h3>
                <div class="company-cate">
                    <?php foreach ($company_categories as $categories) {
                        $cate_link = get_term_link($categories, 'company-categories'); ?>
                        <a href="<?php echo esc_url($cate_link); ?>" class="label label-categories">
                            <i class="far fa-tag"></i><?php esc_html_e($categories->name); ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="conpany-insights">
                <h3><?php esc_html_e('Insights', 'felan-framework') ?></h3>
                <ul class="coo-list-insights">
                    <?php if ($enable_post_type_project == '1') : ?>
                        <li>
                            <span class="text"><?php echo esc_html_e('Total Spending', 'felan-framework') ?></span>
                            <span class="value"><?php echo esc_html($sending_price); ?></span>
                        </li>
                        <li>
                            <span class="text"><?php echo esc_html_e('Projects Completed', 'felan-framework') ?></span>
                            <span class="value"><?php echo esc_html($project_completed); ?></span>
                        </li>
                    <?php endif; ?>
                    <li>
                        <span class="text"><?php echo esc_html_e('Member Since', 'felan-framework') ?></span>
                        <span class="value"><?php echo felan_get_member_since($company_id); ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>