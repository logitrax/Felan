<div class="container">
	<div class="row">

		<div class="left-header">
			<?php echo Felan_Templates::canvas_menu(); ?>
			<?php echo Felan_Templates::style_logo(); ?>

			<div class="d-none d-xl-block">
				<?php echo Felan_Templates::main_menu(); ?>
			</div>
		</div>

		<div class="right-header">
			<?php echo Felan_Templates::notification(); ?>

			<div class="d-none d-xl-block">
				<?php echo Felan_Templates::account(); ?>
			</div>

			<div class="d-xl-none">
				<?php echo Felan_Templates::search_icon('icon', true); ?>
			</div>

			<div class="d-none d-xl-block">
				<?php echo Felan_Templates::add_project(); ?>
				<?php echo Felan_Templates::render_button(); ?>
			</div>
		</div>

	</div>
</div><!-- .container -->
<?php echo Felan_Templates::header_categories(); ?>