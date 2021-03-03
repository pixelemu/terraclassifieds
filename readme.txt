=== TerraClassifieds - Simple Classifieds Plugin ===
Contributors: PixelEmu
Tags: classifieds, classified ads, classifieds plugin, advertising
Requires at least: 4.0
Tested up to: 5.7
Stable tag: 2.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
TerraClassifieds is a free classifieds WordPress plugin that allows creating a simple classifieds website with WordPress. There is also a dedicated free theme TerraClassic available to download on pixelemu.com or even a demo copy with all configured settings.

== Useful links: ==
<a href="https://terraclassifieds.pixelemu.com/">TerraClassifieds documentation and tutorials</a>
<a href="https://demo.pixelemu.com/pe-terraclassic/">Live demo created with free TerraClassic theme</a>
<a href="https://www.pixelemu.com/wordpress-themes/i/244-terraclassic">Download TerraClassic for free</a>

== TerraClassifieds Pages ==
* Add advert on the frontend
* Edit ad
* My submissions
* Favorite ads
* User registration
* Edit user profile
* Login & forgot password


== TerraClassifieds Views ==
* Category view
* Author view
* Advert page


== TerraClassifieds Settings ==

= Category view =
* Show category on the archive page with name, description, image, subcategories
* Number of items per page
* Text limit (number of words) for item description
* Add to favourites
* Displaying search form with inputs combinations and setting its sizes for different devices


= Adding advert =
* Number of images limit (max. 8)
* Title characters limit (0 for unlimited)
* Description characters limit (0 for unlimited)
* The number of columns for subcategories list
* The number of days after which the ads will be expired
* The number of days before sending a notification to classified's author
* Required images for a new advert (yes/no)
* Required location for a new advert (yes/no)


= Image processing =

You may set images dimentions for:

* Image sizes for classifieds archive view
* Image sizes for single post view


= Email templates =
* Registration - administrator notification
* Registration - user notification
* New advert - administrator notification
* New advert - user notification
* Contact form - user notification
* Abuse form - user notification
* Change status - user notification
* Expiration - user notification


= Advert view =
Add to favourites

= Style =
* Layout: default or override from theme
* CSS Style: default or override from theme


= Security =

Password for registration

*  Automatically generated password
* Let the user choose a password during registration

= GDPR =
* GDPR method extended
* GDPR method simple


= Locations - locations manually filled in on wp-admin panel =

General

* Currency
* Unit position



== TerraClassifieds Built In Widgets ==
* An account menu
* Latest ads
* Classifieds search
* Classifieds categories

== Installation ==

1. In your admin panel, go to Plugins -> Add New and click the Upload Plugin button.
2. Click Choose File, then select the plugin ZIP file. Click Install Now.
3. Click Activate to activate the plugin.

== Changelog ==

= 2.0.2 =
* Fixed PHP fatal error on users adverts page
* Fixed an incorrect advert expiry time
* Improvements related to jQuery 3

= 2.0.1 =
* Fixed the location select field in submitting advert form

= 2.0 =
* Added polish translation
* Added reCaptcha for 'Registration' page

= 1.9.1 =
* Fixed always shown banner with 'PE Terraclassic' theme in the dashboard on Wordpress 5.5

= 1.9 =
* Added option to sort categories in the 'TerraClassifieds Categories' widget by name and ID
* Added option to sort categories in the 'TerraClassifieds Categories' widget ascending and descending
* Added option to filter adverts by selling type in the search form
* Added option to translate string "Enter at least XXX characters" in the 'Registration' view
* Fixed translation issue for strings in the 'TerraClassifieds Search' widget
* Fixed padding issue for inputs and selects in the search form for some themes
* Fixed not visible buttons 'Upload Files' and 'Media Library' in the Media Manager modal (Add advert view)
* Fixed error "Invalid Post Type" in the dashboard while searching posts
* Fixed PHP warning after user registration when field "E-mail notifications for administrators" is empty
* Fixed unnecessary warnings in the SEO tab about missing pages when 'Permalink structure' in Wordpress is set to 'Plain'
* Fixed not showing warnings when pages in the SEO tab are not selected
* Fixed error 'Wrong username, email or password' in the URL: www.yourdomain.com/wp-login.php
* Fixed problem with too high permissions for the role 'TerraClassifieds user' that allowed editing posts from the frontend (deactivation and reactivation of the plugin required)
* Fixed mixed post types and wrong pagination on URL www.yourdomain.com/classified
* Fixed not visible error about too big image size and image dimensions on mobiles

= 1.8 =
* Added button 'Cancel' for add/edit form
* Added option to save advert with 'Draft' status
* Added option to filter adverts by price
* Added redirect to an advert after frontend editing
* Added option to show adverts with status 'Pending' and 'Rejected' in a 'Your adverts' view
* Added choice to disable selling type for each advert using option 'Nothing'
* Added option to disable selling types for the whole website
* Added advert ID in the single advert view
* Added option to disable ad's author in the archive / category view
* Added option to disable images
* Added option to disbale locations
* Added option to disable phone number in the single advert view
* Added option to disable contact form in the single advert view
* Added additional location fields - address and post / ZIP code
* Added 'Edit' button on advert view after editing it
* Added better description for a field 'Reply-To E-mail Address' in the plugin settings
* Added option to show HTML code for a category description in the archive view if there is a visual editor for this field enabled, ex. when Yoast SEO plugin is active
* Added option to choose pages for Terraclassifieds views like 'Add advert', 'Edit advert' etc.
* Added option to change slugs for views like 'Add advert', 'Edit advert' etc.
* Minor styling fixes in the plugin settings
* Fixed PHP warning in the 'Add advert' view if a field 'E-mail notifications for administrators' in the plugin settings is empty
* Fixed unnecessary email notification about ad expiring sent just after ad expiring
* Published date is being changed now to the current date when user will renew the advert
* Fixed no adverts in the archive view when widget 'Terraclassifieds Search' is not used
* Fixed not selected location and selling type during advert editing

= 1.7 =
* Added option to hide 'Report abuse' in the advert view
* Added option to hide 'Website URL' in the advert view
* Added option to hide 'Ad's author' in the advert view
* Added option to show default image for adverts submitted with no image
* Added option to hide username in the author's page
* Added option to set a minimum number of characters for a description
* Added information about the minimum number of characters in the 'Add advert' view
* Added option to choose selling types
* Added option to set thousand separator for a price
* Added option to set decimal separator for a price 
* Added option to set decimal points for a price
* Added option to set ordering for date elements (number, period, ago)
* Added hits (ads views counter)
* Added currency near a price selling option in the 'Add advert' view. The currency value is taken from the general price settings.
* Added option to append additional email addresses for administrator notifications ( abuse report, new advert, new user registration )
* Added option to choose behavior for expired adverts
* Fixed currency position in the 'My submissions' view
* Value less than 0.01 for the 'Price' field is forbidden in the 'Add advert' view
* Image in the widget 'Terraclassifieds Latest Ads' is now centered vertically and horizontally
* Favourite icon is now in the top right corner of the image container (archive view), even if the image is not 100% height
* Fixed not working limit of words in the archive view when the content contains newlines
* Fixed error with default WP widget 'Search' - unnecessary fields related to 'Terraclassifieds Search' widget
* Removed unnecessary hidden field with email address for an abuse contact form in the 'Single Advert' view
* Email address in the hidden field for a contact form in the 'Single Advert' view is now encoded using Base64 format
* Fixed the wrong number of items after removing advert in the 'My submissions' view

= 1.6 =
* Fixed logging to WP dashboard if it's link is located in the frontend menu
* Added option to select many types for each classified
* Fixed JS conflict with Revolution Slider
* Fixed not working layout override for child themes
* Added option to choose advert status after submitting it
* Shortcode [advert_title_link] for Email templates works now only if 'Ad status' is set to 'publish' to not generate wrong URLs
* Fixed error 'Call to undefined function is_plugin_active()'
* Fixed PHP notices related to the message about the recommended theme - PE Terraclassic
* Fixed PHP notices when no location is selected
* Fixed not sending email notifications when 'Reply-to e-mail address' in section 'Email templates' field is empty - admin email is used instead
* Fixed missing action 'Move to trash' and button 'Trash' in the Wordpress dashboard - issue related to the unnecessary user capabilities
* Fixed problem with not expired ads (wrong WP-CRON activation/deactivation method)
* Fixed problem with missing notifications about expired ads (wrong WP-CRON activation/deactivation method)
* Fixed styling issues in author view when user details are missing
* Fixed PHP error related to breadcrumbs in advert view when the ad was created in the backend without assigning a category
* CMB2 updated to version 2.6.0
* Fixed display issues for category and location in the 'Add advert' view when CMB2 is active as a separate plugin
* Fixed PHP errors related to GDPR checkboxes when 'single.php' is overridden in the theme
* Fixed missing default avatar image when 'single.php' is overriden in the theme
* Added possibility to translate adverts statuses for email notifications
* Option 'Items per page' in a 'Category view' tab works now also in a search view
* Changed text for not logged in users in 'Your adverts' view
* Added option to disable location hierarchical list. Disabling hierarchical location list can be useful (quicker) when you have a lot of nested locations.
* Changed a simple input box for a location to an input box with the AJAX search feature
* Added meta description for a single ad (content from the classified description) and classified category (content from category description)
* Added field 'Meta description text limit' in Settings->SEO for changing meta description length
* Removed unnecessary tab 'Status' in Settings
* Tested up to WP 5.3

= 1.5 =
* [fixed] locations not visible on category view once enable on TerraClassifieds settings

= 1.4 =
* [new feature] Added "Ads types" that allows marking an advert with the specific badge as well as using "types" as a filter in a search form
* [fixed] for adverts created in the backend, a title and description were missing once editing an advert on the frontend
* [fixed] translation for 'Location' field label was missing
* [removed] Removed styles responsible for buttons and search form design

= 1.3 =
* Removed custom update notification
* Fixed calling files locations without hardcoding
* Stopped calling core loading files directly
* Fixed sanitizing and escaping data
* Fixed modal window appearing for an abuse form terms
* Fixed some notices

= 1.2 =
* Fixed "Reply-to" from the email sent from contact form
* Added email field "reply-to" for other user notifications in the 'Email templates' section
* Fixed image container size in the archive view
* Added new option "None" in Category view for "Show category on the archive page" that allows not showing items (previously at least one item had to be selected)
* Added dashboard notification after successfull TerraClassifieds plugin installation

= 1.1 =
* Added location feature for adverts
* New functionality added 'Add to favourites'
* Merged 'Categories' and 'Category View' in Terraclassifieds Settings
* Added 'negotiable' text under price in the add advert view
* Added the setting option for Adding advert view 'The number of columns for subcategories list'
* Changed a way of opening subcategories in add avert view (on click)
* Added text 'see all ads by' next to a user name in single ad view
* Added option to make images upload and lo9cation fields required in the add advert view
* Font Awesome updated to 5.6.3

= 1.0 =
* Added missing translable strings for terraclassifieds.pot file
* Added 'Status' tab in TerraClassifieds settings with plugins's version
* Added 'Documentation' tab in TerraClassifieds settings with useful links

= beta 1.0 =
* Added alert with confirmation before removing advert in My submissions view
* Fixed searching in 'Terraclassifieds Seach' widget if no category selected
* Search word and category are visible as selected on the search results page
* Fixed submenu for 'Terraclassifieds Menu' widget in a single ad view
* Fixed wrong URL errors for links in Terraclassifieds pages
* Added options for change text limit and posts per page for a category view
* FontAwesome updated to version 5.3.1
* Error messages after login error instead of redirecting to wp-login.php