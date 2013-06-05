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

// register hook
$GLOBALS['TL_HOOKS']['initializeSystem'][] = array('Contao\Auth\Auth', 'fakeLogin');

// remember previous 403 error page implementation
\Contao\Auth\Auth::$page403Class = $GLOBALS['TL_PTY']['error_403'];

// register self as 403 handler
$GLOBALS['TL_PTY']['error_403'] = 'Contao\Auth\Auth';
