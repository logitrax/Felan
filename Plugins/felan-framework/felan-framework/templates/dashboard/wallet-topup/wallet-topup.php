<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'wallet-payment');
wp_enqueue_script('razorpay_checkout', 'https://checkout.razorpay.com/v1/checkout.js', null, null);

global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$roles = $current_user->roles;
$current_role = !empty($roles) ? $roles[0] : 'No role';
$wallet_payment_method = isset($_GET['payment_method']) ? absint(wp_unslash($_GET['payment_method'])) : -1;
$enable_wallet_wire_transfer = felan_get_option('enable_wallet_wire_transfer', 1);
$enable_wallet_paypal = felan_get_option('enable_wallet_paypal', 1);
$enable_wallet_stripe = felan_get_option('enable_wallet_stripe', 1);
$enable_wallet_razor = felan_get_option('enable_wallet_razor', 0);
$enable_wallet_woocheckout = felan_get_option('enable_wallet_woocheckout', 0);
$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
$felan_wallet_payment = new Felan_Wallet_Payment();

if ($current_role == 'felan_user_employer') {
    $name_role = esc_html__('Employer', 'felan-framework');
} else {
    if ($enable_post_type_jobs == '1') {
        $name_role = esc_html__('Candidate', 'felan-framework');
    } else {
        $name_role = esc_html__('Freelancer', 'felan-framework');
    }
}
?>
<div class="felan-wallet-topup">
    <div class="felan-payment-method-wrap">
        <div class="price-inner">
            <label for="wallet_price"><?php esc_html_e('Amount to add to wallet', 'felan-framework'); ?><sup>
                    *</sup></label>
            <input type="number" id="wallet_price" value="" name="wallet_price"
                   placeholder="<?php echo esc_attr('0.00', 'felan-framework') ?>" required>
        </div>
        <?php if ($enable_wallet_paypal !== '1' && $enable_wallet_stripe !== '1') : ?>
            <p class="notice"><i class="far fa-exclamation-circle"></i>
                <?php esc_html_e("You have not selected any payment method.", "felan-framework"); ?>
            </p>
        <?php else: ?>
            <div class="payment-inner">
                <label for="wallet_price"><?php esc_html_e('Choose payment method', 'felan-framework'); ?><sup>
                        *</sup></label>
                <ul>
                    <?php if ($enable_wallet_wire_transfer == '1') : ?>
                        <li class="payout-item">
                            <h5 class="title"
                                data-payment="wire_transfer"><?php esc_html_e('Wire Transfer', 'felan-framework') ?></h5>
                        </li>
                    <?php endif; ?>
                    <?php if ($enable_wallet_paypal == '1') : ?>
                        <li class="payout-item">
                            <h5 class="title"
                                data-payment="paypal"><?php esc_html_e('Paypal', 'felan-framework') ?></h5>
                        </li>
                    <?php endif; ?>
                    <?php if ($enable_wallet_stripe == '1') : ?>
                        <li class="payout-item">
                            <h5 class="title"
                                data-payment="stripe"><?php esc_html_e('Stripe', 'felan-framework') ?></h5>
                            <?php $felan_wallet_payment->felan_stripe_form_payment_wallet(); ?>
                        </li>
                    <?php endif; ?>
                    <?php if ($enable_wallet_razor == '1') : ?>
                        <li class="payout-item">
                            <h5 class="title"
                                data-payment="razor"><?php esc_html_e('Razor', 'felan-framework') ?></h5>
                            <?php $felan_wallet_payment->felan_razor_payment_wallet_addons(); ?>
                        </li>
                    <?php endif; ?>
                    <?php if ($enable_wallet_woocheckout == '1') : ?>
                        <li class="payout-item">
                            <h5 class="title"
                                data-payment="woocheckout"><?php esc_html_e('Woocommerce', 'felan-framework') ?></h5>
                        </li>
                    <?php endif; ?>
                </ul>
                <input type="hidden" name="payment_method" value=""/>
                <input type="hidden" name="user_role" value="<?php echo esc_attr($name_role); ?>"/>
            </div>
        <?php endif; ?>
    </div>
    <div class="felan-message-error"></div>
    <button id="felan_payment_wallet" type="submit"
            class="btn btn-success btn-submit gl-button"><?php esc_html_e('Pay Now', 'felan-framework'); ?></button>
    <?php wp_nonce_field('felan_wallet_payment_ajax_nonce', 'felan_wallet_security_payment'); ?>
</div>

