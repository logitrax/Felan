<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$company_single_id = get_the_ID();
if (!empty($post_id)) {
    $company_single_id = $post_id;
}
$enable_post_type_project = felan_get_option('enable_post_type_project','1');
$classes = array('felan-company-wrap', 'single-company-area');
$hide_company_tabs_groups = felan_get_option('hide_company_tabs_groups', array());
if (!is_array($hide_company_tabs_groups)) {
    $hide_company_tabs_groups = array();
}
$layout = array('about_us', 'photos', 'projects', 'reviews');
?>
<div id="company-<?php the_ID(); ?>" <?php post_class($classes); ?>>
    <div class="block-company-warrper">
        <div class="tab-single">
            <ul class="tab-single-list">
                <?php foreach ($layout as $value) {
                    if ($enable_post_type_project != '1' && $value === 'projects') {
                        continue;
                    }

                    switch ($value) {
                        case 'about_us':
                            $name = esc_html__('About Us', 'felan-framework');
                            break;
                        case 'photos':
                            $name = esc_html__('Photos', 'felan-framework');
                            break;
                        case 'projects':
                            $name = esc_html__('Projects', 'felan-framework');
                            break;
                        case 'reviews':
                            $name = esc_html__('Reviews', 'felan-framework');
                            break;
                        default:
                            $name = ''; // Default value in case no match is found
                            break;
                    }

                    if (!in_array($value, $hide_company_tabs_groups)) : ?>
                        <li class="tab-single-item">
                            <a href="#tab-<?php echo esc_attr($value); ?>"><?php echo esc_html($name); ?></a>
                        </li>
                    <?php endif;
                } ?>
            </ul>
            <div class="tab-single-content">
                <?php foreach ($layout as $value) {
                    if (!in_array($value, $hide_company_tabs_groups)) : ?>
                        <div id="tab-<?php esc_attr_e($value) ?>" class="tab-single-info">
                            <?php do_action('felan_tab_single_company_' . $value, $company_single_id); ?>
                        </div>
                <?php endif;
                } ?>
            </div>
        </div>
        <?php
        /**
         * Hook: felan_after_content_single_company_summary hook.
         */
        do_action('felan_after_content_single_company_summary'); ?>
    </div>
</div>