<?php
/**
 * Corporate Pro
 *
 * This file adds helper functions used in the Corporate Pro Theme.
 *
 * @package   SEOThemes\CorporatePro
 * @link      https://seothemes.com/themes/corporate-pro
 * @author    SEO Themes
 * @copyright Copyright © 2019 SEO Themes
 * @license   GPL-3.0-or-later
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Returns the default logo size in pixels.
 *
 * @since  1.0.0
 *
 * @return string Hex value.
 */
function corporate_logo_size() {
	return 150;
}

/**
 * Returns the default gradient one color.
 *
 * @since  1.0.0
 *
 * @return string Hex value.
 */
function corporate_gradient_one_color() {
	return '#00c6ff';
}

/**
 * Returns the default gradient two color.
 *
 * @since  1.0.0
 *
 * @return string Hex value.
 */
function corporate_gradient_two_color() {
	return '#0072ff';
}

/**
 * Returns the default overlay color.
 *
 * @since  1.0.0
 *
 * @return string Hex value.
 */
function corporate_overlay_color() {
	return 'rgba(42,49,57,0.5)';
}

/**
 * Custom header image callback.
 *
 * Loads custom header or featured image depending on what is set on a per
 * page basis. If a featured image is set for a page, it will override
 * the default header image. It also gets the image for custom post
 * types by looking for a page with the same slug as the CPT e.g
 * the Portfolio CPT archive will pull the featured image from
 * a page with the slug of 'portfolio', if the page exists.
 *
 * @since  1.1.0
 *
 * @return string
 */
function corporate_custom_header() {
	$id  = '';
	$url = '';

	if ( class_exists( 'WooCommerce' ) && is_shop() ) {
		$id = wc_get_page_id( 'shop' );

	} elseif ( is_post_type_archive() ) {
		$id = get_page_by_path( get_query_var( 'post_type' ) );
		$id = $id->ID && has_post_thumbnail( $id->ID ) ? $id->ID : false;

	} elseif ( is_category() ) {
		$id = get_page_by_title( 'category-' . get_query_var( 'category_name' ), OBJECT, 'attachment' );

	} elseif ( is_tag() ) {
		$id = get_page_by_title( 'tag-' . get_query_var( 'tag' ), OBJECT, 'attachment' );

	} elseif ( is_tax() ) {
		$id = get_page_by_title( 'term-' . get_query_var( 'term' ), OBJECT, 'attachment' );

	} elseif ( is_front_page() ) {
		$id = get_option( 'page_on_front' );

	} elseif ( 'posts' === get_option( 'show_on_front' ) && is_home() ) {
		$id = get_option( 'page_for_posts' );

	} elseif ( is_search() ) {
		$id = get_page_by_path( 'search' );

	} elseif ( is_404() ) {
		$id = get_page_by_path( 'error' );

	} elseif ( is_singular() ) {
		$id = get_the_id();
	}

	if ( is_object( $id ) ) {
		$url = wp_get_attachment_image_url( $id->ID, 'hero' );

	} elseif ( $id ) {
		$url = get_the_post_thumbnail_url( $id, 'hero' );
	}

	if ( ! $url ) {
		$url = get_header_image();
	}

	if ( $url ) {
		$selector = get_theme_support( 'custom-header', 'header-selector' );

		return printf( '<style id="hero-section" type="text/css">' . esc_attr( $selector ) . '{background-image:url(%s)}</style>' . "\n", esc_url( $url ) );

	} else {
		return '';
	}
}

/**
 * Sanitize number values.
 *
 * Ensure number is an absolute integer (whole number, zero or greater). If
 * input is an absolute integer, return it. Otherwise, return default.
 *
 * @since  2.0.0
 *
 * @param  string $number  The rgba color to sanitize.
 * @param string  $setting Sanitized value.
 *
 * @return string
 */
function corporate_sanitize_number( $number, $setting ) {
	$number = absint( $number );

	return ( $number ? $number : $setting->default );
}

/**
 * Sanitize RGBA values.
 *
 * If string does not start with 'rgba', then treat as hex then
 * sanitize the hex color and finally convert hex to rgba.
 *
 * @since  1.0.0
 *
 * @param  string $color The rgba color to sanitize.
 *
 * @return string
 */
function corporate_sanitize_rgba( $color ) {

	// Return invisible if empty.
	if ( empty( $color ) || is_array( $color ) ) {
		return 'rgba(0,0,0,0)';
	}

	// Return sanitized hex if not rgba value.
	if ( false === strpos( $color, 'rgba' ) ) {
		return sanitize_hex_color( $color );
	}

	// Finally, sanitize and return rgba.
	$color = str_replace( ' ', '', $color );
	sscanf( $color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );

	return 'rgba(' . $red . ',' . $green . ',' . $blue . ',' . $alpha . ')';
}

/**
 * Minify CSS helper function.
 *
 * @since  1.0.0
 *
 * @author Gary Jones
 * @link   https://github.com/GaryJones/Simple-PHP-CSS-Minification
 *
 * @param  string $css The CSS to minify.
 *
 * @return string
 */
function corporate_minify_css( $css ) {

	// Normalize whitespace.
	$css = preg_replace( '/\s+/', ' ', $css );

	// Remove spaces before and after comment.
	$css = preg_replace( '/(\s+)(\/\*(.*?)\*\/)(\s+)/', '$2', $css );

	// Remove comment blocks, everything between /* and */, unless preserved with /*! ... */ or /** ... */.
	$css = preg_replace( '~/\*(?![\!|\*])(.*?)\*/~', '', $css );

	// Remove ; before }.
	$css = preg_replace( '/;(?=\s*})/', '', $css );

	// Remove space after , : ; { } */ >.
	$css = preg_replace( '/(,|:|;|\{|}|\*\/|>) /', '$1', $css );

	// Remove space before , ; { } ( ) >.
	$css = preg_replace( '/ (,|;|\{|}|\(|\)|>)/', '$1', $css );

	// Strips leading 0 on decimal values (converts 0.5px into .5px).
	$css = preg_replace( '/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $css );

	// Strips units if value is 0 (converts 0px to 0).
	$css = preg_replace( '/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}0', $css );

	// Converts all zeros value into short-hand.
	$css = preg_replace( '/0 0 0 0/', '0', $css );

	// Shorten 6-character hex color codes to 3-character where possible.
	$css = preg_replace( '/#([a-f0-9])\\1([a-f0-9])\\2([a-f0-9])\\3/i', '#\1\2\3', $css );

	return trim( $css );
}

/**
 * Helper function to check if we're on a WooCommerce page.
 *
 * This function is used to check whether or not to output the
 * WooCommerce CSS in the corporate_scripts_styles function.
 * Since it's a relatively large file, we don't want it to
 * load on unnecessary pages where it's not required.
 *
 * @since  1.0.0
 *
 * @link   https://docs.woocommerce.com/document/conditional-tags/.
 *
 * @return bool
 */
function corporate_is_woocommerce_page() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return false;
	}

	if ( is_woocommerce() || is_shop() || is_product_category() || is_product_tag() || is_product() || is_cart() || is_checkout() || is_account_page() ) {
		return true;
	}

	return false;
}

/**
 * Convert hex to rgba value.
 *
 * This function takes a hex code (e.g. #eeeeee) and returns array of RGBA values.
 * Used in the corporate_customizer_output function to handle transparency.
 *
 * @since  1.0.0
 *
 * @param  string $colour  Hex color to convert.
 * @param  int    $opacity Opacity amount.
 *
 * @return string
 */
function corporate_hex_to_rgba( $colour, $opacity ) {
	if ( '#' === $colour[0] ) {
		$colour = substr( $colour, 1 );
	}

	if ( strlen( $colour ) === 6 ) {
		list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
	} elseif ( strlen( $colour ) === 3 ) {
		list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
	} else {
		return false;
	}

	$r = hexdec( $r );
	$g = hexdec( $g );
	$b = hexdec( $b );

	$rgb = array(
		'red'   => $r,
		'green' => $g,
		'blue'  => $b,
	);

	$rgba = implode( $rgb, ',' ) . ',' . $opacity;

	return $rgba;
}

/**
 * Check if Front Page 1 contains slider widget.
 *
 * @since  1.0.1
 *
 * @uses   $sidebars_widgets
 *
 * @param  string $sidebar Name of sidebar, e.g `primary`.
 * @param  string $widget  Widget ID to check, e.g `custom_html`.
 *
 * @return bool
 */
function corporate_sidebar_has_widget( $sidebar, $widget ) {
	global $sidebars_widgets;

	if ( isset( $sidebars_widgets[ $sidebar ][0] ) && strpos( $sidebars_widgets[ $sidebar ][0], $widget ) !== false && is_active_sidebar( $sidebar ) ) {
		return true;
	}

	return false;
}
