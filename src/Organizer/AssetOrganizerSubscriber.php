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

namespace Bit3\Contao\ThemePlus\Organizer;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\AssetCollectionInterface;
use Assetic\Asset\AssetInterface;
use Assetic\Asset\StringAsset;
use Assetic\Filter\CssRewriteFilter;
use Bit3\Contao\ThemePlus\Asset\DelegatorAssetInterface;
use Bit3\Contao\ThemePlus\Asset\ExtendedAssetInterface;
use Bit3\Contao\ThemePlus\Event\GenerateAssetPathEvent;
use Bit3\Contao\ThemePlus\Event\OrganizeAssetsEvent;
use Bit3\Contao\ThemePlus\Filter\FilterRules;
use Bit3\Contao\ThemePlus\Filter\FilterRulesCompiler;
use Bit3\Contao\ThemePlus\ThemePlusEnvironment;
use Bit3\Contao\ThemePlus\ThemePlusEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AssetOrganizerSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ThemePlusEvents::GENERATE_ASSET_PATH        => 'generateAssetPath',
            ThemePlusEvents::ORGANIZE_STYLESHEET_ASSETS => 'organizeStylesheets',
            ThemePlusEvents::ORGANIZE_JAVASCRIPT_ASSETS => 'organizeJavaScripts',
        ];
    }

    /**
     * @param GenerateAssetPathEvent   $event
     * @param                          $eventName
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function generateAssetPath(
        GenerateAssetPathEvent $event,
        $eventName,
        EventDispatcherInterface $eventDispatcher
    ) {
        $asset = $event->getAsset();

        $filtersCacheKey = $this->buildFiltersCacheKey($asset);

        // Skip delegator assets
        while ($asset instanceof DelegatorAssetInterface) {
            $asset = $asset->getAsset();
        }

        if ($asset instanceof AssetCollectionInterface) {
            // calculate path for collections
            $string = $filtersCacheKey;

            foreach ($asset->all() as $child) {
                $generateAssetPathEvent = new GenerateAssetPathEvent(
                    $event->getPage(),
                    $event->getLayout(),
                    $child,
                    $event->getType()
                );
                $eventDispatcher->dispatch(ThemePlusEvents::GENERATE_ASSET_PATH, $generateAssetPathEvent);

                $string .= '-' . $generateAssetPathEvent->getPath();
            }

            $assetPath = sprintf(
                'assets/%s/%s-collection.%s',
                $event->getType(),
                substr(
                    md5($string),
                    0,
                    8
                ),
                $event->getType()
            );
        } else {
            // calculate cache path from content
            if ($asset instanceof StringAsset) {
                $assetPath = sprintf(
                    'assets/%s/%s-%s.%s',
                    $event->getType(),
                    substr(
                        md5($filtersCacheKey . '-' . $asset->getContent() . '-' . $asset->getLastModified()),
                        0,
                        8
                    ),
                    basename($asset->getSourcePath()),
                    $event->getType()
                );
            } else {
                // calculate cache path from source path
                $assetPath = sprintf(
                    'assets/%s/%s-%s.%s',
                    $event->getType(),
                    substr(
                        md5($filtersCacheKey . '-' . $asset->getSourcePath() . '-' . $asset->getLastModified()),
                        0,
                        8
                    ),
                    basename(
                        $asset->getSourcePath(),
                        '.' . $event->getType()
                    ),
                    $event->getType()
                );
            }
        }

        $event->setPath($assetPath);
    }

    /**
     * Build the filters cache key from an asset.
     *
     * @param AssetInterface $asset The asset.
     *
     * @return string
     */
    private function buildFiltersCacheKey(AssetInterface $asset)
    {
        $filtersCacheKey = [];
        foreach ($asset->getFilters() as $v) {
            $filtersCacheKey[] = get_class($v);
        }
        $filtersCacheKey = '[' . implode(',', $filtersCacheKey) . ']';

        return $filtersCacheKey;
    }

    /**
     * Organize stylesheets into collections.
     *
     * @param OrganizeAssetsEvent $event The event.
     */
    public function organizeStylesheets(OrganizeAssetsEvent $event)
    {
        $this->organizeAssets($event);

        $cssRewriteFilter = new CssRewriteFilter();
        foreach ($event->getOrganizedAssets()->all() as $asset) {
            /** @var AssetInterface $asset */
            $asset->ensureFilter($cssRewriteFilter);
        }
    }

    /**
     * Organize javascripts into collections.
     *
     * @param OrganizeAssetsEvent $event The event.
     */
    public function organizeJavaScripts(OrganizeAssetsEvent $event)
    {
        $this->organizeAssets($event);
    }

    /**
     * Organize assets into collections.
     *
     * @param OrganizeAssetsEvent $event The event.
     */
    private function organizeAssets(OrganizeAssetsEvent $event)
    {
        if (!$event->getOrganizedAssets()) {
            // list of organized assets
            $organizedAssets = new AssetCollection();

            // distribute the assets over the combined and organized collections
            $this->splitAssets($event->getAssets(), $organizedAssets, $event);

            if ($event->getDeveloperTool()) {
                $assets = new AssetCollection();

                foreach ($organizedAssets as $asset) {
                    /** @var AssetInterface $asset */

                    if ($asset instanceof AssetCollectionInterface) {
                        foreach ($asset as $collectionAsset) {
                            $assets->add($collectionAsset);
                        }
                    } else {
                        $assets->add($asset);
                    }
                }

                $event->setOrganizedAssets($assets);
            } else {
                $event->setOrganizedAssets($organizedAssets);
            }
        }
    }

    /**
     * Split assets and distribute them over the combined and organized collections.
     *
     * @param AssetCollectionInterface|AssetInterface[] $assets               The assets to split.
     * @param AssetCollectionInterface                  $organizedAssets      The organized assets collection.
     * @param OrganizeAssetsEvent                       $event                The event.
     * @param AssetCollectionInterface                  $combinedAssets       The combined assets collection from the
     *                                                                        parent call.
     * @param FilterRules                               $inheritedFilterRules The filter rules from the parent call.
     * @param array                                     $inheritedFilters     The filters from the parent call.
     *
     * @return \Assetic\Asset\AssetCollection|\Assetic\Asset\AssetCollectionInterface|null
     */
    private function splitAssets(
        AssetCollectionInterface $assets,
        AssetCollectionInterface $organizedAssets,
        OrganizeAssetsEvent $event,
        AssetCollectionInterface $combinedAssets = null,
        FilterRules $inheritedFilterRules = null,
        array $inheritedFilters = []
    ) {
        foreach ($assets as $asset) {
            if ($this->determineSkip($asset)) {
                continue;
            }

            $this->cleanFilters($asset, $inheritedFilters);

            if ($this->determineDoSubSplit($asset)) {
                $filterRules = $asset instanceof ExtendedAssetInterface ? $asset->getFilterRules() : null;
                $filterRules = $this->joinFilterRules($filterRules, $inheritedFilterRules);

                $combinedAssets = static::splitAssets(
                    $asset,
                    $organizedAssets,
                    $event,
                    $combinedAssets,
                    $filterRules,
                    $asset->getFilters()
                );
            } elseif ($this->determineIsStandalone($asset, $event)) {
                $organizedAssets->add($asset);
                $combinedAssets = null;
            } else {
                if (!$combinedAssets) {
                    $combinedAssets = new AssetCollection();
                    $organizedAssets->add($combinedAssets);
                }

                $combinedAssets->add($asset);
            }
        }

        return $combinedAssets;
    }

    /**
     * Clean filters, remove css-rewrite filter and inherit collection filters.
     *
     * @param AssetInterface $asset            The asset.
     * @param array          $inheritedFilters The inherited filters.
     *
     * @return void
     */
    private function cleanFilters(AssetInterface $asset, array $inheritedFilters)
    {
        // remove css-rewrite filter
        $filters = $asset->getFilters();
        $asset->clearFilters();
        foreach ($filters as $filter) {
            if (!$filter instanceof CssRewriteFilter) {
                $asset->ensureFilter($filter);
            }
        }

        // inherit filters
        foreach ($inheritedFilters as $filter) {
            if (!$filter instanceof CssRewriteFilter) {
                $asset->ensureFilter($filter);
            }
        }
    }

    /**
     * Join asset filter rules with inherited filter rules.
     *
     * @param FilterRules $filterRules          The asset filter rules.
     * @param FilterRules $inheritedFilterRules The inherited filter rules.
     *
     * @return FilterRules|null
     */
    private function joinFilterRules(FilterRules $filterRules = null, FilterRules $inheritedFilterRules = null)
    {
        if (!$filterRules) {
            $filterRules = $inheritedFilterRules;
        } elseif ($inheritedFilterRules) {
            $filterRules = clone $filterRules;

            foreach ($inheritedFilterRules as $inheritedFilterRule) {
                $filterRules->add($inheritedFilterRule);
            }
        }

        return $filterRules;
    }

    /**
     * Determine if asset is skipped.
     *
     * @param AssetInterface $asset The asset.
     *
     * @return bool
     */
    private function determineSkip(AssetInterface $asset)
    {
        if (!$asset instanceof ExtendedAssetInterface) {
            return false;
        }

        if (ThemePlusEnvironment::isInPreCompileMode()) {
            return false;
        }

        if ($asset->getFilterRules()) {
            return $this->evaluateRules($asset->getFilterRules());
        }

        return false;
    }

    /**
     * Evaluate filter rules.
     *
     * @param AssetInterface $asset The asset.
     *
     * @return bool
     */
    private function evaluateRules(FilterRules $filterRules)
    {
        global $container;

        /** @var FilterRulesCompiler $filterRulesCompiler */
        $filterRulesCompiler = $container['theme-plus-filter-rules-compiler'];

        return !$filterRulesCompiler->evaluate($filterRules);
    }

    /**
     * Determine if sub-split is required.
     *
     * @param AssetInterface $asset The asset.
     *
     * @return bool
     */
    private function determineDoSubSplit(AssetInterface $asset)
    {
        return $asset instanceof AssetCollectionInterface;
    }

    /**
     * Determine if asset is standalone.
     *
     * @param AssetInterface $asset The asset.
     *
     * @return bool
     */
    private function determineIsStandalone(AssetInterface $asset)
    {
        if (!$asset instanceof ExtendedAssetInterface) {
            return false;
        }

        if ($asset->getMediaQuery()
            || $asset->getConditionalComment()
            || $asset->isInline()
            || $asset->isStandalone()
        ) {
            return true;
        }

        return false;
    }
}
