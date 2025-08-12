<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$user_id = $current_user->ID;
$post_id = get_the_ID();
$author_id = get_post_field('post_author', $post_id);
$check_package_send_message = felan_get_field_check_employer_package('send_message');
?>
<?php if (is_user_logged_in() && in_array('felan_user_employer', (array)$current_user->roles)) { ?>
    <?php if($user_id == $author_id) { ?>
        <a href="#" class="felan-button btn-add-to-message" data-text="<?php echo esc_attr('This feature is not available for the same user ID', 'felan-framework'); ?>">
            <?php esc_html_e('Send Message', 'felan-framework') ?>
        </a>
    <?php } else { ?>
        <?php if ($check_package_send_message == -1 || $check_package_send_message == 0) { ?>
            <a href="#" class="felan-button btn-add-to-message" data-text="<?php echo esc_attr('Package expired. Please select a new one.', 'felan-framework'); ?>">
                <?php esc_html_e('Send Message', 'felan-framework') ?>
            </a>
        <?php } else { ?>
            <a href="#form-messages-popup" class="felan-button" id="felan-add-messages" data-post-current="<?php echo intval($freelancer_id) ?>" data-author-id="<?php echo intval($user_id) ?>">
                <?php esc_html_e('Send Message', 'felan-framework') ?>
            </a>
        <?php } ?>
    <?php } ?>
<?php } else { ?>
    <div class="logged-out">
        <a href="#popup-form" class="felan-button btn-login notice-employer" data-freelancer-id="<?php echo intval($user_id) ?>" data-notice="<?php esc_attr_e('Please login role Employer to view', 'felan-framework') ?>">
            <?php esc_html_e('Send Message', 'felan-framework') ?>
        </a>
    </div>
<?php } ?>