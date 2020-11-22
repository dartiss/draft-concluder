<?php
/**
 * Draft Concluder
 *
 * @package           draft-concluder
 * @author            David Artiss
 * @license           GPL-2.0-or-later
 *
 * Plugin Name:       Draft List
 * Plugin URI:        https://wordpress.org/plugins/draft-concluder/
 * Description:       📝 Email users that have outstanding drafts.
 * Version:           1.1
 * Requires at least: 4.6
 * Requires PHP:      5.3
 * Author:            David Artiss
 * Author URI:        https://artiss.blog
 * Text Domain:       draft-concluder
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

// Require the various code components - all held within the inc folder.
require_once plugin_dir_path( __FILE__ ) . 'inc/setup.php';

require_once plugin_dir_path( __FILE__ ) . 'inc/settings.php';

require_once plugin_dir_path( __FILE__ ) . 'inc/process-drafts.php';

require_once plugin_dir_path( __FILE__ ) . 'inc/debug.php';
