<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!is_user_logged_in()) {
    echo felan_get_template_html('global/access-denied.php', array('type' => 'not_login'));
    return;
}
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'payment');
$allow_submit = felan_allow_submit();
if (!$allow_submit) {
    echo felan_get_template_html('global/access-denied.php', array('type' => 'not_permission'));
    return;
}
$package_id = isset($_GET['package_id']) ? absint(wp_unslash($_GET['package_id']))  : '';
$jobs_id   = isset($_GET['jobs_id']) ? absint(wp_unslash($_GET['jobs_id']))  : '';
$is_upgrade = isset($_GET['is_upgrade']) ? absint(wp_unslash($_GET['is_upgrade']))  : '';
if ($is_upgrade == 1) {
    $prop_featured = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'place_featured', true);
    if ($prop_featured == 1) {
        echo ("<script>location.href = '" . home_url() . "'</script>");
    }
}
if (empty($package_id) && empty($jobs_id)) {
    echo ("<script>location.href = '" . home_url() . "'</script>");
}
$Felan_jobs = new Felan_Jobs();

if (!empty($jobs_id) && !$Felan_jobs->user_can_edit_place($jobs_id)) {
    echo ("<script>location.href = '" . home_url() . "'</script>");
}
//set_time_limit(700);
$paid_submission_type = felan_get_option('paid_submission_type', 'no');
?>
<div class="payment-wrap">
    <?php
    do_action('felan_payment_before');
    if ($paid_submission_type == 'per_package') {
        felan_get_template('jobs/payment/per-package.php');
    } else { ?>
        <p class="notice"><i class="far fa-exclamation-circle"></i>
            <?php esc_html_e("You are on free submit active", 'felan-framework'); ?>
            <a href="<?php echo felan_get_permalink('jobs_submit'); ?>">
                <?php esc_html_e('Add Jobs', 'felan-framework'); ?>
            </a>
        </p>
    <?php }
    wp_nonce_field('felan_payment_ajax_nonce', 'felan_security_payment');
    do_action('felan_payment_after');
    ?>
</div>