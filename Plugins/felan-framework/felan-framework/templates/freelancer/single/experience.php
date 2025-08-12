<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$freelancer_id = get_the_ID();
if (!empty($freelancer_single_id)) {
    $freelancer_id = $freelancer_single_id;
}
$freelancer_experiences = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_experience_list', false);
$freelancer_experiences = !empty($freelancer_experiences) ? $freelancer_experiences[0] : '';
if (empty($freelancer_experiences[0][FELAN_METABOX_PREFIX . 'freelancer_experience_job'])) {
    return;
}
?>
<div class="block-archive-inner freelancer-single-field">
    <h4 class="title-freelancer"><?php esc_html_e('Work Experience', 'felan-framework') ?></h4>
    <?php foreach ($freelancer_experiences as $experience) : ?>
        <?php if (!empty($experience[FELAN_METABOX_PREFIX . 'freelancer_experience_job'])) : ?>
            <div class="single freelancer-experience">
                <?php if (!empty($experience[FELAN_METABOX_PREFIX . 'freelancer_experience_job'])) : ?>
                    <div class="experience-title time-dot">
                        <?php echo $experience[FELAN_METABOX_PREFIX . 'freelancer_experience_job']; ?>
                    </div>
                <?php endif; ?>
                <div class="experience-details time-line">
                    <?php if (!empty($experience[FELAN_METABOX_PREFIX . 'freelancer_experience_company'])) : ?>
                        <span><?php echo $experience[FELAN_METABOX_PREFIX . 'freelancer_experience_company']; ?></span>
                    <?php endif; ?>
                    <?php if (!empty($experience[FELAN_METABOX_PREFIX . 'freelancer_experience_from'])) : ?>
                        <span><?php echo $experience[FELAN_METABOX_PREFIX . 'freelancer_experience_from']; ?></span>
                    <?php endif; ?>
                    <span>-</span>
                    <?php if (!empty($experience[FELAN_METABOX_PREFIX . 'freelancer_experience_to'])) : ?>
                        <span><?php echo $experience[FELAN_METABOX_PREFIX . 'freelancer_experience_to']; ?></span>
                    <?php endif; ?>
                    <?php if (!empty($experience[FELAN_METABOX_PREFIX . 'freelancer_experience_description'])) : ?>
                        <span class="des"><?php echo $experience[FELAN_METABOX_PREFIX . 'freelancer_experience_description']; ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php felan_custom_field_single_freelancer('experience'); ?>
</div>