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

namespace Contao\Auth;

interface AuthInterface
{
	/**
	 * Authenticate a member, return <em>null</em> if authentication failed.
	 *
	 * @param \PageModel $rootPage
	 *
	 * @return \MemberModel|null
	 */
	public function authenticate(\PageModel $rootPage);

	/**
	 * Handle 403 on the page.
	 *
	 * @param int $pageId
	 * @param \PageModel $rootPage
	 */
	public function handle403($pageId, \PageModel $rootPage);
}
