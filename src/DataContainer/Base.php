<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Bit3\Contao\ThemePlus\DataContainer;


class Base extends \Backend
{

    /**
     * @param \Model\Collection $collection
     *
     * @return mixed|null|string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getFileLabel($collection)
    {
        if (is_array($collection)) {
            $collection = (object) $collection;
        }

        switch ($collection->type) {
            case 'code':
                $label = $collection->code_snippet_title;
                break;

            case 'url':
                $label = preg_replace('#/([^/]+)$#', '/<strong>$1</strong>', $collection->url);
                break;

            case 'file':
                if ($collection->filesource == $GLOBALS['TL_CONFIG']['uploadPath']
                    && version_compare(
                        VERSION,
                        '3',
                        '>='
                    )
                ) {
                    $file = (version_compare(VERSION, '3.2', '>=') ? \FilesModel::findByUuid($collection->file)
                        : \FilesModel::findByPk($collection->file));

                    if ($file) {
                        $label = preg_replace('#/([^/]+)$#', '/<strong>$1</strong>', $file->path);
                        break;
                    }
                } else {
                    $label = preg_replace('#/([^/]+)$#', '/<strong>$1</strong>', $collection->file);
                    break;
                }
            // no break

            default:
                $label = '?';

                return $label;
        }

        return $label;
    }
}
