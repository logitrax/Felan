<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $current_user;
$user_id = $current_user->ID;
$service_id = get_the_ID();
if (!empty($service_single_id)) {
    $service_id = $service_single_id;
}
$author_id = get_post_field('post_author', $service_id);

$service_quantity = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_quantity', true);
$service_time = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_time', true);
$currency_sign_default = felan_get_option('currency_sign_default');
$service_basic_price_default = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_basic_price', true);
$service_basic_time = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_basic_time', true);
$service_basic_des = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_basic_des', true);
$service_standard_price_default = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_standard_price', true);
$service_standard_time = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_standard_time', true);
$service_standard_des = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_standard_des', true);
$service_premium_price_default = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_premium_price', true);
$service_premium_des = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_premium_des', true);
$service_premium_time = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_premium_time', true);
$service_package_new = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_package_new', true);

$currency_position = felan_get_option('currency_position');
if ($currency_position == 'before') {
    $service_basic_price = $currency_sign_default . felan_get_format_number($service_basic_price_default);
    $service_standard_price = $currency_sign_default . felan_get_format_number($service_standard_price_default);
    $service_premium_price = $currency_sign_default . felan_get_format_number($service_premium_price_default);
} else {
    $service_basic_price = felan_get_format_number($service_basic_price_default) . $currency_sign_default;
    $service_standard_price = felan_get_format_number($service_standard_price_default) . $currency_sign_default;
    $service_premium_price = felan_get_format_number($service_premium_price_default) . $currency_sign_default;
}

?>
<div class="felan-block-inner block-archive-inner service-package-details service-package-submit" id="service-package-details">
    <h4 class="title-service"><?php esc_html_e('Packages', 'felan-framework') ?></h4>
    <div class="table-responsive">
        <table class="table-service-package table-striped">
            <thead>
                <tr>
                    <th>
                        <span class="thead"><?php esc_html_e('Packages', 'felan-framework') ?></span>
                    </th>
                    <th>
                        <span class="thead title"><?php esc_html_e('Basic', 'felan-framework') ?></span>
                        <p class="price"><?php echo esc_html($service_basic_price); ?></p>
                        <?php if (is_user_logged_in() && in_array('felan_user_employer', (array)$current_user->roles)) { ?>
                            <?php if($user_id == $author_id) { ?>
                                <a href="#" class="felan-button button-block btn-add-to-message"
                                   data-text="<?php echo esc_attr('This feature is not available for the same user ID', 'felan-framework'); ?>">
                                    <?php esc_html_e('Select', 'felan-framework') ?>
                                </a>
                            <?php } else { ?>
                                <a href="#" class="felan-button button-block btn-submit-addons"
                                   data-price="<?php echo esc_attr($service_basic_price_default); ?>"
                                   data-time="<?php echo esc_attr($service_basic_time); ?>"
                                   data-des="<?php echo esc_attr($service_basic_des); ?>"
                                   data-time-type="<?php echo esc_attr($service_time); ?>">
                                    <?php esc_html_e('Select', 'felan-framework') ?>
                                    <span class="btn-loading"><i class="fal fa-spinner fa-spin large"></i></span>
                                </a>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="logged-out">
                                <a href="#popup-form" class="felan-button button-block btn-login tooltip notice-employer" data-notice="<?php esc_attr_e('Please access the role Employer', 'felan-framework') ?>">
                                    <?php esc_html_e('Select', 'felan-framework') ?>
                                </a>
                            </div>
                        <?php } ?>
                    </th>
                    <?php if ($service_quantity === '2' || $service_quantity === '3') : ?>
                        <th>
                            <span class="thead title"><?php esc_html_e('Standard', 'felan-framework') ?></span>
                            <p class="price"><?php echo esc_html($service_standard_price); ?></p>
                            <?php if (is_user_logged_in() && in_array('felan_user_employer', (array)$current_user->roles)) { ?>
                                <?php if($user_id == $author_id) { ?>
                                    <a href="#" class="felan-button button-block btn-add-to-message"
                                       data-text="<?php echo esc_attr('This feature is not available for the same user ID', 'felan-framework'); ?>">
                                        <?php esc_html_e('Select', 'felan-framework') ?>
                                    </a>
                                <?php } else { ?>
                                    <a href="#" class="felan-button button-block btn-submit-addons"
                                       data-price="<?php echo esc_attr($service_standard_price_default); ?>"
                                       data-time="<?php echo esc_attr($service_standard_time); ?>"
                                       data-des="<?php echo esc_attr($service_standard_des); ?>"
                                       data-time-type="<?php echo esc_attr($service_time); ?>">
                                        <?php esc_html_e('Select', 'felan-framework') ?>
                                        <span class="btn-loading"><i class="fal fa-spinner fa-spin large"></i></span>
                                    </a>
                                <?php } ?>
                            <?php } else { ?>
                                <div class="logged-out">
                                    <a href="#popup-form" class="felan-button button-block btn-login tooltip notice-employer" data-notice="<?php esc_attr_e('Please access the role Employer', 'felan-framework') ?>">
                                        <?php esc_html_e('Select', 'felan-framework') ?>
                                    </a>
                                </div>
                            <?php } ?>
                        </th>
                    <?php endif; ?>
                    <?php if ($service_quantity === '3') : ?>
                        <th>
                            <span class="thead title"><?php esc_html_e('Premium', 'felan-framework') ?></span>
                            <p class="price"><?php echo esc_html($service_premium_price); ?></p>
                            <?php if (is_user_logged_in() && in_array('felan_user_employer', (array)$current_user->roles)) { ?>
                                <?php if($user_id == $author_id) { ?>
                                    <a href="#" class="felan-button button-block btn-add-to-message"
                                       data-text="<?php echo esc_attr('This feature is not available for the same user ID', 'felan-framework'); ?>">
                                        <?php esc_html_e('Select', 'felan-framework') ?>
                                    </a>
                                <?php } else { ?>
                                    <a href="#" class="felan-button button-block btn-submit-addons"
                                       data-price="<?php echo esc_attr($service_premium_price_default); ?>"
                                       data-time="<?php echo esc_attr($service_premium_time); ?>"
                                       data-des="<?php echo esc_attr($service_premium_des); ?>"
                                       data-time-type="<?php echo esc_attr($service_time); ?>">
                                        <?php esc_html_e('Select', 'felan-framework') ?>
                                        <span class="btn-loading"><i class="fal fa-spinner fa-spin large"></i></span>
                                    </a>
                                <?php } ?>
                            <?php } else { ?>
                                <div class="logged-out">
                                    <a href="#popup-form" class="felan-button button-block btn-login tooltip notice-employer" data-notice="<?php esc_attr_e('Please access the role Employer', 'felan-framework') ?>">
                                        <?php esc_html_e('Select', 'felan-framework') ?>
                                    </a>
                                </div>
                            <?php } ?>
                        </th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?php esc_html_e('Delivery Time', 'felan-framework') ?>
                    </td>
                    <td>
                        <?php echo esc_html($service_basic_time . ' ' . $service_time); ?>
                    </td>
                    <?php if ($service_quantity === '2' || $service_quantity === '3') : ?>
                        <td>
                            <?php echo esc_html($service_standard_time . ' ' . $service_time); ?>
                        </td>
                    <?php endif; ?>
                    <?php if ($service_quantity === '3') : ?>
                        <td>
                            <?php echo esc_html($service_premium_time . ' ' . $service_time); ?>
                        </td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td>
                        <?php esc_html_e('Number of Revisions', 'felan-framework') ?>
                    </td>
                    <td>
                        <?php echo felan_service_revisions($service_id, 'basic'); ?>
                    </td>
                    <?php if ($service_quantity === '2' || $service_quantity === '3') : ?>
                        <td>
                            <?php echo felan_service_revisions($service_id, 'standard'); ?>
                        </td>
                    <?php endif; ?>
                    <?php if ($service_quantity === '3') : ?>
                        <td>
                            <?php echo felan_service_revisions($service_id, 'premium'); ?>
                        </td>
                    <?php endif; ?>
                </tr>
                <?php if (!empty($service_package_new)) :
                    foreach ($service_package_new as $index => $package) :
                        $new_title = $package[FELAN_METABOX_PREFIX . 'service_package_new_title'];
                        $new_list_key = FELAN_METABOX_PREFIX . 'service_package_new_list';
                        $new_list = isset($package[$new_list_key]) ? $package[$new_list_key] : [];
                        if (!empty($new_list)) {
                ?>
                            <tr>
                                <td>
                                    <?php echo esc_html($new_title); ?>
                                </td>
                                <td>
                                    <?php echo (in_array('basic', $new_list) ? '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.71278 3.64026C10.2941 3.14489 10.5847 2.8972 10.8886 2.75195C11.5915 2.41602 12.4085 2.41602 13.1114 2.75195C13.4153 2.8972 13.7059 3.14489 14.2872 3.64026C14.8856 4.15023 15.4938 4.40761 16.2939 4.47146C17.0552 4.53222 17.4359 4.56259 17.7535 4.67477C18.488 4.93421 19.0658 5.51198 19.3252 6.24652C19.4374 6.5641 19.4678 6.94476 19.5285 7.70608C19.5924 8.50621 19.8498 9.11436 20.3597 9.71278C20.8551 10.2941 21.1028 10.5847 21.248 10.8886C21.584 11.5915 21.584 12.4085 21.248 13.1114C21.1028 13.4153 20.8551 13.7059 20.3597 14.2872C19.8391 14.8981 19.5911 15.5102 19.5285 16.2939C19.4678 17.0552 19.4374 17.4359 19.3252 17.7535C19.0658 18.488 18.488 19.0658 17.7535 19.3252C17.4359 19.4374 17.0552 19.4678 16.2939 19.5285C15.4938 19.5924 14.8856 19.8498 14.2872 20.3597C13.7059 20.8551 13.4153 21.1028 13.1114 21.248C12.4085 21.584 11.5915 21.584 10.8886 21.248C10.5847 21.1028 10.2941 20.8551 9.71278 20.3597C9.10185 19.8391 8.48984 19.5911 7.70608 19.5285C6.94476 19.4678 6.5641 19.4374 6.24652 19.3252C5.51198 19.0658 4.93421 18.488 4.67477 17.7535C4.56259 17.4359 4.53222 17.0552 4.47146 16.2939C4.40761 15.4938 4.15023 14.8856 3.64026 14.2872C3.14489 13.7059 2.8972 13.4153 2.75195 13.1114C2.41602 12.4085 2.41602 11.5915 2.75195 10.8886C2.8972 10.5847 3.14489 10.2941 3.64026 9.71278C4.16089 9.10185 4.40892 8.48984 4.47146 7.70608C4.53222 6.94476 4.56259 6.5641 4.67477 6.24652C4.93421 5.51198 5.51198 4.93421 6.24652 4.67477C6.5641 4.56259 6.94476 4.53222 7.70608 4.47146C8.50621 4.40761 9.11436 4.15023 9.71278 3.64026Z" stroke="#3AB446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8.66797 12.6302L10.1738 14.3512C10.5972 14.835 11.3606 14.7994 11.7371 14.2781L15.3346 9.29688" stroke="#3AB446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>' : '_'); ?>
                                </td>
                                <?php if ($service_quantity === '2' || $service_quantity === '3') : ?>
                                    <td>
                                        <?php echo (in_array('standard', $new_list) ? '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.71278 3.64026C10.2941 3.14489 10.5847 2.8972 10.8886 2.75195C11.5915 2.41602 12.4085 2.41602 13.1114 2.75195C13.4153 2.8972 13.7059 3.14489 14.2872 3.64026C14.8856 4.15023 15.4938 4.40761 16.2939 4.47146C17.0552 4.53222 17.4359 4.56259 17.7535 4.67477C18.488 4.93421 19.0658 5.51198 19.3252 6.24652C19.4374 6.5641 19.4678 6.94476 19.5285 7.70608C19.5924 8.50621 19.8498 9.11436 20.3597 9.71278C20.8551 10.2941 21.1028 10.5847 21.248 10.8886C21.584 11.5915 21.584 12.4085 21.248 13.1114C21.1028 13.4153 20.8551 13.7059 20.3597 14.2872C19.8391 14.8981 19.5911 15.5102 19.5285 16.2939C19.4678 17.0552 19.4374 17.4359 19.3252 17.7535C19.0658 18.488 18.488 19.0658 17.7535 19.3252C17.4359 19.4374 17.0552 19.4678 16.2939 19.5285C15.4938 19.5924 14.8856 19.8498 14.2872 20.3597C13.7059 20.8551 13.4153 21.1028 13.1114 21.248C12.4085 21.584 11.5915 21.584 10.8886 21.248C10.5847 21.1028 10.2941 20.8551 9.71278 20.3597C9.10185 19.8391 8.48984 19.5911 7.70608 19.5285C6.94476 19.4678 6.5641 19.4374 6.24652 19.3252C5.51198 19.0658 4.93421 18.488 4.67477 17.7535C4.56259 17.4359 4.53222 17.0552 4.47146 16.2939C4.40761 15.4938 4.15023 14.8856 3.64026 14.2872C3.14489 13.7059 2.8972 13.4153 2.75195 13.1114C2.41602 12.4085 2.41602 11.5915 2.75195 10.8886C2.8972 10.5847 3.14489 10.2941 3.64026 9.71278C4.16089 9.10185 4.40892 8.48984 4.47146 7.70608C4.53222 6.94476 4.56259 6.5641 4.67477 6.24652C4.93421 5.51198 5.51198 4.93421 6.24652 4.67477C6.5641 4.56259 6.94476 4.53222 7.70608 4.47146C8.50621 4.40761 9.11436 4.15023 9.71278 3.64026Z" stroke="#3AB446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8.66797 12.6302L10.1738 14.3512C10.5972 14.835 11.3606 14.7994 11.7371 14.2781L15.3346 9.29688" stroke="#3AB446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>' : '_'); ?>
                                    </td>
                                <?php endif; ?>
                                <?php if ($service_quantity === '3') : ?>
                                    <td>
                                        <?php echo (in_array('premium', $new_list) ? '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.71278 3.64026C10.2941 3.14489 10.5847 2.8972 10.8886 2.75195C11.5915 2.41602 12.4085 2.41602 13.1114 2.75195C13.4153 2.8972 13.7059 3.14489 14.2872 3.64026C14.8856 4.15023 15.4938 4.40761 16.2939 4.47146C17.0552 4.53222 17.4359 4.56259 17.7535 4.67477C18.488 4.93421 19.0658 5.51198 19.3252 6.24652C19.4374 6.5641 19.4678 6.94476 19.5285 7.70608C19.5924 8.50621 19.8498 9.11436 20.3597 9.71278C20.8551 10.2941 21.1028 10.5847 21.248 10.8886C21.584 11.5915 21.584 12.4085 21.248 13.1114C21.1028 13.4153 20.8551 13.7059 20.3597 14.2872C19.8391 14.8981 19.5911 15.5102 19.5285 16.2939C19.4678 17.0552 19.4374 17.4359 19.3252 17.7535C19.0658 18.488 18.488 19.0658 17.7535 19.3252C17.4359 19.4374 17.0552 19.4678 16.2939 19.5285C15.4938 19.5924 14.8856 19.8498 14.2872 20.3597C13.7059 20.8551 13.4153 21.1028 13.1114 21.248C12.4085 21.584 11.5915 21.584 10.8886 21.248C10.5847 21.1028 10.2941 20.8551 9.71278 20.3597C9.10185 19.8391 8.48984 19.5911 7.70608 19.5285C6.94476 19.4678 6.5641 19.4374 6.24652 19.3252C5.51198 19.0658 4.93421 18.488 4.67477 17.7535C4.56259 17.4359 4.53222 17.0552 4.47146 16.2939C4.40761 15.4938 4.15023 14.8856 3.64026 14.2872C3.14489 13.7059 2.8972 13.4153 2.75195 13.1114C2.41602 12.4085 2.41602 11.5915 2.75195 10.8886C2.8972 10.5847 3.14489 10.2941 3.64026 9.71278C4.16089 9.10185 4.40892 8.48984 4.47146 7.70608C4.53222 6.94476 4.56259 6.5641 4.67477 6.24652C4.93421 5.51198 5.51198 4.93421 6.24652 4.67477C6.5641 4.56259 6.94476 4.53222 7.70608 4.47146C8.50621 4.40761 9.11436 4.15023 9.71278 3.64026Z" stroke="#3AB446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8.66797 12.6302L10.1738 14.3512C10.5972 14.835 11.3606 14.7994 11.7371 14.2781L15.3346 9.29688" stroke="#3AB446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                ' : '_'); ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php } ?>
                <?php endforeach;
                endif;
                ?>
            </tbody>
        </table>
    </div>
    <p class="note-addons"><?php esc_html_e('You can add services add-ons on the next page.', 'felan-framework') ?></p>
    <input type="hidden" name="service_id" class="service_id" value="<?php echo $service_id; ?>">
</div>