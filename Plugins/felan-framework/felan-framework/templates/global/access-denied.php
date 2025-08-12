<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (empty($type)) {
    return;
}
?>
<div class="access-denied not-login">
    <div class="container">
        <div class="felan-my-page">
            <div class="entry-my-page">
                <?php
                switch ($type) {
                    case 'not_login':
                ?>
                        <div class="account logged-out felan-message alert-success">
                            <div class="icon-message">
                                <i class="far fa-thumbs-up large"></i>
                            </div>

                            <div class="entry-message">
                                <span><?php esc_html_e('You need login to continue.', 'felan-framework'); ?></span>
                                <a href="#popup-form" class="btn-login"><?php esc_html_e('Login Here', 'felan-framework'); ?></a>
                                <span><?php esc_html_e('or', 'felan-framework'); ?></span>
                                <a href="#popup-form" class="btn-register"><?php esc_html_e('Sign Up Now', 'felan-framework'); ?></a>
                            </div>
                        </div>
                    <?php
                        break;

                    case 'warning':
                    ?>
                        <div class="account logged-out felan-message alert-warning">
                            <div class="icon-message">
                                <i class="far fa-exclamation-circle large"></i>
                            </div>

                            <div class="entry-message">
                                <p><?php esc_html_e('You are now a Premium Member.', 'felan-framework'); ?></p>
                            </div>
                        </div>
                    <?php
                        break;

                    case 'error':
                    ?>
                        <div class="account logged-out felan-message alert-error">
                            <div class="icon-message">
                                <i class="far fa-times large"></i>
                            </div>

                            <div class="entry-message">
                                <p><?php esc_html_e('An error occurred. Please try again.', 'felan-framework'); ?></p>
                            </div>
                        </div>
                    <?php
                        break;

                    case 'free_submit':
                    ?>
                        <div class="felan-message alert-warning">
                            <div class="icon-message">
                                <i class="far fa-exclamation-circle large"></i>
                            </div>

                            <div class="entry-message">
                                <p>
                                    <?php global $current_user;
                                    wp_get_current_user();
                                    $user_id = $current_user->ID;
                                    esc_html_e("You are on free submit active", 'felan-framework'); ?>
                                    <?php if (in_array("felan_user_employer", (array)$current_user->roles)) { ?>
                                        <a href="<?php echo felan_get_permalink('jobs_submit'); ?>">
                                            <?php esc_html_e('Add Jobs', 'felan-framework'); ?>
                                        </a>
                                    <?php } else { ?>
                                        <a href="<?php echo felan_get_permalink('submit_service'); ?>">
                                            <?php esc_html_e('Add Service', 'felan-framework'); ?>
                                        </a>
                                    <?php } ?>

                                </p>
                            </div>
                        </div>
                <?php
                        break;

                    default:
                        # code...
                        break;
                }
                ?>

            </div>
        </div>
    </div>
</div>