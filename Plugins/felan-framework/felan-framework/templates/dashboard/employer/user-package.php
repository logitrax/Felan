<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'package_id', $user_id);
$package_unlimited_job = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_job', true);
$package_unlimited_featured_job = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_job_featured', true);
$package_num_job = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_number_job', true);
$package_num_featured_job = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_number_featured', true);
$package_activate = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_activate_date', true);
$package_activate_date = felan_convert_date_format($package_activate);
$package_time_unit = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_time_unit', true);
$package_period = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_period', true);
$package_unlimited_time = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_time', true);
$package_name = get_the_title($package_id);
$user_info = get_userdata($user_id);
$felan_package = new Felan_Package();
$expired_date = $felan_package->get_expired_date($package_id, $user_id);
$paid_submission_type = felan_get_option('paid_submission_type', 'no');

$current_date = date('Y-m-d');
if ($current_date < $expired_date) {
    $seconds = strtotime($expired_date) - strtotime($current_date);
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    $expired_jobs = $dtF->diff($dtT)->format('%a');
} else {
    $expired_jobs = 0;
}
?>
<?php if ($paid_submission_type !== 'per_package') : ?>
    <p class="notice"><i class="far fa-exclamation-circle"></i>
        <?php esc_html_e("You are on free submit active", 'felan-framework'); ?>
        <a href="<?php echo felan_get_permalink('jobs_submit'); ?>">
            <?php esc_html_e('Add Jobs', 'felan-framework'); ?>
        </a>
    </p>
<?php else : ?>
    <?php if ($current_date >= $expired_date) : ?>
        <p class="notice"><i class="far fa-exclamation-circle"></i>
            <?php esc_html_e("Your package has expired please choose another one", 'felan-framework'); ?>
        </p>
    <?php endif; ?>
    <div class="entry-my-page pakages-dashboard">
        <div class="entry-title">
            <h4><?php esc_html_e('My packages', 'felan-framework') ?></h4>
        </div>
        <div class="table-dashboard-wapper">
            <table class="table-dashboard <?php if ($current_date >= $expired_date && $paid_submission_type == 'per_package') {
                                                echo 'expired';
                                            } ?>">
                <thead>
                    <tr>
                        <th><?php esc_html_e('ID', 'felan-framework') ?></th>
                        <th class="col-name"><?php esc_html_e('Package Name', 'felan-framework') ?></th>
                        <th><?php esc_html_e('Number Jobs', 'felan-framework') ?></th>
                        <th><?php esc_html_e('Number Featured', 'felan-framework') ?></th>
                        <th><?php esc_html_e('Job Duration', 'felan-framework') ?></th>
                        <th><?php esc_html_e('Status', 'felan-framework') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span class="package-id">
                                <?php if ($package_id) {
                                    echo "#$package_id";
                                } ?>
                            </span>
                        </td>
                        <td>
                            <h3><a href="<?php echo felan_get_permalink('package') ?>"><?php echo esc_attr($package_name) ?></a></h3>
                            <p><?php echo esc_attr($package_activate_date) ?></p>
                        </td>
                        <td>
                            <span class="limit">
                                <?php if ($package_unlimited_job == 1) {
                                    esc_html_e('Unlimited', 'felan-framework');
                                } else {
                                    echo $package_num_job;
                                } ?>
                            </span>
                        </td>
                        <td>
                            <span class="days">
                                <?php if ($package_unlimited_featured_job == 1) {
                                    esc_html_e('Unlimited', 'felan-framework');
                                } else {
                                    echo $package_num_featured_job;
                                } ?>
                            </span>
                        </td>
                        <td>
                            <span class="remaining">
                                <?php if ($package_unlimited_time == 1) {
                                    esc_html_e('never expires', 'felan-framework');
                                } else {
                                    if($expired_jobs > 1){
                                        echo sprintf(esc_html__('%s days', 'felan-framework'), $expired_jobs);
                                    } else {
                                        echo sprintf(esc_html__('%s day', 'felan-framework'), $expired_jobs);
                                    }
                                } ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            global $current_user;
                            $user_id = $current_user->ID;
                            $args_invoice = array(
                                'post_type'           => 'invoice',
                                'posts_per_page'      => 1,
                                'author'              => $user_id,
                            );
                            $data_invoice = new WP_Query($args_invoice);
                            if (!empty($data_invoice->post)) :
                                $invoice_id = $data_invoice->post->ID;
                                $invoice_status = get_post_meta($invoice_id, FELAN_METABOX_PREFIX . 'invoice_payment_status', true);
                                if ($invoice_status == 0) { ?>
                                    <span class="label label-pending"><?php esc_html_e('Pending', 'felan-framework') ?></span>
                                <?php } else { ?>
                                    <?php if (($current_date < $expired_date) || ($package_unlimited_time == 1)) { ?>
                                        <span class="label label-open"><?php esc_html_e('Actived', 'felan-framework') ?></span>
                                    <?php } else { ?>
                                        <span class="label label-close"><?php esc_html_e('Expired', 'felan-framework') ?></span>
                            <?php
                                    }
                                }
                            endif;
                            ?>
                        </td>
                        <td>
                            <a href="#form-employer-user-package" id="action-employer-user-package">
                                <?php esc_html_e('Overview', 'felan-framework') ?>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <a href="<?php echo felan_get_permalink('package'); ?>" class="felan-button felan-new-package">
            <i class="far fa-plus"></i><?php esc_html_e('Add new package', 'felan-framework') ?>
        </a>
    </div>
<?php endif; ?>