<?php

/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 */

?>

<?php
$copyright_text   = Felan_Helper::get_setting('footer_copyright_text');
?>
<div class="copyright">
	<div class="container">
		<div class="area-copyright">
			<div class="copyright-text"><?php echo esc_html($copyright_text); ?></div>
		</div>
	</div>
</div>