<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!is_user_logged_in()) {
    echo felan_get_template_html('global/access-denied.php', array('type' => 'not_login'));
    return;
}
$allow_submit = felan_allow_submit();
if (!$allow_submit) {
    echo felan_get_template_html('global/access-denied.php', array('type' => 'not_permission'));
    return;
}
$enable_post_type_jobs = felan_get_option('enable_post_type_jobs','1');
if($enable_post_type_jobs == '1'){
    $freelancer_package_id = isset($_GET['candidate_package_id']) ? absint(wp_unslash($_GET['candidate_package_id']))  : '';
} else {
    $freelancer_package_id = isset($_GET['freelancer_package_id']) ? absint(wp_unslash($_GET['freelancer_package_id']))  : '';
}
$freelancer_id   = isset($_GET['freelancer_id']) ? absint(wp_unslash($_GET['freelancer_id']))  : '';
if (empty($freelancer_package_id) && empty($freelancer_id)) {
    echo ("<script>location.href = '" . home_url() . "'</script>");
}
set_time_limit(700);
$freelancer_paid_submission_type = felan_get_option('freelancer_paid_submission_type');
?>
<div class="payment-wrap">
    <?php
    do_action('felan_freelancer_payment_before');
    if ($freelancer_paid_submission_type == 'freelancer_per_package') {
        felan_get_template('freelancer/payment/per-package.php');
    } else { ?>
        <p class="notice"><i class="far fa-exclamation-circle"></i>
            <?php esc_html_e("You are on free submit active", 'felan-framework'); ?>
            <a href="<?php echo felan_get_permalink('submit_service'); ?>">
                <?php esc_html_e('Add Service', 'felan-framework'); ?>
            </a>
        </p>
    <?php }
    wp_nonce_field('felan_freelancer_payment_ajax_nonce', 'felan_freelancer_security_payment');
    do_action('felan_freelancer_payment_after');
    ?>
</div>