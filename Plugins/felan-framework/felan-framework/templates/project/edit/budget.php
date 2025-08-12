<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $hide_projects_fields,$project_data, $current_user;
$project_id = $project_data->ID;
$projects_budget_show = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_budget_show', true);
$projects_budget_minimum = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_budget_minimum', true);
$projects_budget_maximum = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_budget_maximum', true);
$project_value_rate = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_value_rate', true);
$projects_budget_rate = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_budget_rate', true);
$project_maximum_hours = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_maximum_hours', true);
?>
<div class="block-from" id="project-submit-budget">
    <h6><?php echo esc_html('Budget', 'felan-framework') ?></h6>
    <div class="row">
        <div class="form-group col-md-12">
            <label><?php esc_html_e('Project Type', 'felan-framework'); ?></label>
            <div class="select2-field">
                <select id="select-budget-show" name="project_budget_show" class="felan-select2">
                    <option value="fixed" <?php if ($projects_budget_show == "fixed") {
                        echo 'selected';
                    } ?>><?php esc_html_e('Fixed Price', 'felan-framework'); ?></option>
                    <option value="hourly" <?php if ($projects_budget_show == "hourly") {
                        echo 'selected';
                    } ?>><?php esc_html_e('Hourly Rate', 'felan-framework'); ?></option>
                </select>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label for="project_budget_minimum"><?php esc_html_e('Minimum Price', 'felan-framework'); ?></label>
            <input type="number" id="project_budget_minimum" name="project_budget_minimum" pattern="[-+]?[0-9]" value="<?php echo esc_attr($projects_budget_minimum); ?>">
        </div>
        <div class="form-group col-md-6">
            <label for="project_budget_maximum"><?php esc_html_e('Maximum Price', 'felan-framework'); ?></label>
            <input type="number" id="project_budget_maximum" name="project_budget_maximum" pattern="[-+]?[0-9]" value="<?php echo esc_attr($projects_budget_maximum); ?>">
        </div>
        <div class="form-group col-md-6">
            <label for="project_maximum_hours"><?php esc_html_e('Estimated maximum hours', 'felan-framework'); ?></label>
            <input type="number" id="project_maximum_hours" name="project_maximum_hours" pattern="[-+]?[0-9]" value="<?php echo esc_attr($project_maximum_hours); ?>">
        </div>
        <div class="form-group col-md-6">
            <label for="project_value_rate"><?php esc_html_e('Estimated maximum time', 'felan-framework'); ?></label>
            <input type="number" id="project_value_rate" name="project_value_rate" pattern="[-+]?[0-9]" value="<?php echo esc_attr($project_value_rate); ?>">
        </div>
        <div class="form-group col-md-6" id="projects_rate">
            <label><?php esc_html_e('Rate', 'felan-framework'); ?></label>
            <div class="select2-field">
                <select name="project_budget_rate" class="felan-select2">
                    <option value=""><?php esc_html_e('None', 'felan-framework'); ?></option>
                    <option <?php if ($projects_budget_rate == "hour") {
                                echo 'selected';
                            } ?> value="hour"><?php esc_html_e('Per Hour', 'felan-framework'); ?></option>
                    <option <?php if ($projects_budget_rate == "day") {
                                echo 'selected';
                            } ?> value="day"><?php esc_html_e('Per Day', 'felan-framework'); ?></option>
                    <option <?php if ($projects_budget_rate == "week") {
                                echo 'selected';
                            } ?> value="week"><?php esc_html_e('Per Week', 'felan-framework'); ?></option>
                    <option <?php if ($projects_budget_rate == "month") {
                                echo 'selected';
                            } ?> value="month"><?php esc_html_e('Per Month', 'felan-framework'); ?></option>
                    <option <?php if ($projects_budget_rate == "year") {
                                echo 'selected';
                            } ?> value="year"><?php esc_html_e('Per Year', 'felan-framework'); ?></option>
                </select>
            </div>
        </div>
    </div>
</div>