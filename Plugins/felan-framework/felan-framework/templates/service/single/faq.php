<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$service_id = get_the_ID();
if (!empty($service_single_id)) {
    $service_id = $service_single_id;
}
$service_faq = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_tab_faq', true);
if (empty($service_faq[0]['felan-service_faq_title'])) {
    return;
}
?>
<div class="felan-block-inner block-archive-inner service-faq-details">
    <h4 class="title-service"><?php esc_html_e('FAQ', 'felan-framework') ?></h4>
    <?php foreach ($service_faq as $faq) { ?>
        <?php if (!empty($faq['felan-service_faq_title'])) : ?>
            <div class="faq-inner">
                <div class="faq-header">
                    <h5><?php echo $faq['felan-service_faq_title']; ?></h5>
                    <span><i class="far fa-chevron-down"></i></span>
                </div>
                <div class="faq-content">
                    <?php echo $faq['felan-service_faq_description']; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php } ?>
</div>