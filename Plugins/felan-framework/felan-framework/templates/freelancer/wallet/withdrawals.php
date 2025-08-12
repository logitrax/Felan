<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$user_id = $current_user->ID;
$posts_per_page = 10;

$args = array(
    'post_type' => 'freelancer_withdraw',
    'ignore_sticky_posts' => 1,
    'posts_per_page' => $posts_per_page,
    'post_status'  => 'publish',
    'offset' => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
    'meta_query' => array(
        array(
            'key' => FELAN_METABOX_PREFIX . 'freelancer_withdraw_user_id',
            'value' => $user_id,
            'compare' => '==',
        )
    ),
);

$data = new WP_Query($args);
?>
<div class="search-dashboard-warpper">
    <div class="search-left">
        <div class="select2-field">
            <select class="search-control felan-select2" name="wallet_method">
                <option value=""><?php esc_html_e('Payout Method', 'felan-framework') ?></option>
                <option value="wire transfer"><?php esc_html_e('Wire Transfer', 'felan-framework') ?></option>
                <option value="stripe"><?php esc_html_e('Pay With Stripe', 'felan-framework') ?></option>
                <option value="paypal"><?php esc_html_e('Pay With Paypal', 'felan-framework') ?></option>
            </select>
        </div>
        <div class="select2-field">
            <select class="search-control felan-select2" name="wallet_status">
                <option value=""><?php esc_html_e('All status', 'felan-framework') ?></option>
                <option value="pending"><?php esc_html_e('Pending', 'felan-framework') ?></option>
                <option value="completed"><?php esc_html_e('Completed', 'felan-framework') ?></option>
                <option value="canceled"><?php esc_html_e('Canceled', 'felan-framework') ?></option>
            </select>
        </div>
    </div>
    <div class="search-right">
        <label class="text-sorting"><?php esc_html_e('Sort by', 'felan-framework') ?></label>
        <div class="select2-field">
            <select class="search-control action-sorting felan-select2" name="wallet_sort_by">
                <option value="newest"><?php esc_html_e('Newest', 'felan-framework') ?></option>
                <option value="oldest"><?php esc_html_e('Oldest', 'felan-framework') ?></option>
            </select>
        </div>
    </div>
</div>
<?php if ($data->have_posts()) { ?>
    <div class="table-dashboard-wapper">
        <table class="table-dashboard" id="my-wallet">
            <thead>
            <tr>
                <th><?php esc_html_e('Payout Method', 'felan-framework') ?></th>
                <th><?php esc_html_e('Status', 'felan-framework') ?></th>
                <th><?php esc_html_e('Amount', 'felan-framework') ?></th>
                <th><?php esc_html_e('Request Date', 'felan-framework') ?></th>
                <th><?php esc_html_e('Process Date', 'felan-framework') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php while ($data->have_posts()) : $data->the_post(); ?>
                <?php
                $withdraw_id = get_the_ID();
                $payment_method = get_post_meta($withdraw_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_payment_method', true);
                $payment_method = str_replace(['-', '_'], ' ', $payment_method);
                $price = get_post_meta($withdraw_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_price', true);
                $status = get_post_meta($withdraw_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_status', true);
                $request_date =  get_the_date(get_option('date_format'));
                $process_date = get_post_meta($withdraw_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_process_date', true);
                if (empty($process_date)) {
                    $process_date = '...';
                } else {
                    $process_date = felan_convert_date_format($process_date);
                }
                $currency_position = felan_get_option('currency_position');
                $currency_sign_default = felan_get_option('currency_sign_default');
                if ($currency_position == 'before') {
                    $price = $currency_sign_default . $price;
                } else {
                    $price = $price . $currency_sign_default;
                }
                ?>
                <tr>
                    <td>
                        <?php echo $payment_method; ?>
                    </td>
                    <td>
                        <?php if ($status == 'pending') : ?>
                            <span class="label label-pending"><?php esc_html_e('Pending', 'felan-framework') ?></span>
                        <?php elseif ($status == 'canceled') : ?>
                            <span class="label label-close"><?php esc_html_e('Canceled', 'felan-framework') ?></span>
                        <?php elseif ($status == 'completed') : ?>
                            <span class="label label-open"><?php esc_html_e('Completed', 'felan-framework') ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="price">
                        <?php echo $price; ?>
                    </td>
                    <td>
                        <?php echo $request_date; ?>
                    </td>
                    <td>
                        <?php echo $process_date; ?>
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