<?php
/*
  Plugin Name: Alter WP-CFM config path
  Description: Alters the WP-CFM config path to be outside the plugin directory
  Version: 0.1
  Author: Andrew Taylor
*/

// Tell wp-cfm where our config files live
add_filter('wpcfm_config_dir', function($var) { return ABSPATH . '../config'; });
add_filter('wpcfm_config_url', function($var) { return WP_HOME . '../config'; });
