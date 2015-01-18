<?php

/**
 * This file is part of bit3/contao-theme-plus.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    bit3/contao-theme-plus
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @copyright  bit3 UG <https://bit3.de>
 * @link       https://github.com/bit3/contao-theme-plus
 * @license    http://opensource.org/licenses/LGPL-3.0 LGPL-3.0+
 * @filesource
 */

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
        $database = \Database::getInstance();

        if (!$database->tableExists($table)) {
            return;
        }

        if ($database->fieldExists('file', $table)) {
            // get the field description
            $desc = $database->query('DESC ' . $table . ' file');

            // convert the field into a blob
            if ($desc->Type != 'blob') {
                $database->query('ALTER TABLE `' . $table . '` CHANGE `file` `file` blob NULL');
                $database->query('UPDATE `' . $table . '` SET `file`=NULL WHERE `file`=\'\' OR `file`=0');
            }

            // select fields with numeric values
            $resultSet = $database->query('SELECT id, file FROM ' . $table . ' WHERE file REGEXP \'^[0-9]+$\'');

            while ($resultSet->next()) {
                // Numeric ID to UUID
                $file = \FilesModel::findByPk($resultSet->file);

                if ($file) {
                    $database
                        ->prepare('UPDATE `' . $table . '` SET file=? WHERE id=?')
                        ->execute($file->uuid, $resultSet->id);
                }
            }
        }
    }
}

$upgrade_to_contao3_2 = new upgrade_to_contao3_2();
$upgrade_to_contao3_2->run();
