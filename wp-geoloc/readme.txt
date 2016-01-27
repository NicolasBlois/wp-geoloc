=== Plugin Name ===
Contributors: bloutch
Tags: geo, location, geolocation, geotag, radius, latitude, longitude, distance, miles, km, geoloc, range, plugin, widget, url, GET
Requires at least: 3.8
Tested up to: 4.4.1
Stable tag: 1.0.2

Add the geolocation to your wordpress search. Search for posts based on latitude, longitude and distance.

== Description ==

This plugin adds geolocation functionnality to your articles. It allows you to request for articles based on latitude, longitude and distance provided as GET parameters in URL.
A widget is available with the Google Place Autocomplete + distance range slider.

<h4>Usage:</h4>

Example : on an archive page, you are looking restaurants located within 10 km of this latitude [50.8385607] and this longitude [4.37526040] :

* http://www.example.com/category/restaurant?latitude=50.8385607&longitude=4.37526040&distance=10

You can also do it on the search page with the keyword "super" :

* http://www.example.com/?s=super&latitude=50.8385607&longitude=4.37526040&distance=10

Search within any radius chosen from a range slider. A widget to search for geolocated content is available.

There is also a shortcode to include it where you want : [wpgeoloc]

<h4>Languages :</h4>
* English
* French

<h4>Requirement :</h4>

You need a Google Places API KEY for the Google place autocomplete service.

<h4>Contributions :</h4>

github : https://github.com/NicolasBlois/wp-geoloc

== Installation ==

* Upload to plugins dir
* Activate plugin
* Add your Google Places API KEY in the plugin settings page
* Add a place for each post you wish to geotag. A box is provided for this purpose in post edit mode.
* Enjoy the geolocated requests within URL.

== Changelog ==
= 1.0.2 =
* standardization of code

= 1.0.1 =
* New : widget to search geolocated content.
* New : shortcode to include the geolocated search form in any post.
* New : translations in French
* Fix : Prevent XSS and other attacks

= 1.0.0 =
* Initial release
