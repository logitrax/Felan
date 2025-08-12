<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $hide_project_fields;

?>
<?php if (!in_array('fields_project_video', $hide_project_fields)) : ?>
    <div class="form-group col-md-12">
        <label for="project_video_url"><?php esc_html_e('Introduction video Url', 'felan-framework') ?></label>
        <input type="url" id="project_video_url" name="project_video_url" placeholder="<?php esc_attr_e('Enter url video', 'felan-framework') ?>">
    </div>
<?php endif; ?>