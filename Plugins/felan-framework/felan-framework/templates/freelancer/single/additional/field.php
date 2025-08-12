<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_style('lity');
wp_enqueue_script('lity');
$freelancer_id = get_the_ID();
$freelancer_meta_data = get_post_custom($freelancer_id);
$freelancer_data = get_post($freelancer_id);
$custom_field_freelancer = felan_render_custom_field('freelancer');
$image_src = FELAN_PLUGIN_URL . 'assets/images/bg-video.webp';
if (count($custom_field_freelancer) <= 0) {
    return;
}
?>
<?php switch ($field['type']) {
    case 'text':
        if (!empty($freelancer_meta_data[$field['id']])) { ?>
            <div class="additional-warpper">
                <h4 class="title-freelancer"><?php echo $field['title']; ?></h4>
                <div class="content">
                    <?php echo sanitize_text_field($freelancer_meta_data[$field['id']][0]); ?>
                </div>
            </div>
        <?php }
        break;
    case 'url':
        if (!empty($freelancer_meta_data[$field['id']])) { ?>
            <div class="additional-warpper">
                <h4 class="title-freelancer"><?php echo $field['title']; ?></h4>
                <div class="embed-responsive embed-responsive-16by9 embed-responsive-full">
                    <?php echo wp_oembed_get($freelancer_meta_data[$field['id']][0], array('wmode' => 'transparent')); ?>
                </div>
            </div>
        <?php }
        break;
    case 'textarea':
        if (!empty($freelancer_meta_data[$field['id']])) { ?>
            <div class="additional-warpper">
                <h4 class="title-freelancer"><?php echo $field['title']; ?></h4>
                <div class="content">
                    <?php echo sanitize_text_field($freelancer_meta_data[$field['id']][0]); ?>
                </div>
            </div>
        <?php }
        break;
    case 'select':
        if (!empty($freelancer_meta_data[$field['id']])) { ?>
            <div class="additional-warpper">
                <h4 class="title-freelancer"><?php echo $field['title']; ?></h4>
                <div class="content">
                    <?php echo sanitize_text_field($freelancer_meta_data[$field['id']][0]); ?>
                </div>
            </div>
        <?php }
        break;
    case 'checkbox_list':
        if (!empty($freelancer_meta_data[$field['id']])) {
        ?>
            <div class="additional-warpper">
                <h4 class="title-freelancer"><?php echo $field['title']; ?></h4>
                <div class="content">
                    <?php $freelancer_field = get_post_meta($freelancer_data->ID, $field['id'], true);
                    if (empty($freelancer_field)) {
                        $freelancer_field = array();
                    }
                    foreach ($field['options'] as $opt_value) :
                        if (in_array($opt_value, $freelancer_field)) : ?>
                            <div class="label label-skills"><?php esc_html_e($opt_value); ?></div>
                    <?php endif;
                    endforeach;
                    ?>
                </div>
            </div>
        <?php }
        break;
    case 'image':
        $freelancer_field = get_post_meta($freelancer_data->ID, $field['id'], true);
        if (!empty($freelancer_field['url'])) { ?>
            <div class="additional-warpper">
                <h4 class="title-freelancer"><?php echo $field['title']; ?></h4>
                <img src="<?php echo esc_html($freelancer_field['url']); ?>" alt="<?php echo esc_attr($field['title']); ?>" />
            </div>
<?php }
        break;
    case 'file':
        $freelancer_field = get_post_meta($freelancer_data->ID, $field['id'], true);
        $file_title = $file_url = '';
        if(!empty($freelancer_field)){
            $file_title = get_the_title(intval($freelancer_field));
            $file_url = wp_get_attachment_url(intval($freelancer_field));
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
