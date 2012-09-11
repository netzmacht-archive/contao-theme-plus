<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class ThemePlusRunonce
 */
class ThemePlusRunonce extends Frontend
{

	/**
	 * Initialize the object
	 */
	public function __construct()
	{
		parent::__construct();

		$this->import('Database');
	}


	/**
	 * Update a configuration entry.
	 *
	 * @param string $strKey
	 */
	protected function updateConfig($strKey, $strValue)
	{
		$GLOBALS['TL_CONFIG'][$strKey] = $strValue;
		$strKey                        = sprintf("\$GLOBALS['TL_CONFIG']['%s']", $strKey);
		$this->Config->update($strKey, $strValue);
	}


	/**
	 * Delete a configuration entry.
	 *
	 * @param string $strKey
	 */
	protected function deleteConfig($strKey)
	{
		$strKey = sprintf("\$GLOBALS['TL_CONFIG']['%s']", $strKey);
		$this->Config->delete($strKey);
	}


	/**
	 * Test if yui compressor exists.
	 */
	protected function testYUI()
	{
		$strCmd = escapeshellcmd($GLOBALS['TL_CONFIG']['additional_sources_yui_cmd']);
		$proc   = proc_open(
			$strCmd,
			array(
				0 => array("pipe", "r"),
				1 => array("pipe", "w"),
				2 => array("pipe", "w")
			),
			$arrPipes);
		if ($proc === false) {
			return false;
		}
		// close stdin
		fclose($arrPipes[0]);
		// read and close stdout
		$strOut = stream_get_contents($arrPipes[1]);
		fclose($arrPipes[1]);
		// read and close stderr
		$strErr = stream_get_contents($arrPipes[2]);
		fclose($arrPipes[2]);
		// wait until process terminates
		proc_close($proc);

		// no error means, the command was found and successfully executed
		return !strlen($strErr);
	}

	public function run()
	{
		try {
			$this->loadLanguageFile('theme_plus');
			$this->upgrade1_5();
			$this->upgrade1_6();
			$this->upgrade2_0();
			$this->upgrade2_2();
			$this->upgrade3_0();
			$this->checkCompression();
		} catch(Exception $e) {
			$this->log($e->getMessage() . ";\n" . $e->getTraceAsString(), $e->getFile(), TL_ERROR);
		}
	}


	/**
	 * Database upgrade to 1.5 [layout_additional_source]
	 */
	protected function upgrade1_5()
	{
		if ($this->Database->tableExists('tl_additional_source', null, true) && !$this->Database->fieldExists('additional_source', 'tl_layout', true)) {
			$this->Database->executeUncached('ALTER TABLE tl_layout ADD additional_source blob NULL');

			// go over all themes
			$objTheme = $this->Database->executeUncached("SELECT * FROM tl_theme");
			while ($objTheme->next())
			{
				// list all additional sources
				$objAdditionalSource = $this->Database->prepare("
						SELECT * FROM tl_additional_source WHERE pid=?")
					->executeUncached($objTheme->id);

				// go over all theme layouts
				$objLayout = $this->Database->prepare("
						SELECT * FROM tl_layout WHERE pid=?")
					->executeUncached($objTheme->id);
				while ($objLayout->next())
				{
					$arrAdditionalSource = array();
					$objAdditionalSource->reset();
					while ($objAdditionalSource->next())
					{
						if ($objAdditionalSource->restrictLayout) {
							$arrLayouts = deserialize($objAdditionalSource->layout);
							if (!in_array($objLayout->id, $arrLayouts)) {
								continue;
							}
						}

						if ($objAdditionalSource->editor_only) {
							continue;
						}

						$arrAdditionalSource[] = $objAdditionalSource->id;
					}

					$this->Database->prepare("
							UPDATE tl_layout SET additional_source=? WHERE id=?")
						->executeUncached(serialize($arrAdditionalSource), $objLayout->id);
				}
			}

			# add backend message
			$GLOBALS['TL_HOOKS']['parseBackendTemplate'][] = array('ThemePlusRunonce', 'addUpgrade1dot5Message');
		}
	}


	/**
	 * Configuration upgrade to 1.6 [layout_additional_source]
	 */
	protected function upgrade1_6()
	{
		/**
		 * Convert old setting.
		 */
		if (isset($GLOBALS['TL_CONFIG']['yui_compression_disabled'])) {
			if ($GLOBALS['TL_CONFIG']['yui_compression_disabled']) {
				$this->updateConfig('additional_sources_css_compression', 'none');
				$this->updateConfig('additional_sources_js_compression', 'none');
			}
			$this->deleteConfig('yui_compression_disabled');
		}
		if (isset($GLOBALS['TL_CONFIG']['yui_cmd'])) {
			$this->updateConfig('additional_sources_yui_cmd', $GLOBALS['TL_CONFIG']['yui_cmd']);
			$this->deleteConfig('yui_cmd');
		}
		if (isset($GLOBALS['TL_CONFIG']['gz_compression_disabled'])) {
			$this->updateConfig('additional_sources_gz_compression_disabled', $GLOBALS['TL_CONFIG']['gz_compression_disabled']);
			$this->deleteConfig('gz_compression_disabled');
		}
	}


	/**
	 * Configuration and database upgrade to 2.0
	 */
	protected function upgrade2_0()
	{
		# upgrade from layout_additional_sources
		if ($this->Database->tableExists('tl_additional_source', null, true)) {
			# update configuration from layout_additional_sources to theme+

			# update css compression mode
			if (!empty($GLOBALS['TL_CONFIG']['additional_sources_css_compression'])) {
				# less mode
				if (preg_match('#^less.js\+(.*)$#', $GLOBALS['TL_CONFIG']['additional_sources_css_compression'], $m)) {
					// less is in precompiled mode
					$this->updateConfig('theme_plus_lesscss_mode', 'less.js+pre');

					if ($m[1] != 'pre') {
						if (!isset($GLOBALS['TL_CONFIG']['default_css_minimizer']) || $GLOBALS['TL_CONFIG']['default_css_minimizer'] == 'none') {
							$this->updateConfig('default_css_minimizer', $m[1]);
						}
					}
				}

				# no less mode
				else if ($GLOBALS['TL_CONFIG']['additional_sources_css_compression'] != 'less.js') {
					if (empty($GLOBALS['TL_CONFIG']['default_css_minimizer']) || $GLOBALS['TL_CONFIG']['default_css_minimizer'] == 'none') {
						$this->updateConfig('default_css_minimizer', $GLOBALS['TL_CONFIG']['additional_sources_css_compression']);
					}
				}

			}

			# update javascript compression mode
			if (!empty($GLOBALS['TL_CONFIG']['additional_sources_js_compression'])) {
				if (empty($GLOBALS['TL_CONFIG']['default_js_minimizer']) || $GLOBALS['TL_CONFIG']['default_js_minimizer'] == 'none') {
					$this->updateConfig('default_js_minimizer', $GLOBALS['TL_CONFIG']['additional_sources_js_compression']);
				}

			}

			# update gzip disable mode
			if (isset($GLOBALS['TL_CONFIG']['additional_sources_gz_compression_disabled']) && !$GLOBALS['TL_CONFIG']['additional_sources_gz_compression_disabled']) {
				$this->updateConfig('gzipScripts', 1);
			}

			# delete outdated settings
			$this->deleteConfig('additional_sources_combination');
			$this->deleteConfig('additional_sources_css_compression');
			$this->deleteConfig('additional_sources_js_compression');
			$this->deleteConfig('additional_sources_gz_compression_disabled');
			$this->deleteConfig('additional_sources_hide_cssmin_message');
			$this->deleteConfig('additional_sources_hide_jsmin_message');

			# update database

			# create table tl_theme_plus_file
			$blnTableThemePlusFileExists = false; // flag required because tableExists caching is faulty, do not remove!
			if (!$this->Database->tableExists('tl_theme_plus_file', null, true)) {
				try {
					$this->Database->executeUncached("CREATE TABLE `tl_theme_plus_file` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `type` varchar(32) NOT NULL default '',
  `js_file` blob NULL,
  `js_url` blob NULL,
  `css_file` blob NULL,
  `css_url` blob NULL,
  `media` blob NULL,
  `editor_integration` blob NULL,
  `force_editor_integration` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
					$blnTableThemePlusFileExists = true;
				} catch (Exception $e) {
					try {
						$this->log($e->getMessage(), 'ThemePlusRunonce::upgrade2_0', TL_ERROR);
					} catch (Exception $e) {
					}
				}
			}
			else
			{
				$blnTableThemePlusFileExists = true;
			}

			# convert from tl_additional_source to tl_theme_plus_file
			if ($blnTableThemePlusFileExists && $this->Database->tableExists('tl_additional_source', null, true)) {
				try {
					$this->Database->executeUncached("INSERT INTO tl_theme_plus_file (id,pid,sorting,tstamp,type,js_file,js_url,css_file,css_url,media,editor_integration,force_editor_integration)
							SELECT id,pid,sorting,tstamp,type,js_file,js_url,css_file,css_url,media,editor_integration,force_editor_integration FROM tl_additional_source");
					$objFile = $this->Database->executeUncached("SELECT * FROM tl_theme_plus_file");
					while ($objFile->next())
					{
						$arrMedia = deserialize($objFile->media, true);
						if (count($arrMedia)) {
							$this->Database->prepare("UPDATE tl_theme_plus_file SET media=? WHERE id=?")
								->executeUncached(implode(',', $arrMedia), $objFile->id);
						}
					}
				} catch (Exception $e) {
					try {
						$this->log($e->getMessage(), 'ThemePlusRunonce::upgrade2_0', TL_ERROR);
					} catch (Exception $e) {
					}
				}
			}

			# drop tl_additional_source
			if ($this->Database->tableExists('tl_additional_source', null, true)) {
				try {
					$this->Database->executeUncached("DROP TABLE tl_additional_source");
				} catch (Exception $e) {
					try {
						$this->log($e->getMessage(), 'ThemePlusRunonce::upgrade2_0', TL_ERROR);
					} catch (Exception $e) {
					}
				}
			}

			# convert tl_layout.additional_source to tl_layout.theme_plus_files
			if ($this->Database->fieldExists('additional_source', 'tl_layout', true) && !$this->Database->fieldExists('theme_plus_files', 'tl_layout', true)) {
				try {
					$this->Database->executeUncached("ALTER TABLE tl_layout CHANGE additional_source theme_plus_files blob NULL");
				} catch (Exception $e) {
					try {
						$this->log($e->getMessage(), 'ThemePlusRunonce::upgrade2_0', TL_ERROR);
					} catch (Exception $e) {
					}
				}
			}

			# convert tl_page.additional_source to tl_page.theme_plus_files
			$blnFieldThemePlusFilesTLPageExists = $this->Database->fieldExists('theme_plus_files', 'tl_page', true);
			if ($this->Database->fieldExists('additional_source', 'tl_page', true) && !$blnFieldThemePlusFilesTLPageExists) {
				try {
					$this->Database->executeUncached("ALTER TABLE tl_page CHANGE additional_source theme_plus_files blob NULL");
					$blnFieldThemePlusFilesTLPageExists = true;
				} catch (Exception $e) {
					try {
						$this->log($e->getMessage(), 'ThemePlusRunonce::upgrade2_0', TL_ERROR);
					} catch (Exception $e) {
					}
				}
			}

			# add new include flag fields
			$blnFieldThemePlusIncludeFilesTLPageExists = $this->Database->fieldExists('theme_plus_include_files', 'tl_page', true);
			if (!$blnFieldThemePlusIncludeFilesTLPageExists) {
				try {
					$this->Database->executeUncached("ALTER TABLE tl_page ADD theme_plus_include_files char(1) NOT NULL default ''");
					$blnFieldThemePlusIncludeFilesTLPageExists = true;
				} catch (Exception $e) {
					try {
						$this->log($e->getMessage(), 'ThemePlusRunonce::upgrade2_0', TL_ERROR);
					} catch (Exception $e) {
					}
				}
			}

			# set the include flag field
			if ($blnFieldThemePlusIncludeFilesTLPageExists && $blnFieldThemePlusFilesTLPageExists) {
				try {
					$objPage = $this->Database->executeUncached("SELECT * FROM tl_page");
					$arrIds  = array();
					while ($objPage->next())
					{
						$arrFiles = deserialize($objPage->theme_plus_files, true);
						if (count($arrFiles)) {
							$arrIds[] = $objPage->id;
						}
					}
					if (count($arrIds)) {
						$this->Database->executeUncached("UPDATE tl_page SET theme_plus_include_files='1' WHERE id IN (" . implode(',', $arrIds) . ")");
					}
				} catch (Exception $e) {
					try {
						$this->log($e->getMessage(), 'ThemePlusRunonce::upgrade2_0', TL_ERROR);
					} catch (Exception $e) {
					}
				}
			}

			# remove layout_additional_sources
			try {
				# check repository installation
				$objInstall = $this->Database->executeUncached("SELECT * FROM tl_repository_installs WHERE extension='layout_additional_sources'");
				if ($objInstall->next()) {
					$this->Database->prepare("DELETE FROM tl_repository_instfiles WHERE pid=?")
						->executeUncached($objInstall->id);
					$this->Database->prepare("DELETE FROM tl_repository_installs WHERE id=?")
						->executeUncached($objInstall->id);
				}
			} catch (Exception $e) {
				try {
					$this->log($e->getMessage(), 'ThemePlusRunonce::upgrade2_0', TL_ERROR);
				} catch (Exception $e) {
				}
			}

			if (is_dir(TL_ROOT . '/system/modules/layout_additional_sources')) {
				try {
					$this->import('Files');
					# remove module directory
					$this->Files->rrdir('system/modules/layout_additional_sources');
				} catch (Exception $e) {
					try {
						$this->log($e->getMessage(), 'ThemePlusRunonce::upgrade2_0', TL_ERROR);
					} catch (Exception $e) {
					}
				}
			}

			# remove callbacks ...
			# ... from generatePage hook
			if (is_array($GLOBALS['TL_HOOKS']['generatePage'])) {
				foreach ($GLOBALS['TL_HOOKS']['generatePage'] as $k=> $v)
				{
					if ($v[0] == 'LayoutAdditionalSources') {
						unset($GLOBALS['TL_HOOKS']['generatePage'][$k]);
					}
				}
			}
			# ... from replaceInsertTags hook
			if (is_array($GLOBALS['TL_HOOKS']['replaceInsertTags'])) {
				foreach ($GLOBALS['TL_HOOKS']['replaceInsertTags'] as $k=> $v)
				{
					if ($v[0] == 'LayoutAdditionalSources') {
						unset($GLOBALS['TL_HOOKS']['replaceInsertTags'][$k]);
					}
				}
			}
			# ... from parseBackendTemplate hook
			if (is_array($GLOBALS['TL_HOOKS']['parseBackendTemplate'])) {
				foreach ($GLOBALS['TL_HOOKS']['parseBackendTemplate'] as $k=> $v)
				{
					if ($v[0] == 'LayoutAdditionalSourcesBackend') {
						unset($GLOBALS['TL_HOOKS']['parseBackendTemplate'][$k]);
					}
				}
			}

			# remove easy_themes integration
			if (isset($GLOBALS['TL_EASY_THEMES_MODULES']['additional_source'])) {
				unset($GLOBALS['TL_EASY_THEMES_MODULES']['additional_source']);
			}

			# add backend message
			$GLOBALS['TL_HOOKS']['parseBackendTemplate'][] = array('ThemePlusRunonce', 'addUpgrade2dot0Message');
		}
	}


	/**
	 * Configuration and database upgrade to 2.2
	 */
	protected function upgrade2_2()
	{
		if (version_compare(VERSION, '2.11', '>=') &&
			$this->Database->fieldExists('theme_plus_exclude_contaocss', 'tl_layout') &&
			$this->Database->fieldExists('skipFramework', 'tl_layout')
		) {
			// set skipFramework if theme_plus_exclude_contaocss was enabled
			$this->Database
				->query('UPDATE tl_layout SET skipFramework=\'1\' WHERE theme_plus_exclude_contaocss=\'1\'');
			// disable theme_plus_exclude_contaocss to prevent overwrite of skipFramework next time
			$this->Database
				->query('UPDATE tl_layout SET theme_plus_exclude_contaocss=\'\' WHERE theme_plus_exclude_contaocss=\'1\'');
		}
	}


	/**
	 * Database upgrade to 3.0
	 */
	protected function upgrade3_0()
	{
		if ($this->Database->tableExists('tl_theme_plus_file', null, false)) {
			// Upgrade from 3.0 alpha1
			if (!$this->Database->fieldExists('sorting', 'tl_theme_plus_file', false) &&
				$this->Database->fieldExists('theme_plus_stylesheets', 'tl_layout', false) &&
				$this->Database->fieldExists('theme_plus_javascripts', 'tl_layout', false)) {

				$this->Database->query('ALTER TABLE `tl_theme_plus_file` ADD `sorting` int(10) unsigned NOT NULL default \'0\'');

				$arrCssSorting = array();
				$arrJsSorting = array();

				// collect all files, set index count to 0 for all
				$objFile = $this->Database->query('SELECT * FROM tl_theme_plus_file', true);
				while ($objFile->next()) {
					if (preg_match('#^css_#', $objFile->type)) {
						$arrCssSorting[$objFile->id] = 0;
					}
					else if (preg_match('#^js_#', $objFile->type)) {
						$arrJsSorting[$objFile->id] = 0;
					}
				}

				// collect the index count of each file
				// (may not be the best way to find out the sorting of files)
				$objLayout = $this->Database->query('SELECT * FROM tl_layout', false);
				while ($objLayout->next()) {
					$arrFiles = deserialize($objLayout->theme_plus_stylesheets, true);
					foreach ($arrFiles as $index=>$id) {
						if (isset($arrCssSorting[$id])) {
							$arrCssSorting[$id] += $index + 1;
						}
					}
					$arrFiles = deserialize($objLayout->theme_plus_javascripts, true);
					foreach ($arrFiles as $index=>$id) {
						if (isset($arrJsSorting[$id])) {
							$arrJsSorting[$id] += $index + 1;
						}
					}
				}

				// put unused files to the end
				$intMax = max($arrCssSorting);
				foreach ($arrCssSorting as $id=>$count) {
					if ($count == 0) {
						$arrCssSorting[$id] = ++$intMax;
					}
				}
				$intMax = max($arrJsSorting);
				foreach ($arrJsSorting as $id=>$count) {
					if ($count == 0) {
						$arrJsSorting[$id] = ++$intMax;
					}
				}

				// sort the files by index position count
				asort($arrCssSorting);
				asort($arrJsSorting);

				// update sorting
				$intSorting = 128;
				foreach ($arrCssSorting as $id=>$count) {
					$this->Database
						->prepare('UPDATE tl_theme_plus_file SET sorting=? WHERE id=?')
						->execute($intSorting, $id);
					$intSorting *= 2;
				}
				$intSorting = 128;
				foreach ($arrJsSorting as $id=>$count) {
					$this->Database
						->prepare('UPDATE tl_theme_plus_file SET sorting=? WHERE id=?')
						->execute($intSorting, $id);
					$intSorting *= 2;
				}
			}

			if ($this->Database->fieldExists('theme_plus_files', 'tl_layout', false) &&
				!$this->Database->fieldExists('theme_plus_stylesheets', 'tl_layout', false) &&
				!$this->Database->fieldExists('theme_plus_javascripts', 'tl_layout', false)) {
				// create new fields
				$this->Database->query('ALTER TABLE `tl_layout` ADD `theme_plus_stylesheets` blob NULL');
				$this->Database->query('ALTER TABLE `tl_layout` ADD `theme_plus_javascripts` blob NULL');
				// set values
				$objLayout = $this->Database->query('SELECT * FROM tl_layout', false);
				while ($objLayout->next()) {
					$arrFiles = deserialize($objLayout->theme_plus_files, true);

					if (count($arrFiles)) {
						$arrCSS = $this->Database
							->query('SELECT * FROM tl_theme_plus_file WHERE id IN (' . implode(',', $arrFiles) . ') AND type LIKE \'css_%\'')
							->fetchEach('id');
						$arrJS = $this->Database
							->query('SELECT * FROM tl_theme_plus_file WHERE id IN (' . implode(',', $arrFiles) . ') AND type LIKE \'js_%\'')
							->fetchEach('id');

						$this->Database
							->prepare('UPDATE tl_layout SET theme_plus_stylesheets=?, theme_plus_javascripts=? WHERE id=?')
							->execute(serialize($arrCSS), serialize($arrJS), $objLayout->id);
					}
				}

				// drop obsolet field
				$this->Database->query('ALTER TABLE `tl_layout` DROP `theme_plus_files`');
			}

			if ($this->Database->fieldExists('theme_plus_files', 'tl_page', false) &&
				!$this->Database->fieldExists('theme_plus_stylesheets', 'tl_page', false) &&
				!$this->Database->fieldExists('theme_plus_javascripts', 'tl_page', false)) {
				// create new fields
				$this->Database->query('ALTER TABLE `tl_page` ADD `theme_plus_stylesheets` blob NULL');
				$this->Database->query('ALTER TABLE `tl_page` ADD `theme_plus_javascripts` blob NULL');
				// set values
				$objPage = $this->Database->query('SELECT * FROM tl_page', false);
				while ($objPage->next()) {
					$arrFiles = deserialize($objPage->theme_plus_files, true);

					if (count($arrFiles)) {
						$arrCSS = $this->Database
							->query('SELECT * FROM tl_theme_plus_file WHERE id IN (' . implode(',', $arrFiles) . ') AND type LIKE \'css_%\'')
							->fetchEach('id');
						$arrJS = $this->Database
							->query('SELECT * FROM tl_theme_plus_file WHERE id IN (' . implode(',', $arrFiles) . ') AND type LIKE \'js_%\'')
							->fetchEach('id');

						$this->Database
							->prepare('UPDATE tl_page SET theme_plus_stylesheets=?, theme_plus_javascripts=? WHERE id=?')
							->execute(serialize($arrCSS), serialize($arrJS), $objPage->id);
					}
				}

				// drop obsolet field
				$this->Database->query('ALTER TABLE `tl_page` DROP `theme_plus_files`');
			}

			if ($this->Database->fieldExists('theme_plus_files_noinherit', 'tl_page', false) &&
				!$this->Database->fieldExists('theme_plus_stylesheets_noinherit', 'tl_page', false) &&
				!$this->Database->fieldExists('theme_plus_javascripts_noinherit', 'tl_page', false)) {
				// create new fields
				$this->Database->query('ALTER TABLE `tl_page` ADD `theme_plus_stylesheets_noinherit` blob NULL');
				$this->Database->query('ALTER TABLE `tl_page` ADD `theme_plus_javascripts_noinherit` blob NULL');
				// set values
				$objPage = $this->Database->query('SELECT * FROM tl_page', false);
				while ($objPage->next()) {
					$arrFiles = deserialize($objPage->theme_plus_files_noinherit, true);

					if (count($arrFiles)) {
						$arrCSS = $this->Database
							->query('SELECT * FROM tl_theme_plus_file WHERE id IN (' . implode(',', $arrFiles) . ') AND type LIKE \'css_%\'')
							->fetchEach('id');
						$arrJS = $this->Database
							->query('SELECT * FROM tl_theme_plus_file WHERE id IN (' . implode(',', $arrFiles) . ') AND type LIKE \'js_%\'')
							->fetchEach('id');

						$this->Database
							->prepare('UPDATE tl_page SET theme_plus_stylesheets_noinherit=?, theme_plus_javascripts_noinherit=? WHERE id=?')
							->execute(serialize($arrCSS), serialize($arrJS), $objPage->id);
					}
				}

				// drop obsolet field
				$this->Database->query('ALTER TABLE `tl_page` DROP `theme_plus_files_noinherit`');
			}
		}
	}


	/**
	 * Check the available compression methods.
	 */
	protected function checkCompression()
	{
		/**
		 * Test if yui is available, otherwise disable css compression.
		 */
		if ($GLOBALS['TL_CONFIG']['additional_sources_css_compression'] == 'yui'
			|| $GLOBALS['TL_CONFIG']['additional_sources_js_compression'] == 'yui'
		) {
			if (!$this->testYUI()) {
				if ($GLOBALS['TL_CONFIG']['additional_sources_css_compression'] == 'yui') {
					// try cssmin compression instead
					$this->updateConfig('additional_sources_css_compression', 'cssmin');
				}
				if ($GLOBALS['TL_CONFIG']['additional_sources_js_compression'] == 'yui') {
					$this->updateConfig('additional_sources_js_compression', 'jsmin');
				}
			}
		}

		/**
		 * Test if cssmin is available, otherwise disable css compression.
		 */
		if ($GLOBALS['TL_CONFIG']['additional_sources_css_compression'] == 'cssmin') {
			if (!file_exists(TL_ROOT . '/system/libraries/CssMinimizer.php')) {
				$this->updateConfig('additional_sources_css_compression', 'none');
			}
		}

		/**
		 * Test if jsmin is available, otherwise try dep.
		 */
		if ($GLOBALS['TL_CONFIG']['additional_sources_js_compression'] == 'jsmin') {
			if (!file_exists(TL_ROOT . '/system/libraries/JsMinimizer.php')) {
				$this->updateConfig('additional_sources_js_compression', 'dep');
			}
		}

		/**
		 * Test if dep is available, otherwise disable js compression.
		 */
		if ($GLOBALS['TL_CONFIG']['additional_sources_js_compression'] == 'dep') {
			if (!file_exists(TL_ROOT . '/system/libraries/DeanEdwardsPacker.php')) {
				$this->updateConfig('additional_sources_js_compression', 'none');
			}
		}
	}


	/**
	 * Add message to backend.
	 */
	public function addUpgrade1dot5Message($strContent, $strTemplate)
	{
		if ($strTemplate == 'be_main') {
			$strContent = str_replace(
				'<div id="container">',
				sprintf('<p style="border: 1px solid #BBBBBB; margin: 18px auto; text-align: left; width: 914px;" class="tl_info">%s</p><div id="container">', $GLOBALS['TL_LANG']['theme_plus']['upgrade1.5']),
				$strContent);
		}
		return $strContent;
	}


	/**
	 * Add message to backend.
	 */
	public function addUpgrade2dot0Message($strContent, $strTemplate)
	{
		if ($strTemplate == 'be_main') {
			$strContent = str_replace(
				'<div id="container">',
				sprintf('<p style="border: 1px solid #BBBBBB; margin: 18px auto; text-align: left; width: 914px;" class="tl_info">%s</p><div id="container">', $GLOBALS['TL_LANG']['theme_plus']['upgrade2.0']),
				$strContent);
		}
		return $strContent;
	}
}

/**
 * Instantiate controller
 */
$objThemePlusRunonce = new ThemePlusRunonce();
$objThemePlusRunonce->run();
