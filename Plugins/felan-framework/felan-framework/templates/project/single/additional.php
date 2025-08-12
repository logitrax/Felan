<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_style('lity');
wp_enqueue_script('lity');
$project_id = get_the_ID();
if (!empty($project_single_id)) {
    $project_id = $project_single_id;
}
$project_meta_data = get_post_custom($project_id);
$project_data = get_post($project_id);
$custom_field_project = felan_render_custom_field('project');
$image_src = FELAN_PLUGIN_URL . 'assets/images/bg-video.webp';
if (count($custom_field_project) <= 0) {
    return;
}
?>
<?php foreach ($custom_field_project as $key => $field) { ?>
    <?php switch ($field['type']) {
        case 'text':
            if (!empty($project_meta_data[$field['id']])) { ?>
                <div class="block-archive-inner project-additional-text">
                    <div class="additional-warpper">
                        <h4 class="title-project"><?php echo $field['title']; ?></h4>
                        <div class="content">
                            <?php echo sanitize_text_field($project_meta_data[$field['id']][0]); ?>
                        </div>
                    </div>
                </div>
            <?php }
            break;
        case 'url':
            if (!empty($project_meta_data[$field['id']])) { ?>
                <div class="block-archive-inner project-additional-url">
                    <div class="additional-warpper">
                        <h4 class="title-project"><?php echo $field['title']; ?></h4>
                        <div class="embed-responsive embed-responsive-16by9 embed-responsive-full">
                            <?php echo wp_oembed_get($project_meta_data[$field['id']][0], array('wmode' => 'transparent')); ?>
                        </div>
                    </div>
                </div>
            <?php }
            break;
        case 'textarea':
            if (!empty($project_meta_data[$field['id']])) { ?>
                <div class="block-archive-inner project-additional-textarea">
                    <div class="additional-warpper">
                        <h4 class="title-project"><?php echo $field['title']; ?></h4>
                        <div class="content">
                            <?php echo sanitize_text_field($project_meta_data[$field['id']][0]); ?>
                        </div>
                    </div>
                </div>
            <?php }
            break;
        case 'select':
            if (!empty($project_meta_data[$field['id']])) { ?>
                <div class="block-archive-inner project-additional-select">
                    <div class="additional-warpper">
                        <h4 class="title-project"><?php echo $field['title']; ?></h4>
                        <div class="content">
                            <?php echo sanitize_text_field($project_meta_data[$field['id']][0]); ?>
                        </div>
                    </div>
                </div>
            <?php }
            break;
        case 'checkbox_list':
            if (!empty($project_meta_data[$field['id']])) {
            ?>
                <div class="block-archive-inner project-additional-checkbox_list">
                    <div class="additional-warpper">
                        <h4 class="title-project"><?php echo $field['title']; ?></h4>
                        <div class="content">
                            <?php $project_field = get_post_meta($project_data->ID, $field['id'], true);
                            if (empty($project_field)) {
                                $project_field = array();
                            }
                            foreach ($field['options'] as $opt_value) :
                                if (in_array($opt_value, $project_field)) : ?>
                                    <div class="label label-skills"><?php esc_html_e($opt_value); ?></div>
                            <?php endif;
                            endforeach;
                            ?>
                        </div>
                    </div>
                </div>
            <?php }
            break;
        case 'image':
            $project_field = get_post_meta($project_data->ID, $field['id'], true);
            if (!empty($project_field['url'])) { ?>
                <div class="block-archive-inner project-additional-image">

                    <div class="additional-warpper">
                        <h4 class="title-project"><?php echo $field['title']; ?></h4>
                        <img src="<?php echo esc_html($project_field['url']); ?>" alt="<?php echo esc_attr($field['title']); ?>" />
                    </div>
                </div>
<?php }
            break;
        case 'file':
            $project_field = get_post_meta($project_data->ID, $field['id'], true);
            $file_title = $file_url = '';
            if(!empty($project_field)){
                $file_title = get_the_title(intval($project_field));
                $file_url = wp_get_attachment_url(intval($project_field));
            }
            if (!empty($file_url)) { ?>
                <div class="block-archive-inner jobs-additional-upload">
                    <div class="additional-warpper">
                        <h4 class="title-jobs"><?php echo $field['title']; ?></h4>
                        <a class="felan-button" href="<?php echo esc_attr($file_url); ?>">
                            <i class="far fa-download"></i>
                            <?php echo esc_html($file_title); ?>
                        </a>
                    </div>
                </div>
            <?php }
            break;
    }
} ?>