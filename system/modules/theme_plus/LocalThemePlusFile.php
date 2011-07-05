<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class LocalThemePlusFile
 */
abstract class LocalThemePlusFile extends ThemePlusFile {

	/**
	 * The origin file path.
	 */
	protected $strOriginFile;
	
	
	/**
	 * The corresponding theme.
	 */
	protected $objTheme;
	
	
	/**
	 * Create a new local file object.
	 */
	public function __construct($strOriginFile, $strCc = '', $objTheme = false)
	{
		if (!file_exists(TL_ROOT . '/' . $strOriginFile))
		{
			throw new Exception('File does not exists: ' . $this->strOriginFile);
		}
		
		parent::__construct($strCc);
		$this->strOriginFile = $strOriginFile;
		$this->objTheme = $objTheme;
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
	
	
	public function getGlobalVariableCode()
	{
		return $this->getFile() . (strlen($this->strCc) ? '|' . $this->strCc : '');
	}
	
		
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