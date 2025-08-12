<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'login-to-view');
global $post;

$freelancer_single_id = get_the_ID();
if (!empty($post_id)) {
    $freelancer_single_id = $post_id;
}

$classes = array('felan-freelancer-wrap', 'single-freelancer-area');
$hide_freelancer_tabs_groups = felan_get_option('hide_freelancer_tabs_groups', array());
if (!is_array($hide_freelancer_tabs_groups)) {
    $hide_freelancer_tabs_groups = array();
}
$layout = array('about_me', 'projects', 'services', 'reviews');

$enable_post_type_service = felan_get_option('enable_post_type_service','1');
$enable_post_type_project = felan_get_option('enable_post_type_project','1');
?>
<div id="freelancer-<?php the_ID(); ?>" <?php post_class($classes); ?>>
    <div class="block-freelancer-warrper">
        <div class="tab-single">
            <ul class="tab-single-list">
                <?php
                foreach ($layout as $value) {
                    if ($value === 'services' && $enable_post_type_service !== '1') {
                        continue;
                    }

                    if ($value === 'projects' && $enable_post_type_project !== '1') {
                        continue;
                    }

                    switch ($value) {
                        case 'about_me':
                            $name = esc_html__('About Me', 'felan-framework');
                            break;
                        case 'projects':
                            $name = esc_html__('Projects Completed', 'felan-framework');
                            break;
                        case 'services':
                            $name = esc_html__('Services', 'felan-framework');
                            break;
                        case 'reviews':
                            $name = esc_html__('Reviews', 'felan-framework');
                            break;
                    }
                    if (!in_array($value, $hide_freelancer_tabs_groups)) : ?>
                        <li class="tab-single-item"><a href="#tab-<?php esc_attr_e($value) ?>"><?php esc_html_e($name) ?></a>
                        </li>
                    <?php
                    endif;
                } ?>
            </ul>
            <div class="tab-single-content">
                <?php
                foreach ($layout as $value) {
                    if ($value === 'services' && $enable_post_type_service !== '1') {
                        continue;
                    }

                    if ($value === 'projects' && $enable_post_type_project !== '1') {
                        continue;
                    }

                    if (!in_array($value, $hide_freelancer_tabs_groups)) : ?>
                        <div id="tab-<?php esc_attr_e($value) ?>" class="tab-single-info">
                            <?php do_action('felan_tab_single_freelancer_' . $value, $freelancer_single_id); ?>
                        </div>
                    <?php
                    endif;
                } ?>
            </div>
        </div>
        <?php
        /**
         * Hook: felan_after_content_single_freelancer_summary hook.
         */
        do_action('felan_after_content_single_freelancer_summary'); ?>
    </div>
</div>