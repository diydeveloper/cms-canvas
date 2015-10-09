<?php 

namespace CmsCanvas\Content\Navigation\Item;

use CmsCanvas\Database\Eloquent\Collection as CmsCanvasCollection;

class RenderCollection extends CmsCanvasCollection {

    /**
     * @var \CmsCanvas\Content\Navigation\Builder
     */
    protected $navigationBuilder;

    /**
     * Contructor to set collection of navigation items
     *
     * @param  \CmsCanvas\Content\Navigation\Builder\Item|array  $items
     * @param  \CmsCanvas\Content\Navigation\Builder  $navigationBuilder
     * @return void
     */
    public function __construct($items, \CmsCanvas\Content\Navigation\Builder $navigationBuilder = null)
    {
        $this->navigationBuilder = $navigationBuilder;

        foreach ($items as $item) {
            $this->items[] = $item->render();
        }
    }

    /**
     * Magic method to render the items as a string
     *
     * @return string
     */
    public function __toString()
    {
        $attributes = '';

        if ($this->navigationBuilder != null) {
            $attributes = $this->navigationBuilder->getAttributes();
        }

        $contents = '<ul'.$attributes.'>';

        foreach ($this->items as $item) {
            $contents .= (string) $item;
        }

        $contents .= '</ul>';

        return $contents;
    }

}