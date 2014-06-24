<?php

/**
 * Theme+ - Theme extension for the Contao Open Source CMS
 *
 * Copyright (C) 2013 bit3 UG <http://bit3.de>
 *
 * @package    Theme+
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @link       http://www.themeplus.de
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

use Bit3\Contao\ThemePlus;

use Template;
use FrontendTemplate;
use Bit3\Contao\ThemePlus\DataContainer\File;
use Bit3\Contao\ThemePlus\Model\StylesheetModel;
use Bit3\Contao\ThemePlus\Model\JavaScriptModel;
use Bit3\Contao\ThemePlus\Model\VariableModel;
use ContaoAssetic\AsseticFactory;
use Assetic\Asset\AssetInterface;
use Assetic\Asset\FileAsset;
use Assetic\Asset\HttpAsset;
use Assetic\Asset\StringAsset;
use Assetic\Asset\AssetCollection;

/**
 * Class AssetCollector
 */
class AssetCollector
{
	public function addAssetsToCollectionFromArray(
		array $sources,
		$type,
		$split,
		AssetCollection $collection,
		array &$array,
		$defaultFilters,
		$position = 'head'
	) {
		foreach ($sources as $source) {
			if ($source instanceof AssetInterface) {
				if (ThemePlusEnvironment::isLiveMode()) {
					$collection->add($source);
				}
				else if ($source instanceof StringAsset) {
					$data = $source->dump();
					$data = gzcompress($data, 9);
					$data = base64_encode($data);

					$array[] = array(
						'id'       => $type . ':' . 'base64:' . $data,
						'name'     => 'string' . substr(md5($data), 0, 8) . '.' . $type,
						'time'     => substr(md5($data), 0, 8),
						'asset'    => $source,
						'position' => $position
					);
				}
				else if ($source instanceof FileAsset) {
					$reflectionClass = new \ReflectionClass('Assetic\Asset\BaseAsset');
					$sourceProperty  = $reflectionClass->getProperty('sourcePath');
					$sourceProperty->setAccessible(true);
					$sourcePath = $sourceProperty->getValue($source);

					if (in_array($sourcePath, $GLOBALS['TL_THEME_EXCLUDE'])) {
						continue;
					}

					$array[] = array(
						'id'       => $type . ':asset:' . spl_object_hash($source),
						'name'     => basename($sourcePath, '.' . $type) . '.' . $type,
						'time'     => filemtime($sourcePath),
						'asset'    => $source,
						'position' => $position
					);
					$GLOBALS['TL_THEME_EXCLUDE'][] = $sourcePath;
				}
				else {
					$array[] = array(
						'id'       => $type . ':asset:' . spl_object_hash($source),
						'name'     => get_class($source) . '.' . $type,
						'time'     => time(),
						'asset'    => $source,
						'position' => $position
					);
				}
				continue;
			}

			if ($split === null) {
				// use source as source
			}
			else if ($split === true) {
				list($source, $media, $mode) = explode(
					'|',
					$source
				);
			}
			else if ($split === false) {
				list($source, $mode) = explode(
					'|',
					$source
				);
			}
			else {
				return;
			}

			// remove static url
			$source = static::stripStaticURL($source);

			// skip file
			if (in_array(
				$source,
				$GLOBALS['TL_THEME_EXCLUDE']
			)
			) {
				continue;
			}

			$GLOBALS['TL_THEME_EXCLUDE'][] = $source;

			// if stylesheet is an absolute url...
			if (preg_match(
				'#^\w+:#',
				$source
			)
			) {
				// ...fetch the stylesheet
				if ($mode == 'static' && ThemePlusEnvironment::isLiveMode()) {
					$asset = new HttpAsset($source);
					$asset->setTargetPath($this->getAssetPath($asset, $type));
				}
				// ...or add if it is not static
				else {
					$array[] = array(
						'url'   => $source,
						'name'  => basename($source),
						'time'  => time(),
						'media' => $media
					);
					continue;
				}
			}
			else if ($source) {
				$asset = new FileAsset(TL_ROOT . '/' . $source, $defaultFilters, TL_ROOT, $source);
				$asset->setTargetPath($this->getAssetPath($asset, $type));
			}
			else {
				continue;
			}

			if (($mode == 'static' || $mode === null) && static::isLiveMode()) {
				$collection->add($asset);
			}
			else {
				$array[] = array(
					'id'       => $type . ':' . $source,
					'name'     => basename($source),
					'time'     => filemtime($source),
					'asset'    => $asset,
					'media'    => $media,
					'position' => $position
				);
			}
		}
	}

	protected function addAssetsToCollectionFromDatabase(
		\Model\Collection $data,
		$type,
		AssetCollection $collection,
		array &$array,
		$defaultFilters,
		$position = 'head'
	) {
		if ($data) {
			while ($data->next()) {
				if (static::checkBrowserFilter($data)) {
					$asset  = null;
					$filter = array();

					if ($data->asseticFilter) {
						$temp = AsseticFactory::createFilterOrChain(
							$data->asseticFilter,
							static::isDesignerMode()
						);
						if ($temp) {
							$filter = array($temp);
						}
					}

					$filter[] = $defaultFilters;

					if ($data->position) {
						$position = $data->position;
					}

					switch ($data->type) {
						case 'code':
							$name  = ($data->code_snippet_title
								? $data->code_snippet_title
								: ('string' . substr(
									md5($data->code),
									0,
									8
								))) . '.' . $type;
							$time = $data->tstamp;
							$asset = new StringAsset(
								$data->code,
								$filter,
								TL_ROOT,
								'assets/' . $type . '/' . $data->code_snippet_title . '.' . $type
							);
							$asset->setTargetPath($this->getAssetPath($asset, $type));
							$asset->setLastModified($data->tstamp);
							break;

						case 'url':
							// skip file
							if (in_array(
								$data->url,
								$GLOBALS['TL_THEME_EXCLUDE']
							)
							) {
								break;
							}

							$GLOBALS['TL_THEME_EXCLUDE'][] = $data->url;

							$name = basename($data->url);
							$time = $data->tstamp;
							if ($data->fetchUrl) {
								$asset = new HttpAsset($data->url, $filter);
								$asset->setTargetPath($this->getAssetPath($asset, $type));
							}
							else {
								$array[] = array(
									'name'  => $name,
									'url'   => $data->url,
									'media' => $data->media,
									'cc'    => $data->cc,
									'position' => $position
								);
							}
							break;

						case 'file':
							$filepath = false;
							if ($data->filesource == $GLOBALS['TL_CONFIG']['uploadPath'] && version_compare(VERSION, '3', '>=')) {
								$file = (version_compare(VERSION, '3.2', '>=') ? \FilesModel::findByUuid($data->file) : \FilesModel::findByPk($data->file));
								if ($file) {
									$filepath = $file->path;
								}
							}
							else {
								$filepath = $data->file;
							}

							if ($filepath) {
								// skip file
								if (in_array(
									$filepath,
									$GLOBALS['TL_THEME_EXCLUDE']
								)
								) {
									break;
								}

								$GLOBALS['TL_THEME_EXCLUDE'][] = $filepath;

								$name  = basename($filepath, '.' . $type) . '.' . $type;
								$time  = filemtime($filepath);
								$asset = new FileAsset(TL_ROOT . '/' . $filepath, $filter, TL_ROOT, $filepath);
								$asset->setTargetPath($this->getAssetPath($asset, $type));
							}
							break;
					}

					if ($asset) {
						if (static::isLiveMode()) {
							$collection->add($asset);
						}
						else {
							$array[] = array(
								'id'       => $type . ':' . $data->id,
								'name'     => $name,
								'time'     => $time,
								'asset'    => $asset,
								'position' => $position
							);
						}
					}
				}
			}
		}
	}

	protected function addAssetsToCollectionFromPageTree(
		$objPage,
		$type,
		$model,
		AssetCollection $collection,
		array &$array,
		$defaultFilters,
		$local = false,
		$position = 'head'
	) {
		// inherit from parent page
		if ($objPage->pid) {
			$objParent = \PageModel::findWithDetails($objPage->pid);
			$this->addAssetsToCollectionFromPageTree(
				$objParent,
				$type,
				$model,
				$collection,
				$array,
				$defaultFilters,
				false,
				$position
			);
		}

		// add local (not inherited) files
		if ($local) {
			$trigger = 'theme_plus_include_' . $type . '_noinherit';

			if ($objPage->$trigger) {
				$key = 'theme_plus_' . $type . '_noinherit';

				$data = call_user_func(
					array($model, 'findByPks'),
					deserialize(
						$objPage->$key,
						true
					),
					array('order' => 'sorting')
				);
				if ($data) {
					$this->addAssetsToCollectionFromDatabase(
						$data,
						$type == 'stylesheets'
							? 'css'
							: 'js',
						$collection,
						$array,
						$defaultFilters,
						$position
					);
				}
			}
		}

		// add inherited files
		$trigger = 'theme_plus_include_' . $type;

		if ($objPage->$trigger) {
			$key = 'theme_plus_' . $type;

			$data = call_user_func(
				array($model, 'findByPks'),
				deserialize(
					$objPage->$key,
					true
				),
				array('order' => 'sorting')
			);
			if ($data) {
				$this->addAssetsToCollectionFromDatabase(
					$data,
					$type == 'stylesheets'
						? 'css'
						: 'js',
					$collection,
					$array,
					$position
				);
			}
		}
	}

	/**
	 * Render a variable to css code.
	 */
	static public function renderVariable(VariableModel $variable)
	{
		// HOOK: create framework code
		if (isset($GLOBALS['TL_HOOKS']['renderVariable']) && is_array($GLOBALS['TL_HOOKS']['renderVariable'])) {
			foreach ($GLOBALS['TL_HOOKS']['renderVariable'] as $callback) {
				$object = \System::importStatic($callback[0]);
				$varResult = $object->$callback[1]($variable);
				if ($varResult !== false) {
					return $varResult;
				}
			}
		}

		switch ($variable->type) {
			case 'text':
				return $variable->text;

			case 'url':
				return sprintf(
					'url("%s")',
					str_replace(
						'"',
						'\\"',
						$variable->url
					)
				);

			case 'file':
				return sprintf(
					'url("../../%s")',
					str_replace(
						'"',
						'\\"',
						$variable->file
					)
				);

			case 'color':
				return '#' . $variable->color;

			case 'size':
				$arrSize       = deserialize($variable->size);
				$arrTargetSize = array();
				foreach (array('top', 'right', 'bottom', 'left') as $k) {
					if (strlen($arrSize[$k])) {
						$arrTargetSize[] = $arrSize[$k] . $arrSize['unit'];
					}
					else {
						$arrTargetSize[] = '';
					}
				}
				while (count($arrTargetSize) > 0 && empty($arrTargetSize[count($arrTargetSize) - 1])) {
					array_pop($arrTargetSize);
				}
				foreach ($arrTargetSize as $k => $v) {
					if (empty($v)) {
						$arrTargetSize[$k] = '0';
					}
				}
				return implode(
					' ',
					$arrTargetSize
				);
		}
	}


	/**
	 * Get the variables.
	 */
	public function getVariables($varTheme, $strPath = false)
	{
		$objTheme = $this->findTheme($varTheme);

		if (!isset($this->arrVariables[$objTheme->id])) {
			$this->arrVariables[$objTheme->id] = array();

			$objVariable = \Database::getInstance()
				->prepare("SELECT * FROM tl_theme_plus_variable WHERE pid=?")
				->execute($objTheme->id);

			while ($objVariable->next()) {
				$this->arrVariables[$objTheme->id][$objVariable->name] = $this->renderVariable(
					$objVariable,
					$strPath
				);
			}
		}

		return $this->arrVariables[$objTheme->id];
	}


	/**
	 * Replace variables.
	 */
	public function replaceVariables($strCode, $arrVariables = false, $strPath = false)
	{
		if (!$arrVariables) {
			$arrVariables = $this->getVariables(
				false,
				$strPath
			);
		}
		$objVariableReplace = new VariableReplacer($arrVariables);
		return preg_replace_callback(
			'#\$([[:alnum:]_\-]+)#',
			array(&$objVariableReplace, 'replace'),
			$strCode
		);
	}


	/**
	 * Replace variables.
	 */
	public function replaceVariablesByTheme($strCode, $varTheme, $strPath = false)
	{
		$objVariableReplace = new VariableReplacer($this->getVariables(
			$varTheme,
			$strPath
		));
		return preg_replace_callback(
			'#\$([[:alnum:]_\-]+)#',
			array(&$objVariableReplace, 'replace'),
			$strCode
		);
	}


	/**
	 * Replace variables.
	 */
	public function replaceVariablesByLayout($strCode, $varLayout, $strPath = false)
	{
		$objVariableReplace = new VariableReplacer($this->getVariables(
			$this->findThemeByLayout($varLayout),
			$strPath
		));
		return preg_replace_callback(
			'#\$([[:alnum:]_\-]+)#',
			array(&$objVariableReplace, 'replace'),
			$strCode
		);
	}


	/**
	 * Calculate a variables hash.
	 */
	public function getVariablesHash($arrVariables)
	{
		$strVariables = '';
		foreach ($arrVariables as $k => $v) {
			$strVariables .= $k . ':' . $v . "\n";
		}
		return md5($strVariables);
	}


	/**
	 * Calculate a variables hash.
	 */
	public function getVariablesHashByTheme($varTheme)
	{
		return $this->getVariablesHash($this->getVariables($varTheme));
	}


	/**
	 * Calculate a variables hash.
	 */
	public function getVariablesHashByLayout($varLayout)
	{
		return $this->getVariablesHash($this->getVariables($this->findThemeByLayout($varLayout)));
	}


	/**
	 * Wrap a javascript src for lazy include.
	 *
	 * @return string
	 */
	public function wrapJavaScriptLazyInclude($strSrc)
	{
		return 'loadAsync(' . json_encode($strSrc) . (ThemePlus::getInstance()->isDesignerMode() ? ', ' . json_encode(md5($strSrc)) : '') . ');';
	}


	/**
	 * Wrap a javascript src for lazy embedding.
	 *
	 * @return string
	 */
	public function wrapJavaScriptLazyEmbedded($strSource)
	{
		$strBuffer = 'var f=(function(){';
		$strBuffer .= $strSource;
		$strBuffer .= '});';
		$strBuffer .= 'if (window.attachEvent){';
		$strBuffer .= 'window.attachEvent("onload",f);';
		$strBuffer .= '}else{';
		$strBuffer .= 'window.addEventListener("load",f,false);';
		$strBuffer .= '}';
		return $strBuffer;
	}


	/**
	 * Generate the html code.
	 *
	 * @param array  $arrFileIds
	 * @param bool   $blnAbsolutizeUrls
	 * @param object $objAbsolutizePage
	 *
	 * @return string
	 */
	public function includeFiles(
		$arrFileIds,
		$blnAggregate = null,
		$blnAbsolutizeUrls = false,
		$objAbsolutizePage = null
	) {
		$arrResult = array();

		// add css files
		$arrFiles = $this->getCssFiles(
			$arrFileIds,
			$blnAggregate,
			$blnAbsolutizeUrls,
			$objAbsolutizePage
		);
		foreach ($arrFiles as $objFile) {
			$arrResult[] = $objFile->getIncludeHtml();
		}

		// add javascript files
		$arrFiles = $this->getJavaScriptFiles($arrFileIds);
		foreach ($arrFiles as $objFile) {
			$arrResult[] = $objFile->getIncludeHtml();
		}
		return $arrResult;
	}


	/**
	 * Generate the html code.
	 *
	 * @param array $arrFileIds
	 *
	 * @return array
	 */
	public function embedFiles($arrFileIds, $blnAggregate = null, $blnAbsolutizeUrls = false, $objAbsolutizePage = null)
	{
		$arrResult = array();

		// add css files
		$arrFiles = $this->getCssFiles(
			$arrFileIds,
			$blnAbsolutizeUrls,
			$objAbsolutizePage
		);
		foreach ($arrFiles as $objFile) {
			$arrResult[] = $objFile->getEmbeddedHtml();
		}

		// add javascript files
		$arrFiles = $this->getJavaScriptFiles($arrFileIds);
		foreach ($arrFiles as $objFile) {
			$arrResult[] = $objFile->getEmbeddedHtml();
		}
		return $arrResult;
	}


	/**
	 * Hook
	 *
	 * @param string $strTag
	 *
	 * @return mixed
	 */
	public function hookReplaceInsertTags($strTag)
	{
		$arrParts = explode(
			'::',
			$strTag
		);
		$arrIds   = explode(
			',',
			$arrParts[1]
		);
		switch ($arrParts[0]) {
			case 'include_theme_file':
				return implode(
					"\n",
					$this->includeFiles($arrIds)
				) . "\n";

			case 'embed_theme_file':
				return implode(
					"\n",
					$this->embedFiles($arrIds)
				) . "\n";

			// @deprecated
			case 'insert_additional_sources':
				return implode(
					"\n",
					$this->includeFiles($arrIds)
				) . "\n";

			// @deprecated
			case 'include_additional_sources':
				return implode(
					"\n",
					$this->embedFiles($arrIds)
				) . "\n";
		}

		return false;
	}


	/**
	 * Helper function that filter out all non integer values.
	 */
	public function filter_int($string)
	{
		if (is_numeric($string)) {
			return true;
		}
		return false;
	}


	/**
	 * Helper function that filter out all integer values.
	 */
	public function filter_string($string)
	{
		if (is_numeric($string)) {
			return false;
		}
		return true;
	}
}


/**
 * Sorting helper.
 */
class SortingHelper
{
	/**
	 * Sorted array of ids and paths.
	 */
	protected $arrSortedIds;


	/**
	 * Constructor
	 */
	public function __construct($arrSortedIds)
	{
		$this->arrSortedIds = array_values($arrSortedIds);
	}


	/**
	 * uksort callback
	 */
	public function cmp($a, $b)
	{
		$a = array_search(
			$a,
			$this->arrSortedIds
		);
		$b = array_search(
			$b,
			$this->arrSortedIds
		);

		// both are equals or not found
		if ($a === $b) {
			return 0;
		}

		// $a not found
		if ($a === false) {
			return -1;
		}

		// $b not found
		if ($b === false) {
			return 1;
		}

		return $a - $b;
	}
}


/**
 * A little helper class that work as callback for preg_replace_callback.
 */
class VariableReplacer
{
	/**
	 * The variables and there values.
	 */
	protected $variables;


	/**
	 * Constructor
	 */
	public function __construct($variables)
	{
		$this->variables = $variables;
	}


	/**
	 * Callback function for preg_replace_callback.
	 * Searching the variable in $this->variables and return the value
	 * or a comment, that the variable does not exists!
	 */
	public function replace($m)
	{
		if (isset($this->variables[$m[1]])) {
			return $this->variables[$m[1]];
		}

		// HOOK: replace undefined variable
		if (isset($GLOBALS['TL_HOOKS']['replaceUndefinedVariable']) && is_array(
			$GLOBALS['TL_HOOKS']['replaceUndefinedVariable']
		)
		) {
			foreach ($GLOBALS['TL_HOOKS']['replaceUndefinedVariable'] as $callback) {
				$object = \System::importStatic($callback[0]);
				$varResult = $object->$callback[1]($m[1]);
				if ($varResult !== false) {
					return $varResult;
				}
			}
		}

		return $m[0];
	}
}
