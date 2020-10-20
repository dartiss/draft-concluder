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
 * @param string $when 'daily' or 'weekly' - how often the email is produced.
 */
function draft_concluder_process_posts( $when ) {

	if ( ! isset( $when ) ) {
		exit;
	}

	// Get age of acceptable posts.
	// If not set, assume 0 which means an unlimited.

	$since = get_option( 'draft_concluder_since' );
	$age   = get_option( 'draft_concluder_age' );
	if ( ! isset( $age ) ) {
		$age = 0;
	}

	// Set up the post types that will be searched for.

	$postpage = get_option( 'draft_concluder_what' );
	if ( ! isset( $postpage ) || 'postpage' == $postpage ) {
		$postpage = array( 'page', 'post' );
	}

	// Get an array of users and loop through each.

	$users = get_users();
	foreach ( $users as $user ) {

		$draft_count = 0;

		// Now grab all the posts of each user.

		$args = array(
			'author'      => $user->display_name,
			'post_type'   => $postpage,
			'post_status' => 'draft',
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

				if ( $strtotime( $date ) > ( time() - $age ) ) {
					$include_draft = false;
				}
			}

			if ( $include_draft ) {

				// Build a list of drafts that require the user's attention.

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
						substr( $post->post_date, 0, strlen( $post->post_date ) - 3 ),
						substr( $post->post_modified, 0, strlen( $post->post_modified ) - 3 ),
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
					$when,
					$draft_count,
				),
				$header
			);

			/* translators: %1$s: name of blog, %2$s: number of drafts */
			$subject = sprintf( __( '[%1$s] You have %2$s outstanding drafts', 'draft-concluder' ), get_bloginfo( 'name' ), $draft_count );
			$body    = $header . $message;

			// phpcs:ignore -- ignoring from PHPCS as this is only being used for a small number of mails
			wp_mail( $email_addy, $subject, $body );
		}
	}
}
