<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_style('lity');
wp_enqueue_script('lity');
$jobs_id = get_the_ID();
if (!empty($job_id)) {
    $jobs_id = $job_id;
}
$jobs_meta_data = get_post_custom($jobs_id);
$jobs_data = get_post($jobs_id);
$custom_field_jobs = felan_render_custom_field('jobs');
$image_src = FELAN_PLUGIN_URL . 'assets/images/bg-video.webp';
if (count($custom_field_jobs) <= 0) {
    return;
}
?>
<?php foreach ($custom_field_jobs as $key => $field) { ?>
    <?php switch ($field['type']) {
        case 'text':
            if (!empty($jobs_meta_data[$field['id']])) { ?>
                <div class="block-archive-inner jobs-additional-text">
                    <div class="additional-warpper">
                        <h4 class="title-jobs"><?php echo $field['title']; ?></h4>
                        <div class="content">
                            <?php echo sanitize_text_field($jobs_meta_data[$field['id']][0]); ?>
                        </div>
                    </div>
                </div>
            <?php }
            break;
        case 'url':
            if (!empty($jobs_meta_data[$field['id']])) { ?>
                <div class="block-archive-inner jobs-additional-url">
                    <div class="additional-warpper">
                        <h4 class="title-jobs"><?php echo $field['title']; ?></h4>
                        <div class="embed-responsive embed-responsive-16by9 embed-responsive-full">
                            <?php echo wp_oembed_get($jobs_meta_data[$field['id']][0], array('wmode' => 'transparent')); ?>
                        </div>
                    </div>
                </div>
            <?php }
            break;
        case 'textarea':
            if (!empty($jobs_meta_data[$field['id']])) { ?>
                <div class="block-archive-inner jobs-additional-textarea">
                    <div class="additional-warpper">
                        <h4 class="title-jobs"><?php echo $field['title']; ?></h4>
                        <div class="content">
                            <?php echo sanitize_text_field($jobs_meta_data[$field['id']][0]); ?>
                        </div>
                    </div>
                </div>
            <?php }
            break;
        case 'select':
            if (!empty($jobs_meta_data[$field['id']])) { ?>
                <div class="block-archive-inner jobs-additional-select">
                    <div class="additional-warpper">
                        <h4 class="title-jobs"><?php echo $field['title']; ?></h4>
                        <div class="content">
                            <?php echo sanitize_text_field($jobs_meta_data[$field['id']][0]); ?>
                        </div>
                    </div>
                </div>
            <?php }
            break;
        case 'checkbox_list':
            if (!empty($jobs_meta_data[$field['id']])) {
            ?>
                <div class="block-archive-inner jobs-additional-checkbox_list">
                    <div class="additional-warpper">
                        <h4 class="title-jobs"><?php echo $field['title']; ?></h4>
                        <div class="content">
                            <?php $jobs_field = get_post_meta($jobs_data->ID, $field['id'], true);
                            if (empty($jobs_field)) {
                                $jobs_field = array();
                            }
                            foreach ($field['options'] as $opt_value) :
                                if (in_array($opt_value, $jobs_field)) : ?>
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
            $jobs_field = get_post_meta($jobs_data->ID, $field['id'], true);
            if (!empty($jobs_field['url'])) { ?>
                <div class="block-archive-inner jobs-additional-image">
                    <div class="additional-warpper">
                        <h4 class="title-jobs"><?php echo $field['title']; ?></h4>
                        <img src="<?php echo esc_html($jobs_field['url']); ?>" alt="<?php echo esc_attr($field['title']); ?>" />
                    </div>
                </div>
<?php }
            break;
        case 'file':
            $jobs_field = get_post_meta($jobs_data->ID, $field['id'], true);
            $file_title = $file_url = '';
            if(!empty($jobs_field)){
                $file_title = get_the_title(intval($jobs_field));
                $file_url = wp_get_attachment_url(intval($jobs_field));
            }
            if (!empty($file_url)) { ?>
                <div class="block-archive-inner jobs-additional-file">
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