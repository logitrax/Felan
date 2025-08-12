<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $freelancer_data;
$freelancer_education_list = get_post_meta($freelancer_data->ID, FELAN_METABOX_PREFIX . 'freelancer_education_list', false);
$freelancer_education_list = !empty($freelancer_education_list) ? $freelancer_education_list[0] : '';
$freelancer_education_quantity = !empty($freelancer_education_list) ?  count($freelancer_education_list) : 1;
$date_format = get_option('date_format');
$text_present = esc_html__('Present', 'felan-framework');
?>

<div id="tab-education" class="tab-info">
    <div class="education-info block-from">
        <h5 class="education"><?php esc_html_e('Education', 'felan-framework') ?></h5>

        <div class="sub-head"><?php esc_html_e('We recommend at least one education entry.', 'felan-framework') ?></div>

        <div class="felan-freelancer-warpper">
            <?php if (!empty($freelancer_education_list)) :
                foreach ($freelancer_education_list as $index => $freelancer_education) :
                    $freelancer_education_to = !empty($freelancer_education[FELAN_METABOX_PREFIX . 'freelancer_education_to']) ? $freelancer_education[FELAN_METABOX_PREFIX . 'freelancer_education_to'] : '';
                    if ($freelancer_education_to == $text_present) {
                        $is_check = 'checked';
                    } else {
                        $is_check = '';
                    }
            ?>
                    <div class="row">
                        <div class="group-title col-md-12">
                            <i class="delete-group fas fa-times"></i>
                            <h6 class="education">
                                <?php echo esc_html_e('Education', 'felan-framework') ?>
                                <span><?php echo $index + 1 ?></span>
                            </h6>
                            <i class="far fa-angle-up"></i>
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php esc_html_e('Title', 'felan-framework') ?></label>
                            <input type="text" name="freelancer_education_title[]" placeholder="<?php esc_attr_e('Enter Title', 'felan-framework'); ?>" value="<?php echo esc_attr($freelancer_education[FELAN_METABOX_PREFIX . 'freelancer_education_title']) ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php esc_html_e('Level of Education', 'felan-framework') ?></label>
                            <input type="text" name="freelancer_education_level[]" placeholder="<?php esc_attr_e('Enter Level', 'felan-framework'); ?>" value="<?php echo esc_attr($freelancer_education[FELAN_METABOX_PREFIX . 'freelancer_education_level']) ?>">
                        </div>
                        <div class="form-group col-md-12">
                            <input <?php echo $is_check; ?> type="checkbox" class="custom-checkbox input-control" name="freelancer_education_check[]" value="present" />
                            <label class="label-present"><?php esc_html_e('Choose at the present time', 'felan-framework') ?></label>
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php esc_html_e('From', 'felan-framework') ?></label>
                            <input type="text" class="datepicker" placeholder="<?php echo $date_format; ?>" name="freelancer_education_from[]" value="<?php echo esc_attr($freelancer_education[FELAN_METABOX_PREFIX . 'freelancer_education_from']) ?>">
                        </div>
                        <div class="form-group col-md-6 present-to">
                            <label><?php esc_html_e('To', 'felan-framework') ?></label>
                            <?php if ($freelancer_education_to == $text_present) {
                                echo '<input disabled class="text-present" type="text" name="freelancer_education_to[]" value="' . esc_attr__('Present', 'felan-framework') . '">';
                            } else {
                                echo '<input type="text" class="datepicker" placeholder="' . $date_format . '" name="freelancer_education_to[]" value="' . $freelancer_education_to . '">';
                            } ?>
                        </div>

                        <div class="form-group col-md-12">
                            <label><?php esc_html_e('Description', 'felan-framework') ?></label>
                            <textarea name="freelancer_education_description[]" cols="30" placeholder="<?php esc_attr_e('Short description', 'felan-framework'); ?>" rows="7"><?php echo esc_attr($freelancer_education[FELAN_METABOX_PREFIX . 'freelancer_education_description']) ?></textarea>
                        </div>
                    </div>
            <?php endforeach;
            endif;
            ?>
            <button type="button" class="btn-more profile-fields"><i class="far fa-angle-down"></i><?php esc_html_e('Add another education', 'felan-framework') ?></button>
            <template id="template-item-education" data-size="<?php echo esc_attr($freelancer_education_quantity) ?>">
                <div class="row">
                    <div class="group-title col-md-12">
                        <i class="delete-group fas fa-times"></i>
                        <h6 class="education">
                            <?php echo esc_html_e('Education', 'felan-framework') ?>
                            <span></span>
                        </h6>
                        <i class="far fa-angle-up"></i>
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php esc_html_e('Title', 'felan-framework') ?></label>
                        <input type="text" name="freelancer_education_title[]" value="" placeholder="<?php esc_attr_e('Enter Title', 'felan-framework'); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php esc_html_e('Level of Education', 'felan-framework') ?></label>
                        <input type="text" name="freelancer_education_level[]" value="" placeholder="<?php esc_attr_e('Enter Level', 'felan-framework'); ?>">
                    </div>
                    <div class="form-group col-md-12">
                        <input type="checkbox" class="custom-checkbox input-control" name="freelancer_education_check[]" value="" />
                        <label class="label-present"><?php esc_html_e('Choose at the present time', 'felan-framework') ?></label>
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php esc_html_e('From', 'felan-framework') ?></label>
                        <input type="text" class="datepicker" placeholder="<?php echo $date_format; ?>" name="freelancer_education_from[]" value="">
                    </div>
                    <div class="form-group col-md-6 present-to">
                        <label><?php esc_html_e('To', 'felan-framework') ?></label>
                        <input type="text" class="datepicker" placeholder="<?php echo $date_format; ?>" name="freelancer_education_to[]" value="">
                    </div>
                    <div class="form-group col-md-12">
                        <label><?php esc_html_e('Description', 'felan-framework') ?></label>
                        <textarea name="freelancer_education_description[]" cols="30" rows="7" placeholder="<?php esc_attr_e('Short description', 'felan-framework'); ?>"></textarea>
                    </div>
                </div>
            </template>
        </div>
    </div>
    <?php felan_custom_field_freelancer('education'); ?>
</div>