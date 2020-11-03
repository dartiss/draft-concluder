=== Draft Concluder ===
Contributors: dartiss
Donate link: https://artiss.blog/donate
Tags: drafts, email, mail, pages, posts, reminder
Requires at least: 3.1
Tested up to: 5.5
Requires PHP: 5.3
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

📝 Email users that have outstanding drafts.

== Description ==

Based on [an idea by John Blackbourn](https://twitter.com/johnbillion/status/1314494422529331203), this plugin is designed to be a reminder to those who leave draft posts unloved. And, yes, all of John's ideas are here, with more to boot.

* Send emails out on a daily or weekly schedule and at a time that you'd prefer
* Look for draft pages as well as posts, if you like. Or just pages, if that's what you want. We won't judge
* Target those drafts that have not been updated for over a certain period of time
* Each user, who has drafts that then reminding about, will receive an email. No, they can't unsubscribe from them
* Each email will show the number of drafts, along with a reminder of each of them
* Optional ability to prevent the plugin from being deactivated (allow you to avoid the temptation to do so rather than, you know, deal with the drafts)

Oh, and, naturally, the code passes [WordPress](https://github.com/WordPress/WordPress-Coding-Standards) and [WordPress VIP](https://github.com/Automattic/VIP-Coding-Standards) coding standards 🎉

Iconography is courtesy of the very talented [Janki Rathod](https://www.linkedin.com/in/jankirathore/) ♥️

👉 Please visit the [Github page](https://github.com/dartiss/draft-concluder "Github") for the latest code development, planned enhancements and known issues 👈

== Installation ==

Draft Concluder can be found and installed via the Plugin menu within WordPress administration (Plugins -> Add New). Alternatively, it can be downloaded from WordPress.org and installed manually...

1. Upload the entire `draft-concluder` folder to your `wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress administration.

It's now ready to go but, if you want to tweak further, head to Settings -> Writing.

== Frequently Asked Questions ==

= The email isn't turn up at the time that it's scheduled! =

The WordPress even scheduler is an interesting beast. It's totally reliant on someone visiting your site for it to trigger - so if you set it to 1am but nobody visits until 8am then, yeah, you won't get email until after 8am.

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

== Changelog ==

I use symantic versioning, with the first release being 0.1.

= 0.1 =
* Initial release

== Upgrade Notice ==

= 0.1 =
* Initial release