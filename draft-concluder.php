<?php
/**
Plugin Name: Draft Concluder
Plugin URI: https://wordpress.org/plugins/draft-concluder/
Description: 📝 Emails users that have outstanding drafts
Version: 0.1
Author: David Artiss
Author URI: https://artiss.blog
Text Domain: draft-concluder

@package draft-concluder
 */

/**
 * Add meta to plugin details
 *
 * Add options to plugin meta line
 *
 * @param    string $links  Current links.
 * @param    string $file   File in use.
 * @return   string         Links, now with settings added.
 */
function draft_concluder_plugin_meta( $links, $file ) {

	if ( false !== strpos( $file, 'draft-concluder.php' ) ) {

		$links = array_merge( $links, array( '<a href="https://github.com/dartiss/draft-concluder">' . __( 'Github', 'draft-concluder' ) . '</a>' ) );

		$links = array_merge( $links, array( '<a href="https://wordpress.org/support/plugin/draft-concluder">' . __( 'Support', 'draft-concluder' ) . '</a>' ) );

		$links = array_merge( $links, array( '<a href="https://artiss.blog/donate">' . __( 'Donate', 'draft-concluder' ) . '</a>' ) );

		$links = array_merge( $links, array( '<a href="https://wordpress.org/support/plugin/draft-concluder/reviews/#new-post">' . __( 'Write a Review', 'draft-concluder' ) . '&nbsp;⭐️⭐️⭐️⭐️⭐️</a>' ) );
	}

	return $links;
}

add_filter( 'plugin_row_meta', 'draft_concluder_plugin_meta', 10, 2 );
