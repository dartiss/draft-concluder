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

		$links = array_merge(
			$links,
			array( '<a href="https://github.com/dartiss/draft-concluder">' . __( 'Github', 'draft-concluder' ) . '</a>' ),
			array( '<a href="https://wordpress.org/support/plugin/draft-concluder">' . __( 'Support', 'draft-concluder' ) . '</a>' ),
			array( '<a href="https://artiss.blog/donate">' . __( 'Donate', 'draft-concluder' ) . '</a>' ),
			array( '<a href="https://wordpress.org/support/plugin/draft-concluder/reviews/#new-post">' . __( 'Write a Review', 'draft-concluder' ) . '&nbsp;⭐️⭐️⭐️⭐️⭐️</a>' ),
		);
	}

	return $links;
}

add_filter( 'plugin_row_meta', 'draft_concluder_plugin_meta', 10, 2 );

/**
 * Define scheduler
 *
 * If a schedule isn't already set up, set one up!
 * It will run at 1am tomorrow and then daily afterwards.
 */
function draft_concluder_set_up_schedule() {

	if ( ! wp_next_scheduled( 'draft_concluder_mailer' ) && ! wp_installing() ) {
		wp_schedule_event( strtotime( 'tomorrow 1am' ), 'daily', 'draft_concluder_mailer' );
	}
}

add_action( 'init', 'draft_concluder_set_up_schedule' );

/**
 * Scheduler engine
 *
 * This is the function that runs when the daily scheduler rolls around
 */
function draft_concluder_schedule_engine() {

	// Grab the settings for how often to run this.
	// If it's not been set, assume weekly!

	$when = get_option( 'draft_concluder_when' );
	if ( ! isset( $when ) ) {
		$when = 'Monday';
	}

	// Check to see if it should be run. If weekly, make sure it's a Sunday.
	
	if ( 'daily' == $when || ( 'daily' != $when && date( 'l' ) == $when ) ) {
		draft_concluder_process_posts();
	}

}

add_action( 'draft_concluder_mailer', 'draft_concluder_schedule_engine' );

/**
 * Process posts
 *
 * This processes the draft posts for each user in turn
 * It's defined as a seperate function, seperate from the scheduler action, so that it can
 * be called seperately, if required.
 */
function draft_concluder_process_posts() {

	// Get an array of users and loop through each.

	$users = get_users();
	foreach ( $users as $user ) {

		// Now grab all the postd of each user.

		$args = array(
			'author'      => $user->display_name,
			'post_status' => 'draft',
			'orderby'     => 'post_date',
			'sort_order'  => 'asc',
		);

		$posts = get_posts( $args );
		foreach ( $posts as $post ) {
		}
	}
}
