=== ELU Hide Admin Menu ===
Contributors: elightup, rilwis
Donate link: https://elightup.com
Tags: admin menu, admin bar, access control, hide menu, hide admin, hide wordpress
Requires at least: 4.8
Tested up to: 4.9.8
Stable tag: 1.0.0
Requires PHP: 5.3

Hide admin menu and admin bar items in WordPress admin area based on user role.

== Description ==

The **ELU Hide Admin Menu** plugin helps you hide admin menu and admin bar items in WordPress admin area based on user role. Simply select the checkboxes corresponding to menu items and user role and Save. You'll have a simplified menu for your clients and users. That will help you hide some important menu items that you don't want them to access, like settings page, account detail page, etc.

This plugin has a friendly interface and very easy to use. Everything is simple and you can understand all the functionality within 5 minutes!

The **ELU Hide Admin Menu** plugin works well with custom user roles and menu items added by other plugins and compatible with WordPress Multisite.

### Features

- Ability to hide items in admin menu and admin bar.
- Customizable by user role, including Administrator. Works with custom user roles.
- Works with menu items added by other plugins.
- Ability to export and import settings.
- Works in Multisite mode. Setup for whole network only ONCE. You don't need to go to each blog admin page to setup the plugin anymore.

### User roles / capabilities

Please note that the plugin does **not** alter or create user roles or capabilities. It simply uses the user roles as conditions to hide the admin menu / admin bar items.

It's recommended to use a role management plugin (like [Members](https://wordpress.org/plugins/members/)) to create roles and then use this plugin to control the menu visibility.

**Warning:** Be careful if you hide menus from **administrators**. You'll loose access to these menus. If you want to block access to certain menu items for other administrators, please create another role and move those users to that role. Then use the plugin to hide menus from them.

### Bug reports

If you find any problems using the plugin, please [open a new issue on Github](https://github.com/elightup/elu-hide-admin-menu).

### You might also like

- [Meta Box](https://metabox.io) - A lightweight and powerful WordPress custom fields frameworks. Built by developers, for developers.
- [GretaThemes](https://gretathemes.com) - Clean and beautiful premium WordPress themes.
- [ProWCPlugins](https://prowcplugins.com) - Professional WooCommmerce plugins.

== Installation ==

1. Go to *Plugins > Add New*
1. Search for "ELU Hide Admin Menu"
1. Click **Install** button to install the plugin
1. Click **Activate** button to activate the plugin

== Frequently Asked Questions ==

### Does the plugin alter or create user roles?

No. The plugin simply uses the user roles as conditions to hide the admin menu / admin bar items.

It's recommended to use a role management plugin (like [Members](https://wordpress.org/plugins/members/)) to create roles and then use this plugin to control the menu visibility.

### What happens if I hide menus from myself?

You'll loose access to these menus! So be careful with that, especially when you're administrators.

### How to hide menus from other admins?

Please create another role using a role management plugin (like [Members](https://wordpress.org/plugins/members/)) and move those admins to that role. Then use the plugin to hide menus from them.

== Screenshots ==

1. Hide admin menu settings
2. Hide admin bar settings
3. Import / Export

== Changelog ==

= 1.0 =
* First release

== Upgrade Notice ==
