<?php 

namespace CmsCanvas\Content;

use Config, Auth;
use CmsCanvas\Content\Entry\Builder as EntryBuilder;
use CmsCanvas\Content\Navigation\Builder as NavigationBuilder;
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
     * @param  mixed $config
     * @return \CmsCanvas\Content\Entry\Render
     */
    public function entry(array $config = [])
    {
        $entries = $this->entries($config);

        return $entries->first();
    }

    /**
     * Builds and returns collection of entries based on 
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
     * @param  bool $userPreferredFlag
     * @return \Carbon\Carbon
     */
    public function convertTimezone(Carbon $dateTime, $userPreferredFlag = true)
    {
        if ($userPreferredFlag && Auth::check()) {
            $dateTime->setTimezone(Auth::user()->getTimezoneIdentifier());
        } else {
            $dateTime->setTimezone(Config::get('cmscanvas::config.default_timezone'));
        }

        return $dateTime->format('d/M/Y h:i:s a');
    }

}