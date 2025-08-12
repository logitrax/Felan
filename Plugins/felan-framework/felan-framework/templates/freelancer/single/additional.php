<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$custom_field_freelancer = felan_render_custom_field('freelancer');
if (count($custom_field_freelancer) > 0) :
    $tabs_array = array();
    foreach ($custom_field_freelancer as $field) {
        if ((!in_array($field['section'], $tabs_array)) && !empty($field['section'])) {
            $tabs_array[] = $field['section'];
        }
    }
    foreach ($tabs_array as $value) {
        $tabs_id = str_replace(" ", "-", $value); ?>
        <div class="block-archive-inner freelancer-single-field freelancer-custom-tab additional-<?php echo $tabs_id; ?>">
            <h3 class="title-additional"><?php echo $tabs_id; ?></h3>
            <?php felan_custom_field_single_freelancer($value, true); ?>
        </div>
    <?php } ?>
<?php endif; ?>