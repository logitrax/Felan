<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly 
}

global $freelancer_data, $freelancer_meta_data;
$freelancer_video_url = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_video_url']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_video_url'][0] : '';
?>
<div class="freelancer-video block-from">
    <h6><?php esc_html_e('Video', 'felan-framework') ?></h6>

    <div class="row">
        <div class="form-group col-md-12">
            <label for="freelancer-video-url"><?php esc_html_e('Video', 'felan-framework') ?></label>
            <input class="point-mark" type="url" id="freelancer-video-url" name="freelancer_video_url" placeholder="<?php esc_html_e('Enter url video', 'felan-framework') ?>" value="<?php echo esc_attr($freelancer_video_url); ?>">
        </div>
    </div>

</div>