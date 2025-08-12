<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
wp_get_current_user();
$user_id = $current_user->ID;
$user_login = $current_user->user_login;
$user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
$ajax_url = admin_url('admin-ajax.php');
$upload_nonce = wp_create_nonce('felan_thumbnail_allow_upload');

wp_enqueue_script('plupload');
wp_enqueue_script('jquery-validate');
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'settings');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'settings',
    'felan_settings_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
        'site_url' => get_site_url(),
    )
);
?>
<div class="form-dashboard">
    <form action="#" class="block-from form-password form-change-password">
        <h6><?php esc_html_e('Change password', 'felan-framework') ?></h6>
        <div class="row">
            <div class="form-group col-md-12">
                <label for="oldpass"><?php esc_html_e('Current password', 'felan-framework') ?></label>
                <input class="form-control" type="password" id="oldpass" name="oldpass" value="" placeholder="<?php esc_attr_e('Enter current password', 'felan-framework'); ?>">
                <span toggle="#oldpass" class="fa fa-fw fa-eye field-icon felan-toggle-password"></span>
            </div>
            <div class="form-group col-md-12">
                <label for="newpass"><?php esc_html_e('New password', 'felan-framework') ?></label>
                <input class="form-control" type="password" id="newpass" name="nnewpass" value="" placeholder="<?php esc_attr_e('Enter new password', 'felan-framework'); ?>">
                <span toggle="#newpass" class="fa fa-fw fa-eye field-icon felan-toggle-password"></span>
            </div>
            <div class="form-group col-md-12">
                <label for="confirmpass"><?php esc_html_e('Confirm new password', 'felan-framework') ?></label>
                <input class="form-control" type="password" id="confirmpass" name="confirmpass" value="" placeholder="<?php esc_attr_e('Enter confirm password', 'felan-framework'); ?>">
                <span toggle="#confirmpass" class="fa fa-fw fa-eye field-icon felan-toggle-password"></span>
            </div>
        </div>
        <?php wp_nonce_field('felan_change_password_ajax_nonce', 'felan_security_change_password'); ?>
        <div class="message"></div>
        <?php if ($user_demo == 'yes') : ?>
            <button class="felan-button btn-add-to-message" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>">
                <?php esc_html_e('Save changes', 'felan-framework'); ?>
            </button>
        <?php else : ?>
            <button class="felan-button button-password" id="felan_change_pass">
                <span><?php esc_html_e('Save changes', 'felan-framework'); ?></span>
                <span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
            </button>
        <?php endif; ?>
    </form>
    <?php if ($user_demo == 'yes') : ?>
        <a class="btn-add-to-message delete-account" data-text="<?php echo esc_attr('This is a "Demo" account so you not cant deactive it', 'felan-framework'); ?>" href="#"><?php esc_html_e('Deactive account', 'felan-framework') ?></a></li>
    <?php else : ?>
        <a href="#" class="delete-account" id="btn-setting-deactive"><?php esc_html_e('Deactive account', 'felan-framework') ?></a>
    <?php endif; ?>
</div>