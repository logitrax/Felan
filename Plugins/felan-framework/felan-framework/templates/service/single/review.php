<?php
global $wpdb;

wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'service-review');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'service-review',
    'felan_service_review_vars',
    array(
        'ajax_url'  => FELAN_AJAX_URL,
    )
);
$rating = $total_reviews = $total_stars = 0;
$no_avatar_src = '';
$current_user = wp_get_current_user();
$user_id      = $current_user->ID;
$service_id = get_the_ID();
if (!empty($service_single_id)) {
    $service_id = $service_single_id;
}
$service_rating = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_rating', true);

$comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $service_id AND meta.meta_key = 'service_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 OR comment.user_id = $user_id )";
$get_comments   = $wpdb->get_results($comments_query);
$my_review      = $wpdb->get_row("SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $service_id AND comment.user_id = $user_id AND meta.meta_key = 'service_rating' AND meta.comment_id = comment.comment_ID ORDER BY comment.comment_ID DESC");
$my_review_service_salary_rating = $my_review_service_service_rating  = $my_review_service_skill_rating = $my_review_service_work_rating = '';
$salary_rating_class = $service_rating_class = $skill_rating_class = $work_rating_class = '';

$check_freelancer_package = felan_get_field_check_freelancer_package('review_and_commnent');
$check_employer_package = felan_get_field_check_employer_package('review_and_commnent');

if (!is_null($get_comments)) {

    $service_salary_rating = $service_service_rating = $service_skill_rating = $service_work_rating = array();
    foreach ($get_comments as $comment) {
        if (intval(get_comment_meta($comment->comment_ID, 'service_salary_rating', true)) != 0) {
            $service_salary_rating[]         = intval(get_comment_meta($comment->comment_ID, 'service_salary_rating', true));
        }
        if (intval(get_comment_meta($comment->comment_ID, 'service_service_rating', true)) != 0) {
            $service_service_rating[]         = intval(get_comment_meta($comment->comment_ID, 'service_service_rating', true));
        }
        if (intval(get_comment_meta($comment->comment_ID, 'service_skill_rating', true)) != 0) {
            $service_skill_rating[]         = intval(get_comment_meta($comment->comment_ID, 'service_skill_rating', true));
        }
        if (intval(get_comment_meta($comment->comment_ID, 'service_work_rating', true)) != 0) {
            $service_work_rating[]         = intval(get_comment_meta($comment->comment_ID, 'service_work_rating', true));
        }

        if ($comment->comment_approved == 1) {
            if (!empty($comment->meta_value) && $comment->meta_value != 0.00) {
                $total_reviews++;
            }
            if ($comment->meta_value > 0) {
                $total_stars += $comment->meta_value;
            }
        }

        if (isset($my_review) ? $comment->comment_ID : 0) {
            if ($comment->comment_ID == $my_review->comment_ID) {
                $my_review_service_salary_rating = intval(get_comment_meta($comment->comment_ID, 'service_salary_rating', true));
                $my_review_service_service_rating = intval(get_comment_meta($comment->comment_ID, 'service_service_rating', true));
                $my_review_service_skill_rating = intval(get_comment_meta($comment->comment_ID, 'service_skill_rating', true));
                $my_review_service_work_rating = intval(get_comment_meta($comment->comment_ID, 'service_work_rating', true));
            }
        }
    }

    if ($total_reviews != 0) {
        $rating = number_format($total_stars / $total_reviews, 1);
    }

    if (!empty($service_salary_rating)) {
        $salary_rating = array_sum($service_salary_rating) / count($service_salary_rating);
        $salary_rating = number_format((float)$salary_rating, 2, '.', '');
        $salary_rating_percent = ($salary_rating / 5) * 100;
        if ($salary_rating_percent >= 0 && $salary_rating_percent <= 30) {
            $salary_rating_class = 'low';
        } else if ($salary_rating_percent >= 31 && $salary_rating_percent <= 70) {
            $salary_rating_class = 'mid';
        } else if ($salary_rating_percent >= 71 && $salary_rating_percent <= 100) {
            $salary_rating_class = 'high';
        }
    } else {
        $salary_rating = 0;
        $salary_rating_percent = 0;
    }

    if (!empty($service_service_rating)) {
        $service_rating = array_sum($service_service_rating) / count($service_service_rating);
        $service_rating = number_format((float)$service_rating, 2, '.', '');
        $service_rating_percent = ($service_rating / 5) * 100;
        if ($service_rating_percent >= 0 && $service_rating_percent <= 30) {
            $service_rating_class = 'low';
        } else if ($service_rating_percent >= 31 && $service_rating_percent <= 70) {
            $service_rating_class = 'mid';
        } else if ($service_rating_percent >= 71 && $service_rating_percent <= 100) {
            $service_rating_class = 'high';
        }
    } else {
        $service_rating = 0;
        $service_rating_percent = 0;
    }

    if (!empty($service_skill_rating)) {
        $skill_rating = array_sum($service_skill_rating) / count($service_skill_rating);
        $skill_rating = number_format((float)$skill_rating, 2, '.', '');
        $skill_rating_percent = ($skill_rating / 5) * 100;
        if ($skill_rating_percent >= 0 && $skill_rating_percent <= 30) {
            $skill_rating_class = 'low';
        } else if ($skill_rating_percent >= 31 && $skill_rating_percent <= 70) {
            $skill_rating_class = 'mid';
        } else if ($skill_rating_percent >= 71 && $skill_rating_percent <= 100) {
            $skill_rating_class = 'high';
        }
    } else {
        $skill_rating = 0;
        $skill_rating_percent = 0;
    }

    if (!empty($service_work_rating)) {
        $work_rating = array_sum($service_work_rating) / count($service_work_rating);
        $work_rating = number_format((float)$work_rating, 2, '.', '');
        $work_rating_percent = ($work_rating / 5) * 100;
        if ($work_rating_percent >= 0 && $work_rating_percent <= 30) {
            $work_rating_class = 'low';
        } else if ($work_rating_percent >= 31 && $work_rating_percent <= 70) {
            $work_rating_class = 'mid';
        } else if ($work_rating_percent >= 71 && $work_rating_percent <= 100) {
            $work_rating_class = 'high';
        }
    } else {
        $work_rating = 0;
        $work_rating_percent = 0;
    }
}

?>
<div class="felan-block-inner block-archive-inner service-review-details" id="service-review-details">
    <h3 class="title-service"><?php esc_html_e('Review', 'felan-framework'); ?></h3>
    <div class="entry-heading">
        <span class="rating-count">
            <span><?php esc_html_e($rating); ?></span>
            <i class="fas fa-star"></i>
        </span>
        <span class="review-count"><?php printf(_n('Base on %s reviews', 'Base on %s reviews', $total_reviews, 'felan-framework'), $total_reviews); ?></span>
    </div>
    <div class="entry-overview">
        <div class="rating-bars">
            <div class="rating-bars-item">
                <div class="rating-bars-name">
                    <?php esc_html_e('Salary & Benefits', 'felan-framework'); ?>
                    <div class="tip" data-tip-content="<?php esc_html_e('Salary review every 6 months based on the work performance', 'felan-framework'); ?>">
                        <div class="tip-content"><?php esc_html_e('Salary review every 6 months based on the work performance', 'felan-framework'); ?></div>
                    </div>
                </div>
                <div class="rating-bars-inner">
                    <span class="rating-bars-rating <?php echo esc_attr($salary_rating_class); ?>" data-rating="<?php echo esc_attr($salary_rating); ?>">
                        <span class="rating-bars-rating-inner" style="width: <?php echo esc_attr($salary_rating_percent); ?>%;"></span>
                    </span>
                    <span class="value-rating"><?php echo esc_attr($salary_rating); ?></span>
                </div>
            </div>
            <div class="rating-bars-item">
                <div class="rating-bars-name">
                    <?php esc_html_e('Work Speed', 'felan-framework'); ?>
                    <div class="tip" data-tip-content="<?php esc_html_e('Does the speed of project completion meet the deadline?', 'felan-framework'); ?>">
                        <div class="tip-content"><?php esc_html_e('Does the speed of project completion meet the deadline?', 'felan-framework'); ?></div>
                    </div>
                </div>
                <div class="rating-bars-inner">
                    <span class="rating-bars-rating <?php echo esc_attr($service_rating_class); ?>" data-rating="<?php echo esc_attr($service_rating); ?>">
                        <span class="rating-bars-rating-inner" style="width: <?php echo esc_attr($service_rating_percent); ?>%;"></span>
                    </span>
                    <span class="value-rating"><?php echo esc_attr($service_rating); ?></span>
                </div>
            </div>
            <div class="rating-bars-item">
                <div class="rating-bars-name">
                    <?php esc_html_e('Skill Development', 'felan-framework'); ?>
                    <div class="tip" data-tip-content="<?php esc_html_e('Well trained and dedicated to being able to catch the pace smoothly.', 'felan-framework'); ?>">
                        <div class="tip-content"><?php esc_html_e('Well trained and dedicated to being able to catch the pace smoothly.', 'felan-framework'); ?></div>
                    </div>
                </div>
                <span class="rating-bars-inner">
                    <span class="rating-bars-rating <?php echo esc_attr($skill_rating_class); ?>" data-rating="<?php echo esc_attr($skill_rating); ?>">
                        <span class="rating-bars-rating-inner" style="width: <?php echo esc_attr($skill_rating_percent); ?>%;"></span>
                    </span>
                    <span class="value-rating"><?php echo esc_attr($skill_rating); ?></span>
                </span>
            </div>
            <div class="rating-bars-item">
                <div class="rating-bars-name">
                    <?php esc_html_e('Work Satisfaction', 'felan-framework'); ?>
                    <div class="tip" data-tip-content="<?php esc_html_e('Our office is located with creative, open workspaces and a high-quality engaging environment.', 'felan-framework'); ?>">
                        <div class="tip-content"><?php esc_html_e('Our office is located with creative, open workspaces and a high-quality engaging environment.', 'felan-framework'); ?></div>
                    </div>
                </div>
                <div class="rating-bars-inner">
                    <span class="rating-bars-rating <?php echo esc_attr($work_rating_class); ?>" data-rating="<?php echo esc_attr($work_rating); ?>">
                        <span class="rating-bars-rating-inner" style="width: <?php echo esc_attr($work_rating_percent); ?>%;"></span>
                    </span>
                    <span class="value-rating"><?php echo esc_attr($work_rating); ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="entry-detail">
        <ul class="reviews-list">
            <?php if (!is_null($get_comments)) {
                foreach ($get_comments as $comment) {
                    $comment_id        = $comment->comment_ID;
                    $author_avatar_url = get_avatar_url($comment->user_id, ['size' => '50']);
                    $author_avatar_image_url = get_the_author_meta('author_avatar_image_url', $comment->user_id);
                    if (!empty($author_avatar_image_url)) {
                        $author_avatar_url = $author_avatar_image_url;
                    }
                    $user_link = get_author_posts_url($comment->user_id);

                    $comment_thumb = get_comment_meta($comment->comment_ID, 'comment_thumb', true);

            ?>
                    <li class="author-review">
                        <div class="entry-head">
                            <div class="entry-avatar">
                                <figure>
                                    <?php
                                    if (!empty($author_avatar_url)) {
                                    ?>
                                        <a href="<?php echo esc_url($user_link); ?>">
                                            <img src="<?php echo esc_url($author_avatar_url); ?>" alt="<?php the_author_meta('display_name', $comment->user_id); ?>">
                                        </a>
                                    <?php
                                    } else {
                                    ?>
                                        <a href="<?php echo esc_url($user_link); ?>">
                                            <img src="<?php echo esc_url($no_avatar_src); ?>" alt="<?php the_author_meta('display_name', $comment->user_id); ?>"></a>
                                    <?php
                                    }
                                    ?>
                                </figure>
                            </div>
                            <div class="entry-info">
                                <div class="entry-name">
                                    <h4 class="author-name"><a href="<?php echo esc_url($user_link); ?>"><?php the_author_meta('display_name', $comment->user_id); ?></a></h4>
                                    <span class="review-date"><?php echo felan_get_comment_time($comment->comment_ID); ?></span>
                                </div>
                                <?php if ($comment->meta_value > 0) : ?>
                                    <div class="author-rating">
                                        <span class="star <?php if ($comment->meta_value >= 1) : echo 'checked';
                                                            endif; ?>">
                                            <i class="fas fa-star"></i>
                                        </span>
                                        <span class="star <?php if ($comment->meta_value >= 2) : echo 'checked';
                                                            endif; ?>">
                                            <i class="fas fa-star"></i>
                                        </span>
                                        <span class="star <?php if ($comment->meta_value >= 3) : echo 'checked';
                                                            endif; ?>">
                                            <i class="fas fa-star"></i>
                                        </span>
                                        <span class="star <?php if ($comment->meta_value >= 4) : echo 'checked';
                                                            endif; ?>">
                                            <i class="fas fa-star"></i>
                                        </span>
                                        <span class="star <?php if ($comment->meta_value == 5) : echo 'checked';
                                                            endif; ?>">
                                            <i class="fas fa-star"></i>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="entry-comment felan-review-details">
                            <p class="review-content felan-review"><?php echo wp_kses_post($comment->comment_content); ?></p>
                            <div class="toggle-review">
                                <a href="#" class="show-more-review"><?php esc_html_e('Show more', 'felan-framework'); ?><i class="far fa-angle-down"></i></a>
                                <a href="#" class="hide-all-review"><?php esc_html_e('Hide less', 'felan-framework'); ?><i class="far fa-angle-up"></i></a>
                            </div>
                            <?php
                            if ($comment_thumb) :
                            ?>
                                <ul>
                                    <?php
                                    foreach ($comment_thumb as $key => $value) :
                                        $image_attributes = wp_get_attachment_image_src($value, 'full');
                                    ?>
                                        <li><a href="<?php echo $image_attributes[0]; ?>" target="_Blank"><img src="<?php echo $image_attributes[0]; ?>" /></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>

                        <?php if (is_user_logged_in()) { ?>
                            <div class="entry-nav">
                                <div class="reply">
                                    <a href="#">
                                        <i class="far fa-comment-alt-lines medium"></i>
                                        <span><?php esc_html_e('Reply', 'felan-framework'); ?></span>
                                    </a>
                                </div>

                                <?php if ($comment->comment_approved == 0) { ?>
                                    <span class="waiting-for-approval"> <?php esc_html_e('Waiting for approval', 'felan-framework'); ?> </span>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <?php
                        $args = array(
                            'status' => 'approve',
                            'number' => '',
                            'order'  => 'ASC',
                            'parent' => $comment->comment_ID
                        );
                        $child_comments = get_comments($args);
                        ?>
                        <?php if ($child_comments) : ?>
                            <ol class="children">
                                <?php foreach ($child_comments as $child_comment) { ?>
                                    <?php
                                    $child_avatar_url       = get_avatar_url($child_comment->user_id, ['size' => '50']);
                                    $child_link             = get_author_posts_url($child_comment->user_id);
                                    $child_avatar_image_url = get_the_author_meta('author_avatar_image_url', $child_comment->user_id);
                                    if (isset($child_avatar_image_url)) {
                                        $child_avatar_url = $child_avatar_image_url;
                                    }
                                    if (empty($child_avatar_url)) {
                                        $child_avatar_url = FELAN_PLUGIN_URL . 'assets/images/default-user-image.png';
                                    }
                                    ?>
                                    <li class="author-review">
                                        <div class="entry-head">
                                            <div class="entry-avatar">
                                                <figure>
                                                    <?php
                                                    if (!empty($child_avatar_url)) {
                                                    ?>
                                                        <a href="<?php echo esc_url($child_link); ?>">
                                                            <img src="<?php echo esc_url($child_avatar_url); ?>" alt="<?php the_author_meta('display_name', $comment->user_id); ?>">
                                                        </a>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <a href="<?php echo esc_url($child_link); ?>">
                                                            <img src="<?php echo esc_url($no_avatar_src); ?>" alt="<?php the_author_meta('display_name', $comment->user_id); ?>"></a>
                                                    <?php
                                                    }
                                                    ?>
                                                </figure>
                                            </div>
                                            <div class="entry-info">
                                                <div class="entry-name">
                                                    <h4 class="author-name"><a href="<?php echo esc_url($child_link); ?>"><?php the_author_meta('display_name', $child_comment->user_id); ?></a></h4>
                                                </div>
                                                <span class="review-date"><?php echo felan_get_comment_time($child_comment->comment_ID); ?></span>
                                            </div>
                                        </div>

                                        <div class="entry-comment felan-review-details">
                                            <p class="review-content felan-review"><?php esc_html_e($child_comment->comment_content); ?></p>
                                            <div class="toggle-review">
                                                <a href="#" class="show-more-review"><?php esc_html_e('Show more', 'felan-framework'); ?><i class="far fa-angle-down"></i></a>
                                                <a href="#" class="hide-all-review"><?php esc_html_e('Hide less', 'felan-framework'); ?><i class="far fa-angle-up"></i></a>
                                            </div>
                                        </div>

                                        <?php if ($child_comment->comment_approved == 0) { ?>
                                            <div class="entry-nav">
                                                <span class="waiting-for-approval"> <?php esc_html_e('Waiting for approval', 'felan-framework'); ?> </span>
                                            </div>
                                        <?php } ?>
                                    </li>
                                <?php } ?>
                            </ol>
                        <?php endif; ?>

                        <div class="form-reply" data-id="<?php echo esc_attr($comment->comment_ID); ?>"></div>
                    </li>
            <?php
                }
            }
            ?>
        </ul>
    </div>

    <div class="duplicate-form-reply hide none">
        <div class="entry-head">
            <h4 class="review-title"><?php esc_html_e('Reply', 'felan-framework'); ?></h4>
            <a href="#" class="cancel-reply">
                <i class="far fa-times"></i>
                <span><?php esc_html_e('Cancel reply', 'felan-framework'); ?></span>
            </a>
        </div>
        <?php
        $current_user = wp_get_current_user();
        $user_name    = $current_user->display_name;
        $avatar_url   = get_avatar_url($current_user->ID);
        $author_avatar_image_url = get_the_author_meta('author_avatar_image_url', $current_user->ID);
        if (!empty($author_avatar_image_url)) {
            $avatar_url = $author_avatar_image_url;
        }
        ?>
        <form method="post" class="repreviewForm" action="#">
            <div class="form-group custom-area">
                <textarea class="form-control" rows="5" name="message" placeholder="<?php esc_attr_e('Add a comment...', 'felan-framework'); ?>"></textarea>
            </div>
            <button type="submit" class="felan-submit-service-reply felan-button"><?php esc_html_e('Send', 'felan-framework'); ?></button>
            <?php wp_nonce_field('felan_submit_reply_ajax_nonce', 'felan_security_submit_reply'); ?>
            <input type="hidden" name="action" value="felan_service_submit_reply_ajax">
            <input type="hidden" name="service_id" value="<?php the_ID(); ?>">
            <input type="hidden" name="comment_id" value="">
        </form>
    </div>
</div>