<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$get_order_id = isset($_GET['order_id']) ? felan_clean(wp_unslash($_GET['order_id'])) : '';
$get_disputes_id = isset($_GET['disputes_id']) ? felan_clean(wp_unslash($_GET['disputes_id'])) : '';
$get_post_type = isset($_GET['listing']) ? felan_clean(wp_unslash($_GET['listing'])) : '';
?>
<?php if (!empty($get_order_id) && !empty($get_disputes_id) && !empty($get_post_type == 'project')) { ?>
    <div class="felan-project-disputes-detail entry-my-page">
        <div class="entry-title">
            <h4><?php esc_html_e('Dispute History', 'felan-framework'); ?></h4>
        </div>
        <?php felan_get_template('dashboard/freelancer/my-project/disputes-detail.php'); ?>
    </div>
<?php } elseif (!empty($get_order_id) && !empty($get_disputes_id)) { ?>
    <div class="felan-disputes entry-my-page">
        <div class="entry-title">
            <h4><?php esc_html_e('Dispute History', 'felan-framework'); ?></h4>
        </div>
        <?php felan_get_template('dashboard/freelancer/service/disputes-detail.php'); ?>
    </div>
<?php } else { ?>
    <div class="felan-employer-disputes entry-my-page">
        <div class="entry-title">
            <h4><?php esc_html_e('Dispute History', 'felan-framework'); ?></h4>
        </div>
        <div class="tab-dashboard">
            <ul class="tab-list">
                <li class="tab-item tab-service-item"><a href="#tab-service"><?php esc_html_e('Services', 'felan-framework'); ?></a></li>
                <li class="tab-item tab-project-item"><a href="#tab-project"><?php esc_html_e('Projects', 'felan-framework'); ?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-info" id="tab-service">
                    <?php felan_get_template('dashboard/freelancer/service/service-disputes.php'); ?>
                </div>
                <div class="tab-info" id="tab-project">
                    <?php felan_get_template('dashboard/freelancer/my-project/project-disputes.php'); ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

