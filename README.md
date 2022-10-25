# Variable Inspector

Contributors: qriouslad  
Donate link: https://paypal.me/qriouslad  
Tags: php variables, variable dump, debug, developer  
Requires at least: 4.8  
Tested up to: 6.0.2  
Stable tag: 1.7.1  
Requires PHP: 5.6  
License: GPLv2 or later  
License URI: http://www.gnu.org/licenses/gpl-2.0.html

![](.wordpress-org/banner-772x250.png)

Inspect PHP variables on a central dashboard in wp-admin for convenient debugging.

## Description

Variable Inspector allows you to easily inspect your PHP $variables in a visually clean manner at a central dashboard in wp-admin. It aims to be an **easy and useful enough debug tool**. 

It provides **a single-line code** to inspect your variable (see "How to Use" below). Nothing is shown to site visitors nor being output on the frontend, and the **$variable content is nicely formatted for review** using [var_dump()](https://www.php.net/manual/en/function.var-dump.php), [var_export()](https://www.php.net/manual/en/function.var-export.php) and [print_r()](https://www.php.net/manual/en/function.print-r.php) on the inspector dashboard in wp-admin. 

It's a real time-saver for scenarios where [Xdebug](https://xdebug.org/) or even something like [Ray](https://myray.app/) is not ideal or simply an overkill. For example, when coding on a non-local environment via tools like [Code Snippets](https://wordpress.org/plugins/code-snippets/), [WPCodeBox](https://wpcodebox.com/), [Scripts Organizer](https://dplugins.com/products/scripts-organizer/) or [Advanced Scripts](https://www.cleanplugins.com/products/advanced-scripts/). Additionally, because it is a regular WordPress plugin, you simply install, activate and use without the need for complicated configuration.

### How to Use

Simply place the following line anywhere in your code after the `$variable_name` you'd like to inspect:

`do_action( 'inspect', [ 'variable_name', $variable_name ] );`

If you'd like to record the originating PHP file and line number, append the PHP magic constants `__FILE__` and `__LINE__` as follows.

`do_action( 'inspect', [ 'variable_name', $variable_name, __FILE__, __LINE__ ] );`

This would help you locate and clean up the inspector lines once you're done debugging.

### Give Back

* [A nice review](https://wordpress.org/plugins/variable-inspector/#reviews) would be great!
* [Give feedback](https://wordpress.org/support/plugin/variable-inspector/) and help improve future versions.
* [Github repo](https://github.com/qriouslad/variable-inspector) to contribute code.
* [Donate](https://paypal.me/qriouslad) and support my work.

### Check These Out Too

* [System Dashboard](https://wordpress.org/plugins/system-dashboard/): Central dashboard to monitor various WordPress components, processes and data, including the server.
* [Debug Log Manager](https://wordpress.org/plugins/debug-log-manager/): Log PHP, database and JavaScript errors via WP_DEBUG with one click. Conveniently create, view, filter and clear the debug.log file.
* [Code Explorer](https://wordpress.org/plugins/code-explorer/): Fast directory explorer and file/code viewer with syntax highlighting.
* [Database Admin](https://github.com/qriouslad/database-admin): Securely manage your WordPress website's database with a clean and user-friendly interface based on a custom-themed Adminer app. Only available on Github.

## Screenshots

1. The main Variable Inspector page
   ![The main Variable Inspector page](.wordpress-org/screenshot-1.png)

## Frequently Asked Questions

### How was this plugin built?

Variable Inspector was built with: [WordPress Plugin Boilerplate](https://github.com/devinvinson/WordPress-Plugin-Boilerplate/) | [wppb.me](https://wppb.me/) | [CodeStar framework](https://github.com/Codestar/codestar-framework) | [Simple Accordion](https://codepen.io/gecugamo/pen/xGLyXe) | [Fomantic UI](https://fomantic-ui.com/). It was originally inspired by [WP Logger](https://wordpress.org/plugins/wp-data-logger/).

## Changelog

### 1.7.1 (2022.10.25)

* All admin notices are now suppressed, i.e. no longer shown, on the Variable Inspector page

### 1.7.0 (2022.10.11)

* Add viewer (function) selector, e.g. print_r, that will apply to all inspection results after the selection is made and will persist after page reload. The selection is stored in wp_options table. Different viewer can still be selected for each result. Props to [@pexlechris](https://profiles.wordpress.org/pexlechris/) for [the feedback](https://wordpress.org/support/topic/awsome-plugin-that-every-developer-need-it/).

### 1.6.0 (2022.10.11)

* Add toggle to expand or collapse all inspection results. Props to [@pexlechris](https://profiles.wordpress.org/pexlechris/) for [the feedback](https://wordpress.org/support/topic/awsome-plugin-that-every-developer-need-it/).

### 1.5.0 (2022.10.09)

* Remove CodeStar framework dependency and replace with lightweight solution
* Dequeue public css and js files as they are empty and unused

### 1.4.0 (2022.08.18)

* Add Refresh button and "Auto refresh" checkbox to load latest results. Props to [@imantsk](https://github.com/imantsk) for the [code and suggestion](https://github.com/qriouslad/variable-inspector/issues/3)
* Add quick tutorial on the inspector results page to enable users to quickly reference the inspector code

### 1.3.2 (2022.05.26)

* Confirmed compatibility with WordPress 6.0

### 1.3.1 (2022.05.19)

* Fixed output via var_export()
* Better sanitization of variable name output
* Update plugin description

### 1.2.0 (2022.04.14)

* Fixed output buffering mistake causing the output of the '1' character in variable values
* NEW: implement tabbed output of var_export, var_dump and print_r

### 1.1.0 (2022.04.13)

* Fixed "Fatal error: Uncaught Error: Call to undefined function dbDelta()". Thanks to [@rashedul007](https://profiles.wordpress.org/rashedul007/) for [the fix](https://github.com/qriouslad/variable-inspector/pull/2)!

### 1.0.1 (2022.04.13)

* Initial stable release

## Upgrade Notice

None required yet.