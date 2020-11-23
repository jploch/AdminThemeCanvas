# Admin Theme Boss
#### A minimal theme with optimised page editor UI, based on Uikit 3

### Features

* Five unique color options: blue, vibrant, black & pink
* Beautiful redesigned login screen
* Modern typography using Roboto Condensed
* Extended breadcrumb with edit links
* Extends the default AdminThemeUikit so you can continue using all current (and future) AdminThemeUikit features
* Option to activate theme for all users
* Compatibility with AdminOnStreoids and other third party modules


# Less distraction for editors

![alt text](https://raw.githubusercontent.com/jploch/AdminThemeCanvas/master/docs/images/edit.png "Default ProcessWire Blue Page Tree")



## Requirements

Requires AdminThemeUikit installed and activated.


## Installation

1. Make sure the above requirements are met
1. Go to “Modules > Site > Add New“
2. Paste the Module Class Name “AdminThemeCanvas“ into the field “Add Module From Directory“
3. Click “Download And Install“
4. On the overview, click “Download And Install“ again
5. On the following screen, click “Install Now“

![alt text](https://raw.githubusercontent.com/noelboss/AdminThemeCanvas/master/docs/images/installation.png "Installation using URL")


### Manually

1. Make sure the above requirements are met
2. Download the theme files from [GitHub](https://github.com/noelboss/AdminThemeCanvas) or the [ProcessWire Modules Repository](https://modules.processwire.com/modules/admin-theme-uikit/).
3. Copy all of the files for this module into /site/modules/AdminThemeCanvas/
4. Go to “Modules > Refresh” in your admin
5. Click “Install“ on the “AdminThemeCanvas“ Module


## Modify yourself

This is how you modify the themes less files on your own: Run “yarn watch“ to recompile css on file change:

	# Change into uikit folder:
	$ cd AdminThemeCanvas/uikit

	# install node_modules:
	$ yarn

	# recompile on change
	$ yarn watch

	# or with npm:
	$ npm run watch

Now you can modify the less file(s) under “AdminThemeCanvas/uikit/custom/…“.
The primary theme file is “AdminThemeCanvas/uikit/custom/theme/main.less“.


## Update to latest AdminThemeUikit

There is a [script](https://github.com/noelboss/AdminThemeCanvas/blob/master/upgrade-theme.sh) that you can run to update the dependencies. It makes a backup of the current theme, downloads the latest AdminThemeUikit and puts everything back together.

## License: MIT

See included [LICENSE](https://github.com/noelboss/AdminThemeCanvas/blob/master/LICENSE) file for full license text.

© [noelboss.com](https://www.noelboss.com) | [ProcessWire Modules](https://modules.processwire.com/authors/noelboss/)
