<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $service_data;
$service_quantity = get_post_meta($service_data->ID, FELAN_METABOX_PREFIX . 'service_quantity', true);
$service_time = get_post_meta($service_data->ID, FELAN_METABOX_PREFIX . 'service_time', true);

$service_basic_price = get_post_meta($service_data->ID, FELAN_METABOX_PREFIX . 'service_basic_price', true);
$service_basic_time = get_post_meta($service_data->ID, FELAN_METABOX_PREFIX . 'service_basic_time', true);
$service_basic_revisions = get_post_meta($service_data->ID, FELAN_METABOX_PREFIX . 'service_basic_revisions', true);
$service_basic_number_revisions = get_post_meta($service_data->ID, FELAN_METABOX_PREFIX . 'service_basic_number_revisions', true);
$service_basic_des = get_post_meta($service_data->ID, FELAN_METABOX_PREFIX . 'service_basic_des', true);

$service_standard_price = get_post_meta($service_data->ID, FELAN_METABOX_PREFIX . 'service_standard_price', true);
$service_standard_time = get_post_meta($service_data->ID, FELAN_METABOX_PREFIX . 'service_standard_time', true);
$service_standard_revisions = get_post_meta($service_data->ID, FELAN_METABOX_PREFIX . 'service_standard_revisions', true);
$service_standard_number_revisions = get_post_meta($service_data->ID, FELAN_METABOX_PREFIX . 'service_standard_number_revisions', true);
$service_standard_des = get_post_meta($service_data->ID, FELAN_METABOX_PREFIX . 'service_standard_des', true);

$service_premium_price = get_post_meta($service_data->ID, FELAN_METABOX_PREFIX . 'service_premium_price', true);
$service_premium_time = get_post_meta($service_data->ID, FELAN_METABOX_PREFIX . 'service_premium_time', true);
$service_premium_revisions = get_post_meta($service_data->ID, FELAN_METABOX_PREFIX . 'service_premium_revisions', true);
$service_premium_number_revisions = get_post_meta($service_data->ID, FELAN_METABOX_PREFIX . 'service_premium_number_revisions', true);
$service_premium_des = get_post_meta($service_data->ID, FELAN_METABOX_PREFIX . 'service_premium_des', true);
?>
<div class="row">
    <div class="pricing-heder">
        <div class="header-right">
            <h6><?php echo esc_html__('Pricing', 'felan-framework') ?></h6>
        </div>
    </div>
    <div class="pricing-center">
        <div class="form-group">
            <label><?php esc_html_e('Time Type', 'felan-framework') ?> </label>
            <div class="select2-field">
                <select name="service_time" class="felan-select2">
                    <option value="hr" <?php echo $service_time === 'hr' ? 'selected' : '' ?>><?php esc_html_e('Hour', 'felan-framework'); ?></option>
                    <option value="day" <?php echo $service_time === 'day' ? 'selected' : '' ?>><?php esc_html_e('Day', 'felan-framework'); ?></option>
                    <option value="week" <?php echo $service_time === 'week' ? 'selected' : '' ?>><?php esc_html_e('Week', 'felan-framework'); ?></option>
                    <option value="month" <?php echo $service_time === 'month' ? 'selected' : '' ?>><?php esc_html_e('Month', 'felan-framework'); ?></option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label><?php esc_html_e('Package Quantity', 'felan-framework') ?></label>
            <div class="select2-field">
                <select name="service_quantity" class="felan-select2">
                    <option value="1" <?php echo $service_quantity === '1' ? 'selected' : '' ?>>1</option>
                    <option value="2" <?php echo $service_quantity === '2' ? 'selected' : '' ?>>2</option>
                    <option value="3" <?php echo $service_quantity === '3' ? 'selected' : '' ?>>3</option>
                </select>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table-pricing">
            <thead>
                <tr>
                    <th></th>
                    <th><?php esc_html_e('Basic', 'felan-framework') ?></th>
                    <th class="field-standard"><?php esc_html_e('Standard', 'felan-framework') ?></th>
                    <th class="field-premium"><?php esc_html_e('Premium', 'felan-framework') ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="title">
                        <?php esc_html_e('Description', 'felan-framework') ?>
                    </td>
                    <td>
                        <textarea class="form-control" rows="3" name="service_basic_des" placeholder="<?php esc_attr_e('Describe the details here...', 'felan-framework'); ?>"><?php echo esc_html($service_basic_des); ?></textarea>
                    </td>
                    <td class="field-standard">
                        <textarea class="form-control" rows="3" name="service_standard_des" placeholder="<?php esc_attr_e('Describe the details here...', 'felan-framework'); ?>"><?php echo esc_html($service_standard_des); ?></textarea>
                    </td>
                    <td class="field-premium">
                        <textarea class="form-control" rows="3" name="service_premium_des" placeholder="<?php esc_attr_e('Describe the details here...', 'felan-framework'); ?>"><?php echo esc_attr($service_premium_des); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="title">
                        <?php esc_html_e('Price', 'felan-framework') ?>
                    </td>
                    <td>
                        <input type="number" name="service_basic_price" value="<?php echo esc_attr($service_basic_price); ?>" placeholder="0.00" oninput="this.value = this.value.replace(/[^\d]/g, '')"/>
                    </td>
                    <td class="field-standard">
                        <input type="number" name="service_standard_price" value="<?php echo esc_attr($service_standard_price); ?>" placeholder="0.00" oninput="this.value = this.value.replace(/[^\d]/g, '')"/>
                    </td>
                    <td class="field-premium">
                        <input type="number" name="service_premium_price" value="<?php echo esc_attr($service_premium_price); ?>" placeholder="0.00" oninput="this.value = this.value.replace(/[^\d]/g, '')"/>
                    </td>
                </tr>
                <tr>
                    <td class="title">
                        <?php esc_html_e('Delivery Time', 'felan-framework') ?>
                    </td>
                    <td>
                        <input type="number" name="service_basic_time" value="<?php echo esc_attr($service_basic_time); ?>" placeholder="0" oninput="this.value = this.value.replace(/[^\d]/g, '')"/>
                    </td>
                    <td class="field-standard">
                        <input type="number" name="service_standard_time" value="<?php echo esc_attr($service_standard_time); ?>" placeholder="0" oninput="this.value = this.value.replace(/[^\d]/g, '')"/>
                    </td>
                    <td class="field-premium">
                        <input type="number" name="service_premium_time" value="<?php echo esc_attr($service_premium_time); ?>" placeholder="0" oninput="this.value = this.value.replace(/[^\d]/g, '')"/>
                    </td>
                </tr>
                <tr class="number-revisions">
                    <td class="title">
                        <?php esc_html_e('Number of Revisions', 'felan-framework') ?>
                    </td>
                    <td class="filed-revisions">
                        <div class="select2-field">
                            <select name="service_basic_revisions" class="felan-select2">
                                <option value="none" <?php echo $service_basic_revisions === 'none' ? 'selected' : '' ?>><?php esc_html_e('None', 'felan-framework'); ?></option>
                                <option value="unlimited" <?php echo $service_basic_revisions === 'unlimited' ? 'selected' : '' ?>><?php esc_html_e('Unlimited', 'felan-framework'); ?></option>
                                <option value="custom" <?php echo $service_basic_revisions === 'custom' ? 'selected' : '' ?>><?php esc_html_e('Custom', 'felan-framework'); ?></option>
                            </select>
                            <input type="number" name="service_basic_number_revisions" value="<?php echo esc_attr($service_basic_number_revisions); ?>" placeholder="0" />
                        </div>
                    </td>
                    <td class="field-standard filed-revisions">
                        <div class="select2-field">
                            <select name="service_standard_revisions" class="felan-select2">
                                <option value="none" <?php echo $service_standard_revisions === 'none' ? 'selected' : '' ?>><?php esc_html_e('None', 'felan-framework'); ?></option>
                                <option value="unlimited" <?php echo $service_standard_revisions === 'unlimited' ? 'selected' : '' ?>><?php esc_html_e('Unlimited', 'felan-framework'); ?></option>
                                <option value="custom" <?php echo $service_standard_revisions === 'custom' ? 'selected' : '' ?>><?php esc_html_e('Custom', 'felan-framework'); ?></option>
                            </select>
                            <input type="number" name="service_standard_number_revisions" value="<?php echo esc_attr($service_standard_number_revisions); ?>" placeholder="0" />
                        </div>
                    </td>
                    <td class="field-premium filed-revisions">
                        <div class="select2-field">
                            <select name="service_premium_revisions" class="felan-select2">
                                <option value="none" <?php echo $service_premium_revisions === 'none' ? 'selected' : '' ?>><?php esc_html_e('None', 'felan-framework'); ?></option>
                                <option value="unlimited" <?php echo $service_premium_revisions === 'unlimited' ? 'selected' : '' ?>><?php esc_html_e('Unlimited', 'felan-framework'); ?></option>
                                <option value="custom" <?php echo $service_premium_revisions === 'custom' ? 'selected' : '' ?>><?php esc_html_e('Custom', 'felan-framework'); ?></option>
                            </select>
                        </div>
                        <input type="number" name="service_premium_number_revisions" value="<?php echo esc_attr($service_premium_number_revisions); ?>" placeholder="0" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>