<?php

/**
 * Browser authentication mechanism for Contao.
 * Copyright (C) 2013 Tristan Lins
 *
 * PHP version 5
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    auth
 * @license    LGPL-3.0+
 * @filesource
 */

$this->loadLanguageFile('browser_auth');

/**
 * Table tl_page
 */
// do not remove, it is necessary to keep the order of selectors!
$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'browser_auth_enabled';
$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'browser_auth_module';
MetaPalettes::appendAfter('tl_page', 'root', 'protected', array('browser_auth' => array('browser_auth_enabled')));
$GLOBALS['TL_DCA']['tl_page']['metasubpalettes']['browser_auth_enabled'] = array('browser_auth_module');

$GLOBALS['TL_DCA']['tl_page']['fields']['browser_auth_enabled'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_page']['browser_auth_enabled'],
	'exclude'   => true,
	'inputType' => 'checkbox',
	'eval'      => array('submitOnChange' => true, 'tl_class' => 'm12 w50'),
	'sql'       => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['browser_auth_module'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_page']['browser_auth_module'],
	'exclude'   => true,
	'inputType' => 'select',
	'options'   => is_array($GLOBALS['BROWSER_AUTH_MODULES']) ? array_keys($GLOBALS['BROWSER_AUTH_MODULES']) : array(),
	'eval'      => array('mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true, 'tl_class' => 'w50'),
	'reference' => &$GLOBALS['TL_LANG']['browser_auth'],
	'sql'       => "varchar(32) NOT NULL default ''"
);
