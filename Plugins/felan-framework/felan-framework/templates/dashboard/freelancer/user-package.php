<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$enable_post_type_jobs = felan_get_option('enable_post_type_jobs','1');
$enable_post_type_service = felan_get_option('enable_post_type_service','1');
$freelancer_package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'freelancer_package_id', $user_id);
$freelancer_package_activate = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_package_activate_date', true);
$freelancer_package_activate_date = felan_convert_date_format($freelancer_package_activate);
$freelancer_package_time_unit = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_time_unit', true);
$freelancer_package_period = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_period', true);
$enable_package_service_unlimited_time = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'enable_package_service_unlimited_time', true);
$freelancer_package_name = get_the_title($freelancer_package_id);
$user_info = get_userdata($user_id);
$felan_freelancer_package = new Felan_freelancer_package();
$expired_date = $felan_freelancer_package->get_expired_date($freelancer_package_id, $user_id);
$check_freelancer_package = $felan_freelancer_package->user_freelancer_package_available($user_id);
$freelancer_paid_submission_type = felan_get_option('freelancer_paid_submission_type');
$expired_date_format = date(get_option('date_format'), strtotime($expired_date));
$freelancer_package_activate_date = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_package_activate_date', true);
$activate_date_format = date(get_option('date_format'), strtotime($freelancer_package_activate_date));

$current_date = date('Y-m-d');
if ($current_date < $expired_date) {
    $seconds = strtotime($expired_date) - strtotime($current_date);
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    $expired_days = $dtF->diff($dtT)->format('%a');
} else {
    $expired_days = 0;
}
?>
<?php if ($freelancer_paid_submission_type !== 'freelancer_per_package') : ?>
    <p class="notice"><i class="far fa-exclamation-circle"></i>
        <?php esc_html_e("You are on free submit active", 'felan-framework'); ?>
        <a href="<?php echo felan_get_permalink('submit_service'); ?>">
            <?php esc_html_e('Add Service', 'felan-framework'); ?>
        </a>
    </p>
<?php else : ?>
    <?php if ($current_date >= $expired_date) : ?>
        <p class="notice"><i class="far fa-exclamation-circle"></i>
            <?php esc_html_e("Package expired. Please select a new one.", 'felan-framework'); ?>
        </p>
    <?php endif; ?>
    <div class="entry-my-page pakages-dashboard my-freelancer-package">
        <div class="entry-title">
            <h4><?php esc_html_e('My Package', 'felan-framework') ?></h4>
            <?php if($enable_post_type_service == '1') { ?>
                <a href="<?php echo felan_get_permalink('submit_service'); ?>" class="felan-button button-outline-accent">
                    <i class="far fa-plus"></i><?php esc_html_e('Create new service', 'felan-framework') ?>
                </a>
            <?php } ?>

        </div>
        <div class="table-dashboard-wapper">
            <table class="table-dashboard <?php if ($check_freelancer_package == -1 || $check_freelancer_package == 0) {
                                                echo 'expired';
                                            } ?>">
                <thead>
                    <tr>
                        <th><?php esc_html_e('ID', 'felan-framework') ?></th>
                        <th><?php esc_html_e('Package Name', 'felan-framework') ?></th>
                        <th><?php esc_html_e('Status', 'felan-framework') ?></th>
                        <th><?php esc_html_e('Activation Date', 'felan-framework') ?></th>
                        <th><?php esc_html_e('Expiration Date', 'felan-framework') ?></th>
                        <th><?php esc_html_e('Date Remaining', 'felan-framework') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span class="package-id">
                                <?php if ($freelancer_package_id) {
                                    echo "#$freelancer_package_id";
                                } ?>
                            </span>
                        </td>
                        <td>
                            <h3>
                                <a href="<?php echo felan_get_permalink('package_freelancer') ?>"><?php echo esc_attr($freelancer_package_name) ?></a>
                            </h3>
                            <p><?php echo esc_attr($freelancer_package_activate_date) ?></p>
                        </td>
                        <td>
                            <?php $package_status = felan_freelancer_package_status();
                            if ($package_status === '0') { ?>
                                <span class="label label-pending"><?php esc_html_e('Pending', 'felan-framework') ?></span>
                            <?php } elseif ($package_status === '-1') { ?>
                                <span class="label label-close"><?php esc_html_e('Canceled', 'felan-framework') ?></span>
                            <?php } else { ?>
                                <?php if (($current_date < $expired_date) || ($enable_package_service_unlimited_time == 1)) { ?>
                                    <span class="label label-open"><?php esc_html_e('Actived', 'felan-framework') ?></span>
                                <?php } else { ?>
                                    <span class="label label-close"><?php esc_html_e('Expired', 'felan-framework') ?></span>
                                <?php } ?>
                            <?php } ?>
                        </td>
                        <td>
                            <span class="active-date">
                                <?php if ($enable_package_service_unlimited_time == 1) {
                                    esc_html_e('Unlimited', 'felan-framework');
                                } else {
                                    echo $activate_date_format;
                                } ?>
                            </span>
                        </td>
                        <td>
                            <span class="expired-date">
                                <?php if ($enable_package_service_unlimited_time == 1) {
                                    esc_html_e('Unlimited', 'felan-framework');
                                } else {
                                    echo $expired_date_format;
                                } ?>
                            </span>
                        </td>
                        <td>
                            <span class="remaining">
                                <?php if ($enable_package_service_unlimited_time == 1) {
                                    esc_html_e('Never Expires', 'felan-framework');
                                } else {
                                    if($expired_days > 1){
                                        echo sprintf(esc_html__('%s Days', 'felan-framework'), $expired_days);
                                    } else {
                                        echo sprintf(esc_html__('%s Day', 'felan-framework'), $expired_days);
                                    }
                                } ?>
                            </span>
                        </td>
                        <td>
                            <a href="#form-freelancer-user-package" id="action-user-package">
                                <?php esc_html_e('Overview', 'felan-framework') ?>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <a href="<?php echo felan_get_permalink('freelancer_package'); ?>" class="felan-button felan-new-package">
            <i class="far fa-plus"></i><?php esc_html_e('Add new package', 'felan-framework') ?>
        </a>
    </div>
<?php endif; ?>