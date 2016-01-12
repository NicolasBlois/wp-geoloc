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

add_action('admin_head-post-new.php', 'wpgeoloc_admin_head');
add_action('admin_head-post.php', 'wpgeoloc_admin_head');

function wpgeoloc_admin_head() {
  echo '<script src="https://maps.googleapis.com/maps/api/js?key='.esc_attr( get_option('google_api_key') ).'&signed_in=true&libraries=places&callback=initAutocomplete" async defer></script>
        <script>
          function initAutocomplete() {
            autocomplete = new google.maps.places.Autocomplete(
              (document.getElementById("autocomplete")),
                {types: ["geocode"]});
            autocomplete.addListener("place_changed", fillInAddress);
          }

          function fillInAddress() {
            var place = autocomplete.getPlace();
            document.getElementById("latitude").value = place.geometry.location.lat();
            document.getElementById("longitude").value = place.geometry.location.lng();
          }
        </script>
      ';
}

add_action('admin_menu', 'wpgeoloc_add_custom_box');

function wpgeoloc_add_custom_box() {
  $types = array( 'post', 'videogallery' );
  foreach( $types as $type ) {
    add_meta_box('geolocation_sectionid', __( 'Geotag', 'myplugin_textdomain' ), 'wpgeoloc_custom_box', $type, 'advanced' );
  }
}

function wpgeoloc_custom_box() {
	echo '<input type="hidden" id="geolocation_nonce" name="geolocation_nonce" value="' . wp_create_nonce(plugin_basename(__FILE__) ) . '" />
    		<label class="screen-reader-text" for="geolocation-address">Geotag</label>
    		<p>Provide a place for this content</p>
        <div id="locationField">
          <input id="autocomplete" name="autocomplete" placeholder="Google place autocomplete" type="text" style="width:100%;" value="'.get_post_meta(get_the_ID(), 'autocomplete',true).'"></input>
          <input class="field" id="latitude" name="latitude" placeholder="latitude" style="width:100%;" value="'.get_post_meta(get_the_ID(), 'latitude',true).'"></input>
          <input class="field" id="longitude" name="longitude" placeholder="longitude" style="width:100%;" value="'.get_post_meta(get_the_ID(), 'longitude',true).'"></input>
        </div>
	';
}

add_action('save_post', 'wpgeoloc_save_postdata');

function wpgeoloc_save_postdata($post_id) {

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    return $post_id;

  if('page' == $_POST['post_type'] ) {

    if(!current_user_can('edit_page', $post_id))
		return $post_id;

  } else {

    if(!current_user_can('edit_post', $post_id))
		return $post_id;

  }

  if(isset($_POST['latitude']) && isset($_POST['longitude']) && isset($_POST['autocomplete'])){
    update_post_meta($post_id, 'latitude', $_POST['latitude']);
    update_post_meta($post_id, 'longitude', $_POST['longitude']);
  	update_post_meta($post_id, 'autocomplete', $_POST['autocomplete']);
    update_post_meta($post_id, 'wpgeoloc_coords', $_POST['latitude'].",".$_POST['longitude']);
  }

  return $post_id;

}

add_filter( 'sc_geodatastore_meta_keys', 'posts_geodata' );

function posts_geodata( $keys ){
  $keys[] = "wpgeoloc_coords";
  return $keys;
}

add_action('pre_get_posts','wpgeoloc_alter_query');

function wpgeoloc_alter_query( $query ){
  global $wp_query, $sc_gds;
  if(isset($_GET['latitude']) && isset($_GET['longitude']) && isset($_GET['distance'])){
   // Load instance of GeoDataStore
   if ( ! isset( $sc_gds ) )
     $sc_gds = new sc_GeoDataStore();

   // Just get the ID's of posts in range
   $ids = (array) $sc_gds->getPostIDsOfInRange( "post", $_GET['distance'], $_GET['latitude'], $_GET['longitude'] );

   // We we have no results then set an array just one that will trigger no posts found.
   if( empty( $ids ) )
     $wp_query->set( 'post__in', array(1) );
   else
     $wp_query->set( 'post__in', $ids );
  }
}


?>
