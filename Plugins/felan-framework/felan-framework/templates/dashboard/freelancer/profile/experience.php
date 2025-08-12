<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $freelancer_data;
$freelancer_experience_list = get_post_meta($freelancer_data->ID, FELAN_METABOX_PREFIX . 'freelancer_experience_list', false);
$freelancer_experience_list = !empty($freelancer_experience_list) ? $freelancer_experience_list[0] : '';
$freelancer_experience_quantity = !empty($freelancer_experience_list) ? count($freelancer_experience_list) : '';
$date_format = get_option('date_format');
$text_present = esc_html__('Present', 'felan-framework');
?>

<div id="tab-experience" class="tab-info">
    <div class="experience-info block-from">
        <h5><?php esc_html_e('Experience', 'felan-framework') ?></h5>

        <div class="sub-head"><?php esc_html_e('We recommend at least one experience entry.', 'felan-framework') ?></div>

        <div class="felan-freelancer-warpper">
            <?php
            if (!empty($freelancer_experience_list)) :
                foreach ($freelancer_experience_list as $index => $freelancer_experience) :
                    $freelancer_experience_to = !empty($freelancer_experience[FELAN_METABOX_PREFIX . 'freelancer_experience_to']) ? $freelancer_experience[FELAN_METABOX_PREFIX . 'freelancer_experience_to'] : '';
                    if ($freelancer_experience_to == $text_present) {
                        $is_check = 'checked';
                    } else {
                        $is_check = '';
                    }
            ?>
                    <div class="row">
                        <div class="group-title col-md-12">
                            <i class="delete-group fas fa-times"></i>
                            <h6 class="experience">
                                <?php echo esc_html_e('Experience', 'felan-framework') ?>
                                <span><?php echo $index + 1 ?></span>
                            </h6>
                            <i class="far fa-angle-up"></i>
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php esc_html_e('Job Title', 'felan-framework') ?></label>
                            <input type="text" name="freelancer_experience_job[]" placeholder="<?php esc_attr_e('Enter Job Title', 'felan-framework'); ?>" value="<?php echo esc_attr($freelancer_experience[FELAN_METABOX_PREFIX . 'freelancer_experience_job']) ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php esc_html_e('Company', 'felan-framework') ?></label>
                            <input type="text" name="freelancer_experience_company[]" placeholder="<?php esc_attr_e('Enter Company', 'felan-framework'); ?>" value="<?php echo esc_attr($freelancer_experience[FELAN_METABOX_PREFIX . 'freelancer_experience_company']) ?>">
                        </div>
                        <div class="form-group col-md-12">
                            <input <?php echo $is_check; ?> type="checkbox" class="custom-checkbox input-control" name="freelancer_experience_check[]" value="present" />
                            <label class="label-present"><?php esc_html_e('Choose at the present time', 'felan-framework') ?></label>
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php esc_html_e('From', 'felan-framework') ?></label>
                            <input type="text" class="datepicker" placeholder="<?php echo $date_format; ?>" name="freelancer_experience_from[]" value="<?php echo esc_attr($freelancer_experience[FELAN_METABOX_PREFIX . 'freelancer_experience_from']) ?>">
                        </div>
                        <div class="form-group col-md-6 present-to">
                            <label><?php esc_html_e('To', 'felan-framework') ?></label>
                            <?php if ($freelancer_experience_to == $text_present) {
                                echo '<input disabled class="text-present" type="text" name="freelancer_experience_to[]" value="' . esc_attr__('Present', 'felan-framework') . '">';
                            } else {
                                echo '<input type="text" class="datepicker" placeholder="' . $date_format . '" name="freelancer_experience_to[]" value="' . $freelancer_experience_to . '">';
                            } ?>
                        </div>
                        <div class="form-group col-md-12">
                            <label><?php esc_html_e('Description', 'felan-framework') ?></label>
                            <textarea name="freelancer_experience_description[]" cols="30" placeholder="<?php esc_attr_e('Short description', 'felan-framework'); ?>" rows="7"><?php esc_attr_e($freelancer_experience[FELAN_METABOX_PREFIX . 'freelancer_experience_description']) ?></textarea>
                        </div>
                    </div>
            <?php endforeach;
            endif;
            ?>

            <button type="button" class="btn-more profile-fields"><i class="far fa-angle-down"></i><?php esc_html_e('Add another experience', 'felan-framework') ?>
            </button>

            <template id="template-item-experience" data-size="<?php echo esc_attr($freelancer_experience_quantity) ?>">
                <div class="row">
                    <div class="group-title col-md-12">
                        <i class="delete-group fas fa-times"></i>
                        <h6 class="experience">
                            <?php echo esc_html_e('Experience', 'felan-framework') ?>
                            <span></span>
                        </h6>
                        <i class="far fa-angle-up"></i>
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php esc_html_e('Job Title', 'felan-framework') ?></label>
                        <input type="text" name="freelancer_experience_job[]" value="" placeholder="<?php esc_attr_e('Enter Job Title', 'felan-framework'); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php esc_html_e('Company', 'felan-framework') ?></label>
                        <input type="text" name="freelancer_experience_company[]" value="" placeholder="<?php esc_attr_e('Enter Company', 'felan-framework'); ?>">
                    </div>
                    <div class="form-group col-md-12">
                        <input type="checkbox" class="custom-checkbox input-control" name="freelancer_experience_check[]" value="" />
                        <label class="label-present"><?php esc_html_e('Choose at the present time', 'felan-framework') ?></label>
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php esc_html_e('From', 'felan-framework') ?></label>
                        <input type="text" class="datepicker" placeholder="<?php echo $date_format; ?>" name="freelancer_experience_from[]" value="">
                    </div>
                    <div class="form-group col-md-6 present-to">
                        <label><?php esc_html_e('To', 'felan-framework') ?></label>
                        <input type="text" class="datepicker" placeholder="<?php echo $date_format; ?>" name="freelancer_experience_to[]" value="">
                    </div>
                    <div class="form-group col-md-12">
                        <label><?php esc_html_e('Description', 'felan-framework') ?></label>
                        <textarea name="freelancer_experience_description[]" cols="30" rows="7" placeholder="<?php esc_attr_e('Short description', 'felan-framework'); ?>"></textarea>
                    </div>
                </div>
            </template>
        </div>
    </div>
    <?php felan_custom_field_freelancer('experience'); ?>
</div>