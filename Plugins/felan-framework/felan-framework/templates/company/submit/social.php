<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'social-network');

$enable_social_twitter = felan_get_option('enable_social_twitter', '1');
$enable_social_linkedin = felan_get_option('enable_social_linkedin', '1');
$enable_social_facebook = felan_get_option('enable_social_facebook', '1');
$enable_social_instagram = felan_get_option('enable_social_instagram', '1');
?>

<div class="row">
    <?php if ($enable_social_twitter == 1) : ?>
        <div class="form-group col-12 col-sm-6">
            <label><?php esc_html_e('Twitter', 'felan-framework') ?></label>
            <input type="url" name="company_twitter" id="company_twitter" placeholder="<?php esc_attr_e('twitter.com/company', 'felan-framework') ?>">
        </div>
    <?php endif; ?>
    <?php if ($enable_social_linkedin == 1) : ?>
        <div class="form-group col-12 col-sm-6">
            <label><?php esc_html_e('Linkedin', 'felan-framework') ?></label>
            <input type="url" name="company_linkedin" id="company_linkedin" placeholder="<?php esc_attr_e('linkedin.com/company', 'felan-framework') ?>">
        </div>
    <?php endif; ?>
    <?php if ($enable_social_facebook == 1) : ?>
        <div class="form-group col-12 col-sm-6">
            <label><?php esc_html_e('Facebook', 'felan-framework') ?></label>
            <input type="url" name="company_facebook" id="company_facebook" placeholder="<?php esc_attr_e('facebook.com/company', 'felan-framework') ?>">
        </div>
    <?php endif; ?>
    <?php if ($enable_social_instagram == 1) : ?>
        <div class="form-group col-12 col-sm-6">
            <label><?php esc_html_e('Instagram', 'felan-framework') ?></label>
            <input type="url" name="company_instagram" id="company_instagram" placeholder="<?php esc_attr_e('instagram.com/company', 'felan-framework') ?>">
        </div>
    <?php endif; ?>
</div>

<div class="field-social-clone">
    <div class="clone-wrap">
        <div class="soical-remove-inner">
            <a href="#" class="remove-social"><i class="far fa-times"></i></a>
            <span><?php esc_html_e('Network', 'felan-framework') ?><span class="number-network"></span></span>
        </div>
        <div class="row field-wrap">
            <div class="form-group col-12 col-sm-6">
                <label><?php esc_html_e('Name', 'felan-framework') ?></label>
                <input type="text" name="company_social_name[]" placeholder="<?php esc_attr_e('Company', 'felan-framework') ?>">
            </div>
            <div class="form-group col-12 col-sm-6">
                <label><?php esc_html_e('Url', 'felan-framework') ?></label>
                <input type="url" name="company_social_url[]" placeholder="<?php esc_attr_e('url.com/company', 'felan-framework') ?>">
            </div>
        </div>
    </div>
</div>

<div class="add-social-list"></div>
<a class="felan-button button-link add-social" href="#addsocial">
    <span class="felan-button-icon"><i class="far fa-chevron-down"></i></span>
    <?php esc_html_e('Add more', 'felan-framework') ?>
</a>