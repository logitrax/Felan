<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $hide_service_fields;
$layout = array('pricing', 'package', 'addons');

foreach ($layout as $value) {
    switch ($value) {
        case 'pricing':
            $name = esc_html__('Pricing', 'felan-framework');
            break;
        case 'package':
            $name = esc_html__('Package', 'felan-framework');
            break;
        case 'addons':
            $name = esc_html__('Addons', 'felan-framework');
            break;
    } ?>
    <?php if (!in_array('fields_service_' . $value, $hide_service_fields)) { ?>
        <div class="block-from" id="<?php echo 'service-submit-' . esc_attr($value); ?>">
            <?php felan_get_template('service/submit/pricing/' . $value . '.php'); ?>
        </div>
<?php }
} ?>