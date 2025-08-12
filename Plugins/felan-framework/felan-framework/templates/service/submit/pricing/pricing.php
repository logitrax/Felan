<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $service_data;
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
                    <option value="hr"><?php esc_html_e('Hour', 'felan-framework'); ?></option>
                    <option value="day"><?php esc_html_e('Day', 'felan-framework'); ?></option>
                    <option value="week"><?php esc_html_e('Week', 'felan-framework'); ?></option>
                    <option value="month"><?php esc_html_e('Month', 'felan-framework'); ?></option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label><?php esc_html_e('Package Quantity', 'felan-framework') ?></label>
            <div class="select2-field">
                <select name="service_quantity" class="felan-select2">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3" selected>3</option>
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
                        <textarea class="form-control" rows="3" name="service_basic_des" placeholder="<?php esc_attr_e('Describe the details here...', 'felan-framework'); ?>"></textarea>
                    </td>
                    <td class="field-standard">
                        <textarea class="form-control" rows="3" name="service_standard_des" placeholder="<?php esc_attr_e('Describe the details here...', 'felan-framework'); ?>"></textarea>
                    </td>
                    <td class="field-premium">
                        <textarea class="form-control" rows="3" name="service_premium_des" placeholder="<?php esc_attr_e('Describe the details here...', 'felan-framework'); ?>"></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="title">
                        <?php esc_html_e('Price', 'felan-framework') ?><sup> *</sup>
                    </td>
                    <td>
                        <input type="number" name="service_basic_price" value="" placeholder="0.00" oninput="this.value = this.value.replace(/[^\d]/g, '')"/>
                    </td>
                    <td class="field-standard">
                        <input type="number" name="service_standard_price" value="" placeholder="0.00" oninput="this.value = this.value.replace(/[^\d]/g, '')">
                    </td>
                    <td class="field-premium">
                        <input type="number" name="service_premium_price" value="" placeholder="0.00" oninput="this.value = this.value.replace(/[^\d]/g, '')"/>
                    </td>
                </tr>
                <tr>
                    <td class="title">
                        <?php esc_html_e('Delivery Time', 'felan-framework') ?>
                    </td>
                    <td>
                        <input type="number" name="service_basic_time" value="" placeholder="0" oninput="this.value = this.value.replace(/[^\d]/g, '')"/>
                    </td>
                    <td class="field-standard">
                        <input type="number" name="service_standard_time" value="" placeholder="0" oninput="this.value = this.value.replace(/[^\d]/g, '')"/>
                    </td>
                    <td class="field-premium">
                        <input type="number" name="service_premium_time" value="" placeholder="0" oninput="this.value = this.value.replace(/[^\d]/g, '')"/>
                    </td>
                </tr>
                <tr class="number-revisions">
                    <td class="title">
                        <?php esc_html_e('Number of Revisions', 'felan-framework') ?>
                    </td>
                    <td class="filed-revisions">
                        <div class="select2-field">
                            <select name="service_basic_revisions" class="felan-select2">
                                <option value="none"><?php esc_html_e('None', 'felan-framework'); ?></option>
                                <option value="unlimited"><?php esc_html_e('Unlimited', 'felan-framework'); ?></option>
                                <option value="custom"><?php esc_html_e('Custom', 'felan-framework'); ?></option>
                            </select>
                            <input type="number" name="service_basic_number_revisions" value="" placeholder="0" />
                        </div>
                    </td>
                    <td class="field-standard filed-revisions">
                        <div class="select2-field">
                            <select name="service_standard_revisions" class="felan-select2">
                                <option value="none"><?php esc_html_e('None', 'felan-framework'); ?></option>
                                <option value="unlimited"><?php esc_html_e('Unlimited', 'felan-framework'); ?></option>
                                <option value="custom"><?php esc_html_e('Custom', 'felan-framework'); ?></option>
                            </select>
                            <input type="number" name="service_standard_number_revisions" value="" placeholder="0" />
                        </div>
                    </td>
                    <td class="field-premium filed-revisions">
                        <div class="select2-field">
                            <select name="service_premium_revisions" class="felan-select2">
                                <option value="none"><?php esc_html_e('None', 'felan-framework'); ?></option>
                                <option value="unlimited"><?php esc_html_e('Unlimited', 'felan-framework'); ?></option>
                                <option value="custom"><?php esc_html_e('Custom', 'felan-framework'); ?></option>
                            </select>
                        </div>
                        <input type="number" name="service_premium_number_revisions" value="" placeholder="0" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>