<?php

/**
 * layout_wrapper_start
 */
function layout_wrapper_start()
{
    $class_layout = array('site-layout');

    if (is_single() && get_post_type() == 'company') {
        $single_company_style = felan_get_option('single_company_style');
        $single_company_style = !empty($_GET['layout']) ? felan_clean(wp_unslash($_GET['layout'])) : $single_company_style;

        if ($single_company_style == 'large-cover-img') {
            $class_layout[] = 'has-large-thumbnail';
        }
    }

    if (is_single() && get_post_type() == 'freelancer') {
        $single_freelancer_style = felan_get_option('single_freelancer_style');
        $single_freelancer_style = !empty($_GET['layout']) ? felan_clean(wp_unslash($_GET['layout'])) : $single_freelancer_style;

        if ($single_freelancer_style == 'large-cover-img') {
            $class_layout[] = 'has-large-thumbnail';
        }
    }

    if (is_single() && get_post_type() == 'jobs') {
        $enable_single_jobs_info_left = felan_get_option('enable_single_jobs_info_left', '0');
        $enable_single_jobs_info_left = !empty($_GET['info-left']) ? felan_clean(wp_unslash($_GET['info-left'])) : $enable_single_jobs_info_left;
        if ($enable_single_jobs_info_left === '1') {
            $class_layout[] = 'info-left';
        }
    }

    if (is_single() && get_post_type() == 'service') {
        $enable_single_service_info_left = felan_get_option('enable_single_service_info_left', '0');
        $enable_single_service_info_left = !empty($_GET['info-left']) ? felan_clean(wp_unslash($_GET['info-left'])) : $enable_single_service_info_left;
        if ($enable_single_service_info_left === '1') {
            $class_layout[] = 'info-left';
        }
    }

    if (is_single() && get_post_type() == 'project') {
        $enable_single_project_info_left = felan_get_option('enable_single_project_info_left', '0');
        $enable_single_project_info_left = !empty($_GET['info-left']) ? felan_clean(wp_unslash($_GET['info-left'])) : $enable_single_project_info_left;
        if ($enable_single_project_info_left === '1') {
            $class_layout[] = 'info-left';
        }
    }

    if (is_tax() || is_archive()) {
        $class_layout[] = 'has-sidebar';
    }

    if (is_single() && ((get_post_type() == 'jobs') || (get_post_type() == 'company') || (get_post_type() == 'freelancer') || (get_post_type() == 'service') || (get_post_type() == 'project'))) {
        $class_layout[] = 'has-sidebar';
    }

?>
    <div class="main-content">
        <div class="container">
            <div class="<?php echo join(' ', $class_layout); ?>">
            <?php
        }

        /**
         * layout_wrapper_end
         */
        function layout_wrapper_end()
        {
            ?>
            </div>
        </div>
    </div>
<?php
        }

        /**
         * output_content_wrapper
         */
        function output_content_wrapper_start()
        {
            felan_get_template('global/wrapper-start.php');
        }

        /**
         * output_content_wrapper
         */
        function output_content_wrapper_end()
        {
            felan_get_template('global/wrapper-end.php');
        }

        /**
         * archive jobs before
         */
        function archive_jobs_post()
        {
            felan_get_template('global/related-post.php');
        }

        /**
         * archive page title
         */
        function archive_page_title()
        {
            felan_get_template('archive-jobs/page-title.php');
        }

        /**
         * archive information
         */
        function archive_information()
        {
            felan_get_template('archive-jobs/information.php');
        }

        /**
         * archive categories
         */
        function archive_categories()
        {
            felan_get_template('archive-jobs/categories.php');
        }

        /**
         * archive map filter
         */
        function archive_map_filter()
        {
            wp_enqueue_script('google-map');
            wp_enqueue_script('markerclusterer');
            $map_type = felan_get_option('map_type', 'mapbox');

            if ($map_type == 'google_map') {
                $map_zoom_level = felan_get_option('map_zoom_level');
                $google_map_style = felan_get_option('googlemap_style', 'streets-v11');
            } else if ($map_type == 'mapbox') {
                $mapbox_api_key = felan_get_option('mapbox_api_key');
                $map_zoom_level = felan_get_option('map_zoom_level');
                $google_map_style = felan_get_option('mapbox_style', 'streets-v11');
            } else if ($map_type == 'openstreetmap') {
                $openstreetmap_api_key = felan_get_option('openstreetmap_api_key');
                $map_zoom_level = felan_get_option('map_zoom_level');
                $openstreetmap_style = felan_get_option('openstreetmap_style', 'streets-v11');
            }
            felan_get_map_enqueue();
?>
    <div class="felan-filter-search-map">
        <div class="entry-map">
            <input id="pac-input" class="controls" type="text" placeholder="<?php esc_html_e('Search...', 'felan-framework'); ?>">
            <?php if ($map_type == 'google_map') { ?>
                <div id="jobs-map-filter" class="felan-map-filter maptype"
                    style="width: 100%;"
                    data-level="<?php if ($map_zoom_level) {
                                    echo $map_zoom_level;
                                } ?>"
                    data-maptype="google_map"
                    data-type="<?php echo $google_map_style; ?>">
                </div>
            <?php } else if ($map_type == 'openstreetmap') { ?>
                <div id="maps" class="felan-openstreetmap-filter maptype" style="width: 100%; height: 100%;" data-type="<?php echo $openstreetmap_style; ?>" data-key="<?php if ($openstreetmap_api_key) {
                                                                                                                                                                            echo $openstreetmap_api_key;
                                                                                                                                                                        } ?>" data-level="<?php if ($map_zoom_level) {
                                                                                                                                                                                                echo $map_zoom_level;
                                                                                                                                                                                            } ?>" data-style="<?php if ($openstreetmap_style) {
                                                                                                                                                                                                                    echo $openstreetmap_style;
                                                                                                                                                                                                                } ?>"></div>
            <?php } else { ?>
                <div id="map" class="maptype" style="width: 100%; height: 100%;" data-type="<?php echo $google_map_style; ?>" data-level="<?php if ($map_zoom_level) {
                                                                                                                                                echo $map_zoom_level;
                                                                                                                                            } ?>" data-key="<?php if ($mapbox_api_key) {
                                                                                                                                                                echo $mapbox_api_key;
                                                                                                                                                            } ?>"></div>
            <?php } ?>
            <div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>
            <div class="no-result"><span><?php esc_html_e("We didn't find any results", 'felan-framework'); ?></span>
            </div>
        </div>
    </div>
<?php
        }

        /**
         * archive jobs top filter
         */
        function archive_jobs_top_filter()
        {
            wp_enqueue_script('jquery-ui-autocomplete');
            wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'search-autocomplete');

            $jobs_search_fields = felan_get_option('jobs_search_field');
            $jobs_search_fields_top = isset($jobs_search_fields['top']) ? $jobs_search_fields['top'] : array();
            unset($jobs_search_fields_top['__no_value__']);
            unset($jobs_search_fields_top['salary']);

            $search_color = $search_image = $search_title_color = '';
            $enable_jobs_search_bg = felan_get_option('enable_jobs_search_bg');
            $enable_jobs_search_location = felan_get_option('enable_jobs_search_location_top', '1');
            $jobs_search_color = felan_get_option('jobs_search_color');
            $jobs_search_image = felan_get_option('jobs_search_image');
            $freelancer_search_color = felan_get_option('freelancer_search_color');
            $enable_jobs_search_location_radius = felan_get_option('enable_jobs_search_location_radius');
            $felan_distance_unit = felan_get_option('felan_distance_unit', 'km');

            $enable_jobs_search_bg = !empty($_GET['has_bg']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_bg'])) : $enable_jobs_search_bg;
            if ($enable_jobs_search_bg == 1) {
                $class_inner = 'has-bg';
            } else {
                $class_inner = '';
            }
            if (!empty($jobs_search_color)) {
                $search_image = 'background-color :' . $jobs_search_color . ';';
            }
            if (!empty($jobs_search_image['url'])) {
                $search_image = "background-image : url({$jobs_search_image['url']})";
            }

?>
    <div class="archive-jobs-top archive-filter-top <?php echo $class_inner; ?>" <?php if ($enable_jobs_search_bg == 1) { ?> style="<?php echo $search_image ?>" <?php } ?>>
        <div class="container">
            <h2><?php esc_html_e('Jobs Listing', 'felan-framework'); ?></h2>
            <form method="post" class="form-jobs-top-filter form-archive-top-filter">
                <div class="row">
                    <?php
                    $jobs_skills = array();
                    $taxonomy_kills = get_categories(
                        array(
                            'taxonomy' => 'jobs-skills',
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'hide_empty' => false,
                            'number' => 88,
                            'parent' => 0
                        )
                    );
                    if (!empty($taxonomy_kills)) {
                        foreach ($taxonomy_kills as $term) {
                            $jobs_skills[] = $term->name;
                        }
                    }
                    $jobs_keyword = json_encode($jobs_skills);
                    $id = apply_filters('felan/search-control/id', 'jobs_filter_search');
                    ?>
                    <div class="form-group">
                        <input class="jobs-search-control archive-search-control" data-key='<?php echo $jobs_keyword ?>' id="<?php echo esc_attr($id); ?>" type="text" name="jobs_filter_search" placeholder="<?php esc_attr_e('Jobs title or keywords', 'felan-framework') ?>" value="<?php if (isset($_GET['s']) && $_GET['s'] != '') {
                                                                                                                                                                                                                                                                                            echo felan_clean(wp_unslash($_GET['s']));
                                                                                                                                                                                                                                                                                        } ?>" autocomplete="off">
                        <span class="btn-filter-search"><i class="far fa-search"></i></span>
                    </div>
                    <?php if ($enable_jobs_search_location === '1') { ?>
                        <div class="form-group felan-form-location">
                            <input class="archive-search-location" type="text" name="jobs-search-location" placeholder="<?php esc_attr_e('All Cities', 'felan-framework') ?>" autocomplete="off" value="<?php if (isset($_GET['jobs-location']) && $_GET['jobs-location'] != '') {
                                                                                                                                                                                                            echo felan_clean(wp_unslash($_GET['jobs-location']));
                                                                                                                                                                                                        } ?>">
                            <?php do_action('felan_search_horizontal_after_location'); ?>
                            <select name="jobs-location-top" class="felan-select2 hide">
                                <?php felan_get_taxonomy('felan_location', false, false); ?>
                            </select>

                            <span class="icon-location">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_8969_23265)">
                                        <path d="M13 1L13.001 4.062C14.7632 4.28479 16.4013 5.08743 17.6572 6.34351C18.9131 7.5996 19.7155 9.23775 19.938 11H23V13L19.938 13.001C19.7153 14.7631 18.9128 16.401 17.6569 17.6569C16.401 18.9128 14.7631 19.7153 13.001 19.938L13 23H11V19.938C9.23775 19.7155 7.5996 18.9131 6.34351 17.6572C5.08743 16.4013 4.28479 14.7632 4.062 13.001L1 13V11H4.062C4.28459 9.23761 5.08713 7.59934 6.34324 6.34324C7.59934 5.08713 9.23761 4.28459 11 4.062V1H13ZM12 6C10.4087 6 8.88258 6.63214 7.75736 7.75736C6.63214 8.88258 6 10.4087 6 12C6 13.5913 6.63214 15.1174 7.75736 16.2426C8.88258 17.3679 10.4087 18 12 18C13.5913 18 15.1174 17.3679 16.2426 16.2426C17.3679 15.1174 18 13.5913 18 12C18 10.4087 17.3679 8.88258 16.2426 7.75736C15.1174 6.63214 13.5913 6 12 6ZM12 10C12.5304 10 13.0391 10.2107 13.4142 10.5858C13.7893 10.9609 14 11.4696 14 12C14 12.5304 13.7893 13.0391 13.4142 13.4142C13.0391 13.7893 12.5304 14 12 14C11.4696 14 10.9609 13.7893 10.5858 13.4142C10.2107 13.0391 10 12.5304 10 12C10 11.4696 10.2107 10.9609 10.5858 10.5858C10.9609 10.2107 11.4696 10 12 10Z" fill="#999999" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_8969_23265">
                                            <rect width="24" height="24" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <span class="icon-arrow">
                                <i class="far fa-angle-down"></i>
                            </span>
                            <?php if ($enable_jobs_search_location_radius == 1) { ?>
                                <span class="radius">
                                    <span class="labels"><?php esc_html_e('Radius:', 'felan-framework') ?></span>
                                    <input type="number" name="jobs_number_radius" value="25" placeholder="0" />
                                    <span class="distance"><?php echo esc_html($felan_distance_unit); ?></span>
                                </span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php foreach ($jobs_search_fields_top as $key_field => $field) {
                        $jobs_search_fields_icon = felan_get_option('jobs_search_fields_' . $key_field);
                        if ($key_field == 'jobs-location') {
                            felan_content_option_taxonomy('jobs', 'top');
                        } else { ?>
                            <div class="form-group">
                                <?php echo $jobs_search_fields_icon; ?>
                                <select name="<?php echo esc_attr($key_field) ?>" class="felan-select2">
                                    <?php echo '<option value="">' . esc_html__('All ' . $field, 'felan-framework') . '</option>'; ?>
                                    <?php felan_get_taxonomy($key_field, false, false); ?>
                                </select>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    <div class="form-group">
                        <span class="felan-clear-top-filter"><?php esc_html_e('Clear', 'felan-framework') ?></span>
                        <input type="hidden" name="has_map" value="<?php if (isset($_GET['has_map']) && $_GET['has_map'] == '1') {
                                                                        echo '1';
                                                                    } else {
                                                                        echo '0';
                                                                    } ?>">
                        <button type="submit" class="btn-top-filter felan-button" name="jobs-top-filter">
                            <?php esc_html_e('Search', 'felan-framework') ?>
                            <span class="btn-loading"><i class="far fa-spinner fa-spin medium"></i></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php }

        /**
         * archive jobs sidebar filter
         */
        function archive_jobs_sidebar_filter($current_term, $total_post)
        {
            wp_enqueue_script('jquery-ui-slider');
            $key = isset($_GET['s']) ? felan_clean(wp_unslash($_GET['s'])) : '';
            $location = isset($_GET['jobs_']) ? felan_clean(wp_unslash($_GET['jobs_location'])) : '';
            $filter_classes = array();
            $taxonomy_name = get_query_var('taxonomy');
            $term_id = '';
            if ($current_term) {
                $term_id = $current_term->term_id;
            }
            $jobs_search_fields = felan_get_option('jobs_search_field');
            $jobs_search_fields_sidebar = isset($jobs_search_fields['sidebar']) ? $jobs_search_fields['sidebar'] : array();
            unset($jobs_search_fields_sidebar['__no_value__']);
?>
    <div class="archive-filter <?php echo join(' ', $filter_classes); ?>">
        <div class="bg-overlay"></div>
        <div class="inner-filter custom-scrollbar">
            <div class="felan-nav-filter">
                <div class="felan-filter-toggle">
                    <span><?php esc_html_e('Filter', 'felan-framework'); ?></span>
                </div>
                <div class="felan-clear-filter">
                    <i class="far fa-sync"></i>
                    <span><?php esc_html_e('Clear All', 'felan-framework'); ?></span>
                </div>
            </div>
            <div class="felan-menu-filter">
                <?php
                if ($jobs_search_fields_sidebar) : foreach ($jobs_search_fields_sidebar as $field => $v) {
                        switch ($field) {
                            case 'jobs-salary':
                ?>
                                <div class="filter-price">
                                    <div class="entry-filter">
                                        <h4><?php esc_html_e('Price', 'felan-framework'); ?></h4>
                                        <div id="range-slider">
                                            <div id="slider-range"></div>
                                            <p><input type="text" id="amount" readonly></p>
                                        </div>
                                    </div>
                                </div>
                            <?php break;

                            case 'jobs-location': ?>
                                <div class="entry-filter entry-filter-locations">
                                    <h4><?php esc_html_e('Locations', 'felan-framework'); ?></h4>
                                    <div class="locations-filter">
                                        <?php felan_content_option_taxonomy('jobs'); ?>
                                    </div>
                                </div>
                <?php break;

                            case 'jobs-categories':
                                $title = esc_html__('Jobs Categories', 'felan-framework');
                                get_search_filter_submenu('jobs-categories', $title);
                                break;

                            case 'jobs-skills':
                                $title = esc_html__('Jobs Skills', 'felan-framework');
                                get_search_filter_submenu('jobs-skills', $title);
                                break;

                            case 'jobs-type':
                                $title = esc_html__('Jobs Type', 'felan-framework');
                                get_search_filter_submenu('jobs-type', $title);
                                break;

                            case 'jobs-experience':
                                $title = esc_html__('Jobs Experience', 'felan-framework');
                                get_search_filter_submenu('jobs-experience', $title, true, true, 'jobs_experience_order');
                                break;

                            case 'jobs-career':
                                $title = esc_html__('Jobs Career', 'felan-framework');
                                get_search_filter_submenu('jobs-career', $title);
                                break;

                            case 'jobs-gender':
                                $title = esc_html__('Jobs Gender', 'felan-framework');
                                get_search_filter_submenu('jobs-gender', $title);
                                break;

                            case 'jobs-qualification':
                                $title = esc_html__('Jobs Qualification', 'felan-framework');
                                get_search_filter_submenu('jobs-qualification', $title);
                                break;
                        }
                        get_search_filter_custom('jobs', $field);
                    }
                endif;
                ?>

            </div>
        </div>
        <div class="show-result">
            <a href="#" class="felan-button button-block">
                <span><?php echo esc_html__('Show', 'felan-framework'); ?></span>
                <span class="result-count">

					<?php if (!empty($key)) { ?>
						<?php
							printf(
								esc_html(_n('%1$s job for "%2$s"', '%1$s jobs for "%2$s"', $total_post, 'felan-framework')),
								'<span>' . $total_post . '</span>',
								$key
							);
						?>
					<?php } else { ?>
						<?php
							echo sprintf(
								_n( '%s job', '%s jobs', $total_post, 'felan-framework' ),
								'<span>' . $total_post . '</span>'
							);
						?>
					<?php } ?>

                </span>
            </a>
        </div>
    </div>
    <?php
            $custom_field_filter = felan_render_custom_field('jobs');
            $jobs_search_custom_fields_sidebar = array_map(function ($value) {
                return $value['id'];
            }, $custom_field_filter);
    ?>
    <input type="hidden" name="search_fields_sidebar" value='<?php echo json_encode($jobs_search_fields_sidebar); ?>'>
    <input type="hidden" name="search_custom_fields_sidebar" value='<?php echo json_encode($jobs_search_custom_fields_sidebar, JSON_FORCE_OBJECT); ?>'>
    <input type="hidden" name="current_term" value="<?php echo esc_attr($term_id); ?>">
    <input type="hidden" name="type_term" value="<?php echo esc_attr($taxonomy_name); ?>">
    <input type="hidden" name="title" value="<?php echo esc_attr($key); ?>">
    <input type="hidden" name="jobs_location" value="<?php echo esc_attr($location); ?>">
<?php
        }

        /**
         * archive company top filter
         */
        function archive_company_top_filter()
        {
            wp_enqueue_script('jquery-ui-autocomplete');
            wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'search-autocomplete');

            $company_search_fields = felan_get_option('company_search_fields');
            $company_search_fields_top = isset($company_search_fields['top']) ? $company_search_fields['top'] : array();
            unset($company_search_fields_top['__no_value__']);

            $search_color = $search_image = '';
            $enable_company_search_bg = felan_get_option('enable_company_search_bg');
            $enable_company_search_location = felan_get_option('enable_company_search_location_top', '1');
            $company_search_color = felan_get_option('company_search_color');
            $company_search_image = felan_get_option('company_search_image');
            $enable_company_search_location_radius = felan_get_option('enable_company_search_location_radius');
            $enable_company_search_bg = !empty($_GET['has_bg']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_bg'])) : $enable_company_search_bg;
            $felan_distance_unit = felan_get_option('felan_distance_unit', 'km');

            if ($enable_company_search_bg == 1) {
                $class_inner = 'has-bg';
            } else {
                $class_inner = '';
            }
            if (!empty($company_search_color)) {
                $search_image = 'background-color :' . $company_search_color . ';';
            }
            if (!empty($company_search_image['url'])) {
                $search_image = "background-image : url({$company_search_image['url']})";
            }
?>
    <div class="archive-company-top archive-filter-top <?php echo $class_inner; ?>" <?php if ($enable_company_search_bg == 1) { ?> style="<?php echo $search_image ?>" <?php } ?>>
        <div class="container">
            <h2><?php esc_html_e('Companies Hiring Internationally', 'felan-framework'); ?></h2>
            <form method="post" class="form-company-top-filter form-archive-top-filter">
                <div class="row">
                    <?php $company_categories = array();
                    $taxonomy_categories = get_categories(
                        array(
                            'taxonomy' => 'company-categories',
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'hide_empty' => false,
                            'parent' => 0
                        )
                    );
                    if (!empty($taxonomy_categories)) {
                        foreach ($taxonomy_categories as $term) {
                            $company_categories[] = $term->name;
                        }
                    }
                    $company_keyword = json_encode($company_categories);
                    $id = apply_filters('felan/search-control/id', 'company_filter_search');
                    ?>
                    <div class="form-group">
                        <input class="company-search-control archive-search-control" data-key='<?php echo $company_keyword ?>' id="<?php echo esc_attr($id); ?>" type="text" name="company_filter_search" placeholder="<?php esc_attr_e('Company title or keywords', 'felan-framework') ?>" autocomplete="off">
                        <span class="btn-filter-search"><i class="far fa-search"></i></span>
                    </div>
                    <?php if ($enable_company_search_location === '1') { ?>
                        <div class="form-group felan-form-location">
                            <input class="archive-search-location" type="text" name="company-search-location" placeholder="<?php esc_attr_e('All Cities', 'felan-framework') ?>" value="<?php if (isset($_GET['company-location']) && $_GET['company-location'] != '') {
                                                                                                                                                                                            echo felan_clean(wp_unslash($_GET['company-location']));
                                                                                                                                                                                        } ?>">
                            <select name="company-location-top" class="felan-select2 hide">
                                <?php felan_get_taxonomy('felan_location', false, false); ?>
                            </select>
                            <span class="icon-location">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_8969_23265)">
                                        <path d="M13 1L13.001 4.062C14.7632 4.28479 16.4013 5.08743 17.6572 6.34351C18.9131 7.5996 19.7155 9.23775 19.938 11H23V13L19.938 13.001C19.7153 14.7631 18.9128 16.401 17.6569 17.6569C16.401 18.9128 14.7631 19.7153 13.001 19.938L13 23H11V19.938C9.23775 19.7155 7.5996 18.9131 6.34351 17.6572C5.08743 16.4013 4.28479 14.7632 4.062 13.001L1 13V11H4.062C4.28459 9.23761 5.08713 7.59934 6.34324 6.34324C7.59934 5.08713 9.23761 4.28459 11 4.062V1H13ZM12 6C10.4087 6 8.88258 6.63214 7.75736 7.75736C6.63214 8.88258 6 10.4087 6 12C6 13.5913 6.63214 15.1174 7.75736 16.2426C8.88258 17.3679 10.4087 18 12 18C13.5913 18 15.1174 17.3679 16.2426 16.2426C17.3679 15.1174 18 13.5913 18 12C18 10.4087 17.3679 8.88258 16.2426 7.75736C15.1174 6.63214 13.5913 6 12 6ZM12 10C12.5304 10 13.0391 10.2107 13.4142 10.5858C13.7893 10.9609 14 11.4696 14 12C14 12.5304 13.7893 13.0391 13.4142 13.4142C13.0391 13.7893 12.5304 14 12 14C11.4696 14 10.9609 13.7893 10.5858 13.4142C10.2107 13.0391 10 12.5304 10 12C10 11.4696 10.2107 10.9609 10.5858 10.5858C10.9609 10.2107 11.4696 10 12 10Z" fill="#999999" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_8969_23265">
                                            <rect width="24" height="24" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <span class="icon-arrow">
                                <i class="far fa-angle-down"></i>
                            </span>
                            <?php if ($enable_company_search_location_radius == 1) { ?>
                                <span class="radius">
                                    <span class="labels"><?php esc_html_e('Radius:', 'felan-framework') ?></span>
                                    <input type="number" name="company_number_radius" value="" placeholder="0" />
                                    <span class="distance"><?php echo esc_html($felan_distance_unit); ?></span>
                                </span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php if ($company_search_fields_top) : foreach ($company_search_fields_top as $field => $v) {
                            switch ($field) {
                                case 'company-rating':
                                    $company_search_icon_ratting = felan_get_option('company_search_fields_company-rating'); ?>
                                    <div class="form-group">
                                        <select name="company-rating" class="felan-select2">
                                            <option value=""><?php echo esc_html__('All Rating', 'felan-framework'); ?></option>
                                            <option value="rating_five"><?php echo esc_html__('Five Star', 'felan-framework'); ?></option>
                                            <option value="rating_four"><?php echo esc_html__('Four Star', 'felan-framework'); ?></option>
                                            <option value="rating_three"><?php echo esc_html__('Three Star', 'felan-framework'); ?></option>
                                            <option value="rating_two"><?php echo esc_html__('Two Star', 'felan-framework'); ?></option>
                                            <option value="rating_one"><?php echo esc_html__('One Star', 'felan-framework'); ?></option>
                                        </select>
                                        <?php echo $company_search_icon_ratting; ?>
                                    </div>
                                <?php break;
                                case 'company-size':
                                    $company_search_icon_size = felan_get_option('company_search_fields_company-size');
                                ?>
                                    <div class="form-group">
                                        <select name="company-size" class="felan-select2">
                                            <?php echo '<option value="">' . esc_html__('All Size', 'felan-framework') . '</option>'; ?>
                                            <?php felan_get_taxonomy('company-size', false, false); ?>
                                        </select>
                                        <?php echo $company_search_icon_size; ?>
                                    </div>
                                <?php break;
                                case 'company-location':
                                    felan_content_option_taxonomy('company', 'top');
                                    break;
                                case 'company-categories':
                                    $company_search_icon_categories = felan_get_option('company_search_fields_company-categories');
                                ?>
                                    <div class="form-group">
                                        <select name="company-categories" class="felan-select2">
                                            <?php echo '<option value="">' . esc_html__('All Categories', 'felan-framework') . '</option>'; ?>
                                            <?php felan_get_taxonomy('company-categories', false, false); ?>
                                        </select>
                                        <?php echo $company_search_icon_categories; ?>
                                    </div>
                                <?php break;
                                case 'company-founded':
                                    $company_search_icon_founded = felan_get_option('company_search_fields_company-founded');
                                ?>
                                    <div class="form-group">
                                        <select name="company-founded" class="felan-select2">
                                            <?php echo '<option value="">' . esc_html__('All Founded', 'felan-framework') . '</option>'; ?>
                                            <?php echo felan_get_company_founded(true) ?>
                                        </select>
                                        <?php echo $company_search_icon_founded; ?>
                                    </div>
                    <?php break;
                            }
                            get_search_filter_custom('company', $field);
                        }
                    endif;
                    ?>

                    <div class="form-group">
                        <span class="felan-clear-top-filter"><?php esc_html_e('Clear', 'felan-framework') ?></span>
                        <input type="hidden" name="has_map" value="<?php if (isset($_GET['has_map']) && $_GET['has_map'] == '1') {
                                                                        echo '1';
                                                                    } else {
                                                                        echo '0';
                                                                    } ?>">
                        <button type="submit" class="btn-top-filter felan-button" name="company-top-filter">
                            <?php esc_html_e('Search', 'felan-framework') ?>
                            <span class="btn-loading"><i class="far fa-spinner fa-spin medium"></i></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php }

        /**
         * archive company sidebar filter
         */
        function archive_company_sidebar_filter($current_term, $total_post)
        {
            wp_enqueue_script('jquery-ui-slider');
            $filter_classes = array();
            $taxonomy_name = get_query_var('taxonomy');
            $term_id = '';
            if ($current_term) {
                $term_id = $current_term->term_id;
            }
            $company_search_fields = felan_get_option('company_search_fields');
            $company_search_fields_sidebar = isset($company_search_fields['sidebar']) ? $company_search_fields['sidebar'] : array();
            unset($company_search_fields_sidebar['__no_value__']);
?>
    <div class="archive-filter <?php echo join(' ', $filter_classes); ?>">
        <div class="bg-overlay"></div>
        <div class="inner-filter custom-scrollbar">
            <div class="felan-nav-filter">
                <div class="felan-filter-toggle">
                    <span><?php esc_html_e('Filter', 'felan-framework'); ?></span>
                </div>
                <div class="felan-clear-filter">
                    <i class="far fa-sync"></i>
                    <span><?php esc_html_e('Clear All', 'felan-framework'); ?></span>
                </div>
            </div>
            <div class="felan-menu-filter">
                <?php
                if ($company_search_fields_sidebar) : foreach ($company_search_fields_sidebar as $field => $v) {
                        switch ($field) {
                            case 'company-rating': ?>
                                <div class="filter-rating">
                                    <div class="entry-filter">
                                        <h4><?php esc_html_e('Rating', 'felan-framework'); ?></h4>
                                        <ul class="rating filter-control custom-scrollbar">
                                            <li>
                                                <input type="checkbox" id="company_rating_five" class="custom-checkbox input-control" name="company_rating[]" value="rating_five" />
                                                <label for="company_rating_five">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="company_rating_four" class="custom-checkbox input-control" name="company_rating[]" value="rating_four" />
                                                <label for="company_rating_four">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="company_rating_three" class="custom-checkbox input-control" name="company_rating[]" value="rating_three" />
                                                <label for="company_rating_three">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="company_rating_two" class="custom-checkbox input-control" name="company_rating[]" value="rating_two" />
                                                <label for="company_rating_two">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="company_rating_one" class="custom-checkbox input-control" name="company_rating[]" value="rating_one" />
                                                <label for="company_rating_one">
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            <?php break;
                            case 'company-size':
                                $title = esc_html__('Size', 'felan-framework');
                                get_search_filter_submenu('company-size', $title, true, true, 'company_size_order');
                                break;
                            case 'company-location': ?>
                                <div class="entry-filter entry-filter-locations">
                                    <h4><?php esc_html_e('Locations', 'felan-framework'); ?></h4>
                                    <div class="locations-filter">
                                        <?php felan_content_option_taxonomy('company'); ?>
                                    </div>
                                </div>
                            <?php break;
                            case 'company-categories':
                                $title = esc_html__('Categories', 'felan-framework');
                                get_search_filter_submenu('company-categories', $title);
                                break;
                            case 'company-founded':
                            ?>
                                <div class="filter-founded">
                                    <div class="entry-filter">
                                        <h4><?php esc_html_e('Founded Date', 'felan-framework'); ?></h4>
                                        <div id="range-slider">
                                            <div id="slider-range"></div>
                                            <p><input type="text" id="amount" readonly></p>
                                        </div>
                                    </div>
                                </div>
                <?php
                                break;
                        }

                        get_search_filter_custom('company', $field);
                    }
                endif;
                ?>

            </div>
        </div>
        <div class="show-result">
            <a href="#" class="felan-button button-block">
                <span><?php echo esc_html__('Show', 'felan-framework'); ?></span>
                <span class="result-count">

					<?php if (!empty($key)) { ?>
						<?php
							printf(
								esc_html(_n('%1$s company for "%2$s"', '%1$s companies for "%2$s"', $total_post, 'felan-framework')),
								'<span>' . $total_post . '</span>',
								$key
							);
						?>
					<?php } else { ?>
						<?php
							echo sprintf(
								_n( '%s company', '%s companies', $total_post, 'felan-framework' ),
								'<span>' . $total_post . '</span>'
							);
						?>
					<?php } ?>

                </span>
            </a>
        </div>
        <?php
            $custom_field_filter = felan_render_custom_field('company');
            $company_search_custom_fields_sidebar = array_map(function ($value) {
                return $value['id'];
            }, $custom_field_filter);
        ?>
        <input type="hidden" name="search_fields_sidebar" value='<?php echo json_encode($company_search_fields_sidebar); ?>'>
        <input type="hidden" name="search_custom_fields_sidebar" value='<?php echo json_encode($company_search_custom_fields_sidebar, JSON_FORCE_OBJECT); ?>'>
        <input type="hidden" name="current_term" value="<?php echo esc_attr($term_id); ?>">
        <input type="hidden" name="type_term" value="<?php echo esc_attr($taxonomy_name); ?>">
    </div>
<?php
        }

        /**
         * Archive freelancer top filter
         */
        function archive_freelancer_top_filter()
        {
            wp_enqueue_script('jquery-ui-autocomplete');
            wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'search-autocomplete');
            $freelancer_search_fields = felan_get_option('freelancer_search_fields');
            $freelancer_search_fields_top = isset($freelancer_search_fields['top']) ? $freelancer_search_fields['top'] : array();
            unset($freelancer_search_fields_top['__no_value__']);

            $search_color = $search_image = $search_title_color = '';

            $enable_freelancer_search_bg = felan_get_option('enable_freelancer_search_bg');
            $enable_freelancer_search_location = felan_get_option('enable_freelancer_search_location_top', '1');
            $freelancer_search_color = felan_get_option('freelancer_search_color');
            $freelancer_search_image = felan_get_option('freelancer_search_image');
            $enable_freelancer_search_location_radius = felan_get_option('enable_freelancer_search_location_radius');
            $enable_freelancer_search_bg = !empty($_GET['has_bg']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_bg'])) : $enable_freelancer_search_bg;
            $felan_distance_unit = felan_get_option('felan_distance_unit', 'km');
            $enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
            $enable_post_type_service = felan_get_option('enable_post_type_service', '1');
            $enable_post_type_project = felan_get_option('enable_post_type_project', '1');

            if ($enable_freelancer_search_bg == 1) {
                $class_inner = 'has-bg';
            } else {
                $class_inner = '';
            }
            if (!empty($freelancer_search_color)) {
                $search_image = 'background-color :' . $freelancer_search_color . ';';
            }
            if (!empty($freelancer_search_image['url'])) {
                $search_image = "background-image : url({$freelancer_search_image['url']})";
            }
            $freelancer_search_color = felan_get_option('freelancer_search_color');

            if (!empty($freelancer_search_color)) {
                $search_title_color = 'color :' . $freelancer_search_color . ';';
            }
?>
    <div class="archive-freelancer-top archive-filter-top <?php echo $class_inner; ?>" <?php if ($enable_freelancer_search_bg == 1) { ?> style="<?php echo $search_image ?>" <?php } ?>>
        <div class="container">
            <?php if ($enable_post_type_jobs == '1' && $enable_post_type_service !== '1' && $enable_post_type_project !== '1') { ?>
                <h2><?php esc_html_e('Candidate Listing', 'felan-framework'); ?></h2>
            <?php } else { ?>
                <h2><?php esc_html_e('Freelancers Listing', 'felan-framework'); ?></h2>
            <?php } ?>
            <form method="post" class="form-freelancer-top-filter form-archive-top-filter">
                <div class="row">
                    <?php $freelancer_skills = array();
                    $taxonomy_skills = get_categories(
                        array(
                            'taxonomy' => 'freelancer_skills',
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'hide_empty' => false,
                            'parent' => 0
                        )
                    );
                    if (!empty($taxonomy_skills)) {
                        foreach ($taxonomy_skills as $term) {
                            $freelancer_skills[] = $term->name;
                        }
                    }
                    $freelancer_keyword = json_encode($freelancer_skills);
                    ?>
                    <div class="form-group">
                        <?php if ($enable_post_type_jobs == '1' && $enable_post_type_service !== '1' && $enable_post_type_project !== '1') { ?>
                            <input class="freelancer-search-control archive-search-control" data-key='<?php echo $freelancer_keyword ?>' id="freelancer_filter_search" type="text" name="freelancer_filter_search" placeholder="<?php esc_attr_e('Candidate title or keywords', 'felan-framework') ?>" autocomplete="off">
                        <?php } else { ?>
                            <input class="freelancer-search-control archive-search-control" data-key='<?php echo $freelancer_keyword ?>' id="freelancer_filter_search" type="text" name="freelancer_filter_search" placeholder="<?php esc_attr_e('Freelancer title or keywords', 'felan-framework') ?>" autocomplete="off">
                        <?php } ?>
                        <span class="btn-filter-search"><i class="far fa-search"></i></span>
                    </div>
                    <?php if ($enable_freelancer_search_location === '1') { ?>
                        <div class="form-group felan-form-location">
                            <input class="archive-search-location" type="text" name="freelancer-search-location" placeholder="<?php esc_attr_e('All Cities', 'felan-framework') ?>" value="<?php if (isset($_GET['freelancer-location']) && $_GET['freelancer-location'] != '') {
                                                                                                                                                                                                echo felan_clean(wp_unslash($_GET['freelancer-location']));
                                                                                                                                                                                            } ?>">
                            <select name="freelancer-location-top" class="felan-select2 hide">
                                <?php felan_get_taxonomy('felan_location', false, false); ?>
                            </select>
                            <span class="icon-location">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_8969_23265)">
                                        <path d="M13 1L13.001 4.062C14.7632 4.28479 16.4013 5.08743 17.6572 6.34351C18.9131 7.5996 19.7155 9.23775 19.938 11H23V13L19.938 13.001C19.7153 14.7631 18.9128 16.401 17.6569 17.6569C16.401 18.9128 14.7631 19.7153 13.001 19.938L13 23H11V19.938C9.23775 19.7155 7.5996 18.9131 6.34351 17.6572C5.08743 16.4013 4.28479 14.7632 4.062 13.001L1 13V11H4.062C4.28459 9.23761 5.08713 7.59934 6.34324 6.34324C7.59934 5.08713 9.23761 4.28459 11 4.062V1H13ZM12 6C10.4087 6 8.88258 6.63214 7.75736 7.75736C6.63214 8.88258 6 10.4087 6 12C6 13.5913 6.63214 15.1174 7.75736 16.2426C8.88258 17.3679 10.4087 18 12 18C13.5913 18 15.1174 17.3679 16.2426 16.2426C17.3679 15.1174 18 13.5913 18 12C18 10.4087 17.3679 8.88258 16.2426 7.75736C15.1174 6.63214 13.5913 6 12 6ZM12 10C12.5304 10 13.0391 10.2107 13.4142 10.5858C13.7893 10.9609 14 11.4696 14 12C14 12.5304 13.7893 13.0391 13.4142 13.4142C13.0391 13.7893 12.5304 14 12 14C11.4696 14 10.9609 13.7893 10.5858 13.4142C10.2107 13.0391 10 12.5304 10 12C10 11.4696 10.2107 10.9609 10.5858 10.5858C10.9609 10.2107 11.4696 10 12 10Z" fill="#999999" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_8969_23265">
                                            <rect width="24" height="24" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <span class="icon-arrow">
                                <i class="far fa-angle-down"></i>
                            </span>
                            <?php if ($enable_freelancer_search_location_radius == 1) { ?>
                                <span class="radius">
                                    <span class="labels"><?php esc_html_e('Radius:', 'felan-framework') ?></span>
                                    <input type="number" name="freelancer_number_radius" value="" placeholder="0" />
                                    <span class="distance"><?php echo esc_html($felan_distance_unit); ?></span>
                                </span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php if ($freelancer_search_fields_top) : foreach ($freelancer_search_fields_top as $field => $v) {
                            switch ($field) {
                                case 'freelancer_rating':
                                    $freelancer_search_icon_ratting = felan_get_option('freelancer_search_fields_freelancer_rating'); ?>
                                    <div class="form-group">
                                        <select name="freelancer_rating" class="felan-select2">
                                            <option value=""><?php echo esc_html__('All Rating', 'felan-framework'); ?></option>
                                            <option value="rating_five"><?php echo esc_html__('Five Star', 'felan-framework'); ?></option>
                                            <option value="rating_four"><?php echo esc_html__('Four Star', 'felan-framework'); ?></option>
                                            <option value="rating_three"><?php echo esc_html__('Three Star', 'felan-framework'); ?></option>
                                            <option value="rating_two"><?php echo esc_html__('Two Star', 'felan-framework'); ?></option>
                                            <option value="rating_one"><?php echo esc_html__('One Star', 'felan-framework'); ?></option>
                                        </select>
                                        <?php echo $freelancer_search_icon_ratting; ?>
                                    </div>
                                <?php break;
                                case 'freelancer_gender':
                                    $freelancer_search_icon_gender = felan_get_option('freelancer_search_fields_freelancer_gender'); ?>
                                    <div class="form-group">
                                        <select name="freelancer_gender" class="felan-select2">
                                            <?php echo '<option value="">' . esc_html__('All Gender', 'felan-framework') . '</option>'; ?>
                                            <?php felan_get_taxonomy('freelancer_gender', false, false); ?>
                                        </select>
                                        <?php echo $freelancer_search_icon_gender; ?>
                                    </div>
                                <?php break;
                                case 'freelancer_locations':
                                    felan_content_option_taxonomy('freelancer', 'top');
                                    break;
                                case 'freelancer_categories':
                                    $freelancer_search_icon_categories = felan_get_option('freelancer_search_fields_freelancer_categories');
                                ?>
                                    <div class="form-group">
                                        <select name="freelancer_categories" class="felan-select2">
                                            <?php echo '<option value="">' . esc_html__('All Categories', 'felan-framework') . '</option>'; ?>
                                            <?php felan_get_taxonomy('freelancer_categories', false, false); ?>
                                        </select>
                                        <?php echo $freelancer_search_icon_categories; ?>
                                    </div>
                                <?php break;
                                case 'freelancer_yoe':
                                    $freelancer_search_icon_yoe = felan_get_option('freelancer_search_fields_freelancer_yoe');
                                ?>
                                    <div class="form-group">
                                        <select name="freelancer_yoe" class="felan-select2">
                                            <?php echo '<option value="">' . esc_html__('All Experience', 'felan-framework') . '</option>'; ?>
                                            <?php felan_get_taxonomy('freelancer_yoe', false, false); ?>
                                        </select>
                                        <?php echo $freelancer_search_icon_yoe; ?>
                                    </div>
                                <?php break;
                                case 'freelancer_qualification':
                                    $freelancer_search_icon_qualification = felan_get_option('freelancer_search_fields_freelancer_qualification');
                                ?>
                                    <div class="form-group">
                                        <select name="freelancer_qualification" class="felan-select2">
                                            <?php echo '<option value="">' . esc_html__('All Qualification', 'felan-framework') . '</option>'; ?>
                                            <?php felan_get_taxonomy('freelancer_qualification', false, false); ?>
                                        </select>
                                        <?php echo $freelancer_search_icon_qualification; ?>
                                    </div>
                                <?php break;
                                case 'freelancer_ages':
                                    $freelancer_search_icon_ages = felan_get_option('freelancer_search_fields_freelancer_ages');
                                ?>
                                    <div class="form-group">
                                        <select name="freelancer_ages" class="felan-select2">
                                            <?php echo '<option value="">' . esc_html__('All Ages', 'felan-framework') . '</option>'; ?>
                                            <?php felan_get_taxonomy('freelancer_ages', false, false); ?>
                                        </select>
                                        <?php echo $freelancer_search_icon_ages; ?>
                                    </div>
                                <?php break;

                                case 'freelancer_skills':
                                    $freelancer_search_icon_skills = felan_get_option('freelancer_search_fields_freelancer_skills');
                                ?>
                                    <div class="form-group">
                                        <select name="freelancer_skills" class="felan-select2">
                                            <?php echo '<option value="">' . esc_html__('All Skills', 'felan-framework') . '</option>'; ?>
                                            <?php felan_get_taxonomy('freelancer_skills', false, false); ?>
                                        </select>
                                        <?php echo $freelancer_search_icon_skills; ?>
                                    </div>
                                <?php break;
                                case 'freelancer_languages':
                                    $freelancer_search_icon_languages = felan_get_option('freelancer_search_fields_freelancer_languages');
                                ?>
                                    <div class="form-group">
                                        <select name="freelancer_languages" class="felan-select2">
                                            <?php echo '<option value="">' . esc_html__('All Languages', 'felan-framework') . '</option>'; ?>
                                            <?php felan_get_taxonomy('freelancer_languages', false, false); ?>
                                        </select>
                                        <?php echo $freelancer_search_icon_languages; ?>
                                    </div>
                    <?php break;
                            }
                            get_search_filter_custom('freelancer', $field);
                        }
                    endif;
                    ?>
                    <div class="form-group">
                        <span class="felan-clear-top-filter"><?php esc_html_e('Clear', 'felan-framework') ?></span>
                        <input type="hidden" name="has_map" value="<?php if (isset($_GET['has_map']) && $_GET['has_map'] == '1') {
                                                                        echo '1';
                                                                    } else {
                                                                        echo '0';
                                                                    } ?>">
                        <button type="submit" class="btn-top-filter felan-button" name="freelancer-top-filter">
                            <?php esc_html_e('Search', 'felan-framework') ?>
                            <span class="btn-loading"><i class="far fa-spinner fa-spin medium"></i></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php }

        /**
         * archive freelancer sidebar filter
         */
        function archive_freelancer_sidebar_filter($current_term, $total_post)
        {
            $enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
            $enable_post_type_service = felan_get_option('enable_post_type_service', '1');
            $enable_post_type_project = felan_get_option('enable_post_type_project', '1');
            $filter_classes = array();
            $taxonomy_name = get_query_var('taxonomy');
            $term_id = '';
            if ($current_term) {
                $term_id = $current_term->term_id;
            }
            $freelancer_search_fields = felan_get_option('freelancer_search_fields');
            $freelancer_search_fields_sidebar = isset($freelancer_search_fields['sidebar']) ? $freelancer_search_fields['sidebar'] : array();
            unset($freelancer_search_fields_sidebar['__no_value__']);
?>
    <div class="archive-filter <?php echo join(' ', $filter_classes); ?>">
        <div class="bg-overlay"></div>
        <div class="inner-filter custom-scrollbar">
            <div class="felan-nav-filter">
                <div class="felan-filter-toggle">
                    <span><?php esc_html_e('Filter', 'felan-framework'); ?></span>
                </div>
                <div class="felan-clear-filter">
                    <i class="far fa-sync"></i>
                    <span><?php esc_html_e('Clear All', 'felan-framework'); ?></span>
                </div>
            </div>
            <div class="felan-menu-filter">
                <?php
                if ($freelancer_search_fields_sidebar) : foreach ($freelancer_search_fields_sidebar as $field => $v) {
                        switch ($field) {
                            case 'freelancer_rating': ?>
                                <div class="filter-rating">
                                    <div class="entry-filter">
                                        <h4><?php esc_html_e('Rating', 'felan-framework'); ?></h4>
                                        <ul class="rating filter-control custom-scrollbar">
                                            <li>
                                                <input type="checkbox" id="freelancer_rating_five" class="custom-checkbox input-control" name="freelancer_rating[]" value="rating_five" />
                                                <label for="freelancer_rating_five">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="freelancer_rating_four" class="custom-checkbox input-control" name="freelancer_rating[]" value="rating_four" />
                                                <label for="freelancer_rating_four">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="freelancer_rating_three" class="custom-checkbox input-control" name="freelancer_rating[]" value="rating_three" />
                                                <label for="freelancer_rating_three">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="freelancer_rating_two" class="custom-checkbox input-control" name="freelancer_rating[]" value="rating_two" />
                                                <label for="freelancer_rating_two">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="freelancer_rating_one" class="custom-checkbox input-control" name="freelancer_rating[]" value="rating_one" />
                                                <label for="freelancer_rating_one">
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            <?php break;

                            case 'freelancer_yoe':
                                $title = esc_html__('Experience Level', 'felan-framework');
                                get_search_filter_submenu('freelancer_yoe', $title);
                                break;

                            case 'freelancer_locations': ?>
                                <div class="entry-filter entry-filter-locations">
                                    <h4><?php esc_html_e('Locations', 'felan-framework'); ?></h4>
                                    <div class="locations-filter">
                                        <?php felan_content_option_taxonomy('freelancer'); ?>
                                    </div>
                                </div>
                <?php break;

                            case 'freelancer_categories':
                                $title = esc_html__('Categories', 'felan-framework');
                                get_search_filter_submenu('freelancer_categories', $title);
                                break;

                            case 'freelancer_yoe':
                                $title = esc_html__('Experience Level', 'felan-framework');
                                get_search_filter_submenu('freelancer_yoe', $title);
                                break;

                            case 'freelancer_qualification':
                                $title = esc_html__('Qualification', 'felan-framework');
                                get_search_filter_submenu('freelancer_qualification', $title);
                                break;

                            case 'freelancer_ages':
                                $title = esc_html__('Ages', 'felan-framework');
                                get_search_filter_submenu('freelancer_ages', $title);
                                break;

                            case 'freelancer_skills':
                                $title = esc_html__('Skills', 'felan-framework');
                                get_search_filter_submenu('freelancer_skills', $title);
                                break;

                            case 'freelancer_languages':
                                $title = esc_html__('Languages', 'felan-framework');
                                get_search_filter_submenu('freelancer_languages', $title);
                                break;

                            case 'freelancer_gender':
                                $title = esc_html__('Gender', 'felan-framework');
                                get_search_filter_submenu('freelancer_gender', $title);
                                break;
                        }
                        get_search_filter_custom('freelancer', $field);
                    }
                endif;
                ?>

            </div>
        </div>
        <div class="show-result">
            <a href="#" class="felan-button button-block">
                <span><?php echo esc_html__('Show', 'felan-framework'); ?></span>
                <?php if ($enable_post_type_jobs == '1' && $enable_post_type_service !== '1' && $enable_post_type_project !== '1') { ?>
                    <span class="result-count">

						<?php if (!empty($key)) { ?>
							<?php
								printf(
									esc_html(_n('%1$s candidate for "%2$s"', '%1$s candidates for "%2$s"', $total_post, 'felan-framework')),
									'<span>' . $total_post . '</span>',
									$key
								);
							?>
						<?php } else { ?>
							<?php
								echo sprintf(
									_n( '%s candidate', '%s candidates', $total_post, 'felan-framework' ),
									'<span>' . $total_post . '</span>'
								);
							?>
						<?php } ?>

                    </span>
                <?php } else { ?>
                    <span class="result-count">
						<?php if (!empty($key)) { ?>
							<?php
								printf(
									esc_html(_n('%1$s freelancer for "%2$s"', '%1$s freelancers for "%2$s"', $total_post, 'felan-framework')),
									'<span>' . $total_post . '</span>',
									$key
								);
							?>
						<?php } else { ?>
							<?php
								echo sprintf(
									_n( '%s freelancer', '%s freelancers', $total_post, 'felan-framework' ),
									'<span>' . $total_post . '</span>'
								);
							?>
						<?php } ?>
                    </span>
                <?php } ?>
            </a>
        </div>

        <?php
            $custom_field_filter = felan_render_custom_field('freelancer');
            $company_search_custom_fields_sidebar = array_map(function ($value) {
                return $value['id'];
            }, $custom_field_filter);
        ?>
        <input type="hidden" name="search_fields_sidebar" value='<?php echo json_encode($freelancer_search_fields_sidebar); ?>'>
        <input type="hidden" name="search_custom_fields_sidebar" value='<?php echo json_encode($company_search_custom_fields_sidebar, JSON_FORCE_OBJECT); ?>'>
        <input type="hidden" name="current_term" value="<?php echo esc_attr($term_id); ?>">
        <input type="hidden" name="type_term" value="<?php echo esc_attr($taxonomy_name); ?>">
    </div>
<?php
        }

        /**
         * archive service top filter
         */
        function archive_service_top_filter()
        {
            wp_enqueue_script('jquery-ui-autocomplete');
            wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'search-autocomplete');

            $service_search_fields = felan_get_option('service_search_fields');
            $service_search_fields_top = isset($service_search_fields['top']) ? $service_search_fields['top'] : array();
            unset($service_search_fields_top['__no_value__']);

            $search_color = $search_image = '';
            $enable_service_search_bg = felan_get_option('enable_service_search_bg');
            $enable_service_search_location = felan_get_option('enable_service_search_location_top', '1');
            $service_search_color = felan_get_option('service_search_color');
            $service_search_image = felan_get_option('service_search_image');
            $enable_service_search_location_radius = felan_get_option('enable_service_search_location_radius');
            $enable_service_search_bg = !empty($_GET['has_bg']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_bg'])) : $enable_service_search_bg;
            $felan_distance_unit = felan_get_option('felan_distance_unit', 'km');

            if ($enable_service_search_bg == 1) {
                $class_inner = 'has-bg';
            } else {
                $class_inner = '';
            }
            if (!empty($service_search_color)) {
                $search_image = 'background-color :' . $service_search_color . ';';
            }
            if (!empty($service_search_image['url'])) {
                $search_image = "background-image : url({$service_search_image['url']})";
            }
?>
    <div class="archive-service-top archive-filter-top <?php echo $class_inner; ?>" <?php if ($enable_service_search_bg == 1) { ?> style="<?php echo $search_image ?>" <?php } ?>>
        <div class="container">
            <h2><?php esc_html_e('Service Listing', 'felan-framework'); ?></h2>
            <form method="post" class="form-service-top-filter form-archive-top-filter">
                <div class="row">
                    <?php $service_skills = array();
                    $taxonomy_skills = get_categories(
                        array(
                            'taxonomy' => 'service-skills',
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'hide_empty' => false,
                            'parent' => 0
                        )
                    );
                    if (!empty($taxonomy_skills)) {
                        foreach ($taxonomy_skills as $term) {
                            $service_skills[] = $term->name;
                        }
                    }
                    $service_keyword = json_encode($service_skills);
                    $id = apply_filters('felan/search-control/id', 'service_filter_search');
                    ?>
                    <div class="form-group">
                        <input class="service-search-control archive-search-control" data-key='<?php echo $service_keyword ?>' id="<?php echo esc_attr($id); ?>" type="text" name="service_filter_search" placeholder="<?php esc_attr_e('Service title or keywords', 'felan-framework') ?>" autocomplete="off">
                        <span class="btn-filter-search"><i class="far fa-search"></i></span>
                    </div>
                    <?php if ($enable_service_search_location === '1') { ?>
                        <div class="form-group felan-form-location">
                            <input class="archive-search-location" type="text" name="service-search-location" placeholder="<?php esc_attr_e('All Cities', 'felan-framework') ?>" value="<?php if (isset($_GET['service-location']) && $_GET['service-location'] != '') {
                                                                                                                                                                                            echo felan_clean(wp_unslash($_GET['service-location']));
                                                                                                                                                                                        } ?>">
                            <select name="service-location-top" class="felan-select2 hide">
                                <?php felan_get_taxonomy('felan_location', false, false); ?>
                            </select>
                            <span class="icon-location">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_8969_23265)">
                                        <path d="M13 1L13.001 4.062C14.7632 4.28479 16.4013 5.08743 17.6572 6.34351C18.9131 7.5996 19.7155 9.23775 19.938 11H23V13L19.938 13.001C19.7153 14.7631 18.9128 16.401 17.6569 17.6569C16.401 18.9128 14.7631 19.7153 13.001 19.938L13 23H11V19.938C9.23775 19.7155 7.5996 18.9131 6.34351 17.6572C5.08743 16.4013 4.28479 14.7632 4.062 13.001L1 13V11H4.062C4.28459 9.23761 5.08713 7.59934 6.34324 6.34324C7.59934 5.08713 9.23761 4.28459 11 4.062V1H13ZM12 6C10.4087 6 8.88258 6.63214 7.75736 7.75736C6.63214 8.88258 6 10.4087 6 12C6 13.5913 6.63214 15.1174 7.75736 16.2426C8.88258 17.3679 10.4087 18 12 18C13.5913 18 15.1174 17.3679 16.2426 16.2426C17.3679 15.1174 18 13.5913 18 12C18 10.4087 17.3679 8.88258 16.2426 7.75736C15.1174 6.63214 13.5913 6 12 6ZM12 10C12.5304 10 13.0391 10.2107 13.4142 10.5858C13.7893 10.9609 14 11.4696 14 12C14 12.5304 13.7893 13.0391 13.4142 13.4142C13.0391 13.7893 12.5304 14 12 14C11.4696 14 10.9609 13.7893 10.5858 13.4142C10.2107 13.0391 10 12.5304 10 12C10 11.4696 10.2107 10.9609 10.5858 10.5858C10.9609 10.2107 11.4696 10 12 10Z" fill="#999999" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_8969_23265">
                                            <rect width="24" height="24" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <span class="icon-arrow">
                                <i class="far fa-angle-down"></i>
                            </span>
                            <?php if ($enable_service_search_location_radius == 1) { ?>
                                <span class="radius">
                                    <span class="labels"><?php esc_html_e('Radius:', 'felan-framework') ?></span>
                                    <input type="number" name="service_number_radius" value="" placeholder="0" />
                                    <span class="distance"><?php echo esc_html($felan_distance_unit); ?></span>
                                </span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php if ($service_search_fields_top) : foreach ($service_search_fields_top as $field => $v) {
                            switch ($field) {
                                case 'service-rating':
                                    $service_search_icon_ratting = felan_get_option('service_search_fields_service-rating'); ?>
                                    <div class="form-group">
                                        <select name="service-rating" class="felan-select2">
                                            <option value=""><?php echo esc_html__('All Rating', 'felan-framework'); ?></option>
                                            <option value="rating_five"><?php echo esc_html__('Five Star', 'felan-framework'); ?></option>
                                            <option value="rating_four"><?php echo esc_html__('Four Star', 'felan-framework'); ?></option>
                                            <option value="rating_three"><?php echo esc_html__('Three Star', 'felan-framework'); ?></option>
                                            <option value="rating_two"><?php echo esc_html__('Two Star', 'felan-framework'); ?></option>
                                            <option value="rating_one"><?php echo esc_html__('One Star', 'felan-framework'); ?></option>
                                        </select>
                                        <?php echo $service_search_icon_ratting; ?>
                                    </div>
                                <?php break;
                                case 'service-skills':
                                    $service_search_icon_skills = felan_get_option('service_search_fields_service-skills');
                                ?>
                                    <div class="form-group">
                                        <select name="service-skills" class="felan-select2">
                                            <?php echo '<option value="">' . esc_html__('All Skills', 'felan-framework') . '</option>'; ?>
                                            <?php felan_get_taxonomy('service-skills', false, false); ?>
                                        </select>
                                        <?php echo $service_search_icon_skills; ?>
                                    </div>
                                <?php break;
                                case 'service-location':
                                    felan_content_option_taxonomy('service', 'top');
                                    break;
                                case 'service-categories':
                                    $service_search_icon_categories = felan_get_option('service_search_fields_service-categories');
                                ?>
                                    <div class="form-group">
                                        <select name="service-categories" class="felan-select2">
                                            <?php echo '<option value="">' . esc_html__('All Categories', 'felan-framework') . '</option>'; ?>
                                            <?php felan_get_taxonomy('service-categories', false, false); ?>
                                        </select>
                                        <?php echo $service_search_icon_categories; ?>
                                    </div>
                                <?php break;
                                case 'service-language':
                                    $service_search_icon_language = felan_get_option('service_search_fields_service-language');
                                ?>
                                    <div class="form-group">
                                        <select name="service-language" class="felan-select2">
                                            <?php echo '<option value="">' . esc_html__('All Languages', 'felan-framework') . '</option>'; ?>
                                            <?php felan_get_taxonomy('service-language', false, false); ?>
                                        </select>
                                        <?php echo $service_search_icon_language; ?>
                                    </div>
                    <?php break;
                            }
                            get_search_filter_custom('service', $field);
                        }
                    endif;
                    ?>

                    <div class="form-group">
                        <span class="felan-clear-top-filter"><?php esc_html_e('Clear', 'felan-framework') ?></span>
                        <input type="hidden" name="has_map" value="<?php if (isset($_GET['has_map']) && $_GET['has_map'] == '1') {
                                                                        echo '1';
                                                                    } else {
                                                                        echo '0';
                                                                    } ?>">
                        <button type="submit" class="btn-top-filter felan-button" name="service-top-filter">
                            <?php esc_html_e('Search', 'felan-framework') ?>
                            <span class="btn-loading"><i class="far fa-spinner fa-spin medium"></i></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php }

        /**
         * archive service sidebar filter
         */
        function archive_service_sidebar_filter($current_term, $total_post)
        {
            wp_enqueue_script('jquery-ui-slider');
            $currency_sign_default = felan_get_option('currency_sign_default');
            $filter_classes = array();
            $taxonomy_name = get_query_var('taxonomy');
            $term_id = '';
            if ($current_term) {
                $term_id = $current_term->term_id;
            }
            $service_search_fields = felan_get_option('service_search_fields');
            $service_search_fields_sidebar = isset($service_search_fields['sidebar']) ? $service_search_fields['sidebar'] : array();
            unset($service_search_fields_sidebar['__no_value__']);
?>
    <div class="archive-filter <?php echo join(' ', $filter_classes); ?>">
        <div class="bg-overlay"></div>
        <div class="inner-filter custom-scrollbar">
            <div class="felan-nav-filter">
                <div class="felan-filter-toggle">
                    <span><?php esc_html_e('Filter', 'felan-framework'); ?></span>
                </div>
                <div class="felan-clear-filter">
                    <i class="far fa-sync"></i>
                    <span><?php esc_html_e('Clear All', 'felan-framework'); ?></span>
                </div>
            </div>
            <div class="felan-menu-filter">
                <?php
                if ($service_search_fields_sidebar) : foreach ($service_search_fields_sidebar as $field => $v) {
                        switch ($field) {
                            case 'service-rating': ?>
                                <div class="filter-rating">
                                    <div class="entry-filter">
                                        <h4><?php esc_html_e('Rating', 'felan-framework'); ?></h4>
                                        <ul class="rating filter-control custom-scrollbar">
                                            <li>
                                                <input type="checkbox" id="service_rating_five" class="custom-checkbox input-control" name="service_rating[]" value="rating_five" />
                                                <label for="service_rating_five">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="service_rating_four" class="custom-checkbox input-control" name="service_rating[]" value="rating_four" />
                                                <label for="service_rating_four">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="service_rating_three" class="custom-checkbox input-control" name="service_rating[]" value="rating_three" />
                                                <label for="service_rating_three">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="service_rating_two" class="custom-checkbox input-control" name="service_rating[]" value="rating_two" />
                                                <label for="service_rating_two">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="service_rating_one" class="custom-checkbox input-control" name="service_rating[]" value="rating_one" />
                                                <label for="service_rating_one">
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            <?php break;
                            case 'service-price':
                            ?>
                                <div class="filter-price">
                                    <div class="entry-filter">
                                        <h4><?php esc_html_e('Price', 'felan-framework'); ?></h4>
                                        <div id="range-slider">
                                            <div id="slider-range" data-currency="<?php echo $currency_sign_default; ?>"></div>
                                            <p><input type="text" id="amount" readonly></p>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                break;
                                ?>
                                <div class="filter-language-level">
                                    <div class="entry-filter">
                                        <h4><?php esc_html_e('Languages Level', 'felan-framework'); ?></h4>
                                        <ul class="filter-control custom-scrollbar">
                                            <?php foreach ($list_language as $keys => $value) { ?>
                                                <li>
                                                    <input type="checkbox" id="service_language_<?php echo $keys; ?>" class="custom-checkbox input-control" name="service_language_level[]" value="<?php echo $keys; ?>" />
                                                    <label for="service_language_<?php echo $keys; ?>">
                                                        <?php echo $value; ?><span class="count">(<?php echo felan_field_count($keys, FELAN_METABOX_PREFIX . 'service_language_level', 'service'); ?>
                                                            )</span>
                                                    </label>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php break;
                            case 'service-location': ?>
                                <div class="entry-filter entry-filter-locations">
                                    <h4><?php esc_html_e('Locations', 'felan-framework'); ?></h4>
                                    <div class="locations-filter">
                                        <?php felan_content_option_taxonomy('service'); ?>
                                    </div>
                                </div>
                <?php break;
                            case 'service-categories':
                                $title = esc_html__('Categories', 'felan-framework');
                                get_search_filter_submenu('service-categories', $title);
                                break;
                            case 'service-skills':
                                $title = esc_html__('Skills', 'felan-framework');
                                get_search_filter_submenu('service-skills', $title);
                                break;
                            case 'service-language':
                                $title = esc_html__('Language', 'felan-framework');
                                get_search_filter_submenu('service-language', $title);
                                break;
                        }
                        get_search_filter_custom('service', $field);
                    }
                endif;
                ?>
            </div>
        </div>
        <div class="show-result">
            <a href="#" class="felan-button button-block">
                <span><?php echo esc_html__('Show', 'felan-framework'); ?></span>
                <span class="result-count">
					<?php if (!empty($key)) { ?>
						<?php
							printf(
								esc_html(_n('%1$s company for "%2$s"', '%1$s companies for "%2$s"', $total_post, 'felan-framework')),
								'<span>' . $total_post . '</span>',
								$key
							);
						?>
					<?php } else { ?>
						<?php
							echo sprintf(
								_n( '%s company', '%s companies', $total_post, 'felan-framework' ),
								'<span>' . $total_post . '</span>'
							);
						?>
					<?php } ?>
                </span>
            </a>
        </div>

        <?php
            $custom_field_filter = felan_render_custom_field('service');
            $company_search_custom_fields_sidebar = array_map(function ($value) {
                return $value['id'];
            }, $custom_field_filter);
        ?>
        <input type="hidden" name="search_fields_sidebar" value='<?php echo json_encode($service_search_fields_sidebar); ?>'>
        <input type="hidden" name="search_custom_fields_sidebar" value='<?php echo json_encode($company_search_custom_fields_sidebar, JSON_FORCE_OBJECT); ?>'>
        <input type="hidden" name="current_term" value="<?php echo esc_attr($term_id); ?>">
        <input type="hidden" name="type_term" value="<?php echo esc_attr($taxonomy_name); ?>">
    </div>
<?php
        }

        /**
         * archive project top filter
         */
        function archive_project_top_filter()
        {
            wp_enqueue_script('jquery-ui-autocomplete');
            wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'search-autocomplete');

            $project_search_fields = felan_get_option('project_search_fields');
            $project_search_fields_top = isset($project_search_fields['top']) ? $project_search_fields['top'] : array();
            unset($project_search_fields_top['__no_value__']);

            $search_color = $search_image = '';
            $enable_project_search_bg = felan_get_option('enable_project_search_bg');
            $enable_project_search_location = felan_get_option('enable_project_search_location_top', '1');
            $project_search_color = felan_get_option('project_search_color');
            $project_search_image = felan_get_option('project_search_image');
            $enable_project_search_location_radius = felan_get_option('enable_project_search_location_radius');
            $enable_project_search_bg = !empty($_GET['has_bg']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_bg'])) : $enable_project_search_bg;
            $felan_distance_unit = felan_get_option('felan_distance_unit', 'km');

            if ($enable_project_search_bg == 1) {
                $class_inner = 'has-bg';
            } else {
                $class_inner = '';
            }
            if (!empty($project_search_color)) {
                $search_image = 'background-color :' . $project_search_color . ';';
            }
            if (!empty($project_search_image['url'])) {
                $search_image = "background-image : url({$project_search_image['url']})";
            }
?>
    <div class="archive-project-top archive-filter-top <?php echo $class_inner; ?>" <?php if ($enable_project_search_bg == 1) { ?> style="<?php echo $search_image ?>" <?php } ?>>
        <div class="container">
            <h2><?php esc_html_e('Projects Listing', 'felan-framework'); ?></h2>
            <form method="post" class="form-project-top-filter form-archive-top-filter">
                <div class="row">
                    <?php $project_skills = array();
                    $taxonomy_skills = get_categories(
                        array(
                            'taxonomy' => 'project-skills',
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'hide_empty' => false,
                            'parent' => 0
                        )
                    );
                    if (!empty($taxonomy_skills)) {
                        foreach ($taxonomy_skills as $term) {
                            $project_skills[] = $term->name;
                        }
                    }
                    $project_keyword = json_encode($project_skills);
                    $id = apply_filters('felan/search-control/id', 'project_filter_search');
                    ?>
                    <div class="form-group">
                        <input class="project-search-control archive-search-control" data-key='<?php echo $project_keyword ?>' id="<?php echo esc_attr($id); ?>" type="text" name="project_filter_search" placeholder="<?php esc_attr_e('Project title or keywords', 'felan-framework') ?>" autocomplete="off">
                        <span class="btn-filter-search"><i class="far fa-search"></i></span>
                    </div>
                    <?php if ($enable_project_search_location === '1') { ?>
                        <div class="form-group felan-form-location">
                            <input class="archive-search-location" type="text" name="project-search-location" placeholder="<?php esc_attr_e('All Cities', 'felan-framework') ?>" value="<?php if (isset($_GET['project-location']) && $_GET['project-location'] != '') {
                                                                                                                                                                                            echo felan_clean(wp_unslash($_GET['project-location']));
                                                                                                                                                                                        } ?>">
                            <select name="project-location-top" class="felan-select2 hide">
                                <?php felan_get_taxonomy('felan_location', false, false); ?>
                            </select>
                            <span class="icon-location">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_8969_23265)">
                                        <path d="M13 1L13.001 4.062C14.7632 4.28479 16.4013 5.08743 17.6572 6.34351C18.9131 7.5996 19.7155 9.23775 19.938 11H23V13L19.938 13.001C19.7153 14.7631 18.9128 16.401 17.6569 17.6569C16.401 18.9128 14.7631 19.7153 13.001 19.938L13 23H11V19.938C9.23775 19.7155 7.5996 18.9131 6.34351 17.6572C5.08743 16.4013 4.28479 14.7632 4.062 13.001L1 13V11H4.062C4.28459 9.23761 5.08713 7.59934 6.34324 6.34324C7.59934 5.08713 9.23761 4.28459 11 4.062V1H13ZM12 6C10.4087 6 8.88258 6.63214 7.75736 7.75736C6.63214 8.88258 6 10.4087 6 12C6 13.5913 6.63214 15.1174 7.75736 16.2426C8.88258 17.3679 10.4087 18 12 18C13.5913 18 15.1174 17.3679 16.2426 16.2426C17.3679 15.1174 18 13.5913 18 12C18 10.4087 17.3679 8.88258 16.2426 7.75736C15.1174 6.63214 13.5913 6 12 6ZM12 10C12.5304 10 13.0391 10.2107 13.4142 10.5858C13.7893 10.9609 14 11.4696 14 12C14 12.5304 13.7893 13.0391 13.4142 13.4142C13.0391 13.7893 12.5304 14 12 14C11.4696 14 10.9609 13.7893 10.5858 13.4142C10.2107 13.0391 10 12.5304 10 12C10 11.4696 10.2107 10.9609 10.5858 10.5858C10.9609 10.2107 11.4696 10 12 10Z" fill="#999999" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_8969_23265">
                                            <rect width="24" height="24" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <span class="icon-arrow">
                                <i class="far fa-angle-down"></i>
                            </span>
                            <?php if ($enable_project_search_location_radius == 1) { ?>
                                <span class="radius">
                                    <span class="labels"><?php esc_html_e('Radius:', 'felan-framework') ?></span>
                                    <input type="number" name="project_number_radius" value="" placeholder="0" />
                                    <span class="distance"><?php echo esc_html($felan_distance_unit); ?></span>
                                </span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php if ($project_search_fields_top) : foreach ($project_search_fields_top as $field => $v) {
                            switch ($field) {
                                case 'project-rating':
                                    $project_search_icon_ratting = felan_get_option('project_search_fields_project-rating'); ?>
                                    <div class="form-group">
                                        <select name="project-rating" class="felan-select2">
                                            <option value=""><?php echo esc_html__('All Rating', 'felan-framework'); ?></option>
                                            <option value="rating_five"><?php echo esc_html__('Five Star', 'felan-framework'); ?></option>
                                            <option value="rating_four"><?php echo esc_html__('Four Star', 'felan-framework'); ?></option>
                                            <option value="rating_three"><?php echo esc_html__('Three Star', 'felan-framework'); ?></option>
                                            <option value="rating_two"><?php echo esc_html__('Two Star', 'felan-framework'); ?></option>
                                            <option value="rating_one"><?php echo esc_html__('One Star', 'felan-framework'); ?></option>
                                        </select>
                                        <?php echo $project_search_icon_ratting; ?>
                                    </div>
                                <?php break;
                                case 'project-skills':
                                    $project_search_icon_skills = felan_get_option('project_search_fields_project-skills');
                                ?>
                                    <div class="form-group">
                                        <select name="project-skills" class="felan-select2">
                                            <?php echo '<option value="">' . esc_html__('All Skills', 'felan-framework') . '</option>'; ?>
                                            <?php felan_get_taxonomy('project-skills', false, false); ?>
                                        </select>
                                        <?php echo $project_search_icon_skills; ?>
                                    </div>
                                <?php break;
                                case 'project-location':
                                    felan_content_option_taxonomy('project', 'top');
                                    break;
                                case 'project-categories':
                                    $project_search_icon_categories = felan_get_option('project_search_fields_project-categories');
                                ?>
                                    <div class="form-group">
                                        <select name="project-categories" class="felan-select2">
                                            <?php echo '<option value="">' . esc_html__('All Categories', 'felan-framework') . '</option>'; ?>
                                            <?php felan_get_taxonomy('project-categories', false, false); ?>
                                        </select>
                                        <?php echo $project_search_icon_categories; ?>
                                    </div>
                                <?php break;
                                case 'project-language':
                                    $project_search_icon_language = felan_get_option('project_search_fields_project-language');
                                ?>
                                    <div class="form-group">
                                        <select name="project-language" class="felan-select2">
                                            <?php echo '<option value="">' . esc_html__('All Languages', 'felan-framework') . '</option>'; ?>
                                            <?php felan_get_taxonomy('project-language', false, false); ?>
                                        </select>
                                        <?php echo $project_search_icon_language; ?>
                                    </div>
                    <?php break;
                            }
                            get_search_filter_custom('project', $field);
                        }
                    endif;
                    ?>

                    <div class="form-group">
                        <span class="felan-clear-top-filter"><?php esc_html_e('Clear', 'felan-framework') ?></span>
                        <input type="hidden" name="has_map" value="<?php if (isset($_GET['has_map']) && $_GET['has_map'] == '1') {
                                                                        echo '1';
                                                                    } else {
                                                                        echo '0';
                                                                    } ?>">
                        <button type="submit" class="btn-top-filter felan-button" name="project-top-filter">
                            <?php esc_html_e('Search', 'felan-framework') ?>
                            <span class="btn-loading"><i class="far fa-spinner fa-spin medium"></i></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php }

        /**
         * archive project sidebar filter
         */
        function archive_project_sidebar_filter($current_term, $total_post)
        {
            wp_enqueue_script('jquery-ui-slider');
            $currency_sign_default = felan_get_option('currency_sign_default');
            $filter_classes = array();
            $taxonomy_name = get_query_var('taxonomy');
            $term_id = '';
            if ($current_term) {
                $term_id = $current_term->term_id;
            }
            $project_search_fields = felan_get_option('project_search_fields');
            $project_search_fields_sidebar = isset($project_search_fields['sidebar']) ? $project_search_fields['sidebar'] : array();
            unset($project_search_fields_sidebar['__no_value__']);
?>
    <div class="archive-filter <?php echo join(' ', $filter_classes); ?>">
        <div class="bg-overlay"></div>
        <div class="inner-filter custom-scrollbar">
            <div class="felan-nav-filter">
                <div class="felan-filter-toggle">
                    <span><?php esc_html_e('Filter', 'felan-framework'); ?></span>
                </div>
                <div class="felan-clear-filter">
                    <i class="far fa-sync"></i>
                    <span><?php esc_html_e('Clear All', 'felan-framework'); ?></span>
                </div>
            </div>
            <div class="felan-menu-filter">
                <?php
                if ($project_search_fields_sidebar) : foreach ($project_search_fields_sidebar as $field => $v) {
                        switch ($field) {
                            case 'project-rating': ?>
                                <div class="filter-rating">
                                    <div class="entry-filter">
                                        <h4><?php esc_html_e('Rating', 'felan-framework'); ?></h4>
                                        <ul class="rating filter-control custom-scrollbar">
                                            <li>
                                                <input type="checkbox" id="project_rating_five" class="custom-checkbox input-control" name="project_rating[]" value="rating_five" />
                                                <label for="project_rating_five">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="project_rating_four" class="custom-checkbox input-control" name="project_rating[]" value="rating_four" />
                                                <label for="project_rating_four">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="project_rating_three" class="custom-checkbox input-control" name="project_rating[]" value="rating_three" />
                                                <label for="project_rating_three">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="project_rating_two" class="custom-checkbox input-control" name="project_rating[]" value="rating_two" />
                                                <label for="project_rating_two">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="project_rating_one" class="custom-checkbox input-control" name="project_rating[]" value="rating_one" />
                                                <label for="project_rating_one">
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            <?php break;
                            case 'project-price':
                            ?>
                                <div class="filter-price">
                                    <div class="entry-filter">
                                        <h4><?php esc_html_e('Price', 'felan-framework'); ?></h4>
                                        <div id="range-slider">
                                            <div id="slider-range" data-currency="<?php echo $currency_sign_default; ?>"></div>
                                            <p><input type="text" id="amount" readonly></p>
                                        </div>
                                    </div>
                                </div>
                            <?php
                                break;
                            case 'project-location': ?>
                                <div class="entry-filter entry-filter-locations">
                                    <h4><?php esc_html_e('Locations', 'felan-framework'); ?></h4>
                                    <div class="locations-filter">
                                        <?php felan_content_option_taxonomy('project'); ?>
                                    </div>
                                </div>
                <?php break;
                            case 'project-categories':
                                $title = esc_html__('Categories', 'felan-framework');
                                get_search_filter_submenu('project-categories', $title);
                                break;
                            case 'project-skills':
                                $title = esc_html__('Skills', 'felan-framework');
                                get_search_filter_submenu('project-skills', $title);
                                break;
                            case 'project-language':
                                $title = esc_html__('Language', 'felan-framework');
                                get_search_filter_submenu('project-language', $title);
                                break;
                        }
                        get_search_filter_custom('project', $field);
                    }
                endif;
                ?>

            </div>
        </div>
        <div class="show-result">
            <a href="#" class="felan-button button-block">
                <span><?php echo esc_html__('Show', 'felan-framework'); ?></span>
                <span class="result-count">
					<?php if (!empty($key)) { ?>
						<?php
							printf(
								esc_html(_n('%1$s company for "%2$s"', '%1$s companies for "%2$s"', $total_post, 'felan-framework')),
								'<span>' . $total_post . '</span>',
								$key
							);
						?>
					<?php } else { ?>
						<?php
							echo sprintf(
								_n( '%s company', '%s companies', $total_post, 'felan-framework' ),
								'<span>' . $total_post . '</span>'
							);
						?>
					<?php } ?>
                </span>
            </a>
        </div>

        <?php
            $custom_field_filter = felan_render_custom_field('project');
            $company_search_custom_fields_sidebar = array_map(function ($value) {
                return $value['id'];
            }, $custom_field_filter);
        ?>
        <input type="hidden" name="search_fields_sidebar" value='<?php echo json_encode($project_search_fields_sidebar); ?>'>
        <input type="hidden" name="search_custom_fields_sidebar" value='<?php echo json_encode($company_search_custom_fields_sidebar, JSON_FORCE_OBJECT); ?>'>
        <input type="hidden" name="current_term" value="<?php echo esc_attr($term_id); ?>">
        <input type="hidden" name="type_term" value="<?php echo esc_attr($taxonomy_name); ?>">
    </div>
<?php
        }

        // felan_oembed_get
        function felan_oembed_get($url, $args = '')
        {
            if ($url) {
                // Manually build the IFRAME embed with the related videos option disabled and autoplay turned on
                if (preg_match("/youtube.com\/watch\?v=([^&]+)/i", $url, $aMatch)) {
                    return '<iframe width="560" height="315" src="http://www.youtube.com/embed/' . $aMatch[1] . '?rel=0&autoplay=1&controls=0&loop=1&mute=1&disablekb=1" allowfullscreen></iframe>';
                }

                require_once(ABSPATH . WPINC . '/class-oembed.php');
                $oembed = _wp_oembed_get_object();
                return $oembed->get_html($url, $args);
            }
        }

        /**
         * sidebar jobs
         */
        function sidebar_jobs()
        {
            felan_get_template('global/sidebar-jobs.php');
        }

        /**
         * single jobs head
         */
        function single_jobs_head($job_id, $layout = null)
        {
            $array = array(
                'job_id' => $job_id,
            );
            if (!empty('layout')) {
                $array['layout'] = $layout;
            }
            felan_get_template('jobs/single/head.php', $array);
        }

        /**
         * single jobs insights
         */
        function single_jobs_insights($job_id)
        {
            felan_get_template('jobs/single/insights.php', array(
                'job_id' => $job_id,
            ));
        }

        /**
         * single jobs
         */

        function single_jobs_short_description($job_id)
        {
            felan_get_template('jobs/single/short-description.php', array(
                'job_id' => $job_id,
            ));
        }

        /**
         * single jobs description
         */
        function single_jobs_description($job_id)
        {
            felan_get_template('jobs/single/description.php', array(
                'job_id' => $job_id,
            ));
        }

        /**
         * single jobs_thumbnai
         */
        function single_jobs_thumbnail($job_id)
        {
            felan_get_template('jobs/single/thumbnail.php', array(
                'job_id' => $job_id,
            ));
        }

        function single_jobs_insigh($job_id)
        {
            felan_get_template('jobs/single/insigh.php', array(
                'job_id' => $job_id,
            ));
        }

        /**
         * single jobs skills
         */
        function single_jobs_skills($job_id)
        {
            felan_get_template('jobs/single/skills.php', array(
                'job_id' => $job_id,
            ));
        }

        /**
         * single jobs map
         */
        function single_jobs_map($job_id)
        {
            felan_get_template('jobs/single/map.php', array(
                'job_id' => $job_id,
            ));
        }

        /**
         * single jobs video
         */
        function single_jobs_video($job_id)
        {
            felan_get_template('jobs/single/video.php', array(
                'job_id' => $job_id,
            ));
        }

        /**
         * single jobs gallery
         */
        function gallery_jobs($job_id)
        {
            felan_get_template('jobs/single/gallery.php', array(
                'job_id' => $job_id,
            ));
        }

        function single_jobs_additional($job_id)
        {
            felan_get_template('jobs/single/additional.php', array(
                'job_id' => $job_id
            ));
        }

        /**
         * related jobs
         */
        function single_jobs_related($job_id)
        {
            felan_get_template('jobs/single/related.php', array(
                'job_id' => $job_id,
            ));
        }

        function single_jobs_sidebar_insights($job_id)
        {
            felan_get_template('jobs/single/sidebar/insights.php', array(
                'job_id' => $job_id
            ));
        }

        function single_jobs_sidebar_company($job_id)
        {
            felan_get_template('jobs/single/sidebar/company.php', array(
                'job_id' => $job_id
            ));
        }

        //Company

        /**
         * sidebar company
         */
        function sidebar_company($company_single_id)
        {
            felan_get_template('global/sidebar-company.php', array(
                'company_single_id' => $company_single_id
            ));
        }

        /**
         * single company thumbnail
         */
        function single_company_thumbnail($company_single_id)
        {
            felan_get_template('company/single/thumbnail.php', array(
                'company_single_id' => $company_single_id
            ));
        }

        /**
         * single company overview
         */
        function single_company_overview($company_single_id)
        {
            felan_get_template('company/single/overview.php', array(
                'company_single_id' => $company_single_id
            ));
        }

        /**
         * single company photos
         */
        function single_company_photos($company_single_id)
        {
            felan_get_template('company/single/photos.php', array(
                'company_single_id' => $company_single_id
            ));
        }

        /**
         * single company video
         */
        function single_company_video($company_single_id)
        {
            felan_get_template('company/single/video.php', array(
                'company_single_id' => $company_single_id
            ));
        }

        /**
         * single company additional
         */
        function single_company_additional($company_single_id)
        {
            felan_get_template('company/single/additional.php', array(
                'company_single_id' => $company_single_id
            ));
        }

        /**
         * single company related
         */
        function single_company_related($company_single_id)
        {
            felan_get_template('company/single/related.php', array(
                'company_single_id' => $company_single_id
            ));
        }

        /**
         * single review
         */
        function single_company_review($company_single_id)
        {
            felan_get_template('company/single/review.php', array(
                'company_single_id' => $company_single_id
            ));
        }

        /**
         * single projects
         */
        function single_company_projects($company_single_id)
        {
            felan_get_template('company/single/projects.php', array(
                'company_single_id' => $company_single_id
            ));
        }

        //Company Sidebar
        /**
         * single sidebar company info
         */
        function single_company_sidebar_info($company_single_id)
        {
            felan_get_template('company/single/sidebar/info.php', array(
                'company_single_id' => $company_single_id
            ));
        }

        /**
         * single sidebar company location
         */
        function single_company_sidebar_location($company_single_id)
        {
            felan_get_template('company/single/sidebar/location.php', array(
                'company_single_id' => $company_single_id
            ));
        }


        /**
         * single freelancer thumbnail
         */
        function single_freelancer_thumbnail($freelancer_single_id)
        {
            felan_get_template('freelancer/single/thumbnail.php', array(
                'freelancer_single_id' => $freelancer_single_id
            ));
        }

        /**
         *  Single freelancer head
         */
        function single_freelancer_head($freelancer_single_id)
        {
            felan_get_template('freelancer/single/head.php', array(
                'freelancer_single_id' => $freelancer_single_id
            ));
        }

        /**
         *  Single freelancer about me
         */
        function single_freelancer_descriptions($freelancer_single_id)
        {
            felan_get_template('freelancer/single/descriptions.php', array(
                'freelancer_single_id' => $freelancer_single_id
            ));
        }

        /**
         *  Single freelancer photos
         */
        function single_freelancer_photos($freelancer_single_id)
        {
            felan_get_template('freelancer/single/photos.php', array(
                'freelancer_single_id' => $freelancer_single_id
            ));
        }

        /**
         * Single Freelancer video
         */
        function single_freelancer_video($freelancer_single_id)
        {
            felan_get_template('freelancer/single/video.php', array(
                'freelancer_single_id' => $freelancer_single_id
            ));
        }

        /**
         *  Single freelancer skills
         */
        function single_freelancer_skills($freelancer_single_id)
        {
            felan_get_template('freelancer/single/skills.php', array(
                'freelancer_single_id' => $freelancer_single_id
            ));
        }

        /**
         *  Single freelancer experience
         */
        function single_freelancer_experience($freelancer_single_id)
        {
            felan_get_template('freelancer/single/experience.php', array(
                'freelancer_single_id' => $freelancer_single_id
            ));
        }

        /**
         *  Single freelancer education
         */
        function single_freelancer_education($freelancer_single_id)
        {
            felan_get_template('freelancer/single/education.php', array(
                'freelancer_single_id' => $freelancer_single_id
            ));
        }

        /**
         *  Single freelancer portfolio
         */
        function single_freelancer_portfolio($freelancer_single_id)
        {
            felan_get_template('freelancer/single/portfolio.php', array(
                'freelancer_single_id' => $freelancer_single_id
            ));
        }

        /**
         *  Single freelancer projects
         */
        function single_freelancer_projects($freelancer_single_id)
        {
            felan_get_template('freelancer/single/projects.php', array(
                'freelancer_single_id' => $freelancer_single_id
            ));
        }

        /**
         *  Single freelancer awards
         */
        function single_freelancer_awards($freelancer_single_id)
        {
            felan_get_template('freelancer/single/awards.php', array(
                'freelancer_single_id' => $freelancer_single_id
            ));
        }

        function single_freelancer_additional($freelancer_single_id)
        {
            felan_get_template('freelancer/single/additional.php', array(
                'freelancer_single_id' => $freelancer_single_id
            ));
        }

        /**
         *  Single freelancer cover image
         */
        function single_freelancer_cover_hero($freelancer_single_id)
        {
            felan_get_template('freelancer/single/cover.php', array(
                'freelancer_single_id' => $freelancer_single_id
            ));
        }

        /**
         *  Single freelancer review
         */
        function single_freelancer_service($freelancer_single_id)
        {
            felan_get_template('freelancer/single/service.php', array(
                'freelancer_single_id' => $freelancer_single_id
            ));
        }

        /**
         *  Single freelancer review
         */
        function single_freelancer_review($freelancer_single_id)
        {
            felan_get_template('freelancer/single/review.php', array(
                'freelancer_single_id' => $freelancer_single_id
            ));
        }

        // Freelancer Sidebar
        /**
         *  Single sidebar freelancer info
         */
        function single_freelancer_sidebar_info($freelancer_single_id)
        {
            felan_get_template('freelancer/single/sidebar/info.php', array(
                'freelancer_single_id' => $freelancer_single_id
            ));
        }

        /**
         *  Single sidebar freelancer location
         */
        function single_freelancer_sidebar_location($freelancer_single_id)
        {
            felan_get_template('freelancer/single/sidebar/location.php', array(
                'freelancer_single_id' => $freelancer_single_id
            ));
        }

        /**
         * sidebar Freelancer
         */
        function sidebar_freelancer($freelancer_single_id)
        {
            felan_get_template('global/sidebar-freelancer.php', array(
                'freelancer_single_id' => $freelancer_single_id
            ));
        }

        //Service
        /**
         * sidebar service
         */
        function sidebar_service($service_single_id)
        {
            felan_get_template('global/sidebar-service.php', array(
                'service_single_id' => $service_single_id
            ));
        }

        /**
         * single service gallery
         */
        function single_service_head($service_single_id)
        {
            felan_get_template('service/single/head.php', array(
                'service_single_id' => $service_single_id
            ));
        }

        /**
         * single service gallery
         */
        function single_service_gallery($service_single_id)
        {
            felan_get_template('service/single/gallery.php', array(
                'service_single_id' => $service_single_id
            ));
        }

        /**
         * single service descriptions
         */
        function single_service_descriptions($service_single_id)
        {
            felan_get_template('service/single/descriptions.php', array(
                'service_single_id' => $service_single_id
            ));
        }

        /**
         * single service skills
         */
        function single_service_skills($service_single_id)
        {
            felan_get_template('service/single/skills.php', array(
                'service_single_id' => $service_single_id
            ));
        }

        function single_service_additional($service_single_id)
        {
            felan_get_template('service/single/additional.php', array(
                'service_single_id' => $service_single_id
            ));
        }

        /**
         * single service skills
         */
        function single_service_package($service_single_id)
        {
            felan_get_template('service/single/package.php', array(
                'service_single_id' => $service_single_id
            ));
        }

        /**
         * single service location
         */
        function single_service_location($service_single_id)
        {
            felan_get_template('service/single/location.php', array(
                'service_single_id' => $service_single_id
            ));
        }

        /**
         * single service video
         */
        function single_service_video($service_single_id)
        {
            felan_get_template('service/single/video.php', array(
                'service_single_id' => $service_single_id
            ));
        }

        /**
         * single service faq
         */
        function single_service_faq($service_single_id)
        {
            felan_get_template('service/single/faq.php', array(
                'service_single_id' => $service_single_id
            ));
        }

        /**
         * single related review
         */
        function single_service_review($service_single_id)
        {
            felan_get_template('service/single/review.php', array(
                'service_single_id' => $service_single_id
            ));
        }

        /**
         * single service related
         */
        function single_service_related($service_single_id)
        {
            felan_get_template('service/single/related.php', array(
                'service_single_id' => $service_single_id
            ));
        }

        //Service Sidebar

        /**
         * single sidebar service location
         */
        function single_service_sidebar_package($service_single_id)
        {
            felan_get_template('service/single/sidebar/package.php', array(
                'service_single_id' => $service_single_id
            ));
        }

        /**
         * single sidebar service info
         */
        function single_service_sidebar_info($service_single_id)
        {
            felan_get_template('service/single/sidebar/info.php', array(
                'service_single_id' => $service_single_id
            ));
        }


        //Project
        /**
         * sidebar project
         */
        function sidebar_project($project_single_id)
        {
            felan_get_template('global/sidebar-project.php', array(
                'project_single_id' => $project_single_id
            ));
        }

        /**
         * single project gallery
         */
        function single_project_head($project_single_id)
        {
            felan_get_template('project/single/head.php', array(
                'project_single_id' => $project_single_id
            ));
        }

        /**
         * single project gallery
         */
        function single_project_gallery($project_single_id)
        {
            felan_get_template('project/single/gallery.php', array(
                'project_single_id' => $project_single_id
            ));
        }

        /**
         * single project descriptions
         */
        function single_project_descriptions($project_single_id)
        {
            felan_get_template('project/single/descriptions.php', array(
                'project_single_id' => $project_single_id
            ));
        }

        /**
         * single project skills
         */
        function single_project_skills($project_single_id)
        {
            felan_get_template('project/single/skills.php', array(
                'project_single_id' => $project_single_id
            ));
        }

        /**
         * single project location
         */
        function single_project_location($project_single_id)
        {
            felan_get_template('project/single/location.php', array(
                'project_single_id' => $project_single_id
            ));
        }

        /**
         * single project faq
         */
        function single_project_faq($project_single_id)
        {
            felan_get_template('project/single/faq.php', array(
                'project_single_id' => $project_single_id
            ));
        }

        /**
         * single project video
         */
        function single_project_video($project_single_id)
        {
            felan_get_template('project/single/video.php', array(
                'project_single_id' => $project_single_id
            ));
        }

        /**
         * Single project related
         */
        function single_project_related($project_single_id)
        {
            felan_get_template('project/single/related.php', array(
                'project_single_id' => $project_single_id
            ));
        }

        /**
         * Single project additional
         */
        function single_project_additional($project_single_id)
        {
            felan_get_template('project/single/additional.php', array(
                'project_single_id' => $project_single_id
            ));
        }

        //Service Sidebar
        /**
         * single sidebar project apply
         */
        function single_project_sidebar_apply($project_single_id)
        {
            felan_get_template('project/single/sidebar/apply.php', array(
                'project_single_id' => $project_single_id
            ));
        }

        /**
         * single sidebar project info
         */
        function single_project_sidebar_info($project_single_id)
        {
            felan_get_template('project/single/sidebar/info.php', array(
                'project_single_id' => $project_single_id
            ));
        }
