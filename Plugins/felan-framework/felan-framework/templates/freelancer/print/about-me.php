<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$content = get_post_field('post_content', $freelancer_id);
if (isset($content) && !empty($content)) : ?>
    <div class="block-archive-inner freelancer-overview-details">
        <h4 class="title-freelancer"><?php esc_html_e('About me', 'felan-framework') ?></h4>
        <?php echo $content; ?>
    </div>
<?php endif;
