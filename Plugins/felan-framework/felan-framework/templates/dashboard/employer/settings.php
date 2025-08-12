<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$show_employer_payout = felan_get_option('show_employer_payout', 1)
?>
<div class="entry-my-page settings-dashboard">
    <div class="entry-title">
        <h4><?php esc_html_e('Settings', 'felan-framework') ?></h4>
    </div>
    <div class="tab-dashboard">
        <ul class="tab-list">
            <li class="tab-item tab-profile"><a href="#tab-profile"><?php esc_html_e('Personal info', 'felan-framework'); ?></a></li>
            <?php if ($show_employer_payout) { ?>
                <li class="tab-item tab-payout"><a href="#tab-payout"><?php esc_html_e('Payout', 'felan-framework'); ?></a></li>
            <?php } ?>
        </ul>
        <div class="tab-content">
            <div class="tab-info" id="tab-profile">
                <?php felan_get_template('dashboard/employer/settings/profile.php'); ?>
            </div>
            <?php if ($show_employer_payout) { ?>
                <div class="tab-info" id="tab-payout">
                    <?php felan_get_template('dashboard/payout/payout.php'); ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>