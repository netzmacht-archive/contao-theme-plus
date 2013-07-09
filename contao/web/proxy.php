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

define('TL_MODE', 'FE');
require(dirname(dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME'])))) . '/initialize.php');

use Assetic\Asset\AssetInterface;
use Assetic\Asset\FileAsset;
use Assetic\Asset\HttpAsset;
use Assetic\Asset\StringAsset;
use Assetic\Filter\FilterCollection;
use ContaoAssetic\AsseticFactory;
use ThemePlus\ThemePlus;
use ThemePlus\Model\StylesheetModel;
use ThemePlus\Model\JavaScriptModel;

class proxy
{
	/**
	 * @var PageModel
	 */
	protected $page;

	/**
	 * @var LayoutModel
	 */
	protected $layout;

	/**
	 * @var "css"|"js"
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $data;

	/**
	 * @var array
	 */
	protected $defaultFilters = array();

	public function run()
	{
		if (!ThemePlus::isDesignerMode()) {
			$this->deny();
		}

		$user = FrontendUser::getInstance();
		$user->authenticate();

		$pathInfo = \Environment::get('pathInfo');

		list($type, $pageId, $sourceDescriptor) = explode('/', substr($pathInfo, 1));
		$sourceDescriptor = base64_decode($sourceDescriptor);

		$this->page   = PageModel::findWithDetails($pageId);
		$this->layout = LayoutModel::findByPk($this->page->layout);

		if (!$this->page ||
			!$this->layout ||
			!preg_match('#^(js|css):(base64:(.+)|.+)$#', $sourceDescriptor, $match)
		) {
			$this->deny();
		}

		$this->type = $match[1];
		$this->id   = $match[2];
		$this->data = $match[3];

		header("Cache-Control: public");

		switch ($this->type) {
			case 'css':
				$this->defaultFilters = AsseticFactory::createFilterOrChain(
					$this->layout->asseticStylesheetFilter,
					true
				);
				break;

			case 'js':
				$this->defaultFilters = AsseticFactory::createFilterOrChain(
					$this->layout->asseticJavaScriptFilter,
					true
				);
				break;
		}

		if (is_numeric($this->id)) {
			switch ($this->type) {
				case 'css':
					$stylesheet = StylesheetModel::findByPk($this->id);

					if ($stylesheet) {
						$this->outputFromDatabase($stylesheet);
					}
					break;

				case 'js':
					$javascript = JavaScriptModel::findByPk($this->id);

					if ($javascript) {
						$this->outputFromDatabase($javascript);
					}
					break;
			}
		}
		else if (!empty($this->data)) {
			$raw = base64_decode($this->data);
			$raw = gzuncompress($raw);
			$raw = trim($raw);
			$this->outputString($raw);
		}
		else {
			$this->outputFile($this->id);
		}

		$this->deny();
	}

	protected function outputFromDatabase($model)
	{
		$asset  = null;
		$filter = array();

		if ($model->asseticFilter) {
			$temp = AsseticFactory::createFilterOrChain(
				$model->asseticFilter,
				ThemePlus::isDesignerMode()
			);
			if ($temp) {
				$filter = array($temp);
			}
		}

		switch ($model->type) {
			case 'code':
				$asset = new StringAsset($model->code, $filter, TL_ROOT);
				$asset->setLastModified($model->tstamp);
				break;

			case 'url':
				$asset = new HttpAsset($model->url, $filter);
				break;

			case 'file':
				$filepath = false;
				if ($model->filesource == $GLOBALS['TL_CONFIG']['uploadPath'] && version_compare(VERSION, '3', '>=')) {
					$file = \FilesModel::findByPk($model->file);
					if ($file) {
						$filepath = $file->path;
					}
				}
				else {
					$filepath = $model->file;
				}

				if ($filepath) {
					$asset = new FileAsset(TL_ROOT . '/' . $filepath, $filter, TL_ROOT, $file->path);
				}
				break;
		}

		if ($asset) {
			$this->outputAsset($asset);
		}

		$this->deny();
	}

	protected function outputString($code)
	{
		$asset = new StringAsset($code);
		$this->outputAsset($asset);
	}

	protected function outputFile($file)
	{
		$path = TL_ROOT . '/' . $file;

		if (file_exists($path)) {
			$asset = new FileAsset(
				$path,
				array(),
				TL_ROOT,
				$file
			);

			$this->outputAsset($asset);
		}
	}

	protected function outputAsset(AssetInterface $asset)
	{
		$asset->setTargetPath('system/modules/theme-plus/web/proxy.php/:type/:page/:id/:file');

		$this->output(
			$asset->dump(
				new FilterCollection($this->defaultFilters)
			)
		);
	}

	protected function output($code)
	{
		switch ($this->type) {
			case 'css':
				header('Content-Type: text/css; charset=utf-8');
				break;

			case 'js':
				header('Content-Type: text/javascript; charset=utf-8');
				break;
		}

		echo $code;
		exit;
	}

	protected function deny()
	{
		header('HTTP/1.1 403 Forbidden');
		header('Status: 403 Forbidden');
		exit;
	}
}

$proxy = new proxy();
$proxy->run();
