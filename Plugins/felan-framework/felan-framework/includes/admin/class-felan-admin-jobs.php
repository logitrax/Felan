<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('Felan_Admin_Jobs')) {
    /**
     * Class Felan_Admin_Jobs
     */
    class Felan_Admin_Jobs
    {

        /**
         * Register custom columns
         * @param $columns
         * @return array
         */
        public function register_custom_column_titles($columns)
        {
            unset($columns['tags']);
            $columns['thumb'] = esc_html__('Logo', 'felan-framework');
            $columns['title'] = esc_html__('Jobs Title', 'felan-framework');
            $columns['type'] =  esc_html__('Type', 'felan-framework');
            $columns['skills'] = esc_html__('Skills', 'felan-framework');
            $columns['featured'] = '<span data-tip="' .  esc_html__('Featured?', 'felan-framework') . '" class="tips dashicons dashicons-star-filled"></span>';
            $columns['author'] = esc_html__('Author', 'felan-framework');
            $new_columns = array();
            $custom_order = array('cb', 'thumb', 'title', 'type', 'skills', 'featured', 'author', 'date');
            foreach ($custom_order as $colname) {
                $new_columns[$colname] = $columns[$colname];
            }
            return $new_columns;
        }
        /**
         * Display custom column for jobs
         * @param $column
         */
        public function display_custom_column($column)
        {
            global $post;
            $post_id = $post->ID;
            switch ($column) {
                case 'thumb':
                    $jobs_select_company = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'jobs_select_company');
                    $company_id = isset($jobs_select_company[0]) ? $jobs_select_company[0] : '';
                    $company_logo   = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_logo');
                    $company_logo = !empty($company_logo) ? $company_logo[0]['url'] : '';
                    if (!empty($company_logo)) {
                        echo '<img src = " ' . $company_logo . '" alt=""/>';
                    } else {
                        echo '&ndash;';
                    }
                    break;
                case 'type':
                    echo felan_admin_taxonomy_terms($post->ID, 'jobs-type', 'jobs');
                    break;
                case 'skills':
                    echo felan_admin_taxonomy_terms($post->ID, 'jobs-skills', 'jobs');
                    break;
                case 'featured':
                    $featured = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'jobs_featured', true);
                    if ($featured == 1) {
                        echo '<i data-tip="' .  esc_html__('Featured', 'felan-framework') . '" class="tips accent-color dashicons dashicons-star-filled"></i>';
                    } else {
                        echo '<i data-tip="' .  esc_html__('Not Featured', 'felan-framework') . '" class="tips dashicons dashicons-star-empty"></i>';
                    }
                    break;
                case 'author':
                    echo '<a href="' . esc_url(add_query_arg('author', $post->post_author)) . '">' . get_the_author() . '</a>';
                    break;
            }
        }

        /**
         * @param $actions
         * @param $post
         * @return mixed
         */
        public function modify_list_row_actions($actions, $post)
        {
            // Check for your post type.
            if ($post->post_type == 'jobs') {
                if (in_array($post->post_status, array('pending', 'expired'))) {
                    $actions['jobs-approve'] = '<a href="' . wp_nonce_url(add_query_arg('approve_listing', $post->ID), 'approve_listing') . '">' . esc_html__('Approve', 'felan-framework') . '</a>';
                }
                if (in_array($post->post_status, array('publish', 'pending'))) {
                    $actions['jobs-expired'] = '<a href="' . wp_nonce_url(add_query_arg('expire_listing', $post->ID), 'expire_listing') . '">' . esc_html__('Expire', 'felan-framework') . '</a>';
                }
                if (in_array($post->post_status, array('publish'))) {
                    $actions['jobs-hidden'] = '<a href="' . wp_nonce_url(add_query_arg('hidden_listing', $post->ID), 'hidden_listing') . '">' . esc_html__('Hide', 'felan-framework') . '</a>';
                }
                if (in_array($post->post_status, array('hidden'))) {
                    $actions['jobs-show'] = '<a href="' . wp_nonce_url(add_query_arg('show_listing', $post->ID), 'show_listing') . '">' . esc_html__('Show', 'felan-framework') . '</a>';
                }
            }
            return $actions;
        }

        /**
         * sortable_columns
         * @param $columns
         * @return mixed
         */
        public function sortable_columns($columns)
        {
            $columns['skills'] = 'skills';
            $columns['featured'] = 'featured';
            $columns['author'] = 'author';
            $columns['post_date'] = 'post_date';
            return $columns;
        }

        /**
         * @param $vars
         * @return array
         */
        public function column_orderby($vars)
        {
            if (!is_admin())
                if (isset($vars['orderby']) && 'featured' == $vars['orderby']) {
                    $vars = array_merge($vars, array(
                        'meta_key' => FELAN_METABOX_PREFIX . 'jobs_featured',
                        'orderby' => 'meta_value_num',
                    ));
                }
            return $vars;
        }
        /**
         * Modify jobs slug
         * @param $existing_slug
         * @return string
         */
        public function modify_jobs_slug($existing_slug)
        {
            $jobs_url_slug = felan_get_option('jobs_url_slug');
            $enable_slug_categories = felan_get_option('enable_slug_categories');
            if ($jobs_url_slug) {
                if ($enable_slug_categories == 1) {
                    return $jobs_url_slug . '/%jobs-categories%';
                } else {
                    return $jobs_url_slug;
                }
            }
            return $existing_slug;
        }

        public function modify_jobs_has_archive($existing_slug)
        {
            $jobs_url_slug = felan_get_option('jobs_url_slug');
            if ($jobs_url_slug) {
                return $jobs_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Modify jobs type slug
         * @param $existing_slug
         * @return string
         */
        public function modify_jobs_type_slug($existing_slug)
        {
            $jobs_type_url_slug = felan_get_option('jobs_type_url_slug');
            if ($jobs_type_url_slug) {
                return $jobs_type_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Modify jobs tags slug
         * @param $existing_slug
         * @return string
         */
        public function modify_jobs_tags_slug($existing_slug)
        {
            $jobs_tags_url_slug = felan_get_option('jobs_tags_url_slug');
            if ($jobs_tags_url_slug) {
                return $jobs_tags_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Modify jobs categories slug
         * @param $existing_slug
         * @return string
         */
        public function modify_jobs_categories_slug($existing_slug)
        {
            $jobs_categories_url_slug = felan_get_option('jobs_categories_url_slug');
            if ($jobs_categories_url_slug) {
                return $jobs_categories_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Modify jobs skills slug
         * @param $existing_slug
         * @return string
         */
        public function modify_jobs_skills_slug($existing_slug)
        {
            $jobs_skills_url_slug = felan_get_option('jobs_skills_url_slug');
            if ($jobs_skills_url_slug) {
                return $jobs_skills_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Modify jobs location slug
         * @param $existing_slug
         * @return string
         */
        public function modify_jobs_location_slug($existing_slug)
        {
            $jobs_location_url_slug = felan_get_option('jobs_location_url_slug');
            if ($jobs_location_url_slug) {
                return $jobs_location_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Modify jobs career slug
         * @param $existing_slug
         * @return string
         */
        public function modify_jobs_career_slug($existing_slug)
        {
            $jobs_career_url_slug = felan_get_option('jobs_career_url_slug');
            if ($jobs_career_url_slug) {
                return $jobs_career_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Modify jobs experience slug
         * @param $existing_slug
         * @return string
         */
        public function modify_jobs_experience_slug($existing_slug)
        {
            $jobs_experience_url_slug = felan_get_option('jobs_experience_url_slug');
            if ($jobs_experience_url_slug) {
                return $jobs_experience_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Modify jobs qualification slug
         * @param $existing_slug
         * @return string
         */
        public function modify_jobs_qualification_slug($existing_slug)
        {
            $jobs_qualification_url_slug = felan_get_option('jobs_qualification_url_slug');
            if ($jobs_qualification_url_slug) {
                return $jobs_qualification_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Modify jobs gender slug
         * @param $existing_slug
         * @return string
         */
        public function modify_jobs_gender_slug($existing_slug)
        {
            $jobs_gender_url_slug = felan_get_option('jobs_gender_url_slug');
            if ($jobs_gender_url_slug) {
                return $jobs_gender_url_slug;
            }
            return $existing_slug;
        }


        /**
         * Approve_jobs
         */
        public function approve_jobs()
        {
            if (!empty($_GET['approve_listing']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'approve_listing') && current_user_can('publish_post', $_GET['approve_listing'])) {
                $post_id = absint(felan_clean(wp_unslash($_GET['approve_listing'])));
                $listing_data = array(
                    'ID' => $post_id,
                    'post_status' => 'publish'
                );
                wp_update_post($listing_data);

                $author_id = get_post_field('post_author', $post_id);
                $user = get_user_by('id', $author_id);
                $user_email = $user->user_email;

                $args = array(
                    'your_name' => $user->user_login,
                    'listing_title' => get_the_title($post_id),
                    'listing_url' => get_permalink($post_id)
                );
                felan_send_email($user_email, 'mail_approved_listing', $args);
                wp_redirect(remove_query_arg('approve_listing', add_query_arg('approve_listing', $post_id, admin_url('edit.php?post_type=jobs'))));
                exit;
            }
        }

        /**
         * Expire jobs
         */
        public function expire_jobs()
        {
            if (!empty($_GET['expire_listing']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'expire_listing') && current_user_can('publish_post', $_GET['expire_listing'])) {
                $post_id = absint(felan_clean(wp_unslash($_GET['expire_listing'])));

                $listing_data = array(
                    'ID' => $post_id,
                    'post_status' => 'expired'
                );
                wp_update_post($listing_data);

                $author_id = get_post_field('post_author', $post_id);
                $user = get_user_by('id', $author_id);
                $user_email = $user->user_email;

                $args = array(
                    'listing_title' => get_the_title($post_id),
                    'listing_url' => get_permalink($post_id)
                );
                felan_send_email($user_email, 'mail_expired_listing', $args);

                wp_redirect(remove_query_arg('expire_listing', add_query_arg('expire_listing', $post_id, admin_url('edit.php?post_type=jobs'))));
                exit;
            }
        }

        /**
         * Hidden jobs
         */
        public function hidden_jobs()
        {
            if (!empty($_GET['hidden_listing']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'hidden_listing') && current_user_can('publish_post', $_GET['hidden_listing'])) {
                $post_id = absint(felan_clean(wp_unslash($_GET['hidden_listing'])));
                $listing_data = array(
                    'ID' => $post_id,
                    'post_status' => 'hidden'
                );
                wp_update_post($listing_data);
                wp_redirect(remove_query_arg('hidden_listing', add_query_arg('hidden_listing', $post_id, admin_url('edit.php?post_type=jobs'))));
                exit;
            }
        }

        /**
         * Show jobs
         */
        public function show_jobs()
        {
            if (!empty($_GET['show_listing']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'show_listing') && current_user_can('publish_post', $_GET['show_listing'])) {
                $post_id = absint(felan_clean(wp_unslash($_GET['show_listing'])));
                $listing_data = array(
                    'ID' => $post_id,
                    'post_status' => 'publish'
                );
                wp_update_post($listing_data);
                wp_redirect(remove_query_arg('show_listing', add_query_arg('show_listing', $post_id, admin_url('edit.php?post_type=jobs'))));
                exit;
            }
        }

        /**
         * filter_restrict_manage_jobs
         */
        public function filter_restrict_manage_jobs()
        {
            global $typenow;
            $post_type = 'jobs';
            if ($typenow == $post_type) {
                $taxonomy_arr  = array('jobs-skills', 'jobs-type');
                foreach ($taxonomy_arr as $taxonomy) {
                    $selected      = isset($_GET[$taxonomy]) ? felan_clean(wp_unslash($_GET[$taxonomy])) : '';
                    $info_taxonomy = get_taxonomy($taxonomy);
                    wp_dropdown_categories(array(
                        'show_option_all' => __("All {$info_taxonomy->label}"),
                        'taxonomy'        => $taxonomy,
                        'name'            => $taxonomy,
                        'orderby'         => 'name',
                        'selected'        => $selected,
                        'hide_empty'      => false,
                    ));
                }
?>
                <?php
            };
        }

        /**
         * h_filter
         * @param $query
         */
        public function jobs_filter($query)
        {
            global $pagenow;
            $post_type = 'jobs';
            $q_vars    = &$query->query_vars;
            if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type) {
                $taxonomy_arr  = array('jobs-skills', 'jobs-type');
                foreach ($taxonomy_arr as $taxonomy) {
                    if (isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0) {
                        $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
                        $q_vars[$taxonomy] = $term->slug;
                    }
                }
            }
        }

        public function auto_description_generate()
        {
            // Get keywords from the request
            $keywords = $_POST['keywords'];
            $tone = $_POST['tone'];
            $language = $_POST['language'];

			$ai_type = felan_get_option('ai_type');
            $ai_model = felan_get_option('ai_model');
            $ai_temperature = felan_get_option('ai_temperature');
            $ai_key = felan_get_option('ai_key');

            if ($tone) {
                $keywords = $keywords . ' Writing style and tone:' . $tone . '.';
            }

            if ($language) {
                $keywords = $keywords . ' Write in:' . $language  . '.';
            }

            $payload = array(
                'messages' => array(
                    array('role' => 'system', 'content' => 'You can start the conversation.'),
                    array('role' => 'user', 'content' => $keywords)
                )
            );

            $endpoint = 'https://api.openai.com/v1/chat/completions';
			if ( $ai_type == 'deep_seek' ) {
				$ai_key = felan_get_option('ai_deepseek_key');
				$ai_model = felan_get_option('ai_deepseek_model');
				$endpoint = 'https://api.deepseek.com/chat/completions';
			}
			if ( $ai_type == 'grok' ) {
				$ai_key = felan_get_option('ai_grok_key');
				$ai_model = felan_get_option('ai_grok_model');
				$endpoint = 'https://api.x.ai/v1/chat/completions';
			}

            $body = [
                'temperature' => intval($ai_temperature),
                'max_tokens' => 2048,
                'model' => $ai_model,
                'messages' => $payload['messages']
            ];

            $args = array(
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer " . $ai_key
                ),
                'body' => json_encode($body),
                'timeout' => 300,
            );

            $response = wp_remote_post($endpoint, $args);

            if (is_wp_error($response)) {
                echo json_encode(array('success' => false, 'response' => $response, 'message' => $response->get_error_message()));
            } else {
                $response_body = json_decode(wp_remote_retrieve_body($response));
                if (isset($response_body->error->message) || isset($response_body->error)) {
                    if ($response_body->error->code === 'invalid_api_key') {
                        echo json_encode(array('success' => false, 'response' => $response_body, 'message' => esc_html__('Invalid API Key', 'felan-framework')));
                    } else {
						$message = $response_body->error->message ?? $response_body->code;
                        echo json_encode(array('success' => false, 'response' => $response_body, 'message' => $message));
                    }
                } else {
                    echo json_encode(array('success' => true, 'response' => $response_body, 'message' => wpautop($response_body->choices[0]->message->content)));
                }
            }

            wp_die();
        }
    }
}
