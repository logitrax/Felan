<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$project_id = get_the_ID();
if (!empty($project_single_id)) {
    $project_id = $project_single_id;
}
$project_faq = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_tab_faq', true);
if (empty($project_faq[0]['felan-project_faq_title'])) {
    return;
}
?>
<div class="felan-block-inner block-archive-inner project-faq-details">
    <h4 class="title-project"><?php esc_html_e('Frequently Asked Questions', 'felan-framework') ?></h4>
    <?php foreach ($project_faq as $faq) { ?>
        <?php if (!empty($faq['felan-project_faq_title'])) : ?>
            <div class="faq-inner">
                <div class="faq-header">
                    <h5><?php echo $faq['felan-project_faq_title']; ?></h5>
                    <span><i class="far fa-chevron-down"></i></span>
                </div>
                <div class="faq-content">
                    <?php echo $faq['felan-project_faq_description']; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php } ?>
</div>