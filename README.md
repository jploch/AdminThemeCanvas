# Admin Theme Canvas
#### A minimal admin theme with optimised page editor UI, based on Uikit 3

### Features

* Five unique color options: blue, vibrant, black & pink
* Beautiful redesigned login screen
* Modern typography using Roboto Condensed
* Extended breadcrumb with edit links
* Extends the default AdminThemeUikit so you can continue using all current (and future) AdminThemeUikit features
* Option to activate theme for all users
* Compatibility with AdminOnStreoids and other third party modules


## Less distraction for editors
![alt text](https://raw.githubusercontent.com/jploch/AdminThemeCanvas/main/docs/images/edit.gif "Page edit screen")

## Direct access to page tree navigation inside dropdown 
![alt text](https://raw.githubusercontent.com/jploch/AdminThemeCanvas/main/docs/images/nav.png "Page edit screen")

## Page tree
![alt text](https://raw.githubusercontent.com/jploch/AdminThemeCanvas/main/docs/images/tree.png "Page edit screen")

## Options to customise the ui
![alt text](https://raw.githubusercontent.com/jploch/AdminThemeCanvas/main/docs/images/options.png "Page edit screen")


## Requirements

Process Wire 3.0.100 or greater

## Installation

1. Make sure the above requirements are met
1. Go to “Modules > Site > Add New“
2. Paste the Module Class Name “AdminThemeCanvas“ into the field “Add Module From Directory“
3. Click “Download And Install“
4. On the overview, click “Download And Install“ again
5. On the following screen, click “Install Now“

![alt text](https://raw.githubusercontent.com/jploch/AdminThemeCanvas/main/docs/images/installation.png "Installation using URL")


### Manually

1. Make sure the above requirements are met
2. Download the theme files from [GitHub](https://github.com/jploch/AdminThemeCanvas) 
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


## License: MIT

See included [LICENSE](https://github.com/jploch/AdminThemeCanvas/blob/master/LICENSE) file for full license text.

© [jploch.com](https://www.janploch.de) | [ProcessWire Modules](https://modules.processwire.com/authors/jploch/)
