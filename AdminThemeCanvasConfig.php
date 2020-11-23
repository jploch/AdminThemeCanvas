<?php

/**
 * © ICF Church – <web@icf.ch>.
 *
 * This source file is subject to the license file that is bundled
 * with this source code in the file LICENSE.
 *
 * File created/changed: 2018-10-11T14:17:03+02:00
 */

namespace ProcessWire;

if (!defined('PROCESSWIRE')) {
	die();
}

/**
 * Implementation for Uikit admin theme getConfigInputfields method.
 *
 * @param AdminTheme|AdminThemeCanvas $adminTheme
 * @param InputfieldWrapper         $inputfields
 */
class AdminThemeCanvasConfig extends ModuleConfig
{
	public function __construct()
	{
		$adminThemeUrl = $this->modules->getModuleEditUrl('AdminThemeUikit');

		$this->add([
             [
				'type'     => 'fieldset',
				'name'     => 'theme-options',
				'label'    => $this->_('Theme Options'),
				'icon'     => 'paint-brush',
                'collapsed' => Inputfield::collapsedYes,
                'themeOffset' => true,
				'children' => [
                  [
						'name'    => 'nav-icons',
						'type'    => 'radios',
						'icon'    => 'adjust',
						'label'   => $this->_('Navigation Icons'),
						'value'   => $this->get('showicons'),
						'options' => [
                            'nav-show-text'      => $this->_('Text only'),
                            'nav-show-icons'      => $this->_('Icons only'),
                            'nav-show-icontext'      => $this->_('Show both'),
						],
					],
                  [
						'name'    => 'hide-title',
						'type'    => 'checkbox',
						'icon'    => 'eye',
                        'label'         => $this->_('Hide title'),
						'checkboxLabel'   => $this->_('Hide title and add it to settings tab'),
						'value'   => $this->get('hide-title'),
					],
                      [
						'name'    => 'hide-bookmarks',
						'type'    => 'checkbox',
						'icon'    => 'bookmark',
                        'label'         => $this->_('Hide bookmarks'),
						'checkboxLabel'   => $this->_('Hide bookmarks button on page list'),
						'value'   => $this->get('hide-bookmarks'),
					],
                   [
						'name'    => 'breadcrump',
						'type'    => 'radios',
						'icon'    => 'tree',
						'label'   => $this->_('Breadcrump'),
						'value'   => $this->get('breadcrump'),
						'options' => [
                            'breadcrumb-show'      => $this->_('Show breadcrumb'),  
                            'breadcrumb-show-last'      => $this->_('Only show parent'),
                            'breadcrumb-hide'    => $this->_('Hide breadcrumb in page edit'), 
                            'breadcrumb-with-title'    => $this->_('Hide title Headline and add it to breadcrump'), 
						],
					],         
				],
			],
		]);
	}

	public function getDefaults()
	{
		return [
            'nav-icons' => 'nav-show-icons',
            'hide-title' => true,
            'hide-bookmarks' => true,
            'breadcrump' => 'breadcrumb-show',
		];
	}
}
