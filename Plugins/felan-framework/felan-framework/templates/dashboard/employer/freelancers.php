<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="entry-my-page my-freelancer">
    <div class="entry-title">
        <h4><?php esc_html_e('Following', 'felan-framework'); ?></h4>
    </div>
    <div class="tab-dashboard">
        <ul class="tab-list">
            <li class="tab-item tab-my-follow"><a href="#tab-my-follow" data-text="<?php esc_attr_e('Following', 'felan-framework'); ?>"><?php esc_html_e('Following', 'felan-framework'); ?></a></li>
            <li class="tab-item tab-freelancers-dashboard"><a href="#tab-freelancers-dashboard" data-text="<?php esc_attr_e('Followers', 'felan-framework'); ?>"><?php esc_html_e('Followers', 'felan-framework'); ?></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-info" id="tab-my-follow">
                <?php felan_get_template('dashboard/employer/freelancer/my-follow.php'); ?>
            </div>
            <div class="tab-info" id="tab-freelancers-dashboard">
                <?php felan_get_template('dashboard/employer/freelancer/freelancers-follow.php'); ?>
            </div>
        </div>
    </div>
</div>