<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

wp_enqueue_script('razorpay_checkout', 'https://checkout.razorpay.com/v1/checkout.js', null, null);
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'service-payment');
global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);

$currency_sign_default = felan_get_option('currency_sign_default');
$service_id = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_service_id', true);
$service_time = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_time', true);
$service_package_price_default = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_package_price', true);
$service_package_time = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_package_time', true);
$service_package_time_type = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_package_time_type', true);
$service_package_des = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_package_des', true);
$service_package_new = json_encode(get_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_package_new', true));
$currency_sign_default = felan_get_option('currency_sign_default');
$service_featured  = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_featured', true);
$service_skills = get_the_terms($service_id, 'service-skills');
$service_categories =  get_the_terms($service_id, 'service-categories');
$service_location =  get_the_terms($service_id, 'felan_location');
$thumbnail = get_the_post_thumbnail_url($service_id, '70x70');
$currency_position = felan_get_option('currency_position');
$service_addon = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_tab_addon', true);

$enable_freelancer_service_fee =  felan_get_option('enable_freelancer_service_fee');
$freelancer_number_service_fee =  felan_get_option('freelancer_number_service_fee');
$price_fee = round(intval($service_package_price_default) * intval($freelancer_number_service_fee) / 100);
if ($enable_freelancer_service_fee == '1' || (!empty($freelancer_number_service_fee) || $freelancer_number_service_fee == 0)) {
    $total_price = intval($service_package_price_default) + intval($price_fee);
} else {
    $total_price = $service_package_price_default;
}
$currency_position = felan_get_option('currency_position');
if ($currency_position == 'before') {
    $service_package_price = $currency_sign_default . felan_get_format_number($service_package_price_default);
    $service_package_price_fee = $currency_sign_default . felan_get_format_number($price_fee);
} else {
    $service_package_price = felan_get_format_number($service_package_price_default) . $currency_sign_default;
    $service_package_price_fee = felan_get_format_number($price_fee) . $currency_sign_default;
}

$terms_condition = felan_get_option('terms_condition');
$allowed_html = array(
    'a' => array(
        'href' => array(),
        'title' => array(),
        'target' => array()
    ),
    'strong' => array()
);
$service_enable_paypal = felan_get_option('service_enable_paypal', 1);
$service_enable_stripe = felan_get_option('service_enable_stripe', 1);
$service_enable_woocheckout = felan_get_option('service_enable_woocheckout', 1);
$service_enable_wire_transfer = felan_get_option('service_enable_wire_transfer', 1);
$service_enable_razor = felan_get_option('service_enable_razor', 1);
?>
<div class="payment-wrap">
    <div class="row">
        <div class="col-lg-4 col-md-5 col-sm-6">
            <div class="felan-package-wrap package-service">
                <div class="entry-heading">
                    <h2 class="entry-title"><?php esc_html_e('Order summary', 'felan-framework'); ?></h2>
                </div>
                <div class="felan-package-item">
                    <div class="package-header">
                        <?php if (!empty($thumbnail)) : ?>
                            <img class="thumbnail" src="<?php echo $thumbnail; ?>" alt="" />
                        <?php endif; ?>
                        <h3 class="title-my-service">
                            <a href="<?php echo get_the_permalink($service_id) ?>">
                                <?php echo get_the_title($service_id); ?>
                            </a>
                            <a class="felan-button button-link service-change" href="<?php echo esc_url(get_post_type_archive_link('service')) ?>"><?php esc_html_e('Change Service', 'felan-framework'); ?></a>
                        </h3>
                    </div>
                    <div class="package-content">
                        <p>
                            <span class="title"><?php esc_html_e('Basic price', 'felan-framework') ?></span>
                            <span class="price" data-start-price="<?php echo esc_attr($total_price); ?>"><?php echo esc_html($service_package_price); ?></span>
                        </p>
                        <?php if (!empty($freelancer_number_service_fee) || $freelancer_number_service_fee !== '0') : ?>
                            <p>
                                <span class="title"><?php esc_html_e('Service Fee', 'felan-framework') ?></span>
                                <span class="price" data-price-fee="<?php echo esc_attr($price_fee); ?>">+ <?php echo esc_html($service_package_price_fee); ?></span>
                            </p>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($service_addon[0]['felan-service_addons_title'])) : ?>
                        <div class="package-center">
                            <h4><?php echo esc_html__('Add-ons services', 'felan-framework'); ?></h4>
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
                                                data-delivery-time="<?php echo $addon_time; ?>" />
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
                    <div class="package-bottom">
                        <p>
                            <span class="total"><?php esc_html_e('Total', 'felan-framework') ?></span>
                            <?php
                            if ($currency_position == 'before') { ?>
                                <span class="price"><?php echo esc_html($currency_sign_default); ?><span class="number"><?php echo esc_html($total_price); ?></span></span>
                            <?php } else { ?>
                                <span class="price"><span class="number"><?php echo esc_html($total_price); ?></span><?php echo esc_html($currency_sign_default); ?></span>
                            <?php } ?>
                        </p>
                        <p>
                            <span class="title"><?php esc_html_e('Delivery time', 'felan-framework') ?></span>
                            <span class="delivery-time">
                                <span class="time" data-delivery-time="<?php echo esc_attr($service_package_time); ?>"><?php echo esc_html($service_package_time); ?></span>
                                <span class="time-type"><?php echo esc_html($service_package_time_type); ?></span>
                            </span>
                        </p>
                    </div>
                </div>
                <input type="hidden" name="package_price" value="<?php echo esc_attr($service_package_price_default); ?>">
                <input type="hidden" name="total_price" value="<?php echo esc_attr($total_price); ?>">
                <input type="hidden" name="package_des" value="<?php echo esc_attr($service_package_des); ?>">
                <input type="hidden" name="package_time" value="<?php echo esc_attr($service_package_time); ?>">
                <input type="hidden" name="package_time_type" value="<?php echo esc_attr($service_package_time_type); ?>">
                <input type="hidden" name="package_new" value="<?php echo esc_attr($service_package_new); ?>">
            </div>
        </div>
        <div class="col-lg-8 col-md-7 col-sm-6">
            <div class="felan-payment-method-wrap">
                <div class="entry-heading">
                    <h2 class="entry-title"><?php esc_html_e('Payment Method', 'felan-framework'); ?></h2>
                </div>
                <?php if ($service_enable_wire_transfer != 0) : ?>
                    <div class="radio wire-transfer active">
                        <label>
                            <input type="radio" name="felan_payment_method" value="wire_transfer" checked>
                            <i class="far fa-window-restore"></i><?php esc_html_e('Wire Transfer', 'felan-framework'); ?>
                        </label>
                    </div>
                <?php endif; ?>
                <?php if ($service_enable_paypal != 0) : ?>
                    <div class="radio">
                        <label>
                            <input type="radio" class="payment-paypal" name="felan_payment_method" value="paypal">
                            <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/paypal.png'); ?>" alt="<?php esc_html_e('Paypal', 'felan-framework'); ?>">
                            <?php esc_html_e('Pay With Paypal', 'felan-framework'); ?>
                        </label>
                    </div>
                <?php endif; ?>
                <?php if ($service_enable_stripe != 0) : ?>
                    <div class="radio">
                        <label>
                            <input type="radio" class="payment-stripe" name="felan_payment_method" value="stripe">
                            <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/stripe.png'); ?>" alt="<?php esc_html_e('Stripe', 'felan-framework'); ?>">
                            <?php esc_html_e('Pay with Credit Card', 'felan-framework'); ?>
                        </label>
                        <?php
                        $felan_payment = new Felan_Service_Payment();
                        $felan_payment->felan_stripe_payment_service_addons($service_id, $total_price, $service_package_price_default, $service_addon, $service_package_time, $service_package_time_type, $service_package_des, $service_package_new);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ($service_enable_razor != 0) : ?>
                    <div class="radio">
                        <label>
                            <input type="radio" class="payment-razor" name="felan_payment_method" value="razor">
                            <img src="https://cdn.razorpay.com/static/assets/logo/payment.svg" alt="<?php esc_html_e('Razor', 'felan-framework'); ?>">
                            <?php esc_html_e('Pay with Razor', 'felan-framework'); ?>
                        </label>
                        <?php
                        $felan_payment_razor = new Felan_Service_Payment();
                        $felan_payment_razor->felan_razor_payment_project_addons($service_id);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ($service_enable_woocheckout != 0) : ?>
                    <div class="radio">
                        <label>
                            <input type="radio" class="payment-woocheckout" name="felan_payment_method" value="woocheckout">
                            <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/woocommerce-logo.png'); ?>" alt="<?php esc_html_e('Woocommerce', 'felan-framework'); ?>">
                            <?php esc_html_e('Pay with Woocommerce', 'felan-framework'); ?>
                        </label>
                    </div>
                <?php endif; ?>
            </div>
            <p class="terms-conditions"><i class="fa fa-hand-o-right"></i> <?php echo sprintf(wp_kses(__('Please read <a target="_blank" href="%s"><strong>Terms & Conditions</strong></a> first', 'felan-framework'), $allowed_html), get_permalink($terms_condition)); ?></p>
            <div class="btn-wrapper">
                <button id="felan_payment_service" type="submit" class="btn btn-success btn-submit gl-button"><?php esc_html_e('Pay Now', 'felan-framework'); ?></button>
            </div>
        </div>
    </div>
    <input type="hidden" name="service_id" value="<?php echo esc_attr($service_id); ?>">
    <input type="hidden" name="user_demo" value="<?php echo esc_attr($user_demo); ?>">
    <?php wp_nonce_field('felan_service_payment_ajax_nonce', 'felan_service_security_payment'); ?>
</div>