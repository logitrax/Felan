<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$freelancer_id = get_the_ID();
if (!empty($freelancer_single_id)) {
    $freelancer_id = $freelancer_single_id;
}
$freelancer_educations = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_education_list', false);
$freelancer_educations = !empty($freelancer_educations) ? $freelancer_educations[0] : '';
if (empty($freelancer_educations[0][FELAN_METABOX_PREFIX . 'freelancer_education_title'])) {
    return;
}
?>

<div class="block-archive-inner freelancer-single-field">
    <h4 class="title-freelancer"><?php esc_html_e('Education', 'felan-framework') ?></h4>
    <?php foreach ($freelancer_educations as $education) : ?>
        <?php if (!empty($education[FELAN_METABOX_PREFIX . 'freelancer_education_title'])) : ?>
            <div class="single freelancer-education">
                <?php if (!empty($education[FELAN_METABOX_PREFIX . 'freelancer_education_title'])) : ?>
                    <div class="education-title time-dot">
                        <?php echo $education[FELAN_METABOX_PREFIX . 'freelancer_education_title']; ?>
                    </div>
                <?php endif; ?>
                <div class="education-details time-line">
                    <?php if (!empty($education[FELAN_METABOX_PREFIX . 'freelancer_education_level'])) : ?>
                        <span><?php echo $education[FELAN_METABOX_PREFIX . 'freelancer_education_level']; ?></span>
                    <?php endif; ?>
                    <?php if (!empty($education[FELAN_METABOX_PREFIX . 'freelancer_education_from'])) : ?>
                        <span><?php echo $education[FELAN_METABOX_PREFIX . 'freelancer_education_from']; ?></span>
                    <?php endif; ?>
                    <span>-</span>
                    <?php if (!empty($education[FELAN_METABOX_PREFIX . 'freelancer_education_to'])) : ?>
                        <span><?php echo $education[FELAN_METABOX_PREFIX . 'freelancer_education_to']; ?></span>
                    <?php endif; ?>
                    <?php if (!empty($education[FELAN_METABOX_PREFIX . 'freelancer_education_description'])) : ?>
                        <span class="des"><?php echo $education[FELAN_METABOX_PREFIX . 'freelancer_education_description']; ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php felan_custom_field_single_freelancer('education'); ?>
</div>