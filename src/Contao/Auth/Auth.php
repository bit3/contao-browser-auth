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

class Auth extends \Frontend
{
	static public $page403Class;

	public function fakeLogin()
	{
		if (TL_MODE == 'FE') {
			$rootPage = $this->getRootPageFromUrl();

			if ($rootPage && is_array($GLOBALS['BROWSER_AUTH_MODULES'])) {
				foreach ($GLOBALS['BROWSER_AUTH_MODULES'] as $authModuleClass) {
					$authModule = new $authModuleClass;
					$member = $authModule->authenticate($rootPage);

					if ($member) {
						$database = \Database::getInstance();
						$cookieName = 'FE_USER_AUTH';
						$ip = \Environment::get('ip');
						$time = time();

						// Generate the cookie hash
						$hash = sha1(session_id() . (!$GLOBALS['TL_CONFIG']['disableIpCheck'] ? $ip : '') . $cookieName);

						if ($hash == \Input::cookie($cookieName)) {
							$session = $database
								->prepare('SELECT * FROM tl_session WHERE hash=? AND name=?')
								->executeUncached($hash, $cookieName);
							$update = array();

							if ($session->numRows) {
								// Validate the session
								if ($session->sessionID != session_id()) {
									$update['sessionID'] = session_id();
								}
								if (!$GLOBALS['TL_CONFIG']['disableIpCheck'] && $session->ip != $ip) {
									$update['ip'] = $ip;
								}
								if ($session->hash != $hash) {
									$update['hash'] = $hash;
								}
								if (($session->tstamp + $GLOBALS['TL_CONFIG']['sessionTimeout']) < $time) {
									$update['tstamp'] = $time;
								}
								if (count($update)) {
									$database
										->prepare('UPDATE tl_session %s WHERE hash=? AND name=?')
										->set($update)
										->execute($hash, $cookieName);
								}
								break;
							}
						}

						// fake a new session
						$database
							->prepare(
								'INSERT INTO tl_session (pid, tstamp, name, sessionID, ip, hash)
								 VALUES (?, ?, ?, ?, ?, ?)
								 ON DUPLICATE KEY UPDATE tstamp=?, name=?, sessionID=?, ip=?'
							)
							->execute(
								$member->id, $time, $cookieName, session_id(), $ip, $hash, $time, $cookieName, session_id(), $ip
							);

						// fake authentication cookie
						$this->setCookie($cookieName, $hash, ($time + $GLOBALS['TL_CONFIG']['sessionTimeout']), null, null, false, true);
						break;
					}
				}
			}
		}
	}

	public function generate($pageId, $rootPage=null)
	{
		// Use the given root page object if available (thanks to Andreas Schempp)
		if ($rootPage === null)
		{
			$rootPage = $this->getRootPageFromUrl();
		}
		else
		{
			$rootPage = \PageModel::findPublishedById(is_integer($rootPage) ? $rootPage : $rootPage->id);
		}

		if ($rootPage && $rootPage->browser_auth_enabled) {
			if (array_key_exists($rootPage->browser_auth_module, $GLOBALS['BROWSER_AUTH_MODULES'])) {
				$this->log('Access to page ID "' . $pageId . '" denied', 'PageAuth generate()', TL_ERROR);

				$authModuleClass = $GLOBALS['BROWSER_AUTH_MODULES'][$rootPage->browser_auth_module];
				$authModule = new $authModuleClass;
				$authModule->handle403($pageId, $rootPage);
			}
			else {
				$this->log('Root page ID "' . $rootPage->id . '" does not have a suitable auth module!', 'PageAuth generate()', TL_ERROR);

				header('HTTP/1.1 501 Not Implemented');
				echo '501 Not Implemented';
				exit;
			}
		}

		$page = new static::$page403Class();
		$page->generate($pageId, $rootPage);
	}
}
