<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $freelancer_data;
$freelancer_award_list = get_post_meta($freelancer_data->ID, FELAN_METABOX_PREFIX . 'freelancer_award_list', false);
$freelancer_award_list = !empty($freelancer_award_list) ? $freelancer_award_list[0] : '';
$freelancer_award_quantity = !empty($freelancer_award_list) ? count($freelancer_award_list) : '';
$date_format = get_option('date_format');
?>

<div id="tab-awards" class="tab-info">

    <div class="awards-info block-from">
        <h5><?php esc_html_e('Awards', 'felan-framework') ?></h5>

        <div class="sub-head"><?php esc_html_e('We recommend at least one award entry', 'felan-framework') ?></div>

        <div class="felan-freelancer-warpper">
            <?php if (!empty($freelancer_award_list)) :
                foreach ($freelancer_award_list as $index => $freelancer_award) : ?>
                    <div class="row">
                        <div class="group-title col-md-12">
                            <i class="delete-group fas fa-times"></i>
                            <h6 class="project">
                                <?php esc_html_e('Award', 'felan-framework') ?>
                                <span><?php echo $index + 1 ?></span>
                            </h6>
                            <i class="far fa-angle-up"></i>
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php esc_html_e('Title', 'felan-framework') ?></label>
                            <input type="text" name="freelancer_award_title[]" placeholder="<?php esc_attr_e('Name of award', 'felan-framework'); ?>" value="<?php echo esc_attr($freelancer_award[FELAN_METABOX_PREFIX . 'freelancer_award_title']) ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php esc_html_e('Date awarded', 'felan-framework') ?></label>
                            <input type="text" class="datepicker" placeholder="<?php echo $date_format; ?>" name="freelancer_award_date[]" value="<?php echo esc_attr($freelancer_award[FELAN_METABOX_PREFIX . 'freelancer_award_date']) ?>">
                        </div>
                        <div class="form-group col-md-12">
                            <label><?php esc_html_e('Description', 'felan-framework') ?></label>
                            <textarea name="freelancer_award_description[]" cols="30" rows="7" placeholder="<?php esc_attr_e('Short description', 'felan-framework'); ?>"><?php echo esc_attr($freelancer_award[FELAN_METABOX_PREFIX . 'freelancer_award_description']) ?></textarea>
                        </div>
                    </div>
            <?php endforeach;
            endif;
            ?>

            <button type="button" class="btn-more profile-fields"><i class="far fa-angle-down"></i><?php esc_html_e('Add another award', 'felan-framework') ?></button>

            <template id="template-item-award" data-size="<?php echo esc_attr($freelancer_award_quantity) ?>">
                <div class="row">
                    <div class="group-title col-md-12">
                        <i class="delete-group fas fa-times"></i>
                        <h6 class="project">
                            <?php esc_html_e('Award', 'felan-framework') ?>
                            <span></span>
                        </h6>
                        <i class="far fa-angle-up"></i>
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php esc_html_e('Title', 'felan-framework') ?></label>
                        <input type="text" name="freelancer_award_title[]" placeholder="<?php esc_attr_e('Name of award', 'felan-framework'); ?>" value="">
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php esc_html_e('Date awarded', 'felan-framework') ?></label>
                        <input type="text" class="datepicker" placeholder="<?php echo $date_format; ?>" name="freelancer_award_date[]" value="">
                    </div>
                    <div class="form-group col-md-12">
                        <label><?php esc_html_e('Description', 'felan-framework') ?></label>
                        <textarea name="freelancer_award_description[]" cols="30" rows="7" placeholder="<?php esc_attr_e('Short description', 'felan-framework'); ?>"></textarea>
                    </div>
                </div>
            </template>
        </div>
    </div>
    <?php felan_custom_field_freelancer('awards'); ?>
</div>