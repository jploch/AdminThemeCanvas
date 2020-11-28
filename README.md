# Admin Theme Canvas
A minimal admin theme with optimised page editor UI, based on Uikit 3. 
Currently this is close to stable, but users are advised to be cautious and test thoroughly.
This theme is tested in all major Browsers including IE 11, Edge (>85), Chrome (>85), Firefox (>81), Safari (>11).

### Features

* Minimal black and white admin theme 
* Fixed masthead navigation
* Direct access to page tree navigation inside page dropdown 
* Less distraction for editors
* Options to customise the ui


#### Less distraction for editors
![alt text](https://raw.githubusercontent.com/jploch/AdminThemeCanvas/main/docs/images/edit.gif "Page edit screen")

#### Direct access to page tree navigation inside dropdown 
![alt text](https://raw.githubusercontent.com/jploch/AdminThemeCanvas/main/docs/images/nav.png "Navigation")

#### Page tree
![alt text](https://raw.githubusercontent.com/jploch/AdminThemeCanvas/main/docs/images/tree.png "Page Tree")

#### Options to customise the ui
![alt text](https://raw.githubusercontent.com/jploch/AdminThemeCanvas/main/docs/images/options.png "Options")

#### Login
![alt text](https://raw.githubusercontent.com/jploch/AdminThemeCanvas/main/docs/images/login.png "Login")


## Requirements

Process Wire 3.0.100 or greater

## Installation

1. Go to “Modules > Site > Add New“
2. Paste the Module Class Name “AdminThemeCanvas“ into the field “Add Module From Directory“
3. Click “Download And Install“
4. On the overview, click “Download And Install“ again…
5. On the following screen, click “Install Now“
6. Go to your user profile page and change the theme to Admin Theme Canvas

### Manually

1. Download the theme files from [GitHub](https://github.com/jploch/AdminThemeCanvas) 
2. Copy all of the files for this module into /site/modules/AdminThemeCanvas/
3. Go to “Modules > Refresh” in your admin
4. Click “Install“ on the “AdminThemeCanvas“ Module
5. Go to your user profile page and change the theme to Admin Theme Canvas


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
