<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Helper functions
 */
if (!class_exists('Felan_Helper')) {

    class Felan_Helper
    {

        /**
         * The constructor.
         */
        function __construct()
        {
            add_action('delete_attachment', array($this, 'felan_delete_resized_images'));

            add_filter('body_class', array($this, 'felan_body_class'));
        }

        /**
         * Get Setting
         */
        public static function get_setting($key)
        {
            $option = '';
            $option = get_option_customize($key);
            return $option;
        }

        /**
         * Get Option
         */
        public static function felan_get_option($key, $default = '')
        {
            $option = '';
            if (class_exists('Felan_Framework')) {
                $option = felan_get_option($key, $default);
            }
            return (isset($option)) ? $option : $default;
        }

        /**
         * Clean Variable
         */
        public static function felan_clean($var)
        {
            if (is_array($var)) {
                return array_map('felan_clean', $var);
            } else {
                return is_scalar($var) ? sanitize_text_field($var) : $var;
            }
        }

        /**
         * Get Setting
         */
        public static function felan_body_class($classes)
        {

            $enable_rtl_mode  = Felan_Helper::felan_get_option('enable_rtl_mode', 0);

            $type_single_jobs = Felan_Helper::felan_get_option('single_job_layout', '01');
            $type_single_jobs = !empty($_GET['layout']) ? felan_clean(wp_unslash($_GET['layout'])) : $type_single_jobs;

            if (is_rtl() || $enable_rtl_mode) {
                $classes[] = 'rtl';
            }

            if ($type_single_jobs == '04') {
                $classes[] = 'single-jobs-layout-04';
            }

            return $classes;
        }

        /**
         * Check has shortcode
         */
        public static function felan_page_shortcode($shortcode = NULL)
        {

            $post = get_post(get_the_ID());

            if (empty($post->post_content)) {
                return false;
            }

            $found = false;

            if ($post->post_content === $shortcode) {
                $found = true;
            }

            // return our final results
            return $found;
        }

        /**
         * Allowed_html
         */
        public static function felan_kses_allowed_html()
        {
            $allowed_tags = array(
                'a' => array(
                    'id'    => array(),
                    'class' => array(),
                    'href'  => array(),
                    'rel'   => array(),
                    'title' => array(),
                ),
                'abbr' => array(
                    'title' => array(),
                ),
                'b' => array(),
                'blockquote' => array(
                    'cite'  => array(),
                ),
                'cite' => array(
                    'title' => array(),
                ),
                'code' => array(),
                'del' => array(
                    'datetime' => array(),
                    'title' => array(),
                ),
                'dd' => array(),
                'div' => array(
                    'class' => array(),
                    'title' => array(),
                    'style' => array(),
                ),
                'dl' => array(),
                'dt' => array(),
                'em' => array(),
                'h1' => array(),
                'h2' => array(),
                'h3' => array(),
                'h4' => array(),
                'h5' => array(),
                'h6' => array(),
                'i' => array(
                    'class' => array(),
                ),
                'img' => array(
                    'alt'    => array(),
                    'class'  => array(),
                    'height' => array(),
                    'src'    => array(),
                    'width'  => array(),
                ),
                'li' => array(
                    'class' => array(),
                ),
                'ol' => array(
                    'class' => array(),
                ),
                'p' => array(
                    'class' => array(),
                ),
                'q' => array(
                    'cite' => array(),
                    'title' => array(),
                ),
                'span' => array(
                    'class' => array(),
                    'title' => array(),
                    'style' => array(),
                ),
                'strike' => array(),
                'strong' => array(),
                'ul' => array(
                    'class' => array(),
                ),
            );

            return $allowed_tags;
        }

        /**
         * Image size
         */
        public static function felan_image_resize($data, $image_size)
        {
            if (preg_match('/\d+x\d+/', $image_size)) {
                $image_sizes = explode('x', $image_size);
                $image_src  = self::felan_image_resize_id($data, $image_sizes[0], $image_sizes[1], true);
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

        /**
         * Image resize by url
         */
        public static function felan_image_resize_url($url, $width = NULL, $height = NULL, $crop = true, $retina = false)
        {

            global $wpdb;

            if (empty($url))
                return new WP_Error('no_image_url', esc_html__('No image URL has been entered.', 'felan'), $url);

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
            $file_path = ABSPATH . $file_path['path'];

            // Check for Multisite
            if (is_multisite()) {
                global $blog_id;
                $blog_details = get_blog_details($blog_id);
                $file_path = str_replace($blog_details->path, '/', $file_path);
                //$file_path = str_replace($blog_details->path . 'files/', '/wp-content/blogs.dir/' . $blog_id . '/files/', $file_path);
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
                return new WP_Error('bmp_mime_type', esc_html__('Image is BMP. Please use either JPG or PNG.', 'felan'), $url);
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
                // if (!$get_attachment)
                //     return array('url' => $url, 'width' => $width, 'height' => $height);

                // Load Wordpress Image Editor
                $editor = wp_get_image_editor($file_path);
                if (is_wp_error($editor))
                    return array('url' => $url, 'width' => $width, 'height' => $height);

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
                    $metadata = wp_get_attachment_metadata($get_attachment[0]->ID);
                    if (isset($metadata['image_meta'])) {
                        $metadata['image_meta']['resized_images'][] = $resized_width . 'x' . $resized_height;
                        wp_update_attachment_metadata($get_attachment[0]->ID, $metadata);
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

        /**
         * Image resize by id
         */
        public static function felan_image_resize_id($images_id, $width = NULL, $height = NULL, $crop = true, $retina = false)
        {
            $output = '';
            $image_src = wp_get_attachment_image_src($images_id, 'full');
            if ($image_src) {
                $resize = self::felan_image_resize_url($image_src[0], $width, $height, $crop, $retina);
                if ($resize != null && is_array($resize)) {
                    $output = $resize['url'];
                }
            }
            return $output;
        }

        /**
         * Delete resized images
         */
        public static function felan_delete_resized_images($post_id)
        {
            // Get attachment image metadata
            $metadata = wp_get_attachment_metadata($post_id);
            if (!$metadata)
                return;

            // Do some bailing if we cannot continue
            if (!isset($metadata['file']) || !isset($metadata['image_meta']['resized_images']))
                return;
            $pathinfo = pathinfo($metadata['file']);
            $resized_images = $metadata['image_meta']['resized_images'];

            // Get Wordpress uploads directory (and bail if it doesn't exist)
            $wp_upload_dir = wp_upload_dir();
            $upload_dir = $wp_upload_dir['basedir'];
            if (!is_dir($upload_dir))
                return;

            // Delete the resized images
            foreach ($resized_images as $dims) {

                // Get the resized images filename
                $file = $upload_dir . '/' . $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-' . $dims . '.' . $pathinfo['extension'];

                // Delete the resized image
                @unlink($file);
            }
        }

        /**
         * Get format number
         */
        public static function felan_format_number($number, $decimals = 0)
        {
            $number = doubleval($number);
            if ($number) {
                $dec_point = Felan_Helper::get_setting('decimal_separator', '.');
                $thousands_sep = Felan_Helper::get_setting('thousand_separator', ',');

                return number_format($number, $decimals, $dec_point, $thousands_sep);
            } else {
                return 0;
            }
        }

        /**
         * Phone prefix code
         */
        public static function phone_prefix_code()
        {
            return array(
                'ax' => array(
                    'name' => esc_html__('Åland Islands', 'felan'),
                    'code' => '+358',
                ),
                'af' => array(
                    'name' => esc_html__('Afghanistan', 'felan'),
                    'code' => '+93',
                ),
                'al' => array(
                    'name' => esc_html__('Albania', 'felan'),
                    'code' => '+355',
                ),
                'dz' => array(
                    'name' => esc_html__('Algeria', 'felan'),
                    'code' => '+213',
                ),
                'as' => array(
                    'name' => esc_html__('American Samoa', 'felan'),
                    'code' => '+1684',
                ),
                'ad' => array(
                    'name' => esc_html__('Andorra', 'felan'),
                    'code' => '+376',
                ),
                'ao' => array(
                    'name' => esc_html__('Angola', 'felan'),
                    'code' => '+244',
                ),
                'ai' => array(
                    'name' => esc_html__('Anguilla', 'felan'),
                    'code' => '+1264',
                ),
                'ag' => array(
                    'name' => esc_html__('Antigua and Barbuda', 'felan'),
                    'code' => '+1268',
                ),
                'ar' => array(
                    'name' => esc_html__('Argentina', 'felan'),
                    'code' => '+54',
                ),
                'am' => array(
                    'name' => esc_html__('Armenia', 'felan'),
                    'code' => '+374',
                ),
                'aw' => array(
                    'name' => esc_html__('Aruba', 'felan'),
                    'code' => '+297',
                ),
                'au' => array(
                    'name' => esc_html__('Australia', 'felan'),
                    'code' => '+61',
                ),
                'at' => array(
                    'name' => esc_html__('Austria', 'felan'),
                    'code' => '+43',
                ),
                'az' => array(
                    'name' => esc_html__('Azerbaijan', 'felan'),
                    'code' => '+994',
                ),
                'bs' => array(
                    'name' => esc_html__('Bahamas', 'felan'),
                    'code' => '+1242',
                ),
                'bh' => array(
                    'name' => esc_html__('Bahrain', 'felan'),
                    'code' => '+973',
                ),
                'bd' => array(
                    'name' => esc_html__('Bangladesh', 'felan'),
                    'code' => '+880',
                ),
                'bb' => array(
                    'name' => esc_html__('Barbados', 'felan'),
                    'code' => '+1246',
                ),
                'by' => array(
                    'name' => esc_html__('Belarus', 'felan'),
                    'code' => '+375',
                ),
                'be' => array(
                    'name' => esc_html__('Belgium', 'felan'),
                    'code' => '+32',
                ),
                'bz' => array(
                    'name' => esc_html__('Belize', 'felan'),
                    'code' => '+501',
                ),
                'bj' => array(
                    'name' => esc_html__('Benin', 'felan'),
                    'code' => '+229',
                ),
                'bm' => array(
                    'name' => esc_html__('Bermuda', 'felan'),
                    'code' => '+1441',
                ),
                'bt' => array(
                    'name' => esc_html__('Bhutan', 'felan'),
                    'code' => '+975',
                ),
                'bo' => array(
                    'name' => esc_html__('Bolivia', 'felan'),
                    'code' => '+591',
                ),
                'ba' => array(
                    'name' => esc_html__('Bosnia and Herzegovina', 'felan'),
                    'code' => '+387',
                ),
                'bw' => array(
                    'name' => esc_html__('Botswana', 'felan'),
                    'code' => '+267',
                ),
                'br' => array(
                    'name' => esc_html__('Brazil', 'felan'),
                    'code' => '+55',
                ),
                'io' => array(
                    'name' => esc_html__('British Indian Ocean Territory', 'felan'),
                    'code' => '+246',
                ),
                'vg' => array(
                    'name' => esc_html__('British Virgin Islands', 'felan'),
                    'code' => '+1284',
                ),
                'bn' => array(
                    'name' => esc_html__('Brunei', 'felan'),
                    'code' => '+673',
                ),
                'bg' => array(
                    'name' => esc_html__('Bulgaria', 'felan'),
                    'code' => '+359',
                ),
                'bf' => array(
                    'name' => esc_html__('Burkina Faso', 'felan'),
                    'code' => '+226',
                ),
                'bi' => array(
                    'name' => esc_html__('Burundi', 'felan'),
                    'code' => '+257',
                ),
                'kh' => array(
                    'name' => esc_html__('Cambodia', 'felan'),
                    'code' => '+855',
                ),
                'cm' => array(
                    'name' => esc_html__('Cameroon', 'felan'),
                    'code' => '+237',
                ),
                'ca' => array(
                    'name' => esc_html__('Canada', 'felan'),
                    'code' => '+1',
                ),
                'cv' => array(
                    'name' => esc_html__('Cape Verde', 'felan'),
                    'code' => '+238',
                ),
                'bq' => array(
                    'name' => esc_html__('Caribbean Netherlands', 'felan'),
                    'code' => '+599',
                ),
                'ky' => array(
                    'name' => esc_html__('Cayman Islands', 'felan'),
                    'code' => '+1345',
                ),
                'cf' => array(
                    'name' => esc_html__('Central African Republic', 'felan'),
                    'code' => '+236',
                ),
                'td' => array(
                    'name' => esc_html__('Chad', 'felan'),
                    'code' => '+235',
                ),
                'cl' => array(
                    'name' => esc_html__('Chile', 'felan'),
                    'code' => '+56',
                ),
                'cn' => array(
                    'name' => esc_html__('China', 'felan'),
                    'code' => '+86',
                ),
                'cx' => array(
                    'name' => esc_html__('Christmas Island', 'felan'),
                    'code' => '+61',
                ),
                'co' => array(
                    'name' => esc_html__('Colombia', 'felan'),
                    'code' => '+57',
                ),
                'km' => array(
                    'name' => esc_html__('Comoros', 'felan'),
                    'code' => '+269',
                ),
                'cd' => array(
                    'name' => esc_html__('Congo DRC', 'felan'),
                    'code' => '+243',
                ),
                'cg' => array(
                    'name' => esc_html__('Congo Republic', 'felan'),
                    'code' => '+242',
                ),
                'ck' => array(
                    'name' => esc_html__('Cook Islands', 'felan'),
                    'code' => '+682',
                ),
                'cr' => array(
                    'name' => esc_html__('Costa Rica', 'felan'),
                    'code' => '+506',
                ),
                'ci' => array(
                    'name' => esc_html__('Côte d’Ivoire', 'felan'),
                    'code' => '+225',
                ),
                'hr' => array(
                    'name' => esc_html__('Croatia', 'felan'),
                    'code' => '+385',
                ),
                'cu' => array(
                    'name' => esc_html__('Cuba', 'felan'),
                    'code' => '+53',
                ),
                'cw' => array(
                    'name' => esc_html__('Curaçao', 'felan'),
                    'code' => '+599',
                ),
                'cy' => array(
                    'name' => esc_html__('Cyprus', 'felan'),
                    'code' => '+357',
                ),
                'cz' => array(
                    'name' => esc_html__('Czech Republic', 'felan'),
                    'code' => '+420',
                ),
                'dk' => array(
                    'name' => esc_html__('Denmark', 'felan'),
                    'code' => '+45',
                ),
                'dj' => array(
                    'name' => esc_html__('Djibouti', 'felan'),
                    'code' => '+253',
                ),
                'dm' => array(
                    'name' => esc_html__('Dominica', 'felan'),
                    'code' => '+1767',
                ),
                'ec' => array(
                    'name' => esc_html__('Ecuador', 'felan'),
                    'code' => '+593',
                ),
                'eg' => array(
                    'name' => esc_html__('Egypt', 'felan'),
                    'code' => '+20',
                ),
                'sv' => array(
                    'name' => esc_html__('El Salvador', 'felan'),
                    'code' => '+503',
                ),
                'gq' => array(
                    'name' => esc_html__('Equatorial Guinea', 'felan'),
                    'code' => '+240',
                ),
                'er' => array(
                    'name' => esc_html__('Eritrea', 'felan'),
                    'code' => '+291',
                ),
                'ee' => array(
                    'name' => esc_html__('Estonia', 'felan'),
                    'code' => '+372',
                ),
                'et' => array(
                    'name' => esc_html__('Ethiopia', 'felan'),
                    'code' => '+251',
                ),
                'fk' => array(
                    'name' => esc_html__('Falkland Islands', 'felan'),
                    'code' => '+500',
                ),
                'fo' => array(
                    'name' => esc_html__('Faroe Islands', 'felan'),
                    'code' => '+298',
                ),
                'fj' => array(
                    'name' => esc_html__('Fiji', 'felan'),
                    'code' => '+679',
                ),
                'fi' => array(
                    'name' => esc_html__('Finland', 'felan'),
                    'code' => '+358',
                ),
                'fr' => array(
                    'name' => esc_html__('France', 'felan'),
                    'code' => '+33',
                ),
                'gf' => array(
                    'name' => esc_html__('French Guiana', 'felan'),
                    'code' => '+594',
                ),
                'pf' => array(
                    'name' => esc_html__('French Polynesia', 'felan'),
                    'code' => '+689',
                ),
                'ga' => array(
                    'name' => esc_html__('Gabon', 'felan'),
                    'code' => '+241',
                ),
                'gm' => array(
                    'name' => esc_html__('Gambia', 'felan'),
                    'code' => '+220',
                ),
                'ge' => array(
                    'name' => esc_html__('Georgia', 'felan'),
                    'code' => '+995',
                ),
                'de' => array(
                    'name' => esc_html__('Germany', 'felan'),
                    'code' => '+49',
                ),
                'gh' => array(
                    'name' => esc_html__('Ghana', 'felan'),
                    'code' => '+233',
                ),
                'gi' => array(
                    'name' => esc_html__('Gibraltar', 'felan'),
                    'code' => '+350',
                ),
                'gr' => array(
                    'name' => esc_html__('Greece', 'felan'),
                    'code' => '+30',
                ),
                'gl' => array(
                    'name' => esc_html__('Greenland', 'felan'),
                    'code' => '+299',
                ),
                'gd' => array(
                    'name' => esc_html__('Grenada', 'felan'),
                    'code' => '+1473',
                ),
                'gp' => array(
                    'name' => esc_html__('Guadeloupe', 'felan'),
                    'code' => '+590',
                ),
                'gu' => array(
                    'name' => esc_html__('Guam', 'felan'),
                    'code' => '+1671',
                ),
                'gt' => array(
                    'name' => esc_html__('Guatemala', 'felan'),
                    'code' => '+502',
                ),
                'gg' => array(
                    'name' => esc_html__('Guernsey', 'felan'),
                    'code' => '+44',
                ),
                'gn' => array(
                    'name' => esc_html__('Guinea', 'felan'),
                    'code' => '+224',
                ),
                'gw' => array(
                    'name' => esc_html__('Guinea-Bissau', 'felan'),
                    'code' => '+245',
                ),
                'gy' => array(
                    'name' => esc_html__('Guyana', 'felan'),
                    'code' => '+592',
                ),
                'ht' => array(
                    'name' => esc_html__('Haiti', 'felan'),
                    'code' => '+509',
                ),
                'hn' => array(
                    'name' => esc_html__('Honduras', 'felan'),
                    'code' => '+504',
                ),
                'hk' => array(
                    'name' => esc_html__('Hong Kong', 'felan'),
                    'code' => '+852',
                ),
                'hu' => array(
                    'name' => esc_html__('Hungary', 'felan'),
                    'code' => '+36',
                ),
                'is' => array(
                    'name' => esc_html__('Iceland', 'felan'),
                    'code' => '+354',
                ),
                'in' => array(
                    'name' => esc_html__('India', 'felan'),
                    'code' => '+91',
                ),
                'id' => array(
                    'name' => esc_html__('Indonesia', 'felan'),
                    'code' => '+62',
                ),
                'ir' => array(
                    'name' => esc_html__('Iran', 'felan'),
                    'code' => '+98',
                ),
                'iq' => array(
                    'name' => esc_html__('Iraq', 'felan'),
                    'code' => '+964',
                ),
                'ie' => array(
                    'name' => esc_html__('Ireland', 'felan'),
                    'code' => '+353',
                ),
                'im' => array(
                    'name' => esc_html__('Isle of Man', 'felan'),
                    'code' => '+44',
                ),
                'il' => array(
                    'name' => esc_html__('Israel', 'felan'),
                    'code' => '+972',
                ),
                'it' => array(
                    'name' => esc_html__('Italy', 'felan'),
                    'code' => '+39',
                ),
                'jm' => array(
                    'name' => esc_html__('Jamaica', 'felan'),
                    'code' => '+1876',
                ),
                'jp' => array(
                    'name' => esc_html__('Japan', 'felan'),
                    'code' => '+81',
                ),
                'je' => array(
                    'name' => esc_html__('Jersey', 'felan'),
                    'code' => '+44',
                ),
                'jo' => array(
                    'name' => esc_html__('Jordan', 'felan'),
                    'code' => '+962',
                ),
                'kz' => array(
                    'name' => esc_html__('Kazakhstan', 'felan'),
                    'code' => '+7',
                ),
                'ke' => array(
                    'name' => esc_html__('Kenya', 'felan'),
                    'code' => '+254',
                ),
                'ki' => array(
                    'name' => esc_html__('Kiribati', 'felan'),
                    'code' => '+686',
                ),
                'xk' => array(
                    'name' => esc_html__('Kosovo', 'felan'),
                    'code' => '+383',
                ),
                'kw' => array(
                    'name' => esc_html__('Kuwait', 'felan'),
                    'code' => '+965',
                ),
                'kg' => array(
                    'name' => esc_html__('Kyrgyzstan', 'felan'),
                    'code' => '+996',
                ),
                'la' => array(
                    'name' => esc_html__('Laos', 'felan'),
                    'code' => '+856',
                ),
                'lv' => array(
                    'name' => esc_html__('Latvia', 'felan'),
                    'code' => '+371',
                ),
                'lb' => array(
                    'name' => esc_html__('Lebanon', 'felan'),
                    'code' => '+961',
                ),
                'ls' => array(
                    'name' => esc_html__('Lesotho', 'felan'),
                    'code' => '+266',
                ),
                'lr' => array(
                    'name' => esc_html__('Liberia', 'felan'),
                    'code' => '+231',
                ),
                'ly' => array(
                    'name' => esc_html__('Libya', 'felan'),
                    'code' => '+218',
                ),
                'li' => array(
                    'name' => esc_html__('Liechtenstein', 'felan'),
                    'code' => '+423',
                ),
                'lt' => array(
                    'name' => esc_html__('Lithuania', 'felan'),
                    'code' => '+370',
                ),
                'lu' => array(
                    'name' => esc_html__('Luxembourg', 'felan'),
                    'code' => '+352',
                ),
                'mo' => array(
                    'name' => esc_html__('Macau', 'felan'),
                    'code' => '+853',
                ),
                'mk' => array(
                    'name' => esc_html__('Macedonia', 'felan'),
                    'code' => '+389',
                ),
                'mg' => array(
                    'name' => esc_html__('Madagascar', 'felan'),
                    'code' => '+261',
                ),
                'mw' => array(
                    'name' => esc_html__('Malawi', 'felan'),
                    'code' => '+265',
                ),
                'my' => array(
                    'name' => esc_html__('Malaysia', 'felan'),
                    'code' => '+60',
                ),
                'mv' => array(
                    'name' => esc_html__('Maldives', 'felan'),
                    'code' => '+960',
                ),
                'ml' => array(
                    'name' => esc_html__('Mali', 'felan'),
                    'code' => '+223',
                ),
                'mt' => array(
                    'name' => esc_html__('Malta', 'felan'),
                    'code' => '+356',
                ),
                'mh' => array(
                    'name' => esc_html__('Marshall Islands', 'felan'),
                    'code' => '+692',
                ),
                'mq' => array(
                    'name' => esc_html__('Martinique', 'felan'),
                    'code' => '+596',
                ),
                'mr' => array(
                    'name' => esc_html__('Mauritania', 'felan'),
                    'code' => '+222',
                ),
                'mu' => array(
                    'name' => esc_html__('Mauritius', 'felan'),
                    'code' => '+230',
                ),
                'yt' => array(
                    'name' => esc_html__('Mayotte', 'felan'),
                    'code' => '+262',
                ),
                'mx' => array(
                    'name' => esc_html__('Mexico', 'felan'),
                    'code' => '+52',
                ),
                'fm' => array(
                    'name' => esc_html__('Micronesia', 'felan'),
                    'code' => '+691',
                ),
                'md' => array(
                    'name' => esc_html__('Moldova', 'felan'),
                    'code' => '+373',
                ),
                'mc' => array(
                    'name' => esc_html__('Monaco', 'felan'),
                    'code' => '+377',
                ),
                'mn' => array(
                    'name' => esc_html__('Mongolia', 'felan'),
                    'code' => '+976',
                ),
                'me' => array(
                    'name' => esc_html__('Montenegro', 'felan'),
                    'code' => '+382',
                ),
                'ms' => array(
                    'name' => esc_html__('Montserrat', 'felan'),
                    'code' => '+1664',
                ),
                'ma' => array(
                    'name' => esc_html__('Morocco', 'felan'),
                    'code' => '+212',
                ),
                'mz' => array(
                    'name' => esc_html__('Mozambique', 'felan'),
                    'code' => '+258',
                ),
                'mm' => array(
                    'name' => esc_html__('Myanmar', 'felan'),
                    'code' => '+95',
                ),
                'na' => array(
                    'name' => esc_html__('Namibia', 'felan'),
                    'code' => '+264',
                ),
                'nr' => array(
                    'name' => esc_html__('Nauru', 'felan'),
                    'code' => '+674',
                ),
                'np' => array(
                    'name' => esc_html__('Nepal', 'felan'),
                    'code' => '+977',
                ),
                'nl' => array(
                    'name' => esc_html__('Netherlands', 'felan'),
                    'code' => '+31',
                ),
                'nc' => array(
                    'name' => esc_html__('New Caledonia', 'felan'),
                    'code' => '+687',
                ),
                'nz' => array(
                    'name' => esc_html__('New Zealand', 'felan'),
                    'code' => '+64',
                ),
                'ni' => array(
                    'name' => esc_html__('Nicaragua', 'felan'),
                    'code' => '+505',
                ),
                'ne' => array(
                    'name' => esc_html__('Niger', 'felan'),
                    'code' => '+227',
                ),
                'ng' => array(
                    'name' => esc_html__('Nigeria', 'felan'),
                    'code' => '+234',
                ),
                'nu' => array(
                    'name' => esc_html__('Niue', 'felan'),
                    'code' => '+683',
                ),
                'nf' => array(
                    'name' => esc_html__('Norfolk Island', 'felan'),
                    'code' => '+672',
                ),
                'kp' => array(
                    'name' => esc_html__('North Korea', 'felan'),
                    'code' => '+850',
                ),
                'mp' => array(
                    'name' => esc_html__('Northern Mariana Islands', 'felan'),
                    'code' => '+1670',
                ),
                'no' => array(
                    'name' => esc_html__('Norway', 'felan'),
                    'code' => '+47',
                ),
                'om' => array(
                    'name' => esc_html__('Oman', 'felan'),
                    'code' => '+968',
                ),
                'pk' => array(
                    'name' => esc_html__('Pakistan', 'felan'),
                    'code' => '+92',
                ),
                'pw' => array(
                    'name' => esc_html__('Palau', 'felan'),
                    'code' => '+680',
                ),
                'ps' => array(
                    'name' => esc_html__('Palestine', 'felan'),
                    'code' => '+970',
                ),
                'pa' => array(
                    'name' => esc_html__('Panama', 'felan'),
                    'code' => '+507',
                ),
                'pg' => array(
                    'name' => esc_html__('Papua New Guinea', 'felan'),
                    'code' => '+675',
                ),
                'py' => array(
                    'name' => esc_html__('Paraguay', 'felan'),
                    'code' => '+595',
                ),
                'pe' => array(
                    'name' => esc_html__('Peru', 'felan'),
                    'code' => '+51',
                ),
                'ph' => array(
                    'name' => esc_html__('Philippines', 'felan'),
                    'code' => '+63',
                ),
                'pl' => array(
                    'name' => esc_html__('Poland', 'felan'),
                    'code' => '+48',
                ),
                'pt' => array(
                    'name' => esc_html__('Portugal', 'felan'),
                    'code' => '+351',
                ),
                'qa' => array(
                    'name' => esc_html__('Qatar', 'felan'),
                    'code' => '+974',
                ),
                're' => array(
                    'name' => esc_html__('Réunion', 'felan'),
                    'code' => '+262',
                ),
                'ro' => array(
                    'name' => esc_html__('Romania', 'felan'),
                    'code' => '+40',
                ),
                'ru' => array(
                    'name' => esc_html__('Russia', 'felan'),
                    'code' => '+7',
                ),
                'rw' => array(
                    'name' => esc_html__('Rwanda', 'felan'),
                    'code' => '+250',
                ),
                'bl' => array(
                    'name' => esc_html__('Saint Barthélemy', 'felan'),
                    'code' => '+590',
                ),
                'sh' => array(
                    'name' => esc_html__('Saint Helena', 'felan'),
                    'code' => '+290',
                ),
                'kn' => array(
                    'name' => esc_html__('Saint Kitts and Nevis', 'felan'),
                    'code' => '+1869',
                ),
                'lc' => array(
                    'name' => esc_html__('Saint Lucia', 'felan'),
                    'code' => '+1758',
                ),
                'mf' => array(
                    'name' => esc_html__('Saint Martin', 'felan'),
                    'code' => '+590',
                ),
                'pm' => array(
                    'name' => esc_html__('Saint Pierre and Miquelon', 'felan'),
                    'code' => '+508',
                ),
                'vc' => array(
                    'name' => esc_html__('Saint Vincent and the Grenadines', 'felan'),
                    'code' => '+1784',
                ),
                'ws' => array(
                    'name' => esc_html__('Samoa', 'felan'),
                    'code' => '+685',
                ),
                'sm' => array(
                    'name' => esc_html__('San Marino', 'felan'),
                    'code' => '+378',
                ),
                'st' => array(
                    'name' => esc_html__('São Tomé and Príncipe', 'felan'),
                    'code' => '+239',
                ),
                'sa' => array(
                    'name' => esc_html__('Saudi Arabia', 'felan'),
                    'code' => '+966',
                ),
                'sn' => array(
                    'name' => esc_html__('Senegal', 'felan'),
                    'code' => '+221',
                ),
                'rs' => array(
                    'name' => esc_html__('Serbia', 'felan'),
                    'code' => '+381',
                ),
                'sc' => array(
                    'name' => esc_html__('Seychelles', 'felan'),
                    'code' => '+248',
                ),
                'sl' => array(
                    'name' => esc_html__('Sierra Leone', 'felan'),
                    'code' => '+232',
                ),
                'sg' => array(
                    'name' => esc_html__('Singapore', 'felan'),
                    'code' => '+65',
                ),
                'sx' => array(
                    'name' => esc_html__('Sint Maarten', 'felan'),
                    'code' => '+1721',
                ),
                'sk' => array(
                    'name' => esc_html__('Slovakia', 'felan'),
                    'code' => '+421',
                ),
                'si' => array(
                    'name' => esc_html__('Slovenia', 'felan'),
                    'code' => '+386',
                ),
                'sb' => array(
                    'name' => esc_html__('Solomon Islands', 'felan'),
                    'code' => '+677',
                ),
                'so' => array(
                    'name' => esc_html__('Somalia', 'felan'),
                    'code' => '+252',
                ),
                'za' => array(
                    'name' => esc_html__('South Africa', 'felan'),
                    'code' => '+27',
                ),
                'kr' => array(
                    'name' => esc_html__('South Korea', 'felan'),
                    'code' => '+82',
                ),
                'ss' => array(
                    'name' => esc_html__('South Sudan', 'felan'),
                    'code' => '+211',
                ),
                'es' => array(
                    'name' => esc_html__('Spain', 'felan'),
                    'code' => '+34',
                ),
                'lk' => array(
                    'name' => esc_html__('Sri Lanka', 'felan'),
                    'code' => '+94',
                ),
                'sd' => array(
                    'name' => esc_html__('Sudan', 'felan'),
                    'code' => '+249',
                ),
                'sr' => array(
                    'name' => esc_html__('Suriname', 'felan'),
                    'code' => '+597',
                ),
                'sj' => array(
                    'name' => esc_html__('Svalbard and Jan Mayen', 'felan'),
                    'code' => '+47',
                ),
                'sz' => array(
                    'name' => esc_html__('Swaziland', 'felan'),
                    'code' => '+268',
                ),
                'se' => array(
                    'name' => esc_html__('Sweden', 'felan'),
                    'code' => '+46',
                ),
                'ch' => array(
                    'name' => esc_html__('Switzerland', 'felan'),
                    'code' => '+41',
                ),
                'sy' => array(
                    'name' => esc_html__('Syria', 'felan'),
                    'code' => '+963',
                ),
                'tw' => array(
                    'name' => esc_html__('Taiwan', 'felan'),
                    'code' => '+886',
                ),
                'tj' => array(
                    'name' => esc_html__('Tajikistan', 'felan'),
                    'code' => '+992',
                ),
                'tz' => array(
                    'name' => esc_html__('Tanzania', 'felan'),
                    'code' => '+255',
                ),
                'th' => array(
                    'name' => esc_html__('Thailand', 'felan'),
                    'code' => '+66',
                ),
                'tl' => array(
                    'name' => esc_html__('Timor-Leste', 'felan'),
                    'code' => '+670',
                ),
                'tg' => array(
                    'name' => esc_html__('Togo', 'felan'),
                    'code' => '+228',
                ),
                'tk' => array(
                    'name' => esc_html__('Tokelau', 'felan'),
                    'code' => '+690',
                ),
                'tk' => array(
                    'name' => esc_html__('Tokelau', 'felan'),
                    'code' => '+690',
                ),
                'to' => array(
                    'name' => esc_html__('Tonga', 'felan'),
                    'code' => '+676',
                ),
                'tt' => array(
                    'name' => esc_html__('Trinidad and Tobago', 'felan'),
                    'code' => '+1868',
                ),
                'tn' => array(
                    'name' => esc_html__('Tunisia', 'felan'),
                    'code' => '+216',
                ),
                'tr' => array(
                    'name' => esc_html__('Turkey', 'felan'),
                    'code' => '+90',
                ),
                'tm' => array(
                    'name' => esc_html__('Turkmenistan', 'felan'),
                    'code' => '+993',
                ),
                'tc' => array(
                    'name' => esc_html__('Turks and Caicos Islands', 'felan'),
                    'code' => '+1649',
                ),
                'tv' => array(
                    'name' => esc_html__('Tuvalu', 'felan'),
                    'code' => '+688',
                ),
                'ug' => array(
                    'name' => esc_html__('Uganda', 'felan'),
                    'code' => '+256',
                ),
                'ua' => array(
                    'name' => esc_html__('Ukraine', 'felan'),
                    'code' => '+380',
                ),
                'ae' => array(
                    'name' => esc_html__('United Arab Emirates', 'felan'),
                    'code' => '+971',
                ),
                'gb' => array(
                    'name' => esc_html__('United Kingdom', 'felan'),
                    'code' => '+44',
                ),
                'us' => array(
                    'name' => esc_html__('United States', 'felan'),
                    'code' => '+1',
                ),
                'uy' => array(
                    'name' => esc_html__('Uruguay', 'felan'),
                    'code' => '+598',
                ),
                'uz' => array(
                    'name' => esc_html__('Uzbekistan', 'felan'),
                    'code' => '+998',
                ),
                'vu' => array(
                    'name' => esc_html__('Vanuatu', 'felan'),
                    'code' => '+678',
                ),
                'va' => array(
                    'name' => esc_html__('Vatican City', 'felan'),
                    'code' => '+39',
                ),
                've' => array(
                    'name' => esc_html__('Venezuela', 'felan'),
                    'code' => '+58',
                ),
                'vn' => array(
                    'name' => esc_html__('Vietnam', 'felan'),
                    'code' => '+84',
                ),
                'wf' => array(
                    'name' => esc_html__('Wallis and Futuna', 'felan'),
                    'code' => '+681',
                ),
                'eh' => array(
                    'name' => esc_html__('Western Sahara', 'felan'),
                    'code' => '+212',
                ),
                'ye' => array(
                    'name' => esc_html__('Yemen', 'felan'),
                    'code' => '+967',
                ),
                'zm' => array(
                    'name' => esc_html__('Zambia', 'felan'),
                    'code' => '+260',
                ),
                'zw' => array(
                    'name' => esc_html__('Zimbabwe', 'felan'),
                    'code' => '+263',
                ),
            );
        }
    }
}
