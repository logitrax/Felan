<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'freelancer-project-disputes');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'freelancer-project-disputes',
    'felan_project_disputes_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
        'not_disputes' => esc_html__('No disputes found', 'felan-framework'),
    )
);

global $current_user;
$user_id = $current_user->ID;
$posts_per_page = 10;
$args = array(
    'post_type' => 'project_disputes',
    'ignore_sticky_posts' => 1,
    'posts_per_page' => $posts_per_page,
    'offset' => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
    'meta_query' => array(
        array(
            'key' => FELAN_METABOX_PREFIX . 'project_disputes_freelancer_id',
            'value' => $user_id,
            'compare' => '='
        ),
    ),
);
$data = new WP_Query($args);
?>
 <div class="felan-project-disputes entry-my-page">
    <div class="search-dashboard-warpper">
        <div class="search-left">
            <div class="select2-field">
                <select class="search-control felan-select2" name="disputes_status">
                    <option value=""><?php esc_html_e('All status', 'felan-framework') ?></option>
                    <option value="open"><?php esc_html_e('Open', 'felan-framework') ?></option>
                    <option value="close"><?php esc_html_e('Closed', 'felan-framework') ?></option>
                    <option value="refund"><?php esc_html_e('Refunded', 'felan-framework') ?></option>
                </select>
            </div>
            <div class="action-search">
                <input class="disputes-search-control" type="text" name="disputes_search" placeholder="<?php esc_attr_e('Search disputes title', 'felan-framework') ?>">
                <button class="btn-search">
                    <i class="far fa-search"></i>
                </button>
            </div>
        </div>
        <div class="search-right">
            <label class="text-sorting"><?php esc_html_e('Sort by', 'felan-framework') ?></label>
            <div class="select2-field">
                <select class="search-control action-sorting felan-select2" name="disputes_sort_by">
                    <option value="newest"><?php esc_html_e('Newest', 'felan-framework') ?></option>
                    <option value="oldest"><?php esc_html_e('Oldest', 'felan-framework') ?></option>
                </select>
            </div>
        </div>
    </div>
    <?php if ($data->have_posts()) { ?>
        <div class="table-dashboard-wapper">
            <table class="table-dashboard" id="disputes">
                <thead>
                <tr>
                    <th><?php esc_html_e('Service Name', 'felan-framework') ?></th>
                    <th><?php esc_html_e('Date', 'felan-framework') ?></th>
                    <th><?php esc_html_e('Price', 'felan-framework') ?></th>
                    <th><?php esc_html_e('Employer', 'felan-framework') ?></th>
                    <th><?php esc_html_e('Status', 'felan-framework') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php while ($data->have_posts()) : $data->the_post(); ?>
                    <?php
                    $disputes_id = get_the_ID();
                    $order_id = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_project_order_id', true);
                    $project_id = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_project_id', true);
                    $public_date = get_the_date(get_option('date_format'));
                    $thumbnail = get_the_post_thumbnail_url($project_id, '70x70');
                    $author_id = get_post_field('post_author', $disputes_id);
                    $author_name = get_the_author_meta('display_name', $author_id);

                    $project_featured = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_featured', true);
                    $status = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'project_disputes_status', true);
                    $price_order = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'project_disputes_price', true);
                    $currency_sign_default = felan_get_option('currency_sign_default');
                    $currency_position = felan_get_option('currency_position');
                    if ($currency_position == 'before') {
                        $price_order = $currency_sign_default . $price_order;
                    } else {
                        $price_order = $price_order . $currency_sign_default;
                    }
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
                                                <span class="tooltip featured" data-title="<?php esc_attr_e('Featured', 'felan-framework') ?>">
                                                        <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-featured.svg'); ?>" alt="<?php echo esc_attr('featured', 'felan-framework') ?>">
                                                    </span>
                                            <?php endif; ?>
                                        </a>
                                    </h3>
                                </div>
                            </div>
                        </td>
                        <td class="start-time">
                            <span class="time"><?php echo $public_date; ?></span>
                        </td>
                        <td class="price">
                            <?php echo $price_order; ?>
                        </td>
                        <td class="author">
                            <?php echo esc_html($author_name); ?>
                        </td>
                        <td class="status">
                            <?php if($status == 'close') : ?>
                                <span class="label label-close tooltip"><?php esc_html_e('Closed', 'felan-framework') ?></span>
                            <?php elseif ($status == 'refund') : ?>
                                <span class="label label-open tooltip"><?php esc_html_e('Refunded', 'felan-framework') ?></span>
                            <?php else : ?>
                                <span class="label label-inprogress tooltip"><?php esc_html_e('Open', 'felan-framework') ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo esc_url(felan_get_permalink('freelancer_disputes')); ?>?listing=project&order_id=<?php echo esc_attr($order_id) ?>&disputes_id=<?php echo esc_attr($disputes_id) ?>"
                               class="project-detail felan-button"><?php echo esc_html__('Detail', 'felan-framework') ?></a>
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