<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$freelancer_id = get_the_ID();
if(!empty($freelancer_single_id)){
    $freelancer_id = $freelancer_single_id;
}
$content = get_post_field('post_content', $freelancer_id);
if (isset($content) && !empty($content)) : ?>
    <div class="block-archive-inner freelancer-overview-details">
		<?php echo apply_filters('the_content', $content); ?>
    </div>
<?php endif;
