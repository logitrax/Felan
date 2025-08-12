<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$service_id = isset($_GET['service_id']) ? felan_clean(wp_unslash($_GET['service_id'])) : '';
global $current_user, $service_data, $service_meta_data, $current_user, $hide_service_fields;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
$felan_service_page_id = felan_get_option('felan_freelancer_service_page_id');
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'service-submit');
wp_enqueue_script('jquery-validate');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'service-submit',
    'felan_submit_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
        'service_dashboard' => get_page_link($felan_service_page_id),
    )
);
$form = 'edit-service';
$service_data = get_post($service_id);
$service_meta_data = get_post_custom($service_data->ID);

$hide_service_fields = felan_get_option('hide_service_fields', array());
if (!is_array($hide_service_fields)) {
    $hide_service_fields = array();
}
$layout = array('overview', 'pricing', 'faq');
$package_status = felan_freelancer_package_status();

//Package
$freelancer_paid_submission_type = felan_get_option('freelancer_paid_submission_type');
$user_package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'freelancer_package_id', $user_id);
$freelancer_package_number_service = get_post_meta($user_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_service', true);
$enable_package_service_unlimited = get_post_meta($user_package_id, FELAN_METABOX_PREFIX . 'enable_package_service_unlimited', true);
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
    } elseif (in_array('felan_user_employer', (array)$current_user->roles) && $freelancer_package_number_service < 1 && $enable_package_service_unlimited != '1') {
        $notice_text = esc_html__("The package you selected has reached its allowable limit. Please come back later!", 'felan-framework');
    }
}

$has_freelancer_package = true;
if ($freelancer_paid_submission_type == 'freelancer_per_package') {
    $felan_freelancer_package = new Felan_freelancer_package();
    $check_freelancer_package = $felan_freelancer_package->user_freelancer_package_available($user_id);
    if (($check_freelancer_package == -1) || ($check_freelancer_package == 0)) {
        $has_freelancer_package = false;
    }
}
?>

<div class="entry-my-page submit-service-dashboard">
    <form action="#" method="post" id="submit_service_form" class="form-dashboard" enctype="multipart/form-data" data-titleerror="<?php echo esc_html__('Please enter service name', 'felan-framework'); ?>" data-deserror="<?php echo esc_html__('Please enter service description', 'felan-framework'); ?>" data-caterror="<?php echo esc_html__('Please choose category', 'felan-framework'); ?>">
        <div class="content-service tab-dashboard">
            <div class="submit-service-header felan-submit-header">
                <div class="entry-title">
                    <h4><?php esc_html_e('Update Service', 'felan-framework') ?></h4>
                </div>
                <div class="button-warpper">
                    <a href="<?php echo felan_get_permalink('freelancer_service'); ?>" class="felan-button button-link">
                        <?php esc_html_e('Cancel', 'felan-framework') ?>
                    </a>
                    <?php if ($user_demo == 'yes') : ?>
                        <button class="felan-button btn-add-to-message" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>">
                            <span><?php esc_html_e('Update', 'felan-framework'); ?></span>
                        </button>
                    <?php else : ?>
                        <?php if (
								($has_freelancer_package && $freelancer_package_number_service > 0 && $package_status == 1) ||
								($has_freelancer_package && $enable_package_service_unlimited == '1' && $package_status == 1) ||
								$freelancer_paid_submission_type !== 'freelancer_per_package'
							) { ?>
                            <button type="submit" class="btn-submit-service felan-button" name="submit_service">
                                <span><?php esc_html_e('Update', 'felan-framework'); ?></span>
                                <span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
                            </button>
                        <?php } else { ?>
                            <a href="<?php echo felan_get_permalink('freelancer_package'); ?>" class="felan-button package-out-stock"><?php esc_html_e('Upgrade now', 'felan-framework'); ?></a>
                        <?php } ?>
                    <?php endif; ?>
                </div>
            </div>

            <ul class="tab-list service-submit-tab">
                <li class="tab-item">
                    <a href="#tab-overview"><?php esc_html_e('Overview', 'felan-framework') ?></a>
                </li>
                <li class="tab-item">
                    <a href="#tab-pricing"><?php esc_html_e('Pricing', 'felan-framework') ?></a>
                </li>
                <?php if (!in_array('fields_service_faq', $hide_service_fields)) : ?>
                    <li class="tab-item">
                        <a href="#tab-faq"><?php esc_html_e('FAQ', 'felan-framework') ?></a>
                    </li>
                <?php endif; ?>
            </ul>

            <?php foreach ($layout as $value) { ?>
                <div id="tab-<?php echo $value; ?>" class="tab-info">
                    <?php felan_get_template('service/edit/' . $value . '.php'); ?>
                </div>
            <?php } ?>

            <?php wp_nonce_field('felan_submit_service_action', 'felan_submit_service_nonce_field'); ?>

            <input type="hidden" name="service_form" value="<?php echo esc_attr($form); ?>" />
            <input type="hidden" name="service_id" value="<?php echo esc_attr($service_id); ?>" />
        </div>
    </form>
</div>
<?php do_action('after_post_job_form', $service_id); ?>
