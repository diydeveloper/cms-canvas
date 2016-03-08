<?php 

namespace CmsCanvas\Content;

use Lang, Cache;
use CmsCanvas\Container\Cache\Page;
use CmsCanvas\Content\Entry\Builder as EntryBuilder;
use CmsCanvas\Content\Navigation\Builder as NavigationBuilder;
use CmsCanvas\Content\Breadcrumb\Builder as BreadcrumbBuilder;
use CmsCanvas\Content\Thumbnail\Builder as ThumbnailBuilder;
use Carbon\Carbon;

class Content {

    /**
     * Builds and returns collection of entries based on 
     * the provided configuration
     *
     * @param  array $config
     * @return \CmsCanvas\Content\Entry\RenderCollection
     */
    public function entries(array $config = [])
    {
        $builder = new EntryBuilder($config);
        $collection = $builder->get();

        return $collection;
    }

    /**
     * Builds and returns first entry found provided the configuration
     *
     * @param  array $config
     * @return \CmsCanvas\Content\Entry\Render
     */
    public function entryFirst(array $config = [])
    {
        $entries = $this->entries($config);

        return $entries->first();
    }

    /**
     * Builds and returns entry from cache using the entry id
     *
     * @param  int $entryId
     * @return \CmsCanvas\Content\Entry\Render
     */
    public function entry($entryId)
    {
        $routeName = 'entry.'.$config.'.'.Lang::getLocale();
        $routeArray = explode('.', $routeName);
        list($objectType, $objectId, $locale) = $routeArray;
        $cache = Cache::rememberForever($routeName, function() use($objectType, $objectId) {
            return new Page($objectId, $objectType);
        });

        return $cache->getResource()->render();
    }

    /**
     * Builds and returns collection of navigation items based on 
     * the provided configuration
     *
     * @param  string $shortName
     * @param  array $config
     * @return \CmsCanvas\Content\Navigation\RenderCollection
     */
    public function navigation($shortName, array $config = [])
    {
        $builder = new NavigationBuilder($shortName, $config);
        $collection = $builder->get();

        return $collection;
    }

    /**
     * Builds and returns collection of breadcrumb navigation items based on 
     * the provided configuration
     *
     * @param  string $shortName
     * @param  array $config
     * @return \CmsCanvas\Content\Navigation\RenderCollection
     */
    public function breadcrumb($shortName, array $config = [])
    {
        $builder = new BreadcrumbBuilder($shortName, $config);
        $collection = $builder->get();

        return $collection;
    }

    /**
     * Resizes and caches an image to the specified dimensions
     *
     * @param  string $source
     * @param  array $config
     * @return string
     */
    public function thumbnail($source, array $config = [])
    {
        $builder = new ThumbnailBuilder($source, $config);

        return $builder->get();
    }

    /**
     * Localize and format a carbon date
     *
     * @param  \Carbon\Carbon $dateTime
     * @param  string $format
     * @param  string $timezone
     * @return string
     */
    public function userDate(Carbon $dateTime, $format = null, $timezone = null)
    {
        if ($timezone !== false) {
            if ($timezone == null) {
                $timezone = (auth()->check())
                    ? auth()->user()->getTimezoneIdentifier()
                    : config('cmscanvas::config.default_timezone');
            }

            $dateTime->setTimezone($timezone);
        }

        if ($format != null) {
            return $dateTime->format($format);
        } else {
            return $dateTime->format('d/M/Y h:i:s a');
        }
    }

}