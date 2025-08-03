# SimpleCal

## Overview
SimpleCal is an event and calendar plugin for WordPress. It's probably pretty basic, unless I've made it awesome but forgotten to update the README. It uses custom post types and some metadata to store event details, and then it displays them as a ~~widget~~ WordPress Block element or a shortcode.

It probably supports calendar views and agenda views, and it may even display detailed event data with a custom single post template. Who knows! Not me because I don't plan _shit_ when I code.

Probably a good time to say this is all unsupported and I wish you all the best with implementing this in your environment.

I love you all.

## Major To-Dos
* ~~Pagination~~ DONE. AND AWESOME.
* ~~Archive page~~ DONE. AND MEDIOCRE.
* ~~Default to current month if show previous events is selected~~ FIXED.
* ~~Fix the block templates to do things the right way~~ DONE.
* ~~Refactor to use WP API rather than AJAX~~
* Split plugin into a block theme and a classic version... no sense in trying to support both in a single plugin
* Backport to widget
* ~~Create a compact layout for agenda view~~ DONE.
* Option to delete events on uninstall
* Better styling
* Custom CSS support
* Option to add event to personal calendar (iCal, etc.)
* Option to highlight upcoming events
* Filter/search
* Support for "Doors" time
* A choice of mapping services (and maybe embedded maps???)
* Event pricing
* "Members Event" tag
* Support for "TBD" events within a month/week
* ~~General tag support~~ DONE.
* Option to show event only to logged-in users
* ~~Option to set public and private (logged-in) locations~~
* Date (month) selection
* Calendar view
* Option to auto-delete old events
* Infinite Scroll??
* Sync to/from external calendars (Very unlikely)
* Recurring events

## Minor To-Dos
* Move settings over to a block element instead of the Inspector Panel
* Option to display metadata icons
* Option to display thumbnail to the right or the left in agenda view
* Option to display month/year "headers" in agenda layout 2