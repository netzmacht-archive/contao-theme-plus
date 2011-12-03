<?php

/**
 * Theme+
 * Copyright (C) 2010,2011 InfinitySoft <http://www.infinitysoft.de>
 *
 * Extension for:
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  2010,2011 InfinitySoft <http://www.infinitysoft.de>
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Theme+
 * @license    LGPL
 */


define('TL_MODE', 'BE');
include('../../initialize.php');

/**
 * Class AlphaController
 */
class AlphaController extends Backend
{
	/**
	 * @var Config
	 */
	protected $Config;

	/**
	 * Initialize the controller.
	 */
	public function __construct()
	{
		$this->import('BackendUser', 'User');
		parent::__construct();

		// load default translations
		$this->loadLanguageFile('default');
		$this->loadLanguageFile('alphaController');
	}

	/**
	 * Run the controller.
	 */
	public function run()
	{
		// user have to be authenticated
		$this->User->authenticate();

		if ($this->Input->get('useAlpha'))
		{
			$this->Config->add("\$GLOBALS['TL_CONFIG']['theme_plus_alpha_mode']", true);
			$this->redirect('contao/main.php?do=repository_manager&update=database');
		}

		$objTemplate = new BackendTemplate('be_alpha_controller');
		$objTemplate->theme = $this->getTheme();
		$objTemplate->base = $this->Environment->base;
		$objTemplate->language = $GLOBALS['TL_LANGUAGE'];
		$objTemplate->title = $GLOBALS['TL_CONFIG']['websiteTitle'];
		$objTemplate->charset = $GLOBALS['TL_CONFIG']['characterSet'];
		$objTemplate->request = ampersand($this->Environment->request);
		$objTemplate->top = $GLOBALS['TL_LANG']['MSC']['backToTop'];
		$objTemplate->be27 = !$GLOBALS['TL_CONFIG']['oldBeTheme'];
		$objTemplate->output();
	}
}

$objController = new AlphaController();
$objController->run();
