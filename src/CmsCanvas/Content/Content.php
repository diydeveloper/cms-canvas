<?php 

namespace CmsCanvas\Content;

use CmsCanvas\Content\Entry\Builder as EntryBuilder;
use CmsCanvas\Content\Navigation\Builder as NavigationBuilder;
use CmsCanvas\Content\Thumbnail\Builder as ThumbnailBuilder;

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
     * Builds and returns an entry provided its ID
     *
     * @param  int $entryId
     * @return \CmsCanvas\Content\Entry\Render
     */
    public function entry($entryId)
    {
        $entries = $this->entries(['entry_id' => $entryId]);

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

}