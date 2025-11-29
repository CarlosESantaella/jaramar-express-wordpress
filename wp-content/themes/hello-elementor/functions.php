<?php

/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

define('HELLO_ELEMENTOR_VERSION', '2.8.1');

if (!isset($content_width)) {
	$content_width = 800; // Pixels.
}

if (!function_exists('hello_elementor_setup')) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup()
	{
		if (is_admin()) {
			hello_maybe_update_theme_version_in_db();
		}

		if (apply_filters('hello_elementor_register_menus', true)) {
			register_nav_menus(['menu-1' => esc_html__('Header', 'hello-elementor')]);
			register_nav_menus(['menu-2' => esc_html__('Footer', 'hello-elementor')]);
		}

		if (apply_filters('hello_elementor_post_type_support', true)) {
			add_post_type_support('page', 'excerpt');
		}

		if (apply_filters('hello_elementor_add_theme_support', true)) {
			add_theme_support('post-thumbnails');
			add_theme_support('automatic-feed-links');
			add_theme_support('title-tag');
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
					'script',
					'style',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);

			/*
			 * Editor Style.
			 */
			add_editor_style('classic-editor.css');

			/*
			 * Gutenberg wide images.
			 */
			add_theme_support('align-wide');

			/*
			 * WooCommerce.
			 */
			if (apply_filters('hello_elementor_add_woocommerce_support', true)) {
				// WooCommerce in general.
				add_theme_support('woocommerce');
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support('wc-product-gallery-zoom');
				// lightbox.
				add_theme_support('wc-product-gallery-lightbox');
				// swipe.
				add_theme_support('wc-product-gallery-slider');
			}
		}
	}
}
add_action('after_setup_theme', 'hello_elementor_setup');

function hello_maybe_update_theme_version_in_db()
{
	$theme_version_option_name = 'hello_theme_version';
	// The theme version saved in the database.
	$hello_theme_db_version = get_option($theme_version_option_name);

	// If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if (!$hello_theme_db_version || version_compare($hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<')) {
		update_option($theme_version_option_name, HELLO_ELEMENTOR_VERSION);
	}
}

if (!function_exists('hello_elementor_scripts_styles')) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles()
	{
		$min_suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		if (apply_filters('hello_elementor_enqueue_style', true)) {
			wp_enqueue_style(
				'hello-elementor',
				get_template_directory_uri() . '/style' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if (apply_filters('hello_elementor_enqueue_theme_style', true)) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				get_template_directory_uri() . '/theme' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		wp_enqueue_script(
			'main',
			get_template_directory_uri() . '/assets/js/main.js',
			array('jquery'),
			HELLO_ELEMENTOR_VERSION,
			true
		);
		wp_enqueue_style(
			'main',
			get_template_directory_uri() . '/style.css',
			array(),
			HELLO_ELEMENTOR_VERSION
		);
	}
}
add_action('wp_enqueue_scripts', 'hello_elementor_scripts_styles');

if (!function_exists('hello_elementor_register_elementor_locations')) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations($elementor_theme_manager)
	{
		if (apply_filters('hello_elementor_register_elementor_locations', true)) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action('elementor/theme/register_locations', 'hello_elementor_register_elementor_locations');

if (!function_exists('hello_elementor_content_width')) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width()
	{
		$GLOBALS['content_width'] = apply_filters('hello_elementor_content_width', 800);
	}
}
add_action('after_setup_theme', 'hello_elementor_content_width', 0);

if (is_admin()) {
	require get_template_directory() . '/includes/admin-functions.php';
}

/**
 * If Elementor is installed and active, we can load the Elementor-specific Settings & Features
 */

// Allow active/inactive via the Experiments
require get_template_directory() . '/includes/elementor-functions.php';

/**
 * Include customizer registration functions
 */
function hello_register_customizer_functions()
{
	if (is_customize_preview()) {
		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action('init', 'hello_register_customizer_functions');

if (!function_exists('hello_elementor_check_hide_title')) {
	/**
	 * Check hide title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title($val)
	{
		if (defined('ELEMENTOR_VERSION')) {
			$current_doc = Elementor\Plugin::instance()->documents->get(get_the_ID());
			if ($current_doc && 'yes' === $current_doc->get_settings('hide_title')) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter('hello_elementor_page_title', 'hello_elementor_check_hide_title');

if (!function_exists('hello_elementor_add_description_meta_tag')) {
	/**
	 * Add description meta tag with excerpt text.
	 *
	 * @return void
	 */
	function hello_elementor_add_description_meta_tag()
	{
		$post = get_queried_object();

		if (is_singular() && !empty($post->post_excerpt)) {
			echo '<meta name="description" content="' . esc_attr(wp_strip_all_tags($post->post_excerpt)) . '">' . "\n";
		}
	}
}
add_action('wp_head', 'hello_elementor_add_description_meta_tag');

/**
 * BC:
 * In v2.7.0 the theme removed the `hello_elementor_body_open()` from `header.php` replacing it with `wp_body_open()`.
 * The following code prevents fatal errors in child themes that still use this function.
 */
if (!function_exists('hello_elementor_body_open')) {
	function hello_elementor_body_open()
	{
		wp_body_open();
	}
}


add_theme_support('json-data');

function add_fonts_modify_controls($controls_registry)
{
	$fonts = $controls_registry->get_control('font')->get_settings('options');
	$misfuentes = array_merge(['gotham' => 'system'], $fonts);
	$controls_registry->get_control('font')->set_settings('options', $misfuentes);
}
add_action('elementor/controls/controls_registered', 'add_fonts_modify_controls', 50, 1);

// Función para inyectar colores y fuentes correctas de Jaramar
function jaramar_inject_custom_colors() {
	$theme_url = get_template_directory_uri();
	?>
	<style id="jaramar-color-override">
		/* Fuente Gotham - Jaramar Express */
		@font-face {
			font-family: 'gotham';
			src: url('<?php echo $theme_url; ?>/assets/fonts/GothamLight.ttf') format('truetype');
			font-weight: 300;
			font-style: normal;
			font-display: swap;
		}
		@font-face {
			font-family: 'gotham';
			src: url('<?php echo $theme_url; ?>/assets/fonts/GothamMedium.ttf') format('truetype');
			font-weight: 400;
			font-style: normal;
			font-display: swap;
		}
		@font-face {
			font-family: 'gotham';
			src: url('<?php echo $theme_url; ?>/assets/fonts/GothamMedium.ttf') format('truetype');
			font-weight: 500;
			font-style: normal;
			font-display: swap;
		}
		@font-face {
			font-family: 'gotham';
			src: url('<?php echo $theme_url; ?>/assets/fonts/GothamBold.ttf') format('truetype');
			font-weight: 700;
			font-style: normal;
			font-display: swap;
		}

		/* Colores globales de Jaramar Express */
		.elementor-kit-6 {
			--e-global-color-primary: #FF5100 !important;
			--e-global-color-secondary: #001B71 !important;
			--e-global-color-text: #323232 !important;
			--e-global-color-accent: #FFFFFF !important;
			--e-global-color-96a45ad: #F5F5F5 !important;
			--e-global-typography-primary-font-family: "gotham" !important;
			--e-global-typography-primary-font-weight: 700 !important;
			--e-global-typography-secondary-font-family: "gotham" !important;
			--e-global-typography-secondary-font-weight: 400 !important;
			--e-global-typography-text-font-family: "gotham" !important;
			--e-global-typography-text-font-weight: 400 !important;
			--e-global-typography-accent-font-family: "gotham" !important;
			--e-global-typography-accent-font-weight: 500 !important;
		}

		/* Aplicar fuente Gotham globalmente */
		body {
			font-family: 'gotham', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
		}

		/* Fix para menú de navegación - solo header */
		.elementor-location-header .hfe-nav-menu .hfe-menu-item {
			color: #FFFFFF !important;
			text-decoration: none !important;
			font-family: 'gotham', sans-serif !important;
			font-weight: 500 !important;
			font-size: 16px !important;
			opacity: 1 !important;
			visibility: visible !important;
		}

		.elementor-location-header .hfe-nav-menu .hfe-menu-item:hover {
			color: #FF5100 !important;
		}

		.elementor-location-header .hfe-nav-menu .current-menu-item .hfe-menu-item {
			color: #FF5100 !important;
		}

		/* Asegurar que el menú sea visible */
		.elementor-location-header .hfe-nav-menu ul {
			list-style: none !important;
			opacity: 1 !important;
			visibility: visible !important;
		}

		/* Footer - mantener estilos originales */
		.elementor-location-footer .hfe-nav-menu .hfe-menu-item {
			opacity: 1 !important;
			visibility: visible !important;
		}
	</style>
	<?php
}
add_action('wp_head', 'jaramar_inject_custom_colors', 999);
