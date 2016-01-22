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

add_action('admin_head-post-new.php', 'wpgeoloc_load_google_js');
add_action('admin_head-post.php', 'wpgeoloc_load_google_js');

function wpgeoloc_load_google_js() {
  echo '<script src="https://maps.googleapis.com/maps/api/js?language='. esc_attr(substr(get_locale(), 0, 2)).'&key='.esc_attr( get_option('google_api_key') ).'&signed_in=true&libraries=places&callback=initAutocomplete" async defer></script>
        <script>
          function initAutocomplete() {
            autocomplete = new google.maps.places.Autocomplete(
              (document.getElementById("autocomplete")),
                {types: ["geocode"]});
            autocomplete.addListener("place_changed", fillInAddress);
          }
          function fillInAddress() {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
              jQuery( "#autocomplete_message" ).addClass( "error" );
            }else{
              console.log(place);
              document.getElementById("formatted_address").value = place.formatted_address;
              document.getElementById("latitude").value = place.geometry.location.lat();
              document.getElementById("longitude").value = place.geometry.location.lng();
              jQuery( "#autocomplete_message" ).removeClass( "error" );
            }
          }
        </script>
      ';
}

add_action('admin_menu', 'wpgeoloc_add_custom_box');

function wpgeoloc_add_custom_box() {
  $types = array( 'post', 'videogallery' );
  foreach( $types as $type ) {
    add_meta_box('geolocation_sectionid', esc_html__( 'Geotag', 'wpgeoloc' ), 'wpgeoloc_custom_box', $type, 'advanced' );
  }
}

function wpgeoloc_custom_box() {
  echo '<input type="hidden" id="geolocation_nonce" name="geolocation_nonce" value="' . wp_create_nonce(plugin_basename(__FILE__) ) . '" />
        <div id="locationField">
          <span id="autocomplete_message">'.esc_html__("Please select an address from search results", "wpgeoloc").'</span>
          <input id="autocomplete" placeholder="'.esc_html__("Type an address here", "wpgeoloc").'" type="text" style="width:100%;" value="'.esc_attr(get_post_meta(get_the_ID(), 'autocomplete',true)).'"></input>
          <input type="hidden" id="formatted_address" name="autocomplete" value="'.esc_attr(get_post_meta(get_the_ID(), 'autocomplete',true)).'">
          <input class="field" id="latitude" name="latitude" placeholder="'.esc_html__("Latitude", "wpgeoloc").'" style="width:100%;" value="'.esc_attr(get_post_meta(get_the_ID(), 'latitude',true)).'"></input>
          <input class="field" id="longitude" name="longitude" placeholder="'.esc_html__("Longitude", "wpgeoloc").'" style="width:100%;" value="'.esc_attr(get_post_meta(get_the_ID(), 'longitude',true)).'"></input>
        </div>
        <style>
          #autocomplete_message.error{
            color : #db4a3f;
          }
        </style>
  ';
}

add_action('save_post', 'wpgeoloc_save_postdata');

function wpgeoloc_save_postdata($post_id) {

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    return $post_id;

  if(isset($_POST['post_type']) && 'page' == $_POST['post_type'] ) {

    if(!current_user_can('edit_page', $post_id))
      return $post_id;

  } else {

    if(!current_user_can('edit_post', $post_id))
      return $post_id;

  }

  if(isset($_POST['latitude']) && isset($_POST['longitude']) && isset($_POST['autocomplete'])){
    //Clean GET parameters to avoid XSS and other attacks
    $clean_latitude = preg_replace('/[^-a-zA-Z0-9-.]/', '', $_POST['latitude']);
    $clean_longitude = preg_replace('/[^-a-zA-Z0-9-.]/', '', $_POST['longitude']);
    $clean_autocomplete = sanitize_text_field($_POST['autocomplete']);
    //Update Post Meta
    update_post_meta($post_id, 'latitude', $clean_latitude);
    update_post_meta($post_id, 'longitude', $clean_longitude);
    update_post_meta($post_id, 'autocomplete', $clean_autocomplete);
    update_post_meta($post_id, 'wpgeoloc_coords', $clean_latitude.",".$clean_longitude);
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
    //Clean GET parameters to avoid XSS and other attacks
    $clean_latitude = preg_replace('/[^-a-zA-Z0-9-.]/', '', $_GET['latitude']);
    $clean_longitude = preg_replace('/[^-a-zA-Z0-9-.]/', '', $_GET['longitude']);
    $clean_distance = preg_replace('/[^-a-zA-Z0-9-.]/', '', $_GET['distance']);
    $clean_distance = intval( $clean_distance );
    if ( ! $clean_distance ) {
      $clean_distance = 100;
    }
    // Just get the ID's of posts in range
    $ids = (array) $sc_gds->getPostIDsOfInRange( "post", $clean_distance, $clean_latitude, $clean_longitude );
    // We we have no results then set an array just one that will trigger no posts found.
    if( empty( $ids ) )
     $wp_query->set( 'post__in', array(0) );
    else
     $wp_query->set( 'post__in', $ids );
     $wp_query->set( 'orderby', 'post__in' );
  }
}

add_action( 'wp_enqueue_scripts', 'wpgeoloc_enqueue_scripts' );

function wpgeoloc_enqueue_scripts(){
	wp_enqueue_style( 'wpgeoloc', plugins_url( '../css/wpgeoloc.css' , __FILE__ ) );
  wp_enqueue_script(
		'rangeslider',
    plugins_url( '../js/rangeslider.min.js' , __FILE__ ),
		array( 'jquery' )
	);
}


?>
