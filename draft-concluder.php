<?php
/**
 * Draft Concluder
 *
 * @package           draft-concluder
 * @author            David Artiss
 * @license           GPL-2.0-or-later
 *
 * Plugin Name:       Draft Concluder
 * Plugin URI:        https://wordpress.org/plugins/draft-concluder/
 * Description:       📝 Email users that have outstanding drafts.
 * Version:           1.1.2
 * Requires at least: 4.6
 * Requires PHP:      7.4
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
			array( '<a href="https://wordpress.org/support/plugin/draft-concluder/reviews/#new-post">' . __( 'Write a Review', 'draft-concluder' ) . '&nbsp;⭐️⭐️⭐️⭐️⭐️</a>' )
		);
	}

	return $links;
}

add_filter( 'plugin_row_meta', 'draft_concluder_plugin_meta', 10, 2 );

/**
 * Modify actions links.
 *
 * Add or remove links for the actions listed against this plugin
 *
 * @param    string $actions      Current actions.
 * @param    string $plugin_file  The plugin.
 * @return   string               Actions, now with deactivation removed!
 */
function draft_concluder_action_links( $actions, $plugin_file ) {

	// Make sure we only perform actions for this specific plugin!
	if ( strpos( $plugin_file, 'draft-concluder.php' ) !== false ) {

		// If the appropriate constant is defined, remove the deactionation link.
		if ( defined( 'DO_NOT_DISABLE_MY_DRAFT_REMINDER' ) && true === DO_NOT_DISABLE_MY_DRAFT_REMINDER ) {
			unset( $actions['deactivate'] );
		}

		// Add link to the settings page.
		array_unshift( $actions, '<a href="' . admin_url() . 'options-general.php">' . __( 'Settings', 'draft-concluder' ) . '</a>' );

	}

	return $actions;
}

add_filter( 'plugin_action_links', 'draft_concluder_action_links', 10, 2 );

/**
 * Define scheduler
 *
 * If a schedule isn't already set up, set one up!
 * It will run daily at a time which can be adjusted.
 */
function draft_concluder_set_up_schedule() {

	$day = 'tomorrow';

	// Get the time that the event needs scheduling for.
	// If one isn't specified, default to 1am!
	$time = get_option( 'draft_concluder_time' );
	if ( ! $time ) {
		$time = '1am';
	}

	// If we have an old time saved, check to see if it's changed.
	// If it has, remove the event and update the old one.
	$saved_time = get_option( 'draft_concluder_prev_time' );

	if ( ! $saved_time || $time != $saved_time ) {
		if ( strtotime( 'today ' . $time ) > strtotime( 'now' ) ) {
			$day = 'today';
		}
		wp_clear_scheduled_hook( 'draft_concluder_mailer' );
		update_option( 'draft_concluder_prev_time', $time );
	}

	// Schedule an event if one doesn't already exist.
	if ( ! wp_next_scheduled( 'draft_concluder_mailer' ) && ! wp_installing() ) {
		wp_schedule_event( strtotime( $day . ' ' . $time ), 'daily', 'draft_concluder_mailer' );
	}
}

add_action( 'admin_init', 'draft_concluder_set_up_schedule' );

/**
 * Scheduler engine
 *
 * This is the function that runs when the daily scheduler rolls around
 */
function draft_concluder_schedule_engine() {

	// Grab the settings for how often to run this.
	// If it's not been set, assume weekly!
	$when = get_option( 'draft_concluder_when' );
	if ( ! $when ) {
		$when = 'Monday';
	}

	// Check to see if it should be run.
	if ( 'Daily' == $when || ( 'Daily' != $when && gmdate( 'l' ) == $when ) ) {
		draft_concluder_process_posts();
	}

}

add_action( 'draft_concluder_mailer', 'draft_concluder_schedule_engine' );

/**
 * Add to settings
 *
 * Add fields to the general settings to capture the various settings required for the plugin.
 */
function draft_concluder_settings_init() {

	// Add the new section to General settings.
	add_settings_section( 'draft_concluder_section', __( 'Draft Concluder', 'draft_concluder' ), 'draft_concluder_section_callback', 'general' );

	// Add the settings field for what day to generate the emails.
	add_settings_field( 'draft_concluder_when', __( 'Day to generate', 'draft-concluder' ), 'draft_concluder_when_callback', 'general', 'draft_concluder_section', array( 'label_for' => 'draft_concluder_when' ) );
	register_setting( 'general', 'draft_concluder_when' );

	// Add the settings field for what time to generate the emails.
	add_settings_field( 'draft_concluder_time', __( 'Time to generate', 'draft-concluder' ), 'draft_concluder_time_callback', 'general', 'draft_concluder_section', array( 'label_for' => 'draft_concluder_time' ) );
	register_setting( 'general', 'draft_concluder_time' );

	// Add the settings field for which post types to look through for drafts.
	add_settings_field( 'draft_concluder_what', __( 'Post types to check', 'draft-concluder' ), 'draft_concluder_what_callback', 'general', 'draft_concluder_section', array( 'label_for' => 'draft_concluder_what' ) );
	register_setting( 'general', 'draft_concluder_what' );

	// Add the settings field for how old drafts must be to qualify.
	add_settings_field( 'draft_concluder_age', __( 'Draft age to qualify', 'draft-concluder' ), 'draft_concluder_age_callback', 'general', 'draft_concluder_section', array( 'label_for' => 'draft_concluder_age' ) );
	register_setting( 'general', 'draft_concluder_age' );

	// Add the settings field for whether draft ages should be based on creation or update.
	add_settings_field( 'draft_concluder_since', '', 'draft_concluder_since_callback', 'general', 'draft_concluder_section', array( 'label_for' => 'draft_concluder_since' ) );
	register_setting( 'general', 'draft_concluder_since' );
}

add_action( 'admin_init', 'draft_concluder_settings_init' );

/**
 * Section callback
 *
 * Create the new section that we've added to the Discussion settings screen
 */
function draft_concluder_section_callback() {

	$output = get_option( 'draft_concluder_output' );

	echo esc_html( __( 'These settings allow you to control when the emails are generated and what they should report on.', 'draft-concluder' ) );

	// Show the current status of the event run.
	echo '<br/><br/>';
	echo wp_kses( '<strong>' . __( 'Status: ', 'draft_concluder' ) . '</strong>', array( 'strong' => array() ) );
	if ( ! $output ) {
		echo esc_html( __( 'Draft Concluder has not yet run.', 'draft_concluder' ) );
	} else {
		$timestamp = gmdate( 'l jS \o\f F Y \a\t g:i A', $output['timestamp'] );
		if ( 0 == $output['errors'] ) {
			/* translators: %1$s: timestamp */
			$text = sprintf( __( 'Draft Concluder last ran at %1$s, successfully.', 'draft_concluder' ), $timestamp );
		} else {
			/* translators: %1$s: timestamp */
			$text = sprintf( __( 'Draft Concluder last ran at %1$s, with errors.', 'draft_concluder' ), $timestamp );
		}
	}

	// If an event has been scheduled, output the next run time.
	if ( false !== wp_next_scheduled( 'draft_concluder_mailer' ) ) {
		$next_run = gmdate( 'l jS \o\f F Y \a\t g:i A', wp_next_scheduled( 'draft_concluder_mailer' ) );
		/* translators: %1$s: timestamp */
		$text .= '&nbsp;' . sprintf( __( 'It is next due to run on %1$s.', 'draft_concluder' ), $next_run );
	}

	echo esc_html( $text ) . '<br/>';
}

/**
 * When? callback
 *
 * Add the settings field for what date to generate the emails.
 */
function draft_concluder_when_callback() {

	$option = get_option( 'draft_concluder_when' );
	if ( ! $option ) {
		$option = 'Monday';
	}

	echo '<select name="draft_concluder_when">';
	echo '<option ' . selected( 'Daily', $option, false ) . ' value="Daily">' . esc_html__( 'Daily', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( 'Monday', $option, false ) . ' value="Monday">' . esc_html__( 'Monday', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( 'Tuesday', $option, false ) . ' value="Tuesday">' . esc_html__( 'Tuesday', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( 'Wednesday', $option, false ) . ' value="Wednesday">' . esc_html__( 'Wednesday', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( 'Thursday', $option, false ) . ' value="Thursday">' . esc_html__( 'Thursday', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( 'Friday', $option, false ) . ' value="Friday">' . esc_html__( 'Friday', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( 'Saturday', $option, false ) . ' value="Saturday">' . esc_html__( 'Saturday', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( 'Sunday', $option, false ) . ' value="Sunday">' . esc_html__( 'Sunday', 'draft_concluder' ) . '</option>';
	echo '</select>';
}

/**
 * Time callback
 *
 * Add the settings field for what time to generate the emails.
 */
function draft_concluder_time_callback() {

	$option = get_option( 'draft_concluder_time' );
	if ( ! $option ) {
		$option = '1am';
	}

	echo '<select name="draft_concluder_time">';
	echo '<option ' . selected( '12am', $option, false ) . ' value="12am">' . esc_html__( 'Midnight', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '1am', $option, false ) . ' value="1am">' . esc_html__( '1am', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '2am', $option, false ) . ' value="2am">' . esc_html__( '2am', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '3am', $option, false ) . ' value="3am">' . esc_html__( '3am', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '4am', $option, false ) . ' value="4am">' . esc_html__( '4am', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '5am', $option, false ) . ' value="5am">' . esc_html__( '5am', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '6am', $option, false ) . ' value="6am">' . esc_html__( '6am', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '7am', $option, false ) . ' value="7am">' . esc_html__( '7am', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '8am', $option, false ) . ' value="8am">' . esc_html__( '8am', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '9am', $option, false ) . ' value="9am">' . esc_html__( '9am', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '10am', $option, false ) . ' value="10am">' . esc_html__( '10am', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '11am', $option, false ) . ' value="11am">' . esc_html__( '11am', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '12pm', $option, false ) . ' value="12pm">' . esc_html__( 'Midday', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '1pm', $option, false ) . ' value="1pm">' . esc_html__( '1pm', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '2pm', $option, false ) . ' value="2pm">' . esc_html__( '2pm', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '3pm', $option, false ) . ' value="3pm">' . esc_html__( '3pm', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '4pm', $option, false ) . ' value="4pm">' . esc_html__( '4pm', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '5pm', $option, false ) . ' value="5pm">' . esc_html__( '5pm', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '6pm', $option, false ) . ' value="6pm">' . esc_html__( '6pm', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '7pm', $option, false ) . ' value="7pm">' . esc_html__( '7pm', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '8pm', $option, false ) . ' value="8pm">' . esc_html__( '8pm', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '9pm', $option, false ) . ' value="9pm">' . esc_html__( '9pm', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '10pm', $option, false ) . ' value="10pm">' . esc_html__( '10pm', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '11pm', $option, false ) . ' value="11pm">' . esc_html__( '11pm', 'draft_concluder' ) . '</option>';
	echo '</select>';
}

/**
 * What? callback
 *
 * Add the settings field for which post types to look through for drafts
 */
function draft_concluder_what_callback() {

	$option = get_option( 'draft_concluder_what' );
	if ( ! $option ) {
		$option = 'postpage';
	}

	echo '<select name="draft_concluder_what">';
	echo '<option ' . selected( 'post', $option, false ) . ' value="post">' . esc_html__( 'Posts', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( 'page', $option, false ) . ' value="page">' . esc_html__( 'Pages', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( 'postpage', $option, false ) . ' value="post">' . esc_html__( 'Posts & Pages', 'draft_concluder' ) . '</option>';
	echo '</select>';
}

/**
 * Age? callback
 *
 * Add the settings field for how old drafts must be to qualify.
 */
function draft_concluder_age_callback() {

	$option = get_option( 'draft_concluder_age' );
	if ( ! $option ) {
		$option = 0;
	}

	echo '<input name="draft_concluder_age" size="3" maxlength="3" type="text" value="' . esc_attr( $option ) . '" />&nbsp;' . esc_html__( 'days', 'draft_concluder' );
}

/**
 * Since? callback
 *
 * Add the settings field for whether draft ages should be based on creation or update.
 */
function draft_concluder_since_callback() {

	$option = get_option( 'draft_concluder_since' );
	if ( ! $option ) {
		$option = 'created';
	}

	echo '<select name="draft_concluder_since">';
	echo '<option ' . selected( 'created', $option, false ) . ' value="created">' . esc_html__( 'Since they were created', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( 'modified', $option, false ) . ' value="modified">' . esc_html__( 'Since they were last updated', 'draft_concluder' ) . '</option>';
	echo '</select>';
}

/**
 * Process posts
 *
 * This processes the draft posts for each user in turn
 * It's defined as a seperate function, seperate from the scheduler action, so that it can
 * be called seperately, if required.
 *
 * @param string $debug true or false, determining if this is to be emailed or output.
 */
function draft_concluder_process_posts( $debug = false ) {

	$output = array();
	$errors = 0;

	$since = get_option( 'draft_concluder_since' );

	// Get age of acceptable posts. If age not set, assume 0 which means an unlimited.
	$age = get_option( 'draft_concluder_age' );
	if ( ! $age ) {
		$age = 0;
	}

	// Get how regularly it's due to run. If not daily, then assume weekly.
	$when = strtolower( get_option( 'draft_concluder_when' ) );
	if ( 'daily' != $when ) {
		$when = 'weekly';
	}

	// Set up the post types that will be searched for.

	$postpage = get_option( 'draft_concluder_what' );
	if ( ! $postpage || 'postpage' == $postpage ) {
		$postpage = array( 'page', 'post' );
	}

	// Get an array of users and loop through each.

	$users = get_users();
	foreach ( $users as $user ) {

		$draft_count = 0;

		// Now grab all the posts of each user.

		$args = array(
			'post_status' => 'draft',
			'post_type'   => $postpage,
			'numberposts' => 99,
			'author'      => $user->ID,
			'orderby'     => 'post_date',
			'sort_order'  => 'asc',
		);

		$email_addy = $user->user_email;

		$posts   = get_posts( $args );
		$message = '';

		foreach ( $posts as $post ) {

			// Check to see if draft is old enough!

			$include_draft = true;

			if ( 0 != $age ) {

				if ( 'modified' == $since ) {
					$date = $post->post_modified;
				} else {
					$date = $post->post_date;
				}

				// Convert the post edit date into Unix time format.
				$post_unix = strtotime( $date );

				// Get current time in Unix format and subtract the number of days specified.
				$check_unix = time() - ( $age * DAY_IN_SECONDS );

				if ( $post_unix > $check_unix ) {
					$include_draft = false;
				}
			}

			// If the modified date is different to the creation date, then we'll output it.
			// In which case, we'll build an appropriate end-of-sentence.
			$modified = '';
			if ( $post->post_date != $post->post_modified ) {
				/* translators: %1$s: the date the post was last modified */
				$modified = sprintf( __( ' and last edited on %1$s', 'draft_concluder' ), esc_html( substr( $post->post_modified, 0, strlen( $post->post_modified ) - 3 ) ) );
			}

			if ( $include_draft ) {

				// Build a list of drafts that require the user's attention.
				$draft_count ++;

				/* translators: Do not translate COUNT,TITLE, LINK, CREATED or MODIFIED : those are placeholders. */
				$message .= __(
					'###COUNT###. ###TITLE### - ###LINK### (###WORDS### words)
    This was created on ###CREATED######MODIFIED###.

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
						'###WORDS###',
					),
					array(
						esc_html( $draft_count ),
						esc_html( $post->post_title ),
						esc_html( get_admin_url() . 'post.php?post=' . $post->ID . '&action=edit' ),
						esc_html( substr( $post->post_date, 0, strlen( $post->post_date ) - 3 ) ),
						$modified,
						esc_html( str_word_count( $post->post_content ) ),
					),
					$message
				);
			}
		}

		// Add a header to the email content. A different message is used dependant on whether there is 1 or more drafts.
		if ( 0 < $draft_count ) {

			if ( 1 == $draft_count ) {

				/* translators: Do not translate WHEN: this is a placeholder. */
				$header = __(
					'Howdy!

This is your ###WHEN### reminder that you have an outstanding draft that requires your attention:

',
					'draft_concluder'
				);

			} else {

				/* translators: Do not translate WHEN or NUMBER: those are placeholders. */
				$header = __(
					'Howdy!

This is your ###WHEN### reminder that you have ###NUMBER### outstanding drafts that require your attention:

',
					'draft_concluder'
				);
			}

			$header = str_replace(
				array(
					'###WHEN###',
					'###NUMBER###',
				),
				array(
					esc_html( $when ),
					esc_html( $draft_count ),
				),
				$header
			);

			if ( 1 == $draft_count ) {
				/* translators: %1$s: name of blog */
				$subject = sprintf( __( '[%1$s] You have an outstanding draft', 'draft-concluder' ), get_bloginfo( 'name' ) );
			} else {
				/* translators: %1$s: name of blog, %2$s: number of drafts */
				$subject = sprintf( __( '[%1$s] You have %2$s outstanding drafts', 'draft-concluder' ), get_bloginfo( 'name' ), $draft_count );
			}
			$body = $header . $message;

			$display_out       = '<p>' . esc_html__( 'To: ', 'draft_concluder' ) . esc_html( $email_addy ) . '<br/>' . esc_html__( 'Subject: ', 'draft_concluder' ) . esc_html( $subject ) . '<br/><br/>' . nl2br( esc_html( $body ) ) . '</p>';
			$output['emails'] .= $display_out;

			// If debugging, output to screen - otherwise, email the results.

			if ( $debug ) {
				return wp_kses(
					$display_out,
					array(
						'br' => array(),
						'p'  => array(),
					)
				);
			} else {
				// phpcs:ignore -- ignoring from PHPCS as this is only being used for a small number of mails
				$mail_rc = wp_mail( $email_addy, $subject, $body );
				if ( ! $mail_rc ) {
					$errors++;
				}
			}
		}
	}

	// Update the saved output for the last run.

	if ( ! $debug ) {
		$output['errors']    = $errors;
		$output['timestamp'] = time();
		update_option( 'draft_concluder_output', $output );
	}
}

/**
 * Now shortcode
 *
 * Will generate and output the email content.
 *
 * @param string $paras   Parameters.
 * @param string $content Content between shortcodes.
 */
function draft_concluder_now_shortcode( $paras, $content ) {

	return draft_concluder_process_posts( true );

}

add_shortcode( 'dc_now', 'draft_concluder_now_shortcode' );


/**
 * Last run shortcode
 *
 * Outputs the results of the last run.
 *
 * @param string $paras   Parameters.
 * @param string $content Content between shortcodes.
 */
function draft_concluder_last_run_shortcode( $paras, $content ) {

	$output = get_option( 'draft_concluder_output' );
	$debug  = '';

	if ( ! $output ) {
		$debug .= esc_html( __( 'Draft Concluder has not yet run.', 'draft_concluder' ) );
	} else {
		$timestamp = gmdate( 'l jS \of F Y h:i:s A', $output['timestamp'] );
		if ( 0 == $output['errors'] ) {
			/* translators: %1$s: timestamp */
			$text = sprintf( __( 'Draft Concluder last ran at %1$s, successfully.', 'draft_concluder' ), esc_html( $timestamp ) );
		} else {
			/* translators: %1$s: timestamp %2$s: number of errors */
			$text = sprintf( __( 'Draft Concluder last ran at %1$s, with %2$s errors.', 'draft_concluder' ), esc_html( $timestamp ), esc_html( $output['errors'] ) );
		}
		$debug .= esc_html( $text ) . '<br/>';
		$debug .= wp_kses(
			$output['emails'],
			array(
				'br' => array(),
				'p'  => array(),
			)
		);
	}

	return $debug;
}

add_shortcode( 'dc_last_run', 'draft_concluder_last_run_shortcode' );
