<?php
/**
 * WP Geoloc Search shortcode
 * WP Geoloc plugin
 */
add_shortcode( 'wpgeoloc', 'wpgeoloc_search_shortcode' );

function wpgeoloc_search_shortcode(){

  wpgeoloc_load_google_js();
  //Clean GET parameters to avoid XSS and other attacks
  if(isset($_GET['address'])){
    $clean_address = sanitize_text_field($_GET['address']);
  }
  if(isset($_GET['latitude'])){
    $clean_latitude = preg_replace('/[^-a-zA-Z0-9-.]/', '', $_GET['latitude']);
  }
  if(isset($_GET['longitude'])){
    $clean_longitude = preg_replace('/[^-a-zA-Z0-9-.]/', '', $_GET['longitude']);
  }
  if(isset($_GET['distance'])){
    $clean_distance = preg_replace('/[^-a-zA-Z0-9-.]/', '', $_GET['distance']);
  }
  ?>
  <!-- search form -->
  <form id="wpgeoloc_form" action="<?php echo esc_url(home_url( '/' )); ?>" method="GET">
    <div id="locationField">
      <div class="input-group">
        <?php if(isset($_GET['address'])){
          $clean_address = sanitize_text_field($_POST['address']);
        }?>
        <input id="autocomplete" placeholder="<?php echo esc_html__('Type an address here', 'wpgeoloc') ?>" type="text" class="form-control search-query" value="<?php if(isset($clean_address)) echo esc_attr(stripslashes($clean_address)); else echo ''; ?>">
        <span class="input-group-btn">
          <button type="submit" class="btn btn-default" id="searchsubmit">
            <?php echo esc_html__('Go', 'wpgeoloc') ?>
          </button>
        </span>
      </div>
      <span id="autocomplete_message"><?php echo esc_html__('Please select an address from search results', 'wpgeoloc'); ?></span>
      <input type="hidden" id="s" name="s" value="">
      <input type="hidden" id="formatted_address" name="address" value="<?php if($clean_address) echo esc_attr(stripslashes($clean_address)); else echo ''; ?>">
      <input type="hidden" id="latitude" name="latitude" value="<?php if(isset($clean_latitude)) echo esc_attr($clean_latitude); else echo ''; ?>">
      <input type="hidden" id="longitude" name="longitude" value="<?php if(isset($clean_longitude)) echo esc_attr($clean_longitude); else echo ''; ?>">
      <div id="range-container">
        <input type="range" name="distance" value="<?php if(isset($clean_distance)) echo esc_attr($clean_distance); else echo 100; ?>">
      </div>
    </div>
  </form>
  <style>.rangeslider__ruler:after { content: "<?php echo esc_html__('Distance - km', 'wpgeoloc') ?>"; }</style>
  <script>
    jQuery( "#wpgeoloc_form" ).submit(function( event ) {
      if(jQuery('#wpgeoloc_form #autocomplete').val() == ''){
        event.preventDefault();
        jQuery( "#wpgeoloc_form #autocomplete_message" ).addClass( "error" );
      }
      if(jQuery('#wpgeoloc_form #latitude').val() == ''){
        event.preventDefault();
        jQuery( "#wpgeoloc_form #autocomplete_message" ).addClass( "error" );
      }
      if(jQuery('#wpgeoloc_form #longitude').val() == ''){
        event.preventDefault();
        jQuery( "#wpgeoloc_form #autocomplete_message" ).addClass( "error" );
      }
    });
    var $r = jQuery('input[type="range"]');
    var $ruler = jQuery('<div class="rangeslider__ruler" />');
    var rulerStep = 10;

    // Initialize
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
