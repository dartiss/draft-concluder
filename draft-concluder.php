<?php
/**
Plugin Name: Draft Concluder
Plugin URI: https://wordpress.org/plugins/draft-concluder/
Description: 📝 Email users that have outstanding drafts.
Version: 0.1
Author: David Artiss
Author URI: https://artiss.blog
Text Domain: draft-concluder

@package draft-concluder
 */

// Require the various code components - all held within the inc folder.
require_once plugin_dir_path( __FILE__ ) . 'inc/setup.php';

require_once plugin_dir_path( __FILE__ ) . 'inc/settings.php';

require_once plugin_dir_path( __FILE__ ) . 'inc/process-drafts.php';
