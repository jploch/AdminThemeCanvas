<?php namespace ProcessWire;

/**
 * AdminThemeUikit
 * 
 * @property bool $isSuperuser Is current user a superuser?
 * @property bool $isEditor Does current user have page-edit permission?
 * @property bool $isLoggedIn Is current user logged in?
 * @property bool $useOffset Use offset/margin for all Inputfields?
 * @property array $noBorderTypes Inputfield class names that should always use the noBorder option (when 100% width).
 * @property array $cardTypes Inputfield class names that should always use the card option.
 * @property array $offsetTypes Inputfield class names that should always use the offset option.
 * @property string $logoURL URL to custom logo, relative to PW installation root. 
 * @property string $cssURL URL to custom CSS file, relative to PW installation root. 
 * @property string $layout Layout type (blank=default, sidenav=multi-pane, sidenav-tree=left-tree, sidenav-tree-alt=right-tree)
 * @property int $logoAction Logo click action (0=admin root page list, 1=offcanvas nav)
 * @property string $userLabel Text containing user {vars} to use for user label in masthead (default="{Name}")
 * @property int $maxWidth Maximum layout width in pixels, or 0 for no max (default=1600).
 * @property bool|int $groupNotices Whether or not notices should be grouped by type
 * @property string $inputSize Size for input/select elements. One of "s" for small, "m" for medium (default), or "l" for large. 
 * @property bool|int $noGrid When true, do not use uk-width classes for Inputfields (use CSS percentages instead). 
 * 
 * 
 */
class AdminThemeCanvas extends AdminThemeFramework implements Module, ConfigurableModule {

	public static function getModuleInfo() {
		return array(
			"title" => "Admin Theme Canvas",
			"summary" => "A minimal theme with optimised page editor UI, based on Uikit 3",
			"href" => "https://github.com/jploch/AdminThemeCanvas",
			"version" => "0.82",
			"author" => "Jan Ploch",
			"icon" => "paint-brush",
			'autoload' => 'template=admin',
			'requires' => 'ProcessWire>=3.0.100'
		);
	}

	/**
	 * Development mode, to be used when developing this module’s code
	 * 
	 * Makes it use runtime/temporary compiled CSS files rather than the final ones. 
	 *
	 */
	const dev = false;

	/**
	 * Default logo image file (relative to this dir)
	 * 
	 */
	const logo = 'uikit/custom/images/logo.svg';

	/**
	 * sidenavType: primary navigation on left sidebar
	 * 
	 */
	const sidenavTypePrimary = 0;

	/**
	 * sidenavType: tree navigation on left sidebar
	 * 
	 */
	const sidenavTypeTree = 1;

	/**
	 * Construct and establish default module config settings
	 * 
	 */
	public function __construct() {

		parent::__construct();

		$this->set('useOffset', false);
		$this->set('cardTypes', array());
		$this->set('offsetTypes', array());
		$this->set('logoURL', '');
		$this->set('cssURL', '');
		$this->set('layout', '');
		$this->set('noBorderTypes', array()); // 'InputfieldCKEditor' is a good one for this
		$this->set('logoAction', 0);
		$this->set('userLabel', '{Name}');
		$this->set('userAvatar', 'icon.user-circle');
		$this->set('maxWidth', 1600);
		$this->set('groupNotices', true);
		$this->set('inputSize', 'm'); // m=medium (default), s=small, l=large
		$this->set('noGrid', false);

		$this->setClasses(array(
			'input' => 'uk-input',
			'input-checkbox' => 'uk-checkbox',
			'input-radio' => 'uk-radio',
			'input-password' => 'uk-input uk-form-width-medium',
			'select' => 'uk-select',
			'select-asm' => 'uk-select uk-form-small',
			'select-small' => 'uk-select uk-form-small',
			'textarea' => 'uk-textarea',
			'table' => 'uk-table uk-table-divider uk-table-justify uk-table-small',
			'dl' => 'uk-description-list uk-description-list-divider',
		));

		$this->addHookAfter('InputfieldSelector::ajaxReady', $this, 'hookInputfieldSelectorAjax');
	}

	/**
	 * Initialize and attach hooks
	 * 
	 */
	public function init() {
		parent::init();

		// if this is not the current admin theme, exit now so no hooks are attached
		if (!$this->isCurrent()) return;

		/** @var Page $page */
		$page = $this->wire('page');
		/** @var Modules $modules */
		$modules = $this->wire('modules');
		/** @var Modules $modules */
		$session = $this->wire('session');

		//add new custom css overrides, so no need to compile everytime :)
		$this->config->styles->add($this->config->urls->AdminThemeCanvas . "css/overrides.css?v=" . $this->modules->getModuleInfo('AdminThemeCanvas')['version']);

		// add classes to show icons
		$this->addHookBefore('AdminTheme::getExtraMarkup', function ($event) {
			$theme = $event->object;
			if ($this->get('nav-icons')) {
				$theme->addBodyClass($this->get('nav-icons'));
			}
			if ($this->get('hide-title')) {
				$theme->addBodyClass('hide-title');
			}
			if ($this->get('tree-panel-active')) {
				$theme->addBodyClass('tree-panel-active');
			}
			if ($this->get('breadcrump')) {
				$theme->addBodyClass($this->get('breadcrump'));
			}
		});


		$sidenav = strpos($this->layout, 'sidenav') === 0;

		// disable sidebar layout if SystemNotifications is active
		if ($sidenav && $modules->isInstalled('SystemNotifications')) {
			if (!$modules->get('SystemNotifications')->disabled) {
				$this->layout = '';
				$sidenav = false;
			}
		}

		if (!$page || $page->template != 'admin') {
			// front-end
			if ($sidenav) {
				// ensure that page edit links on front-end load the sidenav-init 
				$session->setFor('Page', 'appendEditUrl', "&layout=sidenav-init");
			}
			return;
		}

		$inputSize = $this->get('inputSize');
		if ($inputSize && $inputSize != 'm') {
			$inputClass = $inputSize === 'l' ? 'uk-form-large' : 'uk-form-small';
			foreach (array('input', 'select', 'textarea') as $name) {
				$this->addClass($name, $inputClass);
			}
		}

		if ($this->noGrid) {
			$this->addClass('body', 'AdminThemeUikitNoGrid');
		}

		$session->removeFor('Page', 'appendEditUrl');
		/** @var JqueryUI $jqueryUI */
		$jqueryUI = $modules->get('JqueryUI');
		$jqueryUI->use('panel');

		// add rendering hooks
		$this->addHookBefore('Inputfield::render', $this, 'hookBeforeRenderInputfield');
		$this->addHookBefore('Inputfield::renderValue', $this, 'hookBeforeRenderInputfield');
		$this->addHookAfter('Inputfield::getConfigInputfields', $this, 'hookAfterInputfieldGetConfigInputfields');
		$this->addHookAfter('Inputfield::getConfigAllowContext', $this, 'hookAfterInputfieldGetConfigAllowContext');
		$this->addHookAfter('MarkupAdminDataTable::render', $this, 'hookAfterTableRender');

		// hooks and settings specific to sidebar layouts
		if ($sidenav) {
			$this->addHookAfter('ProcessLogin::afterLoginURL', $this, 'hookAfterLoginURL');
			if (strpos($this->layout, 'sidenav-tree') === 0) {
				// page-edit breadcrumbs go to page editor when page tree is always in sidebar
				$this->wire('config')->pageEdit('editCrumbs', true);
			}
		}

		// add cache clearing hooks
		$this->wire('pages')->addHookAfter('saved', $this, 'hookClearCaches');
		$modules->addHookAfter('refresh', $this, 'hookClearCaches');
	}

	/**
	 * Render an extra markup region
	 *
	 * @param string $for
	 * @return mixed|string
	 *
	 */
	public function renderExtraMarkup($for) {
		$out = parent::renderExtraMarkup($for);
		if ($for === 'notices') {
		}
		return $out;
	}

	/**
	 * Test all notice types
	 * 
	 * @return bool
	 *
	 */
	public function testNotices() {
		if (parent::testNotices()) {
			$v = $this->wire('input')->get('test_notices');
			if ($v === 'group-off') $this->groupNotices = false;
			if ($v === 'group-on') $this->groupNotices = true;
			return true;
		}
		return false;
	}

	/**
	 * Get Uikit uk-width-* class for given column width
	 * 
	 * @param int $columnWidth
	 * @param array $widths
	 * @return string
	 * 
	 */
	protected function getUkWidthClass($columnWidth, array $widths) {

		static $minColumnWidth = null;

		$ukWidthClass = '1-1';

		if ($minColumnWidth === null) {
			$widthKeys = array_keys($widths);
			sort($widthKeys, SORT_NATURAL);
			$minColumnWidth = (int) reset($widthKeys);
		}

		if ($columnWidth < 10) {
			// use uk-width-1-1
		} else if ($columnWidth && $columnWidth < 100) {
			if ($columnWidth < $minColumnWidth) $columnWidth = $minColumnWidth;
			// determine column width class
			foreach ($widths as $pct => $uk) {
				$pct = (int) $pct;
				if ($columnWidth >= $pct) {
					$ukWidthClass = $uk;
					break;
				}
			}
		}

		if ($ukWidthClass === '1-1') {
			return "uk-width-1-1";
		} else {
			return "uk-width-$ukWidthClass@m";
		}
	}

	/*******************************************************************************************
	 * HOOKS
	 *
	 */

	/**
	 * Hook called before each Inputfield::render 
	 * 
	 * This updates the Inputfield classes and settings for Uikit. 
	 * 
	 * @param HookEvent $event
	 * 
	 */
	public function hookBeforeRenderInputfield(HookEvent $event) {

		/** @var Inputfield $inputfield */
		$inputfield = $event->object;
		$class = $inputfield->className();
		$formSettings = $event->wire('config')->get('InputfieldForm');
		$widths = $formSettings['ukGridWidths'];
		$columnWidth = (int) $inputfield->getSetting('columnWidth');
		$field = $inputfield->hasField;
		$isFieldset = $inputfield instanceof InputfieldFieldset;
		$isMarkup = $inputfield instanceof InputfieldMarkup;
		$isWrapper = $inputfield instanceof InputfieldWrapper && !$isFieldset && !$isMarkup;
		$ukWidthClass = 'uk-width-1-1';
		$globalInputSize = $this->get('inputSize');
		$noGrid = $this->get('noGrid');
		$themeColor = '';
		$themeBorder = '';
		$themeOffset = '';
		$themeInputSize = '';
		$themeInputWidth = '';
		$themeBlank = false;
		$hideFieldTitle = false;
		$wrapClasses = array();
		$inputClasses = array();
		$removeInputClasses = array();

		if ($inputfield instanceof InputfieldForm) {
			if ($globalInputSize == 's') {
				$inputfield->addClass('InputfieldFormSmallInputs');
			} else if ($globalInputSize == 'l') {
				$inputfield->addClass('InputfieldFormLargeInputs');
			}
			return;
		} else if ($inputfield instanceof InputfieldSubmit) {
			// button
			$inputfield->addClass('uk-width-auto uk-margin-top', 'wrapClass');
			return; // no further settings needed for button
		}

		if (!$noGrid) {
			$ukWidthClass = $this->getUkWidthClass($columnWidth, $widths);
			if ($ukWidthClass) $wrapClasses[] = $ukWidthClass;
		}

		if ($isWrapper) {
			if ($ukWidthClass != 'uk-width-1-1') $inputfield->addClass($ukWidthClass, 'wrapClass');
			return;
		} else if ($inputfield instanceof InputfieldTextarea) {
			$inputClasses[] = $this->getClass('textarea');
		} else if ($inputfield instanceof InputfieldPassword) {
			$inputClasses[] = $this->getClass('input-password');
		} else if ($inputfield instanceof InputfieldText) {
			$inputClasses[] = $this->getClass('input');
		} else if ($inputfield instanceof InputfieldInteger) {
			$inputClasses[] = $this->getClass('input');
		} else if ($inputfield instanceof InputfieldDatetime) {
			$inputClasses[] = $this->getClass('input');
		} else if ($inputfield instanceof InputfieldCheckboxes || $inputfield instanceof InputfieldCheckbox) {
			$inputClasses[] = $this->getClass('input-checkbox');
			$inputfield->addClass('uk-form-controls-text', 'contentClass');
		} else if ($inputfield instanceof InputfieldRadios) {
			$inputClasses[] = $this->getClass('input-radio');
			$inputfield->addClass('uk-form-controls-text', 'contentClass');
		} else if ($inputfield instanceof InputfieldAsmSelect) {
			$inputClasses[] = $this->getClass('select-asm');
		} else if ($inputfield instanceof InputfieldSelect && !$inputfield instanceof InputfieldHasArrayValue) {
			$inputClasses[] = $this->getClass('select');
		} else if ($inputfield instanceof InputfieldFile) {
			$themeColor = 'secondary';
		}

		if ($field) {
			// pull optional uikit settings from Field object
			$themeBorder = $field->get('themeBorder');
			$themeOffset = $field->get('themeOffset');
			$themeInputSize = $field->get('themeInputSize');
			$themeInputWidth = $field->get('themeInputWidth');
			$themeColor = $field->get('themeColor') ? $field->get('themeColor') : $themeColor;
			$themeBlank = $field->get('themeBlank');
			$hideFieldTitle = $field->get('hideFieldTitle');
		}

		// determine custom settings which may be defined with Inputfield
		if (!$themeBorder) $themeBorder = $inputfield->getSetting('themeBorder');
		if (!$themeOffset) $themeOffset = $inputfield->getSetting('themeOffset'); // || in_array($class, $this->offsetTypes);
		if (!$themeColor) $themeColor = $inputfield->getSetting('themeColor');
		if (!$themeInputSize) $themeInputSize = $inputfield->getSetting('themeInputSize');
		if (!$themeInputWidth) $themeInputWidth = $inputfield->getSetting('themeInputWidth');
		if (!$themeBlank) $themeBlank = $inputfield->getSetting('themeBlank');
		if (!$hideFieldTitle) $hideFieldTitle = $inputfield->getSetting('hideFieldTitle');

		if (!$themeBorder) {
			if ($formSettings['useBorders'] === false || in_array($class, $this->noBorderTypes)) {
				$themeBorder = (!$columnWidth || $columnWidth == 100) ? 'none' : 'hide';
			} else if (in_array($class, $this->cardTypes)) {
				$themeBorder = 'card';
			} else {
				//				$themeBorder = 'line';
			}
		}

		if ($themeInputSize && $globalInputSize != $themeInputSize) {
			if ($globalInputSize === 's') {
				$removeInputClasses[] = 'uk-form-small';
			} else if ($globalInputSize === 'l') {
				$removeInputClasses[] = 'uk-form-large';
			}
			if ($themeInputSize === 'm') {
				$inputClasses[] = 'uk-form-medium';
			} else if ($themeInputSize === 's') {
				$inputClasses[] = 'uk-form-small';
			} else if ($themeInputSize === 'l') {
				$inputClasses[] = 'uk-form-large';
			}
		}

		if ($themeInputWidth) {
			$inputWidthClasses = array(
				'xs' => 'uk-form-width-xsmall',
				's' => 'uk-form-width-small',
				'm' => 'uk-form-width-medium',
				'l' => 'uk-form-width-large',
				'f' => 'InputfieldMaxWidth',
			);
			$inputfield->removeClass($inputWidthClasses);
			if (isset($inputWidthClasses[$themeInputWidth])) {
				$inputClasses[] = $inputWidthClasses[$themeInputWidth];
				if ($themeInputWidth != 'f') $inputClasses[] = 'InputfieldSetWidth';
			}
		}

		if ($hideFieldTitle) {
			$wrapClasses[] = 'hide-field-title';
		}
		if ($themeBlank) {
			$inputClasses[] = 'uk-form-blank';
		}

		if ($themeColor) {
			$wrapClasses[] = 'InputfieldIsColor';
		}

		switch ($themeColor) {
			case 'primary':
				$wrapClasses[] = 'InputfieldIsPrimary';
				break;
			case 'secondary':
				$wrapClasses[] = 'InputfieldIsSecondary';
				break;
			case 'warning':
				$wrapClasses[] = 'InputfieldIsWarning';
				break;
			case 'danger':
				$wrapClasses[] = 'InputfieldIsError';
				break;
			case 'success':
				$wrapClasses[] = 'InputfieldIsSuccess';
				break;
			case 'highlight':
				$wrapClasses[] = 'InputfieldIsHighlight';
				break;
			case 'none':
				break;
		}

		switch ($themeBorder) {
			case 'none':
				$wrapClasses[] = 'InputfieldNoBorder';
				break;
			case 'hide':
				$wrapClasses[] = 'InputfieldHideBorder';
				break;
			case 'line':
				$wrapClasses[] = 'InputfieldShowBorder';
				break;
			case 'card':
				$wrapClasses[] = 'uk-card uk-card-default';
				break;
		}

		if ($themeOffset && $themeOffset !== 'none') {
			$wrapClasses[] = 'InputfieldIsOffset';
			if ($themeOffset === 's') {
				$wrapClasses[] = 'InputfieldIsOffsetSm';
			} else if ($themeOffset === 'l') {
				$wrapClasses[] = 'InputfieldIsOffsetLg';
			}
		}

		if (count($inputClasses)) {
			$inputfield->addClass(implode(' ', $inputClasses));
		}

		if (count($removeInputClasses)) {
			$inputfield->removeClass($removeInputClasses);
		}

		if (count($wrapClasses)) {
			$inputfield->addClass(implode(' ', $wrapClasses), 'wrapClass');
		}
	}

	/**
	 * Hook after Inputfield::getConfigInputfields() to add theme-specific configuration settings
	 * 
	 * @param HookEvent $event
	 * 
	 */
	public function hookAfterInputfieldGetConfigInputfields(HookEvent $event) {

		/** @var Inputfield $inputfield */
		$inputfield = $event->object;
		if ($inputfield instanceof InputfieldWrapper) return;
		/** @var InputfieldWrapper $inputfields */
		$inputfields = $event->return;
		if (!$inputfields instanceof InputfieldWrapper) return;
		include_once(dirname(__FILE__) . '/config.php');
		$configHelper = new AdminThemeUikitConfigHelper($this);
		$configHelper->configInputfield($inputfield, $inputfields);
	}

	/**
	 * Get fields allowed for field/template context configuration
	 * 
	 * @param HookEvent $event
	 * 
	 */
	public function hookAfterInputfieldGetConfigAllowContext(HookEvent $event) {
		$names = $event->return;
		$names[] = '_adminTheme';
		$names[] = 'themeOffset';
		$names[] = 'themeBorder';
		$names[] = 'themeColor';
		$names[] = 'themeInputSize';
		$names[] = 'themeInputWidth';
		$names[] = 'themeBlank';
		$event->return = $names;
	}

	/**
	 * Hook after MarkupAdminDataTable::render
	 * 
	 * This is primarily to add support for Uikit horizontal scrolling responsive tables,
	 * which is used instead of the default MarkupAdminDataTable responsive table.
	 * 
	 * @param HookEvent $event
	 * 
	 */
	public function hookAfterTableRender(HookEvent $event) {
		/** @var MarkupAdminDataTable $table */
		$table = $event->object;
		$classes = array();
		if ($table->responsive) $classes[] = 'pw-table-responsive uk-overflow-auto';
		if ($table->sortable) $classes[] = 'pw-table-sortable';
		if ($table->resizable) $classes[] = 'pw-table-resizable';
		if (count($classes)) {
			$class = implode(' ', $classes);
			$event->return = "<div class='$class'>$event->return</div>";
		}
	}

	/**
	 * Event called when a page is saved or modules refreshed to clear caches
	 *
	 * @param HookEvent $event
	 *
	 */
	public function hookClearCaches(HookEvent $event) {
		$page = $event->arguments(0);
		if (is_null($page) || ($page instanceof Page && $page->template == 'admin')) {
			$this->wire('session')->removeFor($this, 'prnav');
			$this->wire('session')->message("Cleared the admin theme navigation cache (primary nav)", Notice::debug);
		}
	}

	/**
	 * Hook to ProcessLogin::afterLoginURL()
	 * 
	 * @param HookEvent $event
	 * 
	 */
	public function hookAfterLoginURL(HookEvent $event) {
		$layout = $this->layout;
		if (!$layout) return;
		$url = $event->return;
		$url .= (strpos($url, '?') !== false ? '&' : '?') . "layout=$this->layout-init";
		$event->return = $url;
	}


	/*******************************************************************************************
	 * MARKUP RENDERING METHODS
	 *
	 */

	/**
	 * Render a list of breadcrumbs (list items), excluding the containing <ul>
	 *
	 * @return string
	 *
	 */
	public function ___renderBreadcrumbs() {

		if (!$this->isLoggedIn || $this->isModal) return '';
		$process = $this->wire('page')->process;
		if ($process == 'ProcessPageList') return '';
		$breadcrumbs = $this->wire('breadcrumbs');
		$out = '';

		// don't show breadcrumbs if only one of them (subjective)
		if (count($breadcrumbs) < 2 && $process != 'ProcessPageEdit') return '';

		if (strpos($this->layout, 'sidenav') === false) {
			$out = "<li>" . $this->renderQuickTreeLink() . "</li>";
		}

		foreach ($breadcrumbs as $breadcrumb) {
			$title = $breadcrumb->get('titleMarkup');
			if (!$title) $title = $this->wire('sanitizer')->entities1($this->_($breadcrumb->title));
			$out .= "<li><a href='$breadcrumb->url'>$title</a></li>";
		}

		if ($out) $out = "<ul class='uk-breadcrumb'>$out</ul>";

		return $out;
	}

	/**
	 * Render the populated “Add New” head button, or blank when not applicable
	 *
	 * @return string
	 *
	 */
	public function renderAddNewButton() {

		$items = array();

		foreach ($this->getAddNewActions() as $item) {
			$icon = $this->renderNavIcon($item['icon']);
			$items[] = "<li><a href='$item[url]'>$icon$item[label]</a></li>";
		}

		if (!count($items)) return '';

		$out = implode('', $items);
		$label = $this->getAddNewLabel();
		$icon = $this->renderIcon('angle-down');

		$out =
			"<button class='ui-button pw-dropdown-toggle'>$label $icon</button>" .
			"<ul class='pw-dropdown-menu' data-at='right bottom+1'>$out</ul>";

		return $out;
	}

	/**
	 * Render runtime notices div#notices
	 *
	 * @param Notices|bool $notices
	 * @param array $options See defaults in method
	 * @return string|array
	 *
	 */
	public function renderNotices($notices, array $options = array()) {

		$defaults = array(
			'groupByType' => $this->groupNotices ? true : false,
			'messageClass' => 'NoticeMessage uk-alert uk-alert-primary', // class for messages
			'messageIcon' => 'check-square', // default icon to show with notices
			'warningClass' => 'NoticeWarning uk-alert uk-alert-warning', // class for warnings
			'warningIcon' => 'exclamation-circle', // icon for warnings
			'errorClass' => 'NoticeError uk-alert uk-alert-danger', // class for errors
			'errorIcon' => 'exclamation-triangle', // icon for errors
			'debugClass' => 'NoticeDebug uk-alert', // class for debug items (appended)
			'debugIcon' => 'bug', // icon for debug notices
			'closeClass' => 'pw-notice-remove notice-remove', // class for close notices link <a>
			'closeIcon' => 'times', // icon for close notices link
			'listMarkup' => "<ul class='pw-notices' id='notices'>{out}</ul><!--/notices-->",
			'itemMarkup' =>
			"<li class='{class}'>" .
				"<div class='pw-container uk-container uk-container-expand'>{remove}{icon}{text}</div>" .
				"</li>"
		);

		$options = array_merge($defaults, $options);

		return parent::renderNotices($notices, $options);
	}

	/**
	 * Render a single top navigation item for the given page
	 *
	 * This function designed primarily to be called by the renderPrimaryNavItems() function.
	 *
	 * @param array $item
	 * @return string
	 *
	 */
	protected function renderPrimaryNavItem(array $item) {

		$icon = '';
		$title = '';

		//new change setup icon
		if($item['name'] == 'setup') $item['icon'] = 'wrench';
		// if($item['name'] == 'setup') $item['icon'] = 'sliders';
		//END new change setup icon

		if ($this->get('nav-icons') === 'nav-show-icons' || $this->get('nav-icons') === 'nav-show-icontext') {
			$icon = $item['icon'] ? $this->renderNavIcon($item['icon']) : '';
		}

		if ($this->get('nav-icons') === 'nav-show-text' || $this->get('nav-icons') === 'nav-show-icontext') {
			$title = $item['title'];
		}

		$out = "<li class='page-$item[id]-'>";

		if (!count($item['children'])) {
			$out .= "<a href='$item[url]'>$icon$title</a></li>";
			return $out;
		}

		$out .=
			"<a href='$item[url]' " .
			"id='prnav-page-$item[id]' " .
			"data-from='prnav-page-$item[parent_id]' " .
			"title='$item[title]'" .
			"class='pw-dropdown-toggle nav-$item[name]'>" .
			"$icon$title</a>";

		$my = 'left top';
		$at = 'left bottom';
		if (in_array($item['name'], array('access', 'setup', 'module'))) {
			$my = 'right top';
			$at = 'right bottom';
		}
		$out .=
			"<ul class='pw-dropdown-menu prnav $item[name]' data-my='$my' data-at='$at'>" .
			$this->renderPrimaryNavItemChildren($item['children']) .
			"</ul>" .
			"</li>";

		return $out;
	}

	/**
	 * Renders <li> items navigation from given nav array
	 *
	 * @param array $items
	 * @return string
	 *
	 */
	protected function renderPrimaryNavItemChildren(array $items) {
		$out = '';

		foreach ($items as $item) {

			$icon = empty($item['icon']) ? '' : $this->renderNavIcon($item['icon']);
			$title = $item['title'];
			$out .= "<li class='page-$item[id]- nav-$item[name]'>";

			if (!empty($item['children'])) {
				$out .=
					"<a class='pw-has-items' data-from='prnav-page-$item[parent_id]' href='$item[url]'>$icon$title</a>" .
					"<ul>" . $this->renderPrimaryNavItemChildren($item['children']) . "</ul>";
			} else if (!empty($item['navJSON'])) {

				$out .=
					"<a class='pw-has-items pw-has-ajax-items' " .
					"data-from='prnav-page-$item[parent_id]' " .
					"data-json='$item[navJSON]' " .
					"href='$item[url]'>$icon$title" .
					"</a>" .
					"<ul class='subnav-$item[name]'></ul>";
			} else {
				$out .= "<a href='$item[url]'>$icon$title</a>";
			}
		}
		$out .= "</li>";

		return $out;
	}

	/**
	 * Render all top navigation items, ready to populate in ul#prnav
	 *
	 * @return string
	 *
	 */
	public function renderPrimaryNavItems($option = "") {

		//$cache = self::dev ? '' : $this->wire('session')->getFor($this, 'prnav');
		//if($cache) {
		//$this->markCurrentNavItems($cache);
		//return $cache;
		//}


		$out = '';
		$items = $this->getPrimaryNavArray();

		foreach ($items as $item) {

			if ($item['name'] === "page" && $option === "notFirst") {
			} else {
				$out .= $this->renderPrimaryNavItem($item);
			}

			if ($item['name'] === "page" && $option === "first") {
				break;
			}
		}

		if (!self::dev) $this->wire('session')->setFor($this, 'prnav', $out);
		$this->markCurrentNavItems($out);

		return $out;
	}

	/**
	 * Render sidebar navigation that uses uk-nav
	 *
	 * The contents is the same as the Primary nav, except that output is prepared for sidebar.
	 *
	 * @return string
	 *
	 */
	public function renderSidebarNavItems() {

		$cache = self::dev ? '' : $this->wire('session')->getFor($this, 'sidenav');

		if ($cache) {
			$this->markCurrentNavItems($cache);
			return $cache;
		}

		$out = '';
		$items = $this->getPrimaryNavArray();
		$ukNav = "class='uk-nav-sub uk-nav-default uk-nav-parent-icon' data-uk-nav='animation: false; multiple: true;'";

		foreach ($items as $item) {

			$class = "page-$item[id]- nav-$item[name]";
			$subnav = '';

			foreach ($item['children'] as $child) {
				$icon = $child['icon'] ? $this->renderNavIcon($child['icon']) : '';
				$childClass = "page-$child[id]-";
				$childAttr = "";
				$childNav = '';
				if (count($child['children'])) {
					$childClass .= ' uk-parent';
					$childNavList = $this->renderPrimaryNavItemChildren($child['children']);
					$childIcon = $this->renderNavIcon('arrow-circle-right');
					$childNav =
						"<ul $ukNav>" .
						"<li class='pw-nav-dup'><a href='$child[url]'>$childIcon$child[title]</a></li>" .
						$childNavList .
						"</ul>";
				} else if ($child['navJSON']) {
					$childClass .= ' uk-parent';
					$childAttr = " class='pw-has-items pw-has-ajax-items' data-json='$child[navJSON]'";
					$childNav = "<ul $ukNav></ul>";
				}
				$subnav .= "<li class='$childClass'><a$childAttr href='$child[url]'>$icon$child[title]</a>";
				$subnav .= $childNav . "</li>";
			}

			if ($subnav) {
				$icon = $this->renderNavIcon($item['icon']);
				$class .= " uk-parent";
				$subnav =
					"<ul $ukNav>" .
					"<li class='pw-nav-dup'><a href='$item[url]'>$icon$item[title]</a></li>" .
					$subnav .
					"</ul>";
			}

			$out .=
				"<li class='$class'><a href='$item[url]' id='sidenav-page-$item[id]'>$item[title]</a>" .
				$subnav .
				"</li>";
		}

		// render user nav
		$userNav = $this->renderUserNavItems();
		$out .=
			"<li class='uk-parent'>" .
			"<a href='#'>" . $this->renderUserNavLabel() . "</a>" .
			"<ul $ukNav>$userNav</ul>" .
			"</li>";

		$this->wire('session')->setFor($this, 'sidenav', $out);
		$this->markCurrentNavItems($out);

		return $out;
	}

	/**
	 * Identify current items in the primary nav and add appropriate classes to them
	 *
	 * This presumes that navigation items in given $out markup use "page-[id]-" classes,
	 * which will be updated consistent with the current $page.
	 *
	 * @param $out
	 *
	 */
	protected function markCurrentNavItems(&$out) {
		$page = $this->wire('page');
		foreach ($page->parents()->and($page) as $p) {
			$out = str_replace("page-$p-", "page-$p- uk-active uk-open", $out);
		}
	}

	/**
	 * Render label for user masthead dropdown nav item
	 * 
	 * @return string
	 * 
	 */
	public function renderUserNavLabel() {
		/** @var User $user */
		$user = $this->wire('user');
		$userLabel = $this->get('userLabel');
		$userAvatar = $this->get('userAvatar');
		$defaultIcon = 'user-circle';

		if (strpos($userLabel, '{') !== false) {
			if (strpos($userLabel, '{Name}') !== false) {
				$userLabel = str_replace('{Name}', ucfirst($user->name), $userLabel);
			} else if (strpos($userLabel, '{name}') !== false) {
				$userLabel = str_replace('{name}', $user->name, $userLabel);
			}
			if (strpos($userLabel, '{') !== false) {
				$userLabel = $user->getText($userLabel, true, true);
			}
		} else {
			$userLabel = $this->wire('sanitizer')->entities($userLabel);
		}

		if ($userAvatar) {
			if ($userAvatar === 'gravatar') {
				if ($user->email) {
					$url = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($user->email))) . "?s=80&d=mm&r=g";
					$userAvatar = "<img class='pw-avatar' src='$url' alt='$user->name' />&nbsp;";
				} else {
					$userAvatar = $this->renderNavIcon("$defaultIcon fa-lg");
				}
			} else if (strpos($userAvatar, 'icon.') === 0) {
				list(, $icon) = explode('.', $userAvatar);
				$userAvatar = $this->renderNavIcon("$icon fa-lg");
			} else if (strpos($userAvatar, ':')) {
				list($fieldID, $fieldName) = explode(':', $userAvatar);
				$field = $this->wire('fields')->get($fieldName);
				if (!$field || !$field->type instanceof FieldtypeImage) {
					$field = $this->wire('fields')->get((int) $fieldID);
				}
				if ($field && $field->type instanceof FieldtypeImage) {
					$value = $user->get($field->name);
					if ($value instanceof Pageimages) $value = $value->first();
					if ($value instanceof Pageimage) {
						$value = $value->size(60, 60);
						$userAvatar	= "<img class='pw-avatar' src='$value->url' alt='$user->name' />&nbsp;";
					} else {
						$userAvatar = $this->renderNavIcon("$defaultIcon fa-lg");
					}
				} else {
					$userAvatar = '';
				}
			}
		}

		if ($userAvatar) $userLabel = $userAvatar . $userLabel;

		return $userLabel;
	}

	/**
	 * Render navigation for the “user” menu
	 *
	 * @return string
	 *
	 */
	public function renderUserNavItems() {

		$items = $this->getUserNavArray();
		$out = '';

		foreach ($items as $item) {
			$label = $this->wire('sanitizer')->entities($item['title']);
			$icon = isset($item['icon']) ? $this->renderNavIcon($item['icon']) : ' ';
			$target = isset($item['target']) ? " target='$item[target]'" : '';
			$out .= "<li><a$target href='$item[url]'>$icon$label</a></li>";
		}

		return $out;
	}


	/**
	 * Render link that opens the quick page-tree panel
	 *
	 * @param string $icon Icon to use for link (default=sitemap)
	 * @param string $text Optional text to accompany icon (default=empty)
	 * @return string
	 *
	 */
	public function renderQuickTreeLink($icon = 'tree', $text = '') {
		$tree = $this->_('Tree');
		$url = $this->wire('urls')->admin . 'page/';
		return
			"<a class='pw-panel' href='$url' data-tab-text='$tree' data-tab-icon='$icon' title='$tree'>" .
			$this->renderNavIcon($icon) . $text .
			"</a>";
	}

	/**
	 * Get the URL to the ProcessWire or brand logo (or <img> tag) 
	 * 
	 * @param array $options
	 *  - `getURL` (bool): Return only the URL? (default=false)
	 *  - `getNative` (bool): Return only the ProcessWire brand logo? (default=false)
	 *  - `alt` (string): Alt attribute for <img> tag (default=auto)
	 * @return string
	 * 
	 */
	public function getLogo(array $options = array()) {

		/** @var Config $config */
		$config = $this->wire('config');
		/** @var Sanitizer $sanitizer */
		$sanitizer = $this->wire('sanitizer');

		$defaults = array(
			'getURL' => false,
			'getNative' => false,
			'alt' => '',
		);

		$options = array_merge($defaults, $options);
		$logoURL = $this->get('logoURL');

		if (empty($logoURL) || $options['getNative'] || strpos($logoURL, '//') !== false) {
			$native = true;
			$logoURL = $config->urls($this->className()) . self::logo;
		} else {
			$logoURL = $config->urls->root . ltrim($logoURL, '/');
			$logoURL = $sanitizer->entities($logoURL);
			$native = false;
		}

		if ($options['getURL']) return $logoURL;

		$alt = $options['alt'];
		if (empty($alt) && $this->wire('user')->isLoggedin()) {
			$alt = "ProcessWire $config->version";
		}
		$class = 'pw-logo ' . ($native ? 'pw-logo-native' : 'pw-logo-custom');

		// add uk-svg if the logo is a svg file
		// this makes it stylable via CSS/LESS
		$info = pathinfo($logoURL);
		$uksvg = $info['extension'] == 'svg' ? 'uk-svg' : '';
		$img = "<img class='$class' src='$logoURL' alt='$alt' $uksvg />";

		return $img;
	}

	/**
	 * Get the URL to the ProcessWire or brand logo
	 * 
	 * @return string
	 * 
	 */
	public function getLogoURL() {
		return $this->getLogo(array('getURL' => true));
	}

	/**
	 * Get the primary Uikit CSS file to use
	 * 
	 * @return string
	 * 
	 */
	public function getUikitCSS() {
		$config = $this->wire('config');
		$cssURL = $this->get('cssURL');
		$moduleInfo = self::getModuleInfo();
		$version = $moduleInfo['version'];
		if ($cssURL) {
			if (strpos($cssURL, '//') === false) $cssURL = $config->urls->root . ltrim($cssURL, '/');
			return $this->wire('sanitizer')->entities($cssURL);
		} else if (self::dev && strpos(__FILE__, '/wire/modules/') === false) {
			return $config->urls->adminTemplates . 'uikit/custom/pw.css?v=' . $version;
		} else {
			return $config->urls->adminTemplates . 'uikit/dist/css/uikit.theme.css?v=' . $version;
		}
	}

	/**
	 * Get Javascript that must be present in the document <head>
	 *
	 * @return string
	 *
	 */
	public function getHeadJS() {

		$data = $this->wire('config')->js('adminTheme');
		if (!is_array($data)) $data = array();
		$data['logoAction'] = (int) $this->logoAction;
		$this->wire('config')->js('adminTheme', $data);

		return parent::getHeadJS();
	}

	/**
	 * Module configuration
	 * 
	 * @param InputfieldWrapper $inputfields
	 * 
	 */
	public function getModuleConfigInputfields(InputfieldWrapper $inputfields) {
		parent::getModuleConfigInputfields($inputfields);
		include_once(__DIR__ . '/config.php');
		$configHelper = new AdminThemeUikitConfigHelper($this);
		$configHelper->configModule($inputfields);
	}
}
