<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_style('lity');
wp_enqueue_script('lity');
$services_id = get_the_ID();
if (!empty($service_id)) {
    $services_id = $service_id;
}
$services_meta_data = get_post_custom($services_id);
$services_data = get_post($services_id);
$custom_field_services = felan_render_custom_field('service');
$image_src = FELAN_PLUGIN_URL . 'assets/images/bg-video.webp';
if (count($custom_field_services) <= 0) {
    return;
}
?>
<?php foreach ($custom_field_services as $key => $field) { ?>
    <?php switch ($field['type']) {
        case 'text':
            if (!empty($services_meta_data[$field['id']])) { ?>
                <div class="block-archive-inner services-additional-text">
                    <div class="additional-warpper">
                        <h4 class="title-service"><?php echo $field['title']; ?></h4>
                        <div class="content">
                            <?php echo sanitize_text_field($services_meta_data[$field['id']][0]); ?>
                        </div>
                    </div>
                </div>
            <?php }
            break;
        case 'url':
            if (!empty($services_meta_data[$field['id']])) { ?>
                <div class="block-archive-inner services-additional-url">
                    <div class="additional-warpper">
                        <h4 class="title-service"><?php echo $field['title']; ?></h4>
                        <div class="embed-responsive embed-responsive-16by9 embed-responsive-full">
                            <?php echo wp_oembed_get($services_meta_data[$field['id']][0], array('wmode' => 'transparent')); ?>
                        </div>
                    </div>
                </div>
            <?php }
            break;
        case 'textarea':
            if (!empty($services_meta_data[$field['id']])) { ?>
                <div class="block-archive-inner services-additional-textarea">
                    <div class="additional-warpper">
                        <h4 class="title-service"><?php echo $field['title']; ?></h4>
                        <div class="content">
                            <?php echo sanitize_text_field($services_meta_data[$field['id']][0]); ?>
                        </div>
                    </div>
                </div>
            <?php }
            break;
        case 'select':
            if (!empty($services_meta_data[$field['id']])) { ?>
                <div class="block-archive-inner services-additional-select">
                    <div class="additional-warpper">
                        <h4 class="title-service"><?php echo $field['title']; ?></h4>
                        <div class="content">
                            <?php echo sanitize_text_field($services_meta_data[$field['id']][0]); ?>
                        </div>
                    </div>
                </div>
            <?php }
            break;
        case 'checkbox_list':
            if (!empty($services_meta_data[$field['id']])) {
            ?>
                <div class="block-archive-inner services-additional-checkbox_list">
                    <div class="additional-warpper">
                        <h4 class="title-service"><?php echo $field['title']; ?></h4>
                        <div class="content">
                            <?php $services_field = get_post_meta($services_data->ID, $field['id'], true);
                            if (empty($services_field)) {
                                $services_field = array();
                            }
                            foreach ($field['options'] as $opt_value) :
                                if (in_array($opt_value, $services_field)) : ?>
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
            $services_field = get_post_meta($services_data->ID, $field['id'], true);
            if (!empty($services_field['url'])) { ?>
                <div class="block-archive-inner services-additional-image">
                    <div class="additional-warpper">
                        <h4 class="title-service"><?php echo $field['title']; ?></h4>
                        <img src="<?php echo esc_html($services_field['url']); ?>" alt="<?php echo esc_attr($field['title']); ?>" />
                    </div>
                </div>
		<?php }
            break;
        case 'file':
            $services_field = get_post_meta($services_data->ID, $field['id'], true);
            $file_title = $file_url = '';
            if(!empty($services_field)){
                $file_title = get_the_title(intval($services_field));
                $file_url = wp_get_attachment_url(intval($services_field));
            }
            if (!empty($file_url)) { ?>
                <div class="block-archive-inner services-additional-file">
                    <div class="additional-warpper">
                        <h4 class="title-service"><?php echo $field['title']; ?></h4>
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