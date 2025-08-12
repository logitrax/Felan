<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$freelancer_id = get_the_ID();
if (!empty($freelancer_single_id)) {
    $freelancer_id = $freelancer_single_id;
}
$freelancer_awards = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_award_list', false);
$freelancer_awards = !empty($freelancer_awards) ? $freelancer_awards[0] : '';
if (empty($freelancer_awards[0][FELAN_METABOX_PREFIX . 'freelancer_award_title'])) {
    return;
}
?>

<div class="block-archive-inner freelancer-single-field">
    <h4 class="title-freelancer"><?php esc_html_e('Honors & awards', 'felan-framework') ?></h4>
    <?php foreach ($freelancer_awards as $award) : ?>
        <?php if (!empty($award[FELAN_METABOX_PREFIX . 'freelancer_award_title'])) : ?>
            <div class="single freelancer-award">
                <?php if (!empty($award[FELAN_METABOX_PREFIX . 'freelancer_award_title'])) : ?>
                    <div class="award-title time-dot">
                        <?php echo $award[FELAN_METABOX_PREFIX . 'freelancer_award_title']; ?>
                    </div>
                <?php endif; ?>
                <div class="award-details time-line">
                    <?php if (!empty($award[FELAN_METABOX_PREFIX . 'freelancer_award_date'])) : ?>
                        <span><?php echo $award[FELAN_METABOX_PREFIX . 'freelancer_award_date']; ?></span>
                    <?php endif; ?>
                    <?php if (!empty($award[FELAN_METABOX_PREFIX . 'freelancer_award_description'])) : ?>
                        <span><?php echo $award[FELAN_METABOX_PREFIX . 'freelancer_award_description']; ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php felan_custom_field_single_freelancer('awards'); ?>
</div>