<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$user_id = $current_user->ID;
$posts_per_page = 10;

$args = array(
    'post_type' => 'wallet_order',
    'ignore_sticky_posts' => 1,
    'posts_per_page' => $posts_per_page,
    'post_status'  => 'publish',
    'offset' => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
    'author' => $user_id,
);

$data = new WP_Query($args);
?>
<div class="search-dashboard-warpper">
    <div class="search-left">
        <div class="select2-field" style="min-width: 180px">
            <select class="search-control felan-select2" name="wallet_method">
                <option value=""><?php esc_html_e('Payout Method', 'felan-framework') ?></option>
                <option value="wire-transfer"><?php esc_html_e('Wire Transfer', 'felan-framework') ?></option>
                <option value="stripe"><?php esc_html_e('Stripe', 'felan-framework') ?></option>
                <option value="paypal"><?php esc_html_e('Paypal', 'felan-framework') ?></option>
                <option value="razor"><?php esc_html_e('Razor', 'felan-framework') ?></option>
                <option value="woocommerce"><?php esc_html_e('Woocommerce', 'felan-framework') ?></option>
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
        <table class="table-dashboard" id="wallet-topup">
            <thead>
            <tr>
                <th><?php esc_html_e('ID', 'felan-framework') ?></th>
                <th><?php esc_html_e('Payout Method', 'felan-framework') ?></th>
                <th><?php esc_html_e('Status', 'felan-framework') ?></th>
                <th><?php esc_html_e('Amount', 'felan-framework') ?></th>
                <th><?php esc_html_e('Date', 'felan-framework') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php while ($data->have_posts()) : $data->the_post(); ?>
                <?php
                $wallet_id = get_the_ID();
                $payment_method = get_post_meta($wallet_id, FELAN_METABOX_PREFIX . 'wallet_order_payment_method', true);
                $payment_method = str_replace(['-', '_'], ' ', $payment_method);
                $status = get_post_meta($wallet_id, FELAN_METABOX_PREFIX . 'wallet_order_payment_status', true);
                $price = get_post_meta($wallet_id, FELAN_METABOX_PREFIX . 'wallet_order_price', true);
                $currency_sign_default = felan_get_option('currency_sign_default');                        $currency_position = felan_get_option('currency_position');
                $currency_position = felan_get_option('currency_position');
                $date =  get_the_date(get_option('date_format'));
                if ($currency_position == 'before') {
                    $price_order = $currency_sign_default . $price;
                } else {
                    $price_order = $price . $currency_sign_default;
                }
                ?>
                <tr>
                    <td>
                        <?php echo '#' . esc_html($wallet_id); ?>
                    </td>
                    <td>
                        <?php echo esc_html($payment_method); ?>
                    </td>
                    <td>
                        <?php if ($status == 'pending') : ?>
                            <span class="label label-pending"><?php esc_html_e('Pending', 'felan-framework') ?></span>
                        <?php elseif ($status == 'approve') : ?>
                            <span class="label label-open"><?php esc_html_e('Completed', 'felan-framework') ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="price">
                        <?php echo esc_html($price_order); ?>
                    </td>
                    <td>
                        <?php echo esc_html($date); ?>
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