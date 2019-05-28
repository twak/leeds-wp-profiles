<?php
/**
 * Plugin Name: twak's profiles
 * Plugin URI: https://github.com/twak/leeds-wp-profiles
 * GitHub Plugin URI: https://github.com/
 * Description: This plugin adds toolkit profiles.
 * Version: 0.0.¾
 * Author: Application Development, University of Leeds + twak
 * Author URI: https://github.com/twak/leeds-wp-profiles
 */

/**
 * load plugin files from lib
 */
include_once dirname( __FILE__ ) . '/lib/class-tk-profiles.php';
include_once dirname( __FILE__ ) . '/lib/class-tk-profiles-post-type.php';
include_once dirname( __FILE__ ) . '/lib/class-tk-profiles-acf.php';
include_once dirname( __FILE__ ) . '/lib/class-tk-profiles-admin.php';
include_once dirname( __FILE__ ) . '/lib/class-tk-profiles-utilities.php';
include_once dirname( __FILE__ ) . '/lib/class-tk-profiles-shortcodes.php';
include_once dirname( __FILE__ ) . '/lib/class-tk-profiles-templates.php';
include_once dirname( __FILE__ ) . '/lib/class-tk-profiles-as-authors.php';
include_once dirname( __FILE__ ) . '/lib/class-tk-profiles-help.php';
