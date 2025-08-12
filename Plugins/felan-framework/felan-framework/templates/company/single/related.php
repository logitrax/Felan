<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'company-related');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'company-related',
    'felan_company_related_vars',
    array(
        'ajax_url'    => FELAN_AJAX_URL,
    )
);

$company_id = get_the_ID();
if (!empty($company_single_id)) {
    $company_id = $company_single_id;
}

$enable_single_company_related = felan_get_option('enable_single_company_related', '1');
$posts_per_page = 4;

$args = array(
    'post_type'      => 'jobs',
    'post_status'    => 'publish',
    'posts_per_page' => $posts_per_page,
);

$current_post = get_post($company_id);
if ($current_post) {
    $args['meta_key']     = FELAN_METABOX_PREFIX . 'jobs_select_company';
    $args['meta_value']   = $company_id;
    $args['meta_compare'] = '=';
}

$related = get_posts($args);
?>

<?php if ($enable_single_company_related && !empty($related)) : ?>
    <div class="block-archive-inner company-related-details">
        <h4 class="title-company"><?php esc_html_e('Job at ', 'felan-framework') ?><?php echo get_the_title( $company_id ); ?></h4>
        <div class="related-inner">
            <div class="related-company">
                <?php foreach ($related as $relateds) : ?>
                    <?php felan_get_template('content-jobs.php', array(
                        'jobs_id'     => $relateds->ID,
                        'jobs_layout' => 'layout-list',
                    )); ?>
                <?php endforeach; ?>
            </div>
            <div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>
        </div>
        <?php
        $total_post = count($related);
        $max_num_pages = ceil($total_post / $posts_per_page);
        if ($total_post > $posts_per_page) {
            felan_get_template('global/pagination.php', array('max_num_pages' => $max_num_pages, 'total_post' => $total_post, 'layout' => 'number'));
        }
        ?>
        <input type="hidden" name="item_amount" value="<?php echo esc_attr($posts_per_page) ?>">
        <input type="hidden" name="company_id" value="<?php echo esc_attr($company_id) ?>">
    </div>
<?php endif; ?>