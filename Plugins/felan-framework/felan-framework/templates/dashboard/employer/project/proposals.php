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
		'posts_per_page' => -1,
        // 'posts_per_page'      => $posts_per_page,
        // 'offset'              => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
        'author'              => $user_id,
        'orderby'               => 'date',
    );
    $data_project = new WP_Query($args);

	$project_ids = [];
	if ($data_project->have_posts()) {
		while ($data_project->have_posts()) {
			$data_project->the_post();
			$project_ids[] = get_the_ID();
		}
		wp_reset_postdata();
	}


	$args_applicants = array(
		'post_type' => 'project-proposal',
		'ignore_sticky_posts' => 1,
		// 'posts_per_page' => -1,
		'posts_per_page'      => $posts_per_page,
        'offset'              => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key' => FELAN_METABOX_PREFIX . 'proposal_project_id',
				'value' => $project_ids,
				'compare' => 'IN'
			)
		),
	);

	$project_id = isset($_GET['project_id']) ? felan_clean(wp_unslash($_GET['project_id'])) : '';
	if ( ! empty( $project_id ) ) {
		$args_applicants['s'] = get_the_title( $project_id );
	}
	$data = new WP_Query($args_applicants);


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
                <select class="proposal-search-control felan-select2" name="project_status">
                    <option value=""><?php esc_html_e('All status', 'felan-framework') ?></option>
                    <option value="pending"><?php esc_html_e('Pending', 'felan-framework') ?></option>
                    <option value="inprogress"><?php esc_html_e('In Process', 'felan-framework') ?></option>
                    <option value="canceled"><?php esc_html_e('Canceled', 'felan-framework') ?></option>
                    <option value="completed"><?php esc_html_e('Completed', 'felan-framework') ?></option>
                </select>
            </div>
            <div class="action-search">
                <input class="project-proposal-search-control" type="text" name="project_search" placeholder="<?php esc_attr_e('Search project title', 'felan-framework') ?>">
                <button class="btn-search">
                    <i class="far fa-search"></i>
                </button>
            </div>
        </div>
        <div class="search-right">
            <label class="text-sorting"><?php esc_html_e('Sort by', 'felan-framework') ?></label>
            <div class="select2-field">
                <select class="proposal-search-control action-sorting felan-select2" name="project_sort_by">
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
                        <th><?php esc_html_e('Freelancer', 'felan-framework') ?></th>
                        <th><?php esc_html_e('Project Applied', 'felan-framework') ?></th>
                        <th><?php esc_html_e('Budget/Time', 'felan-framework') ?></th>
                        <!-- <th><?php esc_html_e('Date', 'felan-framework') ?></th> -->
                        <th><?php esc_html_e('PRICE', 'felan-framework') ?></th>
                        <th><?php esc_html_e('STATUS', 'felan-framework') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($data->have_posts()) : $data->the_post();
						$applicants_id = get_the_ID();
						$project_id = get_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'proposal_project_id', true);
					?>
                        <tr class="list-applicant" id="list-applicant-<?php echo esc_attr($project_id); ?>">
							<?php

							$project_featured    = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_featured', true);
							$projects_budget_show = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_budget_show', true);

							$author_id = get_post_field('post_author', $applicants_id);
							// $project_dashboard_link = felan_get_permalink('project_dashboard');

							$felan_project_page_id = felan_get_option('felan_projects_page_id');
            				$project_dashboard_link    = get_page_link($felan_project_page_id);

							$freelancer_id = '';
							if (!empty($author_id)) {
								$args_freelancer = array(
									'post_type' => 'freelancer',
									'posts_per_page' => 1,
									'author' => $author_id,
									'post_status' => 'any'
								);
								$current_user_posts = get_posts($args_freelancer);
								$freelancer_id = !empty($current_user_posts) ? $current_user_posts[0]->ID : '';
								$freelancer_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
							}
							$has_freelancer_review = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'has_freelancer_review', true);

							$proposal_status = get_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'proposal_status', true);
							$proposal_price = get_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'proposal_price', true);
							$proposal_time = get_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'proposal_time', true);
							$proposal_fixed_time = get_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'proposal_fixed_time', true);
							$proposal_rate = get_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'proposal_rate', true);
							$proposal_maximum_time = get_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'proposal_maximum_time', true);
							$currency_sign_default = felan_get_option('currency_sign_default');
							$currency_position = felan_get_option('currency_position');
							if ($currency_position == 'before') {
								$proposal_total_price = $currency_sign_default . $proposal_price;
							} else {
								$proposal_total_price = $proposal_price . $currency_sign_default;
							}
							?>

							<td>
								<div class="info-user">
									<?php if (!empty($freelancer_avatar)) : ?>
										<div class="image-applicants"><img class="image-freelancers" src="<?php echo esc_url($freelancer_avatar) ?>" alt="" /></div>
									<?php else : ?>
										<div class="image-applicants"><i class="far fa-camera"></i></div>
									<?php endif; ?>
									<div class="info-details">
										<h3>
											<?php echo get_the_title($freelancer_id); ?>
										</h3>
										<?php echo felan_get_total_rating('freelancer', $freelancer_id); ?>
									</div>
								</div>
							</td>
							<td>

								<h3 class="title-project-dashboard">
									<a href="<?php echo get_the_permalink($project_id); ?>" target="_blank">
										<?php echo get_the_title($project_id); ?>
										<?php if ($project_featured == '1') : ?>
											<img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-featured.svg'); ?>" alt="<?php echo esc_attr('featured', 'felan-framework') ?>">
										<?php endif; ?>
									</a>
								</h3>

							</td>
							<td>
								<p>
									<?php if($projects_budget_show == 'hourly') : ?>
										<?php echo sprintf(esc_html__('%1s / in %2s hours', 'felan-framework'),$proposal_total_price, $proposal_time) ?>
									<?php else: ?>
										<?php echo sprintf(esc_html__('%1s / in %2s %3s ', 'felan-framework'),$proposal_total_price, $proposal_fixed_time, $proposal_rate) ?>
									<?php endif; ?>
								</p>
							</td>
							<!-- <div class="col">
								<p><?php echo sprintf(esc_html__('%1s', 'felan-framework'), get_the_date(get_option('date_format'))) ?></p>
							</div> -->
							<td>

								<?php echo felan_get_budget_project($project_id); ?>
								<p class="budget-show">
									<?php if($projects_budget_show == 'hourly') : ?>
										<?php echo esc_html__('Hourly Rate', 'felan-framework'); ?>
									<?php else: ?>
										<?php echo esc_html__('Fixed Price', 'felan-framework'); ?>
									<?php endif; ?>
								</p>

							</td>
							<td>
								<?php felan_project_package_status($proposal_status); ?>
							</td>
							<td>
								<div class="button-warpper d-flex justify-content-end">
									<?php if($proposal_status == 'completed') : ?>
										<?php if($has_freelancer_review == '1') : ?>
											<div class="action-review mr-2">
												<a href="#"
													class="btn-action-view felan-button button-outline-gray"
													freelancer-id="<?php echo esc_attr($freelancer_id); ?>"
													style="font-size: 14px;"
												>
													<?php echo esc_html__('Your Review', 'felan-framework'); ?>
												</a>
											</div>
										<?php else: ?>
											<div class="action-review mr-2">
												<a href="#"
													class="btn-action-review btn-review-project felan-button button-outline-gray"
													freelancer-id="<?php echo esc_attr($freelancer_id); ?>"
													order-id="<?php echo esc_attr($applicants_id); ?>"
													style="font-size: 14px;"
												>
													<?php echo esc_html__('Review', 'felan-framework'); ?>
												</a>
											</div>
										<?php endif; ?>
									<?php endif; ?>
									<a href="<?php echo esc_url($project_dashboard_link); ?>?applicants_id=<?php echo esc_attr($applicants_id); ?>&project_id=<?php echo esc_attr($project_id); ?>"
										class="felan-button"
										style="font-size: 14px;"
									>
										<?php echo esc_html__('Detail','felan-framework') ?>
									</a>
								</div>
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
    <?php
	$total_post = $data->found_posts;
    $max_num_pages = ceil($total_post / $posts_per_page);
    if ($total_post > $posts_per_page) { ?>
        <div class="pagination-dashboard">
            <?php felan_get_template('global/pagination-proposal.php', array('total_post' => $total_post, 'max_num_pages' => $max_num_pages, 'type' => 'dashboard', 'layout' => 'number'));
            wp_reset_postdata(); ?>
        </div>
    <?php } ?>
    </div>
<?php } ?>