<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
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
 * @copyright  InfinitySoft 2010
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

/**
 * Class LayoutAdditionalSourcesRunonceJob
 * 
 * runonce update job
 * @copyright  InfinitySoft 2010
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 */
class LayoutAdditionalSourcesRunonceJob extends Backend
{
	private static $instance = null;
	
	public static function getInstance()
	{
		if (self::$instance == null) {
			self::$instance = new LayoutAdditionalSourcesRunonceJob();
		}
		return self::$instance;
	}
	
	protected function __construct()
	{
		$this->import('Database');
	}
	
	public function run($strTargetVersion)
	{
		switch ($strTargetVersion)
		{
		// update from version prior 1.0.6 stable
		case "1.5.0 stable":
			if ($this->Database->tableExists('tl_additional_source', $arrTables) && !$this->Database->fieldExists('additional_source', 'tl_layout'))
			{
				$this->Database->execute('ALTER TABLE `tl_layout` ADD `additional_source` blob NULL');
				
				// go over all themes
				$objTheme = $this->Database->execute("SELECT * FROM `tl_theme`");
				while ($objTheme->next())
				{
					// list all additional sources
					$objAdditionalSource = $this->Database->prepare("
							SELECT * FROM `tl_additional_source` WHERE `pid`=?")
						->execute($objTheme->id);
					
					// go over all theme layouts
					$objLayout = $this->Database->prepare("
							SELECT * FROM `tl_layout` WHERE `pid`=?")
						->execute($objTheme->id);
					while ($objLayout->next())
					{
						$arrAdditionalSource = array();
						$objAdditionalSource->reset();
						while ($objAdditionalSource->next())
						{
							if ($objAdditionalSource->restrictLayout)
							{
								$arrLayouts = deserialize($objAdditionalSource->layout);
								if (!in_array($objLayout->id, $arrLayouts))
								{
									continue;
								}
							}
							
							if ($objAdditionalSource->editor_only)
							{
								continue;
							}
							
							$arrAdditionalSource[] = $objAdditionalSource->id;
						}
						
						$this->Database->prepare("
								UPDATE `tl_layout` SET `additional_source`=? WHERE `id`=?")
							->execute(serialize($arrAdditionalSource), $objLayout->id);
					}
				}
				
				$_SESSION['TL_CONFIRM'][] = 'Auto-Upgrade <strong>layout_additional_sources</strong> to version <strong>1.5.0 stable</strong> completed!';
			}
			break;
		}
	}
}

?>