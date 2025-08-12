<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
wp_get_current_user();
$default_image = FELAN_THEME_URI . '/assets/images/default-user-image.png';
$user_id = $current_user->ID;
$user_login = $current_user->user_login;
$user_firstname = get_the_author_meta('first_name', $user_id);
$user_lastname = get_the_author_meta('last_name', $user_id);
$user_email = get_the_author_meta('user_email', $user_id);
$author_mobile_number = get_the_author_meta(FELAN_METABOX_PREFIX . 'author_mobile_number', $user_id);
$author_avatar_image_url = get_the_author_meta('author_avatar_image_url', $user_id);
$author_avatar_image_id = get_the_author_meta('author_avatar_image_id', $user_id);
$user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
$phone_code = get_the_author_meta(FELAN_METABOX_PREFIX . 'phone_code', $user_id);
if(empty($phone_code)){
    $phone_code = felan_get_option('default_phone_number');
}

if (!$author_avatar_image_url) {
    $author_avatar_image_url = $default_image;
}
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'settings');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'settings',
    'felan_settings_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
        'site_url' => get_site_url(),
    )
);
felan_get_avatar_enqueue();
?>

<div class="form-dashboard">
    <form class="block-from form-settings">
        <h6><?php esc_html_e('Personal info', 'felan-framework') ?></h6>
        <div class="felan-user-avatar">
            <div class="avatar felan-fields-avatar">
                <label><?php esc_html_e('Your photo', 'felan-framework'); ?></label>
                <div class="form-field">
                    <div id="felan_avatar_errors" class="errors-log"></div>
                    <div id="felan_avatar_container" class="file-upload-block preview">
                        <div id="felan_avatar_view" data-image-id="<?php echo $author_avatar_image_id; ?>" data-image-url="<?php if (!empty($author_avatar_image_url)) {
                                                                                                                                echo $author_avatar_image_url;
                                                                                                                            } ?>"></div>
                        <div id="felan_add_avatar">
                            <i class="far fa-arrow-from-bottom large"></i>
                            <p id="felan_drop_avatar">
                                <button type="button" id="felan_select_avatar"><?php esc_html_e('Upload', 'felan-framework') ?></button>
                            </p>
                        </div>
                        <input type="hidden" class="avatar_url author_avatar_image_url form-control" name="author_avatar_image_url" value="<?php echo esc_attr($author_avatar_image_url); ?>" id="author_avatar_image_url">
                        <input type="hidden" class="avatar_id author_avatar_image_id" name="author_avatar_image_id" value="<?php echo esc_attr($author_avatar_image_id); ?>" id="author_avatar_image_id" />
                    </div>
                </div>
            </div>
            <p class="des-avatar"><?php esc_html_e('Update your photo manually, if the photo is not set the default Avatar will be the same as your login email account.', 'felan-framework') ?></p>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="user_firstname"><?php esc_html_e('First name', 'felan-framework') ?></label>
                <input type="text" id="user_firstname" name="user_firstname" value="<?php echo esc_attr($user_firstname); ?>">
            </div>
            <div class="form-group col-md-6">
                <label for="user_lastname"><?php esc_html_e('Last name', 'felan-framework') ?></label>
                <input type="text" id="user_lastname" name="user_lastname" value="<?php echo esc_attr($user_lastname); ?>">
            </div>
            <div class="form-group col-md-6">
                <label for="user_email"><?php esc_html_e('Email address', 'felan-framework') ?></label>
                <input type="email" id="user_email" name="user_email" value="<?php echo esc_attr($user_email); ?>">
            </div>
            <div class="form-group col-md-6">
                <label for="author_mobile_number"><?php esc_html_e('Phone number', 'felan-framework') ?></label>
                <div class="tel-group">
                    <select name="prefix_code" class="felan-select2 prefix-code">
                        <?php
                        $prefix_code = phone_prefix_code();
                        foreach ($prefix_code as $key => $value) {
                            echo '<option value="' . $key . '" data-dial-code="' . $value['code'] . '" ' . ($phone_code == $key ? 'selected' : '') . '>' . $value['name'] . ' (' . $value['code'] . ')</option>';
                        }
                        ?>
                    </select>
                    <input type="tel" id="author_mobile_number" name="author_mobile_number" value="<?php echo esc_attr($author_mobile_number); ?>" placeholder="<?php esc_attr_e('Phone number', 'felan-framework'); ?>">
                </div>
            </div>
        </div>
        <?php wp_nonce_field('felan_update_profile_ajax_nonce', 'felan_security_update_profile'); ?>
        <button type="submit" class="felan-button" id="felan_update_profile">
            <span><?php esc_html_e('Save changes', 'felan-framework'); ?></span>
            <span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
        </button>
    </form>
    <form class="block-from form-password form-change-password">
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
            <button type="submit" class="felan-button button-password" id="felan_change_pass">
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