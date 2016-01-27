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

add_action( 'widgets_init', 'wpgeoloc_widgets_init' );

function wpgeoloc_widgets_init(){
  register_widget( 'wpgeoloc_search_widget' );
}

class wpgeoloc_search_widget extends WP_Widget{

  function wpgeoloc_search_widget(){

    $widget_ops = array('classname' => 'wpgeoloc-search','description' => esc_html__( "Location-based search" ,'wp-geoloc') );
    parent::__construct('wpgeoloc-search', esc_html__('WP Geoloc search','wp-geoloc'), $widget_ops);

  }

  function widget($args , $instance) {

    extract($args);
    $title = isset($instance['title']) ? $instance['title'] : esc_html__('Location-based search' , 'wp-geoloc');

    echo $before_widget;
    echo $before_title;
    echo $title;
    echo $after_title;

    /**
     * Widget Content
     */

    wpgeoloc_load_google_js();
    if (isset($_GET['latitude']) && isset($_GET['longitude']) && isset($_GET['distance']) && isset($_GET['address'])) {
      //Clean GET parameters
      $latitude = floatval($_GET['latitude']);
      if (!is_numeric($latitude)) {
        $latitude = 0 ;
      }
      $longitude = floatval($_GET['longitude']);
      if (!is_numeric($longitude)) {
        $longitude = 0 ;
      }
      $distance = floatval($_GET['distance']);
      if (!is_numeric($distance)) {
        $distance = 0;
      }
      $address = sanitize_text_field($_GET['address']);
    }
    ?>
    <!-- search form -->
    <form id="wpgeoloc_form" action="<?php echo esc_url(home_url( '/' )); ?>" method="GET">
      <div id="locationField">
        <div class="input-group">
          <input id="autocomplete" placeholder="<?php echo esc_html__('Type an address here', 'wp-geoloc') ?>" type="text" class="form-control search-query" value="<?php echo (isset($address)) ? esc_attr(stripslashes($address)) : ''; ?>">
          <span class="input-group-btn">
            <button type="submit" class="btn btn-default" id="searchsubmit">
              <?php echo esc_html__('Go', 'wp-geoloc') ?>
            </button>
          </span>
        </div>
        <span id="autocomplete_message"><?php echo esc_html__('Please select an address from search results', 'wp-geoloc'); ?></span>
        <input type="hidden" id="s" name="s" value="">
        <input type="hidden" id="formatted_address" name="address" value="<?php echo ($address) ? esc_attr(stripslashes($address)) : ''; ?>">
        <input type="hidden" id="latitude" name="latitude" value="<?php echo (isset($latitude)) ? esc_attr($latitude) : ''; ?>">
        <input type="hidden" id="longitude" name="longitude" value="<?php echo (isset($longitude)) ? esc_attr($longitude) : ''; ?>">
        <div id="range-container">
          <input type="range" name="distance" value="<?php echo (isset($distance)) ? esc_attr($distance) : '100'; ?>">
        </div>
      </div>
    </form>
    <!-- end search form -->
    <style>.rangeslider__ruler:after { content: "<?php echo esc_html__('Distance - km', 'wp-geoloc') ?>"; }</style>
    <script>
      jQuery( "#wpgeoloc_form" ).submit(function( event ) {
        if(jQuery('#wpgeoloc_form #autocomplete').val() == '' || jQuery('#wpgeoloc_form #latitude').val() == '' || jQuery('#wpgeoloc_form #longitude').val() == ''){
          event.preventDefault();
          jQuery( "#wpgeoloc_form #autocomplete_message" ).addClass( "error" );
        }
      });
      var $r = jQuery('input[type="range"]');
      var $ruler = jQuery('<div class="rangeslider__ruler" />');
      var rulerStep = 10;
      // Initialize range slider
      $r.rangeslider({
      polyfill: false,
      onInit: function() {
        $ruler[0].innerHTML = getRulerRange(this.min, this.max, rulerStep);
        this.$range.prepend($ruler);}
      });
      function getRulerRange(min, max, step) {
        var range = '';
        var i = 0;
        while (i <= max) {
          range += i + ' ';
          i = i + step;
        }
        return range;
      }
    </script>
    <?php
    echo $after_widget;
  }

  function form($instance) {
    if (!isset($instance['title'])) {
      $instance['title'] = esc_html__('Location-based search' , 'wp-geoloc');
    }
    ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html__('Title ','wp-geoloc') ?></label>
      <input type="text" value="<?php echo esc_attr($instance['title']); ?>" name="<?php echo $this->get_field_name('title'); ?>" id="<?php $this->get_field_id('title'); ?>" class="widefat" />
    </p>
    <?php
  }

}

?>
