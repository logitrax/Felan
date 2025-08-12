<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$user_id = $current_user->ID;
$project_id = isset($_GET['project_id']) ? felan_clean(wp_unslash($_GET['project_id'])) : '';
$applicants_id = isset($_GET['applicants_id']) ? felan_clean(wp_unslash($_GET['applicants_id'])) : '';
$pages = isset($_GET['pages']) ? felan_clean(wp_unslash($_GET['pages'])) : '';
$projects_submit = felan_get_permalink('projects_submit');
?>
<?php if (!empty($project_id) && $pages == 'edit') : ?>
    <?php felan_get_template('dashboard/employer/project/my-projects.php'); ?>
<?php elseif (!empty($project_id) && $pages == 'performance') : ?>
    <?php felan_get_template('dashboard/employer/project-performance.php'); ?>
<?php elseif (!empty($applicants_id) && !empty($project_id)) : ?>
    <?php felan_get_template('dashboard/employer/project/order-detail.php'); ?>
<?php else : ?>
    <div class="felan-employer-service entry-my-page">
        <?php felan_withdraw_noti_print('featured_project','You will be charged %s for a featured project.'); ?>
        <div class="entry-title">
            <h4><?php esc_html_e('My Projects', 'felan-framework'); ?></h4>
            <a href="<?php echo esc_url($projects_submit);?>" class="felan-button button-icon-right">
                <?php esc_html_e('Create New Project', 'felan-framework'); ?>
                <i class="far fa-plus"></i>
            </a>
        </div>
         <?php felan_get_template('dashboard/employer/project/my-projects.php'); ?>
    </div>
<?php endif; ?>