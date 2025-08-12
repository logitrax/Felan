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

$project_id = isset($_GET['project_id']) ? felan_clean(wp_unslash($_GET['project_id'])) : '';
$applicants_id = isset($_GET['applicants_id']) ? felan_clean(wp_unslash($_GET['applicants_id'])) : '';
?>

<div class="entry-my-page my-project">
    <?php if (!empty($applicants_id) && !empty($project_id)) { ?>
        <div class="entry-title">
            <h4><?php esc_html_e('Projects Activity', 'felan-framework'); ?></h4>
        </div>
        <?php felan_get_template('dashboard/freelancer/my-project/order-detail.php');
    } else { ?>
        <div class="entry-title">
            <h4><?php esc_html_e('Proposals', 'felan-framework'); ?></h4>
        </div>
        <div class="tab-dashboard">
            <ul class="tab-list">
                <li class="tab-item tab-apply-item"><a
                            href="#tab-apply"><?php esc_html_e('My orders', 'felan-framework'); ?></a></li>
                <li class="tab-item tab-wishlist-item"><a
                            href="#tab-wishlist"><?php esc_html_e('Project Wishlist', 'felan-framework'); ?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-info" id="tab-apply">
                    <?php felan_get_template('dashboard/freelancer/my-project/my-apply.php'); ?>
                </div>
                <div class="tab-info" id="tab-wishlist">
                    <?php felan_get_template('dashboard/freelancer/my-project/my-wishlist.php'); ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

