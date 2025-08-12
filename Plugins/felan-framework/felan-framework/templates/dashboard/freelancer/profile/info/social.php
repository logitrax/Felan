<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'social-network');

global $freelancer_data, $freelancer_meta_data;
$freelancer_twitter = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_twitter']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_twitter'][0] : '';
$freelancer_linkedin = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_linkedin']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_linkedin'][0] : '';
$freelancer_facebook = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_facebook']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_facebook'][0] : '';
$freelancer_instagram = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_instagram']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_instagram'][0] : '';
$enable_social_twitter = felan_get_option('enable_social_twitter', '1');
$enable_social_linkedin = felan_get_option('enable_social_linkedin', '1');
$enable_social_facebook = felan_get_option('enable_social_facebook', '1');
$enable_social_instagram = felan_get_option('enable_social_instagram', '1');
?>

<div class="social-network block-from" id="freelancer-submit-social">
    <h6><?php esc_html_e('Social Network', 'felan-framework') ?></h6>
    <div class="row felan-social-network">
        <?php if ($enable_social_twitter == 1) : ?>
            <div class="form-group col-12 col-sm-6">
                <label><?php esc_html_e('Twitter', 'felan-framework') ?></label>
                <input type="url" name="freelancer_twitter" id="freelancer_twitter" class="point-mark" value="<?php echo esc_attr($freelancer_twitter) ?>" placeholder="<?php esc_attr_e('twitter.com/freelancer', 'felan-framework') ?>">
            </div>
        <?php endif; ?>
        <?php if ($enable_social_linkedin == 1) : ?>
            <div class="form-group col-12 col-sm-6">
                <label><?php esc_html_e('Linkedin', 'felan-framework') ?></label>
                <input type="url" name="freelancer_linkedin" id="freelancer_linkedin" class="point-mark" value="<?php echo esc_attr($freelancer_linkedin) ?>" placeholder="<?php esc_attr_e('linkedin.com/freelancer', 'felan-framework') ?>">
            </div>
        <?php endif; ?>
        <?php if ($enable_social_facebook == 1) : ?>
            <div class="form-group col-12 col-sm-6">
                <label><?php esc_html_e('Facebook', 'felan-framework') ?></label>
                <input type="url" name="freelancer_facebook" id="freelancer_facebook" class="point-mark" value="<?php echo esc_attr($freelancer_facebook) ?>" placeholder="<?php esc_attr_e('facebook.com/freelancer', 'felan-framework') ?>">
            </div>
        <?php endif; ?>
        <?php if ($enable_social_instagram == 1) : ?>
            <div class="form-group col-12 col-sm-6">
                <label><?php esc_html_e('Instagram', 'felan-framework') ?></label>
                <input type="url" name="freelancer_instagram" id="freelancer_instagram" class="point-mark" value="<?php echo esc_attr($freelancer_instagram) ?>" placeholder="<?php esc_attr_e('instagram.com/freelancer', 'felan-framework') ?>">
            </div>
        <?php endif; ?>
        <?php $felan_social_fields = felan_get_option('felan_social_fields');
        if (is_array($felan_social_fields) && !empty($felan_social_fields)) {
            foreach ($felan_social_fields as $key => $value) {
                $freelancer_social_val = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_' . $value['social_name']]) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_' . $value['social_name']][0] : '';
                if(!empty($value['social_name'])) {
        ?>
                <div class="form-group col-12 col-sm-6">
                    <label><?php echo esc_html($value['social_name']); ?></label>
                    <input class="freelancer-social-input" type="url" name="freelancer_<?php echo esc_html($value['social_name']); ?>" id="freelancer_<?php echo esc_html($value['social_name']); ?>" value="<?php echo esc_attr($freelancer_social_val) ?>" placeholder="<?php esc_attr_e($value['social_name'] . '.com/freelancer', 'felan-framework') ?>">
                </div>
        <?php }}
        } ?>
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
                    <input type="text" name="freelancer_social_name[]" placeholder="<?php esc_attr_e('Freelancer', 'felan-framework') ?>">
                </div>
                <div class="form-group col-12 col-sm-6">
                    <label><?php esc_html_e('Url', 'felan-framework') ?></label>
                    <input type="url" name="freelancer_social_url[]" placeholder="<?php esc_attr_e('url.com/freelancer', 'felan-framework') ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="add-social-list">
        <?php
        $freelancer_social_tab = get_post_meta($freelancer_data->ID, FELAN_METABOX_PREFIX . 'freelancer_social_tabs', false);
        $i = 0;
        if (is_array($freelancer_social_tab)) {
            foreach ($freelancer_social_tab as $social) {
                if (is_array($social)) {
                    foreach ($social as $k1 => $social_v1) {
        ?>

                        <div class="clone-wrap">
                            <div class="soical-remove-inner">
                                <a href="#" class="remove-social"><i class="far fa-times"></i></a>
                                <span><?php esc_html_e('Network', 'felan-framework') ?><span class="number-network"></span></span>
                            </div>
                            <div class="row field-wrap">
                                <div class="col col-md-6">
                                    <label><?php esc_html_e('Name', 'felan-framework') ?></label>
                                    <input type="text" name="freelancer_social_name[]" value="<?php echo $social_v1[FELAN_METABOX_PREFIX . 'freelancer_social_name']; ?>" placeholder="<?php esc_attr_e('Freelancer', 'felan-framework') ?>">
                                </div>
                                <div class="col col-md-6">
                                    <label><?php esc_html_e('Url', 'felan-framework') ?></label>
                                    <input type="url" name="freelancer_social_url[]" value="<?php echo $social_v1[FELAN_METABOX_PREFIX . 'freelancer_social_url']; ?>" placeholder="<?php esc_attr_e('url.com/freelancer', 'felan-framework') ?>">
                                </div>
                            </div>
                        </div>
        <?php }
                    $i++;
                }
            }
        } ?>
    </div>
    <a class="felan-button button-link add-social" href="#addsocial">
        <span class="felan-button-icon"><i class="far fa-chevron-down"></i></span>
        <?php esc_html_e('Add more', 'felan-framework') ?>
    </a>
</div>