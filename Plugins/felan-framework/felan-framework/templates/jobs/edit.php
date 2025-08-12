<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!is_user_logged_in()) {
    felan_get_template('global/access-denied.php', array('type' => 'not_login'));
    return;
}

$jobs_id = isset($_GET['jobs_id']) ? felan_clean(wp_unslash($_GET['jobs_id'])) : '';


$jobs_salary_active   = felan_get_option('enable_single_jobs_salary', '1');
if ($jobs_salary_active) {
    $layout = array('general', 'salary', 'apply', 'company', 'location', 'additional', 'thumbnail', 'gallery', 'video');
} else {
    $layout = array('general', 'apply', 'company', 'location', 'additional', 'thumbnail', 'gallery', 'video');
}

$form     = 'edit-jobs';
$action   = 'edit_jobs';

global $jobs_data, $jobs_meta_data, $current_user, $hide_jobs_fields, $hide_jobs_group_fields;
if ($form == 'edit-jobs') {
    $jobs_data      = get_post($jobs_id);
    $jobs_meta_data = get_post_custom($jobs_data->ID);
}

$custom_field_jobs = felan_render_custom_field('jobs');
$felan_jobs_page_id  = felan_get_option('felan_jobs_dashboard_page_id', 0);
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'jobs-submit');
wp_enqueue_script('jquery-validate');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'jobs-submit',
    'felan_submit_vars',
    array(
        'ajax_url'  => FELAN_AJAX_URL,
        'not_found' => esc_html__("We didn't find any results, you can retry with other keyword.", 'felan-framework'),
        'not_jobs' => esc_html__('No jobs found', 'felan-framework'),
        'jobs_dashboard' => get_page_link($felan_jobs_page_id),
        'custom_field_jobs' => $custom_field_jobs,
    )
);

wp_get_current_user();
$user_id = $current_user->ID;
$paid_submission_type = felan_get_option('paid_submission_type', 'no');
$user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);

$hide_jobs_fields = felan_get_option('hide_jobs_fields', array());
if (!is_array($hide_jobs_fields)) {
    $hide_jobs_fields = array();
}

$hide_jobs_group_fields = felan_get_option('hide_jobs_group_fields', array());
if (!is_array($hide_jobs_group_fields)) {
    $hide_jobs_group_fields = array();
}
?>
<div class="entry-my-page submit-jobs-dashboard">
    <form action="#" method="post" id="submit_jobs_form" class="form-dashboard" enctype="multipart/form-data">
        <div class="content-jobs">
            <div class="row">
                <div class="col-lg-8 col-md-7">

                    <div class="submit-jobs-header felan-submit-header">
                        <div class="entry-title">
                            <h4><?php esc_html_e('Create a job post', 'felan-framework') ?></h4>
                        </div>
                        <div class="button-warpper">
                            <a href="<?php echo felan_get_permalink('jobs_dashboard'); ?>" class="felan-button button-link">
                                <?php esc_html_e('Cancel', 'felan-framework') ?>
                            </a>
                            <?php if ($user_demo == 'yes') : ?>
                                <button class="felan-button btn-add-to-message" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>">
                                    <span><?php esc_html_e('Update', 'felan-framework'); ?></span>
                                </button>
                            <?php else : ?>
                                <button type="submit" class="btn-submit-jobs felan-button" name="submit_jobs">
                                    <span><?php esc_html_e('Update', 'felan-framework'); ?></span>
                                    <span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php foreach ($layout as $value) {
                        switch ($value) {
                            case 'general':
                                $name = esc_html__('Basic info', 'felan-framework');
                                break;
                            case 'salary':
                                $name = esc_html__('Salary', 'felan-framework');
                                break;
                            case 'apply':
                                $name = esc_html__('Job apply type', 'felan-framework');
                                break;
                            case 'company':
                                $name = esc_html__('Company', 'felan-framework');
                                break;
                            case 'location':
                                $name = esc_html__('Location', 'felan-framework');
                                break;
                            case 'thumbnail':
                                $name = esc_html__('Cover Image', 'felan-framework');
                                break;
                            case 'gallery':
                                $name = esc_html__('Gallery', 'felan-framework');
                                break;
                            case 'video':
                                $name = esc_html__('Video', 'felan-framework');
                                break;
                            case 'additional':
                                $name = esc_html__('Additional', 'felan-framework');
                                break;
                        }
                        if (!in_array($value, $hide_jobs_group_fields)) : ?>
                            <div class="block-from" id="<?php echo 'jobs-submit-' . esc_attr($value); ?>">
                                <h6><?php echo $name ?></h6>
                                <?php felan_get_template('jobs/edit/' . $value . '.php'); ?>
                            </div>
                    <?php endif;
                    } ?>

                    <?php wp_nonce_field('felan_submit_jobs_action', 'felan_submit_jobs_nonce_field'); ?>

                    <input type="hidden" name="jobs_form" value="<?php echo esc_attr($form); ?>" />
                    <input type="hidden" name="jobs_action" value="<?php echo esc_attr($action) ?>" />
                    <input type="hidden" name="jobs_id" value="<?php echo esc_attr($jobs_id); ?>" />
                </div>
                <div class="col-lg-4 col-md-5">
                    <div class="widget-area-init has-sticky">
                        <div class="header-about">
                            <h3 class="title-jobs-about"><?php esc_html_e('About this job', 'felan-framework') ?></h3>
                            <a class="felan-button button-outline-accent" href="<?php echo esc_url(get_permalink($jobs_id)); ?>" target="_blank">
                                <span><?php esc_html_e('View', 'felan-framework') ?></span>
                                <i class="far fa-external-link-alt"></i>
                            </a>
                        </div>
                        <div class="about-jobs-dashboard">
                            <div class="img-company"><i class="far fa-camera"></i></div>
                            <h4 class="title-about" data-title="<?php esc_attr_e('Title of job', 'felan-framework') ?>"><?php esc_html_e('Title of job', 'felan-framework') ?></h4>
                            <div class="info-jobs-warpper">
                                <?php esc_html_e('by', 'felan-framework'); ?>
                                <span class="name-company" data-name="<?php esc_attr_e('Company Name', 'felan-framework') ?>"><?php esc_html_e('Company Name', 'felan-framework'); ?></span>
                                <?php esc_html_e('in', 'felan-framework'); ?>
                                <span class="cate-about" data-cate="<?php esc_attr_e('Category', 'felan-framework') ?>"><?php esc_html_e('Category', 'felan-framework'); ?></span>
                                <div class="label-warpper">
                                    <span class="label-type-inner"></span>
                                    <span class="label-location-inner"></span>
                                </div>
                                <?php
                                if ($jobs_salary_active) {
                                    echo '<div class="label label-price" data-text-min="' . esc_attr__('Minimum:', 'felan-framework') . '" data-text-max="' . esc_attr__('Maximum:', 'felan-framework') . '" data-text-agree="' . esc_attr__('Negotiable Price', 'felan-framework') . '"></div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php do_action('after_post_job_form', $jobs_id); ?>