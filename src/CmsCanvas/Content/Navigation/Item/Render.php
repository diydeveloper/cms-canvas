<?php 

namespace CmsCanvas\Content\Navigation\Item;

use CmsCanvas\Content\Navigation\Builder\Item as BuilderItem;

class Render {

    /**
     * The navigation item to render from
     *
     * @var \CmsCanvas\Content\Navigation\Builder\Item
     */
    protected $item;

    /**
     * Constructor of the navigation item render
     *
     * @param  \CmsCanvas\Content\Navigation\Builder\Item  $item
     * @return void
     */
    public function __construct(BuilderItem $item)
    {
        $this->item = $item;
    }

    /**
     * Reutrns the firstFlag class property
     *
     * @return bool
     */
    public function isFirst()
    {
        return $this->item->firstFlag;
    }

    /**
     * Reutrns the lastFlag class property
     *
     * @return bool
     */
    public function isLast()
    {
        return $this->item->lastFlag;
    }

    /**
     * Reutrns the index class property
     *
     * @return int
     */
    public function index()
    {
        return $this->item->index;
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