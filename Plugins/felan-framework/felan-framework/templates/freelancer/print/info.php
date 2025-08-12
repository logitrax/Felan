<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$freelancer_details_prints = felan_get_option('freelancer_details_prints');
foreach ($freelancer_details_prints as $print) {
    if (!in_array('enable_print_sp_info', $freelancer_details_prints)) {
        return;
    }
}

$freelancer_salary          = !empty(get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_offer_salary')) ? get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_offer_salary')[0] : '';
$freelancer_yoe             = get_the_terms($freelancer_id, 'freelancer_yoe');
$freelancer_languages       = get_the_terms($freelancer_id, 'freelancer_languages');
$freelancer_location        = get_the_terms($freelancer_id, 'felan_location');
$freelancer_gender          = get_the_terms($freelancer_id, 'freelancer_gender');
$freelancer_qualification   = get_the_terms($freelancer_id, 'freelancer_qualification');
$freelancer_ages            = get_the_terms($freelancer_id, 'freelancer_ages');
$freelancer_phone           = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_phone', true);
$freelancer_email           = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_email', true);
$freelancer_twitter         = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_twitter', true);
$freelancer_facebook        = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_facebook', true);
$freelancer_instagram       = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_instagram', true);
$freelancer_linkedin        = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_linkedin', true);

$enable_social_twitter     = felan_get_option('enable_social_twitter', '1');
$enable_social_linkedin    = felan_get_option('enable_social_linkedin', '1');
$enable_social_facebook    = felan_get_option('enable_social_facebook', '1');
$enable_social_instagram   = felan_get_option('enable_social_instagram', '1');

$option_list_gender = array(
    'both' => esc_html__('Both', 'felan-framework'),
    'female' => esc_html__('Female', 'felan-framework'),
    'male' => esc_html__('Male', 'felan-framework'),
);

$classes = array();
$enable_sticky_sidebar_type = felan_get_option('enable_sticky_freelancer_sidebar_type', 1);
if ($enable_sticky_sidebar_type) {
    $classes[] = 'has-sticky';
};
?>
<div class="freelancer-sidebar block-archive-inner freelancer-single-field <?php echo implode(" ", $classes); ?>">
    <h3 class="title-freelancer"><?php esc_html_e('Information', 'felan-framework'); ?></h3>
    <div class="row">
        <?php if (!empty($freelancer_salary)) : ?>
            <div class="info col-4">
                <p class="title-info"><?php esc_html_e('Offered Salary', 'felan-framework'); ?></p>
                <div class="details-info salary">
                    <?php felan_get_salary_freelancer($freelancer_id); ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if (is_array($freelancer_yoe)) : ?>
            <div class="info col-4">
                <p class="title-info"><?php esc_html_e('Experience time', 'felan-framework'); ?></p>
                <div class="list-cate">
                    <?php foreach ($freelancer_yoe as $yoe) {
                        $yoe_link = get_term_link($yoe, 'freelancer_yoe'); ?>
                        <a href="<?php echo esc_url($yoe_link); ?>">
                            <?php esc_attr_e($yoe->name); ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if (is_array($freelancer_languages)) : ?>
            <div class="info col-4">
                <p class="title-info"><?php esc_html_e('Languages', 'felan-framework'); ?></p>
                <div class="list-cate">
                    <?php foreach ($freelancer_languages as $language) {
                        echo '<span>' . esc_attr($language->name) . '</span>';
                    } ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!empty($freelancer_gender)) : ?>
            <div class="info col-4">
                <p class="title-info"><?php esc_html_e('Gender', 'felan-framework'); ?></p>
                <div class="list-cate">
                    <?php foreach ($freelancer_gender as $gender) {
                        echo esc_attr_e($gender->name);
                    } ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if (is_array($freelancer_qualification)) : ?>
            <div class="info col-4">
                <p class="title-info"><?php esc_html_e('Qualification', 'felan-framework'); ?></p>
                <div class="list-cate">
                    <?php foreach ($freelancer_qualification as $qualification) {
                        echo '<span>' . esc_attr($qualification->name) . '</span>';
                    } ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if (is_array($freelancer_ages)) : ?>
            <div class="info col-4">
                <p class="title-info"><?php esc_html_e('Age', 'felan-framework'); ?></p>
                <div class="list-cate">
                    <?php foreach ($freelancer_ages as $ages) {
                        echo esc_attr_e($ages->name);
                    } ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($freelancer_phone) : ?>
            <div class="info col-4">
                <p class="title-info"><?php esc_html_e('Phone', 'felan-framework'); ?></p>
                <p class="details-info"><a href="tel:<?php esc_attr_e($freelancer_phone); ?>"><?php esc_attr_e($freelancer_phone); ?></a></p>
            </div>
        <?php endif; ?>
        <?php if ($freelancer_email) : ?>
            <div class="info col-4">
                <p class="title-info"><?php esc_html_e('Email', 'felan-framework'); ?></p>
                <p class="details-info email"><a href="mailto:<?php esc_attr_e($freelancer_email) ?>"><?php esc_attr_e($freelancer_email); ?></a></p>
            </div>
        <?php endif; ?>
        <div class="col-4">
            <ul class="list-social">
                <?php if (!empty($freelancer_facebook) && $enable_social_facebook == 1) : ?>
                    <li><a href="<?php echo $freelancer_facebook; ?>"><i class="fab fa-facebook-f"></i></a></li>
                <?php endif; ?>
                <?php if (!empty($freelancer_twitter) && $enable_social_twitter == 1) : ?>
                    <li><a href="<?php echo $freelancer_twitter; ?>"><i class="fab fa-twitter"></i></a></li>
                <?php endif; ?>
                <?php if (!empty($freelancer_linkedin) && $enable_social_linkedin == 1) : ?>
                    <li><a href="<?php echo $freelancer_linkedin; ?>"><i class="fab fa-linkedin"></i></a></li>
                <?php endif; ?>
                <?php if (!empty($freelancer_instagram) && $enable_social_instagram == 1) : ?>
                    <li><a href="<?php echo $freelancer_instagram; ?>"><i class="fab fa-instagram"></i></a></li>
                <?php endif; ?>
                <?php $felan_social_fields = felan_get_option('felan_social_fields');
                if (is_array($felan_social_fields) && !empty($felan_social_fields)) {
                    foreach ($felan_social_fields as $key => $value) {
                        $freelancer_social_val = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_' . $value['social_name'], true);
                        if (!empty($freelancer_social_val)) { ?>
                            <li><a href="<?php echo $freelancer_social_val; ?>"><?php echo $value['social_icon']; ?></a></li>
                <?php }
                    }
                } ?>
                <?php felan_get_social_network($freelancer_id, 'freelancer'); ?>
            </ul>
        </div>
    </div>

</div>