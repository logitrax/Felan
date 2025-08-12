<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'freelancer-review-company');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'freelancer-review-company',
    'felan_company_review_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
    )
);
?>

<div class="entry-my-page my-jobs">
    <div class="entry-title">
        <h4><?php esc_html_e('Applied Jobs', 'felan-framework'); ?></h4>
    </div>
    <div class="tab-dashboard">
        <ul class="tab-list">
            <li class="tab-item tab-apply-item"><a href="#tab-apply"><?php esc_html_e('Applied', 'felan-framework'); ?><span> (<?php echo felan_total_my_apply() ?>)</span></a></li>
            <li class="tab-item tab-wishlist-item"><a href="#tab-wishlist"><?php esc_html_e('Wishlist', 'felan-framework'); ?><span> (<?php echo felan_total_post('jobs', 'my_wishlist') ?>)</span></a></li>
            <li class="tab-item tab-invite-item"><a href="#tab-invite"><?php esc_html_e('Invited to Apply', 'felan-framework'); ?><span> (<?php echo felan_total_post('jobs', 'my_invite') ?>)</span></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-info" id="tab-apply">
                <?php felan_get_template('dashboard/freelancer/my-jobs/my-apply.php'); ?>
            </div>
            <div class="tab-info" id="tab-wishlist">
                <?php felan_get_template('dashboard/freelancer/my-jobs/my-wishlist.php'); ?>
            </div>
            <div class="tab-info" id="tab-invite">
                <?php felan_get_template('dashboard/freelancer/my-jobs/my-invite.php'); ?>
            </div>
        </div>
    </div>
</div>