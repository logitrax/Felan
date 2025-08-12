<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$company_id = get_the_ID();
if (!empty($company_single_id)) {
    $company_id = $company_single_id;
}
$content = get_post_field('post_content', $company_id);
if (isset($content) && !empty($content)) : ?>
    <div class="block-archive-inner company-overview-details felan-description-details">
        <h4 class="title-company"><?php esc_html_e('Overview', 'felan-framework') ?></h4>
        <div class="felan-description">
			<?php echo apply_filters('the_content', $content); ?>
        </div>
        <div class="toggle-description">
            <a href="#" class="show-more-description"><?php esc_html_e('Show more', 'felan-framework'); ?><i class="far fa-angle-down"></i></a>
            <a href="#" class="hide-all-description"><?php esc_html_e('Hide less', 'felan-framework'); ?><i class="far fa-angle-up"></i></a>
        </div>
    </div>
<?php endif; ?>