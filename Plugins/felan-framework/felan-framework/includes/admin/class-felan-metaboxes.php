<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Felan_Metaboxes')) {
    /**
     * Class Felan_Metaboxes
     */
    class Felan_Metaboxes
    {
        /**
         * Meta boxes setup
         */
        public function meta_boxes_setup()
        {
            global $typenow;

            if ($typenow == 'user_package') {
                add_action('add_meta_boxes', array($this, 'render_user_package_meta_boxes'));
            }

            if ($typenow == 'invoice') {
                add_action('add_meta_boxes', array($this, 'render_invoice_meta_boxes'));
                add_action('save_post', array($this, 'save_invoices_metaboxes'), 10, 2);
            }

            if ($typenow == 'post') {
                add_action('save_post', array($this, 'save_post_metaboxes'), 10, 2);
            }
        }

        /**
         * Render agent package meta boxes
         */
        public function render_user_package_meta_boxes()
        {
            add_meta_box(
                FELAN_METABOX_PREFIX . 'user_package_metaboxes',
                esc_html__('Package Details', 'felan-framework'),
                array($this, 'user_package_meta'),
                array('user_package'),
                'normal',
                'default'
            );
        }


        /**
         * Agent package meta
         * @param $object
         */
        public function user_package_meta($object)
        {
            $postID = $object->ID;
            $package_user_id = get_post_meta($postID, FELAN_METABOX_PREFIX . 'package_user_id', true);
            $package_id = get_user_meta($package_user_id, FELAN_METABOX_PREFIX . 'package_id', true);
            $package_number_job = get_user_meta($package_user_id, FELAN_METABOX_PREFIX . 'package_number_job', true);
            $package_number_featured = get_user_meta($package_user_id, FELAN_METABOX_PREFIX . 'package_number_featured', true);
            $package_activate_date = get_user_meta($package_user_id, FELAN_METABOX_PREFIX . 'package_activate_date', true);
            $package_name = get_the_title($package_id);
            $user_info = get_userdata($package_user_id);
            $felan_package = new Felan_Package();
            $get_expired_date = $felan_package->get_expired_date($package_id, $package_user_id);
?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label><?php esc_html_e('Buyer:', 'felan-framework'); ?></label></th>
                        <td><strong><?php if ($user_info) echo esc_attr($user_info->display_name); ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e('Package:', 'felan-framework'); ?></label></th>
                        <td><strong><?php echo esc_attr($package_name); ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e('Number Listings:', 'felan-framework'); ?></label>
                        </th>
                        <td><strong><?php echo esc_attr($package_number_job); ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label><?php esc_html_e('Number Featured Listings:', 'felan-framework'); ?></label>
                        </th>
                        <td><strong><?php echo esc_attr($package_number_featured); ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e('Activate Date:', 'felan-framework'); ?></label></th>
                        <td><strong><?php echo esc_attr($package_activate_date); ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e('Expires Date:', 'felan-framework'); ?></label></th>
                        <td><strong><?php echo esc_attr($get_expired_date); ?></strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php
        }

        /**
         * Verified Listing
         * @param $object
         */
        public function verified_listing($object)
        {
            wp_nonce_field(plugin_basename(__FILE__), 'felan_verified_listing_nonce_field');
            $verified_listing = get_post_meta($object->ID, FELAN_METABOX_PREFIX . 'verified_listing', true);
        ?>
            <div class="felan_meta_control custom_sidebar_js">
                <?php
                if ($verified_listing == 0 || $verified_listing == '') {
                    echo '<span class="label felan-label-red notice inline notice-warning notice-alt">' . esc_html__('Not verify', 'felan-framework') . '</span>';
                } else {
                    echo '<span class=" notice inline notice-success notice-alt">' . esc_html__('Verified', 'felan-framework') . '</span>';
                }

                ?>

                <?php if ($verified_listing == 0 || $verified_listing == '') { ?>
                    <div class="felan-set-item-paid">
                        <input type="checkbox" id="felan[felan_verified_listing]" name="felan[felan_verified_listing]" value="0" />
                        <label class="" for="felan[felan_verified_listing]"><?php esc_html_e('Tick the checkbox to mark it as Verified', 'felan-framework'); ?></label>

                    </div>
                <?php } ?>
            </div>
        <?php
        }

        /**
         * Render invoice meta boxes
         */
        public function render_invoice_meta_boxes()
        {
            add_meta_box(
                FELAN_METABOX_PREFIX . 'invoice_metaboxes',
                esc_html__('Invoice Details', 'felan-framework'),
                array($this, 'invoice_meta'),
                array('invoice'),
                'normal',
                'default'
            );

            add_meta_box(
                FELAN_METABOX_PREFIX . 'invoice_payment_status',
                esc_html__('Payment Status', 'felan-framework'),
                array($this, 'invoice_payment_status'),
                array('invoice'),
                'side',
                'high'
            );
        }

        /**
         * Invoice meta
         * @param $object
         */
        public function invoice_meta($object)
        {
            $felan_invoice = new Felan_Invoice();
            $felan_meta = $felan_invoice->get_invoice_meta($object->ID);
        ?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><?php esc_html_e('Invoice ID:', 'felan-framework'); ?></th>
                        <td><strong><?php echo intval($object->ID); ?></strong></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Payment Method:', 'felan-framework'); ?></th>
                        <td>
                            <strong>
                                <?php echo Felan_Invoice::get_invoice_payment_method($felan_meta['invoice_payment_method']); ?>
                            </strong>
                        </td>
                    </tr>
                    <?php if (($felan_meta['invoice_payment_method'] == 'Stripe') || ($felan_meta['invoice_payment_method'] == 'Paypal')) : ?>
                        <tr>
                            <th scope="row"><?php esc_html_e('PaymentID (PayPal,Stripe):', 'felan-framework'); ?></th>
                            <td>
                                <strong>
                                    <?php echo esc_attr($felan_meta['trans_payment_id']); ?>
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e('PayerID (PayPal,Stripe):', 'felan-framework'); ?></th>
                            <td>
                                <strong>
                                    <?php echo esc_attr($felan_meta['trans_payer_id']); ?>
                                </strong>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <th scope="row"><?php esc_html_e('Payment Type:', 'felan-framework'); ?></th>
                        <td>
                            <strong><?php echo Felan_Invoice::get_invoice_payment_type($felan_meta['invoice_payment_type']); ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php
                            if ($felan_meta['invoice_payment_type'] == 'Package') {
                                esc_html_e('Package ID:', 'felan-framework');
                            } else {
                                esc_html_e('Place ID:', 'felan-framework');
                            }
                            ?>
                        </th>
                        <td>
                            <strong><?php echo esc_attr($felan_meta['invoice_item_id']); ?></strong>
                            <?php
                            if ($felan_meta['invoice_payment_type'] == 'Package') {
                            ?>
                                <a href="<?php echo get_edit_post_link($felan_meta['invoice_item_id']) ?>"><?php esc_html_e('(Edit)', 'felan-framework'); ?></a>
                                <?php
                            } else {
                                if (current_user_can('read_place', $felan_meta['invoice_item_id'])) {
                                ?>
                                    <a href="<?php echo get_permalink($felan_meta['invoice_item_id']) ?>"><?php esc_html_e('(View)', 'felan-framework'); ?></a>
                                <?php
                                }
                                if (current_user_can('edit_place', $felan_meta['invoice_item_id'])) {
                                ?>
                                    <a href="<?php echo get_edit_post_link($felan_meta['invoice_item_id']) ?>"><?php esc_html_e('(Edit)', 'felan-framework'); ?></a>
                            <?php
                                }
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Item Price:', 'felan-framework'); ?></th>
                        <td>
                            <strong>
                                <?php
                                $item_price = felan_get_format_money($felan_meta['invoice_item_price']);
                                echo esc_attr($item_price);
                                ?>
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Purchase Date:', 'felan-framework'); ?>
                        </th>
                        <td>
                            <strong><?php echo esc_attr($felan_meta['invoice_purchase_date']); ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Buyer Name:', 'felan-framework'); ?></th>
                        <td>
                            <strong>
                                <?php
                                $user_info = get_userdata($felan_meta['invoice_user_id']);
                                if (current_user_can('edit_users') && $user_info) {
                                    echo '<a href="' . get_edit_user_link($felan_meta['invoice_user_id']) . '">' . esc_attr($user_info->display_name) . '</a>';
                                } else {
                                    if ($user_info) echo esc_attr($user_info->display_name);
                                }
                                ?>
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Buyer Email:', 'felan-framework'); ?></th>
                        <td>
                            <strong>
                                <?php if ($user_info) echo esc_attr($user_info->user_email); ?>
                            </strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php
        }

        /**
         * Invoice payment status
         * @param $object
         */
        public function invoice_payment_status($object)
        {
            wp_nonce_field(plugin_basename(__FILE__), 'felan_invoice_nonce_field');
            $payment_status = get_post_meta($object->ID, FELAN_METABOX_PREFIX . 'invoice_payment_status', true);
        ?>
            <div class="felan_meta_control custom_sidebar_js">
                <?php
                if ($payment_status == 0) {
                    echo '<span class="label felan-label-red notice inline notice-warning notice-alt">' . esc_html__('Pending', 'felan-framework') . '</span>';
                } else {
                    echo '<span class="label felan-label-blue notice inline notice-success notice-alt">' . esc_html__('Active', 'felan-framework') . '</span>';
                }
                if ($payment_status == 0) {
                ?>
                    <div class="felan-set-item-paid">
                        <input type="checkbox" id="felan[felan_payment_status]" name="felan[felan_payment_status]" value="0" />
                        <label class="label felan-label-blue" for="felan[felan_payment_status]"><?php esc_html_e('Set item active', 'felan-framework'); ?></label>
                    </div>
                <?php } ?>
            </div>
<?php
        }

        /**
         * Save property metaboxes
         * @param $post_id
         * @return bool
         */
        public function save_post_metaboxes($post_id)
        {
            if (!is_admin()) return false;
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
                return false;
            if (isset($_POST['felan']['felan_post_city'])) {
                $post_city = felan_clean(wp_unslash($_POST['felan']['felan_post_city']));
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'post_city', $post_city);
            }
            return true;
        }

        /**
         * Save property metaboxes
         * @param $post_id
         * @param $post
         * @return bool
         */
        public function save_claim_listing_metaboxes($post_id, $post)
        {
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
                return false;
            if (!isset($_POST['felan_verified_listing_nonce_field']) || !wp_verify_nonce($_POST['felan_verified_listing_nonce_field'], plugin_basename(__FILE__))) {
                return false;
            }
            if ($post->post_type == 'place' && isset($_POST['felan'])) {
                if (isset($_POST['felan']['felan_verified_listing'])) {
                    update_post_meta($post_id, FELAN_METABOX_PREFIX . 'verified_listing', 1);
                }
            }

            return true;
        }

        /**
         * Save invoices metaboxes
         * @param $post_id
         * @param $post
         * @return bool
         */
        public function save_invoices_metaboxes($post_id, $post)
        {
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
                return false;
            if (!isset($_POST['felan_invoice_nonce_field']) || !wp_verify_nonce($_POST['felan_invoice_nonce_field'], plugin_basename(__FILE__))) {
                return false;
            }
            if ($post->post_type == 'invoice' && isset($_POST['felan'])) {
                $post_type = get_post_type_object($post->post_type);
                if (!current_user_can($post_type->cap->edit_post, $post_id))
                    return false;
                if (isset($_POST['felan']['felan_payment_status'])) {
                    $felan_invoice = new Felan_Invoice();
                    $felan_meta = $felan_invoice->get_invoice_meta($post_id);
                    $user_id = $felan_meta['invoice_user_id'];
                    $user = get_user_by('id', $user_id);
                    $user_email = $user->user_email;
                    if ($felan_meta['invoice_payment_type'] == 'Package') {
                        $package_id = $felan_meta['invoice_item_id'];
                        $felan_package = new Felan_Package();
                        $felan_package->insert_user_package($user_id, $package_id);
                        update_post_meta($post_id, FELAN_METABOX_PREFIX . 'invoice_payment_status', 1);
                        $args = array();
                        felan_send_email($user_email, 'mail_activated_package', $args);
                    } else {
                        $jobs_id = $felan_meta['invoice_item_id'];
                        if ($felan_meta['invoice_payment_type'] == 'Listing') {
                            update_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'payment_status', 'paid');
                            wp_update_post(array(
                                'ID' => $jobs_id,
                                'post_status' => 'publish',
                                'post_date' => current_time('mysql'),
                                'post_date_gmt' => current_time('mysql'),
                            ));
                            felan_send_email($user_email, 'mail_activated_listing');
                        } else if ($felan_meta['invoice_payment_type'] == 'Upgrade_To_Featured') {
                            update_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_featured', 1);
                        } else if ($felan_meta['invoice_payment_type'] == 'Listing_With_Featured') {
                            update_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'payment_status', 'paid');
                            update_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_featured', 1);
                            wp_update_post(array(
                                'ID' => $jobs_id,
                                'post_status' => 'publish',
                                'post_date' => current_time('mysql'),
                                'post_date_gmt' => current_time('mysql'),
                            ));
                            felan_send_email($user_email, 'mail_activated_listing');
                        }
                        update_post_meta($post_id, FELAN_METABOX_PREFIX . 'invoice_payment_status', 1);
                    }

                    $felan_admin_invoice = new Felan_Admin_Invoice();
                    $product_package = $felan_admin_invoice->get_product_by_name(get_the_title($package_id));
                    if ($product_package) {
                        wp_delete_post($product_package->ID);
                    }
                }
            }
            return true;
        }
    }
}
