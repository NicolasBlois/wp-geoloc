# Description

This wordpress plugin adds geolocation functionnality for your articles. It allows you to request for articles based on latitude, longitude and distance provided as GET parameters in URL.

# Usage

Example :

On an archive page, you are looking for posts, in the "restaurant" category, located within 10 km from 50.8385607 latitude & 4.37526040 longitude :

http://www.example.com/category/restaurant?latitude=50.8385607&longitude=4.37526040&distance=10

You can also do it on the search page with search keywords :

http://www.example.com/?s=keywords&latitude=50.8385607&longitude=4.37526040&distance=10

# Requirements

You need a Google Places API KEY for the Google place autocomplete service.

# Note

This plugin is based on "Geo Data Store" plugin.

# Installation

* Upload to plugins dir
* Activate plugin
* Add your Google Places API KEY in the plugin settings page
* Add a place for each post you wish to geotag. A box is provided for this purpose in post edit mode.
* Enjoy the geolocated requests within URL.

# Changelog

1.0.0 : Initial release
