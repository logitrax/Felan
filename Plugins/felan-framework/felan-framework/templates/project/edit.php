<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$project_id = isset($_GET['project_id']) ? felan_clean(wp_unslash($_GET['project_id'])) : '';
global $current_user, $project_data, $project_meta_data, $current_user, $hide_project_fields;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
$felan_project_page_id = felan_get_option('felan_freelancer_project_page_id');
$custom_field_project = felan_render_custom_field('project');
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'project-submit');
wp_enqueue_script('jquery-validate');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'project-submit',
    'felan_submit_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
        'project_dashboard' => get_page_link($felan_project_page_id),
        'custom_field_project' => $custom_field_project,
    )
);
$form = 'edit-project';
$project_data = get_post($project_id);
$project_meta_data = get_post_custom($project_data->ID);

$hide_project_fields = felan_get_option('hide_project_fields', array());
if (!is_array($hide_project_fields)) {
    $hide_project_fields = array();
}
$layout = array('overview', 'budget', 'company', 'faq', 'additional');
$package_status = felan_employer_package_status();

//Package
$freelancer_paid_submission_type = felan_get_option('freelancer_paid_submission_type');
$user_package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'freelancer_package_id', $user_id);
$package_number_project = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_number_project', true);
$enable_package_project_unlimited = get_post_meta($user_package_id, FELAN_METABOX_PREFIX . 'enable_package_project_unlimited', true);
$notice_text = $shortcode = '';
$felan_freelancer_package = new Felan_freelancer_package();
$get_expired_date = $felan_freelancer_package->get_expired_date($user_package_id, $user_id);
$current_date = date('Y-m-d');
$d1 = strtotime($get_expired_date);
$d2 = strtotime($current_date);
if ($get_expired_date === 'Never Expires' || $get_expired_date === 'Unlimited') {
    $d1 = 999999999999999999999999;
}

if ($freelancer_paid_submission_type == 'no') {
    if (in_array('felan_user_freelancer', (array)$current_user->roles)) {
        $notice_text = esc_html__("Sorry, you can't view this page as Freelancer, register Employer account to get access.", 'felan-framework');
    }

    $package_status = 1;
} else {
    if (in_array('felan_user_freelancer', (array)$current_user->roles)) {
        $notice_text = esc_html__("Sorry, you can't view this page as Freelancer, register Employer account to get access.", 'felan-framework');
    } elseif ((in_array('felan_user_employer', (array)$current_user->roles) && $user_package_id == '') || $d1 < $d2) {
        $notice_text = esc_html__("You have not purchased the package. Please choose 1 of the packages now.", 'felan-framework');
        $shortcode = '1';
    } elseif (in_array('felan_user_employer', (array)$current_user->roles) && $package_number_project < 1 && $enable_package_project_unlimited != '1') {
        $notice_text = esc_html__("The package you selected has reached its allowable limit. Please come back later!", 'felan-framework');
    }
}

$has_package = true;
$paid_submission_type = felan_get_option('paid_submission_type', 'no');
if ($paid_submission_type == 'per_package') {
    $current_package_key = get_the_author_meta(FELAN_METABOX_PREFIX . 'package_key', $user_id);
    $jobs_package_key = get_post_meta($user_id, FELAN_METABOX_PREFIX . 'package_key', true);
    $felan_profile = new Felan_Profile();
    $check_package = $felan_profile->user_package_available($user_id);
    if (($check_package == -1) || ($check_package == 0)) {
        $has_package = false;
    }
}
?>

<div class="entry-my-page submit-project-dashboard edit-project">
    <form action="#" method="post" id="submit_project_form" class="form-dashboard" enctype="multipart/form-data"
          data-titleerror="<?php echo esc_html__('Please enter project name', 'felan-framework'); ?>"
          data-deserror="<?php echo esc_html__('Please enter project description', 'felan-framework'); ?>"
          data-careererror="<?php echo esc_html__('Please enter career description', 'felan-framework'); ?>"
          data-languageerror="<?php echo esc_html__('Please enter language description', 'felan-framework'); ?>"
          data-caterror="<?php echo esc_html__('Please choose category', 'felan-framework'); ?>"
           data-companyerror="<?php echo esc_html__('Please choose company', 'felan-framework'); ?>">
        <div class="row">
            <div class="col-lg-8 col-md-7">
                <div class="content-project tab-dashboard">
                    <div class="submit-project-header felan-submit-header">
                        <div class="entry-title">
                            <h4><?php esc_html_e('Update Project', 'felan-framework') ?></h4>
                        </div>
                        <div class="button-warpper">
                            <a href="<?php echo felan_get_permalink('projects'); ?>" class="felan-button button-link">
                                <?php esc_html_e('Cancel', 'felan-framework') ?>
                            </a>
                            <?php if ($user_demo == 'yes') : ?>
                                <button class="felan-button btn-add-to-message" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>">
                                    <span><?php esc_html_e('Update', 'felan-framework'); ?></span>
                                </button>
                            <?php else : ?>
                                <?php if (($has_package && $package_number_project > 0 && $package_status == 1) || $paid_submission_type !== 'per_package') { ?>
                                    <button type="submit" class="btn-submit-project felan-button" name="submit_project">
                                        <span><?php esc_html_e('Update', 'felan-framework'); ?></span>
                                        <span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
                                    </button>
                                <?php } else { ?>
                                    <a href="<?php echo felan_get_permalink('freelancer_package'); ?>" class="felan-button package-out-stock"><?php esc_html_e('Upgrade now', 'felan-framework'); ?></a>
                                <?php } ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <ul class="tab-list project-submit-tab">
                        <li class="tab-item">
                            <a href="#tab-overview"><?php esc_html_e('Overview', 'felan-framework') ?></a>
                        </li>
                        <?php if (!in_array('fields_project_budget', $hide_project_fields)) : ?>
                            <li class="tab-item">
                                <a href="#tab-budget"><?php esc_html_e('Budget', 'felan-framework') ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if (!in_array('fields_project_company', $hide_project_fields)) : ?>
                            <li class="tab-item">
                                <a href="#tab-company"><?php esc_html_e('Company', 'felan-framework') ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if (!in_array('fields_project_faq', $hide_project_fields)) : ?>
                            <li class="tab-item">
                                <a href="#tab-faq"><?php esc_html_e('FAQ', 'felan-framework') ?></a>
                            </li>
                        <?php endif; ?>

                        <?php if(!empty($custom_field_project)) { ?>
                            <li class="tab-item">
                                <a href="#tab-additional"><?php esc_html_e('Additional', 'felan-framework') ?></a>
                            </li>
                        <?php } ?>
                    </ul>

                    <?php foreach ($layout as $value) { ?>
                        <div id="tab-<?php echo $value; ?>" class="tab-info">
                            <?php felan_get_template('project/edit/' . $value . '.php'); ?>
                        </div>
                    <?php } ?>

                    <?php wp_nonce_field('felan_submit_project_action', 'felan_submit_project_nonce_field'); ?>

                    <input type="hidden" name="project_form" value="<?php echo esc_attr($form); ?>" />
                    <input type="hidden" name="project_id" value="<?php echo esc_attr($project_id); ?>" />
                </div>
            </div>
        </div>
    </form>
</div>
<?php do_action('after_post_job_form', $project_id); ?>
