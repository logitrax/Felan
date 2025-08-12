<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$felan_freelancer_payment = new Felan_freelancer_payment();
$freelancer_payment_method = isset($_GET['payment_method']) ? absint(wp_unslash($_GET['payment_method'])) : -1;
if ($freelancer_payment_method == 1) {
    $felan_freelancer_payment->paypal_payment_completed();
} elseif ($freelancer_payment_method == 2) {
    $felan_freelancer_payment->stripe_payment_completed();
} elseif ($freelancer_payment_method == 4) {
    $felan_freelancer_payment->razor_payment_completed();
}
?>
<div class="felan-payment-completed-wrap">
    <div class="inner-payment-completed">
        <?php
        do_action('felan_before_freelancer_payment_completed');
        if (isset($_GET['order_id']) && $_GET['order_id'] != '') :
            $order_id = absint(wp_unslash($_GET['order_id']));
            $felan_freelancer_order = new Felan_freelancer_order();
            $order_meta = $felan_freelancer_order->get_freelancer_order_meta($order_id);
            $service_wire_transfer_card_number = felan_get_option('service_wire_transfer_card_number', '');
            $service_wire_transfer_card_name = felan_get_option('service_wire_transfer_card_name', '');
            $service_wire_transfer_bank_name = felan_get_option('service_wire_transfer_bank_name', '');
        ?>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2><?php esc_html_e('Thank you for your purchase!', 'felan-framework'); ?></h2>
                </div>
                <p><?php esc_html_e('Please transfer to our account number with the "Order Number" and wait for us to confirm.', 'felan-framework'); ?></p>

                <?php if ($service_wire_transfer_card_number || $service_wire_transfer_card_name || $service_wire_transfer_bank_name) : ?>
                    <div class="card-info">
                        <table>
                            <tr>
                                <th><?php esc_html_e('Card Number', 'felan-framework'); ?></th>
                                <td><?php esc_html_e($service_wire_transfer_card_number); ?></td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('Card Name', 'felan-framework'); ?></th>
                                <td><?php esc_html_e($service_wire_transfer_card_name); ?></td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('Bank Name', 'felan-framework'); ?></th>
                                <td><?php esc_html_e($service_wire_transfer_bank_name); ?></td>
                            </tr>
                        </table>
                    </div>
                <?php endif; ?>

                <div class="entry-title">
                    <h3><?php esc_html_e('Order Detail', 'felan-framework'); ?></h3>
                </div>
                <ul class="list-group">
                    <li class="list-group-item">
                        <span><?php esc_html_e('Order Number', 'felan-framework'); ?></span>
                        <strong class="pull-right"><?php esc_html_e($order_id); ?></strong>
                    </li>
                    <li class="list-group-item">
                        <span><?php esc_html_e('Date', 'felan-framework'); ?></span>
                        <strong class="pull-right"><?php echo get_the_date('', $order_id); ?></strong>
                    </li>
                    <li class="list-group-item">
                        <span><?php esc_html_e('Payment Method', 'felan-framework'); ?></span>
                        <strong class="pull-right">
                            <?php echo Felan_freelancer_order::get_freelancer_order_payment_method($order_meta['freelancer_order_payment_method']);  ?>
                        </strong>
                    </li>
                    <li class="list-group-item">
                        <span><?php esc_html_e('Total', 'felan-framework'); ?></span>
                        <strong class="pull-right"><?php echo felan_get_format_money($order_meta['freelancer_order_item_price']); ?></strong>
                    </li>
                </ul>
            </div>
            <a href="<?php echo felan_get_permalink('freelancer_dashboard'); ?>" class="felan-button"><?php esc_html_e('Go to Dashboard', 'felan-framework'); ?></a>
        <?php else : ?>
            <div class="felan-heading">
                <h2><?php esc_html_e('Thank you for your purchase', 'felan-framework'); ?></h2>
            </div>
            <div class="felan-thankyou-content">
                <?php esc_html_e('Payment Successful! Your package has been successfully updated.', 'felan-framework'); ?>
            </div>
            <a href="<?php echo felan_get_permalink('freelancer_dashboard'); ?>" class="felan-button"> <?php esc_html_e('Go to Dashboard', 'felan-framework'); ?> </a>
        <?php endif;
        do_action('felan_after_freelancer_payment_completed');
        ?>
    </div>
</div>