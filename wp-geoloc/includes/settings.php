<?php
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

// create custom plugin settings menu
add_action('admin_menu', 'wpgeoloc_create_menu');

function wpgeoloc_create_menu() {
  //create new top-level menu
  add_menu_page('WP Geoloc settings', 'WP Geoloc', 'administrator', __FILE__, 'wpgeoloc_settings_page' , 'dashicons-location' );
  //call register settings function
  add_action( 'admin_init', 'register_wpgeoloc_settings' );
}

function register_wpgeoloc_settings() {
  //register our settings
  register_setting( 'wpgeoloc-settings-group', 'google_api_key' );
}

function wpgeoloc_settings_page() {
  ?>
  <div class="wrap">
  <h2><?php echo esc_html__( 'WP Geoloc settings', 'wp-geoloc' ); ?></h2>
  <form method="post" action="options.php">
    <?php settings_fields( 'wpgeoloc-settings-group' ); ?>
    <?php do_settings_sections( 'wpgeoloc-settings-group' ); ?>
    <table class="form-table">
      <tr valign="top">
      <th scope="row"><?php echo esc_html__( 'Google Place API KEY', 'wp-geoloc' ); ?></th>
      <td>
        <input type="text" name="google_api_key" value="<?php echo esc_attr( get_option('google_api_key') ); ?>" />
      </td>
      </tr>
    </table>
    <?php submit_button(); ?>
  </form>
  </div>
  <?php
}

?>
