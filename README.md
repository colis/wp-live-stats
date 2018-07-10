# WP Live Stats
This plugin creates a React widget that shows stats about a site (number of posts, users, etc).
The stats are getting updated in the widget once a minute via AJAX communicating over the REST API to a custom endpoint.

It exposes all the available stats through a custom REST API endpoint
* `/wp-json/wp-live-stats/v1/stats`

## Setup and installation
1. Upload the "wp-live-stats" folder into the "/wp-content/plugins/" directory
2. Activate the plugin through the "Plugins" menu in WordPress

## Usage
This plugin provides two different WordPress views in which the React Widget can be rendered:

- Shortcode `[wp-live-stats]`
- WordPress Widget
