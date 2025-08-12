<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'freelancer-project-proposal');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'freelancer-project-proposal',
    'felan_project_proposal_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
        'not_project' => esc_html__('No project found', 'felan-framework'),
    )
);

global $current_user;
$user_id = $current_user->ID;
$user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
$felan_freelancer = new Felan_freelancer_package();
$check_freelancer_package = $felan_freelancer->user_freelancer_package_available($user_id);
$my_project = felan_get_option('felan_my_project_page_id');
$posts_per_page = 10;
$args = array(
    'post_type' => 'project-proposal',
    'ignore_sticky_posts' => 1,
    'posts_per_page' => $posts_per_page,
    'offset' => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
    'post_status' => 'publish',
    'author' => $user_id,
);
$data = new WP_Query($args);
?>
<div class="felan-project-proposal entry-my-page">
    <div class="search-dashboard-warpper">
        <div class="search-left">
            <div class="select2-field">
                <select class="search-control felan-select2" name="project_status">
                    <option value=""><?php esc_html_e('All status', 'felan-framework') ?></option>
                    <option value="pending"><?php esc_html_e('Pending', 'felan-framework') ?></option>
                    <option value="inprogress"><?php esc_html_e('In Process', 'felan-framework') ?></option>
                    <option value="canceled"><?php esc_html_e('Canceled', 'felan-framework') ?></option>
                    <option value="completed"><?php esc_html_e('Completed', 'felan-framework') ?></option>
                </select>
            </div>
            <div class="action-search">
                <input class="project-search-control" type="text" name="project_search" placeholder="<?php esc_attr_e('Search project title', 'felan-framework') ?>">
                <button class="btn-search">
                    <i class="far fa-search"></i>
                </button>
            </div>
        </div>
        <div class="search-right">
            <label class="text-sorting"><?php esc_html_e('Sort by', 'felan-framework') ?></label>
            <div class="select2-field">
                <select class="search-control action-sorting felan-select2" name="project_sort_by">
                    <option value="newest"><?php esc_html_e('Newest', 'felan-framework') ?></option>
                    <option value="oldest"><?php esc_html_e('Oldest', 'felan-framework') ?></option>
                </select>
            </div>
        </div>
    </div>
    <?php if ($data->have_posts()) { ?>
        <div class="table-dashboard-wapper">
            <table class="table-dashboard <?php if ($check_freelancer_package == -1 || $check_freelancer_package == 0) {
                                                echo 'expired';
                                            } ?>" id="freelancer-project-proposal">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Project Title', 'felan-framework') ?></th>
                        <th><?php esc_html_e('Budget/Time', 'felan-framework') ?></th>
                        <th><?php esc_html_e('Status', 'felan-framework') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($data->have_posts()) : $data->the_post(); ?>
                        <?php
                        $proposal_id = get_the_ID();
                        $project_id = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_project_id', true);
                        $projects_budget_show = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_budget_show', true);
                        $project_categories = get_the_terms($project_id, 'project-categories');
                        $project_location = get_the_terms($project_id, 'felan_location');
                        $thumbnail = get_the_post_thumbnail_url($project_id, '70x70');
                        $project_featured = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_featured', true);
                        $project_select_company = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_select_company', true);
                        $author_id = get_post_field('post_author', $project_id);
                        $author_name = get_the_author_meta('display_name', $author_id);

                        $projects_budget_show = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_budget_show', true);
                        $class_fixed = '';
                        if ($projects_budget_show == 'fixed') {
                            $class_fixed = 'fixed';
                        }

                        $proposal_has_disputes_id = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_has_disputes_id', true);
                        $proposal_status = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_status', true);
                        $proposal_price = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_price', true);
                        $proposal_time = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_time', true);
                        $proposal_fixed_time = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_fixed_time', true);
                        $proposal_rate = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_rate', true);
                        $proposal_maximum_time = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_maximum_time', true);
                        $currency_sign_default = felan_get_option('currency_sign_default');
                        $currency_position = felan_get_option('currency_position');
                        if ($currency_position == 'before') {
                            $proposal_total_price = $currency_sign_default . $proposal_price;
                            $proposal_maximum_hours = $currency_sign_default . $proposal_maximum_time;
                        } else {
                            $proposal_total_price = $proposal_price . $currency_sign_default;
                            $proposal_maximum_hours =  $proposal_maximum_time . $currency_sign_default;
                        }

                        $public_date = get_the_date('Y-m-d');
                        $current_date = date('Y-m-d');
                        $public_timestamp = strtotime($public_date);
                        $current_timestamp = strtotime($current_date);
                        $time_difference = $current_timestamp - $public_timestamp;
                        $months_ago = floor($time_difference / (30 * 24 * 60 * 60));
                        $days_ago = floor($time_difference / (24 * 60 * 60));
                        ?>
                        <tr>
                            <td>
                                <div class="project-header">
                                    <?php if (!empty($thumbnail)) : ?>
                                        <img class="thumbnail" src="<?php echo $thumbnail; ?>" alt="" />
                                    <?php endif; ?>
                                    <div class="content">
                                        <h3 class="title-my-project">
                                            <a href="<?php echo get_the_permalink($project_id) ?>">
                                                <?php echo get_the_title($project_id); ?>
                                                <?php if ($project_featured === '1') : ?>
                                                    <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-featured.svg'); ?>" alt="<?php echo esc_attr('featured', 'felan-framework') ?>">
                                                <?php endif; ?>
                                            </a>
                                        </h3>
                                        <p class="d-flex align-items-center">
                                            <span class="mr-3">
                                                <?php echo sprintf(esc_html__('by %s', 'felan-framework'), $author_name) ?>
                                            </span>
                                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-1">
                                                <path d="M2.25 9C2.25 5.81802 2.25 4.97703 3.23851 3.98851C4.22703 3 5.81802 3 9 3C12.182 3 13.773 3 14.7615 3.98851C15.75 4.97703 15.75 5.81802 15.75 9C15.75 12.182 15.75 13.773 14.7615 14.7615C13.773 15.75 12.182 15.75 9 15.75C5.81802 15.75 4.22703 15.75 3.23851 14.7615C2.25 13.773 2.25 12.182 2.25 9Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M12.375 3.75V2.25" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M5.625 3.75V2.25" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M2.4375 6H15.5625" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <?php if ($months_ago > 0) {
                                                echo esc_html(sprintf(_n('%s month ago', '%s months ago', $months_ago, 'felan-framework'), $months_ago));
                                            } elseif ($days_ago > 0) {
                                                echo esc_html(sprintf(_n('%s day ago', '%s days ago', $days_ago, 'felan-framework'), $days_ago));
                                            } else {
                                                echo esc_html__('Today', 'felan-framework');
                                            } ?>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="price-inner">
                                <p class="price"><?php echo esc_html($proposal_total_price); ?></p>
                                <?php if ($projects_budget_show == 'hourly') : ?>
                                    <p class="maximum-time">
                                        <?php echo esc_html($proposal_time); ?>
                                        <?php echo esc_html__('hours', 'felan-framework'); ?>
                                    </p>
                                <?php else: ?>
                                    <p class="maximum-time"><?php echo sprintf(esc_html__('%1s %2s', 'felan-framework'), $proposal_fixed_time, $proposal_rate) ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="status">
                                <?php felan_project_package_status($proposal_status); ?>
                            </td>
                            <td class="action-order">
                                <?php if ($proposal_status == 'inprogress') : ?>
                                    <a href="<?php echo esc_url(get_page_link($my_project)); ?>?applicants_id=<?php echo esc_attr($proposal_id); ?>&project_id=<?php echo esc_attr($project_id); ?>" class="felan-button">
                                        <?php echo esc_html__('Detail', 'felan-framework') ?>
                                    </a>
                                <?php elseif ($proposal_status == 'canceled') : ?>
                                    <?php if (!empty($proposal_has_disputes_id)) : ?>
                                        <a href="<?php echo esc_url(felan_get_permalink('freelancer_disputes')); ?>?listing=project&order_id=<?php echo esc_attr($proposal_id) ?>&disputes_id=<?php echo esc_attr($proposal_has_disputes_id) ?>"
                                            class="felan-button button-outline-gray btn-dispute">
                                            <?php echo esc_html('View Dispute', 'felan-framework'); ?>
                                        </a>
                                    <?php else: ?>
                                        <?php if ($user_demo == 'yes') { ?>
                                            <a href="#" class="felan-button button-outline-gray btn-add-to-message"
                                                data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>">
                                                <?php echo esc_html__('Delete', 'felan-framework') ?>
                                            </a>
                                        <?php } else { ?>
                                            <a href="#" class="felan-button button-outline-gray btn-delete" item-id="<?php echo esc_attr($proposal_id); ?>">
                                                <?php echo esc_html('Delete', 'felan-framework'); ?>
                                            </a>
                                        <?php } ?>
                                    <?php endif; ?>
                                <?php elseif ($proposal_status == 'reject') : ?>
                                    <?php if ($user_demo == 'yes') { ?>
                                        <a href="#" class="felan-button button-outline-gray btn-add-to-message"
                                            data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>">
                                            <?php echo esc_html__('Delete', 'felan-framework') ?>
                                        </a>
                                    <?php } else { ?>
                                        <a href="#" class="felan-button button-outline-gray btn-delete" item-id="<?php echo esc_attr($proposal_id); ?>">
                                            <?php echo esc_html('Delete', 'felan-framework'); ?>
                                        </a>
                                    <?php } ?>
                                <?php elseif ($proposal_status == 'completed') : ?>
                                    <a href="<?php echo esc_url(get_page_link($my_project)); ?>?applicants_id=<?php echo esc_attr($proposal_id); ?>&project_id=<?php echo esc_attr($project_id); ?>" class="felan-button">
                                        <?php echo esc_html__('Detail', 'felan-framework') ?>
                                    </a>
                                <?php else: ?>
                                    <a href="#form-apply-project" class="felan-button button-outline-gray btn-edit-project btn-edit-proposals <?php echo esc_attr($class_fixed); ?>" id="felan-apply-project"
                                        data-post-current="<?php echo intval($project_id); ?>"
                                        data-proposal-id="<?php echo intval($proposal_id); ?>"
                                        data-author-id="<?php echo intval($user_id); ?>"
                                        data-info-price='<?php echo felan_get_budget_project($project_id); ?>'
                                        data-info-hours="<?php echo esc_attr(felan_project_maximum_time($project_id)); ?>">
                                        <?php esc_html_e('Edit proposals', 'felan-framework') ?>
                                    </a>
                                    <?php if ($user_demo == 'yes') { ?>
                                        <a href="#" class="felan-button button-outline-gray btn-add-to-message"
                                            data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>">
                                            <?php echo esc_html__('Delete', 'felan-framework') ?>
                                        </a>
                                    <?php } else { ?>
                                        <a href="#" class="felan-button button-outline-gray ml-1 btn-delete" item-id="<?php echo esc_attr($proposal_id); ?>">
                                            <?php echo esc_html__('Delete', 'felan-framework'); ?>
                                        </a>
                                    <?php } ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>
        </div>
    <?php } else { ?>
        <div class="item-not-found"><?php esc_html_e('No item found', 'felan-framework'); ?></div>
    <?php } ?>
    <?php $total_post = $data->found_posts;
    if ($total_post > $posts_per_page) { ?>
        <div class="pagination-dashboard pagination-wishlist">
            <?php $max_num_pages = $data->max_num_pages;
            felan_get_template('global/pagination.php', array('total_post' => $total_post, 'max_num_pages' => $max_num_pages, 'type' => 'dashboard', 'layout' => 'number'));
            wp_reset_postdata(); ?>
        </div>
    <?php } ?>
</div>