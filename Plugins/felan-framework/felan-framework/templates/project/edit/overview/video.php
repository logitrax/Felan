<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $project_meta_data, $hide_project_fields;
?>
<?php if (!in_array('fields_project_video', $hide_project_fields)) : ?>
    <div class="form-group col-md-12">
        <label for="project_video_url"><?php esc_html_e('Introduction video Url', 'felan-framework') ?></label>
        <input type="url" id="project_video_url" name="project_video_url" value="<?php if (isset($project_meta_data[FELAN_METABOX_PREFIX . 'project_video_url'][0])) {
                                                                                        echo $project_meta_data[FELAN_METABOX_PREFIX . 'project_video_url'][0];
                                                                                    } ?>" placeholder="<?php esc_attr_e('Enter url video', 'felan-framework') ?>">
    </div>
<?php endif; ?>