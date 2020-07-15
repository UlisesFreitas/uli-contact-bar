<?php
/**
 *
 * @wordpress-plugin
 * Plugin Name:       Uli Contact Bar
 * Plugin URI:        https://www.ulisesfreitas.com/uli-contact-bar
 * Description:       Let your website visitors get in touch with you easily with permanent seen contact informations
 * Version:           1.0.0
 * Author:            Ulises Freitas
 * Author URI:        https://www.ulisesfreitas.com/
 * Text Domain:       uli-contact-bar
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */


?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<div class="th_wrapper">
		<div id="th_main">
			<div class="th_content">

				<form method="post" action="options.php">
<?php
settings_fields( $this->settings_fields_slug );
do_settings_sections( $this->main_options_page_slug );
submit_button();
?>
				</form>

			</div><!-- .th_content -->
		</div><!-- #th_main -->

	</div><!-- .th_wrapper -->
</div><!-- .wrap -->
