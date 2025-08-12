<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$freelancer_details_prints = felan_get_option('freelancer_details_prints');
foreach ($freelancer_details_prints as $print) {
    if (!in_array('enable_print_sp_awards', $freelancer_details_prints)) {
        return;
    }
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
    <?php
    $custom_field_freelancer = felan_render_custom_field('freelancer');
    $freelancer_meta_data = get_post_custom($freelancer_id);
    $freelancer_data = get_post($freelancer_id);
    $check_tabs = false;
    foreach ($custom_field_freelancer as $field) {
        if ($field['tabs'] == 'awards') {
            $check_tabs = true;
        }
    }

    if (count($custom_field_freelancer) > 0) {
        if ($check_tabs == true) : ?>
            <?php foreach ($custom_field_freelancer as $field) {
                if ($field['tabs'] == 'awards') { ?>
            <?php felan_get_template("freelancer/print/additional/field.php", array(
                        'field' => $field,
                        'freelancer_data' => $freelancer_data,
                        'freelancer_meta_data' => $freelancer_meta_data
                    ));
                }
            } ?>
    <?php endif;
    }
    ?>
</div>