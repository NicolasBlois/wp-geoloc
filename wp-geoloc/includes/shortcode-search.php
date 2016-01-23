<?php
/**
 * WP Geoloc Search shortcode
 * WP Geoloc plugin
 */
 ! defined( 'ABSPATH' ) and exit;

add_shortcode( 'wpgeoloc', 'wpgeoloc_search_shortcode' );

function wpgeoloc_search_shortcode(){

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
}

?>
