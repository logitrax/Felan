<?php

/**
 * Get Option
 */
if (!function_exists('felan_get_option')) {
    function felan_get_option($key, $default = '')
    {
        if (function_exists('pll_the_languages')) {
            $option = get_option(pll_current_language() . '_felan-framework');
        } else if (defined('ICL_SITEPRESS_VERSION')) {
            $current_language = apply_filters('wpml_current_language', NULL);

            if ($current_language) {
                $option = get_option($current_language . '_felan-framework');
            }
        } else {
            $option = get_option('felan-framework');
        }

        return (isset($option[$key])) ? apply_filters('felan/get_option', $option[$key], $option, $key) : $default;
    }
}

/**
 * Check nonce
 *
 * @param string $action Action name.
 * @param string $nonce Nonce.
 */
if (!function_exists('verify_nonce')) {
    function verify_nonce($action = '', $nonce = '')
    {

        if (!$nonce && isset($_REQUEST['_wpnonce'])) {
            $nonce = sanitize_text_field(wp_unslash($_REQUEST['_wpnonce']));
        }

        return wp_verify_nonce($nonce, $action);
    }
}

/**
 * Check theme support
 */
if (!function_exists('is_theme_support')) {
    function is_theme_support()
    {
        return current_theme_supports('felan');
    }
}

/**
 * Check has shortcode
 */
if (!function_exists('felan_page_shortcode')) {
    function felan_page_shortcode($shortcode = null)
    {

        $post = get_post(get_the_ID());

        $found = false;

        if (empty($post->post_content)) {
            return $found;
        }

        if (wp_strip_all_tags($post->post_content) === $shortcode) {
            $found = true;
        }

        // return our final results
        return $found;
    }
}

/**
 * Insert custom header script.
 *
 * @return void
 */
function felan_custom_header_js()
{
    if (felan_get_option('header_script', '') && !is_admin()) {
        echo felan_get_option('header_script', ''); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

add_action('wp_head', 'felan_custom_header_js', 99);

/**
 * Insert custom footer script.
 *
 * @return void
 */
function felan_footer_scripts()
{
    echo do_shortcode(felan_get_option('footer_script', ''));
}

add_action('wp_footer', 'felan_footer_scripts');

/**
 * Convert text to 1 line
 *
 * @param $str
 *
 * @return string
 */
if (!function_exists('text2line')) {
    function text2line($str)
    {
        return trim(preg_replace("/[\r\v\n\t]*/", '', $str));
    }
}

/**
 * Get template part (for templates like the shop-loop).
 *
 * @param mixed $slug
 * @param string $name (default: '')
 */
if (!function_exists('felan_get_template_part')) {
    function felan_get_template_part($slug, $name = '')
    {
        $template = '';
        if ($name) {
            $template = locate_template(array(
                "{$slug}-{$name}.php",
                FELAN()->template_path() . "{$slug}-{$name}.php"
            ));
        }

        // Get default slug-name.php
        if (!$template && $name && file_exists(FELAN_PLUGIN_DIR . "templates/{$slug}-{$name}.php")) {
            $template = FELAN_PLUGIN_DIR . "templates/{$slug}-{$name}.php";
        }

        if (!$template) {
            $template = locate_template(array("{$slug}.php", FELAN()->template_path() . "{$slug}.php"));
        }

        // Allow 3rd party plugins to filter template file from their plugin.
        $template = apply_filters('felan_get_template_part', $template, $slug, $name);

        if ($template) {
            load_template($template, false);
        }
    }
}

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 */
if (!function_exists('felan_get_template')) {
    function felan_get_template($template_name, $args = array(), $template_path = '', $default_path = '')
    {
        if (!empty($args) && is_array($args)) {
            extract($args);
        }

        $located = felan_locate_template($template_name, $template_path, $default_path);

        if (!file_exists($located)) {
            _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $located), '2.1');

            return;
        }

        // Allow 3rd party plugin filter template file from their plugin.
        $located = apply_filters('felan_get_template', $located, $template_name, $args, $template_path, $default_path);

        do_action('felan_before_template_part', $template_name, $template_path, $located, $args);

        include($located);

        do_action('felan_after_template_part', $template_name, $template_path, $located, $args);
    }
}

/**
 * Like felan_get_template, but returns the HTML instead of outputting.
 */
if (!function_exists('felan_get_template_html')) {
    function felan_get_template_html($template_name, $args = array(), $template_path = '', $default_path = '')
    {
        ob_start();
        felan_get_template($template_name, $args, $template_path, $default_path);

        return ob_get_clean();
    }
}

/**
 * Send email
 */
if (!function_exists('felan_send_email')) {
    function felan_send_email($email, $email_type, $args = array())
    {
        $content = felan_get_option($email_type, '');
        $subject = felan_get_option('subject_' . $email_type, '');

        if (function_exists('icl_translate')) {
            $content = icl_translate('felan-framework', 'felan_email_' . $content, $content);
            $subject = icl_translate('felan-framework', 'felan_email_subject_' . $subject, $subject);
        }
        $content = wpautop($content);
        $args['website_url'] = get_option('siteurl');
        $args['website_name'] = get_option('blogname');
        $args['user_email'] = $email;
        $user = get_user_by('email', $email);
        if (!empty($user)) {
            $args['username'] = $user->user_login;
        }

        foreach ($args as $key => $val) {
            $subject = str_replace('%' . $key, $val, $subject);
            $content = str_replace('%' . $key, $val, $content);
        }

        ob_start();
        felan_get_template("mail/mail.php", array(
            'content' => $content,
        ));
        $message = ob_get_clean();

        $headers = apply_filters('felan_contact_mail_header', array(
            'From: ' . get_bloginfo('name') . ' <' . get_bloginfo('admin_email') . '>',
            'Content-Type: text/html; charset=UTF-8'
        ));

        @wp_mail(
            $email,
            $subject,
            $message,
            $headers
        );
    }
}

/**
 * Convert date format
 */
if (!function_exists('felan_convert_date_format')) {
    function felan_convert_date_format($date_string)
    {
        $date_timestamp = strtotime($date_string);
        $formatted_date = date(get_option('date_format'), $date_timestamp);

        return $formatted_date;
    }
}

/**
 * Get total posts by user id
 */
if (!function_exists('get_total_posts_by_user')) {
    function get_total_posts_by_user($user_id, $post_type = 'post')
    {
        $args = array(
            'post_type' => $post_type,
            'post_status' => 'publish',
            'author' => $user_id,
        );
        $posts = new WP_Query($args);
        wp_reset_postdata();

        return $posts->found_posts;
    }
}

/**
 * Get page id
 */
if (!function_exists('felan_get_page_id')) {
    function felan_get_page_id($page)
    {
        $page_id = felan_get_option('felan_' . $page . '_page_id');
        if ($page_id) {
            return absint(function_exists('pll_get_post') ? pll_get_post($page_id) : $page_id);
        } else {
            return 0;
        }
    }
}

/**
 * Get permalink
 */
if (!function_exists('felan_get_permalink')) {
    function felan_get_permalink($page)
    {
        if ($page_id = felan_get_page_id($page)) {
            return get_permalink($page_id);
        } else {
            return false;
        }
    }
}

if (!function_exists('felan_image_captcha')) {
    function felan_image_captcha($captcha)
    {

        if (empty($captcha)) return;

        // Generate a 50x24 standard captcha image
        $im = imagecreatetruecolor(50, 40);

        // Accent color
        $bg = imagecolorallocate($im, 0, 116, 86);

        // White color
        $fg = imagecolorallocate($im, 255, 255, 255);

        // Give the image a blue background
        imagefill($im, 0, 0, $bg);

        // Print the captcha text in the image
        // with random position & size
        imagestring($im, 24, 8, 11, $captcha, $fg);

        ob_start();

        // Finally output the captcha as
        // PNG image the browser
        imagepng($im);

        $imgData = ob_get_clean();

        // Free memory
        imagedestroy($im);

        echo '<img src="data:image/png;base64,' . base64_encode($imgData) . '" />';
    }
}

/**
 * allow submit
 */
if (!function_exists('felan_allow_submit')) {
    function felan_allow_submit()
    {
        $enable_submit_jobs_via_frontend = felan_get_option('enable_submit_jobs_via_frontend', 1);
        $user_can_submit = felan_get_option('user_can_submit', 1);

        $allow_submit = true;
        if ($enable_submit_jobs_via_frontend != 1) {
            $allow_submit = false;
        } else {
            if ($user_can_submit != 1) {
                $allow_submit = false;
            }
        }

        return $allow_submit;
    }
}

/**
 * Total View Freelancer
 */
if (!function_exists('felan_total_view_freelancer')) {
    function felan_total_view_freelancer($number_days = 7)
    {
        global $current_user;
        wp_get_current_user();
        $user_id = $current_user->ID;

        $args = array(
            'post_type' => 'freelancer',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'author' => $user_id,
        );

        $data = new WP_Query($args);
        $total_post = $data->found_posts;
        $views_values = array();
        if ($total_post > 0) {
            while ($data->have_posts()) : $data->the_post();
                $id = get_the_ID();
                $views_date = get_post_meta($id, 'felan_view_freelancer_date', true);
                $item = array();
                for ($i = $number_days; $i >= 0; $i--) {
                    $date = date("Y-m-d", strtotime("-" . $i . " day"));

                    if (isset($views_date[$date])) {
                        $item[] = $views_date[$date];
                    } else {
                        $item[] = 0;
                    }
                }
                array_push($views_values, $item);
            endwhile;
        }
        wp_reset_postdata();
        $results_value = array();
        for ($i = 0; $i <= $number_days; $i++) {
            $views_item = 0;
            foreach ($views_values as $views_value) {
                $views_item += $views_value[$i];
            }
            array_push($results_value, $views_item);
        }

        return $results_value;
    }
}

/**
 * Company Green Tick
 */
if (!function_exists('felan_company_green_tick')) {
    function felan_company_green_tick($company_id)
    {
        if (empty($company_id)) {
            return;
        }
        $company_green_tick = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_green_tick', true);
        if ($company_green_tick == 1) : ?>
            <div class="felan-check-company tip active">
                <div class="tip-content">
                    <h4><?php esc_html_e('Conditions for a green tick:', 'felan-framework') ?></h4>
                    <ul class="list-check">
                        <li class="check-webs active">
                            <i class="far fa-check"></i>
                            <?php esc_html_e('Website has been verified', 'felan-framework') ?>
                        </li>
                        <li class="check-phone active">
                            <i class="far fa-check"></i>
                            <?php esc_html_e('Phone has been verified', 'felan-framework') ?>
                        </li>
                        <li class="check-location active">
                            <i class="far fa-check"></i>
                            <?php esc_html_e('Location has been verified', 'felan-framework') ?>
                        </li>
                    </ul>
                </div>
            </div>
        <?php endif;
    }
}


/**
 * Get total my apply
 */
if (!function_exists('felan_total_my_apply')) {
    function felan_total_my_apply()
    {
        global $current_user;
        $user_id = $current_user->ID;
        $args    = array(
            'post_type'      => 'applicants',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'author'         => $user_id,
        );
        $data    = new WP_Query($args);

        return $data->found_posts;
    }
}

/**
 * Actived project
 */
if (!function_exists('felan_total_actived_project')) {
    function felan_total_actived_project($user_id)
    {
        $args = array(
            'post_type' => 'project',
            'posts_per_page' => -1,
            'author' => $user_id,
        );

        $data = new WP_Query($args);
        $total_post = $data->found_posts;

        return $total_post;
    }
}

/**
 * Actived company
 */
if (!function_exists('felan_total_actived_company')) {
    function felan_total_actived_company()
    {

        global $current_user;
        $user_id = $current_user->ID;

        $args = array(
            'post_type' => 'company',
            'posts_per_page' => -1,
            'author' => $user_id,
        );

        $data = new WP_Query($args);
        $total_post = $data->found_posts;

        return $total_post;
    }
}

/**
 * Total Applications
 */
if (!function_exists('felan_total_applications_jobs')) {
    function felan_total_applications_jobs()
    {

        global $current_user;
        $user_id = $current_user->ID;
        $args_jobs = array(
            'post_type' => 'jobs',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'author' => $user_id,
            'orderby' => 'date',
        );
        $data_jobs = new WP_Query($args_jobs);
        $jobs_employer_id = array();
        if ($data_jobs->have_posts()) {
            while ($data_jobs->have_posts()) : $data_jobs->the_post();
                $jobs_employer_id[] = get_the_ID();
            endwhile;
        }
        $args = array(
            'post_type' => 'applicants',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => FELAN_METABOX_PREFIX . 'applicants_jobs_id',
                    'value' => $jobs_employer_id,
                    'compare' => 'IN'
                )
            ),
        );

        $data = new WP_Query($args);
        $total_post = $data->found_posts;

        if (!empty($jobs_employer_id)) {
            return $total_post;
        } else {
            return 0;
        }
    }
}

/**
 * Total meetings
 */
if (!function_exists('felan_total_meeting')) {
    function felan_total_meeting($user)
    {
        if (empty($user)) {
            return;
        }
        global $current_user;
        $user_id = $current_user->ID;
        if ($user == 'employer') {
            $args = array(
                'post_type' => 'meetings',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'author' => $user_id,
            );
        } elseif ($user == 'freelancer') {
            $args_applicants = array(
                'post_type' => 'applicants',
                'ignore_sticky_posts' => 1,
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'author' => $user_id,
            );
            $data_applicants = new WP_Query($args_applicants);
            $applicants_id = array();
            if ($data_applicants->have_posts()) {
                while ($data_applicants->have_posts()) : $data_applicants->the_post();
                    $applicants_id[] = get_the_ID();
                endwhile;
            }
            $args = array(
                'post_type' => 'meetings',
                'ignore_sticky_posts' => 1,
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'meeting_applicants_id',
                        'value' => $applicants_id,
                        'compare' => 'IN'
                    ),
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'meeting_status',
                        'value' => 'completed',
                        'compare' => '!='
                    )
                ),
            );
        }
        $data = new WP_Query($args);
        $total_post = $data->found_posts;

        if ($user == 'employer') {
            return $total_post;
        } elseif ($user == 'freelancer') {
            if (!empty($applicants_id)) {
                return $total_post;
            } else {
                return 0;
            }
        }
    }
}

/**
 * Total View Jobs
 */
if (!function_exists('felan_total_view_jobs')) {
    function felan_total_view_jobs($number_days = 7)
    {
        global $current_user;
        wp_get_current_user();
        $user_id = $current_user->ID;

        $args = array(
            'post_type' => 'jobs',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'author' => $user_id,
        );

        $data = new WP_Query($args);
        $total_post = $data->found_posts;
        $views_values = array();
        if ($total_post > 0) {
            while ($data->have_posts()) : $data->the_post();
                $id = get_the_ID();
                $views_date = get_post_meta($id, 'felan_view_by_date', true);
                $item = array();
                for ($i = $number_days; $i >= 0; $i--) {
                    $date = date("Y-m-d", strtotime("-" . $i . " day"));

                    if (isset($views_date[$date])) {
                        $item[] = $views_date[$date];
                    } else {
                        $item[] = 0;
                    }
                }
                array_push($views_values, $item);
            endwhile;
        }
        wp_reset_postdata();
        $results_value = array();
        for ($i = 0; $i <= $number_days; $i++) {
            $views_item = 0;
            foreach ($views_values as $views_value) {
                $views_item += $views_value[$i];
            }
            array_push($results_value, $views_item);
        }

        return $results_value;
    }
}


/**
 * Total View Project
 */
if (!function_exists('felan_total_view_project')) {
    function felan_total_view_project($number_days = 7)
    {
        global $current_user;
        wp_get_current_user();
        $user_id = $current_user->ID;

        $args = array(
            'post_type' => 'project',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'author' => $user_id,
        );

        $data = new WP_Query($args);
        $total_post = $data->found_posts;
        $views_values = array();
        if ($total_post > 0) {
            while ($data->have_posts()) : $data->the_post();
                $id = get_the_ID();
                $views_date = get_post_meta($id, 'felan_view_project', true);
                $item = array();
                for ($i = $number_days; $i >= 0; $i--) {
                    $date = date("Y-m-d", strtotime("-" . $i . " day"));

                    if (isset($views_date[$date])) {
                        $item[] = $views_date[$date];
                    } else {
                        $item[] = 0;
                    }
                }
                array_push($views_values, $item);
            endwhile;
        }
        wp_reset_postdata();
        $results_value = array();
        for ($i = 0; $i <= $number_days; $i++) {
            $views_item = 0;
            foreach ($views_values as $views_value) {
                $views_item += $views_value[$i];
            }
            array_push($results_value, $views_item);
        }

        return $results_value;
    }
}

/**
 * Total view jobs details
 */
if (!function_exists('felan_total_view_jobs_details')) {
    function felan_total_view_jobs_details($jobs_id)
    {

        if ($jobs_id) {
            $jobs_id = $jobs_id;
        } else {
            $jobs_id = get_the_ID();
        }
        $views_values = get_post_meta($jobs_id, 'felan_view_by_date', true);
        $views = 0;
        if ($views_values) {
            foreach ($views_values as $values) {
                $views += $values;
            }
        }
        if ($views > 1) {
            $text = esc_html__('views', 'felan-framework');
        } else {
            $text = esc_html__('view', 'felan-framework');
        }
        ?>
        <div class="jobs-view">
            <i class="far fa-eye"></i>
            <span class="count"><?php echo sprintf('%1s (%2s)', $views, $text) ?></span>
        </div>
    <?php
    }
}

/**
 * Total view service details
 */
if (!function_exists('felan_total_view_service_details')) {
    function felan_total_view_service_details($service_id)
    {
        if ($service_id) {
            $service_id = $service_id;
        } else {
            $service_id = get_the_ID();
        }
        $views_values = get_post_meta($service_id, 'felan_view_service', true);
        $views = 0;
        if ($views_values) {
            foreach ($views_values as $values) {
                $views += $values;
            }
        }
        if ($views > 1) {
            $text = esc_html__('views', 'felan-framework');
        } else {
            $text = esc_html__('view', 'felan-framework');
        }
    ?>
        <div class="service-view">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M2.54961 13.4056C2.2778 13.0326 2.1419 12.8462 2.04835 12.4854C1.98388 12.2367 1.98388 11.7633 2.04835 11.5146C2.1419 11.1538 2.2778 10.9674 2.54961 10.5944C4.03902 8.55068 7.30262 5 12 5C16.6974 5 19.961 8.55068 21.4504 10.5944C21.7222 10.9674 21.8581 11.1538 21.9516 11.5146C22.0161 11.7633 22.0161 12.2367 21.9516 12.4854C21.8581 12.8462 21.7222 13.0326 21.4504 13.4056C19.961 15.4493 16.6974 19 12 19C7.30262 19 4.03902 15.4493 2.54961 13.4056Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M12 14C13.1046 14 14 13.1046 14 12C14 10.8954 13.1046 10 12 10C10.8954 10 10 10.8954 10 12C10 13.1046 10.8954 14 12 14Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span class="count"><?php echo sprintf('%1s %2s', $views, $text) ?></span>
        </div>
    <?php
    }
}

/**
 * Total view project details
 */
if (!function_exists('felan_total_view_project_details')) {
    function felan_total_view_project_details($project_id)
    {
        if ($project_id) {
            $project_id = $project_id;
        } else {
            $project_id = get_the_ID();
        }
        $views_values = get_post_meta($project_id, 'felan_view_project', true);
        $views = 0;
        if ($views_values) {
            foreach ($views_values as $values) {
                $views += $values;
            }
        }
		return $views;
    }
}

/**
 * Total Applications Jobs ID
 */
if (!function_exists('felan_total_applications_jobs_id')) {
    function felan_total_applications_jobs_id($jobs_id)
    {

        $args = array(
            'post_type' => 'applicants',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => FELAN_METABOX_PREFIX . 'applicants_jobs_id',
                    'value' => $jobs_id,
                    'compare' => '='
                )
            ),
        );
        $data = new WP_Query($args);
        $total_post = $data->found_posts;

        return $total_post;
    }
}

/**
 * Total Applications Project ID
 */
if (!function_exists('felan_total_applications_project_id')) {
    function felan_total_applications_project_id($project_id)
    {
        $args = array(
            'post_type' => 'project-proposal',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => FELAN_METABOX_PREFIX . 'proposal_project_id',
                    'value' => $project_id,
                    'compare' => '='
                )
            ),
        );
        $data = new WP_Query($args);
        $total_post = $data->found_posts;

        return $total_post;
    }
}

/**
 * Has Project Proposal
 */
if (!function_exists('felan_has_project_proposal')) {
    function felan_has_project_proposal($project_id)
    {
        global $current_user;
        $user_id = $current_user->ID;

        $args_proposal = array(
            'post_type' => 'project-proposal',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'author' => $user_id,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => FELAN_METABOX_PREFIX . 'proposal_project_id',
                    'value' => $project_id,
                    'compare' => '='
                )
            ),
        );

        $data_proposal = new WP_Query($args_proposal);
        $has_project_proposal = $data_proposal->found_posts;

        return $has_project_proposal;
    }
}

/**
 * Total Jobs
 */
if (!function_exists('felan_total_jobs')) {
    function felan_total_jobs()
    {
        global $current_user;
        $user_id = $current_user->ID;
        $args = array(
            'post_type' => 'jobs',
            'posts_per_page' => -1,
            'author' => $user_id,
        );
        $data = new WP_Query($args);
        $total_post = $data->found_posts;

        return $total_post;
    }
}


/**
 * Total Projects
 */
if (!function_exists('felan_total_projects_proposal')) {
    function felan_total_projects_proposal()
    {
        global $current_user;
        $user_id = $current_user->ID;
        $args = array(
            'post_type' => 'project-proposal',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'author' => $user_id,
        );
        $data = new WP_Query($args);
        $total_post = $data->found_posts;

        return $total_post;
    }
}


/**
 * Total Jobs Apply
 */
if (!function_exists('felan_total_jobs_apply')) {
    function felan_total_jobs_apply($jobs_id, $number_days = 7)
    {

        if (empty($jobs_id)) {
            return;
        }
        $total_apply = array();
        for ($i = $number_days; $i >= 0; $i--) {
            $date = date("Y-m-d", strtotime("-" . $i . " day"));
            $args = array(
                'post_type' => 'applicants',
                'ignore_sticky_posts' => 1,
                'posts_per_page' => -1,
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'applicants_jobs_id',
                        'value' => $jobs_id,
                        'compare' => '='
                    ),
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'applicants_date',
                        'value' => $date,
                        'compare' => '='
                    ),
                ),
            );
            $data = new WP_Query($args);
            $total_post = $data->found_posts;
            array_push($total_apply, $total_post);
        }

        return $total_apply;
    }
}


/**
 * Jobs Date
 */
if (!function_exists('felan_view_jobs_date')) {
    function felan_view_jobs_date($jobs_id, $number_days = 7)
    {

        if (empty($jobs_id)) {
            return;
        }
        $views_date = get_post_meta($jobs_id, 'felan_view_by_date', true);
        if (!is_array($views_date)) {
            $views_date = array();
        }

        $views_values = array();
        for ($i = $number_days; $i >= 0; $i--) {
            $date = date("Y-m-d", strtotime("-" . $i . " day"));
            if (isset($views_date[$date])) {
                $views_values[] = $views_date[$date];
            } else {
                $views_values[] = 0;
            }
        }

        return $views_values;
    }
}

/**
 * Total Project Apply
 */
if (!function_exists('felan_total_project_apply')) {
    function felan_total_project_apply($project_id, $number_days = 7)
    {

        if (empty($project_id)) {
            return;
        }
        $total_apply = array();
        for ($i = $number_days; $i >= 0; $i--) {
            $date = date("Y-m-d", strtotime("-" . $i . " day"));
            $args = array(
                'post_type' => 'applicants',
                'ignore_sticky_posts' => 1,
                'posts_per_page' => -1,
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'proposal_project_id',
                        'value' => $project_id,
                        'compare' => '='
                    ),
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'applicants_date',
                        'value' => $date,
                        'compare' => '='
                    ),
                ),
            );
            $data = new WP_Query($args);
            $total_post = $data->found_posts;
            array_push($total_apply, $total_post);
        }

        return $total_apply;
    }
}

/**
 * Project Date
 */
if (!function_exists('felan_view_project_date')) {
    function felan_view_project_date($project_id, $number_days = 7)
    {

        if (empty($project_id)) {
            return;
        }
        $views_date = get_post_meta($project_id, 'felan_view_by_date', true);
        if (!is_array($views_date)) {
            $views_date = array();
        }

        $views_values = array();
        for ($i = $number_days; $i >= 0; $i--) {
            $date = date("Y-m-d", strtotime("-" . $i . " day"));
            if (isset($views_date[$date])) {
                $views_values[] = $views_date[$date];
            } else {
                $views_values[] = 0;
            }
        }

        return $views_values;
    }
}

/**
 * User Review
 */
if (!function_exists('felan_total_user_review')) {
    function felan_total_user_review()
    {

        global $current_user;
        wp_get_current_user();
        $user_id = $current_user->ID;

        global $wpdb;
        $comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE meta.meta_key = 'jobs_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 OR comment.user_id = $user_id )";

        $get_comments = $wpdb->get_results($comments_query);

        $comment_author = array();
        if (!is_null($get_comments)) {
            foreach ($get_comments as $comment) {
                $comment_id = $comment->comment_ID;
                $post_id = $comment->comment_post_ID;
                $comment_user_id = $comment->user_id;
                $post_author_id = get_post_field('post_author', $post_id);
                if ($post_author_id == $user_id) {
                    $comment_author[] = $comment_id;
                }
            }
        }
        $total_post = count($comment_author);

        add_user_meta($user_id, 'user_list_comment_id', $comment_author);

        return $total_post;
    }
}

if (!function_exists('felan_admin_taxonomy_terms')) {
    function felan_admin_taxonomy_terms($post_id, $taxonomy, $post_type)
    {

        $terms = get_the_terms($post_id, $taxonomy);

        if (!is_wp_error($terms) && $terms != false) {
            $results = array();
            foreach ($terms as $term) {
                $results[] = sprintf(
                    '<a href="%s">%s</a>',
                    esc_url(add_query_arg(array(
                        'post_type' => $post_type,
                        $taxonomy => $term->slug
                    ), 'edit.php')),
                    esc_html(sanitize_term_field('name', $term->name, $term->term_id, $taxonomy, 'display'))
                );
            }

            return join(', ', $results);
        }

        return false;
    }
}

/**
 * felan_admin_taxonomy_terms
 */
if (!function_exists('felan_admin_taxonomy_terms')) {
    function felan_admin_taxonomy_terms($post_id, $taxonomy, $post_type)
    {

        $terms = get_the_terms($post_id, $taxonomy);

        if (!is_wp_error($terms) && $terms != false) {
            $results = array();
            foreach ($terms as $term) {
                $results[] = sprintf(
                    '<a href="%s">%s</a>',
                    esc_url(add_query_arg(array(
                        'post_type' => $post_type,
                        $taxonomy => $term->slug
                    ), 'edit.php')),
                    esc_html(sanitize_term_field('name', $term->name, $term->term_id, $taxonomy, 'display'))
                );
            }

            return join(', ', $results);
        }

        return false;
    }
}

/**
 * Get format number
 */
if (!function_exists('felan_get_format_number')) {
    function felan_get_format_number($number, $decimals = 0)
    {
        $number = doubleval($number);
        if ($number) {
            $dec_point = felan_get_option('decimal_separator', '.');
            $thousands_sep = felan_get_option('thousand_separator', ',');

            return number_format($number, $decimals, $dec_point, $thousands_sep);
        } else {
            return 0;
        }
    }
}

/**
 * Custom Field Freelancer
 */
if (!function_exists('felan_custom_field_freelancer')) {
    function felan_custom_field_freelancer($tabs, $newTab = false)
    {
        $custom_field_freelancer = felan_render_custom_field('freelancer');
        $freelancer_id = felan_get_post_id_freelancer();
        $freelancer_data = get_post($freelancer_id);

        $check_tabs = false;
        foreach ($custom_field_freelancer as $field) {
            if ($field['tabs'] == $tabs) {
                $check_tabs = true;
            }
        }

        if (count($custom_field_freelancer) > 0) {
            if ($newTab == true) { ?>
                <div class="row">
                    <?php foreach ($custom_field_freelancer as $field) {
                        if ($field['section'] == $tabs) { ?>
                    <?php felan_get_template("dashboard/freelancer/profile/additional/field.php", array(
                                'field' => $field,
                                'freelancer_data' => $freelancer_data
                            ));
                        }
                    } ?>
                </div>
                <?php } else {
                if ($check_tabs == true) : ?>
                    <div class="freelancer-additional block-from">
                        <h6><?php esc_html_e('Additional Filed', 'felan-framework') ?></h6>
                        <div class="row">
                            <?php foreach ($custom_field_freelancer as $field) {
                                if ($field['tabs'] == $tabs) { ?>
                            <?php felan_get_template("dashboard/freelancer/profile/additional/field.php", array(
                                        'field' => $field,
                                        'freelancer_data' => $freelancer_data
                                    ));
                                }
                            } ?>
                        </div>
                    </div>
                <?php endif;
            }
        }
    }
}


/**
 * Custom Field Single Freelancer
 */
if (!function_exists('felan_custom_field_single_freelancer')) {
    function felan_custom_field_single_freelancer($tabs, $newTab = false)
    {
        $custom_field_freelancer = felan_render_custom_field('freelancer');
        $freelancer_id = felan_get_post_id_freelancer();
        $freelancer_data = get_post($freelancer_id);

        $check_tabs = false;
        foreach ($custom_field_freelancer as $field) {
            if ($field['tabs'] == $tabs) {
                $check_tabs = true;
            }
        }

        if (count($custom_field_freelancer) > 0) {
            if ($newTab == true) { ?>
                <?php foreach ($custom_field_freelancer as $field) {
                    if ($field['section'] == $tabs) { ?>
                <?php felan_get_template("freelancer/single/additional/field.php", array(
                            'field' => $field,
                            'freelancer_data' => $freelancer_data
                        ));
                    }
                } ?>
                <?php } else {
                if ($check_tabs == true) : ?>
                    <?php foreach ($custom_field_freelancer as $field) {
                        if ($field['tabs'] == $tabs) { ?>
                    <?php felan_get_template("freelancer/single/additional/field.php", array(
                                'field' => $field,
                                'freelancer_data' => $freelancer_data
                            ));
                        }
                    } ?>
            <?php endif;
            }
        }
    }
}

/**
 * Get Data List Messages
 */
if (!function_exists('felan_get_data_list_message')) {
    function felan_get_data_list_message($first = false, $status_pending = false)
    {
        global $current_user;
        $user_id = $current_user->ID;

        $args = array(
            'post_type' => 'messages',
            'order' => 'DESC',
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => FELAN_METABOX_PREFIX . 'creator_message',
                    'value' => $user_id,
                    'compare' => '=='
                ),
                array(
                    'key' => FELAN_METABOX_PREFIX . 'reply_message',
                    'value' => $user_id,
                    'compare' => '=='
                )
            ),
        );

        if ($status_pending == true) {
            $args['post_status'] = 'pending';
        } else {
            $args['post_status'] = array('publish', 'pending');
        }

        if ($first == true) {
            $args['posts_per_page'] = 1;
        } else {
            $args['posts_per_page'] = -1;
        }

        $data = new WP_Query($args);

        return $data;
    }
}

/**
 * Get total unread message
 */
if (!function_exists('felan_get_total_unread_message')) {
    function felan_get_total_unread_message()
    {
        $data_list = felan_get_data_list_message(false, true);
        $total_unread = $data_list->found_posts;

        if ($total_unread > 0) { ?>
            <span class="badge"><?php esc_html_e($total_unread) ?></span>
            <?php } else {
            return;
        }
    }
}


/**
 * Get Data Notification
 */
if (!function_exists('felan_get_data_notification')) {
    function felan_get_data_notification()
    {
        global $current_user;
        $user_id = $current_user->ID;

        $args = array(
            'post_type' => 'notification',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => FELAN_METABOX_PREFIX . 'user_receive_noti',
                    'value' => $user_id,
                    'compare' => '=='
                ),
            ),
        );

        $data = get_posts($args);

        return $data;
    }
}

/**
 * Get Data Ajax Notification
 */
if (!function_exists('felan_get_data_ajax_notification')) {
    function felan_get_data_ajax_notification($post_current_id, $actions)
    {
        global $current_user;
        $user_id = $current_user->ID;

        $user_receive = get_post_field('post_author', $post_current_id);
        $link = get_the_permalink($post_current_id);
        $page_link = '#';

        //Action
        if (
            in_array("felan_user_employer", (array)$current_user->roles)
            || in_array("felan_user_freelancer", (array)$current_user->roles)
        ) {
            switch ($actions) {
                case 'add-apply':
                    $mess_noti = esc_html__('A new applicant on job', 'felan-framework');
                    $actions = esc_html__('Applicant', 'felan-framework');
                    $felan_jobs_page_id = felan_get_option('felan_jobs_dashboard_page_id');
                    $page_link = get_page_link($felan_jobs_page_id);
                    break;
                case 'add-message':
                    $mess_noti = esc_html__('A new message', 'felan-framework');
                    $actions = esc_html__('Message', 'felan-framework');
                    $page_link = felan_get_permalink('messages');
                    $link = '';
                    break;
                case 'add-wishlist':
                    $mess_noti = esc_html__('A new wishlist on job', 'felan-framework');
                    $actions = esc_html__('Wishlist', 'felan-framework');
                    $page_link = '#';
                    break;
                case 'add-invite':
                    $mess_noti = esc_html__('A new invite', 'felan-framework');
                    $actions = esc_html__('Invite', 'felan-framework');
                    $page_link = felan_get_permalink('my_jobs');
                    $link = '';
                    break;
                case 'add-follow-company':
                    $mess_noti = esc_html__('A new follow on company', 'felan-framework');
                    $actions = esc_html__('Follow Company', 'felan-framework');
                    $page_link = '#';
                    break;
                case 'add-review-company':
                    $mess_noti = esc_html__('A new review on company', 'felan-framework');
                    $page_link = '#';
                    $actions = esc_html__('Review Company', 'felan-framework');
                    break;
                // case 'add-review-freelancer':
                // 	$mess_noti = esc_html__( 'A new review', 'felan-framework' );
                // 	$actions   = esc_html__( 'Review Freelancer', 'felan-framework' );
                // 	$page_link = felan_get_permalink( 'freelancer_reviews' );
                // 	$link      = '';
                // 	break;
                case 'add-follow-freelancer':
                    $mess_noti = esc_html__('A new follow', 'felan-framework');
                    $actions = esc_html__('Follow Freelancer', 'felan-framework');
                    $link = '';
                    $page_link = felan_get_permalink('freelancer_company');
                    break;
                case 'add-meeting':
                    $mess_noti = esc_html__('A new meeting on job', 'felan-framework');
                    $actions = esc_html__('Meeting', 'felan-framework');
                    $jobs_id = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'mee_jobs_id', true);
                    $link = get_the_permalink($jobs_id);
                    $user_receive = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'user_receive_mee', true);
                    $page_link = felan_get_permalink('freelancer_meetings');
                    break;
                case 'add-proposal':
                    $mess_noti = esc_html__('New Proposal Received', 'felan-framework');
                    $actions = esc_html__('Proposal', 'felan-framework');
                    $felan_project_page_id = felan_get_option('felan_projects_page_id');
                    $page_link = get_page_link($felan_project_page_id);
                    break;
                case 'update-proposal':
                    $mess_noti = esc_html__('New Proposal Updated', 'felan-framework');
                    $actions = esc_html__('Proposal', 'felan-framework');
                    $felan_project_page_id = felan_get_option('felan_projects_page_id');
                    $page_link = get_page_link($felan_project_page_id);
                    break;
                case 'approved-dispute':
                    $mess_noti = esc_html__('New Dispute Approved', 'felan-framework');
                    $actions = esc_html__('Dispute', 'felan-framework');
                    $felan_disputes_page_id = felan_get_option('felan_disputes_page_id');
                    $page_link              = get_page_link($felan_disputes_page_id);
                    $project_id             = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'disputes_project_id', true);
                    $user_receive = get_post_field('post_author', $project_id);
                    $link = $project_id;
                    break;
                case 'add-service-order':
                    $mess_noti = esc_html__('New Service Order', 'felan-framework');
                    $actions = esc_html__('Service Order', 'felan-framework');
                    $felan_service_page_id = felan_get_option('felan_freelancer_service_page_id');
                    $page_link = get_page_link($felan_service_page_id);
                    $service_id = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);
                    $user_receive = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'service_order_author_id', true);
                    $link = $service_id;
                    break;
                case 'denies-dispute':
                    $mess_noti = esc_html__('New Dispute denied', 'felan-framework');
                    $actions = esc_html__('Dispute denied', 'felan-framework');
                    $felan_disputes_page_id = felan_get_option('felan_disputes_page_id');
                    $page_link              = get_page_link($felan_disputes_page_id);
                    $project_id             = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'disputes_project_id', true);
                    $user_receive = get_post_field('post_author', $project_id);
                    $link = $project_id;
                    break;
                case 'approved-dispute-service':
                    $mess_noti = esc_html__('New Dispute Approved', 'felan-framework');
                    $actions = esc_html__('Dispute service', 'felan-framework');
                    $felan_disputes_page_id = felan_get_option('felan_disputes_page_id');
                    $page_link              = get_page_link($felan_disputes_page_id);
                    $order_id               = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'disputes_service_order_id', true);
                    $service_id             = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);
                    $user_receive = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_user_id', true);
                    $link = $service_id;
                    break;
                case 'denies-dispute-service':
                    $mess_noti = esc_html__('New Dispute denied', 'felan-framework');
                    $actions = esc_html__('Dispute denied service', 'felan-framework');
                    $felan_disputes_page_id = felan_get_option('felan_disputes_page_id');
                    $page_link              = get_page_link($felan_disputes_page_id);
                    $order_id               = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'disputes_service_order_id', true);
                    $service_id             = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);
                    $user_receive = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_user_id', true);
                    $link = $service_id;
                    break;
                case 'project-dispute-message':
                    $mess_noti = esc_html__('New Message in Dispute for Project', 'felan-framework');
                    $actions = esc_html__('Message Dispute Project', 'felan-framework');
                    $felan_disputes_page_id = felan_get_option('felan_disputes_page_id');
                    $page_link              = get_page_link($felan_disputes_page_id);
                    $project_id             = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'disputes_project_id', true);
                    $user_receive = get_post_field('post_author', $project_id);
                    $link = $project_id;
                    break;
                case 'project-dispute-message-employer':
                    $mess_noti = esc_html__('New Message in Dispute for Project', 'felan-framework');
                    $actions = esc_html__('Message Dispute Project', 'felan-framework');
                    $felan_disputes_page_id = felan_get_option('felan_freelancer_disputes_page_id');
                    $page_link              = get_page_link($felan_disputes_page_id);
                    $project_id             = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'disputes_project_id', true);
                    $user_receive           = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'project_disputes_freelancer_id', true);
                    $link = $project_id;
                    break;
                case 'service-dispute-message':
                    $mess_noti = esc_html__('New Message in Dispute for service', 'felan-framework');
                    $actions = esc_html__('Message Dispute service', 'felan-framework');
                    $felan_disputes_page_id = felan_get_option('felan_disputes_page_id');
                    $page_link              = get_page_link($felan_disputes_page_id);
                    $order_id               = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'disputes_service_order_id', true);
                    $service_id             = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);
                    $user_receive = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_user_id', true);
                    $link = $service_id;
                    break;
                case 'service-dispute-message-employer':
                    $mess_noti = esc_html__('New Message in Dispute for service', 'felan-framework');
                    $actions = esc_html__('Message Dispute service', 'felan-framework');
                    $felan_disputes_page_id = felan_get_option('felan_freelancer_disputes_page_id');
                    $page_link              = get_page_link($felan_disputes_page_id);
                    $order_id               = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'disputes_service_order_id', true);
                    $service_id             = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);
                    $user_receive           = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'disputes_freelancer_id', true);
                    $link = $service_id;
                    break;
                case 'employer-approve-proposal':
                    $mess_noti = esc_html__('Employer Approve Proposal', 'felan-framework');
                    $actions = esc_html__('Employer Approve Proposal', 'felan-framework');
                    $felan_project_page_id  = felan_get_option('felan_my_project_page_id');
                    $page_link              = get_page_link($felan_project_page_id);
                    $project_id             = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'proposal_project_id', true);
                    $user_receive           = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'creator_message', true);
                    $link = $project_id;
                    break;
                case 'employer-reject-proposal':
                    $mess_noti = esc_html__('Employer Reject Proposal', 'felan-framework');
                    $actions = esc_html__('Employer Reject Proposal', 'felan-framework');
                    $felan_project_page_id  = felan_get_option('felan_my_project_page_id');
                    $page_link              = get_page_link($felan_project_page_id);
                    $project_id             = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'proposal_project_id', true);
                    $user_receive           = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'creator_message', true);
                    $link = $project_id;
                    break;
                case 'employer-create-dispute-proposal':
                    $mess_noti = esc_html__('Employer Create Dispute Proposal', 'felan-framework');
                    $actions = esc_html__('Employer Create Dispute Proposal', 'felan-framework');
                    $felan_disputes_page    = felan_get_option('felan_freelancer_disputes_page_id');
                    $page_link              = get_page_link($felan_disputes_page);
                    $project_id             = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'proposal_project_id', true);
                    $user_receive           = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'creator_message', true);
                    $link = $project_id;
                    break;
                case 'service-message-employer':
                    $mess_noti = esc_html__('New Message in Service', 'felan-framework');
                    $actions = esc_html__('Message Service', 'felan-framework');
                    $felan_service_page_id  = felan_get_option('felan_freelancer_service_page_id');
                    $page_link              = get_page_link($felan_service_page_id) . '?order_id=' . $post_current_id;
                    $service_id             = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);
                    $user_receive = get_post_field('post_author', $service_id);
                    $link = $service_id;
                    break;
                case 'service-message-freelancer':
                    $mess_noti = esc_html__('New Message in Service', 'felan-framework');
                    $actions = esc_html__('Message Service', 'felan-framework');
                    $felan_service_page_id  = felan_get_option('felan_employer_service_page_id');
                    $page_link              = get_page_link($felan_service_page_id) . '?order_id=' . $post_current_id;
                    $service_id             = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);
                    $user_receive           = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'service_order_user_id', true);
                    $link = $service_id;
                    break;
                case 'employer-complete-service':
                    $mess_noti = esc_html__('Service Completed', 'felan-framework');
                    $actions = esc_html__('Service Completed', 'felan-framework');
                    $felan_service_page_id  = felan_get_option('felan_freelancer_service_page_id');
                    $page_link              = get_page_link($felan_service_page_id) . '?order_id=' . $post_current_id;
                    $service_id             = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);
                    $user_receive           = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'service_order_author_id', true);
                    $link = $service_id;
                    break;
                case 'employer-cancel-service':
                    $mess_noti = esc_html__('Service Canceled', 'felan-framework');
                    $actions = esc_html__('Service Canceled', 'felan-framework');
                    $felan_service_page_id  = felan_get_option('felan_freelancer_service_page_id');
                    $page_link              = get_page_link($felan_service_page_id) . '?order_id=' . $post_current_id;
                    $service_id             = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);
                    $user_receive           = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'service_order_author_id', true);
                    $link = $service_id;
                    break;
                case 'employer-create-dispute-service':
                    $mess_noti = esc_html__('Employer Create Dispute Service', 'felan-framework');
                    $actions = esc_html__('Employer Create Dispute Service', 'felan-framework');
                    $felan_disputes_page    = felan_get_option('felan_freelancer_disputes_page_id');
                    $page_link              = get_page_link($felan_disputes_page);
                    $service_id             = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);
                    $user_receive           = get_post_meta($post_current_id, FELAN_METABOX_PREFIX . 'service_order_author_id', true);
                    $link = $service_id;
                    break;
            }
        }

        //New
        $new_post = array(
            'post_type' => 'notification',
            'post_status' => 'publish',
        );
        $post_title = get_the_title($post_current_id);
        if (isset($post_title)) {
            $new_post['post_title'] = $post_title;
        }
        if (!empty($new_post['post_title'])) {
            $post_id = wp_insert_post($new_post, true);
        }
        if (isset($post_id)) {
            update_post_meta($post_id, FELAN_METABOX_PREFIX . 'user_send_noti', $user_id);
            update_post_meta($post_id, FELAN_METABOX_PREFIX . 'user_receive_noti', $user_receive);
            update_post_meta($post_id, FELAN_METABOX_PREFIX . 'link_post_noti', $link);
            update_post_meta($post_id, FELAN_METABOX_PREFIX . 'mess_noti', $mess_noti);
            update_post_meta($post_id, FELAN_METABOX_PREFIX . 'action_noti', $actions);
            update_post_meta($post_id, FELAN_METABOX_PREFIX . 'link_page_noti', $page_link);
        }
    }
}

/**
 * Get company founded
 */
if (!function_exists('felan_get_company_founded')) {
    function felan_get_company_founded($option = true)
    {
        global $company_meta_data;
        $founded_min = intval(felan_get_option('value_founded_min'));
        $founded_max = intval(felan_get_option('value_founded_max'));
        if (!empty($founded_min) && !empty($founded_min)) {
            if ($option) {
                for ($founded = $founded_min; $founded <= $founded_max; $founded++) { ?>
                    <option value="<?php echo $founded ?>" <?php if (isset($company_meta_data[FELAN_METABOX_PREFIX . 'company_founded'][0])) {
                                                                if ($company_meta_data[FELAN_METABOX_PREFIX . 'company_founded'][0] == $founded) {
                                                                    echo 'selected';
                                                                }
                                                            } ?>><?php echo $founded ?></option>
                <?php } ?>
            <?php } else {
                $foundeds = array();
                for ($founded = $founded_min; $founded <= $founded_max; $founded++) {
                    $foundeds[] = $founded;
                };

                return array_combine($foundeds, $foundeds);
            };
        };
    };
}

/**
 * Get social network
 */
if (!function_exists('felan_get_social_network')) {
    function felan_get_social_network($id, $post_type)
    {
        $social_name = $social_icon = $social_name_field = $social_url_field = $value_icon = array();
        $felan_social_fields = felan_get_option('felan_social_fields');
        $felan_social_tabs = get_post_meta($id, FELAN_METABOX_PREFIX . $post_type . '_social_tabs');

        if (is_array($felan_social_tabs)) {
            foreach ($felan_social_tabs as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $k1 => $v1) {
                        $social_name_field[] = $v1[FELAN_METABOX_PREFIX . $post_type . '_social_name'];
                        $social_url_field[] = $v1[FELAN_METABOX_PREFIX . $post_type . '_social_url'];
                    }
                }
            }
        }

        if (is_array($felan_social_fields)) {
            foreach ($felan_social_fields as $key => $value) {
                $social_name[] = $value['social_name'];
                $social_icon[] = $value['social_icon'];
            }
        }

        $felan_social_field = array_combine($social_name, $social_icon);
        $icon_filter = array_filter(
            $felan_social_field,
            function ($key) use ($social_name_field) {
                if (in_array($key, $social_name_field)) {
                    return $social_name_field;
                }
            },
            ARRAY_FILTER_USE_KEY
        );
        ksort($icon_filter);
        $felan_social_fields = array_combine($social_name_field, $social_url_field);
        $url_filter = array_filter(
            $felan_social_fields,
            function ($key) use ($social_name_field) {
                if (in_array($key, $social_name_field)) {
                    return $social_name_field;
                }
            },
            ARRAY_FILTER_USE_KEY
        );
        ksort($url_filter);
        $value_icon = array_values($icon_filter);
        $value_url = array_values($url_filter);
        if (!empty($value_icon) && !empty($value_url)) {
            $felan_socials = array_combine($value_icon, $value_url);
            foreach ($felan_socials as $key => $value) {
                if (!empty($value)) {
                    echo '<li><a href="' . esc_url($value) . '">' . $key . '</a></li>';
                }
            }
        }
    };
}

/**
 * Image size
 */
if (!function_exists('felan_image_resize')) {
    function felan_image_resize($data, $image_size)
    {
        if (preg_match('/\d+x\d+/', $image_size)) {
            $image_sizes = explode('x', $image_size);
            $image_src = felan_image_resize_id($data, $image_sizes[0], $image_sizes[1], true);
        } else {
            if (!in_array($image_size, array('full', 'thumbnail'))) {
                $image_size = 'full';
            }
            $image_src = wp_get_attachment_image_src($data, $image_size);
            if ($image_src && !empty($image_src[0])) {
                $image_src = $image_src[0];
            }
        }

        return $image_src;
    }
}

/**
 * Image resize by url
 */
if (!function_exists('felan_image_resize_url')) {
    function felan_image_resize_url($url, $width = null, $height = null, $crop = true, $retina = false)
    {

        global $wpdb;

        if (empty($url)) {
            return new WP_Error('no_image_url', esc_html__('No image URL has been entered.', 'felan-framework'), $url);
        }

        if (class_exists('Jetpack') && method_exists('Jetpack', 'get_active_modules') && in_array('photon', Jetpack::get_active_modules())) {
            $args_crop = array(
                'resize' => $width . ',' . $height,
                'crop' => '0,0,' . $width . 'px,' . $height . 'px'
            );
            $url = jetpack_photon_url($url, $args_crop);
        }

        // Get default size from database
        $width = ($width) ? $width : get_option('thumbnail_size_w');
        $height = ($height) ? $height : get_option('thumbnail_size_h');

        // Allow for different retina sizes
        $retina = $retina ? ($retina === true ? 2 : $retina) : 1;

        // Get the image file path
        $file_path = parse_url($url);
        $file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];

        // Check for Multisite
        if (is_multisite()) {
            global $blog_id;
            $blog_details = get_blog_details($blog_id);
            $file_path = str_replace($blog_details->path, '/', $file_path);
            //$file_path = str_replace( $blog_details->path . 'files/', '/wp-content/blogs.dir/' . $blog_id . '/files/', $file_path );
        }

        // Destination width and height variables
        $dest_width = $width * $retina;

        $dest_height = $height * $retina;

        // File name suffix (appended to original file name)
        $suffix = "{$dest_width}x{$dest_height}";

        // Some additional info about the image
        $info = pathinfo($file_path);
        $dir = $info['dirname'];
        $ext = $name = '';
        if (!empty($info['extension'])) {
            $ext = $info['extension'];
            $name = wp_basename($file_path, ".$ext");
        }

        if ('bmp' == $ext) {
            return new WP_Error('bmp_mime_type', esc_html__('Image is BMP. Please use either JPG or PNG.', 'felan-framework'), $url);
        }

        // Suffix applied to filename
        $suffix = "{$dest_width}x{$dest_height}";

        // Get the destination file name
        $dest_file_name = "{$dir}/{$name}-{$suffix}.{$ext}";

        if (!file_exists($dest_file_name)) {

            /*
             *  Bail if this image isn't in the Media Library.
             *  We only want to resize Media Library images, so we can be sure they get deleted correctly when appropriate.
             */
            $query = $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE guid='%s'", $url);
            $get_attachment = $wpdb->get_results($query);

            //if (!$get_attachment)
            //return array('url' => $url, 'width' => $width, 'height' => $height);

            // Load Wordpress Image Editor
            $editor = wp_get_image_editor($file_path);

            if (is_wp_error($editor)) {
                return array('url' => $url, 'width' => $width, 'height' => $height);
            }

            // Get the original image size
            $size = $editor->get_size();
            $orig_width = $size['width'];
            $orig_height = $size['height'];

            $src_x = $src_y = 0;
            $src_w = $orig_width;
            $src_h = $orig_height;

            if ($crop) {

                $cmp_x = $orig_width / $dest_width;
                $cmp_y = $orig_height / $dest_height;

                // Calculate x or y coordinate, and width or height of source
                if ($cmp_x > $cmp_y) {
                    $src_w = round($orig_width / $cmp_x * $cmp_y);
                    $src_x = round(($orig_width - ($orig_width / $cmp_x * $cmp_y)) / 2);
                } else if ($cmp_y > $cmp_x) {
                    $src_h = round($orig_height / $cmp_y * $cmp_x);
                    $src_y = round(($orig_height - ($orig_height / $cmp_y * $cmp_x)) / 2);
                }
            }

            // Time to crop the image!
            $editor->crop($src_x, $src_y, $src_w, $src_h, $dest_width, $dest_height);

            // Now let's save the image
            $saved = $editor->save($dest_file_name);

            // Get resized image information
            $resized_url = str_replace(wp_basename($url), wp_basename($saved['path']), $url);
            $resized_width = $saved['width'];
            $resized_height = $saved['height'];
            $resized_type = $saved['mime-type'];

            // Add the resized dimensions to original image metadata (so we can delete our resized images when the original image is delete from the Media Library)
            if ($get_attachment) {
                if ($get_attachment[0]->ID) {
                    $metadata = wp_get_attachment_metadata($get_attachment[0]->ID);
                    if (isset($metadata['image_meta'])) {
                        $metadata['image_meta']['resized_images'][] = $resized_width . 'x' . $resized_height;
                        wp_update_attachment_metadata($get_attachment[0]->ID, $metadata);
                    }
                }
            }

            // Create the image array
            $image_array = array(
                'url' => $resized_url,
                'width' => $resized_width,
                'height' => $resized_height,
                'type' => $resized_type
            );
        } else {
            $image_array = array(
                'url' => str_replace(wp_basename($url), wp_basename($dest_file_name), $url),
                'width' => $dest_width,
                'height' => $dest_height,
                'type' => $ext
            );
        }

        // Return image array
        return $image_array;
    }
}

/*
 * Image resize by id
 */
if (!function_exists('felan_image_resize_id')) {
    function felan_image_resize_id($images_id, $width = null, $height = null, $crop = true, $retina = false)
    {
        $output = '';
        $image_src = wp_get_attachment_image_src($images_id, 'full');
        if (is_array($image_src)) {
            $resize = felan_image_resize_url($image_src[0], $width, $height, $crop, $retina);
            if ($resize != null && is_array($resize)) {
                $output = $resize['url'];
            }
        }

        return $output;
    }
}

/**
 * Get name company
 */
if (!function_exists('felan_select_post_company')) {
    function felan_select_post_company($type_option = false)
    {
        global $current_user, $jobs_meta_data;
        $user_id = $current_user->ID;
        $jobs_user_select_company = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'jobs_user_select_company', true);
        $meta_query_args = array(
            'post_type' => 'company',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        );
        if ($type_option) {
            $meta_query_args['author'] = $user_id;
        }
        $meta_query = new WP_Query($meta_query_args);
        $key_company = array("");
        $values_company = array("None");
        foreach ($meta_query->posts as $post) {
            $values_company[] = $post->post_title;
            $key_company[] = $post->ID;
        };
        if ($type_option) {
            echo '<option value="new_company" data-url="">' . esc_html__('Create new company', 'felan-framework') . '</option>';
            echo '<option value="" data-url="">' . esc_html__('None', 'felan-framework') . '</option>';
            foreach ($meta_query->posts as $post) {
                $company_logo = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'company_logo', false);
                $company_logo_url = isset($company_logo[0]['url']) ? $company_logo[0]['url'] : ''; ?>
                <option <?php if ((isset($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_select_company']) && $jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_select_company'][0] == $post->ID) || (isset($jobs_user_select_company) && $jobs_user_select_company == $post->ID)) {
                            echo 'selected';
                        } ?> value="<?php echo $post->ID; ?>" data-url="<?php echo $company_logo_url ?>"><?php echo $post->post_title; ?>
                </option>
            <?php }
        } else {
            return array_combine($key_company, $values_company);
        }
    }
}


/**
 * Get select project company
 */
if (!function_exists('felan_select_project_company')) {
    function felan_select_project_company($type_option = false)
    {
        global $current_user, $project_meta_data;
        $user_id = $current_user->ID;
        $meta_query_args = array(
            'post_type' => 'company',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        );
        if ($type_option) {
            $meta_query_args['author'] = $user_id;
        }
        $meta_query = new WP_Query($meta_query_args);
        $key_company = array("");
        $values_company = array("None");
        foreach ($meta_query->posts as $post) {
            $values_company[] = $post->post_title;
            $key_company[] = $post->ID;
        };
        if ($type_option) {
            echo '<option value="new_company" data-url="">' . esc_html__('Create new company', 'felan-framework') . '</option>';

            echo '<option value="" data-url="">' . esc_html__('None', 'felan-framework') . '</option>';
            foreach ($meta_query->posts as $post) {
                $company_logo = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'company_logo', false);
                $company_logo_url = isset($company_logo[0]['url']) ? $company_logo[0]['url'] : ''; ?>
                <option <?php if ((isset($project_meta_data[FELAN_METABOX_PREFIX . 'project_select_company']) && $project_meta_data[FELAN_METABOX_PREFIX . 'project_select_company'][0] == $post->ID)) {
                            echo 'selected';
                        } ?> value="<?php echo $post->ID; ?>" data-url="<?php echo $company_logo_url ?>"><?php echo $post->post_title; ?>
                </option>
            <?php }
        } else {
            return array_combine($key_company, $values_company);
        }
    }
}


/**
 * Get posts company
 */
if (!function_exists('felan_posts_company')) {
    function felan_posts_company($company_id, $posts_per_page = -1)
    {
        if (empty($company_id)) {
            return;
        }
        $meta_query_args = array(
            'post_type' => 'jobs',
            'post_status' => 'publish',
            'posts_per_page' => $posts_per_page,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => FELAN_METABOX_PREFIX . 'jobs_select_company',
                    'value' => $company_id,
                    'compare' => '=='
                )
            ),
        );
        $meta_query = new WP_Query($meta_query_args);

        return $meta_query;
    }
}

/**
 * Get field count
 */
if (!function_exists('felan_field_count')) {
    function felan_field_count($field, $key, $post_type)
    {
        if (empty($field)) {
            return;
        }
        $args = array(
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => $key,
                    'value' => $field,
                    'compare' => '=='
                )
            ),
        );
        $data = new WP_Query($args);

        return $data->found_posts;
    }
}

/**
 * Get field count
 */
if (!function_exists('felan_post_count_applicant_project')) {
    function felan_post_count_applicant_project($project_id)
    {
        $args_applicants = array(
            'post_type' => 'project-proposal',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => FELAN_METABOX_PREFIX . 'proposal_project_id',
                    'value' => $project_id,
                    'compare' => '='
                )
            ),
        );

        $data = new WP_Query($args_applicants);
        return $data->found_posts;
    }
}

/**
 * Service Count Sale
 */
if (!function_exists('felan_service_count_sale')) {
    function felan_service_count_sale($author_id, $service_id)
    {
        $args_count = array(
            'post_type' => 'service_order',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => FELAN_METABOX_PREFIX . 'service_order_author_id',
                    'value' => $author_id,
                    'compare' => '==',
                ),
                array(
                    'key' => FELAN_METABOX_PREFIX . 'service_order_payment_status',
                    'value' => 'completed',
                    'compare' => '==',
                ),
                array(
                    'key' => FELAN_METABOX_PREFIX . 'service_order_item_id',
                    'value' => $service_id,
                    'compare' => '==',
                )
            ),
        );
        $data_count = get_posts($args_count);

        $count_sales = 0;
        $text_sale =  esc_html__('sale', 'felan-framework');

        if (!empty($data_count)) {
            $count_sales = count($data_count);
        }
        if ($count_sales > 1) {
            $text_sale = esc_html__('sales', 'felan-framework');
        }

        return $count_sales . ' ' . $text_sale;
    }
}


/**
 * Get applicants status
 */
if (!function_exists('felan_applicants_status')) {
    function felan_applicants_status($id)
    {
        $applicants_status = get_post_meta($id, FELAN_METABOX_PREFIX . 'applicants_status');
        if (!empty($applicants_status)) {
            if ($applicants_status[0] == 'rejected') {
                echo '<span class="label label-close">' . esc_html__('Rejected', 'felan-framework') . '</span>';
            } elseif ($applicants_status[0] == 'approved') {
                echo '<span class="label label-open">' . esc_html__('Approved', 'felan-framework') . '</span>';
            } else {
                echo '<span class="label label-pending">' . esc_html__('Pending', 'felan-framework') . '</span>';
            }
        } else {
            echo '<span class="label label-pending">' . esc_html__('Pending', 'felan-framework') . '</span>';
        }
    }
}

/**
 * Get total post
 */
if (!function_exists('felan_total_post')) {
    function felan_total_post($post_type, $meta_key)
    {
        global $current_user;
        $user_id = $current_user->ID;
        $post_in = array();
        if ($meta_key == 'my_wishlist') {
            $post_in = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'my_wishlist', true);
        } elseif ($meta_key == 'my_follow') {
            $post_in = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'my_follow', true);
        } elseif ($meta_key == 'my_invite') {
            $post_in = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'my_invite', true);
        } elseif ($meta_key == 'follow_freelancer') {
            $post_in = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'follow_freelancer', true);
        }

        $meta_query_args = array(
            'post_type' => $post_type,
            'post__in' => $post_in,
            'ignore_sticky_posts' => 1,
        );
        $meta_query = new WP_Query($meta_query_args);
        if (!empty($post_in) && $meta_query->found_posts > 0) {
            return $meta_query->found_posts;
        } else {
            return 0;
        }
    }
}

/**
 * Get total my service
 */
if (!function_exists('felan_total_my_service')) {
    function felan_total_my_service()
    {
        global $current_user;
        $user_id = $current_user->ID;
        $args = array(
            'post_type' => 'service_order',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => FELAN_METABOX_PREFIX . 'service_order_author_id',
                    'value' => $user_id,
                    'compare' => '==',
                )
            ),
        );
        $data = new WP_Query($args);

        return $data->found_posts;
    }
}

/**
 * Get total employer service order
 */
if (!function_exists('felan_total_employer_service_order')) {
    function felan_total_employer_service_order()
    {
        global $current_user;
        $user_id = $current_user->ID;
        $args = array(
            'post_type' => 'service_order',
            'author' => $user_id,
            'posts_per_page' => -1,
        );
        $data = new WP_Query($args);

        return $data->found_posts;
    }
}

/**
 * Get comment id by post
 */
if (!function_exists('felan_comment_id_by_post_and_user')) {
    function felan_comment_id_by_post_and_user($post_id, $user_id)
    {
        $args = array(
            'post_id' => $post_id,
            'user_id' => $user_id,
            'number' => 1,
        );

        $comments = get_comments($args);
        if (!empty($comments)) {
            return $comments[0]->comment_ID;
        }

        return null;
    }
}

/**
 * Get total employer sending
 */
if (!function_exists('felan_total_employer_sending')) {
    function felan_total_employer_sending($user_id)
    {
        $service_price = $project_price = 0;
        $args = array(
            'post_type' => 'service_order',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => FELAN_METABOX_PREFIX . 'service_order_user_id',
                    'value' => $user_id,
                    'compare' => '==',
                ),
                array(
                    'key' => FELAN_METABOX_PREFIX . 'service_order_payment_status',
                    'value' => 'completed',
                    'compare' => '==',
                )
            ),
        );
        $data = new WP_Query($args);
        $currency_sign_default = felan_get_option('currency_sign_default');
        $service_price_total = 0;
        while ($data->have_posts()) : $data->the_post();
            $order_id = get_the_ID();
            $service_order_price = (float)get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_price_not_format', true);
            if (!empty($service_order_price)) {
                // $service_price = str_replace($currency_sign_default, '', $service_order_price);
                $service_price = is_numeric($service_order_price) ? $service_order_price : 0;
                $service_price_total += $service_price;
            }
        endwhile;

        $args_project = apply_filters(
            'felan/dashboard/employer/applicants/args_project',
            array(
                'post_type' => 'project',
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'posts_per_page' => -1,
                'author' => $user_id,
                'orderby' => 'date',
            )
        );

        $data_project = new WP_Query($args_project);
        $project_employer_id = array();
        if ($data_project->have_posts()) {
            while ($data_project->have_posts()) : $data_project->the_post();
                $project_employer_id[] = get_the_ID();
            endwhile;
        }

        if (!empty($project_employer_id)) {
            $args_applicants = array(
                'post_type' => 'project-proposal',
                'ignore_sticky_posts' => 1,
                'posts_per_page' => -1,
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'proposal_project_id',
                        'value' => $project_employer_id,
                        'compare' => 'IN'
                    ),
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'proposal_status',
                        'value' => 'completed',
                        'compare' => '==',
                    )
                ),
            );
            $data_applicants = new WP_Query($args_applicants);
            $project_price = 0;
            while ($data_applicants->have_posts()) : $data_applicants->the_post();
                $proposal_id = get_the_ID();
                $proposal_price = (float)get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_price', true);
                if (!empty($proposal_price)) {
                    $project_price += $proposal_price;
                }
            endwhile;
        }

        $sending_price = intval($service_price_total) +  intval($project_price);
        $sending_price = $currency_sign_default . felan_get_format_number($sending_price);

        return $sending_price;
    }
}


/**
 * Get service to freelancer
 */
if (!function_exists('felan_id_service_to_freelancer')) {
    function felan_id_service_to_freelancer($service_id)
    {
        $author_id = get_post_field('post_author', $service_id);
        $args_freelancer = array(
            'post_type' => 'freelancer',
            'posts_per_page' => 1,
            'author' => $author_id,
        );
        $current_user_posts = get_posts($args_freelancer);
        $freelancer_id = !empty($current_user_posts) ? $current_user_posts[0]->ID : '';

        return $freelancer_id;
    }
}

/**
 * Get project to company
 */
if (!function_exists('felan_id_project_to_company')) {
    function felan_id_project_to_company($project_id)
    {
        $author_id = get_post_field('post_author', $project_id);
        $args_company = array(
            'post_type' => 'company',
            'posts_per_page' => 1,
            'author' => $author_id,
        );
        $current_user_posts = get_posts($args_company);
        $company_id = !empty($current_user_posts) ? $current_user_posts[0]->ID : '';

        return $company_id;
    }
}

/**
 * Get total service
 */
if (!function_exists('felan_total_service')) {
    function felan_total_service()
    {
        global $current_user;
        wp_get_current_user();
        $user_id = $current_user->ID;

        $args = array(
            'post_type' => 'service',
            'posts_per_page' => -1,
            'ignore_sticky_posts' => 1,
            'author' => $user_id,
        );

        $data = new WP_Query($args);
        $total_post = $data->found_posts;

        return $total_post;
    }
}


/**
 * Get total service to freelancer
 */
if (!function_exists('felan_total_services_to_freelancer')) {
    function felan_total_services_to_freelancer($freelancer_id)
    {
        $author_id = get_post_field('post_author', $freelancer_id);
        $args_freelancer = array(
            'post_type' => 'service',
            'posts_per_page' => -1,
            'ignore_sticky_posts' => 1,
            'author' => $author_id,
        );
        $get_service = get_posts($args_freelancer);
        $count_posts = !empty($get_service) ? count($get_service) : 0;

        return $count_posts;
    }
}

/**
 * Get member since
 */
if (!function_exists('felan_get_member_since')) {
    function felan_get_member_since($post_id)
    {
        $formatted_date = '';
        $current_post = get_post($post_id);
        if ($current_post) {
            $post_date_timestamp = strtotime($current_post->post_date);
            $formatted_month = date('M', $post_date_timestamp);
            $formatted_month = strtolower($formatted_month);
            $formatted_month = ucfirst($formatted_month);
            $formatted_year = date('Y', $post_date_timestamp);
            $formatted_date = $formatted_month . ' ' . $formatted_year;
        }

        return apply_filters('felan_get_member_since', $formatted_date, $post_id);
    }
}


/**
 * Get repeater social
 */
if (!function_exists('felan_get_repeater_social')) {
    function felan_get_repeater_social($social_selected, $type_option = false, $data = false)
    {
        $social_name = $social_url = array();
        $felan_social_fields = felan_get_option('felan_social_fields');
        if ($type_option) {
            echo '<option value="">' . esc_html__('None', 'felan-framework') . '</option>';
            foreach ($felan_social_fields as $social_fields) {
                if ($data) {
                    $selected = '';
                    if ($social_selected == $social_fields['social_name']) {
                        $selected = 'selected';
                    }
                    echo '<option value="' . $social_fields['social_name'] . '"' . $selected . '>' . $social_fields['social_name'] . '</option>';
                } else {
                    echo '<option value="' . $social_fields['social_name'] . '">' . $social_fields['social_name'] . '</option>';
                }
            }
        } else {
            if (!empty($felan_social_fields)) {
                foreach ($felan_social_fields as $social_fields) {
                    $social_name[] = $social_fields['social_name'];
                };
            }
            return array_combine($social_name, $social_name);
        }
    }
}


/**
 * Get select currency type
 */
if (!function_exists('felan_get_select_currency_type')) {
    function felan_get_select_currency_type($options_selected = false)
    {
        global $current_user, $jobs_meta_data, $freelancer_meta_data, $service_meta_data;
        $user_id = $current_user->ID;
        $keys = $values = array();
        $options_currency_type = felan_get_option('currency_fields');
        $currency_type_default = felan_get_option('currency_type_default');
        $currency_sign_default = felan_get_option('currency_sign_default');
        $jobs_user_currency_type = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'jobs_user_currency_type', true);
        $jobs_currency_type = isset($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_currency_type']) ? $jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_currency_type'][0] : '';
        $freelancer_currency_type = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_currency_type']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_currency_type'][0] : '';
        if (!empty($options_currency_type)) {
            foreach ($options_currency_type as $key => $value) {
                $keys[] = $value['currency_sign'];
                $values[] = $value['currency_type'];
            }
        }
        if ($options_selected) {
            echo '<option value="' . $currency_sign_default . '">(' . $currency_sign_default . ') - ' . $currency_type_default . '</option>';
            foreach ($options_currency_type as $key => $value) { ?>
                <?php if ($value['currency_sign']) : ?>
                    <option <?php if (!empty($options_currency_type) && ($jobs_user_currency_type == $value['currency_sign'] || $jobs_currency_type == $value['currency_sign'] || $freelancer_currency_type == $value['currency_sign'])) {
                                echo 'selected';
                            } ?> value="<?php echo $value['currency_sign'] ?>">
                        (<?php echo $value['currency_sign'] . ') - ' . $value['currency_type'] ?>
                    </option>
                <?php endif; ?>
                <?php }
        } else {
            $currency_default = array($currency_sign_default => $currency_type_default);
            $currency = array_combine($keys, $values);

            return array_merge($currency_default, $currency);
        }
    }
}


/**
 * Get Post ID Freelancer
 */
if (!function_exists('felan_get_post_id_freelancer')) {
    function felan_get_post_id_freelancer()
    {
        global $current_user;
        $user_id = $current_user->ID;;

        $args_freelancer = array(
            'post_type' => 'freelancer',
            'posts_per_page' => 1,
            'author' => $user_id,
            'post_status' => 'any',
        );
        $current_user_posts = get_posts($args_freelancer);
        $freelancer_id = !empty($current_user_posts) ? $current_user_posts[0]->ID : '';

        return $freelancer_id;
    }
}

/**
 * Get currency type
 */
if (!function_exists('felan_get_currency_type')) {
    function felan_get_currency_type($jobs_id, $currency = 1)
    {
        // $jobs_id = get_the_ID();
        $jobs_currency_type = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_currency_type', true);
        if ($currency == 1) {
            $currency_type = $jobs_currency_type;
        } else {
            $array_key = felan_get_select_currency_type();
            $output_currency = array_filter($array_key, function ($k) {
                // $jobs_id = get_the_ID();
                $jobs_currency_type = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_currency_type', true);

                return $k == $jobs_currency_type;
            }, ARRAY_FILTER_USE_KEY);
            $currency_type = $output_currency[$jobs_currency_type];
        }

        return $currency_type;
    }
}

/**
 * Get salary jobs
 */
if (!function_exists('felan_get_salary_jobs')) {
    function felan_get_salary_jobs($jobs_id)
    {
        $jobs_salary_active = felan_get_option('enable_single_jobs_salary', '1');
        if (empty($jobs_salary_active)) {
            return;
        }
        $jobs_salary_show = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_salary_show', true);
        $jobs_salary_rate = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_salary_rate', true);
        if ($jobs_salary_rate == 'hour') {
            $jobs_salary_rate = esc_html__(' /hour', 'felan-framework');
        } elseif ($jobs_salary_rate == 'day') {
            $jobs_salary_rate = esc_html__(' /day', 'felan-framework');
        } elseif ($jobs_salary_rate == 'week') {
            $jobs_salary_rate = esc_html__(' /week', 'felan-framework');
        } elseif ($jobs_salary_rate == 'month') {
            $jobs_salary_rate = esc_html__(' /month', 'felan-framework');
        } elseif ($jobs_salary_rate == 'year') {
            $jobs_salary_rate = esc_html__(' /year', 'felan-framework');
        } else {
            $jobs_salary_rate = '';
        }

        $jobs_currency_type = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_currency_type', true);
        $currency_sign_default = felan_get_option('currency_sign_default');

        $options_currency_type = felan_get_option('currency_fields', true);
        $keys = $values = array();
        if (is_array($options_currency_type)) {
            foreach ($options_currency_type as $key => $value) {
                $keys[] = $value['currency_sign'];
                $values[] = $value['currency_conversion'];
            }
        }
        $conversion_combine = array_combine($keys, $values);
        $conversion_filter = array_filter($conversion_combine, function ($k) {
            $jobs_id = get_the_ID();
            $jobs_currency_type = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_currency_type', true);

            return $k == $jobs_currency_type;
        }, ARRAY_FILTER_USE_KEY);
        if ($currency_sign_default == $jobs_currency_type) {
            $currency_conversion = 1;
        } else {
            $currency_conversion = intval(implode($conversion_filter));
            if ($currency_conversion == 0) {
                $currency_conversion = 1;
            }
        }

        $jobs_salary_minimum = felan_get_format_number(intval(get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_salary_minimum', true)) * $currency_conversion);
        $jobs_salary_maximum = felan_get_format_number(intval(get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_salary_maximum', true)) * $currency_conversion);
        $jobs_maximum_price = felan_get_format_number(intval(get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_maximum_price', true)) * $currency_conversion);
        $jobs_minimum_price = felan_get_format_number(intval(get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_minimum_price', true)) * $currency_conversion);

        $currency_position = felan_get_option('currency_position');
        $currency_leff = $currency_right = '';
        if ($currency_position == 'before') {
            $currency_leff = felan_get_currency_type($jobs_id);
        } else {
            $currency_right = felan_get_currency_type($jobs_id);
        }
        if ($jobs_salary_show == 'range') {
            $salary = sprintf("%1s%2s%s - %1s%2s%s%s", $currency_leff, $jobs_salary_minimum, $currency_right, $currency_leff, $jobs_salary_maximum, $currency_right, $jobs_salary_rate);
        } elseif ($jobs_salary_show == 'starting_amount') {
            $salary = esc_html_e('Min ', 'felan-framework') ?><?php echo $currency_leff . $jobs_minimum_price . $currency_right . $jobs_salary_rate ?>
                <?php } elseif ($jobs_salary_show == 'maximum_amount') {
                $salary = esc_html_e('Max ', 'felan-framework') ?><?php echo $currency_leff . $jobs_maximum_price . $currency_right . $jobs_salary_rate ?>
            <?php } else {
                $salary = esc_html_e('Negotiable Price', 'felan-framework') ?>
            <?php }

            return $salary;
        }
    }

    /**
     * Get salary freelancer
     */
    if (!function_exists('felan_get_salary_freelancer')) {
        function felan_get_salary_freelancer($freelancer_id, $border_line = '/')
        {
            if (empty($freelancer_id)) {
                return;
            }
            $offer_salary = felan_get_format_number(intval(get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_offer_salary', true)));
            $salary_type = !empty(get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_salary_type')) ? get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_salary_type')[0] : '';
            $currency_type = !empty(get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_currency_type')) ? get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_currency_type')[0] : '';
            $currency_position = felan_get_option('currency_position');
            $currency_leff = $currency_right = '';
            if ($currency_position == 'before') {
                $currency_leff = $currency_type;
            } else {
                $currency_right = $currency_type;
            }
            ?>
            <?php if (!empty($offer_salary)) { ?>
                <div class="freelancer-salary">
                    <?php echo sprintf(__('<span>%1$s%2$s</span>%3$s %4$s%5$s'), $currency_leff, $offer_salary, $currency_right, $border_line, $salary_type); ?>
                </div>
                <?php }
        }
    }

    /**
     * Get value rate project
     */
    if (!function_exists('felan_project_maximum_time')) {
        function felan_project_maximum_time($project_id)
        {
            if (empty($project_id)) {
                return;
            }

            $projects_budget_show = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_budget_show', true);
            $project_value_rate = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_value_rate', true);
            $project_budget_rate = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_budget_rate', true);
            $project_maximum_hours = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_maximum_hours', true);

            if ($project_budget_rate == 'hour') {
                $project_budget_rate = ($project_value_rate > 1) ? esc_html__('hours', 'felan-framework') : esc_html__('hour', 'felan-framework');
            } elseif ($project_budget_rate == 'day') {
                $project_budget_rate = ($project_value_rate > 1) ? esc_html__('days', 'felan-framework') : esc_html__('day', 'felan-framework');
            } elseif ($project_budget_rate == 'week') {
                $project_budget_rate = ($project_value_rate > 1) ? esc_html__('weeks', 'felan-framework') : esc_html__('week', 'felan-framework');
            } elseif ($project_budget_rate == 'month') {
                $project_budget_rate = ($project_value_rate > 1) ? esc_html__('months', 'felan-framework') : esc_html__('month', 'felan-framework');
            } elseif ($project_budget_rate == 'year') {
                $project_budget_rate = ($project_value_rate > 1) ? esc_html__('years', 'felan-framework') : esc_html__('year', 'felan-framework');
            } else {
                $project_budget_rate = '';
            }

            $projects_time = $project_value_rate . ' ' . $project_budget_rate;
            if ($projects_budget_show == 'hourly') {
                $projects_time = $project_maximum_hours;
            }

            return $projects_time;
        }
    }

    /**
     * Get budget project
     */
    if (!function_exists('felan_get_budget_project')) {
        function felan_get_budget_project($project_id)
        {
            if (empty($project_id)) {
                return;
            }

            $currency_sign_default = felan_get_option('currency_sign_default');
            $project_budget_minimum = felan_get_format_number(intval(get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_budget_minimum', true)));
            $project_budget_maximum = felan_get_format_number(intval(get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_budget_maximum', true)));

            $currency_position = felan_get_option('currency_position');
            $currency_leff = $currency_right = '';
            if ($currency_position == 'before') {
                $currency_leff = $currency_sign_default;
            } else {
                $currency_right = $currency_sign_default;
            }

            $budget = sprintf(
                '<span>%1s%2s%s</span><span class="line ml-1 mr-1">-</span><span>%1s%2s%s</span>',
                $currency_leff,
                $project_budget_minimum,
                $currency_right,
                $currency_leff,
                $project_budget_maximum,
                $currency_right
            );

            return $budget;
        }
    }

    /**
     * Get expiration apply
     */
    if (!function_exists('get_head_time_unit')) {
        function get_head_time_unit($head_time_unit)
        {
            if ($head_time_unit == 'Day') {
                return esc_html__('Day', 'felan-framework');
            } else if ($head_time_unit == 'Days') {
                return esc_html__('Days', 'felan-framework');
            } else if ($head_time_unit == 'Week') {
                return esc_html__('week', 'felan-framework');
            } else if ($head_time_unit == 'Weeks') {
                return esc_html__('weeks', 'felan-framework');
            } else if ($head_time_unit == 'Month') {
                return esc_html__('month', 'felan-framework');
            } else if ($head_time_unit == 'Months') {
                return esc_html__('months', 'felan-framework');
            } else if ($head_time_unit == 'Year') {
                return esc_html__('year', 'felan-framework');
            } else if ($head_time_unit == 'Years') {
                return esc_html__('years', 'felan-framework');
            }
            return null;
        }
    }

    /**
     * Get expiration apply
     */
    if (!function_exists('felan_get_expiration_apply')) {
        function felan_get_expiration_apply($jobs_id)
        {
            if (empty($jobs_id)) {
                return;
            }
            $public_date = get_the_date('Y-m-d', $jobs_id);
            $enable_jobs_expires = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'enable_jobs_expires', true);
            $current_date = date('Y-m-d');
            $jobs_days_single = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_days_closing', true);

            if ($enable_jobs_expires == '1') {
                $jobs_days_closing = '0';
            } else {
                if ($jobs_days_single) {
                    $jobs_days_closing = $jobs_days_single;
                } else {
                    $jobs_days_closing = felan_get_option('jobs_number_days', true);
                }
            }

            $expiration_date = date('Y-m-d', strtotime($public_date . '+' . $jobs_days_closing . ' days'));
            $seconds = strtotime($expiration_date) - strtotime($current_date);
            $dtF = new \DateTime('@0');
            $dtT = new \DateTime("@$seconds");
            $expiration_days = $dtF->diff($dtT)->format('%a');
            $status = apply_filters('felan_get_expiration_apply', 'expired');
            if ($expiration_date > $public_date && $expiration_date > $current_date) :
                $expiration_days = $expiration_days;
            else :
                if (get_post_status($jobs_id) != 'expired') {
                    $data = array(
                        'ID' => $jobs_id,
                        'post_type' => 'jobs',
                        'post_status' => $status
                    );
                    wp_update_post($data);
                    if ($status == 'expired') {
                        update_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'enable_jobs_expires', 1);
                    }
                    $expiration_days = 0;
                }

            endif;

            return $expiration_days;
        }
    }


    /**
     * Get status apply
     */
    if (!function_exists('felan_get_status_apply')) {
        function felan_get_status_apply($jobs_id)
        {
            if (empty($jobs_id)) {
                return;
            }
            global $current_user;
            $user_id = $current_user->ID;
            $author_id = get_post_field('post_author', $jobs_id);

            if (in_array('felan_user_freelancer', (array)$current_user->roles)) {
                $args_freelancer = array(
                    'post_type' => 'freelancer',
                    'author' => $user_id,
                );
                $query = new WP_Query($args_freelancer);
                if ($query->have_posts()) {
                    $freelancer_id = $query->post->ID;
                } else {
                    $freelancer_id = null;
                }
            }
            $post_status = get_post_status($jobs_id);
            $key_apply = false;
            $jobs_select_apply = !empty(get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_select_apply')) ? get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_select_apply')[0] : '';
            $jobs_apply_external = !empty(get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_apply_external')) ? get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_apply_external')[0] : '';
            $my_apply = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'my_apply', true);
            if (!empty($my_apply)) {
                $key_apply = array_search($jobs_id, $my_apply);
            }

            $args_applicants = array(
                'post_type'      => 'applicants',
                'posts_per_page' => -1,
                'post_status'    => 'any',
                'author'         => $user_id,
                'fields'         => 'ids',
            );
            $applicants_ids = get_posts($args_applicants);
            $applicants_jobs_id = array();
            if (!empty($applicants_ids)) {
                foreach ($applicants_ids as $applicants_id) {
                    $applicants_jobs_id[] = get_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'applicants_jobs_id', true);
                }
            }
            $key_applicants_jobs = false;
            if (!empty($applicants_jobs_id)) {
                $key_applicants_jobs = array_search($jobs_id, $applicants_jobs_id);
            }

            $freelancer_paid_submission_type = felan_get_option('freelancer_paid_submission_type');
            $check_package = felan_get_field_check_freelancer_package('jobs_apply');
            $freelancer_package_number_jobs_apply = intval(get_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_jobs_apply', true));
            $enable_apply_login = felan_get_option('enable_apply_login');
            if ($enable_apply_login == '1') {
                if ((is_user_logged_in() && in_array('felan_user_freelancer', (array)$current_user->roles))) {
                    if ($user_id == $author_id) { ?>
                        <a href="#" class="felan-button button-block btn-add-to-message"
                            data-text="<?php echo esc_attr('This feature is not available for the same user ID', 'felan-framework'); ?>">
                            <?php esc_html_e('Apply Now', 'felan-framework') ?>
                        </a>
                        <?php } else {
                        if ($key_applicants_jobs !== false) { ?>
                            <button class="felan-button button-disbale button-block"><?php esc_html_e('Applied', 'felan-framework') ?></button>
                        <?php } elseif ($post_status === "pause") { ?>
                            <button class="felan-button button-disbale button-block"><?php esc_html_e('Pause', 'felan-framework') ?></button>
                        <?php } elseif (felan_get_expiration_apply($jobs_id) == 0) { ?>
                            <button class="felan-button button-disbale button-block"><?php esc_html_e('Expires', 'felan-framework') ?></button>
                        <?php } else { ?>
                            <?php if ($jobs_select_apply == 'external') { ?>
                                <a href="<?php echo esc_url($jobs_apply_external) ?>" target="_blank" class="felan-button button-block"><i class="far fa-external-link-alt"></i><?php esc_html_e('Apply Now', 'felan-framework') ?></a>
                            <?php } elseif ($jobs_select_apply == 'call-to') { ?>
                                <a href="#felan_form_apply_jobs" class="felan-button button-block felan-button-apply felan_form_apply_jobs" data-jobs_id="<?php echo $jobs_id ?>" data-freelancer_id="<?php echo $freelancer_id ?>"><?php esc_html_e('Call To Apply', 'felan-framework') ?></a>
                            <?php } else { ?>
                                <?php if ($check_package == -1 || $check_package == 0 || ($freelancer_paid_submission_type == 'freelancer_per_package' && $freelancer_package_number_jobs_apply < 1)) { ?>
                                    <a href="<?php echo get_permalink(felan_get_option('felan_freelancer_package_page_id')); ?>" class="felan-button button-block">
                                        <?php esc_html_e('Renew Package', 'felan-framework') ?>
                                    </a>
                                <?php } else { ?>
                                    <a href="#felan_form_apply_jobs" class="felan-button felan-button-apply felan_form_apply_jobs button-block" data-jobs_id="<?php echo $jobs_id ?>" data-freelancer_id="<?php echo $freelancer_id ?>"><?php esc_html_e('Apply Now', 'felan-framework') ?></a>
                                <?php } ?>
                            <?php } ?>
                    <?php }
                    } ?>
                <?php } else { ?>
                    <div class="account logged-out">
                        <a href="#popup-form" class="btn-login felan-button button-block"><?php esc_html_e('Apply Now', 'felan-framework') ?></a>
                    </div>
                <?php } ?>
                <?php
            }

            if ($enable_apply_login !== '1') {
                if (is_user_logged_in()) {
                    if ((in_array('felan_user_freelancer', (array)$current_user->roles))) {
                        if ($key_applicants_jobs !== false) { ?>
                            <button class="felan-button button-disbale button-block"><?php esc_html_e('Applied', 'felan-framework') ?></button>
                        <?php } elseif ($post_status === "pause") { ?>
                            <button class="felan-button button-disbale button-block"><?php esc_html_e('Pause', 'felan-framework') ?></button>
                        <?php } elseif (felan_get_expiration_apply($jobs_id) == 0) { ?>
                            <button class="felan-button button-disbale button-block"><?php esc_html_e('Expires', 'felan-framework') ?></button>
                        <?php } else { ?>
                            <?php if ($jobs_select_apply == 'external') { ?>
                                <a href="<?php echo esc_url($jobs_apply_external) ?>" target="_blank" class="felan-button button-block"><i class="far fa-external-link-alt"></i><?php esc_html_e('Apply Now', 'felan-framework') ?></a>
                            <?php } elseif ($jobs_select_apply == 'call-to') { ?>
                                <a href="#felan_form_apply_jobs" class="felan-button button-block felan-button-apply felan_form_apply_jobs" data-jobs_id="<?php echo $jobs_id ?>" data-freelancer_id="<?php echo $freelancer_id ?>"><?php esc_html_e('Call To Apply', 'felan-framework') ?></a>
                            <?php } else { ?>
                                <?php if ($check_package == -1 || $check_package == 0 || ($freelancer_paid_submission_type == 'freelancer_per_package' && $freelancer_package_number_jobs_apply < 1)) { ?>
                                    <a href="<?php echo get_permalink(felan_get_option('felan_freelancer_package_page_id')); ?>" class="felan-button button-block">
                                        <?php esc_html_e('Renew Package', 'felan-framework') ?>
                                    </a>
                                <?php } else { ?>
                                    <a href="#felan_form_apply_jobs" class="felan-button button-block felan-button-apply felan_form_apply_jobs" data-jobs_id="<?php echo $jobs_id ?>" data-freelancer_id="<?php echo $freelancer_id ?>"><?php esc_html_e('Apply Now', 'felan-framework') ?></a>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="account logged-out">
                            <a href="#popup-form" class="btn-login felan-button button-block"><?php esc_html_e('Apply Now', 'felan-framework') ?></a>
                        </div>
                    <?php }
                } else {
                    if ($jobs_select_apply == 'external') { ?>
                        <a href="<?php echo esc_url($jobs_apply_external) ?>" target="_blank" class="felan-button button-block"><i class="far fa-external-link-alt"></i><?php esc_html_e('Apply Now', 'felan-framework') ?></a>
                    <?php } elseif ($jobs_select_apply == 'call-to') { ?>
                        <a href="#felan_form_apply_jobs" class="felan-button button-block felan-button-apply felan_form_apply_jobs" data-jobs_id="<?php echo $jobs_id ?>" data-freelancer_id="<?php echo $freelancer_id ?>"><?php esc_html_e('Call To Apply', 'felan-framework') ?></a>
                    <?php } else if ($jobs_select_apply == 'internal') { ?>
                        <div class="account logged-out">
                            <a href="#popup-form" class="btn-login felan-button button-block"><?php esc_html_e('Login To Apply', 'felan-framework') ?></a>
                        </div>
                    <?php } else { ?>
                        <a href="#felan_form_apply_jobs" class="felan-button button-block felan-button-apply felan_form_apply_jobs" data-jobs_id="<?php echo $jobs_id ?>"><?php esc_html_e('Apply Now', 'felan-framework') ?></a>
            <?php }
                }
            }
        }
    }

    /**
     * Get Jobs Icon Status
     */
    if (!function_exists('felan_get_icon_status')) {
        function felan_get_icon_status($jobs_id)
        {
            if (empty($jobs_id)) {
                return;
            }
            $jobs_meta_data = get_post_custom($jobs_id);
            $jobs_featured = isset($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_featured']) ? $jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_featured'][0] : '0';
            $enable_jobs_expires = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'enable_jobs_expires', true);
            $enable_status_urgent = felan_get_option('enable_status_urgent', '1');
            $number_status_urgent = felan_get_option('number_status_urgent', '3');
            ?>
            <?php if ($jobs_featured == '1' && felan_get_expiration_apply($jobs_id) != '0') : ?>
                <span class="tooltip featured" data-title="<?php esc_attr_e('Featured', 'felan-framework') ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19.2703 16.2024C18.0927 19.0199 15.3079 21 12.0599 21C7.74661 21 4.25 17.5078 4.25 13.2C4.25 8.89218 5.89054 7.13076 8.45533 3C13.2614 5.09993 13.2614 11.4 13.2614 11.4C13.2614 11.4 14.8399 8.36201 18.0675 6.9C19.1024 9.94186 20.4978 13.2652 19.2703 16.2024Z" fill="#FFC402" stroke="#FFC402" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M12 18C9.23858 18 7 15.7614 7 13" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
            <?php endif; ?>
            <?php if (felan_get_expiration_apply($jobs_id) == '0' && $enable_jobs_expires == '1') : ?>
                <span class="tooltip filled" data-title="<?php esc_attr_e('Filled', 'felan-framework') ?>">
                    <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-filled.svg'); ?>" alt="<?php echo esc_attr('filled', 'felan-framework') ?>">
                </span>
            <?php endif; ?>
            <?php if (felan_get_expiration_apply($jobs_id) != '0' && $number_status_urgent > felan_get_expiration_apply($jobs_id) && $enable_status_urgent == '1' && $number_status_urgent !== '') : ?>
                <span class="tooltip urgent" data-title="<?php esc_attr_e('Urgent', 'felan-framework') ?>">
                    <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-urgent.svg'); ?>" alt="<?php echo esc_attr('urgent', 'felan-framework') ?>">
                </span>
            <?php endif; ?>
        <?php }
    }

    /**
     * Get
     * enqueue
     */
    if (!function_exists('felan_get_map_enqueue')) {
        function felan_get_map_enqueue()
        {
            $map_type = felan_get_option('map_type', 'mapbox');
            if ($map_type == 'google_map') {
                wp_enqueue_script('google-map');
            } else if ($map_type == 'mapbox') {
                wp_enqueue_style(FELAN_PLUGIN_PREFIX . 'mapbox-gl');
                wp_enqueue_style(FELAN_PLUGIN_PREFIX . 'mapbox-gl-geocoder');

                wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'mapbox-gl');
                wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'mapbox-gl-geocoder');
                wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'es6-promisel');
                wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'es6-promise');
            } else if ($map_type == 'openstreetmap') {
                wp_enqueue_style(FELAN_PLUGIN_PREFIX . 'mapbox-gl');
                wp_enqueue_style(FELAN_PLUGIN_PREFIX . 'leaflet');
                wp_enqueue_style(FELAN_PLUGIN_PREFIX . 'esri-leaflet');

                wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'mapbox-gl');
                wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'leaflet');
                wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'leaflet-src');
                wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'esri-leaflet');
                wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'esri-leaflet-geocoder');
            }
        }
    }

    /**
     * Get map type
     */
    if (!function_exists('felan_get_map_type')) {
        function felan_get_map_type($lng, $lat, $form_submit)
        {
            $map_type = felan_get_option('map_type', 'mapbox');
            $map_zoom_level = felan_get_option('map_zoom_level', '3');
            $map_marker = FELAN_PLUGIN_URL . 'assets/images/map-marker-icon.png';
            felan_get_map_enqueue();

            if ($map_type == 'google_map') {
                wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'google-map-submit');
                wp_localize_script(
                    FELAN_PLUGIN_PREFIX . 'google-map-submit',
                    'felan_google_map_submit_vars',
                    array(
                        'lng' => $lng,
                        'lat' => $lat,
                        'map_zoom' => $map_zoom_level,
                        'map_style' => json_encode(felan_get_option('googlemap_style')),
                        'map_type' => felan_get_option('googlemap_type', 'roadmap'),
                        'map_marker' => $map_marker,
                        'api_key' => felan_get_option('openstreetmap_api_key'),
                        'form_submit' => $form_submit,
                    )
                );
            } else if ($map_type == 'openstreetmap') {
                wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'openstreet-map-submit');
                wp_localize_script(
                    FELAN_PLUGIN_PREFIX . 'openstreet-map-submit',
                    'felan_openstreet_map_submit_vars',
                    array(
                        'lng' => $lng,
                        'lat' => $lat,
                        'map_zoom' => $map_zoom_level,
                        'map_style' => felan_get_option('openstreetmap_style', 'streets-v11'),
                        'map_marker' => $map_marker,
                        'api_key' => felan_get_option('openstreetmap_api_key'),
                        'form_submit' => $form_submit,
                    )
                );
            } else {
                wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'map-box-submit');
                wp_localize_script(
                    FELAN_PLUGIN_PREFIX . 'map-box-submit',
                    'felan_map_box_submit_vars',
                    array(
                        'lng' => $lng,
                        'lat' => $lat,
                        'map_zoom' => $map_zoom_level,
                        'map_style' => felan_get_option('mapbox_style', 'streets-v11'),
                        'map_marker' => $map_marker,
                        'api_key' => felan_get_option('mapbox_api_key'),
                        'form_submit' => $form_submit,
                    )
                );
            }
        }
    }

    /**
     * Get single map type
     */
    if (!function_exists('felan_get_single_map_type')) {
        function felan_get_single_map_type($lng, $lat)
        {

            $map_type = felan_get_option('map_type', 'mapbox');
            $map_zoom_level = felan_get_option('map_zoom_level', '3');
            $map_marker = FELAN_PLUGIN_URL . 'assets/images/map-marker-icon.png';
            felan_get_map_enqueue();

            if ($map_type == 'google_map') {
                wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'google-map-single');
                wp_localize_script(
                    FELAN_PLUGIN_PREFIX . 'google-map-single',
                    'felan_google_map_single_vars',
                    array(
                        'lng' => $lng,
                        'lat' => $lat,
                        'map_zoom' => $map_zoom_level,
                        'map_style' => json_encode(felan_get_option('googlemap_style')),
                        'map_type' => felan_get_option('googlemap_type', 'roadmap'),
                        'api_key' => felan_get_option('openstreetmap_api_key'),
                        'map_marker' => $map_marker,
                    )
                );
            } else if ($map_type == 'openstreetmap') {
                wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'openstreet-map-single');
                wp_localize_script(
                    FELAN_PLUGIN_PREFIX . 'openstreet-map-single',
                    'felan_openstreet_map_single_vars',
                    array(
                        'lng' => $lng,
                        'lat' => $lat,
                        'map_zoom' => $map_zoom_level,
                        'map_style' => felan_get_option('openstreetmap_style', 'streets-v11'),
                        'api_key' => felan_get_option('openstreetmap_api_key'),
                        'map_marker' => $map_marker,
                    )
                );
            } else {
                wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'map-box-single');
                wp_localize_script(
                    FELAN_PLUGIN_PREFIX . 'map-box-single',
                    'felan_map_box_single_vars',
                    array(
                        'lng' => $lng,
                        'lat' => $lat,
                        'map_zoom' => $map_zoom_level,
                        'map_style' => felan_get_option('mapbox_style', 'streets-v11'),
                        'api_key' => felan_get_option('mapbox_api_key'),
                        'map_marker' => $map_marker,
                    )
                );
            }
        }
    }

    /**
     * Get thumbnail enqueue
     */
    if (!function_exists('felan_get_thumbnail_enqueue')) {
        function felan_get_thumbnail_enqueue()
        {
            wp_enqueue_script('plupload');
            wp_enqueue_script('jquery-validate');
            $thumbnail_upload_nonce = wp_create_nonce('felan_thumbnail_allow_upload');
            $thumbnail_type = felan_get_option('felan_image_type');
            $thumbnail_file_size = felan_get_option('felan_image_max_file_size', '1000kb');
            $thumbnail_url = FELAN_AJAX_URL . '?action=felan_thumbnail_upload_ajax&nonce=' . esc_attr($thumbnail_upload_nonce);
            $thumbnail_text = esc_html__('Click here', 'felan-framework');

            wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'thumbnail');
            wp_localize_script(
                FELAN_PLUGIN_PREFIX . 'thumbnail',
                'felan_thumbnail_vars',
                array(
                    'ajax_url' => FELAN_AJAX_URL,
                    'thumbnail_title' => esc_html__('Valid file formats', 'felan-framework'),
                    'thumbnail_type' => $thumbnail_type,
                    'thumbnail_file_size' => $thumbnail_file_size,
                    'thumbnail_upload_nonce' => $thumbnail_upload_nonce,
                    'thumbnail_url' => $thumbnail_url,
                    'thumbnail_text' => $thumbnail_text,
                )
            );
        }
    }

    /**
     * Get avatar enqueue
     */
    if (!function_exists('felan_get_avatar_enqueue')) {
        function felan_get_avatar_enqueue()
        {
            wp_enqueue_script('plupload');
            wp_enqueue_script('jquery-validate');
            $avatar_upload_nonce = wp_create_nonce('felan_avatar_allow_upload');
            $avatar_type = felan_get_option('felan_image_type');
            $avatar_file_size = felan_get_option('felan_image_max_file_size', '1000kb');
            $avatar_url = FELAN_AJAX_URL . '?action=felan_avatar_upload_ajax&nonce=' . esc_attr($avatar_upload_nonce);
            $avatar_text = esc_html__('Upload', 'felan-framework');

            wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'avatar');
            wp_localize_script(
                FELAN_PLUGIN_PREFIX . 'avatar',
                'felan_avatar_vars',
                array(
                    'ajax_url' => FELAN_AJAX_URL,
                    'avatar_title' => esc_html__('Valid file formats', 'felan-framework'),
                    'avatar_type' => $avatar_type,
                    'avatar_file_size' => $avatar_file_size,
                    'avatar_upload_nonce' => $avatar_upload_nonce,
                    'avatar_url' => $avatar_url,
                    'avatar_text' => $avatar_text,
                )
            );
        }
    }


    /**
     * Get custom image enqueue
     */
    if (!function_exists('felan_get_custom_image_enqueue')) {
        function felan_get_custom_image_enqueue()
        {
            wp_enqueue_script('plupload');
            wp_enqueue_script('jquery-validate');
            $custom_image_upload_nonce = wp_create_nonce('felan_custom_image_allow_upload');
            $custom_image_type = felan_get_option('felan_image_type');
            $custom_image_file_size = felan_get_option('felan_image_max_file_size', '1000kb');
            $custom_image_text = esc_html__('Click here', 'felan-framework');

            wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'custom_image');
            wp_localize_script(
                FELAN_PLUGIN_PREFIX . 'custom_image',
                'felan_custom_image_vars',
                array(
                    'ajax_url' => FELAN_AJAX_URL,
                    'custom_image_title' => esc_html__('Valid file formats', 'felan-framework'),
                    'custom_image_type' => $custom_image_type,
                    'custom_image_file_size' => $custom_image_file_size,
                    'custom_image_upload_nonce' => $custom_image_upload_nonce,
                    'custom_image_text' => $custom_image_text,
                )
            );
        }
    }

    /**
     * Get custom upload enqueue
     */
    if (!function_exists('felan_get_custom_upload_enqueue')) {
        function felan_get_custom_cv_enqueue()
        {
            $ajax_url = admin_url('admin-ajax.php');
            $cv_file = felan_get_option('felan-cv-type');
            $cv_max_file_size = felan_get_option('felan_image_max_file_size', '1000kb');
            $upload_nonce = wp_create_nonce('felan_thumbnail_allow_upload');
            $url = FELAN_AJAX_URL . '?action=felan_thumbnail_upload_ajax&nonce=' . esc_attr($upload_nonce);
            $text = '<i class="far fa-arrow-from-bottom large"></i> ' . esc_html__('Browse', 'felan-framework');

            wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'custom-upload-cv');
            wp_localize_script(
                FELAN_PLUGIN_PREFIX . 'custom-upload-cv',
                'felan_upload_cv_vars',
                array(
                    'ajax_url' => $ajax_url,
                    'title' => esc_html__('Valid file formats', 'felan-framework'),
                    'cv_file' => $cv_file,
                    'cv_max_file_size' => $cv_max_file_size,
                    'upload_nonce' => $upload_nonce,
                    'url' => $url,
                    'text' => $text,
                )
            );
        }
    }

    /**
     * Get custom upload enqueue
     */
    if (!function_exists('felan_get_custom_upload_enqueue')) {
        function felan_get_custom_upload_enqueue()
        {
            $ajax_url = admin_url('admin-ajax.php');
            $cv_file = felan_get_option('felan-cv-type');
            $cv_max_file_size = felan_get_option('felan_image_max_file_size', '1000kb');
            $upload_nonce = wp_create_nonce('felan_thumbnail_allow_upload');
            $url = FELAN_AJAX_URL . '?action=felan_thumbnail_upload_ajax&nonce=' . esc_attr($upload_nonce);
            $text = '<i class="far fa-arrow-from-bottom large"></i> ' . esc_html__('Browse', 'felan-framework');

            wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'upload-cv');
            wp_localize_script(
                FELAN_PLUGIN_PREFIX . 'upload-cv',
                'felan_upload_cv_vars',
                array(
                    'ajax_url' => $ajax_url,
                    'title' => esc_html__('Valid file formats', 'felan-framework'),
                    'cv_file' => $cv_file,
                    'cv_max_file_size' => $cv_max_file_size,
                    'upload_nonce' => $upload_nonce,
                    'url' => $url,
                    'text' => $text,
                )
            );
        }
    }

    /**
     * Get gallery enqueue
     */
    if (!function_exists('felan_get_gallery_enqueue')) {
        function felan_get_gallery_enqueue()
        {
            wp_enqueue_script('plupload');
            wp_enqueue_script('jquery-ui-sortable');
            $gallery_upload_nonce = wp_create_nonce('felan_gallery_allow_upload');
            $gallery_type = felan_get_option('felan_image_type');
            $gallery_file_size = felan_get_option('felan_image_max_file_size', '1000kb');
            $gallery_max_images = felan_get_option('felan_max_gallery_images', 5);
            $gallery_url = FELAN_AJAX_URL . '?action=felan_gallery_upload_ajax&nonce=' . esc_attr($gallery_upload_nonce);

            wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'gallery');
            wp_localize_script(
                FELAN_PLUGIN_PREFIX . 'gallery',
                'felan_gallery_vars',
                array(
                    'ajax_url' => FELAN_AJAX_URL,
                    'gallery_title' => esc_html__('Valid file formats', 'felan-framework'),
                    'gallery_type' => $gallery_type,
                    'gallery_file_size' => $gallery_file_size,
                    'gallery_max_images' => $gallery_max_images,
                    'gallery_upload_nonce' => $gallery_upload_nonce,
                    'gallery_url' => $gallery_url,
                )
            );
        }
    }

    /**
     * Format money
     */
    if (!function_exists('felan_get_format_money')) {
        function felan_get_format_money($money, $price_unit = '', $decimals = 0, $small_sign = false, $is_currency_sign = true)
        {
            $formatted_price = $money;
            $money = doubleval($money);
            if ($money) {
                $dec_point = felan_get_option('decimal_separator', '.');
                $thousands_sep = felan_get_option('thousand_separator', ',');

                $price_unit = intval($price_unit);
                $formatted_price = number_format($money, $decimals = 0, $dec_point = '.', $thousands_sep = ',');

                $currency_type = $currency_sign = '';
                if ($is_currency_sign == true) {
                    $currency_sign = felan_get_option('currency_sign_default');
                    $currency = !empty($currency_sign) ? $currency_sign : '';
                } else {
                    $currency_type = felan_get_option('currency_type_default');
                    $currency = !empty($currency_type) ? $currency_type : '';
                }

                if ($small_sign == true) {
                    $currency = '<sup>' . $currency . '</sup>';
                }
                $currency_position = felan_get_option('currency_position', 'before');
                if ($currency_position == 'before') {
                    return $currency . $formatted_price;
                } else {
                    return $formatted_price . $currency;
                }
            } else {
                $currency = 0;
            }

            return $currency;
        }
    }

    /**
     * Get total reviews
     */
    if (!function_exists('felan_get_total_reviews')) {
        function felan_get_total_reviews()
        {
            global $wpdb, $current_user;
            $user_id = $current_user->ID;
            $my_reviews = $wpdb->get_results("SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.user_id = $user_id AND meta.meta_key = 'company_rating' AND meta.comment_id = comment.comment_ID ORDER BY comment.comment_ID DESC LIMIT 999");
            $company_ids = array();
            foreach ($my_reviews as $my_review) {
                $company_ids[] = $my_review->comment_post_ID;
            }

            $args = array(
                'post_type' => 'company',
                'post__in' => $company_ids,
                'ignore_sticky_posts' => 1,
                'posts_per_page' => -1,
            );

            $data = new WP_Query($args);
            if (!empty($company_ids)) {
                $total_post = $data->found_posts;
            } else {
                $total_post = 0;
            }

            return $total_post;
        }
    }

    /**
     * Get total reviews
     */
    if (!function_exists('felan_get_total_reviews')) {
        function felan_get_total_reviews()
        {
            global $wpdb, $current_user;
            $user_id = $current_user->ID;
            $my_reviews = $wpdb->get_results("SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.user_id = $user_id AND meta.meta_key = 'company_rating' AND meta.comment_id = comment.comment_ID ORDER BY comment.comment_ID DESC LIMIT 999");
            $company_ids = array();
            foreach ($my_reviews as $my_review) {
                $company_ids[] = $my_review->comment_post_ID;
            }

            $args = array(
                'post_type' => 'company',
                'post__in' => $company_ids,
                'ignore_sticky_posts' => 1,
                'posts_per_page' => -1,
            );

            $data = new WP_Query($args);
            if (!empty($company_ids)) {
                $total_post = $data->found_posts;
            } else {
                $total_post = 0;
            }

            return $total_post;
        }
    }

    /**
     * Get total rating
     */
    if (!function_exists('felan_get_total_rating')) {
        function felan_get_total_rating($post_type, $id, $text_count = false)
        {
            global $wpdb;
            $current_user = wp_get_current_user();
            $user_id = $current_user->ID;
            $rating = $total_reviews = $total_stars = 0;

            if (empty($id) || !is_numeric($id)) {
                return;
            }

            if ($post_type == 'company') {
                $comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $id AND meta.meta_key = 'company_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 OR comment.user_id = $user_id )";
            } elseif ($post_type == 'freelancer') {
                $comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $id AND meta.meta_key = 'freelancer_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 OR comment.user_id = $user_id )";
            } elseif ($post_type == 'service') {
                $comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $id AND meta.meta_key = 'service_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 OR comment.user_id = $user_id )";
            } elseif ($post_type == 'project') {
                $comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $id AND meta.meta_key = 'project_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 OR comment.user_id = $user_id )";
            }
            $get_comments = $wpdb->get_results($comments_query);
            if (!is_null($get_comments)) {
                foreach ($get_comments as $comment) {
                    if ($comment->comment_approved == 1) {
                        if (!empty($comment->meta_value) && $comment->meta_value != 0.00) {
                            $total_reviews++;
                        }
                        if ($comment->meta_value > 0) {
                            $total_stars += $comment->meta_value;
                        }
                    }
                }
                if ($total_reviews != 0) {
                    $rating = number_format($total_stars / $total_reviews, 1);
                }
            }
            update_post_meta($id, 'felan-' . $post_type . '_rating', $rating);
        ?>

            <div class="felan-rating-warpper">
                <span class="rating-count">
                    <i class="fas fa-star"></i>
                    <span><?php esc_html_e($rating); ?>
                    </span>
                </span>
                <?php if ($text_count == true) { ?>
                    <a class="review-count" href="#service-review-details">
                        (<span><?php echo esc_html($total_reviews); ?></span>
                        <span><?php echo esc_html__('user review', 'felan-framework') ?></span>)
                    </a>
                <?php } else { ?>
                    <span class="review-count"><?php printf(_n('(%s)', '(%s)', $total_reviews, 'felan-framework'), $total_reviews); ?></span>
                <?php } ?>
            </div>

            <?php }
    }

    /**
     * Get service order status
     */
    if (!function_exists('felan_freelancer_package_status')) {
        function felan_service_order_status($status)
        {
            if ($status == 'inprogress') : ?>
                <span class="label label-inprogress tooltip" data-title="<?php echo esc_attr__('Service in Process', 'felan-framework') ?>"><?php esc_html_e('In Process', 'felan-framework') ?></span>
            <?php elseif ($status == 'transferring') : ?>
                <span class="label label-transferring tooltip" data-title="<?php echo esc_attr__('The freelancer has handed over the service', 'felan-framework') ?>"><?php esc_html_e('Transferring', 'felan-framework') ?></span>
            <?php elseif ($status == 'canceled') : ?>
                <span class="label label-close tooltip" data-title="<?php echo esc_attr__('Freelancer has canceled', 'felan-framework') ?>"><?php esc_html_e('Canceled', 'felan-framework') ?></span>
            <?php elseif ($status == 'completed') : ?>
                <span class="label label-open tooltip" data-title="<?php echo esc_attr__('Service is completed', 'felan-framework') ?>"><?php esc_html_e('Completed', 'felan-framework') ?></span>
            <?php elseif ($status == 'expired') : ?>
                <span class="label label-close tooltip" data-title="<?php echo esc_attr__('Service has expired', 'felan-framework') ?>"><?php esc_html_e('Expired', 'felan-framework') ?></span>
            <?php elseif ($status == 'refund') : ?>
                <span class="label label-close tooltip" data-title="<?php echo esc_attr__('Employer has requested a refund', 'felan-framework') ?>"><?php esc_html_e('Refund', 'felan-framework') ?></span>
            <?php else : ?>
                <span class="label label-pending tooltip" data-title="<?php echo esc_attr__('Wait for admin to approve', 'felan-framework') ?>"><?php esc_html_e('Pending', 'felan-framework') ?></span>
            <?php endif;

            return $status;
        }
    }

    /**
     * Update withdraw total price
     */
    if (!function_exists('felan_update_withdraw_total_price')) {
        function felan_update_withdraw_total_price($price, $user_roles)
        {
            global $current_user;
            $user_id = $current_user->ID;

            if ($user_roles == 'Employer') {
                $withdraw_price = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'employer_withdraw_total_price', true);
            } else {
                $withdraw_price = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_total_price', true);
            }

            if (empty($withdraw_price)) {
                $withdraw_price = 0;
            }

            $withdraw_price_new = intval($withdraw_price) + intval($price);
            if ($user_roles == 'Employer') {
                update_user_meta($user_id, FELAN_METABOX_PREFIX . 'employer_withdraw_total_price', $withdraw_price_new);
            } else {
                update_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_total_price', $withdraw_price_new);
            }
        }
    }

    /**
     * Withdraw total price ajax
     */
    if (!function_exists('felan_withdraw_total_price_ajax')) {
        function felan_withdraw_total_price_ajax($action, $author_id, $user_role)
        {
            $enable_wallet_mode = felan_get_option('enable_wallet_mode', '0');
            $enable_wallet_action = felan_get_option('enable_wallet_' . $action, '1');
            $wallet_fee_amount = felan_get_option('wallet_' . $action  . '_fee_amount', '');
            $currency_sign_default = felan_get_option('currency_sign_default');
            $currency_position = felan_get_option('currency_position');

            if ($enable_wallet_mode == '0' || $enable_wallet_action !== '1' || $wallet_fee_amount == '') {
                return;
            }

            if ($user_role == 'employer') {
                $withdraw_price = get_user_meta($author_id, FELAN_METABOX_PREFIX . 'employer_withdraw_total_price', true);
            } else {
                $withdraw_price = get_user_meta($author_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_total_price', true);
            }

            if (empty($withdraw_price)) {
                $withdraw_price = 0;
            }

            if ($withdraw_price > $wallet_fee_amount) {
                $withdraw_price = intval($withdraw_price) - intval($wallet_fee_amount);
                if ($user_role == 'employer') {
                    update_user_meta($author_id, FELAN_METABOX_PREFIX . 'employer_withdraw_total_price', $withdraw_price);
                } else {
                    update_user_meta($author_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_total_price', $withdraw_price);
                }

                if ($currency_position == 'before') {
                    $withdraw_price = $currency_sign_default . $withdraw_price;
                } else {
                    $withdraw_price = $withdraw_price . $currency_sign_default;
                }

                return $withdraw_price;
            } else {
                return '';
            }
        }
    }

    /**
     * Withdraw Noti Print
     */
    if (!function_exists('felan_withdraw_noti_print')) {
        function felan_withdraw_noti_print($action, $text)
        {
            $enable_wallet_mode = felan_get_option('enable_wallet_mode', '0');
            $enable_wallet_action = felan_get_option('enable_wallet_' . $action, '1');
            $wallet_fee_amount = felan_get_option('wallet_' . $action  . '_fee_amount', '');

            if ($enable_wallet_mode == '0' || $enable_wallet_action !== '1' || $wallet_fee_amount == '') {
                return;
            }

            $currency_sign_default = felan_get_option('currency_sign_default');
            $currency_position = felan_get_option('currency_position');
            if ($currency_position == 'before') {
                $fee_amount = $currency_sign_default . $wallet_fee_amount;
            } else {
                $fee_amount = $wallet_fee_amount . $currency_sign_default;
            }

            ob_start(); ?>
            <p class="notice mb-0">
                <i class="far fa-exclamation-circle"></i>
                <span><?php echo sprintf(esc_html__($text, 'felan-framework'), $fee_amount); ?></span>
            </p>
            <?php $html_noti = ob_get_clean();

            echo $html_noti;
        }
    }

    /**
     * Get project order status
     */
    if (!function_exists('felan_project_package_status')) {
        function felan_project_package_status($status)
        {
            if ($status == 'inprogress') : ?>
                <span class="label label-inprogress" data-title="<?php echo esc_attr__('Project in Process', 'felan-framework') ?>"><?php esc_html_e('In Process', 'felan-framework') ?></span>
            <?php elseif ($status == 'canceled') : ?>
                <span class="label label-close" data-title="<?php echo esc_attr__('Freelancer has canceled', 'felan-framework') ?>"><?php esc_html_e('Canceled', 'felan-framework') ?></span>
            <?php elseif ($status == 'reject') : ?>
                <span class="label label-close" data-title="<?php echo esc_attr__('Freelancer has rejected', 'felan-framework') ?>"><?php esc_html_e('Rejected', 'felan-framework') ?></span>
            <?php elseif ($status == 'completed') : ?>
                <span class="label label-open" data-title="<?php echo esc_attr__('Project is completed', 'felan-framework') ?>"><?php esc_html_e('Completed', 'felan-framework') ?></span>
            <?php else : ?>
                <span class="label label-pending" data-title="<?php echo esc_attr__('Freelancer sent proposal', 'felan-framework') ?>"><?php esc_html_e('Pending', 'felan-framework') ?></span>
            <?php endif;

            return $status;
        }
    }

    /**
     * Get service delivery time
     */
    if (!function_exists('felan_service_delivery_time')) {
        function felan_service_delivery_time($service_id, $number_delivery_time)
        {
            $delivery_rate = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_time', true);
            $rate = '';
            if ($number_delivery_time === '1') {
                if ($delivery_rate == 'hr') {
                    $rate = esc_html__('hour', 'felan-framework');
                } elseif ($delivery_rate == 'day') {
                    $rate = esc_html__('day', 'felan-framework');
                } elseif ($delivery_rate == 'week') {
                    $rate = esc_html__('week', 'felan-framework');
                } elseif ($delivery_rate == 'month') {
                    $rate = esc_html__('month', 'felan-framework');
                }
            } else {
                if ($delivery_rate == 'hr') {
                    $rate = esc_html__('hours', 'felan-framework');
                } elseif ($delivery_rate == 'day') {
                    $rate = esc_html__('days', 'felan-framework');
                } elseif ($delivery_rate == 'week') {
                    $rate = esc_html__('weeks', 'felan-framework');
                } elseif ($delivery_rate == 'month') {
                    $rate = esc_html__('months', 'felan-framework');
                }
            }

            return $number_delivery_time . ' ' . $rate;
        }
    }


    /**
     * Get service revisions
     */
    if (!function_exists('felan_service_revisions')) {
        function felan_service_revisions($service_id, $package)
        {
            if ($package === 'basic') {
                $service_revisions = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_basic_revisions', true);
                $service_number_revisions = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_basic_number_revisions', true);
            } elseif ($package === 'standard') {
                $service_revisions = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_standard_revisions', true);
                $service_number_revisions = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_standard_number_revisions', true);
            } elseif ($package === 'premium') {
                $service_revisions = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_premium_revisions', true);
                $service_number_revisions = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_premium_number_revisions', true);
            }

            if ($service_revisions === 'unlimited') {
                $revisions = esc_html('unlimited', 'felan-framework');
            } elseif ($service_revisions === 'custom') {
                $revisions = $service_number_revisions;
            } else {
                $revisions = '_';
            }

            return $revisions;
        }
    }

    /**
     * Get author name by id
     */
    if (!function_exists('felan_get_author_name_by_id')) {
        function felan_get_author_name_by_id($author_id)
        {
            $user = get_userdata($author_id);
            if ($user) {
                return $user->display_name;
            }

            return null;
        }
    }



    /**
     * Get wallet total price
     */
    if (!function_exists('felan_freelancer_package_status')) {
        function felan_wallet_total_price($status = 'pending')
        {
            global $current_user;
            $user_id = $current_user->ID;
            $args_withdraw = array(
                'post_type' => 'freelancer_withdraw',
                'ignore_sticky_posts' => 1,
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'freelancer_withdraw_user_id',
                        'value' => $user_id,
                        'compare' => '==',
                    ),
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'freelancer_withdraw_status',
                        'value' => $status,
                        'compare' => '==',
                    )
                ),
            );
            $data_withdraw = new WP_Query($args_withdraw);
            $total_price = 0;
            if ($data_withdraw->have_posts()) {
                while ($data_withdraw->have_posts()) : $data_withdraw->the_post();
                    $withdraw_id = get_the_ID();
                    $price = get_post_meta($withdraw_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_price', true);
                    if (empty($price)) {
                        $price = 0;
                    }
                    $total_price += $price;
                endwhile;
            }

            $currency_sign_default = felan_get_option('currency_sign_default');
            $currency_position = felan_get_option('currency_position');
            if ($currency_position == 'before') {
                $total_price = $currency_sign_default . felan_get_format_number($total_price);
            } else {
                $total_price = felan_get_format_number($total_price) . $currency_sign_default;
            }

            return $total_price;
        }
    }

    /**
     * Get employer wallet total price
     */
    if (!function_exists('felan_employer_wallet_total_price')) {
        function felan_employer_wallet_total_price($status = 'pending')
        {
            global $current_user;
            $user_id = $current_user->ID;
            $args_withdraw = array(
                'post_type' => 'employer_withdraw',
                'ignore_sticky_posts' => 1,
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'employer_withdraw_user_id',
                        'value' => $user_id,
                        'compare' => '==',
                    ),
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'employer_withdraw_status',
                        'value' => $status,
                        'compare' => '==',
                    )
                ),
            );
            $data_withdraw = new WP_Query($args_withdraw);
            $total_price = 0;
            if ($data_withdraw->have_posts()) {
                while ($data_withdraw->have_posts()) : $data_withdraw->the_post();
                    $withdraw_id = get_the_ID();
                    $price = get_post_meta($withdraw_id, FELAN_METABOX_PREFIX . 'employer_withdraw_price', true);
                    if (empty($price)) {
                        $price = 0;
                    }
                    $total_price += $price;
                endwhile;
            }

            $currency_sign_default = felan_get_option('currency_sign_default');
            $currency_position = felan_get_option('currency_position');
            if ($currency_position == 'before') {
                $total_price = $currency_sign_default . felan_get_format_number($total_price);
            } else {
                $total_price = felan_get_format_number($total_price) . $currency_sign_default;
            }

            return $total_price;
        }
    }

    /**
     * Get freelancer package status
     */
    if (!function_exists('felan_employer_package_status')) {
        function felan_employer_package_status()
        {
            global $current_user;
            $user_id = $current_user->ID;
            $args_invoice = array(
                'post_type' => 'invoice',
                'posts_per_page' => 1,
                'author' => $user_id,
            );
            $data_invoice = new WP_Query($args_invoice);
            $status = '-1';
            if (!empty($data_invoice->post)) {
                $invoice_id = $data_invoice->post->ID;
                $status = get_post_meta($invoice_id, FELAN_METABOX_PREFIX . 'invoice_payment_status', true);
            }

            return $status;
        }
    }


    /**
     * Get freelancer package status
     */
    if (!function_exists('felan_freelancer_package_status')) {
        function felan_freelancer_package_status()
        {
            global $current_user;
            $user_id = $current_user->ID;
            $args_order = array(
                'post_type' => 'freelancer_order',
                'posts_per_page' => 1,
                'author' => $user_id,
            );
            $data_order = new WP_Query($args_order);
            $status = '-1';
            if (!empty($data_order->post)) {
                $order_id = $data_order->post->ID;
                $status = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'freelancer_order_payment_status', true);
            }

            return $status;
        }
    }

    /**
     * Check freelancer package
     */
    if (!function_exists('felan_check_freelancer_package')) {
        function felan_check_freelancer_package()
        {
            global $current_user;
            $user_id = $current_user->ID;
            $freelancer_paid_submission_type = felan_get_option('freelancer_paid_submission_type');
            $package_status = intval(felan_freelancer_package_status());
            $has_freelancer_package = true;
            if ($freelancer_paid_submission_type == 'freelancer_per_package') {
                $felan_freelancer_package = new Felan_freelancer_package();
                $check_freelancer_package = $felan_freelancer_package->user_freelancer_package_available($user_id);
                if (($check_freelancer_package == -1) || ($check_freelancer_package == 0) || ($package_status !== 1)) {
                    $has_freelancer_package = false;
                }
            }

            return $has_freelancer_package;
        }
    }

    /**
     * Get field number package
     */
    if (!function_exists('felan_number_freelancer_package_ajax')) {
        function felan_number_freelancer_package_ajax($field)
        {
            if (empty($field)) {
                return;
            }
            global $current_user;
            $user_id = $current_user->ID;
            $freelancer_paid_submission_type = felan_get_option('freelancer_paid_submission_type');
            $freelancer_package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'freelancer_package_id', $user_id);
            $check_package = felan_check_freelancer_package();
            $show_package_field = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'show_package_' . $field, true);
            if ($show_package_field == 1 && $check_package) {
                if ($freelancer_paid_submission_type == 'freelancer_per_package') {
                    $freelancer_package_number_field = intval(get_the_author_meta(FELAN_METABOX_PREFIX . 'freelancer_package_number_' . $field, $user_id));
                    if ($freelancer_package_number_field - 1 >= -1) {
                        update_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_' . $field, $freelancer_package_number_field - 1);
                    }
                }
            }
        }
    }

    /**
     * Get field check package
     */
    if (!function_exists('felan_get_field_check_freelancer_package')) {
        function felan_get_field_check_freelancer_package($field)
        {
            global $current_user;
            $user_id = $current_user->ID;
            $freelancer_paid_submission_type = felan_get_option('freelancer_paid_submission_type');
            $freelancer_package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'freelancer_package_id', $user_id);
            $check_package = felan_check_freelancer_package();
            $show_package_field = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'show_package_' . $field, true);
            $enable_option_field = felan_get_option('enable_freelancer_package_' . $field);
            $check = 0;
            if ($freelancer_paid_submission_type == 'freelancer_per_package') {
                if ($show_package_field == '1') {
                    if ($check_package) {
                        $check = 1;
                    } else {
                        $check = -1;
                    }
                } else {
                    $check = 0;
                }
            } else {
                $check = 1;
            }
            if ($enable_option_field !== '1' || in_array("administrator", (array)$current_user->roles)) {
                $check = 2;
            }

            return $check;
        }
    }

    /**
     * Get employer field check package
     */
    if (!function_exists('felan_get_field_check_employer_package')) {
        function felan_get_field_check_employer_package($field)
        {
            global $current_user;
            $user_id = $current_user->ID;
            $paid_submission_type = felan_get_option('paid_submission_type');
            $package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'package_id', $user_id);
            $felan_profile = new Felan_Profile();
            $check_package = $felan_profile->user_package_available($user_id);
            $show_package_field = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'show_package_company_' . $field, true);
            $enable_option_field = felan_get_option('enable_company_package_' . $field);
            $args_invoice = array(
                'post_type' => 'invoice',
                'posts_per_page' => 1,
                'author' => $user_id,
            );
            $data_invoice = new WP_Query($args_invoice);
            $invoice_status = '1';
            if (!empty($data_invoice->post)) {
                $invoice_id = $data_invoice->post->ID;
                $invoice_status = get_post_meta($invoice_id, FELAN_METABOX_PREFIX . 'invoice_payment_status', true);
            }

            $check = 0;
            if ($paid_submission_type == 'per_package') {
                if ($show_package_field === '1') {
                    if ($check_package) {
                        $check = 1;
                    } else {
                        $check = -1;
                    }
                } else {
                    $check = 0;
                }
            } else {
                $check = 1;
            }
            if ($invoice_status === '0') {
                $check = -1;
            }
            if ($enable_option_field !== '1' || in_array("administrator", (array)$current_user->roles)) {
                $check = 2;
            }

            return $check;
        }
    }

    /**
     * Get comment time
     */
    if (!function_exists('felan_get_comment_time')) {
        function felan_get_comment_time($comment_id = 0)
        {
            return sprintf(
                _x('%s ago', 'Human-readable time', 'felan-framework'),
                human_time_diff(
                    get_comment_date('U', $comment_id),
                    current_time('timestamp')
                )
            );
        }
    }

    /**
     * Get other templates (e.g. product attributes) passing attributes and including the file.
     *
     * @access public
     *
     * @param string $template_name
     * @param array $args (default: array())
     * @param string $template_path (default: '')
     * @param string $default_path (default: '')
     */
    if (!function_exists('felan_get_template')) {
        function felan_get_template($template_name, $args = array(), $template_path = '', $default_path = '')
        {
            if (!empty($args) && is_array($args)) {
                extract($args);
            }

            $located = felan_locate_template($template_name, $template_path, $default_path);

            if (!file_exists($located)) {
                _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $located), '2.1');

                return;
            }

            // Allow 3rd party plugin filter template file from their plugin.
            $located = apply_filters('felan_get_template', $located, $template_name, $args, $template_path, $default_path);

            do_action('felan_before_template_part', $template_name, $template_path, $located, $args);

            include($located);

            do_action('felan_after_template_part', $template_name, $template_path, $located, $args);
        }
    }

    /**
     * Locate a template and return the path for inclusion.
     */
    if (!function_exists('felan_locate_template')) {
        function felan_locate_template($template_name, $template_path = '', $default_path = '')
        {
            if (!$template_path) {
                $template_path = FELAN()->template_path();
            }

            if (!$default_path) {
                $default_path = FELAN_PLUGIN_DIR . 'templates/';
            }

            // Look within passed path within the theme - this is priority.
            $template = locate_template(
                array(
                    trailingslashit($template_path) . $template_name,
                    $template_name
                )
            );

            // Get default template/
            if (!$template) {
                $template = $default_path . $template_name;
            }

            // Return what we found.
            return apply_filters('felan_locate_template', $template, $template_name, $template_path);
        }
    }

    /**
     * felan_get_jobs_by_category
     */
    if (!function_exists('felan_get_jobs_by_category')) {
        function felan_get_jobs_by_category($total = 3, $show = 3, $category = 0, $exclude = '')
        {
            $args = array(
                'posts_per_page' => $total,
                'post_type' => 'jobs',
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'exclude' => $exclude,
                'orderby' => array(
                    'menu_order' => 'ASC',
                    'date' => 'DESC',
                ),
                'tax_query' => array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'jobs-categories',
                        'field' => 'id',
                        'terms' => $category
                    )
                ),
                'meta_query' => array(
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'enable_jobs_package_expires',
                        'value' => 0,
                        'compare' => '=='
                    )
                ),
            );
            $job = get_posts($args);
            ob_start();
            ?>
            <?php foreach ($job as $jobs) { ?>
                <?php felan_get_template('content-jobs.php', array(
                    'jobs_id' => $jobs->ID,
                    'jobs_layout' => 'layout-list',
                )); ?>
            <?php } ?>
            <?php
            return ob_get_clean();
        }
    }

    /**
     * get_taxonomy
     */
    if (!function_exists('felan_get_taxonomy')) {
        function felan_get_taxonomy($taxonomy_name, $value_as_slug = false, $show_default_none = true, $render_array = false, $order = false, $meta_key = '')
        {
            global $current_user;
            $user_id = $current_user->ID;

            $args = array(
                'orderby' => 'name',
                'parent' => 0,
                'hide_empty' => false,
            );

            if ($order == true) {
                $args['meta_key'] = $meta_key;
                $args['orderby'] = 'meta_value_num';
                $args['meta_type'] = 'DATE';
            }

            $terms = get_terms($taxonomy_name, $args);

            $result = array();

            foreach ($terms as $term) {

                if (!is_object($term) || !property_exists($term, 'term_id')) {
                    continue;
                }

                $term_children = get_terms($taxonomy_name, array(
                    'parent' => $term->term_id,
                    'hide_empty' => false
                ));

                if (is_wp_error($term_children) || !is_array($term_children)) {
                    continue;
                }

                $result[$term->term_id] = array();
                foreach ($term_children as $child) {
                    $child_level_2 = get_terms($taxonomy_name, array(
                        'parent' => $child->term_id,
                        'hide_empty' => false
                    ));

                    if (is_wp_error($child_level_2) || !is_array($child_level_2)) {
                        continue;
                    }

                    $result[$term->term_id][$child->term_id] = array();
                    foreach ($child_level_2 as $grandchild) {
                        $result[$term->term_id][$child->term_id][$grandchild->term_id] = array();
                    }
                }
            }

            if ($render_array) {
                $list = array(
                    '' => esc_html('Select an option', 'felan-framework')
                );
                foreach ($result as $key => $val) {
                    $term_detail = get_term_by('id', $key, $taxonomy_name);
                    $list[$key] = $term_detail->name;
                    if (is_array($val)) {
                        foreach ($val as $key => $val1) {
                            $term_detail1 = get_term_by('id', $key, $taxonomy_name);
                            $list[$key] = $term_detail1->name;
                            if (is_array($val1)) {
                                foreach ($val1 as $key => $val2) {

                                    $term_detail2 = get_term_by('id', $key, $taxonomy_name);
                                    $list[$key] = $term_detail2->name;
                                }
                            }
                        }
                    }
                }

                return $list;
            } else {
                if ($show_default_none) {
                    echo '<option value="">' . esc_html__('Select an option', 'felan-framework') . '</option>';
                }
                if (!empty($result)) {
                    if ($value_as_slug) {
                        foreach ($result as $key => $val) {
                            $term_detail = get_term_by('id', $key, $taxonomy_name);
                            echo '<option value="' . $term_detail->slug . '" data-level="1">' . $term_detail->name . '</option>';
                            if (is_array($val)) {
                                foreach ($val as $key => $val1) {
                                    $term_detail1 = get_term_by('id', $key, $taxonomy_name);
                                    echo '<option value="' . $term_detail1->slug . '" data-level="2">' . $term_detail1->name . '</option>';
                                    if (is_array($val1)) {
                                        foreach ($val1 as $key => $val2) {
                                            $term_detail2 = get_term_by('id', $key, $taxonomy_name);
                                            echo '<option value="' . $term_detail2->slug . '" data-level="3">' . $term_detail2->name . '</option>';
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        foreach ($result as $key => $value) {
                            $term_detail = get_term_by('id', $key, $taxonomy_name);
                            $jobs_user = get_user_meta($user_id, FELAN_METABOX_PREFIX . $taxonomy_name . '_user');
                            $jobs_user = !empty($jobs_user) ? $jobs_user[0] : '';

                            if (!empty($jobs_user)) { ?>
                                <?php if ($show_default_none) { ?>
                                    <option <?php if (!empty($jobs_user) && $jobs_user == $key) {
                                                echo 'selected';
                                            } ?> value="<?php echo $key ?>" data-level="1"><?php echo trim($term_detail->name) ?></option>';
                                    <?php
                                    if (is_array($value)) {
                                        foreach ($value as $key => $val) {
                                            $term_detail1 = get_term_by('id', $key, $taxonomy_name);
                                    ?>
                                            <option <?php if (!empty($jobs_user) && $jobs_user == $key) {
                                                        echo 'selected';
                                                    } ?> value="<?php echo $key ?>" data-level="2"><?php echo trim($term_detail1->name) ?></option>';
                                            <?php
                                            if (is_array($val)) {
                                                foreach ($val as $key => $v) {
                                                    $term_detail2 = get_term_by('id', $key, $taxonomy_name);
                                            ?>
                                                    <option <?php if (!empty($jobs_user) && $jobs_user == $key) {
                                                                echo 'selected';
                                                            } ?> value="<?php echo $key ?>" data-level="3"><?php echo trim($term_detail2->name); ?></option>';
                                    <?php
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                <?php } else { ?>
                                    <?php foreach ($jobs_user as $key => $value) { ?>
                                        <option <?php if ($value == $key) {
                                                    echo 'selected';
                                                } ?> value="<?php echo $key; ?>"><?php echo $term_detail->name ?>
                                        </option>
                                    <?php } ?>
                                <?php } ?>
                            <?php } else { ?>
                                <option <?php if (isset($_GET[$taxonomy_name]) && $_GET[$taxonomy_name] == $term_detail->slug) {
                                            echo 'selected';
                                        } ?> value="<?php echo esc_attr($term_detail->term_id); ?>" data-level="1"><?php echo esc_html(trim($term_detail->name)); ?></option>
                                <?php
                                if (is_array($value)) {
                                    foreach ($value as $key => $val) {
                                        $term_detail1 = get_term_by('id', $key, $taxonomy_name);
                                ?>
                                        <option <?php if (isset($_GET[$taxonomy_name]) && $_GET[$taxonomy_name] == $term_detail1->slug) {
                                                    echo 'selected';
                                                } ?> value="<?php echo esc_attr($term_detail1->term_id); ?>" data-level="2">
                                            <?php echo esc_html(trim($term_detail1->name)); ?>
                                        </option>
                                        <?php
                                        if (is_array($val)) {
                                            foreach ($val as $key => $v) {
                                                $term_detail2 = get_term_by('id', $key, $taxonomy_name);
                                        ?>
                                                <option <?php if (isset($_GET[$taxonomy_name]) && $_GET[$taxonomy_name] == $term_detail2->slug) {
                                                            echo 'selected';
                                                        } ?> value="<?php echo esc_attr($term_detail2->term_id); ?>" data-level="3">
                                                    <?php echo esc_html(trim($term_detail2->name)); ?>
                                                </option>
                                        <?php
                                            }
                                        }
                                        ?>
                                <?php
                                    }
                                }
                                ?>
            <?php
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Get find nearby cities
     */
    if (!function_exists('felan_find_nearby_cities')) {
        function felan_find_nearby_cities($city_name, $radius_km)
        {
            $map_type = felan_get_option('map_type', 'mapbox');
            if ($map_type == 'mapbox') {
                $mapbox_api_key = felan_get_option('mapbox_api_key');
                $url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/' . urlencode($city_name) . '.json?access_token=' . $mapbox_api_key;
            } else if ($map_type == 'openstreetmap') {
                $url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($city_name);
            } else {
                $google_maps_api_key = felan_get_option('googlemap_api_key');
                $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($city_name) . '&key=' . $google_maps_api_key;
            }

            $response = file_get_contents($url);
            $data = json_decode($response, true);

            $longitude = $latitude = '';
            if (isset($data['features'][0]['center'])) {
                $longitude = $data['features'][0]['center'][0];
                $latitude = $data['features'][0]['center'][1];
            }

            $overpass_endpoint = "http://overpass-api.de/api/interpreter";

            $felan_distance_unit = felan_get_option('felan_distance_unit', 'km');
            if ($felan_distance_unit == 'km') {
                $radius_meters = $radius_km * 1000;
            } else {
                $radius_meters = $radius_km * 1609.34;
            }

            $query = "[out:json];
              node(around:{$radius_meters},{$latitude},{$longitude})[place=city];
              out body;";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $overpass_endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
            $response = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($response, true);

            $city_name = array();
            if (isset($data['elements']) && !empty($data['elements'])) {
                foreach ($data['elements'] as $element) {
                    if (isset($element['tags']['name'])) {
                        $city_name[] = $element['tags']['name'];
                    }
                }
            }

            return $city_name;
        }
    }

    /**
     * Get state country
     */
    if (!function_exists('felan_get_state_country')) {
        function felan_get_state_country($city_id, $term_state, $city_state, $state_country)
        {
            $enable_option_state = felan_get_option('enable_option_state');
            $enable_option_country = felan_get_option('enable_option_country');

            $state_name = $country_name = $state_by_id = $country_by_id = '';
            $country_val = array();
            if ($enable_option_state === '1') {
                $state_id = get_term_meta($city_id, $city_state, true);
                if (!empty($state_id)) {
                    $state_by_id = get_term_by('id', $state_id, $term_state);
                    if (!empty($state_by_id)) {
                        $state_name = ', ' . $state_by_id->name;
                    }
                }
            }

            if ($enable_option_state === '1' && $enable_option_country === '1') {
                $country_id = get_term_meta($state_id, $state_country, true);
                $countries = felan_get_countries();
                foreach ($countries as $k => $v) {
                    if ($k == $country_id) {
                        $country_val[] = $v;
                    }
                }
                if (!empty($country_val)) {
                    $country_name = ', ' . implode('', $country_val);
                }
            }

            $location = $state_name . $country_name;

            return $location;
        }
    }

    /**
     * Get label location
     */
    if (!function_exists('felan_get_label_location')) {
        function felan_get_label_location($post_id, $taxonomy_name, $term_state, $city_state, $state_country)
        {
            $taxonomy_location = get_the_terms($post_id, $taxonomy_name);
            if (is_array($taxonomy_location)) {
                foreach ($taxonomy_location as $location) {
                    $location_link = get_term_link($location, $taxonomy_name);
                    echo '<a class="label label-location" href="' . esc_url($location_link) . '">';
                    echo ' <i class="far fa-map-marker-alt"></i>';
                    echo esc_html($location->name);
                    echo felan_get_state_country($location->term_id, $term_state, $city_state, $state_country);
                    echo '</a>';
                }
            }
        }
    }

    /**
     * Get taxonomy location
     */
    if (!function_exists('felan_get_taxonomy_location')) {
        function felan_get_taxonomy_location($taxonomy_name, $term_state, $city_state, $state_country, $post_id = '', $post_type = '')
        {
            $args = array(
                'orderby' => 'name',
                'parent' => 0,
                'hide_empty' => false,
            );
            $terms = get_terms($taxonomy_name, $args);

            $result = array();
            foreach ($terms as $term) {
                $term_children = get_terms($taxonomy_name, array(
                    'parent' => $term->term_id,
                    'hide_empty' => false
                ));
                $result[$term->term_id] = array();
                foreach ($term_children as $child) {
                    $child_level_2 = get_terms($taxonomy_name, array(
                        'parent' => $child->term_id,
                        'hide_empty' => false
                    ));
                    $result[$term->term_id][$child->term_id] = array();
                    foreach ($child_level_2 as $grandchild) {
                        $result[$term->term_id][$child->term_id][$grandchild->term_id] = array();
                    }
                }
            }

            $target_by_id = array();
            $tax_terms = get_the_terms($post_id, $taxonomy_name);
            if (!empty($tax_terms)) {
                foreach ($tax_terms as $tax_term) {
                    $target_by_id[] = $tax_term->term_id;
                }
            }

            echo '<option value="">' . esc_html__('Select an option', 'felan-framework') . '</option>';
            foreach ($result as $key => $val) {
                $term_detail = get_term_by('id', $key, $taxonomy_name);
                $name_state_country = felan_get_state_country($term_detail->term_id, $term_state, $city_state, $state_country);
                if (in_array($term_detail->term_id, $target_by_id) && !empty($post_id)) {
                    echo '<option value="' . $term_detail->term_id . '" selected data-level="1">' . $term_detail->name . $name_state_country . '</option>';
                } else {
                    echo '<option value="' . $term_detail->term_id . '" data-level="1">' . $term_detail->name . $name_state_country . '</option>';
                }

                if (is_array($val)) {
                    foreach ($val as $key => $val1) {
                        $term_detail1 = get_term_by('id', $key, $taxonomy_name);
                        $name_state_country1 = felan_get_state_country($term_detail->term_id, $term_state, $city_state, $state_country);
                        if (in_array($term_detail1->term_id, $target_by_id) && !empty($post_id)) {
                            echo '<option value="' . $term_detail->term_id . '" selected data-level="1">' . $term_detail->name . $name_state_country1 . '</option>';
                        } else {
                            echo '<option value="' . $term_detail->term_id . '" data-level="1">' . $term_detail->name . $name_state_country1 . '</option>';
                        }
                        if (is_array($val1)) {
                            foreach ($val1 as $key => $val2) {
                                $term_detail2 = get_term_by('id', $key, $taxonomy_name);
                                $name_state_country2 = felan_get_state_country($term_detail2->term_id, $term_state, $city_state, $state_country);
                                if (in_array($term_detail2->term_id, $target_by_id) && !empty($post_id)) {
                                    echo '<option value="' . $term_detail2->term_id . '" selected data-level="1">' . $term_detail2->name . $name_state_country2 . '</option>';
                                } else {
                                    echo '<option value="' . $term_detail2->term_id . '" data-level="1">' . $term_detail2->name . $name_state_country2 . '</option>';
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Get option city
     */
    if (!function_exists('felan_get_option_city')) {
        function felan_get_option_city()
        {
            $args = array(
                'orderby'    => 'name',
                'parent'     => 0,
                'hide_empty' => false,
            );

            $terms = get_terms('location-city', $args);
            $options = array(
                '' => esc_html__('Select an option', 'felan-framework'),
            );

            if (!is_wp_error($terms) && is_array($terms)) {
                foreach ($terms as $term) {
                    if ($term instanceof WP_Term) {
                        $options[$term->term_id] = $term->name;
                    }
                }
            }

            return $options;
        }
    }



    /**
     * Get taxonomy slug by post id
     */
    if (!function_exists('felan_get_taxonomy_slug_by_post_id')) {
        function felan_get_taxonomy_slug_by_post_id($post_id, $taxonomy_name)
        {
            $tax_terms = get_the_terms($post_id, $taxonomy_name);
            if (!empty($tax_terms)) {
                foreach ($tax_terms as $tax_term) {
                    return $tax_term->slug;
                }
            }

            return null;
        }
    }

    /**
     * felan_get_taxonomy_slug
     */
    if (!function_exists('felan_get_taxonomy_slug')) {
        function felan_get_taxonomy_slug($taxonomy_name, $target_term_slug = '', $prefix = '')
        {
            $taxonomy_terms = get_categories(
                array(
                    'taxonomy' => $taxonomy_name,
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'hide_empty' => false,
                    'parent' => 0
                )
            );

            if (!empty($taxonomy_terms)) {
                foreach ($taxonomy_terms as $term) {
                    if ($target_term_slug == $term->slug) {
                        echo '<option value="' . $term->slug . '" selected>' . $prefix . $term->name . '</option>';
                    } else {
                        echo '<option value="' . $term->slug . '">' . $prefix . $term->name . '</option>';
                    }
                }
            }
        }
    }

    /**
     * get_taxonomy_by_post_id
     */
    if (!function_exists('felan_get_taxonomy_by_post_id')) {
        function felan_get_taxonomy_by_post_id($post_id, $taxonomy_name, $show_default_none = true, $is_target_by_name = false, $order = false, $meta_key = '')
        {
            $args = array(
                'orderby' => 'name',
                'parent' => 0,
                'hide_empty' => false,
            );

            if ($order == true) {
                $args['meta_key'] = $meta_key;
                $args['orderby'] = 'meta_value_num';
                $args['meta_type'] = 'DATE';
            }

            $args = apply_filters('felan/get_taxonomy_by_post_id/args', $args, $post_id, $taxonomy_name, $order);

            $terms = get_terms($taxonomy_name, $args);

            $result = array();

            foreach ($terms as $term) {
                $term_children = get_terms($taxonomy_name, array(
                    'parent' => $term->term_id,
                    'hide_empty' => false
                ));
                $result[$term->term_id] = array();
                foreach ($term_children as $child) {
                    $child_level_2 = get_terms($taxonomy_name, array(
                        'parent' => $child->term_id,
                        'hide_empty' => false
                    ));
                    $result[$term->term_id][$child->term_id] = array();
                    foreach ($child_level_2 as $grandchild) {
                        $result[$term->term_id][$child->term_id][$grandchild->term_id] = array();
                    }
                }
            }
            $target_by_name = array();
            $target_by_id = array();
            $tax_terms = get_the_terms($post_id, $taxonomy_name);
            if ($is_target_by_name) {
                if (!empty($tax_terms)) {
                    foreach ($tax_terms as $tax_term) {
                        $target_by_name[] = $tax_term->name;
                    }
                }
                if ($show_default_none) {
                    if (empty($target_by_name)) {
                        echo '<option value="" selected>' . esc_html__('None', 'felan-framework') . '</option>';
                    } else {
                        echo '<option value="">' . esc_html__('None', 'felan-framework') . '</option>';
                    }
                }
                felan_get_taxonomy_target_by_name($result, $target_by_name, $taxonomy_name);
            } else {
                if (!empty($tax_terms)) {
                    foreach ($tax_terms as $tax_term) {
                        $target_by_id[] = $tax_term->term_id;
                    }
                }
                if ($show_default_none) {
                    if ($target_by_id == 0 || empty($target_by_id)) {
                        echo '<option value="" selected>' . esc_html__('Select an option', 'felan-framework') . '</option>';
                    } else {
                        echo '<option value="">' . esc_html__('Select an option', 'felan-framework') . '</option>';
                    }
                }
                felan_get_taxonomy_target_by_id($result, $target_by_id, $taxonomy_name);
            }
        }
    }

    /**
     * get_taxonomy_target_by_name
     */
    if (!function_exists('felan_get_taxonomy_target_by_name')) {
        function felan_get_taxonomy_target_by_name($taxonomy_terms, $target_term_name, $taxonomy_name, $prefix = "")
        {
            if (!empty($taxonomy_terms)) {
                foreach ($taxonomy_terms as $key => $val) {
                    $term_detail = get_term_by('id', $key, $taxonomy_name);
                    if (in_array($term_detail->name, $target_term_name)) {
                        echo '<option value="' . $term_detail->slug . '" data-level="1" selected>' . $prefix . $term_detail->name . '</option>';
                    } else {
                        echo '<option value="' . $term_detail->slug . '" data-level="1">' . $term_detail->name . '</option>';
                    }
                    if (is_array($val)) {
                        foreach ($val as $key => $val1) {
                            $term_detail1 = get_term_by('id', $key, $taxonomy_name);
                            if (in_array($term_detail1->name, $target_term_name)) {
                                echo '<option value="' . $term_detail1->slug . '" data-level="2" selected>' . $prefix . $term_detail1->name . '</option>';
                            } else {
                                echo '<option value="' . $term_detail1->slug . '" data-level="2">' . $term_detail1->name . '</option>';
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * get_taxonomy_target_by_id
     */
    if (!function_exists('felan_get_taxonomy_target_by_id')) {
        function felan_get_taxonomy_target_by_id($taxonomy_terms, $target_term_id, $taxonomy_name, $prefix = "")
        {
            if (!empty($taxonomy_terms)) {
                foreach ($taxonomy_terms as $key => $val) {
                    $term_detail = get_term_by('id', $key, $taxonomy_name);
                    if (in_array($term_detail->term_id, $target_term_id)) {
                        echo '<option value="' . $term_detail->term_id . '" data-level="1" selected>' . $prefix . $term_detail->name . '</option>';
                    } else {
                        echo '<option value="' . $term_detail->term_id . '" data-level="1">' . $term_detail->name . '</option>';
                    }
                    if (is_array($val)) {
                        foreach ($val as $key => $val1) {
                            $term_detail1 = get_term_by('id', $key, $taxonomy_name);
                            if (in_array($term_detail1->term_id, $target_term_id)) {
                                echo '<option value="' . $term_detail1->term_id . '" data-level="2" selected>' . $prefix . $term_detail1->name . '</option>';
                            } else {
                                echo '<option value="' . $term_detail1->term_id . '" data-level="2">' . $term_detail1->name . '</option>';
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * server protocol
     */
    if (!function_exists('felan_server_protocol')) {
        function felan_server_protocol()
        {
            if (is_ssl()) {
                return 'https://';
            }

            return 'http://';
        }
    }


    /**
     * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
     * Non-scalar values are ignored.
     *
     * @param string|array $var Data to sanitize.
     *
     * @return string|array
     */
    if (!function_exists('felan_clean')) {
        function felan_clean($var)
        {
            if (is_array($var)) {
                return array_map('felan_clean', $var);
            } else {
                return is_scalar($var) ? sanitize_text_field($var) : $var;
            }
        }
    }

    if (!function_exists('felan_clean_double_val')) {
        function felan_clean_double_val($string)
        {
            $string = preg_replace('/&#36;/', '', $string);
            $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
            $string = preg_replace('/\D/', '', $string);

            return $string;
        }
    }

    /**
     * Actived Jobs
     */
    if (!function_exists('felan_total_actived_jobs')) {
        function felan_total_actived_jobs()
        {
            global $current_user;
            $user_id = $current_user->ID;

            $args = array(
                'post_type'      => 'jobs',
                'posts_per_page' => -1,
                'author'         => $user_id,
            );

            $data       = new WP_Query($args);
            $total_post = $data->found_posts;

            return $total_post;
        }
    }


    if (!function_exists('felan_render_custom_field')) {
        function felan_render_custom_field($post_type)
        {
            if ($post_type == 'company') {
                $form_fields = felan_get_option('custom_field_company');
            } elseif ($post_type == 'freelancer') {
                $form_fields = felan_get_option('custom_field_freelancer');
            } elseif ($post_type == 'project') {
                $form_fields = felan_get_option('custom_field_project');
            } elseif ($post_type == 'service') {
                $form_fields = felan_get_option('custom_field_service');
            } else {
                $form_fields = felan_get_option('custom_field_jobs');
            }

            $meta_prefix = FELAN_METABOX_PREFIX;

            $configs = array();
            if ($form_fields && is_array($form_fields)) {
                foreach ($form_fields as $key => $field) {
                    if (!empty($field['label'])) {
                        $type = $field['field_type'];
                        $config = array(
                            'title' => $field['label'],
                            'id' => $meta_prefix . sanitize_title($field['id']),
                            'type' => $type,
                        );
                        $first_opt = '';
                        switch ($type) {
                            case 'checkbox_list':
                            case 'select':
                                $options = array();
                                $options_arr = isset($field['select_choices']) ? $field['select_choices'] : '';
                                $options_arr = str_replace("\r\n", "\n", $options_arr);
                                $options_arr = str_replace("\r", "\n", $options_arr);
                                $options_arr = explode("\n", $options_arr);
                                $first_opt = !empty($options_arr) ? $options_arr[0] : '';
                                foreach ($options_arr as $opt_value) {
                                    $options[$opt_value] = $opt_value;
                                }

                                $config['options'] = $options;
                                break;
                                break;
                        }

                        if ($post_type == 'freelancer') {
                            $config['tabs'] = $field['tabs'];
                            $config['section'] = $field['section'];
                        }

                        if (in_array($type, array('select'))) {
                            $config['default'] = $first_opt;
                        }
                        $configs[] = $config;
                    }
                }
            }

            return $configs;
        }
    }


    //GET SEARCH FILTER ITEM
    if (!function_exists('get_search_filter_submenu')) {
        function get_search_filter_submenu($taxonomy_name, $title, $load_children = true, $order = false, $meta_key = '')
        {

            if (isset($_GET[$taxonomy_name . '_id'])) {
                $tax_selected_id_list = felan_clean(wp_unslash($_GET[$taxonomy_name . '_id']));
            } else {
                $tax_selected_id_list = array();
            }


            $class_list_wrapper = 'filter-control custom-scrollbar ' . $taxonomy_name;

            $submenu_arg = array(
                'taxonomy_name' => $taxonomy_name,
                'taxonomy_parent_id' => 0,
                'tax_selected_id_list' => $tax_selected_id_list,
                'class_list_wrapper' => $class_list_wrapper,
            );

            $class_wrapper = 'filter-' . $taxonomy_name;

            ?>

            <div class="<?php echo $class_wrapper ?>">
                <div class="entry-filter">
                    <h4><?php echo esc_attr($title) ?></h4>
                    <?php echo render_item_checkbox($submenu_arg, $load_children, $order, $meta_key); ?>
                </div>
            </div>

            <?php

        }
    }

    //GET CHECKBOX ITEM FOR SUBMENU
    if (!function_exists('render_item_checkbox')) {
        function render_item_checkbox($submenu_arg = array(), $load_children = true, $order = false, $meta_key = '')
        {
            $taxonomy_name = $submenu_arg['taxonomy_name'];
            $taxonomy_parent_id = $submenu_arg['taxonomy_parent_id'];
            $tax_selected_id_list = $submenu_arg['tax_selected_id_list'];
            $class_list_wrapper = $submenu_arg['class_list_wrapper'];

            $taxonomy_object = array(
                'taxonomy' => $taxonomy_name,
                'hide_empty' => 0,
                'orderby' => 'title',
                'order' => 'ASC',
                'parent' => $taxonomy_parent_id,
            );

            if ($order == true) {
                $taxonomy_object['meta_key'] = $meta_key;
                $taxonomy_object['orderby'] = 'meta_value_num';
                $taxonomy_object['meta_type'] = 'DATE';
            }

            $taxonomy_object_list = get_categories($taxonomy_object);

            if (empty($taxonomy_object_list)) {
                return;
            }

            $list = '<ul class="' . $class_list_wrapper . '">';
            $list_item = '';

            foreach ($taxonomy_object_list as $term_object) {
                $check = '';
                if (in_array($term_object->term_id, $tax_selected_id_list)) {
                    $check = 'checked';
                }

                $list_item = '<li>';
                $list_item .= '<input type="checkbox" class="custom-checkbox input-control" name="' . $taxonomy_name . '_id[]" value="' . $term_object->term_id . '" id="felan_' . $term_object->term_id . '"' . $check . '/>';

                $list_item .= '<label for="felan_' . esc_attr($term_object->term_id) . '">' . esc_html($term_object->name) . '<span class="count">(' . $term_object->count . ')</span></label>';

                if ($load_children) {
                    $submenu_arg['class_list_wrapper'] = '';
                    $submenu_arg['taxonomy_parent_id'] = $term_object->term_id;
                    $list_item .= render_item_checkbox($submenu_arg);
                }

                $list_item .= '</li>';

                $list .= $list_item;
            }

            return $list .= '</ul>';
        }
    }


    if (! function_exists('get_search_filter_custom')) {
        function get_search_filter_custom($post_type, $field_id)
        {
            $custom_field_filter = felan_render_custom_field($post_type);
            foreach ($custom_field_filter as $key => $field) {
                if (in_array($field['type'], ['text', 'select', 'checkbox_list']) && $field_id == $field['id']) {
            ?>
                    <div class="custom-field-filter filter-custom-<?= $post_type ?>">
                        <div class="entry-filter filter-custom-<?= $field['id'] ?>">
                            <h4><?php echo $field['title']; ?></h4>
                            <?php echo get_custom_field_value($field, $post_type) ?>
                        </div>
                    </div>
                <?php
                }
            }
        }
    }

    if (! function_exists('get_custom_field_value')) {
        function get_custom_field_value($field, $post_type)
        {
            global $wpdb;

            $meta_key = $field['id'];

            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "
					SELECT DISTINCT pm.meta_value, pm.meta_key
					FROM {$wpdb->postmeta} pm
					INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
					WHERE pm.meta_key = %s
					AND p.post_type = %s
					",
                    $meta_key,
                    $post_type
                ),
                ARRAY_A
            );

            $list      = '<ul class="filter-control custom-scrollbar">';
            $list_item = '';
            if (! empty($results)) {
                foreach ($results as $result) {
                    $list_item = '<li>';
                    $list_item .= '<input type="checkbox" class="custom-checkbox input-control" name="' . $result['meta_key'] . '_id[]" value="' . $result['meta_value'] . '" id="felan_' . $result['meta_value'] . '"/>';
                    $list_item .= '<label for="felan_' . esc_attr($result['meta_value']) . '">' . esc_html($result['meta_value']) . '</label>';
                    $list_item .= '</li>';

                    $list .= $list_item;
                }
            }

            return $list .= '</ul>';
        }
    }

    //GET CITY FROM ADDRESS
    if (!function_exists('get_city_from_address')) {
        function get_city_from_address($address)
        {
            $api_key = felan_get_option('googlemap_api_key', 'AIzaSyBvPDNG6pePr9iFpeRKaOlaZF_l0oT3lWk');
            $geocodeApiUrl = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&key=' . $api_key;

            // Send a request to the geocoding API
            $response = file_get_contents($geocodeApiUrl);

            // Parse the response as JSON
            $data = json_decode($response, true);

            // Check if the geocoding was successful
            if ($data['status'] === 'OK') {
                // Extract the city from the address components
                foreach ($data['results'][0]['address_components'] as $component) {
                    if (in_array('locality', $component['types'])) {
                        return $component['long_name'];
                    }
                }
            }

            return null; // City not found
        }
    }

    // CHECK IS CITY
    if (!function_exists('is_city_name')) {
        function is_city_name($name)
        {
            $apiUsername = 'ductrung'; // Replace with your GeoNames username

            // Make a request to the GeoNames API
            $url = "http://api.geonames.org/searchJSON?q=" . urlencode($name) . "&maxRows=1&username=" . $apiUsername;
            $response = file_get_contents($url);

            // Parse the response as JSON
            $data = json_decode($response, true);

            // Check if the API response contains a city
            if (isset($data['geonames']) && count($data['geonames']) > 0) {
                $type = $data['geonames'][0]['fclName'];

                return $type == 'city';
            }

            return false; // City not found
        }
    }


    /**
     * Term slug by id
     */
    if (!function_exists('felan_get_term_slug_by_id')) {
        function felan_get_term_slug_by_id($category_ids)
        {
            if (is_array($category_ids)) {
                $slugs = array();
                foreach ($category_ids as $category_id) {
                    $category_id = intval($category_id);
                    $category = get_term($category_id);

                    if ($category && !is_wp_error($category)) {
                        $slugs[] = $category->slug;
                    }
                }
                return implode(',', $slugs);
            } else {
                $category_id = intval($category_ids);
                $category = get_term($category_id);

                if ($category && !is_wp_error($category)) {
                    return $category->slug;
                }

                return '';
            }
        }
    }

    /**
     * Register Model of AI
     */
    if (!function_exists('model_ai_helper')) {
        function model_ai_helper()
        {
            return array(
                'gpt-4' => esc_html__('gpt-4', 'felan-framework'),
                'gpt-3.5-turbo' => esc_html__('gpt-3.5-turbo', 'felan-framework'),
            );
        }
    }

    /**
     * Register Tone of AI
     */
    if (!function_exists('tone_ai_helper')) {
        function tone_ai_helper()
        {
            return array(
                'professional' => esc_html__('Professional', 'felan-framework'),
                'funny' => esc_html__('Funny', 'felan-framework'),
                'casual' => esc_html__('Casual', 'felan-framework'),
                'excited' => esc_html__('Excited', 'felan-framework'),
                'witty' => esc_html__('Witty', 'felan-framework'),
                'sarcastic' => esc_html__('Sarcastic', 'felan-framework'),
                'feminine' => esc_html__('Feminine', 'felan-framework'),
                'masculine' => esc_html__('Masculine', 'felan-framework'),
                'bold' => esc_html__('Bold', 'felan-framework'),
                'dramatic' => esc_html__('Dramatic', 'felan-framework'),
                'grumpy' => esc_html__('Grumpy', 'felan-framework'),
                'secretive' => esc_html__('Secretive', 'felan-framework'),
            );
        }
    }

    /**
     * Register Language of AI
     */
    if (!function_exists('language_ai_helper')) {
        function language_ai_helper()
        {
            return array(
                'en' => esc_html__('English (en)', 'felan-framework'),
                'zh' => esc_html__('中文 (zh)', 'felan-framework'),
                'hi' => esc_html__('हिन्दी (hi)', 'felan-framework'),
                'es' => esc_html__('Español (es)', 'felan-framework'),
                'fr' => esc_html__('Français (fr)', 'felan-framework'),
                'bn' => esc_html__('বাংলা (bn)', 'felan-framework'),
                'ar' => esc_html__('العربية (ar)', 'felan-framework'),
                'ru' => esc_html__('Русский (ru)', 'felan-framework'),
                'pt' => esc_html__('Português (pt)', 'felan-framework'),
                'id' => esc_html__('Bahasa Indonesia (id)', 'felan-framework'),
                'ur' => esc_html__('اردو (ur)', 'felan-framework'),
                'ja' => esc_html__('日本語 (ja)', 'felan-framework'),
                'de' => esc_html__('Deutsch (de)', 'felan-framework'),
                'jv' => esc_html__('Basa Jawa (jv)', 'felan-framework'),
                'pa' => esc_html__('ਪੰਜਾਬੀ (pa)', 'felan-framework'),
                'te' => esc_html__('తెలుగు (te)', 'felan-framework'),
                'mr' => esc_html__('मराठी (mr)', 'felan-framework'),
                'ko' => esc_html__('한국어 (ko)', 'felan-framework'),
                'tr' => esc_html__('Türkçe (tr)', 'felan-framework'),
                'ta' => esc_html__('தமிழ் (ta)', 'felan-framework'),
                'it' => esc_html__('Italiano (it)', 'felan-framework'),
                'vi' => esc_html__('Tiếng Việt (vi)', 'felan-framework'),
                'th' => esc_html__('ไทย (th)', 'felan-framework'),
                'pl' => esc_html__('Polski (pl)', 'felan-framework'),
                'fa' => esc_html__('فارسی (fa)', 'felan-framework'),
                'uk' => esc_html__('Українська (uk)', 'felan-framework'),
                'ms' => esc_html__('Bahasa Melayu (ms)', 'felan-framework'),
                'ro' => esc_html__('Română (ro)', 'felan-framework'),
                'nl' => esc_html__('Nederlands (nl)', 'felan-framework'),
                'hu' => esc_html__('Magyar (hu)', 'felan-framework'),
            );
        }
    }

    /**
     * Register Phone Prefix Code
     */
    if (!function_exists('phone_prefix_code')) {
        function phone_prefix_code()
        {
            return apply_filters(
                'felan_phone_prefix_code',
                array(
                    'ax' => array(
                        'name' => esc_html__('Åland Islands', 'felan-framework'),
                        'code' => '+358',
                    ),
                    'af' => array(
                        'name' => esc_html__('Afghanistan', 'felan-framework'),
                        'code' => '+93',
                    ),
                    'al' => array(
                        'name' => esc_html__('Albania', 'felan-framework'),
                        'code' => '+355',
                    ),
                    'dz' => array(
                        'name' => esc_html__('Algeria', 'felan-framework'),
                        'code' => '+213',
                    ),
                    'as' => array(
                        'name' => esc_html__('American Samoa', 'felan-framework'),
                        'code' => '+1684',
                    ),
                    'ad' => array(
                        'name' => esc_html__('Andorra', 'felan-framework'),
                        'code' => '+376',
                    ),
                    'ao' => array(
                        'name' => esc_html__('Angola', 'felan-framework'),
                        'code' => '+244',
                    ),
                    'ai' => array(
                        'name' => esc_html__('Anguilla', 'felan-framework'),
                        'code' => '+1264',
                    ),
                    'ag' => array(
                        'name' => esc_html__('Antigua and Barbuda', 'felan-framework'),
                        'code' => '+1268',
                    ),
                    'ar' => array(
                        'name' => esc_html__('Argentina', 'felan-framework'),
                        'code' => '+54',
                    ),
                    'am' => array(
                        'name' => esc_html__('Armenia', 'felan-framework'),
                        'code' => '+374',
                    ),
                    'aw' => array(
                        'name' => esc_html__('Aruba', 'felan-framework'),
                        'code' => '+297',
                    ),
                    'au' => array(
                        'name' => esc_html__('Australia', 'felan-framework'),
                        'code' => '+61',
                    ),
                    'at' => array(
                        'name' => esc_html__('Austria', 'felan-framework'),
                        'code' => '+43',
                    ),
                    'az' => array(
                        'name' => esc_html__('Azerbaijan', 'felan-framework'),
                        'code' => '+994',
                    ),
                    'bs' => array(
                        'name' => esc_html__('Bahamas', 'felan-framework'),
                        'code' => '+1242',
                    ),
                    'bh' => array(
                        'name' => esc_html__('Bahrain', 'felan-framework'),
                        'code' => '+973',
                    ),
                    'bd' => array(
                        'name' => esc_html__('Bangladesh', 'felan-framework'),
                        'code' => '+880',
                    ),
                    'bb' => array(
                        'name' => esc_html__('Barbados', 'felan-framework'),
                        'code' => '+1246',
                    ),
                    'by' => array(
                        'name' => esc_html__('Belarus', 'felan-framework'),
                        'code' => '+375',
                    ),
                    'be' => array(
                        'name' => esc_html__('Belgium', 'felan-framework'),
                        'code' => '+32',
                    ),
                    'bz' => array(
                        'name' => esc_html__('Belize', 'felan-framework'),
                        'code' => '+501',
                    ),
                    'bj' => array(
                        'name' => esc_html__('Benin', 'felan-framework'),
                        'code' => '+229',
                    ),
                    'bm' => array(
                        'name' => esc_html__('Bermuda', 'felan-framework'),
                        'code' => '+1441',
                    ),
                    'bt' => array(
                        'name' => esc_html__('Bhutan', 'felan-framework'),
                        'code' => '+975',
                    ),
                    'bo' => array(
                        'name' => esc_html__('Bolivia', 'felan-framework'),
                        'code' => '+591',
                    ),
                    'ba' => array(
                        'name' => esc_html__('Bosnia and Herzegovina', 'felan-framework'),
                        'code' => '+387',
                    ),
                    'bw' => array(
                        'name' => esc_html__('Botswana', 'felan-framework'),
                        'code' => '+267',
                    ),
                    'br' => array(
                        'name' => esc_html__('Brazil', 'felan-framework'),
                        'code' => '+55',
                    ),
                    'io' => array(
                        'name' => esc_html__('British Indian Ocean Territory', 'felan-framework'),
                        'code' => '+246',
                    ),
                    'vg' => array(
                        'name' => esc_html__('British Virgin Islands', 'felan-framework'),
                        'code' => '+1284',
                    ),
                    'bn' => array(
                        'name' => esc_html__('Brunei', 'felan-framework'),
                        'code' => '+673',
                    ),
                    'bg' => array(
                        'name' => esc_html__('Bulgaria', 'felan-framework'),
                        'code' => '+359',
                    ),
                    'bf' => array(
                        'name' => esc_html__('Burkina Faso', 'felan-framework'),
                        'code' => '+226',
                    ),
                    'bi' => array(
                        'name' => esc_html__('Burundi', 'felan-framework'),
                        'code' => '+257',
                    ),
                    'kh' => array(
                        'name' => esc_html__('Cambodia', 'felan-framework'),
                        'code' => '+855',
                    ),
                    'cm' => array(
                        'name' => esc_html__('Cameroon', 'felan-framework'),
                        'code' => '+237',
                    ),
                    'ca' => array(
                        'name' => esc_html__('Canada', 'felan-framework'),
                        'code' => '+1',
                    ),
                    'cv' => array(
                        'name' => esc_html__('Cape Verde', 'felan-framework'),
                        'code' => '+238',
                    ),
                    'bq' => array(
                        'name' => esc_html__('Caribbean Netherlands', 'felan-framework'),
                        'code' => '+599',
                    ),
                    'ky' => array(
                        'name' => esc_html__('Cayman Islands', 'felan-framework'),
                        'code' => '+1345',
                    ),
                    'cf' => array(
                        'name' => esc_html__('Central African Republic', 'felan-framework'),
                        'code' => '+236',
                    ),
                    'td' => array(
                        'name' => esc_html__('Chad', 'felan-framework'),
                        'code' => '+235',
                    ),
                    'cl' => array(
                        'name' => esc_html__('Chile', 'felan-framework'),
                        'code' => '+56',
                    ),
                    'cn' => array(
                        'name' => esc_html__('China', 'felan-framework'),
                        'code' => '+86',
                    ),
                    'cx' => array(
                        'name' => esc_html__('Christmas Island', 'felan-framework'),
                        'code' => '+61',
                    ),
                    'co' => array(
                        'name' => esc_html__('Colombia', 'felan-framework'),
                        'code' => '+57',
                    ),
                    'km' => array(
                        'name' => esc_html__('Comoros', 'felan-framework'),
                        'code' => '+269',
                    ),
                    'cd' => array(
                        'name' => esc_html__('Congo DRC', 'felan-framework'),
                        'code' => '+243',
                    ),
                    'cg' => array(
                        'name' => esc_html__('Congo Republic', 'felan-framework'),
                        'code' => '+242',
                    ),
                    'ck' => array(
                        'name' => esc_html__('Cook Islands', 'felan-framework'),
                        'code' => '+682',
                    ),
                    'cr' => array(
                        'name' => esc_html__('Costa Rica', 'felan-framework'),
                        'code' => '+506',
                    ),
                    'ci' => array(
                        'name' => esc_html__('Côte d’Ivoire', 'felan-framework'),
                        'code' => '+225',
                    ),
                    'hr' => array(
                        'name' => esc_html__('Croatia', 'felan-framework'),
                        'code' => '+385',
                    ),
                    'cu' => array(
                        'name' => esc_html__('Cuba', 'felan-framework'),
                        'code' => '+53',
                    ),
                    'cw' => array(
                        'name' => esc_html__('Curaçao', 'felan-framework'),
                        'code' => '+599',
                    ),
                    'cy' => array(
                        'name' => esc_html__('Cyprus', 'felan-framework'),
                        'code' => '+357',
                    ),
                    'cz' => array(
                        'name' => esc_html__('Czech Republic', 'felan-framework'),
                        'code' => '+420',
                    ),
                    'dk' => array(
                        'name' => esc_html__('Denmark', 'felan-framework'),
                        'code' => '+45',
                    ),
                    'dj' => array(
                        'name' => esc_html__('Djibouti', 'felan-framework'),
                        'code' => '+253',
                    ),
                    'dm' => array(
                        'name' => esc_html__('Dominica', 'felan-framework'),
                        'code' => '+1767',
                    ),
                    'ec' => array(
                        'name' => esc_html__('Ecuador', 'felan-framework'),
                        'code' => '+593',
                    ),
                    'eg' => array(
                        'name' => esc_html__('Egypt', 'felan-framework'),
                        'code' => '+20',
                    ),
                    'sv' => array(
                        'name' => esc_html__('El Salvador', 'felan-framework'),
                        'code' => '+503',
                    ),
                    'gq' => array(
                        'name' => esc_html__('Equatorial Guinea', 'felan-framework'),
                        'code' => '+240',
                    ),
                    'er' => array(
                        'name' => esc_html__('Eritrea', 'felan-framework'),
                        'code' => '+291',
                    ),
                    'ee' => array(
                        'name' => esc_html__('Estonia', 'felan-framework'),
                        'code' => '+372',
                    ),
                    'et' => array(
                        'name' => esc_html__('Ethiopia', 'felan-framework'),
                        'code' => '+251',
                    ),
                    'fk' => array(
                        'name' => esc_html__('Falkland Islands', 'felan-framework'),
                        'code' => '+500',
                    ),
                    'fo' => array(
                        'name' => esc_html__('Faroe Islands', 'felan-framework'),
                        'code' => '+298',
                    ),
                    'fj' => array(
                        'name' => esc_html__('Fiji', 'felan-framework'),
                        'code' => '+679',
                    ),
                    'fi' => array(
                        'name' => esc_html__('Finland', 'felan-framework'),
                        'code' => '+358',
                    ),
                    'fr' => array(
                        'name' => esc_html__('France', 'felan-framework'),
                        'code' => '+33',
                    ),
                    'gf' => array(
                        'name' => esc_html__('French Guiana', 'felan-framework'),
                        'code' => '+594',
                    ),
                    'pf' => array(
                        'name' => esc_html__('French Polynesia', 'felan-framework'),
                        'code' => '+689',
                    ),
                    'ga' => array(
                        'name' => esc_html__('Gabon', 'felan-framework'),
                        'code' => '+241',
                    ),
                    'gm' => array(
                        'name' => esc_html__('Gambia', 'felan-framework'),
                        'code' => '+220',
                    ),
                    'ge' => array(
                        'name' => esc_html__('Georgia', 'felan-framework'),
                        'code' => '+995',
                    ),
                    'de' => array(
                        'name' => esc_html__('Germany', 'felan-framework'),
                        'code' => '+49',
                    ),
                    'gh' => array(
                        'name' => esc_html__('Ghana', 'felan-framework'),
                        'code' => '+233',
                    ),
                    'gi' => array(
                        'name' => esc_html__('Gibraltar', 'felan-framework'),
                        'code' => '+350',
                    ),
                    'gr' => array(
                        'name' => esc_html__('Greece', 'felan-framework'),
                        'code' => '+30',
                    ),
                    'gl' => array(
                        'name' => esc_html__('Greenland', 'felan-framework'),
                        'code' => '+299',
                    ),
                    'gd' => array(
                        'name' => esc_html__('Grenada', 'felan-framework'),
                        'code' => '+1473',
                    ),
                    'gp' => array(
                        'name' => esc_html__('Guadeloupe', 'felan-framework'),
                        'code' => '+590',
                    ),
                    'gu' => array(
                        'name' => esc_html__('Guam', 'felan-framework'),
                        'code' => '+1671',
                    ),
                    'gt' => array(
                        'name' => esc_html__('Guatemala', 'felan-framework'),
                        'code' => '+502',
                    ),
                    'gg' => array(
                        'name' => esc_html__('Guernsey', 'felan-framework'),
                        'code' => '+44',
                    ),
                    'gn' => array(
                        'name' => esc_html__('Guinea', 'felan-framework'),
                        'code' => '+224',
                    ),
                    'gw' => array(
                        'name' => esc_html__('Guinea-Bissau', 'felan-framework'),
                        'code' => '+245',
                    ),
                    'gy' => array(
                        'name' => esc_html__('Guyana', 'felan-framework'),
                        'code' => '+592',
                    ),
                    'ht' => array(
                        'name' => esc_html__('Haiti', 'felan-framework'),
                        'code' => '+509',
                    ),
                    'hn' => array(
                        'name' => esc_html__('Honduras', 'felan-framework'),
                        'code' => '+504',
                    ),
                    'hk' => array(
                        'name' => esc_html__('Hong Kong', 'felan-framework'),
                        'code' => '+852',
                    ),
                    'hu' => array(
                        'name' => esc_html__('Hungary', 'felan-framework'),
                        'code' => '+36',
                    ),
                    'is' => array(
                        'name' => esc_html__('Iceland', 'felan-framework'),
                        'code' => '+354',
                    ),
                    'in' => array(
                        'name' => esc_html__('India', 'felan-framework'),
                        'code' => '+91',
                    ),
                    'id' => array(
                        'name' => esc_html__('Indonesia', 'felan-framework'),
                        'code' => '+62',
                    ),
                    'ir' => array(
                        'name' => esc_html__('Iran', 'felan-framework'),
                        'code' => '+98',
                    ),
                    'iq' => array(
                        'name' => esc_html__('Iraq', 'felan-framework'),
                        'code' => '+964',
                    ),
                    'ie' => array(
                        'name' => esc_html__('Ireland', 'felan-framework'),
                        'code' => '+353',
                    ),
                    'im' => array(
                        'name' => esc_html__('Isle of Man', 'felan-framework'),
                        'code' => '+44',
                    ),
                    'il' => array(
                        'name' => esc_html__('Israel', 'felan-framework'),
                        'code' => '+972',
                    ),
                    'it' => array(
                        'name' => esc_html__('Italy', 'felan-framework'),
                        'code' => '+39',
                    ),
                    'jm' => array(
                        'name' => esc_html__('Jamaica', 'felan-framework'),
                        'code' => '+1876',
                    ),
                    'jp' => array(
                        'name' => esc_html__('Japan', 'felan-framework'),
                        'code' => '+81',
                    ),
                    'je' => array(
                        'name' => esc_html__('Jersey', 'felan-framework'),
                        'code' => '+44',
                    ),
                    'jo' => array(
                        'name' => esc_html__('Jordan', 'felan-framework'),
                        'code' => '+962',
                    ),
                    'kz' => array(
                        'name' => esc_html__('Kazakhstan', 'felan-framework'),
                        'code' => '+7',
                    ),
                    'ke' => array(
                        'name' => esc_html__('Kenya', 'felan-framework'),
                        'code' => '+254',
                    ),
                    'ki' => array(
                        'name' => esc_html__('Kiribati', 'felan-framework'),
                        'code' => '+686',
                    ),
                    'xk' => array(
                        'name' => esc_html__('Kosovo', 'felan-framework'),
                        'code' => '+383',
                    ),
                    'kw' => array(
                        'name' => esc_html__('Kuwait', 'felan-framework'),
                        'code' => '+965',
                    ),
                    'kg' => array(
                        'name' => esc_html__('Kyrgyzstan', 'felan-framework'),
                        'code' => '+996',
                    ),
                    'la' => array(
                        'name' => esc_html__('Laos', 'felan-framework'),
                        'code' => '+856',
                    ),
                    'lv' => array(
                        'name' => esc_html__('Latvia', 'felan-framework'),
                        'code' => '+371',
                    ),
                    'lb' => array(
                        'name' => esc_html__('Lebanon', 'felan-framework'),
                        'code' => '+961',
                    ),
                    'ls' => array(
                        'name' => esc_html__('Lesotho', 'felan-framework'),
                        'code' => '+266',
                    ),
                    'lr' => array(
                        'name' => esc_html__('Liberia', 'felan-framework'),
                        'code' => '+231',
                    ),
                    'ly' => array(
                        'name' => esc_html__('Libya', 'felan-framework'),
                        'code' => '+218',
                    ),
                    'li' => array(
                        'name' => esc_html__('Liechtenstein', 'felan-framework'),
                        'code' => '+423',
                    ),
                    'lt' => array(
                        'name' => esc_html__('Lithuania', 'felan-framework'),
                        'code' => '+370',
                    ),
                    'lu' => array(
                        'name' => esc_html__('Luxembourg', 'felan-framework'),
                        'code' => '+352',
                    ),
                    'mo' => array(
                        'name' => esc_html__('Macau', 'felan-framework'),
                        'code' => '+853',
                    ),
                    'mk' => array(
                        'name' => esc_html__('Macedonia', 'felan-framework'),
                        'code' => '+389',
                    ),
                    'mg' => array(
                        'name' => esc_html__('Madagascar', 'felan-framework'),
                        'code' => '+261',
                    ),
                    'mw' => array(
                        'name' => esc_html__('Malawi', 'felan-framework'),
                        'code' => '+265',
                    ),
                    'my' => array(
                        'name' => esc_html__('Malaysia', 'felan-framework'),
                        'code' => '+60',
                    ),
                    'mv' => array(
                        'name' => esc_html__('Maldives', 'felan-framework'),
                        'code' => '+960',
                    ),
                    'ml' => array(
                        'name' => esc_html__('Mali', 'felan-framework'),
                        'code' => '+223',
                    ),
                    'mt' => array(
                        'name' => esc_html__('Malta', 'felan-framework'),
                        'code' => '+356',
                    ),
                    'mh' => array(
                        'name' => esc_html__('Marshall Islands', 'felan-framework'),
                        'code' => '+692',
                    ),
                    'mq' => array(
                        'name' => esc_html__('Martinique', 'felan-framework'),
                        'code' => '+596',
                    ),
                    'mr' => array(
                        'name' => esc_html__('Mauritania', 'felan-framework'),
                        'code' => '+222',
                    ),
                    'mu' => array(
                        'name' => esc_html__('Mauritius', 'felan-framework'),
                        'code' => '+230',
                    ),
                    'yt' => array(
                        'name' => esc_html__('Mayotte', 'felan-framework'),
                        'code' => '+262',
                    ),
                    'mx' => array(
                        'name' => esc_html__('Mexico', 'felan-framework'),
                        'code' => '+52',
                    ),
                    'fm' => array(
                        'name' => esc_html__('Micronesia', 'felan-framework'),
                        'code' => '+691',
                    ),
                    'md' => array(
                        'name' => esc_html__('Moldova', 'felan-framework'),
                        'code' => '+373',
                    ),
                    'mc' => array(
                        'name' => esc_html__('Monaco', 'felan-framework'),
                        'code' => '+377',
                    ),
                    'mn' => array(
                        'name' => esc_html__('Mongolia', 'felan-framework'),
                        'code' => '+976',
                    ),
                    'me' => array(
                        'name' => esc_html__('Montenegro', 'felan-framework'),
                        'code' => '+382',
                    ),
                    'ms' => array(
                        'name' => esc_html__('Montserrat', 'felan-framework'),
                        'code' => '+1664',
                    ),
                    'ma' => array(
                        'name' => esc_html__('Morocco', 'felan-framework'),
                        'code' => '+212',
                    ),
                    'mz' => array(
                        'name' => esc_html__('Mozambique', 'felan-framework'),
                        'code' => '+258',
                    ),
                    'mm' => array(
                        'name' => esc_html__('Myanmar', 'felan-framework'),
                        'code' => '+95',
                    ),
                    'na' => array(
                        'name' => esc_html__('Namibia', 'felan-framework'),
                        'code' => '+264',
                    ),
                    'nr' => array(
                        'name' => esc_html__('Nauru', 'felan-framework'),
                        'code' => '+674',
                    ),
                    'np' => array(
                        'name' => esc_html__('Nepal', 'felan-framework'),
                        'code' => '+977',
                    ),
                    'nl' => array(
                        'name' => esc_html__('Netherlands', 'felan-framework'),
                        'code' => '+31',
                    ),
                    'nc' => array(
                        'name' => esc_html__('New Caledonia', 'felan-framework'),
                        'code' => '+687',
                    ),
                    'nz' => array(
                        'name' => esc_html__('New Zealand', 'felan-framework'),
                        'code' => '+64',
                    ),
                    'ni' => array(
                        'name' => esc_html__('Nicaragua', 'felan-framework'),
                        'code' => '+505',
                    ),
                    'ne' => array(
                        'name' => esc_html__('Niger', 'felan-framework'),
                        'code' => '+227',
                    ),
                    'ng' => array(
                        'name' => esc_html__('Nigeria', 'felan-framework'),
                        'code' => '+234',
                    ),
                    'nu' => array(
                        'name' => esc_html__('Niue', 'felan-framework'),
                        'code' => '+683',
                    ),
                    'nf' => array(
                        'name' => esc_html__('Norfolk Island', 'felan-framework'),
                        'code' => '+672',
                    ),
                    'kp' => array(
                        'name' => esc_html__('North Korea', 'felan-framework'),
                        'code' => '+850',
                    ),
                    'mp' => array(
                        'name' => esc_html__('Northern Mariana Islands', 'felan-framework'),
                        'code' => '+1670',
                    ),
                    'no' => array(
                        'name' => esc_html__('Norway', 'felan-framework'),
                        'code' => '+47',
                    ),
                    'om' => array(
                        'name' => esc_html__('Oman', 'felan-framework'),
                        'code' => '+968',
                    ),
                    'pk' => array(
                        'name' => esc_html__('Pakistan', 'felan-framework'),
                        'code' => '+92',
                    ),
                    'pw' => array(
                        'name' => esc_html__('Palau', 'felan-framework'),
                        'code' => '+680',
                    ),
                    'ps' => array(
                        'name' => esc_html__('Palestine', 'felan-framework'),
                        'code' => '+970',
                    ),
                    'pa' => array(
                        'name' => esc_html__('Panama', 'felan-framework'),
                        'code' => '+507',
                    ),
                    'pg' => array(
                        'name' => esc_html__('Papua New Guinea', 'felan-framework'),
                        'code' => '+675',
                    ),
                    'py' => array(
                        'name' => esc_html__('Paraguay', 'felan-framework'),
                        'code' => '+595',
                    ),
                    'pe' => array(
                        'name' => esc_html__('Peru', 'felan-framework'),
                        'code' => '+51',
                    ),
                    'ph' => array(
                        'name' => esc_html__('Philippines', 'felan-framework'),
                        'code' => '+63',
                    ),
                    'pl' => array(
                        'name' => esc_html__('Poland', 'felan-framework'),
                        'code' => '+48',
                    ),
                    'pt' => array(
                        'name' => esc_html__('Portugal', 'felan-framework'),
                        'code' => '+351',
                    ),
                    'qa' => array(
                        'name' => esc_html__('Qatar', 'felan-framework'),
                        'code' => '+974',
                    ),
                    're' => array(
                        'name' => esc_html__('Réunion', 'felan-framework'),
                        'code' => '+262',
                    ),
                    'ro' => array(
                        'name' => esc_html__('Romania', 'felan-framework'),
                        'code' => '+40',
                    ),
                    'ru' => array(
                        'name' => esc_html__('Russia', 'felan-framework'),
                        'code' => '+7',
                    ),
                    'rw' => array(
                        'name' => esc_html__('Rwanda', 'felan-framework'),
                        'code' => '+250',
                    ),
                    'bl' => array(
                        'name' => esc_html__('Saint Barthélemy', 'felan-framework'),
                        'code' => '+590',
                    ),
                    'sh' => array(
                        'name' => esc_html__('Saint Helena', 'felan-framework'),
                        'code' => '+290',
                    ),
                    'kn' => array(
                        'name' => esc_html__('Saint Kitts and Nevis', 'felan-framework'),
                        'code' => '+1869',
                    ),
                    'lc' => array(
                        'name' => esc_html__('Saint Lucia', 'felan-framework'),
                        'code' => '+1758',
                    ),
                    'mf' => array(
                        'name' => esc_html__('Saint Martin', 'felan-framework'),
                        'code' => '+590',
                    ),
                    'pm' => array(
                        'name' => esc_html__('Saint Pierre and Miquelon', 'felan-framework'),
                        'code' => '+508',
                    ),
                    'vc' => array(
                        'name' => esc_html__('Saint Vincent and the Grenadines', 'felan-framework'),
                        'code' => '+1784',
                    ),
                    'ws' => array(
                        'name' => esc_html__('Samoa', 'felan-framework'),
                        'code' => '+685',
                    ),
                    'sm' => array(
                        'name' => esc_html__('San Marino', 'felan-framework'),
                        'code' => '+378',
                    ),
                    'st' => array(
                        'name' => esc_html__('São Tomé and Príncipe', 'felan-framework'),
                        'code' => '+239',
                    ),
                    'sa' => array(
                        'name' => esc_html__('Saudi Arabia', 'felan-framework'),
                        'code' => '+966',
                    ),
                    'sn' => array(
                        'name' => esc_html__('Senegal', 'felan-framework'),
                        'code' => '+221',
                    ),
                    'rs' => array(
                        'name' => esc_html__('Serbia', 'felan-framework'),
                        'code' => '+381',
                    ),
                    'sc' => array(
                        'name' => esc_html__('Seychelles', 'felan-framework'),
                        'code' => '+248',
                    ),
                    'sl' => array(
                        'name' => esc_html__('Sierra Leone', 'felan-framework'),
                        'code' => '+232',
                    ),
                    'sg' => array(
                        'name' => esc_html__('Singapore', 'felan-framework'),
                        'code' => '+65',
                    ),
                    'sx' => array(
                        'name' => esc_html__('Sint Maarten', 'felan-framework'),
                        'code' => '+1721',
                    ),
                    'sk' => array(
                        'name' => esc_html__('Slovakia', 'felan-framework'),
                        'code' => '+421',
                    ),
                    'si' => array(
                        'name' => esc_html__('Slovenia', 'felan-framework'),
                        'code' => '+386',
                    ),
                    'sb' => array(
                        'name' => esc_html__('Solomon Islands', 'felan-framework'),
                        'code' => '+677',
                    ),
                    'so' => array(
                        'name' => esc_html__('Somalia', 'felan-framework'),
                        'code' => '+252',
                    ),
                    'za' => array(
                        'name' => esc_html__('South Africa', 'felan-framework'),
                        'code' => '+27',
                    ),
                    'kr' => array(
                        'name' => esc_html__('South Korea', 'felan-framework'),
                        'code' => '+82',
                    ),
                    'ss' => array(
                        'name' => esc_html__('South Sudan', 'felan-framework'),
                        'code' => '+211',
                    ),
                    'es' => array(
                        'name' => esc_html__('Spain', 'felan-framework'),
                        'code' => '+34',
                    ),
                    'lk' => array(
                        'name' => esc_html__('Sri Lanka', 'felan-framework'),
                        'code' => '+94',
                    ),
                    'sd' => array(
                        'name' => esc_html__('Sudan', 'felan-framework'),
                        'code' => '+249',
                    ),
                    'sr' => array(
                        'name' => esc_html__('Suriname', 'felan-framework'),
                        'code' => '+597',
                    ),
                    'sj' => array(
                        'name' => esc_html__('Svalbard and Jan Mayen', 'felan-framework'),
                        'code' => '+47',
                    ),
                    'sz' => array(
                        'name' => esc_html__('Swaziland', 'felan-framework'),
                        'code' => '+268',
                    ),
                    'se' => array(
                        'name' => esc_html__('Sweden', 'felan-framework'),
                        'code' => '+46',
                    ),
                    'ch' => array(
                        'name' => esc_html__('Switzerland', 'felan-framework'),
                        'code' => '+41',
                    ),
                    'sy' => array(
                        'name' => esc_html__('Syria', 'felan-framework'),
                        'code' => '+963',
                    ),
                    'tw' => array(
                        'name' => esc_html__('Taiwan', 'felan-framework'),
                        'code' => '+886',
                    ),
                    'tj' => array(
                        'name' => esc_html__('Tajikistan', 'felan-framework'),
                        'code' => '+992',
                    ),
                    'tz' => array(
                        'name' => esc_html__('Tanzania', 'felan-framework'),
                        'code' => '+255',
                    ),
                    'th' => array(
                        'name' => esc_html__('Thailand', 'felan-framework'),
                        'code' => '+66',
                    ),
                    'tl' => array(
                        'name' => esc_html__('Timor-Leste', 'felan-framework'),
                        'code' => '+670',
                    ),
                    'tg' => array(
                        'name' => esc_html__('Togo', 'felan-framework'),
                        'code' => '+228',
                    ),
                    'tk' => array(
                        'name' => esc_html__('Tokelau', 'felan-framework'),
                        'code' => '+690',
                    ),
                    'tk' => array(
                        'name' => esc_html__('Tokelau', 'felan-framework'),
                        'code' => '+690',
                    ),
                    'to' => array(
                        'name' => esc_html__('Tonga', 'felan-framework'),
                        'code' => '+676',
                    ),
                    'tt' => array(
                        'name' => esc_html__('Trinidad and Tobago', 'felan-framework'),
                        'code' => '+1868',
                    ),
                    'tn' => array(
                        'name' => esc_html__('Tunisia', 'felan-framework'),
                        'code' => '+216',
                    ),
                    'tr' => array(
                        'name' => esc_html__('Turkey', 'felan-framework'),
                        'code' => '+90',
                    ),
                    'tm' => array(
                        'name' => esc_html__('Turkmenistan', 'felan-framework'),
                        'code' => '+993',
                    ),
                    'tc' => array(
                        'name' => esc_html__('Turks and Caicos Islands', 'felan-framework'),
                        'code' => '+1649',
                    ),
                    'tv' => array(
                        'name' => esc_html__('Tuvalu', 'felan-framework'),
                        'code' => '+688',
                    ),
                    'ug' => array(
                        'name' => esc_html__('Uganda', 'felan-framework'),
                        'code' => '+256',
                    ),
                    'ua' => array(
                        'name' => esc_html__('Ukraine', 'felan-framework'),
                        'code' => '+380',
                    ),
                    'ae' => array(
                        'name' => esc_html__('United Arab Emirates', 'felan-framework'),
                        'code' => '+971',
                    ),
                    'gb' => array(
                        'name' => esc_html__('United Kingdom', 'felan-framework'),
                        'code' => '+44',
                    ),
                    'us' => array(
                        'name' => esc_html__('United States', 'felan-framework'),
                        'code' => '+1',
                    ),
                    'uy' => array(
                        'name' => esc_html__('Uruguay', 'felan-framework'),
                        'code' => '+598',
                    ),
                    'uz' => array(
                        'name' => esc_html__('Uzbekistan', 'felan-framework'),
                        'code' => '+998',
                    ),
                    'vu' => array(
                        'name' => esc_html__('Vanuatu', 'felan-framework'),
                        'code' => '+678',
                    ),
                    'va' => array(
                        'name' => esc_html__('Vatican City', 'felan-framework'),
                        'code' => '+39',
                    ),
                    've' => array(
                        'name' => esc_html__('Venezuela', 'felan-framework'),
                        'code' => '+58',
                    ),
                    'vn' => array(
                        'name' => esc_html__('Vietnam', 'felan-framework'),
                        'code' => '+84',
                    ),
                    'wf' => array(
                        'name' => esc_html__('Wallis and Futuna', 'felan-framework'),
                        'code' => '+681',
                    ),
                    'eh' => array(
                        'name' => esc_html__('Western Sahara', 'felan-framework'),
                        'code' => '+212',
                    ),
                    'ye' => array(
                        'name' => esc_html__('Yemen', 'felan-framework'),
                        'code' => '+967',
                    ),
                    'zm' => array(
                        'name' => esc_html__('Zambia', 'felan-framework'),
                        'code' => '+260',
                    ),
                    'zw' => array(
                        'name' => esc_html__('Zimbabwe', 'felan-framework'),
                        'code' => '+263',
                    ),
                )
            );
        }
    }

    /**
     * Get content option taxonomy
     */
    if (!function_exists('felan_content_option_taxonomy')) {
        function felan_content_option_taxonomy($post_type, $postion = 'sidebar')
        {
            if ($post_type == 'jobs') {
                $list_state = felan_get_option_taxonomy('felan_state');
                $list_city = felan_get_option_taxonomy('felan_location');
                $location_country = isset($_GET['jobs-country']) ? felan_clean(wp_unslash($_GET['jobs-country'])) : '';
                $location_state = isset($_GET['jobs-state']) ? felan_clean(wp_unslash($_GET['jobs-state'])) : '';
                $location_city = isset($_GET['jobs-location']) ? felan_clean(wp_unslash($_GET['jobs-location'])) : '';
                $term_state = !empty($location_state) ? get_term_by('slug', $location_state, 'felan_state') : '';
                $term_city = !empty($location_state) ? get_term_by('slug', $location_city, 'felan_location') : '';
            } elseif ($post_type == 'company') {
                $list_state = felan_get_option_taxonomy('felan_state');
                $list_city = felan_get_option_taxonomy('felan_location');
                $location_country = isset($_GET['company-country']) ? felan_clean(wp_unslash($_GET['company-country'])) : '';
                $location_state = isset($_GET['company-state']) ? felan_clean(wp_unslash($_GET['company-state'])) : '';
                $location_city = isset($_GET['company-location']) ? felan_clean(wp_unslash($_GET['company-location'])) : '';
                $term_state = !empty($location_state) ? get_term_by('slug', $location_state, 'felan_state') : '';
                $term_city = !empty($location_state) ? get_term_by('slug', $location_city, 'felan_location') : '';
            } elseif ($post_type == 'freelancer') {
                $list_state = felan_get_option_taxonomy('felan_state');
                $list_city = felan_get_option_taxonomy('felan_location');
                $location_country = isset($_GET['freelancer_state-country']) ? felan_clean(wp_unslash($_GET['freelancer_state-country'])) : '';
                $location_state = isset($_GET['freelancer-state']) ? felan_clean(wp_unslash($_GET['freelancer-state'])) : '';
                $location_city = isset($_GET['freelancer-location']) ? felan_clean(wp_unslash($_GET['freelancer-location'])) : '';
                $term_state = !empty($location_state) ? get_term_by('slug', $location_state, 'felan_state') : '';
                $term_city = !empty($location_state) ? get_term_by('slug', $location_city, 'felan_location') : '';
            } elseif ($post_type == 'service') {
                $list_state = felan_get_option_taxonomy('felan_state');
                $list_city = felan_get_option_taxonomy('felan_location');
                $location_country = isset($_GET['service-country']) ? felan_clean(wp_unslash($_GET['service-country'])) : '';
                $location_state = isset($_GET['service-state']) ? felan_clean(wp_unslash($_GET['service-state'])) : '';
                $location_city = isset($_GET['service-location']) ? felan_clean(wp_unslash($_GET['service-location'])) : '';
                $term_state = !empty($location_state) ? get_term_by('slug', $location_state, 'felan_state') : '';
                $term_city = !empty($location_state) ? get_term_by('slug', $location_city, 'felan_location') : '';
            } elseif ($post_type == 'project') {
                $list_state = felan_get_option_taxonomy('felan_state');
                $list_city = felan_get_option_taxonomy('felan_location');
                $location_country = isset($_GET['project-country']) ? felan_clean(wp_unslash($_GET['project-country'])) : '';
                $location_state = isset($_GET['project-state']) ? felan_clean(wp_unslash($_GET['project-state'])) : '';
                $location_city = isset($_GET['project-location']) ? felan_clean(wp_unslash($_GET['project-location'])) : '';
                $term_state = !empty($location_state) ? get_term_by('slug', $location_state, 'felan_state') : '';
                $term_city = !empty($location_state) ? get_term_by('slug', $location_city, 'felan_location') : '';
            }

            $icon_city = felan_get_option($post_type . '_search_fields_location');
            $icon_state = felan_get_option($post_type . '_search_fields_state');
            $icon_country = felan_get_option($post_type . '_search_fields_country');
            $location_city_id = '';
            if ($term_city && !is_wp_error($term_city)) {
                $location_city_id = $term_city->term_id;
            }
            $location_state_id = '';
            if ($term_state && !is_wp_error($term_state)) {
                $location_state_id = $term_state->term_id;
            }
            if (felan_get_option('enable_option_country') === '1') { ?>
                <div class="form-group">
                    <?php if ($postion == 'top') {
                        echo $icon_country;
                    } ?>
                    <select class="felan-select-country felan-select2" data-post-type="<?php echo $post_type; ?>">
                        <option value=""><?php esc_html_e('Select Countries', 'felan-framework'); ?></option>
                        <?php felan_get_select_option_countries($location_country); ?>
                    </select>
                </div>
            <?php } ?>
            <?php if (felan_get_option('enable_option_state') === '1') { ?>
                <div class="form-group">
                    <?php if ($postion == 'top') {
                        echo $icon_state;
                    } ?>
                    <select class="felan-select-state felan-select2" data-post-type="<?php echo $post_type; ?>">
                        <?php if (felan_get_option('enable_option_country') === '1' && empty($location_state_id) && empty($location_country)) {
                            echo '<option value="">' . esc_html__('Select States', 'felan-framework') . '</option>';
                        } else {
                            echo '<option value="">' . esc_html__('Select States', 'felan-framework') . '</option>';
                            foreach ($list_state as $k => $v) {
                                $selected = ($k === $location_state_id) ? ' selected' : '';
                                echo '<option value="' . esc_attr($k) . '"' . $selected . '>' . esc_html($v) . '</option>';
                            }
                        } ?>
                    </select>
                </div>
            <?php } ?>
            <div class="form-group">
                <?php if ($postion == 'top') {
                    echo $icon_city;
                } ?>
                <select class="felan-select-city felan-select2">
                    <?php if (felan_get_option('enable_option_state') === '1' && empty($location_city_id) && empty($location_state_id)) {
                        echo '<option value="">' . esc_html__('Select Cities', 'felan-framework') . '</option>';
                    } else {
                        echo '<option value="">' . esc_html__('Select Cities', 'felan-framework') . '</option>';
                        foreach ($list_city as $k => $v) {
                            $selected = ($k === $location_city_id) ? ' selected' : '';
                            echo '<option value="' . esc_attr($k) . '"' . $selected . '>' . esc_html($v) . '</option>';
                        }
                    } ?>
                </select>
            </div>
    <?php
        }
    }

    /**
     * Get option taxonomy
     */
    if (!function_exists('felan_get_option_taxonomy')) {
        function felan_get_option_taxonomy($taxonomy)
        {
            $taxonomy_terms = get_categories(
                array(
                    'taxonomy' => $taxonomy,
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'hide_empty' => false,
                    'parent' => 0
                )
            );
            $keys = $values = array();
            foreach ($taxonomy_terms as $terms) {
                $keys[] = $terms->term_id;
                $values[] = $terms->name;
            }
            $list_location = array_combine($keys, $values);

            return $list_location;
        }
    }

    /**
     * Get select option countries
     */
    if (!function_exists('felan_get_select_option_countries')) {
        function felan_get_select_option_countries($location_country)
        {
            $select_option_country = felan_get_option('select_option_country');
            $countries = felan_get_countries();
            $keys = $values = array();
            if (!empty($select_option_country)) {
                foreach ($select_option_country as $key_country => $option_country) {
                    if (array_key_exists($option_country, $countries)) {
                        $keys[] = $option_country;
                        $values[] = $countries[$option_country];
                    }
                }
                $list_country = array_combine($keys, $values);
            } else {
                $list_country = $countries;
            }

            foreach ($list_country as $k => $v) {
                $selected = ($k === $location_country) ? ' selected' : '';
                echo '<option value="' . esc_attr($k) . '"' . $selected . '>' . esc_html($v) . '</option>';
            }
        }
    }


    /**
     * Get countries
     */
    if (!function_exists('felan_get_countries')) {
        function felan_get_countries()
        {
            $countries = apply_filters('felan_get_countries', array(
                'AF' => esc_html__('Afghanistan', 'felan-framework'),
                'AX' => esc_html__('Aland Islands', 'felan-framework'),
                'AL' => esc_html__('Albania', 'felan-framework'),
                'DZ' => esc_html__('Algeria', 'felan-framework'),
                'AS' => esc_html__('American Samoa', 'felan-framework'),
                'AD' => esc_html__('Andorra', 'felan-framework'),
                'AO' => esc_html__('Angola', 'felan-framework'),
                'AI' => esc_html__('Anguilla', 'felan-framework'),
                'AQ' => esc_html__('Antarctica', 'felan-framework'),
                'AG' => esc_html__('Antigua and Barbuda', 'felan-framework'),
                'AR' => esc_html__('Argentina', 'felan-framework'),
                'AM' => esc_html__('Armenia', 'felan-framework'),
                'AW' => esc_html__('Aruba', 'felan-framework'),
                'AU' => esc_html__('Australia', 'felan-framework'),
                'AT' => esc_html__('Austria', 'felan-framework'),
                'AZ' => esc_html__('Azerbaijan', 'felan-framework'),
                'BS' => esc_html__('Bahamas the', 'felan-framework'),
                'BH' => esc_html__('Bahrain', 'felan-framework'),
                'BD' => esc_html__('Bangladesh', 'felan-framework'),
                'BB' => esc_html__('Barbados', 'felan-framework'),
                'BY' => esc_html__('Belarus', 'felan-framework'),
                'BE' => esc_html__('Belgium', 'felan-framework'),
                'BZ' => esc_html__('Belize', 'felan-framework'),
                'BJ' => esc_html__('Benin', 'felan-framework'),
                'BM' => esc_html__('Bermuda', 'felan-framework'),
                'BT' => esc_html__('Bhutan', 'felan-framework'),
                'BO' => esc_html__('Bolivia', 'felan-framework'),
                'BA' => esc_html__('Bosnia and Herzegovina', 'felan-framework'),
                'BW' => esc_html__('Botswana', 'felan-framework'),
                'BV' => esc_html__('Bouvet Island (Bouvetoya)', 'felan-framework'),
                'BR' => esc_html__('Brazil', 'felan-framework'),
                'IO' => esc_html__('British Indian Ocean Territory (Chagos Archipelago)', 'felan-framework'),
                'VG' => esc_html__('British Virgin Islands', 'felan-framework'),
                'BN' => esc_html__('Brunei Darussalam', 'felan-framework'),
                'BG' => esc_html__('Bulgaria', 'felan-framework'),
                'BF' => esc_html__('Burkina Faso', 'felan-framework'),
                'BI' => esc_html__('Burundi', 'felan-framework'),
                'KH' => esc_html__('Cambodia', 'felan-framework'),
                'CM' => esc_html__('Cameroon', 'felan-framework'),
                'CA' => esc_html__('Canada', 'felan-framework'),
                'CV' => esc_html__('Cape Verde', 'felan-framework'),
                'KY' => esc_html__('Cayman Islands', 'felan-framework'),
                'CF' => esc_html__('Central African Republic', 'felan-framework'),
                'TD' => esc_html__('Chad', 'felan-framework'),
                'CL' => esc_html__('Chile', 'felan-framework'),
                'CN' => esc_html__('China', 'felan-framework'),
                'CX' => esc_html__('Christmas Island', 'felan-framework'),
                'CC' => esc_html__('Felans (Keeling) Islands', 'felan-framework'),
                'CO' => esc_html__('Colombia', 'felan-framework'),
                'KM' => esc_html__('Comoros the', 'felan-framework'),
                'CD' => esc_html__('Congo', 'felan-framework'),
                'CG' => esc_html__('Congo the', 'felan-framework'),
                'CK' => esc_html__('Cook Islands', 'felan-framework'),
                'CR' => esc_html__('Costa Rica', 'felan-framework'),
                'CI' => esc_html__("Cote d'Ivoire", 'felan-framework'),
                'HR' => esc_html__('Croatia', 'felan-framework'),
                'CU' => esc_html__('Cuba', 'felan-framework'),
                'CY' => esc_html__('Cyprus', 'felan-framework'),
                'CZ' => esc_html__('Czech Republic', 'felan-framework'),
                'DK' => esc_html__('Denmark', 'felan-framework'),
                'DJ' => esc_html__('Djibouti', 'felan-framework'),
                'DM' => esc_html__('Dominica', 'felan-framework'),
                'DO' => esc_html__('Dominican Republic', 'felan-framework'),
                'EC' => esc_html__('Ecuador', 'felan-framework'),
                'EG' => esc_html__('Egypt', 'felan-framework'),
                'SV' => esc_html__('El Salvador', 'felan-framework'),
                'GQ' => esc_html__('Equatorial Guinea', 'felan-framework'),
                'ER' => esc_html__('Eritrea', 'felan-framework'),
                'EE' => esc_html__('Estonia', 'felan-framework'),
                'ET' => esc_html__('Ethiopia', 'felan-framework'),
                'FO' => esc_html__('Faroe Islands', 'felan-framework'),
                'FK' => esc_html__('Falkland Islands (Malvinas)', 'felan-framework'),
                'FJ' => esc_html__('Fiji the Fiji Islands', 'felan-framework'),
                'FI' => esc_html__('Finland', 'felan-framework'),
                'FR' => esc_html__('France', 'felan-framework'),
                'GF' => esc_html__('French Guiana', 'felan-framework'),
                'PF' => esc_html__('French Polynesia', 'felan-framework'),
                'TF' => esc_html__('French Southern Territories', 'felan-framework'),
                'GA' => esc_html__('Gabon', 'felan-framework'),
                'GM' => esc_html__('Gambia the', 'felan-framework'),
                'GE' => esc_html__('Georgia', 'felan-framework'),
                'DE' => esc_html__('Germany', 'felan-framework'),
                'GH' => esc_html__('Ghana', 'felan-framework'),
                'GI' => esc_html__('Gibraltar', 'felan-framework'),
                'GR' => esc_html__('Greece', 'felan-framework'),
                'GL' => esc_html__('Greenland', 'felan-framework'),
                'GD' => esc_html__('Grenada', 'felan-framework'),
                'GP' => esc_html__('Guadeloupe', 'felan-framework'),
                'GU' => esc_html__('Guam', 'felan-framework'),
                'GT' => esc_html__('Guatemala', 'felan-framework'),
                'GG' => esc_html__('Guernsey', 'felan-framework'),
                'GN' => esc_html__('Guinea', 'felan-framework'),
                'GW' => esc_html__('Guinea-Bissau', 'felan-framework'),
                'GY' => esc_html__('Guyana', 'felan-framework'),
                'HT' => esc_html__('Haiti', 'felan-framework'),
                'HM' => esc_html__('Heard Island and McDonald Islands', 'felan-framework'),
                'VA' => esc_html__('Holy See (Vatican City State)', 'felan-framework'),
                'HN' => esc_html__('Honduras', 'felan-framework'),
                'HK' => esc_html__('Hong Kong', 'felan-framework'),
                'HU' => esc_html__('Hungary', 'felan-framework'),
                'IS' => esc_html__('Iceland', 'felan-framework'),
                'IN' => esc_html__('India', 'felan-framework'),
                'ID' => esc_html__('Indonesia', 'felan-framework'),
                'IR' => esc_html__('Iran', 'felan-framework'),
                'IQ' => esc_html__('Iraq', 'felan-framework'),
                'IE' => esc_html__('Ireland', 'felan-framework'),
                'IM' => esc_html__('Isle of Man', 'felan-framework'),
                'IL' => esc_html__('Israel', 'felan-framework'),
                'IT' => esc_html__('Italy', 'felan-framework'),
                'JM' => esc_html__('Jamaica', 'felan-framework'),
                'JP' => esc_html__('Japan', 'felan-framework'),
                'JE' => esc_html__('Jersey', 'felan-framework'),
                'JO' => esc_html__('Jordan', 'felan-framework'),
                'KZ' => esc_html__('Kazakhstan', 'felan-framework'),
                'KE' => esc_html__('Kenya', 'felan-framework'),
                'KI' => esc_html__('Kiribati', 'felan-framework'),
                'KP' => esc_html__('Korea', 'felan-framework'),
                'KR' => esc_html__('Korea', 'felan-framework'),
                'KW' => esc_html__('Kuwait', 'felan-framework'),
                'KG' => esc_html__('Kyrgyz Republic', 'felan-framework'),
                'LA' => esc_html__('Lao', 'felan-framework'),
                'LV' => esc_html__('Latvia', 'felan-framework'),
                'LB' => esc_html__('Lebanon', 'felan-framework'),
                'LS' => esc_html__('Lesotho', 'felan-framework'),
                'LR' => esc_html__('Liberia', 'felan-framework'),
                'LY' => esc_html__('Libyan Arab Jamahiriya', 'felan-framework'),
                'LI' => esc_html__('Liechtenstein', 'felan-framework'),
                'LT' => esc_html__('Lithuania', 'felan-framework'),
                'LU' => esc_html__('Luxembourg', 'felan-framework'),
                'MO' => esc_html__('Macao', 'felan-framework'),
                'MK' => esc_html__('Macedonia', 'felan-framework'),
                'MG' => esc_html__('Madagascar', 'felan-framework'),
                'MW' => esc_html__('Malawi', 'felan-framework'),
                'MY' => esc_html__('Malaysia', 'felan-framework'),
                'MV' => esc_html__('Maldives', 'felan-framework'),
                'ML' => esc_html__('Mali', 'felan-framework'),
                'MT' => esc_html__('Malta', 'felan-framework'),
                'MH' => esc_html__('Marshall Islands', 'felan-framework'),
                'MQ' => esc_html__('Martinique', 'felan-framework'),
                'MR' => esc_html__('Mauritania', 'felan-framework'),
                'MU' => esc_html__('Mauritius', 'felan-framework'),
                'YT' => esc_html__('Mayotte', 'felan-framework'),
                'MX' => esc_html__('Mexico', 'felan-framework'),
                'FM' => esc_html__('Micronesia', 'felan-framework'),
                'MD' => esc_html__('Moldova', 'felan-framework'),
                'MC' => esc_html__('Monaco', 'felan-framework'),
                'MN' => esc_html__('Mongolia', 'felan-framework'),
                'ME' => esc_html__('Montenegro', 'felan-framework'),
                'MS' => esc_html__('Montserrat', 'felan-framework'),
                'MA' => esc_html__('Morocco', 'felan-framework'),
                'MZ' => esc_html__('Mozambique', 'felan-framework'),
                'MM' => esc_html__('Myanmar', 'felan-framework'),
                'NA' => esc_html__('Namibia', 'felan-framework'),
                'NR' => esc_html__('Nauru', 'felan-framework'),
                'NP' => esc_html__('Nepal', 'felan-framework'),
                'AN' => esc_html__('Netherlands Antilles', 'felan-framework'),
                'NL' => esc_html__('Netherlands the', 'felan-framework'),
                'NC' => esc_html__('New Caledonia', 'felan-framework'),
                'NZ' => esc_html__('New Zealand', 'felan-framework'),
                'NI' => esc_html__('Nicaragua', 'felan-framework'),
                'NE' => esc_html__('Niger', 'felan-framework'),
                'NG' => esc_html__('Nigeria', 'felan-framework'),
                'NU' => esc_html__('Niue', 'felan-framework'),
                'NF' => esc_html__('Norfolk Island', 'felan-framework'),
                'MP' => esc_html__('Northern Mariana Islands', 'felan-framework'),
                'NO' => esc_html__('Norway', 'felan-framework'),
                'OM' => esc_html__('Oman', 'felan-framework'),
                'PK' => esc_html__('Pakistan', 'felan-framework'),
                'PW' => esc_html__('Palau', 'felan-framework'),
                'PS' => esc_html__('Palestinian Territory', 'felan-framework'),
                'PA' => esc_html__('Panama', 'felan-framework'),
                'PG' => esc_html__('Papua New Guinea', 'felan-framework'),
                'PY' => esc_html__('Paraguay', 'felan-framework'),
                'PE' => esc_html__('Peru', 'felan-framework'),
                'PH' => esc_html__('Philippines', 'felan-framework'),
                'PN' => esc_html__('Pitcairn Islands', 'felan-framework'),
                'PL' => esc_html__('Poland', 'felan-framework'),
                'PT' => esc_html__('Portugal, Portuguese Republic', 'felan-framework'),
                'PR' => esc_html__('Puerto Rico', 'felan-framework'),
                'QA' => esc_html__('Qatar', 'felan-framework'),
                'RE' => esc_html__('Reunion', 'felan-framework'),
                'RO' => esc_html__('Romania', 'felan-framework'),
                'RU' => esc_html__('Russian Federation', 'felan-framework'),
                'RW' => esc_html__('Rwanda', 'felan-framework'),
                'BL' => esc_html__('Saint Barthelemy', 'felan-framework'),
                'SH' => esc_html__('Saint Helena', 'felan-framework'),
                'KN' => esc_html__('Saint Kitts and Nevis', 'felan-framework'),
                'LC' => esc_html__('Saint Lucia', 'felan-framework'),
                'MF' => esc_html__('Saint Martin', 'felan-framework'),
                'PM' => esc_html__('Saint Pierre and Miquelon', 'felan-framework'),
                'VC' => esc_html__('Saint Vincent and the Grenadines', 'felan-framework'),
                'WS' => esc_html__('Samoa', 'felan-framework'),
                'SM' => esc_html__('San Marino', 'felan-framework'),
                'ST' => esc_html__('Sao Tome and Principe', 'felan-framework'),
                'SA' => esc_html__('Saudi Arabia', 'felan-framework'),
                'SN' => esc_html__('Senegal', 'felan-framework'),
                'RS' => esc_html__('Serbia', 'felan-framework'),
                'SC' => esc_html__('Seychelles', 'felan-framework'),
                'SL' => esc_html__('Sierra Leone', 'felan-framework'),
                'SG' => esc_html__('Singapore', 'felan-framework'),
                'SK' => esc_html__('Slovakia (Slovak Republic)', 'felan-framework'),
                'SI' => esc_html__('Slovenia', 'felan-framework'),
                'SB' => esc_html__('Solomon Islands', 'felan-framework'),
                'SO' => esc_html__('Somalia, Somali Republic', 'felan-framework'),
                'ZA' => esc_html__('South Africa', 'felan-framework'),
                'GS' => esc_html__('South Georgia and the South Sandwich Islands', 'felan-framework'),
                'ES' => esc_html__('Spain', 'felan-framework'),
                'LK' => esc_html__('Sri Lanka', 'felan-framework'),
                'SD' => esc_html__('Sudan', 'felan-framework'),
                'SR' => esc_html__('Suriname', 'felan-framework'),
                'SJ' => esc_html__('Svalbard & Jan Mayen Islands', 'felan-framework'),
                'SZ' => esc_html__('Swaziland', 'felan-framework'),
                'SE' => esc_html__('Sweden', 'felan-framework'),
                'CH' => esc_html__('Switzerland, Swiss Confederation', 'felan-framework'),
                'SY' => esc_html__('Syrian Arab Republic', 'felan-framework'),
                'TW' => esc_html__('Taiwan', 'felan-framework'),
                'TJ' => esc_html__('Tajikistan', 'felan-framework'),
                'TZ' => esc_html__('Tanzania', 'felan-framework'),
                'TH' => esc_html__('Thailand', 'felan-framework'),
                'TL' => esc_html__('Timor-Leste', 'felan-framework'),
                'TG' => esc_html__('Togo', 'felan-framework'),
                'TK' => esc_html__('Tokelau', 'felan-framework'),
                'TO' => esc_html__('Tonga', 'felan-framework'),
                'TT' => esc_html__('Trinidad and Tobago', 'felan-framework'),
                'TN' => esc_html__('Tunisia', 'felan-framework'),
                'TR' => esc_html__('Turkey', 'felan-framework'),
                'TM' => esc_html__('Turkmenistan', 'felan-framework'),
                'TC' => esc_html__('Turks and Caicos Islands', 'felan-framework'),
                'TV' => esc_html__('Tuvalu', 'felan-framework'),
                'UG' => esc_html__('Uganda', 'felan-framework'),
                'UA' => esc_html__('Ukraine', 'felan-framework'),
                'AE' => esc_html__('United Arab Emirates', 'felan-framework'),
                'GB' => esc_html__('United Kingdom', 'felan-framework'),
                'SCL' => esc_html__('Scotland', 'felan-framework'),
                'WL' => esc_html__('Wales', 'felan-framework'),
                'NIR' => esc_html__('Northern Ireland', 'felan-framework'),
                'US' => esc_html__('United States', 'felan-framework'),
                'UM' => esc_html__('United States Minor Outlying Islands', 'felan-framework'),
                'VI' => esc_html__('United States Virgin Islands', 'felan-framework'),
                'UY' => esc_html__('Uruguay, Eastern Republic of', 'felan-framework'),
                'UZ' => esc_html__('Uzbekistan', 'felan-framework'),
                'VU' => esc_html__('Vanuatu', 'felan-framework'),
                'VE' => esc_html__('Venezuela', 'felan-framework'),
                'VN' => esc_html__('Vietnam', 'felan-framework'),
                'WF' => esc_html__('Wallis and Futuna', 'felan-framework'),
                'EH' => esc_html__('Western Sahara', 'felan-framework'),
                'YE' => esc_html__('Yemen', 'felan-framework'),
                'ZM' => esc_html__('Zambia', 'felan-framework'),
                'ZW' => esc_html__('Zimbabwe', 'felan-framework'),
                'SVG' => esc_html__('Saint Vincent', 'felan-framework'),
            ));

            return $countries;
        }
    }

    /**
     * Get page by title
     */
    if (!function_exists('felan_get_page_by_title')) {
        function felan_get_page_by_title($title, $post_type)
        {
            $query = new WP_Query(
                array(
                    'post_type' => $post_type,
                    'title' => $title,
                    'posts_per_page' => 1,
                    'no_found_rows' => true,
                    'ignore_sticky_posts' => true,
                    'update_post_term_cache' => false,
                    'update_post_meta_cache' => false,
                )
            );
            if (!empty($query->post)) {
                $fetched_page = $query->post;
                return $fetched_page;
            } else {
                return false;
            }
        }
    }


    function is_string_in_current_url($string)
    {
        $current_url = $_SERVER['REQUEST_URI'];
        return strpos($current_url, $string) !== false;
    }
