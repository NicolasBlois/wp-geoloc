<?php
/**
 * WP Geoloc for Wordpress
 *
 * @package   wp-geoloc
 * @author    Nicolas Blois <nicolas.blois@gmail.com>
 * @license   GPL-2.0+
 * @link      https://wordpress.org/plugins/wp-geoloc/
 * @copyright 2016 WP Geoloc
 *
 * @wordpress-plugin
 * Plugin Name:       WP Geoloc
 * Plugin URI:        https://wordpress.org/plugins/wp-geoloc/
 * Description:       This plugin adds geolocation functionnality for your articles. It allows you to request for articles based on latitude, longitude and distance provided as GET parameters in URL.
 * Version:           1.0.0
 * Author:            Nicolas Blois
 * Author URI:        https://wordpress.org/plugins/wp-geoloc/
 * Text Domain:       wpgeoloc
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */
 /*  Copyright 2016  Nicolas Blois  (email : nicolas.blois@gmail.com)

     This program is free software; you can redistribute it and/or modify
     it under the terms of the GNU General Public License as published by
     the Free Software Foundation; either version 2 of the License, or
     (at your option) any later version.

     This program is distributed in the hope that it will be useful,
     but WITHOUT ANY WARRANTY; without even the implied warranty of
     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     GNU General Public License for more details.

     You should have received a copy of the GNU General Public License
     along with this program; if not, write to the Free Software
     Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

! defined( 'ABSPATH' ) and exit;

include('includes/geo-data-store.php');
include('includes/functions.php');
include('includes/settings.php');
include('includes/widget-search.php');
include('includes/shortcode-search.php');

add_action('plugins_loaded', 'wpgeoloc_init');

function wpgeoloc_init() {
 load_plugin_textdomain( 'wpgeoloc', false, dirname(plugin_basename(__FILE__)).'/languages/' );
}
