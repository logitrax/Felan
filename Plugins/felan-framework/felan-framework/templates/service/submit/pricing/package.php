<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$package_service = felan_get_option('package_service');
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
                <?php foreach ($package_service as $package) : ?>
                    <tr>
                        <?php if (!empty($package['package_checkbox_list']) && !empty($package['package_service_title'])) : ?>
                            <td class="title">
                                <input type="text" id="service_package_title" name="service_package_title[]" value="<?php echo esc_attr($package['package_service_title']) ?>" required>
                            </td>
                            <td>
                                <input type="checkbox" class="custom-checkbox input-control" name="service_package_basic[]" <?php echo (in_array('basic', $package['package_checkbox_list']) ? 'checked' : ''); ?>>
                            </td>
                            <td class="field-standard">
                                <input type="checkbox" class="custom-checkbox input-control" name="service_package_standard[]" <?php echo (in_array('standard', $package['package_checkbox_list']) ? 'checked' : ''); ?>>
                            </td>
                            <td class="field-premium">
                                <input type="checkbox" class="custom-checkbox input-control" name="service_package_premium[]" <?php echo (in_array('premium', $package['package_checkbox_list']) ? 'checked' : ''); ?>>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
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