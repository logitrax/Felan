<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$payment_url = felan_get_permalink('payment_service');
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'service');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'service',
    'felan_addons_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
        'payment_url' => $payment_url,
    )
);

global $current_user;
$user_id = $current_user->ID;
$classes = array();
$enable_sticky_sidebar_type = felan_get_option('enable_sticky_service_sidebar_type');
$currency_sign_default = felan_get_option('currency_sign_default');
if ($enable_sticky_sidebar_type) {
    $classes[] = 'has-sticky';
}

$service_id = get_the_ID();
if (!empty($service_single_id)) {
    $service_id = $service_single_id;
}
$author_id = get_post_field('post_author', $service_id);

$service_quantity = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_quantity', true);
$service_time = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_time', true);
$number_delivery_time = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_basic_time', true);
$currency_sign_default = felan_get_option('currency_sign_default');
$service_basic_price_default = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_basic_price', true);
$service_basic_time = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_basic_time', true);
$service_basic_des = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_basic_des', true);
$service_standard_price_default = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_standard_price', true);
$service_standard_time = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_standard_time', true);
$service_standard_des = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_standard_des', true);
$service_premium_price_default = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_premium_price', true);
$service_premium_time = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_premium_time', true);
$service_premium_des = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_premium_des', true);
$service_package_new = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_package_new', true);
$service_addon = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_tab_addon', true);
$package_service = felan_get_option('package_service');
$enable_wallet_mode = felan_get_option('enable_wallet_mode', '0');

$currency_position = felan_get_option('currency_position');
$enable_freelancer_service_fee =  felan_get_option('enable_freelancer_service_fee');
$freelancer_number_service_fee =  felan_get_option('freelancer_number_service_fee');
$basic_price_fee = 0;

if ($enable_wallet_mode == '1') {
    $service_total_basic_price = $service_basic_price_default;
    $service_total_standard_price = $service_standard_price_default;
    $service_total_premium_price = $service_premium_price_default;

    $basic_price_fee = round(intval($service_basic_price_default) * intval($freelancer_number_service_fee) / 100);
    if ($enable_freelancer_service_fee == '1' || (!empty($freelancer_number_service_fee) || $freelancer_number_service_fee == 0)) {
        $service_total_basic_price = intval($service_basic_price_default) + intval($basic_price_fee);
    }

    $standard_price_fee = round(intval($service_standard_price_default) * intval($freelancer_number_service_fee) / 100);
    if ($enable_freelancer_service_fee == '1' || (!empty($freelancer_number_service_fee) || $freelancer_number_service_fee == 0)) {
        $service_total_standard_price = intval($service_standard_price_default) + intval($standard_price_fee);
    }

    $premium_price_fee = round(intval($service_premium_price_default) * intval($freelancer_number_service_fee) / 100);
    if ($enable_freelancer_service_fee == '1' || (!empty($freelancer_number_service_fee) || $freelancer_number_service_fee == 0)) {
        $service_total_premium_price = intval($service_premium_price_default) + intval($premium_price_fee);
    }

    if ($currency_position == 'before') {
        $basic_price_fee = $currency_sign_default . felan_get_format_number($basic_price_fee);
        $standard_price_fee = $currency_sign_default . felan_get_format_number($standard_price_fee);
        $premium_price_fee = $currency_sign_default . felan_get_format_number($premium_price_fee);
    } else {
        $basic_price_fee = felan_get_format_number($basic_price_fee) . $currency_sign_default;
        $standard_price_fee = felan_get_format_number($standard_price_fee)  . $currency_sign_default;
        $premium_price_fee = felan_get_format_number($premium_price_fee) . $currency_sign_default;
    }
}


if ($currency_position == 'before') {
    $service_basic_price = $currency_sign_default . felan_get_format_number($service_basic_price_default);
    $service_standard_price = $currency_sign_default . felan_get_format_number($service_standard_price_default);
    $service_premium_price = $currency_sign_default . felan_get_format_number($service_premium_price_default);
} else {
    $service_basic_price = felan_get_format_number($service_basic_price_default) . $currency_sign_default;
    $service_standard_price = felan_get_format_number($service_standard_price_default)  . $currency_sign_default;
    $service_premium_price = felan_get_format_number($service_premium_price_default) . $currency_sign_default;
}
?>
<div class="service-package-sidebar block-archive-sidebar service-package-submit <?php echo implode(" ", $classes); ?>">
    <div class="tab-single <?php if ($service_quantity === '1') : ?>d-none<?php endif; ?>">
        <ul class="tab-single-list">
            <li class="tab-single-item">
                <a href="#tab-basic">
                    <?php echo esc_html__('Basic ', 'felan-framework'); ?>
                </a>
            </li>
            <?php if ($service_quantity === '2' || $service_quantity === '3') : ?>
                <li class="tab-single-item">
                    <a href="#tab-standard">
                        <?php echo esc_html__('Standard ', 'felan-framework'); ?>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($service_quantity === '3') : ?>
                <li class="tab-single-item">
                    <a href="#tab-premium">
                        <?php echo esc_html__('Premium ', 'felan-framework'); ?>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="tab-single-content">
        <div id="tab-basic" class="tab-single-info">
            <div class="package-top">
                <span><?php echo esc_html__('From ', 'felan-framework'); ?></span>
                <span class="price"><?php echo esc_html($service_basic_price); ?></span>
                <p class="des"><?php echo wpautop($service_basic_des); ?></p>
            </div>
            <div class="package-center">
                <ul class="content">
                    <li>
                        <span><?php echo esc_html__('Delivery Time', 'felan-framework'); ?></span>
                        <span class="delivery-time"><?php echo felan_service_delivery_time($service_id, $service_basic_time); ?></span>
                    </li>
                    <li>
                        <span><?php echo esc_html__('Number of Revisions', 'felan-framework'); ?></span>
                        <span class="revisions"><?php echo felan_service_revisions($service_id, 'basic'); ?></span>
                    </li>
                    <?php if (is_array($package_service) && !empty($package_service)) :
                        foreach ($package_service as $key => $package) :
                            $service_package_list_key = FELAN_METABOX_PREFIX . 'service_package_list' . $key;
                            $service_package_title_key = FELAN_METABOX_PREFIX . 'service_package_title' . $key;
                            $new_title = get_post_meta($service_id, $service_package_title_key, true);
                            $new_list = get_post_meta($service_id, $service_package_list_key, true);

                            if (!empty($new_title) && !empty($new_list)) :
                                if (in_array('basic', $new_list)) :
                    ?>
                                    <li>
                                        <span><?php echo esc_html($new_title); ?></span>
                                        <span class="check">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.71278 3.64026C10.2941 3.14489 10.5847 2.8972 10.8886 2.75195C11.5915 2.41602 12.4085 2.41602 13.1114 2.75195C13.4153 2.8972 13.7059 3.14489 14.2872 3.64026C14.8856 4.15023 15.4938 4.40761 16.2939 4.47146C17.0552 4.53222 17.4359 4.56259 17.7535 4.67477C18.488 4.93421 19.0658 5.51198 19.3252 6.24652C19.4374 6.5641 19.4678 6.94476 19.5285 7.70608C19.5924 8.50621 19.8498 9.11436 20.3597 9.71278C20.8551 10.2941 21.1028 10.5847 21.248 10.8886C21.584 11.5915 21.584 12.4085 21.248 13.1114C21.1028 13.4153 20.8551 13.7059 20.3597 14.2872C19.8391 14.8981 19.5911 15.5102 19.5285 16.2939C19.4678 17.0552 19.4374 17.4359 19.3252 17.7535C19.0658 18.488 18.488 19.0658 17.7535 19.3252C17.4359 19.4374 17.0552 19.4678 16.2939 19.5285C15.4938 19.5924 14.8856 19.8498 14.2872 20.3597C13.7059 20.8551 13.4153 21.1028 13.1114 21.248C12.4085 21.584 11.5915 21.584 10.8886 21.248C10.5847 21.1028 10.2941 20.8551 9.71278 20.3597C9.10185 19.8391 8.48984 19.5911 7.70608 19.5285C6.94476 19.4678 6.5641 19.4374 6.24652 19.3252C5.51198 19.0658 4.93421 18.488 4.67477 17.7535C4.56259 17.4359 4.53222 17.0552 4.47146 16.2939C4.40761 15.4938 4.15023 14.8856 3.64026 14.2872C3.14489 13.7059 2.8972 13.4153 2.75195 13.1114C2.41602 12.4085 2.41602 11.5915 2.75195 10.8886C2.8972 10.5847 3.14489 10.2941 3.64026 9.71278C4.16089 9.10185 4.40892 8.48984 4.47146 7.70608C4.53222 6.94476 4.56259 6.5641 4.67477 6.24652C4.93421 5.51198 5.51198 4.93421 6.24652 4.67477C6.5641 4.56259 6.94476 4.53222 7.70608 4.47146C8.50621 4.40761 9.11436 4.15023 9.71278 3.64026Z" stroke="#3AB446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M8.66797 12.6302L10.1738 14.3512C10.5972 14.835 11.3606 14.7994 11.7371 14.2781L15.3346 9.29688" stroke="#3AB446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>
                    <?php endforeach;
                    endif; ?>
                    <?php if (!empty($service_package_new)) :
                        foreach ($service_package_new as $index => $package) :
                            $new_title = $package[FELAN_METABOX_PREFIX . 'service_package_new_title'];
                            $new_list_key = FELAN_METABOX_PREFIX . 'service_package_new_list';
                            $new_list = isset($package[$new_list_key]) ? $package[$new_list_key] : [];
                            if (!empty($new_title) && !empty($new_list)) :
                                if (in_array('basic', $new_list)) :
                    ?>
                                    <li>
                                        <span><?php echo esc_html($new_title); ?></span>
                                        <span class="check">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.71278 3.64026C10.2941 3.14489 10.5847 2.8972 10.8886 2.75195C11.5915 2.41602 12.4085 2.41602 13.1114 2.75195C13.4153 2.8972 13.7059 3.14489 14.2872 3.64026C14.8856 4.15023 15.4938 4.40761 16.2939 4.47146C17.0552 4.53222 17.4359 4.56259 17.7535 4.67477C18.488 4.93421 19.0658 5.51198 19.3252 6.24652C19.4374 6.5641 19.4678 6.94476 19.5285 7.70608C19.5924 8.50621 19.8498 9.11436 20.3597 9.71278C20.8551 10.2941 21.1028 10.5847 21.248 10.8886C21.584 11.5915 21.584 12.4085 21.248 13.1114C21.1028 13.4153 20.8551 13.7059 20.3597 14.2872C19.8391 14.8981 19.5911 15.5102 19.5285 16.2939C19.4678 17.0552 19.4374 17.4359 19.3252 17.7535C19.0658 18.488 18.488 19.0658 17.7535 19.3252C17.4359 19.4374 17.0552 19.4678 16.2939 19.5285C15.4938 19.5924 14.8856 19.8498 14.2872 20.3597C13.7059 20.8551 13.4153 21.1028 13.1114 21.248C12.4085 21.584 11.5915 21.584 10.8886 21.248C10.5847 21.1028 10.2941 20.8551 9.71278 20.3597C9.10185 19.8391 8.48984 19.5911 7.70608 19.5285C6.94476 19.4678 6.5641 19.4374 6.24652 19.3252C5.51198 19.0658 4.93421 18.488 4.67477 17.7535C4.56259 17.4359 4.53222 17.0552 4.47146 16.2939C4.40761 15.4938 4.15023 14.8856 3.64026 14.2872C3.14489 13.7059 2.8972 13.4153 2.75195 13.1114C2.41602 12.4085 2.41602 11.5915 2.75195 10.8886C2.8972 10.5847 3.14489 10.2941 3.64026 9.71278C4.16089 9.10185 4.40892 8.48984 4.47146 7.70608C4.53222 6.94476 4.56259 6.5641 4.67477 6.24652C4.93421 5.51198 5.51198 4.93421 6.24652 4.67477C6.5641 4.56259 6.94476 4.53222 7.70608 4.47146C8.50621 4.40761 9.11436 4.15023 9.71278 3.64026Z" stroke="#3AB446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M8.66797 12.6302L10.1738 14.3512C10.5972 14.835 11.3606 14.7994 11.7371 14.2781L15.3346 9.29688" stroke="#3AB446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>
                    <?php endforeach;
                    endif; ?>
                </ul>
                <?php if ($enable_wallet_mode == '1' &&  !empty($service_addon[0]['felan-service_addons_title'])) : ?>
                    <div class="package-addons-warrper mt-3">
                        <h4 class="mb-3"><?php echo esc_html__('Add-ons services', 'felan-framework'); ?></h4>
                        <ul class="package-addons custom-scrollbar">
                            <?php foreach ($service_addon as $key => $addon) {
                                $count = $key + 1;
                                if ($currency_position == 'before') {
                                    $addon_price = $currency_sign_default . $addon['felan-service_addons_price'];
                                } else {
                                    $addon_price = $addon['felan-service_addons_price'] . $currency_sign_default;
                                }
                                $addon_time = !empty($addon['felan-service_addons_time']) ? $addon['felan-service_addons_time'] : 0;
                            ?>
                                <?php if (!empty($addon['felan-service_addons_title'])) : ?>
                                    <li>
                                        <input type="checkbox" id="package-addons-<?php echo $count; ?>"
                                            class="custom-checkbox input-control" name="package_addons[]"
                                            value="<?php echo $addon['felan-service_addons_price']; ?>"
                                            data-title="<?php echo $addon['felan-service_addons_title']; ?>"
                                            data-time="<?php echo $addon_time; ?>" />
                                        <label for="package-addons-<?php echo $count; ?>">
                                            <span class="addons-left">
                                                <span class="title"><?php echo $addon['felan-service_addons_title']; ?></span>
                                                <span class="content"><?php echo sprintf(esc_html__('%1s %2s delivery', 'felan-framework'), $addon['felan-service_addons_time'], $service_time) ?></span>

                                            </span>
                                            <span class="price"><?php echo $addon_price; ?></span>
                                        </label>
                                    </li>
                                <?php endif; ?>
                            <?php } ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (is_user_logged_in() && in_array('felan_user_employer', (array)$current_user->roles)) { ?>
                <?php if ($user_id == $author_id) { ?>
                    <?php if ($enable_wallet_mode == '1') : ?>
                        <a href="#" class="felan-button button-block btn-add-to-message"
                            data-text="<?php echo esc_attr('This feature is not available for the same user ID', 'felan-framework'); ?>">
                            <?php echo sprintf(esc_html__('Continue (%s)', 'felan-framework'), $service_total_basic_price) ?>
                        </a>
                    <?php else: ?>
                        <a href="#" class="felan-button button-block btn-add-to-message"
                            data-text="<?php echo esc_attr('This feature is not available for the same user ID', 'felan-framework'); ?>">
                            <?php echo sprintf(esc_html__('Continue (%s)', 'felan-framework'), $service_basic_price) ?>
                        </a>
                    <?php endif; ?>
                <?php } else { ?>
                    <?php if ($enable_wallet_mode == '1') : ?>
                        <a class="felan-button button-block btn-wallet-submit" href="#"
                            data-price="<?php echo esc_attr($service_total_basic_price); ?>"
                            data-time="<?php echo esc_attr($service_basic_time); ?>"
                            data-des="<?php echo esc_attr($service_basic_des); ?>"
                            data-time-type="<?php echo esc_attr($service_time); ?>">
                            <span><?php echo esc_html__('Continue ', 'felan-framework'); ?></span>
                            <?php if ($currency_position == 'before') : ?>
                                <span>(<?php echo $currency_sign_default; ?></span><span class="number"><?php echo felan_get_format_number($service_total_basic_price); ?>)</span>
                            <?php else : ?>
                                <span class="number"><?php echo felan_get_format_number($service_total_basic_price); ?></span><span><?php echo $currency_sign_default; ?></span>
                            <?php endif; ?>
                            <span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
                        </a>
                    <?php else: ?>
                        <a class="felan-button button-block btn-submit-addons" href="#"
                            data-price="<?php echo esc_attr($service_basic_price_default); ?>"
                            data-time="<?php echo esc_attr($service_basic_time); ?>"
                            data-des="<?php echo esc_attr($service_basic_des); ?>"
                            data-time-type="<?php echo esc_attr($service_time); ?>">
                            <?php echo sprintf(esc_html__('Continue (%s)', 'felan-framework'), $service_basic_price) ?>
                            <span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
                        </a>
                    <?php endif; ?>
                <?php } ?>
            <?php } else { ?>
                <?php if ($enable_wallet_mode == '1') : ?>
                    <div class="logged-out">
                        <a href="#popup-form" class="felan-button button-block btn-login tooltip notice-employer" data-notice="<?php esc_attr_e('Please access the role Employer', 'felan-framework') ?>">
                            <?php echo sprintf(esc_html__('Continue (%s)', 'felan-framework'), $service_total_basic_price) ?>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="logged-out">
                        <a href="#popup-form" class="felan-button button-block btn-login tooltip notice-employer" data-notice="<?php esc_attr_e('Please access the role Employer', 'felan-framework') ?>">
                            <?php echo sprintf(esc_html__('Continue (%s)', 'felan-framework'), $service_basic_price) ?>
                        </a>
                    </div>
                <?php endif; ?>
            <?php } ?>
        </div>
        <div id="tab-standard" class="tab-single-info">
            <div class="package-top">
                <span><?php echo esc_html__('From ', 'felan-framework'); ?></span>
                <span class="price"><?php echo esc_html($service_standard_price); ?></span>
                <p class="des"><?php echo wpautop($service_standard_des); ?></p>
            </div>
            <div class="package-center">
                <ul class="content">
                    <li>
                        <span><?php echo esc_html__('Delivery Time', 'felan-framework'); ?></span>
                        <span class="delivery-time"><?php echo felan_service_delivery_time($service_id, $service_standard_time); ?></span>
                    </li>
                    <li>
                        <span><?php echo esc_html__('Number of Revisions', 'felan-framework'); ?></span>
                        <span class="revisions"><?php echo felan_service_revisions($service_id, 'standard'); ?></span>
                    </li>
                    <?php if (is_array($package_service) && !empty($package_service)) :
                        foreach ($package_service as $key => $package) :
                            $service_package_list_key = FELAN_METABOX_PREFIX . 'service_package_list' . $key;
                            $service_package_title_key = FELAN_METABOX_PREFIX . 'service_package_title' . $key;
                            $new_title = get_post_meta($service_id, $service_package_title_key, true);
                            $new_list = get_post_meta($service_id, $service_package_list_key, true);

                            if (!empty($new_title) &&  !empty($new_list)) :
                                if (in_array('standard', $new_list)) :
                    ?>
                                    <li>
                                        <span><?php echo esc_html($new_title); ?></span>
                                        <span class="check">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.71278 3.64026C10.2941 3.14489 10.5847 2.8972 10.8886 2.75195C11.5915 2.41602 12.4085 2.41602 13.1114 2.75195C13.4153 2.8972 13.7059 3.14489 14.2872 3.64026C14.8856 4.15023 15.4938 4.40761 16.2939 4.47146C17.0552 4.53222 17.4359 4.56259 17.7535 4.67477C18.488 4.93421 19.0658 5.51198 19.3252 6.24652C19.4374 6.5641 19.4678 6.94476 19.5285 7.70608C19.5924 8.50621 19.8498 9.11436 20.3597 9.71278C20.8551 10.2941 21.1028 10.5847 21.248 10.8886C21.584 11.5915 21.584 12.4085 21.248 13.1114C21.1028 13.4153 20.8551 13.7059 20.3597 14.2872C19.8391 14.8981 19.5911 15.5102 19.5285 16.2939C19.4678 17.0552 19.4374 17.4359 19.3252 17.7535C19.0658 18.488 18.488 19.0658 17.7535 19.3252C17.4359 19.4374 17.0552 19.4678 16.2939 19.5285C15.4938 19.5924 14.8856 19.8498 14.2872 20.3597C13.7059 20.8551 13.4153 21.1028 13.1114 21.248C12.4085 21.584 11.5915 21.584 10.8886 21.248C10.5847 21.1028 10.2941 20.8551 9.71278 20.3597C9.10185 19.8391 8.48984 19.5911 7.70608 19.5285C6.94476 19.4678 6.5641 19.4374 6.24652 19.3252C5.51198 19.0658 4.93421 18.488 4.67477 17.7535C4.56259 17.4359 4.53222 17.0552 4.47146 16.2939C4.40761 15.4938 4.15023 14.8856 3.64026 14.2872C3.14489 13.7059 2.8972 13.4153 2.75195 13.1114C2.41602 12.4085 2.41602 11.5915 2.75195 10.8886C2.8972 10.5847 3.14489 10.2941 3.64026 9.71278C4.16089 9.10185 4.40892 8.48984 4.47146 7.70608C4.53222 6.94476 4.56259 6.5641 4.67477 6.24652C4.93421 5.51198 5.51198 4.93421 6.24652 4.67477C6.5641 4.56259 6.94476 4.53222 7.70608 4.47146C8.50621 4.40761 9.11436 4.15023 9.71278 3.64026Z" stroke="#3AB446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M8.66797 12.6302L10.1738 14.3512C10.5972 14.835 11.3606 14.7994 11.7371 14.2781L15.3346 9.29688" stroke="#3AB446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>
                    <?php endforeach;
                    endif; ?>
                    <?php if (!empty($service_package_new)) :
                        foreach ($service_package_new as $index => $package) :
                            $new_title = $package[FELAN_METABOX_PREFIX . 'service_package_new_title'];
                            $new_list_key = FELAN_METABOX_PREFIX . 'service_package_new_list';
                            $new_list = isset($package[$new_list_key]) ? $package[$new_list_key] : [];
                            if (!empty($new_title) && in_array('standard', $new_list)) : ?>
                                <li>
                                    <span><?php echo esc_html($new_title); ?></span>
                                    <span class="check">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.71278 3.64026C10.2941 3.14489 10.5847 2.8972 10.8886 2.75195C11.5915 2.41602 12.4085 2.41602 13.1114 2.75195C13.4153 2.8972 13.7059 3.14489 14.2872 3.64026C14.8856 4.15023 15.4938 4.40761 16.2939 4.47146C17.0552 4.53222 17.4359 4.56259 17.7535 4.67477C18.488 4.93421 19.0658 5.51198 19.3252 6.24652C19.4374 6.5641 19.4678 6.94476 19.5285 7.70608C19.5924 8.50621 19.8498 9.11436 20.3597 9.71278C20.8551 10.2941 21.1028 10.5847 21.248 10.8886C21.584 11.5915 21.584 12.4085 21.248 13.1114C21.1028 13.4153 20.8551 13.7059 20.3597 14.2872C19.8391 14.8981 19.5911 15.5102 19.5285 16.2939C19.4678 17.0552 19.4374 17.4359 19.3252 17.7535C19.0658 18.488 18.488 19.0658 17.7535 19.3252C17.4359 19.4374 17.0552 19.4678 16.2939 19.5285C15.4938 19.5924 14.8856 19.8498 14.2872 20.3597C13.7059 20.8551 13.4153 21.1028 13.1114 21.248C12.4085 21.584 11.5915 21.584 10.8886 21.248C10.5847 21.1028 10.2941 20.8551 9.71278 20.3597C9.10185 19.8391 8.48984 19.5911 7.70608 19.5285C6.94476 19.4678 6.5641 19.4374 6.24652 19.3252C5.51198 19.0658 4.93421 18.488 4.67477 17.7535C4.56259 17.4359 4.53222 17.0552 4.47146 16.2939C4.40761 15.4938 4.15023 14.8856 3.64026 14.2872C3.14489 13.7059 2.8972 13.4153 2.75195 13.1114C2.41602 12.4085 2.41602 11.5915 2.75195 10.8886C2.8972 10.5847 3.14489 10.2941 3.64026 9.71278C4.16089 9.10185 4.40892 8.48984 4.47146 7.70608C4.53222 6.94476 4.56259 6.5641 4.67477 6.24652C4.93421 5.51198 5.51198 4.93421 6.24652 4.67477C6.5641 4.56259 6.94476 4.53222 7.70608 4.47146C8.50621 4.40761 9.11436 4.15023 9.71278 3.64026Z" stroke="#3AB446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M8.66797 12.6302L10.1738 14.3512C10.5972 14.835 11.3606 14.7994 11.7371 14.2781L15.3346 9.29688" stroke="#3AB446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </li>
                            <?php endif; ?>
                    <?php endforeach;
                    endif;
                    ?>
                </ul>
            </div>
            <?php if (is_user_logged_in() && in_array('felan_user_employer', (array)$current_user->roles)) { ?>
                <?php if ($user_id == $author_id) { ?>
                    <a href="#" class="felan-button button-block btn-add-to-message"
                        data-text="<?php echo esc_attr('This feature is not available for the same user ID', 'felan-framework'); ?>">
                        <?php echo sprintf(esc_html__('Continue (%s)', 'felan-framework'), $service_standard_price) ?>
                    </a>
                <?php } else { ?>
                    <a class="felan-button button-block btn-submit-addons" href="#"
                        data-price="<?php echo esc_attr($service_standard_price_default); ?>"
                        data-time="<?php echo esc_attr($service_standard_time); ?>"
                        data-des="<?php echo esc_attr($service_standard_des); ?>"
                        data-time-type="<?php echo esc_attr($service_time); ?>">
                        <?php echo sprintf(esc_html__('Continue (%s)', 'felan-framework'), $service_standard_price) ?>
                        <span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
                    </a>
                <?php } ?>
            <?php } else { ?>
                <div class="logged-out">
                    <a href="#popup-form" class="felan-button button-block btn-login tooltip notice-employer" data-notice="<?php esc_attr_e('Please access the role Employer', 'felan-framework') ?>">
                        <?php echo sprintf(esc_html__('Continue (%s)', 'felan-framework'), $service_standard_price) ?>
                    </a>
                </div>
            <?php } ?>
        </div>
        <div id="tab-premium" class="tab-single-info">
            <div class="package-top">
                <span><?php echo esc_html__('From ', 'felan-framework'); ?></span>
                <span class="price"><?php echo esc_html($service_premium_price); ?></span>
                <p class="des"><?php echo wpautop($service_premium_des); ?></p>
            </div>
            <div class="package-center">
                <ul class="content">
                    <li>
                        <span><?php echo esc_html__('Delivery Time', 'felan-framework'); ?></span>
                        <span class="delivery-time"><?php echo felan_service_delivery_time($service_id, $service_premium_time); ?></span>
                    </li>
                    <li>
                        <span><?php echo esc_html__('Number of Revisions', 'felan-framework'); ?></span>
                        <span class="revisions"><?php echo felan_service_revisions($service_id, 'premium'); ?></span>
                    </li>
                    <?php if (is_array($package_service) && !empty($package_service)) :
                        foreach ($package_service as $key => $package) :
                            $service_package_list_key = FELAN_METABOX_PREFIX . 'service_package_list' . $key;
                            $service_package_title_key = FELAN_METABOX_PREFIX . 'service_package_title' . $key;
                            $new_title = get_post_meta($service_id, $service_package_title_key, true);
                            $new_list = get_post_meta($service_id, $service_package_list_key, true);

                            if (!empty($new_title) &&  !empty($new_list)) :
                                if (in_array('premium', $new_list)) :
                    ?>
                                    <li>
                                        <span><?php echo esc_html($new_title); ?></span>
                                        <span class="check">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.71278 3.64026C10.2941 3.14489 10.5847 2.8972 10.8886 2.75195C11.5915 2.41602 12.4085 2.41602 13.1114 2.75195C13.4153 2.8972 13.7059 3.14489 14.2872 3.64026C14.8856 4.15023 15.4938 4.40761 16.2939 4.47146C17.0552 4.53222 17.4359 4.56259 17.7535 4.67477C18.488 4.93421 19.0658 5.51198 19.3252 6.24652C19.4374 6.5641 19.4678 6.94476 19.5285 7.70608C19.5924 8.50621 19.8498 9.11436 20.3597 9.71278C20.8551 10.2941 21.1028 10.5847 21.248 10.8886C21.584 11.5915 21.584 12.4085 21.248 13.1114C21.1028 13.4153 20.8551 13.7059 20.3597 14.2872C19.8391 14.8981 19.5911 15.5102 19.5285 16.2939C19.4678 17.0552 19.4374 17.4359 19.3252 17.7535C19.0658 18.488 18.488 19.0658 17.7535 19.3252C17.4359 19.4374 17.0552 19.4678 16.2939 19.5285C15.4938 19.5924 14.8856 19.8498 14.2872 20.3597C13.7059 20.8551 13.4153 21.1028 13.1114 21.248C12.4085 21.584 11.5915 21.584 10.8886 21.248C10.5847 21.1028 10.2941 20.8551 9.71278 20.3597C9.10185 19.8391 8.48984 19.5911 7.70608 19.5285C6.94476 19.4678 6.5641 19.4374 6.24652 19.3252C5.51198 19.0658 4.93421 18.488 4.67477 17.7535C4.56259 17.4359 4.53222 17.0552 4.47146 16.2939C4.40761 15.4938 4.15023 14.8856 3.64026 14.2872C3.14489 13.7059 2.8972 13.4153 2.75195 13.1114C2.41602 12.4085 2.41602 11.5915 2.75195 10.8886C2.8972 10.5847 3.14489 10.2941 3.64026 9.71278C4.16089 9.10185 4.40892 8.48984 4.47146 7.70608C4.53222 6.94476 4.56259 6.5641 4.67477 6.24652C4.93421 5.51198 5.51198 4.93421 6.24652 4.67477C6.5641 4.56259 6.94476 4.53222 7.70608 4.47146C8.50621 4.40761 9.11436 4.15023 9.71278 3.64026Z" stroke="#3AB446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M8.66797 12.6302L10.1738 14.3512C10.5972 14.835 11.3606 14.7994 11.7371 14.2781L15.3346 9.29688" stroke="#3AB446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>
                    <?php endforeach;
                    endif; ?>
                    <?php if (!empty($service_package_new)) :
                        foreach ($service_package_new as $index => $package) :
                            $new_title = $package[FELAN_METABOX_PREFIX . 'service_package_new_title'];
                            $new_list_key = FELAN_METABOX_PREFIX . 'service_package_new_list';
                            $new_list = isset($package[$new_list_key]) ? $package[$new_list_key] : [];
                            if (!empty($new_title) && in_array('premium', $new_list)) : ?>
                                <li>
                                    <span><?php echo esc_html($new_title); ?></span>
                                    <span class="check">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.71278 3.64026C10.2941 3.14489 10.5847 2.8972 10.8886 2.75195C11.5915 2.41602 12.4085 2.41602 13.1114 2.75195C13.4153 2.8972 13.7059 3.14489 14.2872 3.64026C14.8856 4.15023 15.4938 4.40761 16.2939 4.47146C17.0552 4.53222 17.4359 4.56259 17.7535 4.67477C18.488 4.93421 19.0658 5.51198 19.3252 6.24652C19.4374 6.5641 19.4678 6.94476 19.5285 7.70608C19.5924 8.50621 19.8498 9.11436 20.3597 9.71278C20.8551 10.2941 21.1028 10.5847 21.248 10.8886C21.584 11.5915 21.584 12.4085 21.248 13.1114C21.1028 13.4153 20.8551 13.7059 20.3597 14.2872C19.8391 14.8981 19.5911 15.5102 19.5285 16.2939C19.4678 17.0552 19.4374 17.4359 19.3252 17.7535C19.0658 18.488 18.488 19.0658 17.7535 19.3252C17.4359 19.4374 17.0552 19.4678 16.2939 19.5285C15.4938 19.5924 14.8856 19.8498 14.2872 20.3597C13.7059 20.8551 13.4153 21.1028 13.1114 21.248C12.4085 21.584 11.5915 21.584 10.8886 21.248C10.5847 21.1028 10.2941 20.8551 9.71278 20.3597C9.10185 19.8391 8.48984 19.5911 7.70608 19.5285C6.94476 19.4678 6.5641 19.4374 6.24652 19.3252C5.51198 19.0658 4.93421 18.488 4.67477 17.7535C4.56259 17.4359 4.53222 17.0552 4.47146 16.2939C4.40761 15.4938 4.15023 14.8856 3.64026 14.2872C3.14489 13.7059 2.8972 13.4153 2.75195 13.1114C2.41602 12.4085 2.41602 11.5915 2.75195 10.8886C2.8972 10.5847 3.14489 10.2941 3.64026 9.71278C4.16089 9.10185 4.40892 8.48984 4.47146 7.70608C4.53222 6.94476 4.56259 6.5641 4.67477 6.24652C4.93421 5.51198 5.51198 4.93421 6.24652 4.67477C6.5641 4.56259 6.94476 4.53222 7.70608 4.47146C8.50621 4.40761 9.11436 4.15023 9.71278 3.64026Z" stroke="#3AB446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M8.66797 12.6302L10.1738 14.3512C10.5972 14.835 11.3606 14.7994 11.7371 14.2781L15.3346 9.29688" stroke="#3AB446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </li>
                            <?php endif; ?>
                    <?php endforeach;
                    endif;
                    ?>
                </ul>
            </div>
            <?php if (is_user_logged_in() && in_array('felan_user_employer', (array)$current_user->roles)) { ?>
                <?php if ($user_id == $author_id) { ?>
                    <a href="#" class="felan-button button-block btn-add-to-message"
                        data-text="<?php echo esc_attr('This feature is not available for the same user ID', 'felan-framework'); ?>">
                        <?php echo sprintf(esc_html__('Continue (%s)', 'felan-framework'), $service_premium_price) ?>
                    </a>
                <?php } else { ?>
                    <a class="felan-button button-block btn-submit-addons" href="#"
                        data-price="<?php echo esc_attr($service_premium_price_default); ?>"
                        data-time="<?php echo esc_attr($service_premium_time); ?>"
                        data-des="<?php echo esc_attr($service_premium_des); ?>"
                        data-time-type="<?php echo esc_attr($service_time); ?>">
                        <?php echo sprintf(esc_html__('Continue (%s)', 'felan-framework'), $service_premium_price) ?>
                        <span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
                    </a>
                <?php } ?>
            <?php } else { ?>
                <div class="logged-out">
                    <a href="#popup-form" class="felan-button button-block btn-login tooltip notice-employer" data-notice="<?php esc_attr_e('Please access the role Employer', 'felan-framework') ?>">
                        <?php echo sprintf(esc_html__('Continue (%s)', 'felan-framework'), $service_premium_price) ?>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
    <a href="#service-package-details" class="compare-packages"><?php echo esc_html__('Compare Packages', 'felan-framework'); ?></a>
    <input type="hidden" name="service_id" class="service_id" value="<?php echo $service_id; ?>">
    <input type="hidden" name="service_package_new" class="service_package_new" value="<?php echo esc_attr(json_encode($service_package_new)); ?>">
</div>