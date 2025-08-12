<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $service_data;
$package_service = felan_get_option('package_service');
$service_package_new = get_post_meta($service_data->ID, FELAN_METABOX_PREFIX . 'service_package_new', true);
?>
<div class="package-heder">
    <h6><?php echo esc_html__('Package Options', 'felan-framework') ?></h6>
    <p><?php echo esc_html__('Add options to your pricing packages', 'felan-framework') ?></p>
</div>
<div class="table-responsive">
    <table class="table-package">
        <thead>
            <tr>
                <th></th>
                <th><?php esc_html_e('Basic', 'felan-framework') ?></th>
                <th class="field-standard"><?php esc_html_e('Standard', 'felan-framework') ?></th>
                <th class="field-premium"><?php esc_html_e('Premium', 'felan-framework') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($package_service)) : ?>
                <?php foreach ($package_service as $key => $package) :
                    $service_package_title = get_post_meta($service_data->ID, FELAN_METABOX_PREFIX . 'service_package_title' . $key, true);
                    $service_package_list = get_post_meta($service_data->ID, FELAN_METABOX_PREFIX . 'service_package_list' . $key, true);
                    ?>
                    <tr>
                        <?php if (!empty($service_package_list) && !empty($service_package_title)) : ?>
                            <td class="title">
                                <input type="text" id="service_package_title" name="service_package_title[]" value="<?php echo esc_attr($service_package_title) ?>" required>
                            </td>
                            <td>
                                <input type="checkbox" class="custom-checkbox input-control" name="service_package_basic[]" <?php echo (in_array('basic', $service_package_list) ? 'checked' : ''); ?>>
                            </td>
                            <td class="field-standard">
                                <input type="checkbox" class="custom-checkbox input-control" name="service_package_standard[]" <?php echo (in_array('standard', $service_package_list) ? 'checked' : ''); ?>>
                            </td>
                            <td class="field-premium">
                                <input type="checkbox" class="custom-checkbox input-control" name="service_package_premium[]" <?php echo (in_array('premium', $service_package_list) ? 'checked' : ''); ?>>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if (!empty($service_package_new)) :
                foreach ($service_package_new as $index => $package) :
                    $new_title = $package[FELAN_METABOX_PREFIX . 'service_package_new_title'];
                    $new_list = $package[FELAN_METABOX_PREFIX . 'service_package_new_list'];
            ?>
                    <tr>
                        <td class="title">
                            <i class="delete-group fas fa-times"></i>
                            <input type="text" name="service_custom_title[]" placeholder="<?php echo esc_attr__('New Title', 'felan-framework'); ?>" value="<?php echo esc_attr($new_title); ?>">
                        </td>
                        <td>
                            <input type="checkbox" name="service_custom_basic[]" class="custom-checkbox input-control checkbox-basic" <?php echo (in_array('basic', $new_list) ? 'checked' : ''); ?>>
                        </td>
                        <td class="field-standard">
                            <input type="checkbox" name="service_custom_standard[]" class="custom-checkbox input-control checkbox-standard" <?php echo (in_array('standard', $new_list) ? 'checked' : ''); ?>>
                        </td>
                        <td class="field-premium">
                            <input type="checkbox" name="service_custom_premium[]" class="custom-checkbox input-control checkbox-premium" <?php echo (in_array('premium', $new_list) ? 'checked' : ''); ?>>
                        </td>
                    </tr>
            <?php endforeach;
            endif;
            ?>
    </table>
    <button type="button" class="btn-more package-fields"><i class="far fa-angle-down"></i><?php esc_html_e('Add more', 'felan-framework') ?></button>
    <template id="template-service-package">
        <tr>
            <td class="title">
                <i class="delete-group fas fa-times"></i>
                <input type="text" name="service_custom_title[]" placeholder="<?php echo esc_attr__('New Title', 'felan-framework'); ?>">
            </td>
            <td>
                <input type="checkbox" name="service_custom_basic[]" class="custom-checkbox input-control checkbox-basic">
            </td>
            <td class="field-standard">
                <input type="checkbox" name="service_custom_standard[]" class="custom-checkbox input-control checkbox-standard">
            </td>
            <td class="field-premium">
                <input type="checkbox" name="service_custom_premium[]" class="custom-checkbox input-control checkbox-premium">
            </td>
        </tr>
    </template>
</div>