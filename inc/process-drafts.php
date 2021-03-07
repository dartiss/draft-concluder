<?php
/**
 * Process posts
 * 
 * Primary function to generate emails for outstanding drafts
 *
 * @package draft-concluder
 */

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
