<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
global $jobs_data, $jobs_meta_data, $hide_jobs_fields;
?>
<div class="row">
	<div class="form-group col-md-6">
		<label><?php esc_html_e('Show pay by', 'felan-framework'); ?></label>
		<div class="select2-field">
			<select id="select-salary-pay" name="jobs_salary_show" class="felan-select2">
				<option <?php if (isset($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_salary_show'][0])) {
							if ($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_salary_show'][0] == "range") {
								echo 'selected';
							}
						} ?> value="range"><?php esc_html_e('Range', 'felan-framework'); ?></option>
				<option <?php if (isset($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_salary_show'][0])) {
							if ($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_salary_show'][0] == "starting_amount") {
								echo 'selected';
							}
						} ?> value="starting_amount"><?php esc_html_e('Starting amount', 'felan-framework'); ?></option>
				<option <?php if (isset($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_salary_show'][0])) {
							if ($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_salary_show'][0] == "maximum_amount") {
								echo 'selected';
							}
						} ?> value="maximum_amount"><?php esc_html_e('Maximum amount', 'felan-framework'); ?></option>
				<option <?php if (isset($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_salary_show'][0])) {
							if ($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_salary_show'][0] == "agree") {
								echo 'selected';
							}
						} ?> value="agree"><?php esc_html_e('Negotiable Price', 'felan-framework'); ?></option>
			</select>
		</div>
	</div>
	<div class="form-group col-md-6">
		<label><?php esc_html_e('Currency', 'felan-framework'); ?></label>
		<div class="select2-field">
			<select name="jobs_currency_type" class="felan-select2">
				<?php felan_get_select_currency_type(true); ?>
			</select>
		</div>
	</div>
	<div class="felan-section-salary-select" id="range">
		<div class="form-group col-md-6">
			<label for="jobs_salary_minimum"><?php esc_html_e('Minimum', 'felan-framework'); ?></label>
			<input type="number" id="jobs_salary_minimum" name="jobs_salary_minimum" value="<?php if (isset($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_salary_minimum'][0])) {
																								echo $jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_salary_minimum'][0];
																							} ?>">
		</div>
		<div class="form-group col-md-6">
			<label for="jobs_salary_maximum"><?php esc_html_e('Maximum', 'felan-framework'); ?></label>
			<input type="number" id="jobs_salary_maximum" name="jobs_salary_maximum" value="<?php if (isset($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_salary_maximum'][0])) {
																								echo $jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_salary_maximum'][0];
																							} ?>">
		</div>
	</div>
	<div class="felan-section-salary-select col-md-6" id="starting_amount">
		<label for="jobs_minimum_price"><?php esc_html_e('Minimum', 'felan-framework'); ?></label>
		<input type="number" id="jobs_minimum_price" name="jobs_minimum_price" value="<?php if (isset($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_minimum_price'][0])) {
																							echo $jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_minimum_price'][0];
																						} ?>">
	</div>
	<div class="felan-section-salary-select col-md-6" id="maximum_amount">
		<label for="jobs_maximum_price"><?php esc_html_e('Maximum', 'felan-framework'); ?></label>
		<input type="number" id="jobs_maximum_price" name="jobs_maximum_price" value="<?php if (isset($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_maximum_price'][0])) {
																							echo $jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_maximum_price'][0];
																						} ?>">
	</div>
	<div class="form-group col-md-6">
		<label><?php esc_html_e('Rate', 'felan-framework'); ?></label>
		<div class="select2-field">
			<select name="jobs_salary_rate" class="felan-select2">
				<option value=""><?php esc_html_e('None', 'felan-framework'); ?></option>
				<option <?php if (isset($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_salary_rate'][0])) {
							if ($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_salary_rate'][0] == "hour") {
								echo 'selected';
							}
						} ?> value="hour"><?php esc_html_e('Per Hour', 'felan-framework'); ?></option>
				<option <?php if (isset($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_salary_rate'][0])) {
							if ($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_salary_rate'][0] == "day") {
								echo 'selected';
							}
						} ?> value="day"><?php esc_html_e('Per Day', 'felan-framework'); ?></option>
				<option <?php if (isset($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_salary_rate'][0])) {
							if ($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_salary_rate'][0] == "week") {
								echo 'selected';
							}
						} ?> value="week"><?php esc_html_e('Per Week', 'felan-framework'); ?></option>
				<option <?php if (isset($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_salary_rate'][0])) {
							if ($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_salary_rate'][0] == "month") {
								echo 'selected';
							}
						} ?> value="month"><?php esc_html_e('Per Month', 'felan-framework'); ?></option>
				<option <?php if (isset($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_salary_rate'][0])) {
							if ($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_salary_rate'][0] == "year") {
								echo 'selected';
							}
						} ?> value="year"><?php esc_html_e('Per Year', 'felan-framework'); ?></option>
			</select>
		</div>
	</div>
	<div class="form-group col-md-6 hidden">
		<label for="jobs_rate_convert_min"><?php esc_html_e('Maximum', 'felan-framework'); ?></label>
		<input type="number" id="jobs_rate_convert_min" name="jobs_rate_convert_min" value="<?php if (isset($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_maximum_price'][0])) {
																								echo $jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_maximum_price'][0];
																							} ?>">
	</div>
</div>