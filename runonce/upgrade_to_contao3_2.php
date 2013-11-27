<?php

// HOTFIX: disable update to prevent data loss
return;

if (version_compare(VERSION, '3.2', '<')) {
	return;
}

/**
 * Class upgrade_to_contao3_2
 */
class upgrade_to_contao3_2
{
	public function run()
	{
		$this->updateFileField('tl_theme_plus_javascript');
		$this->updateFileField('tl_theme_plus_stylesheet');
		$this->updateFileField('tl_theme_plus_variable');
	}

	protected function updateFileField($table)
	{
		$desc = \Database::getInstance()->query('DESC ' . $table . ' file');
		$stillNumericRecordCount = \Database::getInstance()
			->query('SELECT COUNT(id) AS count FROM ' . $table . ' WHERE file REGEXP \'^[0-9]+$\'')
			->count;
		if ($desc->Type != 'blob' && $desc->Type != 'binary(16)' || $stillNumericRecordCount) {
			\Database\Updater::convertSingleField($table, 'file');
		}
	}
}

$upgrade_to_contao3_2 = new upgrade_to_contao3_2();
$upgrade_to_contao3_2->run();
