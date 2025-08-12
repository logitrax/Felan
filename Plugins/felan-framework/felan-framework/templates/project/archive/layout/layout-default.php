<?php

/**
 * The Template for displaying project archive
 */

defined('ABSPATH') || exit;
$price_min = felan_get_option('felan_price_min', '0');
$price_max = felan_get_option('felan_price_max', '1000');
$items_amount = felan_get_option('archive_project_items_amount', '12');
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'select-location');
$enable_project_single_popup = felan_get_option('enable_project_single_popup', '0');
$enable_project_single_popup = !empty($_GET['has_popup']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_popup'])) : $enable_project_single_popup;
$currency_sign_default = felan_get_option('currency_sign_default');

wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'project-archive');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'project-archive',
    'felan_project_archive_vars',
    array(
        'not_project' => esc_html__('No project found', 'felan-framework'),
        'item_amount' => $items_amount,
        'range_min' => $price_min,
        'range_max' => $price_max,
        'currency_sign' => $currency_sign_default,
    )
);

$content_project              = felan_get_option('archive_project_layout', 'layout-list');
$hide_project_top_filter_fields = felan_get_option('hide_project_top_filter_fields');
$enable_project_filter_top = felan_get_option('enable_project_filter_top');
$project_filter_sidebar_option = felan_get_option('project_filter_sidebar_option');
$content_project = !empty($_GET['layout']) ? Felan_Helper::felan_clean(wp_unslash($_GET['layout'])) : $content_project;
$project_filter_sidebar_option = !empty($_GET['filter']) ? Felan_Helper::felan_clean(wp_unslash($_GET['filter'])) : $project_filter_sidebar_option;

$enable_project_show_map = felan_get_option('enable_project_show_map');
$project_map_postion = felan_get_option('project_map_postion');
$project_map_postion = !empty($_GET['map']) ? Felan_Helper::felan_clean(wp_unslash($_GET['map'])) : $project_map_postion;
$enable_project_show_map = !empty($_GET['has_map']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_map'])) : $enable_project_show_map;

if ($content_project == 'layout-list') {
    $class_view = 'list-view';
    $class_inner[] = 'layout-list';
} else {
    $class_view = 'grid-view';
    $class_inner[] = 'layout-grid';
}

$key          = isset($_GET['s']) ? felan_clean(wp_unslash($_GET['s'])) : '';
$archive_class   = array();
$archive_class[] = 'content-project area-project area-archive';
$archive_class[] = $class_view;

$author          = isset($_GET['project_author']) ? felan_clean(wp_unslash($_GET['project_author'])) : '';

$tax_query = array();
$args = array(
    'posts_per_page'      => $items_amount,
    'post_type'           => 'project',
    'ignore_sticky_posts' => 1,
    'post_status'         => 'publish',
    'tax_query'           => $tax_query,
    's'                   => $key,
    'meta_key'            => FELAN_METABOX_PREFIX . 'project_featured',
    'orderby'             => 'meta_value date',
    'order'               => 'DESC',
);

if ($author) {
    $args['author'] = intval($author);
}

//Current term
$project_location = isset($_GET['project-location']) ? felan_clean(wp_unslash($_GET['project-location'])) : '';
if (!empty($project_location)) {
    $current_term = get_term_by('slug', $project_location, 'felan_location');
} else {
    $current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
}
$current_term_name = '';
if (!empty($current_term)) {
    $current_term_name = $current_term->name;
}
if (!empty($current_term)) {
    $taxonomy_title = $current_term->name;
    $taxonomy_name = $current_term->taxonomy;
    if (!empty($taxonomy_name)) {
        $tax_query[] = array(
            'taxonomy' => $taxonomy_name,
            'field' => 'slug',
            'terms' => $current_term->slug
        );
    }
}

$tax_count = count($tax_query);
if ($tax_count > 0) {
    $args['tax_query'] = array(
        'relation' => 'AND',
        $tax_query
    );
}
$data       = new WP_Query($args);
$total_post = $data->found_posts;;

if ($enable_project_show_map == 1) {
    $class_inner[] = 'has-map';
} else {
    $class_inner[] = 'no-map';
}
?>
<?php if ($enable_project_show_map == 1 && $project_map_postion == 'map-top') { ?>
    <div class="col-right">
        <?php
        /**
         * @Hook: felan_archive_map_filter
         *
         * @hooked archive_map_filter
         */
        do_action('felan_archive_map_filter');
        ?>
    </div>
<?php } ?>

<?php if ($enable_project_filter_top == 1) { ?>
    <?php do_action('felan_archive_project_top_filter', $current_term, $total_post); ?>
<?php } ?>

<div class="inner-content container <?php echo join(' ', $class_inner); ?>">
    <div class="col-left">
        <?php if ($project_filter_sidebar_option !== 'filter-right') {
            do_action('felan_archive_project_sidebar_filter', $current_term, $total_post);
        } ?>

        <?php
        /**
         * @Hook: felan_output_content_wrapper_start
         *
         * @hooked output_content_wrapper_start
         */
        do_action('felan_output_content_wrapper_start');
        ?>

        <div class="filter-warpper">
            <div class="entry-left">
                <div class="btn-canvas-filter <?php if ($project_filter_sidebar_option !== 'filter-canvas' && $enable_project_show_map != 1) { ?>hidden-lg-up<?php } ?>">
                    <a href="#"><i class="far fa-filter"></i><?php esc_html_e('Filter', 'felan-framework'); ?></a>
                </div>
                <span class="result-count">
                    <?php if (!empty($key)) { ?>
                        <?php
							// printf(esc_html__('%1$s projects for "%2$s"', 'felan-framework'), '<span>' . $total_post . '</span>', $key);
							printf(
								esc_html(_n('%1$s project for "%2$s"', '%1$s projects for "%2$s"', $total_post, 'felan-framework')),
								'<span>' . $total_post . '</span>',
								$key
							);
						?>
                    <?php } elseif (is_tax()) { ?>
                        <?php
							// printf(esc_html__('%1$s projects for "%2$s"', 'felan-framework'), '<span>' . $total_post . '</span>', $current_term_name);
							printf(
								esc_html(_n('%1$s project for "%2$s"', '%1$s projects for "%2$s"', $total_post, 'felan-framework')),
								'<span>' . $total_post . '</span>',
								$current_term_name
							);
						?>
                    <?php } else { ?>
                        <?php
							// printf(esc_html__('%s projects', 'felan-framework'), '<span>' . $total_post . '</span>');
							echo sprintf(
								_n( '%s project', '%s projects', $total_post, 'felan-framework' ),
								'<span>' . $total_post . '</span>'
							);
						?>
                    <?php } ?>
                </span>
            </div>
            <div class="entry-right">
                <div class="entry-filter">
                    <div class="felan-clear-filter hidden-lg-up">
                        <i class="far fa-sync"></i>
                        <span><?php esc_html_e('Clear All', 'felan-framework'); ?></span>
                    </div>
                    <div class="project-layout switch-layout">
                        <a class="<?php if ($content_project == 'layout-grid') : echo 'active';
                                    endif; ?>" href="#" data-layout="layout-grid"><i class="far far fa-th-large icon-large"></i></a>
                        <a class="<?php if ($content_project == 'layout-list') : echo 'active';
                                    endif; ?>" href="#" data-layout="layout-list"><i class="far fa-list icon-large"></i></a>
                    </div>
                    <select name="sort_by" class="sort-by filter-control felan-select2">
                        <option value="newest"><?php esc_html_e('Newest', 'felan-framework'); ?></option>
                        <option value="oldest"><?php esc_html_e('Oldest', 'felan-framework'); ?></option>
                        <option value="rating"><?php esc_html_e('Rating', 'felan-framework'); ?></option>
                    </select>
                    <?php if ($enable_project_show_map == 1 && $project_map_postion == 'map-right') { ?>
                        <div class="btn-control btn-switch btn-hide-map">
                            <span class="text-switch"><?php esc_html_e('Map', 'felan-framework'); ?></span>
                            <label class="switch">
                                <input type="checkbox" value="hide_map">
                                <span class="slider round"></span>
                            </label>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="entry-mobie">
            <span class="result-count">

				<?php if (!empty($key)) { ?>
					<?php
						// printf(esc_html__('%1$s projects for "%2$s"', 'felan-framework'), '<span>' . $total_post . '</span>', $key);
						printf(
							esc_html(_n('%1$s project for "%2$s"', '%1$s projects for "%2$s"', $total_post, 'felan-framework')),
							'<span>' . $total_post . '</span>',
							$key
						);
					?>
				<?php } elseif (is_tax()) { ?>
					<?php
						// printf(esc_html__('%1$s projects for "%2$s"', 'felan-framework'), '<span>' . $total_post . '</span>', $current_term_name);
						printf(
							esc_html(_n('%1$s project for "%2$s"', '%1$s projects for "%2$s"', $total_post, 'felan-framework')),
							'<span>' . $total_post . '</span>',
							$current_term_name
						);
					?>
				<?php } else { ?>
					<?php
						// printf(esc_html__('%s projects', 'felan-framework'), '<span>' . $total_post . '</span>');
						echo sprintf(
							_n( '%s project', '%s projects', $total_post, 'felan-framework' ),
							'<span>' . $total_post . '</span>'
						);
					?>
				<?php } ?>

            </span>
            <div class="felan-clear-filter hidden-lg-up">
                <i class="far fa-sync"></i>
                <span><?php esc_html_e('Clear All', 'felan-framework'); ?></span>
            </div>
        </div>

        <div class="<?php echo join(' ', $archive_class); ?>">
            <?php if ($data->have_posts()) { ?>
                <?php while ($data->have_posts()) : $data->the_post(); ?>
                    <?php felan_get_template('content-project.php', array(
                        'project_layout' => $content_project,
                    )); ?>
                <?php endwhile; ?>
            <?php } else { ?>
                <div class="item-not-found"><?php esc_html_e('No item found', 'felan-framework'); ?></div>
            <?php } ?>
        </div>

        <?php
        $max_num_pages = $data->max_num_pages;
        $pagination_type = felan_get_option('project_pagination_type');
        felan_get_template('global/pagination.php', array('max_num_pages' => $max_num_pages, 'type' => 'ajax-call', 'pagination_type' => $pagination_type));
        wp_reset_postdata();
        ?>
        <?php
        /**
         * @Hook: felan_output_content_wrapper_end
         *
         * @hooked output_content_wrapper_end
         */
        do_action('felan_output_content_wrapper_end');
        ?>

        <?php if ($project_filter_sidebar_option == 'filter-right' && $enable_project_show_map != 1) {
            do_action('felan_archive_project_sidebar_filter', $current_term, $total_post);
        } ?>

    </div>
    <?php if ($enable_project_show_map == 1 && $project_map_postion == 'map-right') { ?>
        <div class="col-right">
            <?php
            /**
             * @Hook: felan_archive_map_filter
             *
             * @hooked archive_map_filter
             */
            do_action('felan_archive_map_filter');
            ?>
        </div>
    <?php } ?>
</div>