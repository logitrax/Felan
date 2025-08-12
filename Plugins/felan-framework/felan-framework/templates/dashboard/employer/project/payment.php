<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

wp_enqueue_script('razorpay_checkout', 'https://checkout.razorpay.com/v1/checkout.js', null, null);
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'project-payment');
global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;

$currency_sign_default = felan_get_option('currency_sign_default');
$project_id = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_project_id', true);
$package_proposal_price = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_proposal_price', true);
$package_projects_budget_show = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_projects_budget_show', true);
$package_proposal_time = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_proposal_time', true);
$package_proposal_fixed_time = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_proposal_fixed_time', true);
$package_proposal_rate = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_proposal_rate', true);
$project_featured  = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_featured', true);
$project_skills = get_the_terms($project_id, 'project-skills');
$project_categories =  get_the_terms($project_id, 'project-categories');
$project_location =  get_the_terms($project_id, 'felan_location');
$thumbnail = get_the_post_thumbnail_url($project_id, '70x70');
$employer_number_project_fee =  felan_get_option('employer_number_project_fee');
$author_id = get_post_field('post_author', $project_id);
$author_name = get_the_author_meta('display_name', $author_id);

$currency_sign_default = felan_get_option('currency_sign_default');
$currency_position = felan_get_option('currency_position');
if ($currency_position == 'before') {
    $proposal_total_price = $package_proposal_price . $currency_sign_default;
} else {
    $proposal_total_price = $currency_sign_default . $package_proposal_price;
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
$project_enable_paypal = felan_get_option('project_enable_paypal', 1);
$project_enable_stripe = felan_get_option('project_enable_stripe', 1);
$project_enable_woocheckout = felan_get_option('project_enable_woocheckout', 1);
$project_enable_wire_transfer = felan_get_option('project_enable_wire_transfer', 1);
$project_enable_razor = felan_get_option('project_enable_razor', 1);
?>
<div class="payment-wrap">
    <div class="row">
        <div class="col-lg-8 col-md-7 col-sm-6">
            <div class="felan-payment-method-wrap">
                <div class="entry-heading">
                    <h2 class="entry-title"><?php esc_html_e('Payment Method', 'felan-framework'); ?></h2>
                </div>
                <?php if ($project_enable_wire_transfer != 0) : ?>
                    <div class="radio wire-transfer active">
                        <label>
                            <input type="radio" name="felan_payment_method" value="wire_transfer" checked>
                            <i class="far fa-window-restore"></i><?php esc_html_e('Wire Transfer', 'felan-framework'); ?>
                        </label>
                    </div>
                <?php endif; ?>
                <?php if ($project_enable_paypal != 0) : ?>
                    <div class="radio">
                        <label>
                            <input type="radio" class="payment-paypal" name="felan_payment_method" value="paypal">
                            <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/paypal.png'); ?>" alt="<?php esc_html_e('Paypal', 'felan-framework'); ?>">
                            <?php esc_html_e('Pay With Paypal', 'felan-framework'); ?>
                        </label>
                    </div>
                <?php endif; ?>
                <?php if ($project_enable_stripe != 0) : ?>
                    <div class="radio">
                        <label>
                            <input type="radio" class="payment-stripe" name="felan_payment_method" value="stripe">
                            <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/stripe.png'); ?>" alt="<?php esc_html_e('Stripe', 'felan-framework'); ?>">
                            <?php esc_html_e('Pay with Credit Card', 'felan-framework'); ?>
                        </label>
                        <?php
                        $felan_payment = new Felan_project_Payment();
                        $felan_payment->felan_stripe_payment_project_addons($project_id, $package_proposal_price);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ($project_enable_razor != 0) : ?>
                    <div class="radio">
                        <label>
                            <input type="radio" class="payment-razor" name="felan_payment_method" value="razor">
                            <img src="https://cdn.razorpay.com/static/assets/logo/payment.svg" alt="<?php esc_html_e('Razor', 'felan-framework'); ?>">
                            <?php esc_html_e('Pay with Razor', 'felan-framework'); ?>
                        </label>
                        <?php
                        $felan_payment_razor = new Felan_project_Payment();
                        $felan_payment_razor->felan_razor_payment_project_addons($project_id);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ($project_enable_woocheckout != 0) : ?>
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
            <button id="felan_payment_project" type="submit" class="btn btn-success btn-submit gl-button"><?php esc_html_e('Pay Now', 'felan-framework'); ?></button>
        </div>
        <div class="col-lg-4 col-md-5 col-sm-6">
            <div class="felan-package-wrap package-project">
                <div class="entry-heading">
                    <h2 class="entry-title"><?php esc_html_e('Order summary', 'felan-framework'); ?></h2>
                </div>
                <div class="felan-package-item">
                    <div class="package-header">
                        <?php if (!empty($thumbnail)) : ?>
                            <img class="thumbnail" src="<?php echo $thumbnail; ?>" alt="" />
                        <?php endif; ?>
                        <h3 class="title-my-project">
                            <a href="<?php echo get_the_permalink($project_id) ?>">
                                <?php echo get_the_title($project_id); ?>
                            </a>
                        </h3>
                        <p>
                            <span><?php echo esc_html__('by', 'felan-framework') ?></span>
                            <span class="author" style="color: var(--felan-color-accent);"><?php echo $author_name; ?></span>
                        </p>
                    </div>
                    <div class="package-content">
                        <p>
                            <span class="title">
                                <i class="far fa-usd-circle"></i>
                                <?php esc_html_e('Budget', 'felan-framework') ?>
                            </span>
                            <span class="price" data-start-price="<?php echo esc_attr($package_proposal_price); ?>"><?php echo esc_html($proposal_total_price); ?></span>
                        </p>
                        <p>
                            <span class="title">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 4.5V9H12.375" stroke="#333333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <circle cx="9" cy="9" r="6.75" stroke="#333333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <?php esc_html_e('Time', 'felan-framework') ?>
                            </span>
                            <span class="time">
                                <?php if ($package_projects_budget_show == 'hourly') : ?>
                                    <?php echo sprintf(esc_html__('%2s hours', 'felan-framework'), $package_proposal_time) ?>
                                <?php else: ?>
                                    <?php echo sprintf(esc_html__('%2s %3s', 'felan-framework'), $package_proposal_fixed_time, $package_proposal_rate) ?>
                                <?php endif; ?>
                            </span>
                        </p>
                    </div>
                </div>
                <input type="hidden" name="total_price" value="<?php echo esc_attr($package_proposal_price); ?>">
            </div>
        </div>
    </div>
    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
    <?php wp_nonce_field('felan_project_payment_ajax_nonce', 'felan_project_security_payment'); ?>
</div>