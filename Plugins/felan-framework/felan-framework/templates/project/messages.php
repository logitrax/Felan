<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$user_id = $current_user->ID;
$post_id = get_the_ID();
$author_id = get_post_field('post_author', $post_id);
$check_package = felan_get_field_check_freelancer_package('send_message');
?>
<?php if (is_user_logged_in() && in_array('felan_user_freelancer', (array)$current_user->roles)) { ?>
    <?php if($user_id == $author_id) { ?>
        <a href="#" class="felan-button btn-add-to-message felan-send-mess" data-text="<?php echo esc_attr('This feature is not available for the same user ID', 'felan-framework'); ?>">
            <?php esc_html_e('Send message', 'felan-framework'); ?>
        </a>
    <?php } else { ?>
        <?php if ($check_package == -1 || $check_package == 0) { ?>
            <a href="#" class="felan-button btn-add-to-message felan-send-mess" data-text="<?php echo esc_attr('Package expired. Please select a new one.', 'felan-framework'); ?>">
                <?php esc_html_e('Send message', 'felan-framework'); ?>
            </a>
        <?php } else { ?>
            <a href="#form-messages-popup" class="felan-button felan-send-mess" id="felan-add-messages" data-post-current="<?php echo intval($post_id) ?>" data-author-id="<?php echo intval($user_id) ?>">
                <?php esc_html_e('Send message', 'felan-framework') ?>
            </a>
        <?php } ?>
    <?php } ?>
<?php } else { ?>
    <div class="logged-out">
        <a href="#popup-form" class="felan-button btn-login felan-send-mess">
            <?php esc_html_e('Send message', 'felan-framework') ?>
        </a>
    </div>
<?php } ?>