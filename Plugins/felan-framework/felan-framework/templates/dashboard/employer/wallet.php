<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'employer-wallet');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'employer-wallet',
    'felan_my_wallet_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
        'not_wallet' => esc_html__('No wallet found', 'felan-framework'),
    )
);

wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'my-wallet-topup');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'my-wallet-topup',
    'felan_wallet_topup_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
        'not_wallet' => esc_html__('No wallet found', 'felan-framework'),
    )
);

global $current_user;
$user_id = $current_user->ID;
$currency_sign_default = felan_get_option('currency_sign_default');
$currency_position = felan_get_option('currency_position');
$enable_wallet_mode = felan_get_option('enable_wallet_mode','0');

$total_price = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'employer_withdraw_total_price', true);
if (empty($total_price)) {
    $total_price = 0;
}
if ($currency_position == 'before') {
    $total_price = $currency_sign_default . felan_get_format_number($total_price);
} else {
    $total_price = felan_get_format_number($total_price) . $currency_sign_default;
}
?>
<div class="felan-employer-withdraw entry-my-page">
    <div class="entry-title">
        <h4><?php esc_html_e('Wallet', 'felan-framework'); ?></h4>
        <div class="button-warpper">
            <?php if($enable_wallet_mode == '1'){ ?>
                <a href="#form-wallet-topup" class="felan-button button-outline-accent" id="btn-wallet-topup">
                   <?php esc_html_e('Wallet Topup', 'felan-framework') ?>
                </a>
            <?php } ?>
            <a href="#form-employer-withdraw" class="felan-button" id="btn-employer-withdraw">
                <?php esc_html_e('Withdrawal Now', 'felan-framework') ?>
            </a>
        </div>
    </div>
    <div class="felan-dashboard">
        <div class="total-action">
            <ul class="action-wrapper row">
                <li class="col-md-4 col-sm-12">
                    <div class="available-balance felan-boxdb">
                        <div class="entry-detai ">
                            <h3 class="entry-title"><?php esc_html_e('Withdrawable Balance', 'felan-framework'); ?></h3>
                            <span class="entry-number"><?php echo $total_price; ?></span>
                        </div>
                        <div class="icon-total">
                            <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-wallet-01.svg'); ?>" alt="<?php esc_attr_e('jobs', 'felan-framework'); ?>">
                        </div>
                    </div>
                </li>
                <li class="col-md-4 col-sm-12">
                    <div class="pending-balance felan-boxdb">
                        <div class="entry-detai ">
                            <h3 class="entry-title"><?php esc_html_e('Withdraw requested', 'felan-framework'); ?></h3>
                            <span class="entry-number"><?php echo felan_employer_wallet_total_price('pending'); ?></span>
                        </div>
                        <div class="icon-total">
                            <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-wallet-02.svg'); ?>" alt="<?php esc_attr_e('applications', 'felan-framework'); ?>">
                        </div>
                    </div>
                </li>
                <li class="col-md-4 col-sm-12">
                    <div class="withdrawn felan-boxdb">
                        <div class="entry-detai ">
                            <h3 class="entry-title"><?php esc_html_e('Withdrawn', 'felan-framework'); ?></h3>
                            <span class="entry-number"><?php echo felan_employer_wallet_total_price('completed'); ?></span>
                        </div>
                        <div class="icon-total">
                            <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-wallet-03.svg'); ?>" alt="<?php esc_attr_e('interviews', 'felan-framework'); ?>">
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <?php if ($enable_wallet_mode == '1') : ?>
        <div class="tab-dashboard">
            <ul class="tab-list">
                <li class="tab-item tab-wallet-item"><a href="#tab-withdrawals"><?php esc_html_e('Withdrawals', 'felan-framework'); ?></a></li>
                <li class="tab-item tab-wallet-item"><a href="#tab-topup"><?php esc_html_e('Topup History', 'felan-framework'); ?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-info" id="tab-withdrawals">
                    <?php felan_get_template('dashboard/employer/wallet/withdrawals.php'); ?>
                </div>
                <div class="tab-info" id="tab-topup">
                    <?php felan_get_template('dashboard/employer/wallet/topup.php'); ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div id="tab-withdrawals">
            <?php felan_get_template('dashboard/employer/wallet/withdrawals.php'); ?>
        </div>
    <?php endif; ?>
</div>