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

namespace Bit3\Contao\ThemePlus\Asset;

use Assetic\Asset\AssetInterface;
use Assetic\Asset\FileAsset;
use Assetic\Asset\HttpAsset;
use Assetic\Asset\StringAsset;
use Assetic\Filter\CssRewriteFilter;
use Assetic\Filter\FilterInterface;
use Bit3\Contao\Assetic\AsseticFactory;
use Bit3\Contao\ThemePlus\ThemePlusEnvironment;

class DatabaseAsset implements ExtendedAssetInterface, DelegatorAssetInterface, \Serializable
{

    /**
     * @var array
     */
    protected $row;

    /**
     * @var string
     */
    protected $type;

    protected $asset;

    public function __construct(array $row, $type)
    {
        $this->row  = $row;
        $this->type = (string) $type;
    }

    /**
     * @return array
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return AssetInterface
     */
    public function getAsset()
    {
        if (!$this->asset) {
            $filter = [];

            if ($this->type == 'css') {
                $filter[] = new CssRewriteFilter();
            }

            if ($this->row['asseticFilter']) {
                /** @var AsseticFactory $asseticFactory */
                $asseticFactory = $GLOBALS['container']['assetic.factory'];

                $temp = $asseticFactory->createFilterOrChain(
                    $this->row['asseticFilter'],
                    ThemePlusEnvironment::isDesignerMode()
                );
                if ($temp) {
                    $filter[] = $temp;
                }
            }

            switch ($this->row['type']) {
                case 'file':
                    if ($this->row['filesource'] == $GLOBALS['TL_CONFIG']['uploadPath']) {
                        $file = \FilesModel::findByUuid($this->row['file']);

                        if ($file) {
                            $filePath = $file->path;
                        } else {
                            $filePath = $this->row['file'];
                        }
                    } else {
                        $filePath = $this->row['file'];
                    }

                    $this->asset =
                        new FileAsset(TL_ROOT . DIRECTORY_SEPARATOR . $filePath, $filter, TL_ROOT, $filePath);

                    break;

                case 'url':
                    $this->asset = new HttpAsset($this->row['url'], $filter);
                    break;

                case 'code':
                    $this->asset = new StringAsset($this->row['code'], $filter, TL_ROOT, 'string_asset');
                    $this->asset->setLastModified($this->row['tstamp']);
                    break;
            }
        }

        return $this->asset;
    }

    /**
     * {@inheritdoc}
     */
    public function ensureFilter(FilterInterface $filter)
    {
        $this->getAsset()->ensureFilter($filter);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return $this->getAsset()->getFilters();
    }

    /**
     * {@inheritdoc}
     */
    public function clearFilters()
    {
        return $this->getAsset()->clearFilters();
    }

    /**
     * {@inheritdoc}
     */
    public function load(FilterInterface $additionalFilter = null)
    {
        return $this->getAsset()->load($additionalFilter);
    }

    /**
     * {@inheritdoc}
     */
    public function dump(FilterInterface $additionalFilter = null)
    {
        return $this->getAsset()->dump($additionalFilter);
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->getAsset()->getContent();
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        return $this->getAsset()->setContent($content);
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceRoot()
    {
        return $this->getAsset()->getSourceRoot();
    }

    /**
     * {@inheritdoc}
     */
    public function getSourcePath()
    {
        return $this->getAsset()->getSourcePath();
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceDirectory()
    {
        return $this->getAsset()->getSourceDirectory();
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetPath()
    {
        return $this->getAsset()->getTargetPath();
    }

    /**
     * {@inheritdoc}
     */
    public function setTargetPath($targetPath)
    {
        return $this->getAsset()->setTargetPath($targetPath);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastModified()
    {
        return $this->getAsset()->getLastModified();
    }

    /**
     * {@inheritdoc}
     */
    public function getVars()
    {
        return $this->getAsset()->getVars();
    }

    /**
     * {@inheritdoc}
     */
    public function setValues(array $values)
    {
        return $this->getAsset()->setValues($values);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        return $this->getAsset()->getValues();
    }

    /**
     * {@inheritdoc}
     */
    public function getConditionalComment()
    {
        return $this->row['cc'];
    }

    /**
     * {@inheritdoc}
     */
    public function setConditionalComment($conditionalComment)
    {
        $this->row['cc'] = $conditionalComment;
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaQuery()
    {
        return $this->row['media'];
    }

    /**
     * {@inheritdoc}
     */
    public function setMediaQuery($mediaQuery)
    {
        $this->row['media'] = $mediaQuery;
    }

    /**
     * {@inheritdoc}
     */
    public function isStandalone()
    {
        return $this->row['standalone'];
    }

    /**
     * {@inheritdoc}
     */
    public function setStandalone($standalone)
    {
        $this->row['standalone'] = (bool) $standalone;
    }

    /**
     * {@inheritdoc}
     */
    public function isInline()
    {
        return $this->row['inline'];
    }

    /**
     * {@inheritdoc}
     */
    public function setInline($inline)
    {
        $this->row['inline'] = (bool) $inline;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([$this->row, $this->type]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($this->row, $this->type) = unserialize($serialized);
    }
}