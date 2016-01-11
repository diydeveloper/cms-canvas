<?php 

namespace CmsCanvas\Content\Navigation\Item;

use CmsCanvas\Content\Navigation\Builder\Item as ItemBuilder;

class Render {

    /**
     * The navigation item builder to render from
     *
     * @var \CmsCanvas\Content\Navigation\Builder\Item
     */
    protected $itemBuilder;

    /**
     * Constructor of the navigation item render
     *
     * @param  \CmsCanvas\Content\Navigation\Builder\Item  $itemBuilder
     * @return void
     */
    public function __construct(ItemBuilder $itemBuilder)
    {
        $this->itemBuilder = $itemBuilder;
    }

    /**
     * Reutrns the firstFlag class property
     *
     * @return bool
     */
    public function isFirst()
    {
        return $this->itemBuilder->getFirstFlag();
    }

    /**
     * Reutrns the lastFlag class property
     *
     * @return bool
     */
    public function isLast()
    {
        return $this->itemBuilder->getLastFlag();
    }

    /**
     * Reutrns the index class property
     *
     * @return int
     */
    public function index()
    {
        return $this->itemBuilder->getIndex();
    }

    /**
     * Magic method to render the navigation item as a string
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return (string) $this->itemBuilder->renderContents();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

}