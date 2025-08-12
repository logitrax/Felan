<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!is_user_logged_in()) {
    felan_get_template('global/access-denied.php', array('type' => 'not_login'));
    return;
}

global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$id = get_the_ID();
$jobs_id = isset($_GET['jobs_id']) ? felan_clean(wp_unslash($_GET['jobs_id'])) : '';
$pages = isset($_GET['pages']) ? felan_clean(wp_unslash($_GET['pages'])) : '';

if (!empty($jobs_id) && $pages == 'edit') {
    $job_author_id = get_post_field('post_author', $jobs_id);
    if ($user_id == $job_author_id) {
        felan_get_template('jobs/edit.php');
    } else { ?>
        <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                "You do not have permission to access this page",
                "felan-framework"
            ); ?></p>
    <?php }
} else if (!empty($jobs_id) && $pages == 'performance') {
    $job_author_id = get_post_field('post_author', $jobs_id);
    if ($user_id == $job_author_id) {
        felan_get_template('dashboard/employer/jobs-performance.php');
    } else { ?>
        <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                "You do not have permission to access this page",
                "felan-framework"
            ); ?></p>
    <?php }
} else { ?>
    <?php felan_withdraw_noti_print('featured_jobs','You will be charged %s for a featured jobs.'); ?>
    <div class="entry-my-page jobs-dashboard">
        <div class="entry-title">
            <h4><?php esc_html_e('My Jobs', 'felan-framework'); ?></h4>
            <a href="<?php echo felan_get_permalink('jobs_submit');?>" class="felan-button button-icon-right">
                <?php esc_html_e('Create New Jobs', 'felan-framework'); ?>
                <i class="far fa-plus"></i>
            </a>
        </div>
        <div class="tab-dashboard">
            <ul class="tab-list">
                <li class="tab-item tab-jobs-item"><a href="#tab-jobs"><?php esc_html_e('My Jobs', 'felan-framework'); ?></a></li>
                <li class="tab-item tab-applicants-item"><a href="#tab-applicants"><?php esc_html_e('Applicants', 'felan-framework'); ?></a></li>
                <li class="tab-item tab-invite"><a href="#tab-invite" data-text="<?php esc_attr_e('Job Invites', 'felan-framework'); ?>"><?php esc_html_e('Job Invite', 'felan-framework'); ?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-info" id="tab-jobs">
                    <?php felan_get_template('dashboard/employer/my-jobs.php'); ?>
                </div>
                <div class="tab-info" id="tab-applicants">
                    <?php felan_get_template('dashboard/employer/applicants.php'); ?>
                </div>
                <div class="tab-info" id="tab-invite">
                    <?php felan_get_template('dashboard/employer/freelancer/freelancers-invite.php'); ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>