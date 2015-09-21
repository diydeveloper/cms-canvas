<?php 

namespace CmsCanvas\Content\Navigation\Item;

class Render {

    /**
     * The navigation item to render from
     *
     * @var \CmsCanvas\Models\Content\Navigation\Item
     */
    protected $item;

    /**
     * Constructor of the navigation item render
     *
     * @param \CmsCanvas\Models\Content\Navigation\Item $item
     * @return void
     */
    public function __construct(\CmsCanvas\Models\Content\Navigation\Item $item)
    {
        $this->item = $item;
    }

    /**
     * Magic method to render the navigation item as a string
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->item->renderContents();
    }

}