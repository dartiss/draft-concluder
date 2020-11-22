=== Draft Concluder ===
Contributors: dartiss
Donate link: https://artiss.blog/donate
Tags: drafts, email, pages, posts, reminder
Requires at least: 4.6
Tested up to: 5.5
Requires PHP: 5.3
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

📝 Email users that have outstanding drafts.

== Description ==

Based on [an idea by John Blackbourn](https://twitter.com/johnbillion/status/1314494422529331203), this plugin is designed to be a reminder to those who leave draft posts unloved. And, yes, all of John's ideas are here, with more to boot.

* Send emails out on a daily or weekly schedule and at a time that you'd prefer
* Look for draft pages as well as posts, if you like. Or just pages, if that's what you want. We won't judge
* Target those drafts that were created more than a specific time period ago, or have not been updated for a while
* Each user, who has drafts that then reminding about, will receive an email. No, they can't unsubscribe from them
* Each email will show the number of drafts, along with a reminder of each of them
* Optional ability to prevent the plugin from being deactivated (allow you to avoid the temptation to do so rather than, you know, deal with the drafts)
* Debug features to allow to verify what's being sent

Oh, and, naturally, the code passes [WordPress](https://github.com/WordPress/WordPress-Coding-Standards) and [WordPress VIP](https://github.com/Automattic/VIP-Coding-Standards) coding standards 🎉

I'd like to thank [Caleb Burks](https://calebburks.com/) for the feedback he provided. Also, the iconography is courtesy of the very talented [Janki Rathod](https://www.fiverr.com/jankirathore) ♥️

👉 Please visit the [Github page](https://github.com/dartiss/draft-concluder "Github") for the latest code development, planned enhancements and known issues 👈

== Installation ==

Draft Concluder can be found and installed via the Plugin menu within WordPress administration (Plugins -> Add New). Alternatively, it can be downloaded from WordPress.org and installed manually...

1. Upload the entire `draft-concluder` folder to your `wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress administration.

It's now ready to go but, if you want to tweak further, head to Settings -> General.

== Frequently Asked Questions ==

= Why's it called Draft Concluder? =

Because it helps you find an end to those annoying drafts that sit around and never get completed. And it's a pun on "draft excluder". Yes, I know - blame my SO, who came up with the name.

= What does the "Status" on the Settings screen mean? =

In Settings -> General -> Draft Concluder, you'll see, under the title, a status - this will tell you when it last ran and whether it was successful or not.

The success is dependant on whether any error was returned when sending out emails - a failure would indicate a problem with `wp_mail` and will need further investigation.

= The email isn't turning up at the time that it's scheduled! =

The internal WordPress scheduler is an interesting beast. It's totally reliant on someone visiting your site for it to trigger - so if you set it to 1am but nobody visits until 8am then, yeah, you won't get email until after 8am.

Thankfully, I thought of you when I wrote the plugin. As per the previous question, there's a status on the settings screen. Additionally, there are 2 shortcodes that you can add to any post or page to help you out.

`[dc_now]` - this will run the email generator and show, on screen, what would be sent if you were running it now.

`[dc_last_run]` - this will show, on screen, what happened during the last scheduled run. So, if the status in the settings shows it ran, you can use this shortcode to display what was actually sent and to whom.

= Can I unsubscribe from the email that I'm sent? =

Heck, no. That's the beauty of this plugin.

= Can I just send the email to spam instead? =

I wouldn't do that. The email comes from the site's account - marking it as spam may also lead to all other site emails going the same way, including password resets and important security information.

Look, if it means that much to you, maybe look to sorting out your drafts, yes?

= How can I prevent the plugin from being deactivated? =

...said no-one. But seriously, you want to remove the temptation, right?

Crack open your site's `wp-config.php` and add the following line of code...

`define( 'DO_NOT_DISABLE_MY_DRAFT_REMINDER', true );`

And the deed is done - you can no longer disable the plugin (cue diabolical laughter).

== Screenshots ==

1. Available options, available in Settings -> General

== Changelog ==

I use semantic versioning, with the first release being 1.0.

= 1.0.1 =
* Bug: In what must be the quickest time from initial release to first bug report, thanks to [JeanPaulH](https://wordpress.org/support/users/djr/) for finding a stray comma!

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.0.1 =
* Bug fix
