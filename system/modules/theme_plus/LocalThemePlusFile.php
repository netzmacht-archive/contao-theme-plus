<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class LocalThemePlusFile
 * 
 * 
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 */
abstract class LocalThemePlusFile extends ThemePlusFile {

	/**
	 * The origin file path.
	 */
	protected $strOriginFile;
	
	
	/**
	 * The processed temporary file path.
	 */
	protected $strProcessedFile;
	
	
	/**
	 * Create a new local file object.
	 */
	public function __construct($strOriginFile)
	{
		$this->strOriginFile = $strOriginFile;
		$this->strProcessedFile = null;
	}
	
	
	/**
	 * Get the original file path relative to TL_ROOT.
	 */
	public function getOriginFile()
	{
		return $this->strOriginFile;
	}
	
	
	/**
	 * Get the file path relative to TL_ROOT
	 */
	public abstract function getFile();
	
		
	public function __get($k)
	{
		switch ($k)
		{
		case 'origin':
			return $this->getOriginFile();
			
		case 'file':
		case 'path':
			return $this->getFile();
		
		default:
			return parent::__get($k);
		}
	}
	
}

?>