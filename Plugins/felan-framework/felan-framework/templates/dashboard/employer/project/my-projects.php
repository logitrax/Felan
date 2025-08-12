<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!is_user_logged_in()) {
    felan_get_template('global/access-denied.php', array('type' => 'not_login'));
    return;
}
global $current_user;
$user_id = $current_user->ID;
$id = get_the_ID();
$project_id = isset($_GET['project_id']) ? felan_clean(wp_unslash($_GET['project_id'])) : '';
$applicants_id = isset($_GET['applicants_id']) ? felan_clean(wp_unslash($_GET['applicants_id'])) : '';
$pages = isset($_GET['pages']) ? felan_clean(wp_unslash($_GET['pages'])) : '';
$current_date = date('Y-m-d');
$felan_package = new Felan_Package();
$package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'package_id', $user_id);
$expired_date = $felan_package->get_expired_date($package_id, $user_id);
$paid_submission_type = felan_get_option('paid_submission_type', 'no');


if (!empty($project_id) && $pages == 'edit') {
    $project_author_id = get_post_field('post_author', $project_id);
    if ($user_id == $project_author_id) {
        felan_get_template('project/edit.php');
    } else { ?>
        <p class="notice"><i class="far fa-exclamation-circle"></i><?php esc_html_e(
                "You do not have permission to access this page",
                "felan-framework"
            ); ?></p>
    <?php }
} else {
    $posts_per_page = 10;
    wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'my-project');
    wp_localize_script(
        FELAN_PLUGIN_PREFIX . 'my-project',
        'felan_project_dashboard_vars',
        array(
            'ajax_url'    => FELAN_AJAX_URL,
            'not_project'   => esc_html__('No project found', 'felan-framework'),
        )
    );
    $project_classes = array('felan-project', 'grid', 'columns-4');
    $tax_query = $meta_query = array();
    global $current_user;
    wp_get_current_user();
    $user_id = $current_user->ID;
    $felan_profile = new Felan_Profile();

    $args = array(
        'post_type'           => 'project',
        'post_status'         => array('publish', 'expired', 'pending', 'pause'),
        'ignore_sticky_posts' => 1,
        'posts_per_page'      => $posts_per_page,
        'offset'              => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
        'author'              => $user_id,
        'orderby'               => 'date',
    );
    $data = new WP_Query($args);
?>
    <?php if ($current_date >= $expired_date && $paid_submission_type == 'per_package') : ?>
        <p class="notice"><i class="far fa-exclamation-circle"></i>
            <?php esc_html_e("Package expired. Please select a new one.", 'felan-framework'); ?>
            <a href="<?php echo felan_get_permalink('package'); ?>">
                <?php esc_html_e('Add Package', 'felan-framework'); ?>
            </a>
        </p>
    <?php endif; ?>
    <div class="entry-my-page project-dashboard <?php if ($current_date >= $expired_date && $paid_submission_type == 'per_package') {
                                                    echo 'expired';
                                                } ?>"">
        <div class=" search-dashboard-warpper">
        <div class="search-left">
            <div class="select2-field">
                <select class="search-control felan-select2" name="project_status">
                    <option value=""><?php esc_html_e('All projects', 'felan-framework') ?></option>
                    <option value="publish"><?php esc_html_e('Opening', 'felan-framework') ?></option>
                    <option value="pause"><?php esc_html_e('Paused', 'felan-framework') ?></option>
                    <option value="expired"><?php esc_html_e('Closed', 'felan-framework') ?></option>
                    <option value="pending"><?php esc_html_e('Pending', 'felan-framework') ?></option>
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
                    <option value="featured"><?php esc_html_e('Featured', 'felan-framework') ?></option>
                </select>
            </div>
        </div>
    </div>
    <?php if ($data->have_posts()) { ?>
        <div class="table-dashboard-wapper">
            <table class="table-dashboard" id="my-project">
                <thead>
                    <tr>
                        <th><?php esc_html_e('TITLE', 'felan-framework') ?></th>
                        <th><?php esc_html_e('POSTED', 'felan-framework') ?></th>
                        <th><?php esc_html_e('PRICE', 'felan-framework') ?></th>
                        <th><?php esc_html_e('STATUS', 'felan-framework') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $ids = $project_expires = array(); ?>
                    <?php while ($data->have_posts()) : $data->the_post(); ?>
                        <?php
                        $id = get_the_ID();
                        $ids[] = $id;
                        global $current_user;
                        wp_get_current_user();
                        $user_id = $current_user->ID;
                        $status = get_post_status($id);
                        $project_categories =  get_the_terms($id, 'project-categories');
                        $public_date = get_the_date('Y-m-d');
                        $current_date = date('Y-m-d');
                        $project_featured    = get_post_meta($id, FELAN_METABOX_PREFIX . 'project_featured', true);
                        $val_public_date = get_the_date(get_option('date_format'));
                        $thumbnail_id = get_post_thumbnail_id();
                        $thumbnail_url = !empty($thumbnail_id) ? wp_get_attachment_image_src($thumbnail_id, 'full') : false;
                        $projects_budget_show = get_post_meta($id, FELAN_METABOX_PREFIX . 'project_budget_show', true);

                        $withdraw_price = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'employer_withdraw_total_price', true);
                        $wallet_fee_amount = felan_get_option('wallet_featured_project_fee_amount','');
                        $enable_wallet_mode = felan_get_option('enable_wallet_mode','0');
                        ?>
                        <tr>
                            <td>
                                <div class="project-thumbnail-inner">
                                    <?php if ($thumbnail_url) : ?>
                                        <div class="project-thumbnail">
                                            <img src="<?php echo $thumbnail_url[0]; ?>" alt="<?php the_title(); ?>">
                                        </div>
                                    <?php endif; ?>
                                    <div class="content-project">
                                        <h3 class="title-project-dashboard">
                                            <a href="<?php echo get_the_permalink($id); ?>" target="_blank">
                                                <?php echo get_the_title($id); ?>
                                                <?php if ($project_featured == '1') : ?>
                                                    <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-featured.svg'); ?>" alt="<?php echo esc_attr('featured', 'felan-framework') ?>">
                                                <?php endif; ?>
                                            </a>
                                        </h3>
                                        <p>
                                            <span><?php echo esc_html__('in', 'felan-framework'); ?></span>
                                            <?php if (is_array($project_categories)) {
                                                foreach ($project_categories as $categories) {
                                                    $categories_link = get_term_link($categories, 'project-categories'); ?>
                                                    <a href="<?php echo esc_url($categories_link); ?>" class="cate">
                                                        <?php esc_html_e($categories->name); ?>
                                                    </a>
                                                <?php }
                                            } ?>
                                        </p>
                                    </div>
                                </div>
                                 <?php if (felan_total_applications_project_id($id) > 0) { ?>
									<?php
									$proposals_dashboard = felan_get_option('felan_proposal_page_id');
									$proposals_dashboard_url = get_permalink( $proposals_dashboard );
									$proposals_page      = add_query_arg(
										[
											'project_id' => $id,
										],
										$proposals_dashboard_url
									);
									?>
                                     <a href="<?php echo esc_url($proposals_page); ?>" class="project-proposal-number-applicant">
                                        <span class="number"><?php echo felan_total_applications_project_id($id); ?></span>
                                        <?php if (felan_total_applications_project_id($id) > 1) { ?>
                                            <span><?php echo esc_html__('Proposals', 'felan-framework') ?></span>
                                        <?php } else { ?>
                                            <span><?php echo esc_html__('Proposal', 'felan-framework') ?></span>
                                        <?php } ?>
                                    </a>
                                 <?php } else { ?>
                                     <span class="project-number-applicant">
                                         <span class="number"><?php echo felan_total_applications_project_id($id); ?></span>
                                         <?php if (felan_total_applications_project_id($id) > 1) { ?>
                                             <span><?php echo esc_html__('Proposals', 'felan-framework') ?></span>
                                         <?php } else { ?>
                                             <span><?php echo esc_html__('Proposal', 'felan-framework') ?></span>
                                         <?php } ?>
                                     </span>
                                 <?php } ?>
                            </td>
                            <td>
                                <span class="start-time"><?php echo $val_public_date ?></span>
                            </td>
                            <td class="price">
                                <?php echo felan_get_budget_project($id); ?>
                                <p class="budget-show">
                                    <?php if($projects_budget_show == 'hourly') : ?>
                                        <?php echo esc_html__('Hourly Rate', 'felan-framework'); ?>
                                    <?php else: ?>
                                        <?php echo esc_html__('Fixed Price', 'felan-framework'); ?>
                                    <?php endif; ?>
                                </p>
                            </td>
                            <td>
                                <?php if ($status == 'expired') : ?>
                                    <span class="label label-close"><?php esc_html_e('Closed', 'felan-framework') ?></span>
                                <?php endif; ?>
                                <?php if ($status == 'publish') : ?>
                                    <span class="label label-open"><?php esc_html_e('Opening', 'felan-framework') ?></span>
                                <?php endif; ?>
                                <?php if ($status == 'pending') : ?>
                                    <span class="label label-pending"><?php esc_html_e('Pending', 'felan-framework') ?></span>
                                <?php endif; ?>
                                <?php if ($status == 'pause') : ?>
                                    <span class="label label-pause"><?php esc_html_e('Pause', 'felan-framework') ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="action-setting project-control">
                                <?php if ($status !== 'expired') : ?>
                                    <a href="#" class="icon-setting"><i class="far fa-ellipsis-h"></i></a>
                                    <ul class="action-dropdown">
                                        <?php
                                        $project_dashboard_link = felan_get_permalink('project_dashboard');
                                        $paid_submission_type = felan_get_option('paid_submission_type', 'no');
                                        $check_package = $felan_profile->user_package_available($user_id);
                                        $package_num_featured_project = get_the_author_meta(FELAN_METABOX_PREFIX . 'package_number_project_featured', $user_id);
                                        $package_unlimited_featured_project = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_project_featured', true);
                                        $user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
                                        switch ($status) {
                                            case 'publish':
                                            if ($paid_submission_type == 'per_package') { ?>
                                                    <li><a class="btn-edit" href="<?php echo esc_url($project_dashboard_link); ?><?php echo strpos(esc_url($project_dashboard_link), '?') ? '&' : '?' ?>pages=edit&project_id=<?php echo esc_attr($id); ?>"><?php esc_html_e('Edit', 'felan-framework'); ?></a></li>
                                                    <?php if ($user_demo == 'yes') { ?>

                                                        <li><a class="btn-add-to-message" href="#" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>"><?php esc_html_e('Paused', 'felan-framework'); ?></a></li>
                                                        <?php if ($project_featured != 1) { ?>
                                                            <li><a class="btn-add-to-message" href="#" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>"><?php esc_html_e('Mark featured', 'felan-framework'); ?></a></li>
                                                        <?php } ?>
                                                        <li><a class="btn-add-to-message" href="#" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>"><?php esc_html_e('Mark Filled', 'felan-framework'); ?></a></li>

                                                        <?php } else {

                                                        if ($check_package != -1 && $check_package != 0) { ?>
                                                            <li><a class="btn-pause" project-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Paused', 'felan-framework') ?></a></li>
                                                        <?php } ?>

                                                        <?php if($enable_wallet_mode == '1'){
                                                            if(intval($withdraw_price) > intval($wallet_fee_amount)){ ?>
                                                                <?php if ($project_featured != 1) { ?>
                                                                    <li><a class="btn-mark-featured" project-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Mark featured', 'felan-framework') ?></a></li>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                                <?php if ($project_featured !== 1) { ?>
                                                                    <li>
                                                                        <a class="btn-add-to-message" href="#"
                                                                           data-text="<?php echo esc_attr('Your wallet balance is currently insufficient.', 'felan-framework'); ?>">
                                                                            <?php esc_html_e('Mark featured', 'felan-framework') ?>
                                                                        </a>
                                                                    </li>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            <?php if (($package_unlimited_featured_project == '1' || $package_num_featured_project > 0) && $project_featured != 1 && $check_package != -1  && $check_package != 0) { ?>
                                                                <li><a class="btn-mark-featured" project-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Mark featured', 'felan-framework') ?></a></li>
                                                            <?php } ?>
                                                        <?php } ?>

                                                        <?php if ($check_package != -1 && $check_package != 0) { ?>
                                                            <li><a class="btn-mark-filled" project-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Mark Filled', 'felan-framework') ?></a></li>
                                                        <?php }
                                                    }

                                                    if ($check_package != -1 && $check_package != 0) { ?>
                                                        <li><a href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('View detail', 'felan-framework') ?></a></li>
                                                    <?php }
                                                } else { ?>
                                                    <li><a class="btn-edit" href="<?php echo esc_url($project_dashboard_link); ?>?pages=edit&project_id=<?php echo esc_attr($id); ?>"><?php esc_html_e('Edit', 'felan-framework'); ?></a></li>

                                                    <?php if ($user_demo == 'yes') { ?>
                                                        <li><a class="btn-add-to-message" href="#" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>"><?php esc_html_e('Paused', 'felan-framework'); ?></a></li>
                                                        <?php if ($project_featured != 1) { ?>
                                                            <li><a class="btn-add-to-message" href="#" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>"><?php esc_html_e('Mark featured', 'felan-framework'); ?></a></li>
                                                        <?php } ?>
                                                        <li><a class="btn-add-to-message" href="#" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>"><?php esc_html_e('Mark Filled', 'felan-framework'); ?></a></li>
                                                    <?php } else { ?>
                                                        <li><a class="btn-pause" project-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Paused', 'felan-framework') ?></a></li>

                                                        <?php if($enable_wallet_mode == '1'){
                                                            if(intval($withdraw_price) > intval($wallet_fee_amount)){ ?>
                                                                <?php if ($project_featured != 1) { ?>
                                                                    <li><a class="btn-mark-featured" project-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Mark featured', 'felan-framework') ?></a></li>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                                <?php if ($project_featured !== 1) { ?>
                                                                    <li>
                                                                        <a class="btn-add-to-message" href="#"
                                                                           data-text="<?php echo esc_attr('Your wallet balance is currently insufficient.', 'felan-framework'); ?>">
                                                                            <?php esc_html_e('Mark featured', 'felan-framework') ?>
                                                                        </a>
                                                                    </li>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            <?php if ($project_featured != 1) { ?>
                                                                <li><a class="btn-mark-featured" project-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Mark featured', 'felan-framework') ?></a></li>
                                                            <?php } ?>
                                                        <?php } ?>

                                                        <li><a class="btn-mark-filled" project-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Mark Filled', 'felan-framework') ?></a></li>
                                                    <?php } ?>

                                                    <li><a href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('View detail', 'felan-framework') ?></a></li>
                                                <?php }
                                                break;
                                            case 'pending': ?>
                                                <li><a class="btn-edit" href="<?php echo esc_url($project_dashboard_link); ?>?pages=edit&project_id=<?php echo esc_attr($id); ?>"><?php esc_html_e('Edit', 'felan-framework'); ?></a></li>
                                            <?php
                                                break;
                                            case 'pause':
                                            ?>
                                                <li><a class="btn-edit" href="<?php echo esc_url($project_dashboard_link); ?>?pages=edit&project_id=<?php echo esc_attr($id); ?>"><?php esc_html_e('Edit', 'felan-framework'); ?></a></li>
                                                <li><a class="btn-show" project-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Continue', 'felan-framework'); ?></a>
                                            <?php
                                        } ?>
                                    </ul>
                                <?php else : ?>
                                    <a href="#" class="icon-setting btn-add-to-message" data-text="<?php echo esc_attr('Project has expired so you can not change it', 'felan-framework'); ?>"><i class="far fa-ellipsis-h"></i></a></a>
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
    <?php $max_num_pages = $data->max_num_pages;
    $total_post = $data->found_posts;
    if ($total_post > $posts_per_page) { ?>
        <div class="pagination-dashboard">
            <?php felan_get_template('global/pagination.php', array('total_post' => $total_post, 'max_num_pages' => $max_num_pages, 'type' => 'dashboard', 'layout' => 'number'));
            wp_reset_postdata(); ?>
        </div>
    <?php } ?>
    </div>
<?php } ?>