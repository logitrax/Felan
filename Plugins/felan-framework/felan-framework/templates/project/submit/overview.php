<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $hide_project_fields;
$layout = array('general', 'skills', 'location', 'cover_image', 'gallery', 'video');

foreach ($layout as $value) {
    switch ($value) {
        case 'general':
            $name = esc_html__('Basic info', 'felan-framework');
            break;
        case 'skills':
            $name = esc_html__('Skills', 'felan-framework');
            break;
        case 'location':
            $name = esc_html__('Location', 'felan-framework');
            break;
        case 'cover_image':
            $name = esc_html__('Cover image', 'felan-framework');
            break;
        case 'gallery':
            $name = esc_html__('Gallery', 'felan-framework');
            break;
        case 'video':
            $name = esc_html__('Video', 'felan-framework');
            break;
    } ?>
    <?php if (!in_array('fields_project_' . $value, $hide_project_fields)) { ?>
        <div class="block-from" id="<?php echo 'project-submit-' . esc_attr($value); ?>">
            <h6><?php echo $name ?></h6>
            <?php felan_get_template('project/submit/overview/' . $value . '.php'); ?>
        </div>
<?php }
} ?>