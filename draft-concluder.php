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
		if ( 'daily' != $when ) {
			$daily = 'weekly';
		}
		draft_concluder_process_posts( $when );
	}

}

add_action( 'draft_concluder_mailer', 'draft_concluder_schedule_engine' );

/**
 * Process posts
 *
 * This processes the draft posts for each user in turn
 * It's defined as a seperate function, seperate from the scheduler action, so that it can
 * be called seperately, if required.
 *
 * @param string $when 'daily' or 'weekly' - how often the email is produced.
 */
function draft_concluder_process_posts( $when ) {

	// Get an array of users and loop through each.

	$users = get_users();
	foreach ( $users as $user ) {

		$draft_count = 0;

		// Now grab all the posts of each user.

		$args = array(
			'author'      => $user->display_name,
			'post_status' => 'draft',
			'orderby'     => 'post_date',
			'sort_order'  => 'asc',
		);

		$email_addy = $user->user_email;

		$posts   = get_posts( $args );
		$message = '';

		foreach ( $posts as $post ) {

			$title = $post->post_title;

			$draft_count ++;

			/* translators: Do not translate COUNT,TITLE, LINK, CREATED or MODIFIED : those are placeholders. */
			$message .= __(
				'###COUNT###. ###TITLE### - ###LINK###
    This was created on ###CREATED### and was last edited on ###MODIFIED###.
',
				'draft_concluder'
			);


			$message = str_replace(
				array(
					'###COUNT###',
					'###TITLE###',
					'###LINK###',
					'###CREATED###',
					'###MODIFIED###',
				),
				array(
					$draft_count,
					$post->post_title,
					get_admin_url() . 'post.php?post=' . $post->ID . '&action=edit',
					$post->post_date,
					$post->post_modified,
				),
				$message
			);
		}

		if ( 0 < $draft_count ) {

			/* translators: Do not translate WHEN or NUMBER: those are placeholders. */
			$header = __(
				'Howdy!

This is your ###WHEN### reminder that you have ###NUMBER### outstanding draft(s) that require your attention:

',
				'draft_concluder'
			);

			$header = str_replace(
				array(
					'###WHEN###',
					'###NUMBER###',
				),
				array(
					$when,
					$draft_count,
				),
				$header
			);

			/* translators:  */
			$subject = sprintf( __( '[%s] You have outstanding drafts', 'draft-concluder' ), get_bloginfo( 'name' ) );
			$body    = $header . $message;
	 
			$mail_check = wp_mail( $email_addy, $subject, $body );
		}
	}


}
